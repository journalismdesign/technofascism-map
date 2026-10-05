/**
 * Bandeau défilant : duplique le texte pour une boucle continue.
 * Le texte reste lisible tel quel sans JavaScript ou si l'utilisateur
 * a demandé de réduire les animations.
 */
( function () {
	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}
	document.querySelectorAll( '.jd-marquee__text' ).forEach( function ( el ) {
		var html = el.innerHTML;
		var track = document.createElement( 'span' );
		track.className = 'jd-marquee__track';
		for ( var i = 0; i < 4; i++ ) {
			var item = document.createElement( 'span' );
			item.innerHTML = html + ' ✦';
			if ( i > 0 ) {
				item.setAttribute( 'aria-hidden', 'true' );
			}
			track.appendChild( item );
		}
		el.innerHTML = '';
		el.appendChild( track );
		el.parentElement.classList.add( 'is-animated' );
		var seconds = Math.max( 20, Math.round( track.scrollWidth / 2 / 60 ) );
		track.style.setProperty( '--jd-marquee-duration', seconds + 's' );
	} );
} )();
