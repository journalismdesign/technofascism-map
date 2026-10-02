<?php
/**
 * Contenu de départ : création des pages éditables à l'activation du thème.
 *
 * Chaque page est composée de sections (fichiers du dossier /patterns).
 * Le contenu est copié dans la page sous forme de blocs : il se modifie
 * ensuite librement dans l'éditeur, sans toucher au thème.
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
				'sections' => array( 'home-hero', 'home-questions', 'approach', 'levels-overview', 'terrains', 'formations-teaser', 'synth', 'first-step' ),
				'front'    => true,
			),
			'diagnostic-strategie'       => array(
				'title'    => 'Diagnostic & stratégie',
				'sections' => array( 'diagnostic-hero', 'diagnostic-mission', 'diagnostic-day', 'levels-overview' ),
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
				'sections' => array( 'formations-hero', 'formations-list' ),
			),
			'cas-clients'                => array(
				'title'    => 'Cas clients',
				'sections' => array( 'cases-hero', 'case-study' ),
				// Brouillon : aucun cas client n'a été fourni, rien n'est inventé.
				'status'   => 'draft',
			),
			'a-propos'                   => array(
				'title'    => 'À propos',
				'sections' => array( 'about-hero', 'about-gerald', 'about-independence', 'about-open-source', 'about-responsable', 'synth' ),
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
 * Motifs « page complète » proposés à la création d'une nouvelle page.
 */
function jd_register_page_patterns() {
	foreach ( jd_site_map() as $slug => $page ) {
		register_block_pattern(
			'journalism-design/page-' . $slug,
			array(
				'title'      => sprintf( /* translators: %s: titre de page */ __( 'Page — %s', 'journalism-design' ), $page['title'] ),
				'categories' => array( 'jd-pages' ),
				'blockTypes' => array( 'core/post-content' ),
				'postTypes'  => array( 'page' ),
				'content'    => jd_compose( $page['sections'] ),
			)
		);
	}
}
add_action( 'init', 'jd_register_page_patterns', 20 );

/**
 * Crée les pages manquantes. Ne modifie jamais une page existante.
 *
 * @return int Nombre de pages créées.
 */
function jd_install_content() {
	$created = 0;
	foreach ( jd_site_map() as $slug => $page ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $existing ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_title'   => $page['title'],
					'post_name'    => $slug,
					'post_status'  => isset( $page['status'] ) ? $page['status'] : 'publish',
					'post_content' => wp_slash( jd_compose( $page['sections'] ) ),
					'meta_input'   => array( '_wp_page_template' => 'page-editorial' ),
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			$existing = get_post( $id );
			++$created;
		}
		$front = (int) get_option( 'page_on_front' );
		$front_ok = 'page' === get_option( 'show_on_front' ) && $front && 'publish' === get_post_status( $front );
		if ( ! empty( $page['front'] ) && $existing && ! $front_ok ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $existing->ID );
		}
	}
	update_option( 'jd_content_version', JD_VERSION );
	return $created;
}

/**
 * À l'activation : création du contenu et permaliens lisibles si besoin.
 */
function jd_after_switch_theme() {
	jd_install_content();
	if ( '' === get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules();
	}
}
add_action( 'after_switch_theme', 'jd_after_switch_theme' );

/**
 * Page d'outil : Apparence › Contenu Journalism.design
 * pour recréer les pages supprimées.
 */
function jd_admin_menu() {
	add_theme_page(
		__( 'Contenu Journalism.design', 'journalism-design' ),
		__( 'Contenu Journalism.design', 'journalism-design' ),
		'edit_theme_options',
		'jd-content',
		'jd_admin_page'
	);
}
add_action( 'admin_menu', 'jd_admin_menu' );

function jd_admin_page() {
	$done = null;
	if ( isset( $_POST['jd_install'] ) && check_admin_referer( 'jd_install' ) ) {
		$done = jd_install_content();
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Contenu Journalism.design', 'journalism-design' ) . '</h1>';
	if ( null !== $done ) {
		/* translators: %d: nombre de pages */
		echo '<div class="notice notice-success"><p>' . esc_html( sprintf( _n( '%d page créée.', '%d pages créées.', $done, 'journalism-design' ), $done ) ) . '</p></div>';
	}
	echo '<p>' . esc_html__( 'Recrée les pages du thème qui n’existent pas encore (les pages existantes ne sont jamais modifiées).', 'journalism-design' ) . '</p><table class="widefat striped" style="max-width:640px"><tbody>';
	foreach ( jd_site_map() as $slug => $page ) {
		$p = get_page_by_path( $slug, OBJECT, 'page' );
		echo '<tr><td>' . esc_html( $page['title'] ) . '</td><td><code>/' . esc_html( $slug ) . '/</code></td><td>';
		echo $p ? '<a href="' . esc_url( get_edit_post_link( $p ) ) . '">' . esc_html( get_post_status_object( $p->post_status )->label ) . '</a>' : esc_html__( 'absente', 'journalism-design' );
		echo '</td></tr>';
	}
	echo '</tbody></table><form method="post" style="margin-top:1em">';
	wp_nonce_field( 'jd_install' );
	submit_button( __( 'Créer les pages manquantes', 'journalism-design' ), 'primary', 'jd_install' );
	echo '</form></div>';
}
