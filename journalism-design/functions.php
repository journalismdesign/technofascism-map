<?php
/**
 * Journalism.design — fonctions du thème.
 *
 * @package journalism-design
 */

defined( 'ABSPATH' ) || exit;

define( 'JD_VERSION', '1.1.0' );
define( 'JD_DIR', get_template_directory() );
define( 'JD_URI', get_template_directory_uri() );

require JD_DIR . '/inc/helpers.php';
require JD_DIR . '/inc/block-styles.php';
require JD_DIR . '/inc/contact-form.php';
require JD_DIR . '/inc/setup-content.php';

/**
 * Supports du thème.
 */
function jd_setup() {
	load_theme_textdomain( 'journalism-design', JD_DIR . '/languages' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_editor_style( 'assets/css/theme.css' );
	// Les motifs du répertoire officiel ne correspondent pas à la charte.
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'jd_setup' );

/**
 * Feuille de style principale (front).
 */
function jd_enqueue() {
	wp_enqueue_style(
		'journalism-design',
		JD_URI . '/assets/css/theme.css',
		array(),
		JD_VERSION . '.' . filemtime( JD_DIR . '/assets/css/theme.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'jd_enqueue' );

/**
 * Catégories de motifs.
 */
function jd_pattern_categories() {
	register_block_pattern_category( 'jd-sections', array( 'label' => __( 'Journalism.design — Sections', 'journalism-design' ) ) );
	register_block_pattern_category( 'jd-cta', array( 'label' => __( 'Journalism.design — Appels à l’action', 'journalism-design' ) ) );
}
add_action( 'init', 'jd_pattern_categories', 9 );
