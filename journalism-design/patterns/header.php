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
<div class="wp-block-group alignfull jd-header"><!-- wp:image {"sizeSlug":"full","linkDestination":"custom","className":"jd-logo"} -->
<figure class="wp-block-image size-full jd-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-journalism-design.png' ) ); ?>" alt="Journalism.design — accueil"/></a></figure>
<!-- /wp:image -->

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

<!-- wp:navigation-link {"label":"SYNTH ↗","url":"<?php echo jd_url( 'synth' ); ?>","kind":"custom","className":"jd-nav-external","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Prendre contact →","url":"<?php echo jd_url( 'contact' ); ?>","kind":"custom","className":"jd-nav-cta-mobile","isTopLevelLink":true} /-->
<!-- /wp:navigation -->

<!-- wp:buttons {"className":"jd-header__cta"} -->
<div class="wp-block-buttons jd-header__cta"><!-- wp:button {"className":"is-style-accent"} -->
<div class="wp-block-button is-style-accent"><a class="wp-block-button__link wp-element-button" href="<?php echo jd_url( 'contact' ); ?>">Prendre contact →</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
