<?php
/**
 * Title: Pied de page du site
 * Slug: journalism-design/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: false
 *
 * @package journalism-design
 */

// Liens légaux : uniquement vers des pages publiées (pas de lien vers une 404).
$jd_legal_links = array();
$jd_legal_page  = get_page_by_path( 'mentions-legales' );
if ( $jd_legal_page && 'publish' === $jd_legal_page->post_status ) {
	$jd_legal_links[] = '<a href="' . esc_url( get_permalink( $jd_legal_page ) ) . '">Mentions légales</a>';
}
if ( get_privacy_policy_url() ) {
	$jd_legal_links[] = '<a href="' . esc_url( get_privacy_policy_url() ) . '">Confidentialité</a>';
}
?>
<!-- wp:group {"align":"full","className":"jd-footer","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull jd-footer"><!-- wp:columns {"align":"wide","className":"jd-footer__cols"} -->
<div class="wp-block-columns alignwide jd-footer__cols"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:image {"sizeSlug":"full","linkDestination":"custom","className":"jd-footer__logo"} -->
<figure class="wp-block-image size-full jd-footer__logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-journalism-design.png' ) ); ?>" alt="Journalism.design"/></a></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"jd-footer__tagline"} -->
<p class="jd-footer__tagline"><strong>Concevoir un numérique utile, désirable et maîtrisé.</strong></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"jd-footer__meta"} -->
<p class="jd-footer__meta">Studio éditorial · numérique et IA.<br>Paris · France</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":6} -->
<h6 class="wp-block-heading">Expertises</h6>
<!-- /wp:heading -->

<!-- wp:list {"className":"jd-footer__links"} -->
<ul class="wp-block-list jd-footer__links"><!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'diagnostic-strategie' ); ?>">Diagnostic &amp; stratégie</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'transformation-prototypage' ); ?>">Transformation &amp; prototypage</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'gouvernance-souverainete' ); ?>">Gouvernance &amp; souveraineté</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'formations' ); ?>">Formation</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":6} -->
<h6 class="wp-block-heading">Journalism.design</h6>
<!-- /wp:heading -->

<!-- wp:list {"className":"jd-footer__links"} -->
<ul class="wp-block-list jd-footer__links"><!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'cas-clients' ); ?>">Cas clients</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'a-propos' ); ?>">À propos</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'contact' ); ?>">Contact</a></li>
<!-- /wp:list-item --><?php if ( '#' !== jd_external_url( 'ressources' ) ) : // Lien masqué tant que l'URL n'est pas renseignée. ?>

<!-- wp:list-item -->
<li><a href="<?php echo jd_external_url( 'ressources' ); ?>">Ressources</a></li>
<!-- /wp:list-item --><?php endif; ?></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":6} -->
<h6 class="wp-block-heading">Écosystème</h6>
<!-- /wp:heading -->

<!-- wp:list {"className":"jd-footer__links"} -->
<ul class="wp-block-list jd-footer__links"><!-- wp:list-item -->
<li><a href="<?php echo jd_url( 'synth' ); ?>">SYNTH ↗</a></li>
<!-- /wp:list-item --><?php if ( '#' !== jd_external_url( 'inferences' ) ) : // Lien masqué tant que l'URL n'est pas renseignée. ?>

<!-- wp:list-item -->
<li><a href="<?php echo jd_external_url( 'inferences' ); ?>">Inférences</a></li>
<!-- /wp:list-item --><?php endif; ?></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"jd-footer__bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide jd-footer__bottom"><!-- wp:paragraph -->
<p><?php echo implode( ' · ', $jd_legal_links ); // phpcs:ignore WordPress.Security.EscapeOutput -- liens échappés ci-dessus ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
