<?php
/**
 * Contenu de départ : création des pages éditables à l'activation du thème.
 *
 * Chaque page est composée de sections (fichiers du dossier /patterns).
 * Le contenu est écrit directement dans la page sous forme de blocs
 * Gutenberg : il se modifie ensuite librement dans l'éditeur de la page,
 * sans composition à choisir et sans toucher au thème.
 *
 * @package journalism-design
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plan du site : slug => réglages de la page.
 *
 * @return array
 */
function jd_site_map() {
	return apply_filters(
		'jd_site_map',
		array(
			'accueil'                    => array(
				'title'    => 'Accueil',
				'sections' => array( 'home-hero', 'marquee', 'studio', 'indice-teaser', 'use-cases', 'levels-overview', 'diagnostic-day', 'references-teaser', 'home-questions', 'commitments', 'synth', 'terrains', 'first-step' ),
				'front'    => true,
			),
			'diagnostic-strategie'       => array(
				'title'    => 'Diagnostic & stratégie',
				'sections' => array( 'diagnostic-hero', 'diagnostic-mission', 'diagnostic-deliverable', 'diagnostic-day', 'levels-overview' ),
			),
			'transformation-prototypage' => array(
				'title'    => 'Transformation & prototypage',
				'sections' => array( 'transformation-hero', 'transformation-trajectories', 'transformation-prototype', 'transformation-ia', 'transformation-desirable', 'levels-overview' ),
			),
			'gouvernance-souverainete'   => array(
				'title'    => 'Gouvernance & souveraineté',
				'sections' => array( 'gouvernance-hero', 'gouvernance-reversibilite', 'gouvernance-doctrine', 'gouvernance-regles', 'levels-overview' ),
			),
			'formations'                 => array(
				'title'    => 'Formations',
				'sections' => array( 'formations-hero', 'formations-levels', 'formations-faq', 'first-step' ),
			),
			'cas-clients'                => array(
				'title'    => 'Cas clients',
				'sections' => array( 'cases-hero', 'cases-missions', 'cases-references', 'first-step' ),
			),
			'a-propos'                   => array(
				'title'    => 'À propos',
				'sections' => array( 'about-hero', 'studio', 'about-gerald', 'approach', 'about-independence', 'about-open-source', 'about-responsable', 'synth' ),
			),
			'synth'                      => array(
				'title'    => 'SYNTH',
				'sections' => array( 'synth-hero', 'synth-coverage', 'synth-manifesto', 'synth-link', 'first-step' ),
			),
			'indice-dependance-numerique' => array(
				'title'    => 'Indice de dépendance numérique',
				'sections' => array( 'indice' ),
			),
			'contact'                    => array(
				'title'    => 'Contact',
				'sections' => array( 'contact' ),
			),
			'mentions-legales'           => array(
				'title'    => 'Mentions légales',
				'sections' => array( 'legal' ),
				'status'   => 'draft',
			),
		)
	);
}

/**
 * Rend le contenu d'un fichier de motif (sans passer par le registre,
 * qui n'est pas forcément prêt au moment de l'activation).
 *
 * @param string $slug Nom du fichier sans extension.
 * @return string
 */
function jd_render_section( $slug ) {
	$file = JD_DIR . '/patterns/' . sanitize_file_name( $slug ) . '.php';
	if ( ! file_exists( $file ) ) {
		return '';
	}
	ob_start();
	include $file;
	return trim( ob_get_clean() );
}

/**
 * Assemble plusieurs sections.
 *
 * @param string[] $sections Slugs.
 * @return string
 */
function jd_compose( $sections ) {
	return implode( "\n\n", array_filter( array_map( 'jd_render_section', $sections ) ) );
}

/**
 * Contenu d'une page ne comportant aucun texte ni bloc significatif.
 *
 * @param WP_Post $post Page.
 * @return bool
 */
function jd_page_is_empty( $post ) {
	$content = preg_replace( '/<!--.*?-->/s', '', (string) $post->post_content );
	return '' === trim( wp_strip_all_tags( $content ) );
}

/**
 * La page contient-elle déjà le contenu fourni par le thème ?
 *
 * @param WP_Post $post Page.
 * @return bool
 */
function jd_page_has_theme_content( $post ) {
	return false !== strpos( (string) $post->post_content, 'jd-section' );
}

/**
 * Mémorise l'empreinte du contenu inséré par le thème, pour savoir
 * ensuite si la page a été modifiée à la main.
 *
 * @param int $id Page.
 */
function jd_stamp_page( $id ) {
	update_post_meta( $id, '_jd_content_hash', md5( (string) get_post_field( 'post_content', $id, 'raw' ) ) );
	update_post_meta( $id, '_jd_content_version', JD_VERSION );
}

