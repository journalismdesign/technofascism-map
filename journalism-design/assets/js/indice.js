/**
 * Indice de dépendance numérique — interface du questionnaire.
 * Données (questions, barème, textes) fournies par PHP dans JD_INDICE.
 * Le même calcul est refait côté serveur pour l'e-mail et le baromètre.
 */
( function () {
	'use strict';

	var D = window.JD_INDICE;
	if ( ! D ) {
		return;
	}
	var T = D.texts;
	var DIM_KEYS = Object.keys( D.dims );
	var SCORED = D.questions.filter( function ( q ) { return q.dim; } );

	// Poids d'une dimension dans l'indice et maximum de points par dimension.
	var DIM_MAX = {};
	DIM_KEYS.forEach( function ( k ) { DIM_MAX[ k ] = 0; } );
	SCORED.forEach( function ( q ) { DIM_MAX[ q.dim ] += 6; } );

	/* ---------- utilitaires ---------- */

	function el( tag, attrs, children ) {
		var n = document.createElement( tag );
		Object.keys( attrs || {} ).forEach( function ( k ) {
			if ( k === 'text' ) {
				n.textContent = attrs[ k ];
			} else if ( k === 'html' ) {
				n.innerHTML = attrs[ k ];
			} else if ( k.indexOf( 'on' ) === 0 ) {
				n.addEventListener( k.slice( 2 ), attrs[ k ] );
			} else if ( attrs[ k ] !== null && attrs[ k ] !== undefined && attrs[ k ] !== false ) {
				n.setAttribute( k, attrs[ k ] );
			}
		} );
		( children || [] ).forEach( function ( c ) {
			if ( c ) {
				n.appendChild( typeof c === 'string' ? document.createTextNode( c ) : c );
			}
		} );
		return n;
	}

	function questionPoints( q, a ) {
		if ( q.type === 'criteria' ) {
			a = a || [];
			if ( a.indexOf( q.none ) !== -1 ) {
				return 6;
			}
			var n = a.filter( function ( i ) { return q.neutral.indexOf( i ) === -1 && i !== q.none; } ).length;
			return Math.max( 0, 6 - n );
		}
		return q.options[ a ][ 1 ];
	}

	function contribution( q, pts ) {
		// Points sur 100 apportés par la réponse (chaque dimension pèse 20 %).
		return Math.round( ( pts / DIM_MAX[ q.dim ] ) * ( 100 / DIM_KEYS.length ) );
	}

	function compute( answers ) {
		var score = {};
		DIM_KEYS.forEach( function ( k ) { score[ k ] = 0; } );
		SCORED.forEach( function ( q ) {
			if ( answers[ q.id ] !== undefined ) {
				score[ q.dim ] += questionPoints( q, answers[ q.id ] );
			}
		} );
		var dims = {};
		var sum = 0;
		DIM_KEYS.forEach( function ( k ) {
			dims[ k ] = Math.round( ( 100 * score[ k ] ) / DIM_MAX[ k ] );
			sum += dims[ k ];
		} );
		var total = Math.round( sum / DIM_KEYS.length );
		var label = '';
		for ( var i = 0; i < T.levels.length; i++ ) {
			if ( total < T.levels[ i ][ 0 ] ) {
				label = T.levels[ i ][ 1 ];
				break;
			}
		}
		return { total: total, label: label, dims: dims };
	}

	function post( endpoint, body ) {
		return fetch( D.urls.rest + endpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify( body ),
			credentials: 'omit',
		} ).then( function ( r ) {
			return r.json().then( function ( j ) {
				j.status = r.status;
				return j;
			} );
		} );
	}

	/* ---------- application ---------- */

	function App( root ) {
		this.root = root;
		this.h = 'h' + ( root.getAttribute( 'data-heading' ) === '1' ? '1' : '2' );
		this.reset();
	}

	App.prototype.reset = function () {
		this.started = this.answers !== undefined; // vrai après « Recommencer »
		this.answers = {};
		this.step = 0; // index dans this.steps
		this.steps = [];
		this.render( this.intro() );
	};

	App.prototype.render = function ( node, focus ) {
		this.root.innerHTML = '';
		this.root.appendChild( node );
		var target = this.root.querySelector( focus || '[data-focus]' );
		// Le focus ne bouge qu'après une action du visiteur (pas au chargement de la page).
		if ( target && ( this.started || this.root.closest( 'dialog' ) ) ) {
			target.setAttribute( 'tabindex', '-1' );
			target.focus( { preventScroll: false } );
		}
		var dialog = this.root.closest( 'dialog' );
		if ( dialog ) {
			dialog.scrollTop = 0;
		} else if ( this.started ) {
			var top = this.root.getBoundingClientRect().top + window.pageYOffset - 100;
			if ( window.pageYOffset > top ) {
				window.scrollTo( { top: top } );
			}
		}
	};

	App.prototype.intro = function () {
		var self = this;
		return el( 'div', { class: 'jd-indice__intro' }, [
			el( 'p', { class: 'is-style-eyebrow', text: T.kicker } ),
			el( this.h, { class: 'jd-indice__title', 'data-focus': '', text: T.title } ),
			el( 'p', { class: 'is-style-lead', text: T.lead } ),
			el( 'ul', { class: 'jd-indice__dims' }, DIM_KEYS.map( function ( k ) {
				return el( 'li', {}, [ el( 'strong', { text: D.dims[ k ].label } ), ' — ' + D.dims[ k ].question ] );
			} ) ),
			el( 'p', { class: 'jd-indice__note', text: T.privacy } ),
			el( 'div', { class: 'wp-block-buttons' }, [
				el( 'div', { class: 'wp-block-button' }, [
					el( 'button', { type: 'button', class: 'wp-block-button__link wp-element-button', text: T.start, onclick: function () { self.begin( true ); } } ),
				] ),
				el( 'div', { class: 'wp-block-button is-style-ghost' }, [
					el( 'button', { type: 'button', class: 'wp-block-button__link wp-element-button', text: T.skipIntro, onclick: function () { self.begin( false ); } } ),
				] ),
			] ),
		] );
	};

	App.prototype.begin = function ( withPrologue ) {
		this.started = true;
		this.steps = [];
		if ( withPrologue ) {
			D.prologue.forEach( function ( p ) { this.steps.push( { kind: 'prologue', q: p } ); }, this );
			this.steps.push( { kind: 'transition' } );
		}
		D.questions.forEach( function ( q ) { this.steps.push( { kind: 'question', q: q } ); }, this );
		this.step = 0;
		this.show();
	};

	App.prototype.progress = function () {
		var q = this.steps[ this.step ].q;
		var idx = D.questions.indexOf( q );
		var scoredBefore = D.questions.slice( 0, idx + 1 ).filter( function ( x ) { return x.dim; } ).length;
		var current = compute( this.answers );
		var answered = SCORED.filter( function ( x ) { return this.answers[ x.id ] !== undefined; }, this ).length;
		return el( 'div', { class: 'jd-indice__progress' }, [
			el( 'div', { class: 'jd-indice__bar', role: 'progressbar', 'aria-valuemin': '0', 'aria-valuemax': String( SCORED.length ), 'aria-valuenow': String( answered ), 'aria-label': 'Progression' }, [
				el( 'span', { style: 'width:' + Math.round( ( 100 * answered ) / SCORED.length ) + '%' } ),
			] ),
			el( 'p', { class: 'jd-indice__meta' }, [
				el( 'span', { text: 'Question ' + Math.max( 1, scoredBefore ) + ' / ' + SCORED.length + ( q.dim ? ' · ' + D.dims[ q.dim ].label : '' ) } ),
				answered ? el( 'span', { text: 'Indice provisoire : ' + current.total + ' / 100' } ) : null,
			] ),
		] );
	};

	App.prototype.show = function () {
		var s = this.steps[ this.step ];
		if ( ! s ) {
			return this.result();
		}
		if ( s.kind === 'prologue' ) {
			return this.render( this.prologueView( s.q ) );
		}
		if ( s.kind === 'transition' ) {
			return this.render( this.transitionView() );
		}
		return this.render( this.questionView( s.q ) );
	};

	App.prototype.next = function () {
		this.step++;
		this.show();
	};

	App.prototype.prev = function () {
		if ( this.step > 0 ) {
			this.step--;
			this.show();
		} else {
			this.reset();
		}
	};

	App.prototype.prologueView = function ( p ) {
		var self = this;
		var n = D.prologue.indexOf( p ) + 1;
		return el( 'div', { class: 'jd-indice__step' }, [
			el( 'p', { class: 'is-style-eyebrow', text: T.prologueK + ' · ' + n + ' / ' + D.prologue.length } ),
			el( 'p', { class: 'jd-indice__q', 'data-focus': '', text: p.text } ),
			el( 'div', { class: 'jd-indice__options', role: 'group', 'aria-label': p.text }, p.options.map( function ( label ) {
				return el( 'button', { type: 'button', class: 'jd-indice__opt', text: label, onclick: function () { self.next(); } } );
			} ) ),
			el( 'p', { class: 'jd-indice__nav' }, [
				el( 'button', { type: 'button', class: 'jd-indice__link', text: T.skipIntro + ' →', onclick: function () {
					self.step = self.steps.findIndex( function ( s ) { return s.kind === 'question'; } );
					self.show();
				} } ),
			] ),
		] );
	};

	App.prototype.transitionView = function () {
		var self = this;
		return el( 'div', { class: 'jd-indice__step jd-indice__transition' }, [
			el( 'p', { class: 'is-style-lead', 'data-focus': '', text: T.transition } ),
			el( 'div', { class: 'wp-block-buttons' }, [
				el( 'div', { class: 'wp-block-button' }, [
					el( 'button', { type: 'button', class: 'wp-block-button__link wp-element-button', text: T.transitionB, onclick: function () { self.next(); } } ),
				] ),
			] ),
		] );
	};

	App.prototype.questionView = function ( q ) {
		var self = this;
		var multi = q.type !== 'single';
		var selected = this.answers[ q.id ] !== undefined ? this.answers[ q.id ] : ( multi ? [] : null );
		var feedback = el( 'div', { class: 'jd-indice__feedback', 'aria-live': 'polite' } );
		var nextBtn;

		function isLast() {
			return self.steps.slice( self.step + 1 ).every( function ( s ) { return s.kind !== 'question'; } );
		}

		function showFeedback() {
			feedback.innerHTML = '';
			if ( ! q.dim ) {
				return;
			}
			var pts = questionPoints( q, self.answers[ q.id ] );
			var msg;
			if ( q.type === 'criteria' ) {
				msg = pts <= 1 ? q.feedbacks.high : ( pts <= 4 ? q.feedbacks.mid : q.feedbacks.low );
			} else {
				msg = q.options[ self.answers[ q.id ] ][ 2 ];
			}
			var plus = contribution( q, pts );
			feedback.className = 'jd-indice__feedback ' + ( pts === 0 ? 'is-good' : ( pts >= 4 ? 'is-alert' : 'is-mid' ) );
			feedback.appendChild( el( 'p', { class: 'jd-indice__plus', text: plus ? '+' + plus + ' ' + T.points + ' · ' + D.dims[ q.dim ].label : '0 ' + T.points.replace( 'points', 'point' ) + ' · ' + D.dims[ q.dim ].label } ) );
			feedback.appendChild( el( 'p', { text: msg } ) );
		}

		var options = q.options.map( function ( opt, i ) {
			var pressed = multi ? selected.indexOf( i ) !== -1 : selected === i;
			return el( 'button', {
				type: 'button',
				class: 'jd-indice__opt',
				'aria-pressed': pressed ? 'true' : 'false',
				text: opt[ 0 ],
				onclick: function ( e ) {
					var b = e.currentTarget;
					if ( multi ) {
						var cur = ( self.answers[ q.id ] || [] ).slice();
						var at = cur.indexOf( i );
						if ( q.type === 'criteria' && i === q.none ) {
							cur = at === -1 ? [ i ] : [];
						} else {
							if ( at === -1 ) {
								cur.push( i );
							} else {
								cur.splice( at, 1 );
							}
							if ( q.type === 'criteria' ) {
								cur = cur.filter( function ( x ) { return x !== q.none; } );
							}
						}
						self.answers[ q.id ] = cur;
						Array.prototype.forEach.call( b.parentNode.children, function ( c, j ) {
							c.setAttribute( 'aria-pressed', cur.indexOf( j ) !== -1 ? 'true' : 'false' );
						} );
						nextBtn.disabled = q.type === 'criteria' && cur.length === 0;
						if ( q.type === 'criteria' && cur.length ) {
							showFeedback();
						} else {
							feedback.innerHTML = '';
						}
					} else {
						self.answers[ q.id ] = i;
						Array.prototype.forEach.call( b.parentNode.children, function ( c ) {
							c.setAttribute( 'aria-pressed', c === b ? 'true' : 'false' );
						} );
						nextBtn.disabled = false;
						showFeedback();
					}
				},
			} );
		} );

		nextBtn = el( 'button', {
			type: 'button',
			class: 'wp-block-button__link wp-element-button',
			text: isLast() ? T.result : ( q.dim ? T.next : T.continue ),
			disabled: ( q.type === 'single' && selected === null ) || ( q.type === 'criteria' && ! selected.length ) ? 'disabled' : null,
			onclick: function () {
				if ( q.type === 'multi' && self.answers[ q.id ] === undefined ) {
					self.answers[ q.id ] = [];
				}
				self.next();
			},
		} );

		var view = el( 'div', { class: 'jd-indice__step' }, [
			this.progress(),
			el( 'p', { class: 'jd-indice__q', 'data-focus': '', text: q.text } ),
			q.help ? el( 'p', { class: 'jd-indice__help', text: q.help } ) : null,
			el( 'div', { class: 'jd-indice__options' + ( multi ? ' is-multi' : '' ), role: 'group', 'aria-label': q.text }, options ),
			feedback,
			el( 'div', { class: 'jd-indice__nav' }, [
				el( 'button', { type: 'button', class: 'jd-indice__link', text: T.back, onclick: function () { self.prev(); } } ),
				el( 'div', { class: 'wp-block-buttons' }, [ el( 'div', { class: 'wp-block-button' }, [ nextBtn ] ) ] ),
			] ),
		] );
		if ( selected !== null && ( ! multi || selected.length ) && q.dim ) {
			setTimeout( showFeedback, 0 );
		}
		return view;
	};

	App.prototype.radar = function ( dims ) {
		var NS = 'http://www.w3.org/2000/svg';
		var size = 360;
		var c = size / 2;
		var R = 118;
		var n = DIM_KEYS.length;
		function pt( i, r ) {
			var a = ( -90 + ( 360 / n ) * i ) * Math.PI / 180;
			return [ c + r * Math.cos( a ), c + r * Math.sin( a ) ];
		}
		function svg( tag, attrs ) {
			var e = document.createElementNS( NS, tag );
			Object.keys( attrs ).forEach( function ( k ) { e.setAttribute( k, attrs[ k ] ); } );
			return e;
		}
		var root = svg( 'svg', { viewBox: '-90 -10 ' + ( size + 180 ) + ' ' + ( size + 20 ), class: 'jd-indice__radar', role: 'img', 'aria-label': 'Radar des cinq dimensions : ' + DIM_KEYS.map( function ( k ) { return D.dims[ k ].label + ' ' + dims[ k ]; } ).join( ', ' ) } );
		[ 25, 50, 75, 100 ].forEach( function ( v ) {
			root.appendChild( svg( 'polygon', { class: 'jd-radar__ring', points: DIM_KEYS.map( function ( k, i ) { return pt( i, ( R * v ) / 100 ).join( ',' ); } ).join( ' ' ) } ) );
		} );
		DIM_KEYS.forEach( function ( k, i ) {
			var p = pt( i, R );
			root.appendChild( svg( 'line', { class: 'jd-radar__axis', x1: c, y1: c, x2: p[ 0 ], y2: p[ 1 ] } ) );
		} );
		root.appendChild( svg( 'polygon', { class: 'jd-radar__value', points: DIM_KEYS.map( function ( k, i ) { return pt( i, Math.max( 3, ( R * dims[ k ] ) / 100 ) ).join( ',' ); } ).join( ' ' ) } ) );
		DIM_KEYS.forEach( function ( k, i ) {
			var p = pt( i, R + 26 );
			var anchor = Math.abs( p[ 0 ] - c ) < 8 ? 'middle' : ( p[ 0 ] > c ? 'start' : 'end' );
			var t = svg( 'text', { x: p[ 0 ], y: p[ 1 ], 'text-anchor': anchor, class: 'jd-radar__label' } );
			t.textContent = D.dims[ k ].label.replace( ' & automatisation', '' ) + ' ' + dims[ k ];
			root.appendChild( t );
		} );
		return root;
	};

	App.prototype.conclusions = function ( res ) {
		var sorted = DIM_KEYS.slice().sort( function ( a, b ) { return res.dims[ b ] - res.dims[ a ]; } );
		var weak = sorted[ 0 ];
		var strong = sorted[ sorted.length - 1 ];
		var unknown = SCORED.filter( function ( q ) {
			var a = this.answers[ q.id ];
			return typeof a === 'number' && /ne sai|ne savons|ne saurions|aucune idée|ignorons/i.test( q.options[ a ][ 0 ] );
		}, this ).length;
		var investigate;
		if ( unknown >= 3 ) {
			investigate = T.unknown;
		} else if ( weak !== 'ia' && res.dims.ia >= 50 ) {
			investigate = D.dims.ia.weak;
		} else {
			investigate = D.dims[ sorted[ 1 ] ].weak;
		}
		return [
			[ T.weak, D.dims[ weak ].weak ],
			[ T.strong, res.dims[ strong ] <= 40 ? D.dims[ strong ].strong : T.noStrong ],
			[ T.investigate, investigate ],
		];
	};

	App.prototype.result = function () {
		var self = this;
		var res = compute( this.answers );
		this.res = res;
		var providers = ( this.answers.q1b || [] ).map( function ( i ) { return D.questions[ 1 ].options[ i ][ 0 ]; } );

		var view = el( 'div', { class: 'jd-indice__result' }, [
			el( 'p', { class: 'is-style-eyebrow', text: T.scoreTitle } ),
			el( this.h, { class: 'jd-indice__score', 'data-focus': '' }, [
				el( 'span', { class: 'jd-indice__num', text: String( res.total ) } ),
				el( 'span', { class: 'jd-indice__den', text: ' / 100' } ),
			] ),
			el( 'p', { class: 'jd-indice__label', text: res.label } ),
			el( 'div', { class: 'jd-indice__charts' }, [
				this.radar( res.dims ),
				el( 'ul', { class: 'jd-indice__bars' }, DIM_KEYS.map( function ( k ) {
					return el( 'li', {}, [
						el( 'span', { class: 'jd-indice__barlabel', text: D.dims[ k ].label } ),
						el( 'span', { class: 'jd-indice__barval', text: String( res.dims[ k ] ) } ),
						el( 'span', { class: 'jd-indice__bartrack', 'aria-hidden': 'true' }, [ el( 'span', { style: 'width:' + res.dims[ k ] + '%' } ) ] ),
					] );
				} ) ),
			] ),
			el( 'div', { class: 'jd-indice__conclusions' }, this.conclusions( res ).map( function ( c ) {
				return el( 'div', { class: 'jd-indice__conclusion' }, [ el( 'p', { class: 'is-style-eyebrow', text: c[ 0 ] } ), el( 'p', { text: c[ 1 ] } ) ] );
			} ) ),
			providers.length ? el( 'p', { class: 'jd-indice__providers' }, [ el( 'strong', { text: T.providers + ' : ' } ), providers.join( ' · ' ) ] ) : null,
			this.cta(),
			this.mailForm(),
			this.share(),
			el( 'details', { class: 'jd-indice__method' }, [
				el( 'summary', { text: T.methodTitle } ),
				el( 'ul', {}, T.method.map( function ( m ) { return el( 'li', { text: m } ); } ) ),
				el( 'ul', { class: 'jd-indice__methoddims' }, DIM_KEYS.map( function ( k ) {
					var qs = SCORED.filter( function ( q ) { return q.dim === k; } ).map( function ( q ) { return q.text; } );
					return el( 'li', {}, [ el( 'strong', { text: D.dims[ k ].label + ' (' + qs.length + ' questions) : ' } ), qs.join( ' / ' ) ] );
				} ) ),
			] ),
			el( 'p', { class: 'jd-indice__nav' }, [
				el( 'button', { type: 'button', class: 'jd-indice__link', text: T.restart, onclick: function () { self.reset(); } } ),
			] ),
		] );
		this.render( view );
	};

	App.prototype.cta = function () {
		return el( 'div', { class: 'jd-indice__cta' }, [
			el( 'p', { class: 'jd-indice__ctatitle', text: T.ctaTitle } ),
			el( 'p', { text: T.ctaText } ),
			el( 'p', { class: 'is-style-eyebrow', text: T.ctaListK } ),
			el( 'ul', { class: 'is-style-arrows' }, T.ctaList.map( function ( x ) { return el( 'li', { text: x } ); } ) ),
			el( 'p', { class: 'jd-indice__price', text: T.ctaPrice } ),
			el( 'div', { class: 'wp-block-buttons' }, [
				el( 'div', { class: 'wp-block-button' }, [ el( 'a', { class: 'wp-block-button__link wp-element-button', href: D.urls.contact, text: T.ctaBook } ) ] ),
				el( 'div', { class: 'wp-block-button is-style-ghost' }, [ el( 'a', { class: 'wp-block-button__link wp-element-button', href: D.urls.livrable, text: T.ctaSample } ) ] ),
			] ),
		] );
	};

	App.prototype.mailForm = function () {
		var self = this;
		var id = 'jd-indice-mail-' + Math.random().toString( 36 ).slice( 2, 8 );
		var status = el( 'p', { class: 'jd-indice__status', 'aria-live': 'polite' } );
		var form = el( 'form', { class: 'jd-indice__mail', novalidate: 'novalidate' }, [
			el( 'p', { class: 'jd-indice__subtitle', text: T.mailTitle } ),
			el( 'label', { for: id, text: T.mailLabel } ),
			el( 'input', { id: id, type: 'email', name: 'email', required: 'required', autocomplete: 'email' } ),
			el( 'input', { type: 'text', name: 'site', tabindex: '-1', autocomplete: 'off', class: 'jd-hp', 'aria-hidden': 'true' } ),
			el( 'label', { class: 'jd-indice__check' }, [ el( 'input', { type: 'checkbox', name: 'contact' } ), ' ' + T.mailContact ] ),
			el( 'label', { class: 'jd-indice__check' }, [ el( 'input', { type: 'checkbox', name: 'news' } ), ' ' + T.mailNews ] ),
			el( 'p', { class: 'jd-indice__note', text: T.mailNote } ),
			el( 'div', { class: 'wp-block-buttons' }, [ el( 'div', { class: 'wp-block-button is-style-ghost' }, [ el( 'button', { type: 'submit', class: 'wp-block-button__link wp-element-button', text: T.mailSend } ) ] ) ] ),
			status,
		] );
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			var btn = form.querySelector( 'button[type=submit]' );
			btn.disabled = true;
			status.textContent = '';
			post( 'report', {
				email: form.email.value.trim(),
				contact: form.contact.checked,
				news: form.news.checked,
				site: form.site.value,
				answers: self.answers,
			} ).then( function ( r ) {
				if ( r.ok ) {
					status.textContent = T.mailOk;
					form.email.disabled = true;
				} else {
					status.textContent = r.error === 'pro' ? T.mailPro : T.mailErr;
					btn.disabled = false;
				}
			} ).catch( function () {
				status.textContent = T.mailErr;
				btn.disabled = false;
			} );
		} );
		return form;
	};

	App.prototype.share = function () {
		var self = this;
		var status = el( 'p', { class: 'jd-indice__status', 'aria-live': 'polite' } );
		var btn = el( 'button', { type: 'button', class: 'jd-indice__link', text: T.shareBtn + ' →' } );
		btn.addEventListener( 'click', function () {
			btn.disabled = true;
			post( 'share', { answers: self.answers, site: '' } ).then( function ( r ) {
				status.textContent = r.ok ? T.shareOk : T.mailErr;
				if ( ! r.ok ) {
					btn.disabled = false;
				}
			} ).catch( function () {
				status.textContent = T.mailErr;
				btn.disabled = false;
			} );
		} );
		return el( 'div', { class: 'jd-indice__share' }, [
			el( 'p', { class: 'jd-indice__subtitle', text: T.shareTitle } ),
			el( 'p', { class: 'jd-indice__note', text: T.shareText } ),
			btn,
			status,
		] );
	};

	/* ---------- montage ---------- */

	function mount( root ) {
		if ( ! root.jdIndice ) {
			root.jdIndice = new App( root );
		}
		return root.jdIndice;
	}

	function init() {
		document.querySelectorAll( '.jd-indice' ).forEach( function ( root ) {
			if ( ! root.closest( 'dialog' ) ) {
				mount( root );
			}
		} );

		var dialog = document.getElementById( 'jd-indice-dialog' );
		if ( ! dialog || typeof dialog.showModal !== 'function' ) {
			return; // Sans <dialog>, les liens mènent simplement à la page autonome.
		}
		var pagePath = new URL( D.urls.page, location.href ).pathname.replace( /\/$/, '' );
		var opener = null;

		document.addEventListener( 'click', function ( e ) {
			var a = e.target.closest ? e.target.closest( 'a[href]' ) : null;
			if ( ! a || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0 ) {
				return;
			}
			var u = new URL( a.href, location.href );
			if ( u.origin !== location.origin || u.pathname.replace( /\/$/, '' ) !== pagePath ) {
				return;
			}
			if ( dialog.contains( a ) ) {
				return; // « Ouvrir en pleine page » suit le lien.
			}
			e.preventDefault();
			opener = a;
			mount( dialog.querySelector( '.jd-indice' ) );
			dialog.showModal();
			document.documentElement.classList.add( 'jd-dialog-open' );
			var f = dialog.querySelector( '[data-focus]' );
			if ( f ) {
				f.setAttribute( 'tabindex', '-1' );
				f.focus();
			}
		} );

		dialog.querySelector( '.jd-indice-dialog__close' ).addEventListener( 'click', function () { dialog.close(); } );
		dialog.addEventListener( 'click', function ( e ) {
			if ( e.target === dialog ) {
				dialog.close(); // Clic sur le fond.
			}
		} );
		dialog.addEventListener( 'close', function () {
			document.documentElement.classList.remove( 'jd-dialog-open' );
			if ( opener ) {
				opener.focus();
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
