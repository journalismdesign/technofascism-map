<?php
/**
 * Fonctions utilitaires.
 *
 * @package journalism-design
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL d'une page du site à partir de son slug.
 * Utilise le permalien réel si la page existe, sinon une URL construite.
 *
 * @param string $path   Slug (ou chemin) de la page. Vide = accueil.
 * @param string $anchor Ancre optionnelle, sans « # ».
 * @return string URL échappée.
 */
function jd_url( $path = '', $anchor = '' ) {
	$url = home_url( '/' );
	if ( $path ) {
		$page = get_page_by_path( $path );
		$url  = $page ? get_permalink( $page ) : home_url( '/' . trailingslashit( $path ) );
	}
	if ( $anchor ) {
		$url .= '#' . $anchor;
	}
	return esc_url( $url );
}

/**
 * Liens externes de l'écosystème, modifiables par filtre
 * (ex. dans une extension « mu-plugin ») sans toucher au thème.
 *
 * @param string $key synth|inferences|ressources.
 * @return string URL échappée.
 */
function jd_external_url( $key ) {
	$urls = apply_filters(
		'jd_external_urls',
		array(
			'synth'      => 'https://synthmedia.fr',
			'inferences' => '#', // À renseigner : URL d'Inférences.
			'ressources' => '#', // À renseigner : page ou rubrique Ressources.
			'rendezvous' => '#', // Facultatif : outil de prise de rendez-vous (Cal.com, Calendly…).
		)
	);
	return esc_url( isset( $urls[ $key ] ) ? $urls[ $key ] : '#' );
}

/**
 * Lien « Réserver une journée de diagnostic ».
 * Outil de rendez-vous s'il est renseigné (filtre `jd_external_urls`, clé `rendezvous`),
 * sinon formulaire de contact avec « Journée de diagnostic » précoché.
 *
 * @return string URL échappée.
 */
function jd_booking_url() {
	$ext = jd_external_url( 'rendezvous' );
	if ( '#' !== $ext ) {
		return $ext;
	}
	$page = get_page_by_path( 'contact' );
	$base = $page ? get_permalink( $page ) : home_url( '/contact/' );
	return esc_url( add_query_arg( 'accompagnement', rawurlencode( 'Journée de diagnostic' ), $base ) . '#jd-contact' );
}