/**
 * La page contient-elle exactement le contenu inséré par le thème (non modifié) ?
 *
 * @param WP_Post $post Page.
 * @return bool
 */
function jd_page_is_pristine( $post ) {
	$hash = get_post_meta( $post->ID, '_jd_content_hash', true );
	return $hash && hash_equals( $hash, md5( (string) $post->post_content ) );
}

/**
 * La page contient-elle le contenu non modifié de la version actuelle du thème ?
 *
 * @param WP_Post $post Page.
 * @return bool
 */
function jd_page_is_current( $post ) {
	return JD_VERSION === get_post_meta( $post->ID, '_jd_content_version', true ) && jd_page_is_pristine( $post );
}

/**
 * Crée ou remplit une page du plan du site.
 *
 * @param string $slug  Slug de la page.
 * @param array  $page  Réglages (jd_site_map).
 * @param bool   $force   Remplacer un contenu existant (une révision est conservée).
 * @param bool   $upgrade Mettre à jour une page dont le contenu du thème n'a pas été modifié.
 * @return string created|filled|replaced|updated|kept|error
 */
function jd_fill_page( $slug, $page, $force = false, $upgrade = false ) {
	$content  = wp_slash( jd_compose( $page['sections'] ) );
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	if ( ! $existing ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_status'  => isset( $page['status'] ) ? $page['status'] : 'publish',
				'post_content' => $content,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return 'error';
		}
		jd_stamp_page( $id );
		return 'created';
	}

	// Le titre de page n'est jamais affiché : on retire un éventuel modèle
	// hérité d'un autre thème pour utiliser le modèle « Page » du thème.
	if ( get_post_meta( $existing->ID, '_wp_page_template', true ) ) {
		delete_post_meta( $existing->ID, '_wp_page_template' );
	}

	$empty = jd_page_is_empty( $existing );
	// Contenu inséré par une version du thème antérieure à 1.2.0, qui ne
	// mémorisait pas d'empreinte : il est mis à jour comme un contenu non
	// modifié (l'ancienne version est conservée dans les révisions).
	$legacy  = ! get_post_meta( $existing->ID, '_jd_content_hash', true ) && jd_page_has_theme_content( $existing );
	$updated = $upgrade && ! $empty && ! jd_page_is_current( $existing ) && ( $legacy || jd_page_is_pristine( $existing ) );
	if ( ! $force && ! $empty && ! $updated ) {
		return 'kept';
	}
	if ( ! $empty && wp_revisions_enabled( $existing ) ) {
		// Sauvegarde explicite de la version actuelle avant remplacement.
		_wp_put_post_revision( $existing );
	}
	$target = isset( $page['status'] ) ? $page['status'] : 'publish';
	$args   = array(
		'ID'           => $existing->ID,
		'post_content' => $content,
	);
	// Une page du thème autrefois livrée en brouillon (ex. Cas clients) est
	// publiée quand le thème fournit désormais son contenu.
	if ( 'draft' === $existing->post_status && 'publish' === $target ) {
		$args['post_status'] = 'publish';
	}
	$result = wp_update_post( $args, true );
	if ( is_wp_error( $result ) ) {
		return 'error';
	}
	jd_stamp_page( $existing->ID );
	if ( $empty ) {
		return 'filled';
	}
	return $updated && ! $force ? 'updated' : 'replaced';
}

/**
 * Définit « Accueil » comme page d'accueil.
 *
 * @param bool $force Même si une autre page d'accueil est déjà définie.
 */
function jd_set_front_page( $force = false ) {
	$front    = (int) get_option( 'page_on_front' );
	$front_ok = 'page' === get_option( 'show_on_front' ) && $front && 'publish' === get_post_status( $front );
	if ( $front_ok && ! $force ) {
		return;
	}
	foreach ( jd_site_map() as $slug => $page ) {
		if ( ! empty( $page['front'] ) ) {
			$home = get_page_by_path( $slug, OBJECT, 'page' );
			if ( $home ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $home->ID );
			}
		}
	}
}

/**
 * Crée les pages absentes et remplit les pages vides.
 * Les pages ayant déjà leur propre contenu sont conservées et signalées.
 *
 * @param bool        $force   Remplacer aussi les contenus existants.
 * @param string|null $only    Ne traiter qu'une page (slug).
 * @param bool        $upgrade Mettre à jour les pages du thème non modifiées.
 * @return array Statut par slug.
 */
