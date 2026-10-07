<?php
/**
 * Variations de styles de blocs (sélectionnables dans l'éditeur, panneau « Styles »).
 * Le rendu est défini dans assets/css/theme.css.
 *
 * @package journalism-design
 */

defined( 'ABSPATH' ) || exit;

function jd_register_block_styles() {
	$styles = array(
		'core/paragraph' => array(
			'eyebrow'  => __( 'Kicker [ mono ]', 'journalism-design' ),
			'lead'     => __( 'Chapô', 'journalism-design' ),
			'question' => __( 'Affirmation en capitales', 'journalism-design' ),
			'price'    => __( 'Encadré tarif', 'journalism-design' ),
		),
		'core/heading'   => array(
			'numbered' => __( 'Numéro de section', 'journalism-design' ),
			'rule'     => __( 'Filet au-dessus', 'journalism-design' ),
		),
		'core/list'      => array(
			'index'  => __( 'Index à filets', 'journalism-design' ),
			'arrows' => __( 'Puces carrées', 'journalism-design' ),
			'tags'   => __( 'Pastilles', 'journalism-design' ),
			'columns' => __( 'Deux colonnes à filets', 'journalism-design' ),
		),
		'core/group'     => array(
			'card'      => __( 'Carte', 'journalism-design' ),
			'rule-top'  => __( 'Filet épais au-dessus', 'journalism-design' ),
			'principle' => __( 'Cellule numérotée', 'journalism-design' ),
		),
		'core/button'    => array(
			'accent' => __( 'Contact (cyan cerclé)', 'journalism-design' ),
			'ghost'  => __( 'Secondaire (crème cerclé)', 'journalism-design' ),
		),
		'core/columns'   => array(
			'ruled' => __( 'Colonnes séparées par des filets', 'journalism-design' ),
		),
	);

	foreach ( $styles as $block => $variations ) {
		foreach ( $variations as $name => $label ) {
			register_block_style( $block, array( 'name' => $name, 'label' => $label ) );
		}
	}
}
add_action( 'init', 'jd_register_block_styles' );
