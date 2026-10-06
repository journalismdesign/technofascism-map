<?php
/**
 * Journalism.design — fonctions du thème.
 *
 * @package journalism-design
 */

defined( 'ABSPATH' ) || exit;

define( 'JD_VERSION', '1.8.0' );
define( 'JD_DIR', get_template_directory() );
define( 'JD_URI', get_template_directory_uri() );

require JD_DIR . '/inc/helpers.php';
require JD_DIR . '/inc/block-styles.php';
require JD_DIR . '/inc/contact-form.php';
require JD_DIR . '/inc/setup-content.php';
require JD_DIR . '/inc/indice.php';

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
	wp_enqueue_script(
		'journalism-design-marquee',
		JD_URI . '/assets/js/marquee.js',
		array(),
		JD_VERSION . '.' . filemtime( JD_DIR . '/assets/js/marquee.js' ),
		array( 'in_footer' => true, 'strategy' => 'defer' )
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

/**
 * Logo sur la page de connexion.
 */
function jd_login_logo() {
	$logo = get_theme_file_uri( 'assets/images/logo-journalism-design.png' );
	echo '<style>
		body.login { background: #F4EFE7; }
		#login h1 a { background-image: url(' . esc_url( $logo ) . '); background-size: contain; background-position: center; width: 100%; max-width: 320px; height: 44px; }
		.login form { border: 1px solid #0A0F0E; border-radius: 0; box-shadow: 6px 6px 0 #0A0F0E; background: #F4EFE7; }
		.login input[type=text], .login input[type=password] { border-radius: 0; border-color: #0A0F0E; background: #F4EFE7; }
		.login #backtoblog a, .login #nav a { color: #0A0F0E; }
		body.login.wp-core-ui .button-primary { background: #0A0F0E; border-color: #0A0F0E; color: #01FFE0; border-radius: 0; box-shadow: 4px 4px 0 #01FFE0; }
		body.login.wp-core-ui .button-primary:hover, body.login.wp-core-ui .button-primary:focus { background: #0A0F0E; color: #01FFE0; box-shadow: none; transform: translate(4px, 4px); }
	</style>';
}
add_action( 'login_enqueue_scripts', 'jd_login_logo' );

function jd_login_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'jd_login_url' );

function jd_login_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'jd_login_title' );