function jd_install_content( $force = false, $only = null, $upgrade = false ) {
	$report = array();
	foreach ( jd_site_map() as $slug => $page ) {
		if ( $only && $only !== $slug ) {
			continue;
		}
		$report[ $slug ] = jd_fill_page( $slug, $page, $force, $upgrade );
	}

	$kept     = array();
	$outdated = array();
	foreach ( jd_site_map() as $slug => $page ) {
		$p = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $p || jd_page_is_empty( $p ) || jd_page_is_current( $p ) ) {
			continue;
		}
		if ( jd_page_has_theme_content( $p ) ) {
			$outdated[] = $slug;
		} else {
			$kept[] = $slug;
		}
	}
	update_option( 'jd_pages_kept', $kept, false );
	update_option( 'jd_pages_outdated', $outdated, false );
	$auto = array_keys( array_filter( $report, function ( $status ) { return 'updated' === $status; } ) );
	if ( $auto ) {
		update_option( 'jd_pages_updated', $auto, false );
	}
	update_option( 'jd_content_version', JD_VERSION );
	return $report;
}

/**
 * À l'activation : pages préremplies, page d'accueil, permaliens lisibles.
 */
function jd_after_switch_theme() {
	jd_install_content();
	jd_set_front_page( true );
	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();
	}
}
add_action( 'after_switch_theme', 'jd_after_switch_theme' );

/**
 * Mise à jour du thème sans réactivation (fichiers remplacés) :
 * on crée/remplit ce qui manque au premier passage dans l'administration.
 */
function jd_maybe_install_on_update() {
	if ( JD_VERSION === get_option( 'jd_content_version' ) || ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	jd_install_content( false, null, true );
	jd_set_front_page();
}
add_action( 'admin_init', 'jd_maybe_install_on_update' );

/**
 * Avertit quand des pages existantes n'ont pas été préremplies.
 */
function jd_admin_notice() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$screen   = get_current_screen();
	$map      = jd_site_map();
	$messages = array(
		'jd_pages_kept'     => __( 'Journalism.design : ces pages existaient déjà avec leur propre contenu et n’ont pas été préremplies :', 'journalism-design' ),
		'jd_pages_outdated' => __( 'Journalism.design : ces pages contiennent une version précédente du thème que vous avez modifiée, et n’ont pas reçu la nouvelle version :', 'journalism-design' ),
	);
	$updated = (array) get_option( 'jd_pages_updated', array() );
	$visible = $screen && in_array( $screen->id, array( 'dashboard', 'edit-page', 'themes' ), true );
	if ( $updated && $visible ) {
		$titles = array();
		foreach ( $updated as $slug ) {
			$titles[] = isset( $map[ $slug ] ) ? $map[ $slug ]['title'] : $slug;
		}
		printf(
			'<div class="notice notice-success is-dismissible"><p>%1$s <strong>%2$s</strong>. %3$s</p></div>',
			esc_html__( 'Journalism.design : pages mises à jour avec la nouvelle version du thème :', 'journalism-design' ),
			esc_html( implode( ', ', $titles ) ),
			esc_html__( 'Les versions précédentes sont conservées dans les révisions de chaque page.', 'journalism-design' )
		);
		delete_option( 'jd_pages_updated' );
	}
	if ( $screen && 'appearance_page_jd-content' === $screen->id ) {
		return;
	}
	foreach ( $messages as $option => $message ) {
		$slugs = (array) get_option( $option, array() );
		if ( ! $slugs ) {
			continue;
		}
		$titles = array();
		foreach ( $slugs as $slug ) {
			$titles[] = isset( $map[ $slug ] ) ? $map[ $slug ]['title'] : $slug;
		}
		printf(
			'<div class="notice notice-warning"><p>%1$s <strong>%2$s</strong>. <a href="%3$s">%4$s</a></p></div>',
			esc_html( $message ),
			esc_html( implode( ', ', $titles ) ),
			esc_url( admin_url( 'themes.php?page=jd-content' ) ),
			esc_html__( 'Les remplacer par le contenu du thème', 'journalism-design' )
		);
	}
}
add_action( 'admin_notices', 'jd_admin_notice' );

/**
 * Page d'outil : Apparence › Contenu Journalism.design.
 */
function jd_admin_menu() {
	add_theme_page(
		__( 'Contenu Journalism.design', 'journalism-design' ),
		__( 'Contenu Journalism.design', 'journalism-design' ),
		'edit_pages',
		'jd-content',
		'jd_admin_page'
	);
}
add_action( 'admin_menu', 'jd_admin_menu' );

