<?php
/**
 * Formulaire de contact « Premier échange ».
 *
 * Usage : bloc Code court contenant [jd_contact].
 * Les demandes sont envoyées par e-mail (wp_mail) à l'adresse d'administration
 * du site — filtre `jd_contact_recipient` pour en changer — et ne sont pas
 * stockées en base de données.
 *
 * @package journalism-design
 */

defined( 'ABSPATH' ) || exit;

/**
 * Définition des questions à choix. Modifiable via le filtre `jd_contact_fields`.
 *
 * @return array
 */
function jd_contact_fields() {
	return apply_filters(
		'jd_contact_fields',
		array(
			'sujet'          => array(
				'label'    => __( 'Sur quel sujet souhaitez-vous travailler ?', 'journalism-design' ),
				'type'     => 'checkbox',
				'options'  => array(
					__( 'Usages et stratégie IA', 'journalism-design' ),
					__( 'Workflows & automatisation', 'journalism-design' ),
					__( 'Souveraineté numérique', 'journalism-design' ),
					__( 'Confidentialité & données', 'journalism-design' ),
					__( 'Open source / alternatives', 'journalism-design' ),
					__( 'Gouvernance et doctrine IA', 'journalism-design' ),
					__( 'Formation', 'journalism-design' ),
					__( 'Autre', 'journalism-design' ),
				),
			),
			'avancement'     => array(
				'label'   => __( 'Où en êtes-vous ?', 'journalism-design' ),
				'type'    => 'radio',
				'options' => array(
					__( 'Nous commençons à explorer le sujet', 'journalism-design' ),
					__( 'Des usages existent déjà dans les équipes', 'journalism-design' ),
					__( 'Nous avons déjà déployé plusieurs solutions', 'journalism-design' ),
					__( 'Nous souhaitons revoir certains choix technologiques', 'journalism-design' ),
					__( 'Nous préparons une transformation plus importante', 'journalism-design' ),
				),
			),
			'effectif'       => array(
				'label'   => __( 'Combien de personnes sont concernées ?', 'journalism-design' ),
				'type'    => 'radio',
				'inline'  => true,
				'options' => array( '1–10', '10–30', '30–100', '100+' ),
			),
			'calendrier'     => array(
				'label'   => __( 'Quel est votre calendrier ?', 'journalism-design' ),
				'type'    => 'radio',
				'options' => array(
					__( 'Besoin immédiat', 'journalism-design' ),
					__( 'Dans les trois prochains mois', 'journalism-design' ),
					__( 'Dans les six prochains mois', 'journalism-design' ),
					__( 'Pas encore défini', 'journalism-design' ),
				),
			),
			'accompagnement' => array(
				'label'   => __( 'Quel type d’accompagnement imaginez-vous ?', 'journalism-design' ),
				'type'    => 'radio',
				'options' => array(
					__( 'Journée de diagnostic', 'journalism-design' ),
					__( 'Mission courte', 'journalism-design' ),
					__( 'Accompagnement de plusieurs semaines', 'journalism-design' ),
					__( 'Accompagnement stratégique dans la durée', 'journalism-design' ),
					__( 'À définir ensemble', 'journalism-design' ),
				),
			),
		)
	);
}

/**
 * Rendu du formulaire.
 *
 * @param array $atts Attributs : bouton (libellé du bouton d'envoi).
 * @return string
 */
