<?php
/**
 * Title: En-tête du site
 * Slug: journalism-design/header
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: false
 *
 * @package journalism-design
 */
?>
<!-- wp:group {"align":"full","className":"jd-header","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignfull jd-header"><!-- wp:site-title {"level":0} /-->

<!-- wp:group {"className":"jd-header__right","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group jd-header__right"><!-- wp:navigation {"overlayMenu":"mobile","className":"jd-nav","layout":{"type":"flex","justifyContent":"right"}} -->
<!-- wp:navigation-submenu {"label":"Expertises","url":"<?php echo jd_url( '', 'expertises' ); ?>","kind":"custom","isTopLevelItem":true} -->
<!-- wp:navigation-link {"label":"Diagnostic & stratégie","url":"<?php echo jd_url( 'diagnostic-strategie' ); ?>","kind":"custom"} /-->

<!-- wp:navigation-link {"label":"Transformation & prototypage","url":"<?php echo jd_url( 'transformation-prototypage' ); ?>","kind":"custom"} /-->

<!-- wp:navigation-link {"label":"Gouvernance & souveraineté","url":"<?php echo jd_url( 'gouvernance-souverainete' ); ?>","kind":"custom"} /-->
<!-- /wp:navigation-submenu -->

<!-- wp:navigation-link {"label":"Formations","url":"<?php echo jd_url( 'formations' ); ?>","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Cas clients","url":"<?php echo jd_url( 'cas-clients' ); ?>","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"À propos","url":"<?php echo jd_url( 'a-propos' ); ?>","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"SYNTH ↗","url":"<?php echo jd_external_url( 'synth' ); ?>","kind":"custom","opensInNewTab":true,"className":"jd-nav-external","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Parler d’un projet →","url":"<?php echo jd_url( 'contact' ); ?>","kind":"custom","className":"jd-nav-cta-mobile","isTopLevelLink":true} /-->
<!-- /wp:navigation -->

<!-- wp:buttons {"className":"jd-header__cta"} -->
<div class="wp-block-buttons jd-header__cta"><!-- wp:button {"className":"is-style-accent"} -->
<div class="wp-block-button is-style-accent"><a class="wp-block-button__link wp-element-button" href="<?php echo jd_url( 'contact' ); ?>">Parler d’un projet →</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