function jd_admin_page() {
	$report = null;
	if ( isset( $_POST['jd_action'] ) && check_admin_referer( 'jd_content' ) ) {
		$action = sanitize_key( wp_unslash( $_POST['jd_action'] ) );
		$only   = isset( $_POST['jd_slug'] ) ? sanitize_title( wp_unslash( $_POST['jd_slug'] ) ) : null;
		$report = jd_install_content( 'replace' === $action || 'replace_all' === $action, $only ? $only : null );
		jd_set_front_page();
	}

	$labels = array(
		'created'  => __( 'créée', 'journalism-design' ),
		'filled'   => __( 'remplie', 'journalism-design' ),
		'replaced' => __( 'remplacée (révision conservée)', 'journalism-design' ),
		'updated'  => __( 'mise à jour (révision conservée)', 'journalism-design' ),
		'kept'     => __( 'conservée', 'journalism-design' ),
		'error'    => __( 'erreur', 'journalism-design' ),
	);

	echo '<div class="wrap"><h1>' . esc_html__( 'Contenu Journalism.design', 'journalism-design' ) . '</h1>';
	if ( $report ) {
		echo '<div class="notice notice-success"><p>';
		$out = array();
		foreach ( $report as $slug => $status ) {
			$out[] = esc_html( jd_site_map()[ $slug ]['title'] . ' : ' . $labels[ $status ] );
		}
		echo implode( ' · ', $out ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</p></div>';
	}
	echo '<p>' . esc_html__( 'Chaque page du site est préremplie avec ses blocs Gutenberg. « Remplacer » réécrit la page avec le contenu du thème ; l’ancienne version reste disponible dans les révisions de la page.', 'journalism-design' ) . '</p>';
	echo '<table class="widefat striped" style="max-width:860px"><thead><tr><th>' . esc_html__( 'Page', 'journalism-design' ) . '</th><th>' . esc_html__( 'Adresse', 'journalism-design' ) . '</th><th>' . esc_html__( 'Contenu', 'journalism-design' ) . '</th><th></th></tr></thead><tbody>';
	foreach ( jd_site_map() as $slug => $page ) {
		$p = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $p ) {
			$state  = __( 'absente', 'journalism-design' );
			$action = 'fill';
			$button = __( 'Créer', 'journalism-design' );
		} elseif ( jd_page_is_empty( $p ) ) {
			$state  = __( 'vide', 'journalism-design' );
			$action = 'fill';
			$button = __( 'Remplir', 'journalism-design' );
		} else {
			if ( jd_page_is_current( $p ) ) {
				$state = __( 'contenu du thème, à jour', 'journalism-design' );
			} elseif ( jd_page_has_theme_content( $p ) ) {
				$state = __( 'version précédente du thème ou contenu modifié', 'journalism-design' );
			} else {
				$state = __( 'contenu personnalisé', 'journalism-design' );
			}
			$action = 'replace';
			$button = __( 'Remplacer', 'journalism-design' );
		}
		echo '<tr><td>' . ( $p ? '<a href="' . esc_url( get_edit_post_link( $p ) ) . '">' . esc_html( $page['title'] ) . '</a> <span class="description">(' . esc_html( get_post_status_object( $p->post_status )->label ) . ')</span>' : esc_html( $page['title'] ) ) . '</td>';
		echo '<td><code>/' . esc_html( $slug ) . '/</code></td><td>' . esc_html( $state ) . '</td><td>';
		echo '<form method="post" style="margin:0"' . ( 'replace' === $action ? ' onsubmit="return confirm(\'' . esc_js( __( 'Remplacer le contenu de cette page ? L’ancienne version restera dans les révisions.', 'journalism-design' ) ) . '\')"' : '' ) . '>';
		wp_nonce_field( 'jd_content' );
		echo '<input type="hidden" name="jd_action" value="' . esc_attr( $action ) . '"><input type="hidden" name="jd_slug" value="' . esc_attr( $slug ) . '">';
		submit_button( $button, 'secondary small', 'submit', false );
		echo '</form></td></tr>';
	}
	echo '</tbody></table><div style="margin-top:1.5em;display:flex;gap:.5em">';
	echo '<form method="post">';
	wp_nonce_field( 'jd_content' );
	echo '<input type="hidden" name="jd_action" value="fill">';
	submit_button( __( 'Créer et remplir les pages absentes ou vides', 'journalism-design' ), 'primary', 'submit', false );
	echo '</form><form method="post" onsubmit="return confirm(\'' . esc_js( __( 'Remplacer le contenu de toutes les pages ? Les anciennes versions resteront dans les révisions.', 'journalism-design' ) ) . '\')">';
	wp_nonce_field( 'jd_content' );
	echo '<input type="hidden" name="jd_action" value="replace_all">';
	submit_button( __( 'Tout remplacer', 'journalism-design' ), 'secondary', 'submit', false );
	echo '</form></div></div>';
}