function jd_contact_shortcode( $atts ) {
	$atts = shortcode_atts(
		array( 'bouton' => __( 'Envoyer la demande →', 'journalism-design' ) ),
		$atts,
		'jd_contact'
	);

	$status = isset( $_GET['envoi'] ) ? sanitize_key( wp_unslash( $_GET['envoi'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$id     = 'jd-contact';
	ob_start();
	?>
	<div class="jd-form-wrap" id="<?php echo esc_attr( $id ); ?>">
		<?php if ( 'ok' === $status ) : ?>
			<p class="jd-form-notice is-success" role="status"><?php esc_html_e( 'Merci, votre demande a bien été envoyée.', 'journalism-design' ); ?></p>
		<?php elseif ( 'erreur' === $status ) : ?>
			<p class="jd-form-notice is-error" role="alert"><?php esc_html_e( 'L’envoi a échoué. Vérifiez les champs obligatoires ou écrivez-nous directement par e-mail.', 'journalism-design' ); ?></p>
		<?php endif; ?>

		<form class="jd-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="jd_contact">
			<input type="hidden" name="jd_return" value="<?php echo esc_url( get_permalink() ); ?>">
			<?php wp_nonce_field( 'jd_contact', 'jd_contact_nonce' ); ?>
			<p class="jd-hp" aria-hidden="true"><label>Ne pas remplir <input type="text" name="jd_site" tabindex="-1" autocomplete="off"></label></p>

			<div class="jd-form__grid">
				<p class="jd-field"><label for="jd-nom"><?php esc_html_e( 'Nom', 'journalism-design' ); ?> <span aria-hidden="true">*</span></label>
					<input id="jd-nom" type="text" name="nom" required autocomplete="name"></p>
				<p class="jd-field"><label for="jd-organisation"><?php esc_html_e( 'Organisation', 'journalism-design' ); ?></label>
					<input id="jd-organisation" type="text" name="organisation" autocomplete="organization"></p>
				<p class="jd-field"><label for="jd-fonction"><?php esc_html_e( 'Fonction', 'journalism-design' ); ?></label>
					<input id="jd-fonction" type="text" name="fonction" autocomplete="organization-title"></p>
				<p class="jd-field"><label for="jd-email"><?php esc_html_e( 'Email professionnel', 'journalism-design' ); ?> <span aria-hidden="true">*</span></label>
					<input id="jd-email" type="email" name="email" required autocomplete="email"></p>
			</div>

			<?php foreach ( jd_contact_fields() as $key => $field ) : ?>
				<fieldset class="jd-choices<?php echo ! empty( $field['inline'] ) ? ' is-inline' : ''; ?>">
					<legend><?php echo esc_html( $field['label'] ); ?></legend>
					<?php foreach ( $field['options'] as $i => $option ) : ?>
						<?php $name = 'checkbox' === $field['type'] ? $key . '[]' : $key; ?>
						<label class="jd-choice">
							<input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $option ); ?>">
							<span><?php echo esc_html( $option ); ?></span>
						</label>
					<?php endforeach; ?>
				</fieldset>
			<?php endforeach; ?>

			<p class="jd-field"><label for="jd-situation"><?php esc_html_e( 'Décrivez-nous brièvement votre situation', 'journalism-design' ); ?></label>
				<textarea id="jd-situation" name="situation" rows="7"></textarea></p>

			<p class="jd-form__legal"><?php esc_html_e( 'Les informations transmises servent uniquement à répondre à votre demande. Elles sont envoyées par e-mail et ne sont pas conservées sur ce site.', 'journalism-design' ); ?></p>

			<div class="wp-block-buttons"><div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button"><?php echo esc_html( $atts['bouton'] ); ?></button></div></div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'jd_contact', 'jd_contact_shortcode' );

/**
 * Traitement de l'envoi.
 */
function jd_contact_handle() {
	$return = isset( $_POST['jd_return'] ) ? esc_url_raw( wp_unslash( $_POST['jd_return'] ) ) : home_url( '/' );
	$return = wp_validate_redirect( $return, home_url( '/' ) );

	$fail = function () use ( $return ) {
		wp_safe_redirect( add_query_arg( 'envoi', 'erreur', $return ) . '#jd-contact' );
		exit;
	};

	if ( ! isset( $_POST['jd_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['jd_contact_nonce'] ) ), 'jd_contact' ) ) {
		$fail();
	}
	// Pot de miel : un robot remplit ce champ invisible.
	if ( ! empty( $_POST['jd_site'] ) ) {
		wp_safe_redirect( add_query_arg( 'envoi', 'ok', $return ) . '#jd-contact' );
		exit;
	}

	$nom   = isset( $_POST['nom'] ) ? sanitize_text_field( wp_unslash( $_POST['nom'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( '' === $nom || ! is_email( $email ) ) {
		$fail();
	}

	$lines = array(
		__( 'Nom', 'journalism-design' )                 => $nom,
		__( 'Organisation', 'journalism-design' )        => isset( $_POST['organisation'] ) ? sanitize_text_field( wp_unslash( $_POST['organisation'] ) ) : '',
		__( 'Fonction', 'journalism-design' )            => isset( $_POST['fonction'] ) ? sanitize_text_field( wp_unslash( $_POST['fonction'] ) ) : '',
		__( 'Email professionnel', 'journalism-design' ) => $email,
	);

	foreach ( jd_contact_fields() as $key => $field ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$raw = array_map( 'sanitize_text_field', (array) $raw );
		// On ne garde que les valeurs proposées.
		$raw                      = array_values( array_intersect( $raw, $field['options'] ) );
		$lines[ $field['label'] ] = implode( ', ', $raw );
	}

	$situation = isset( $_POST['situation'] ) ? sanitize_textarea_field( wp_unslash( $_POST['situation'] ) ) : '';

	$body = '';
	foreach ( $lines as $label => $value ) {
		$body .= $label . ' : ' . ( '' !== $value ? $value : '—' ) . "\n";
	}
	$body .= "\n" . __( 'Situation', 'journalism-design' ) . " :\n" . ( $situation ? $situation : '—' ) . "\n";

	$to      = apply_filters( 'jd_contact_recipient', get_option( 'admin_email' ) );
	$subject = sprintf( '[%1$s] %2$s — %3$s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), __( 'Nouvelle demande', 'journalism-design' ), $nom );
	$headers = array( 'Reply-To: ' . $nom . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'envoi', $sent ? 'ok' : 'erreur', $return ) . '#jd-contact' );
	exit;
}
add_action( 'admin_post_jd_contact', 'jd_contact_handle' );
add_action( 'admin_post_nopriv_jd_contact', 'jd_contact_handle' );
