<?php
/**
 * Indice de dépendance numérique — auto-diagnostic en 12 questions.
 *
 * - Page autonome (/indice-dependance-numerique/) ou popin (<dialog>) ouverte
 *   depuis tout lien vers cette page.
 * - Questions, barème et textes définis ici : source unique pour le JS,
 *   l'e-mail de synthèse et le baromètre.
 * - Aucune donnée personnelle avant le résultat. E-mail facultatif, adresse
 *   professionnelle, non conservée. Contribution anonyme au baromètre sur
 *   demande explicite (ni adresse, ni IP, ni identifiant).
 *
 * @package journalism-design
 */

defined( 'ABSPATH' ) || exit;

define( 'JD_INDICE_SLUG', 'indice-dependance-numerique' );

/**
 * Dimensions mesurées.
 *
 * @return array
 */
function jd_indice_dimensions() {
	return array(
		'fournisseurs' => array(
			'label'    => 'Fournisseurs',
			'question' => 'Sommes-nous prisonniers de certains outils ?',
			'weak'     => 'Plusieurs activités critiques reposent sur un nombre limité de fournisseurs, sans alternative testée.',
			'strong'   => 'Le choix et le nombre de vos fournisseurs semblent maîtrisés.',
			'action'   => 'Listez les cinq outils sans lesquels l’activité s’arrêterait et, pour chacun, une alternative envisageable.',
		),
		'donnees'      => array(
			'label'    => 'Données',
			'question' => 'Savons-nous récupérer et déplacer ce qui nous appartient ?',
			'weak'     => 'Vos données les plus importantes seraient difficiles à récupérer dans un format réutilisable ailleurs.',
			'strong'   => 'Vos données semblent relativement récupérables.',
			'action'   => 'Testez l’export complet de vos données depuis l’outil le plus critique, et vérifiez qu’elles s’ouvrent ailleurs.',
		),
		'competences'  => array(
			'label'    => 'Compétences',
			'question' => 'Le savoir-faire reste-t-il dans l’organisation ?',
			'weak'     => 'Une partie du savoir-faire repose sur des outils ou sur quelques personnes, sans documentation partagée.',
			'strong'   => 'Le savoir-faire reste documenté et partagé dans l’organisation.',
			'action'   => 'Mettez par écrit les deux ou trois processus qui ne reposent que sur une personne ou sur un outil.',
		),
		'ia'           => array(
			'label'    => 'IA & automatisation',
			'question' => 'Savons-nous réellement ce qui est automatisé et comment ?',
			'weak'     => 'Les usages d’IA progressent plus vite que leur gouvernance.',
			'strong'   => 'Vos usages d’IA sont encadrés et vous savez ce qui est transmis aux modèles.',
			'action'   => 'Recensez les outils d’IA réellement utilisés dans les équipes et fixez une règle simple sur les données qu’on peut y saisir.',
		),
		'resilience'   => array(
			'label'    => 'Résilience',
			'question' => 'Avons-nous un plan B réaliste ?',
			'weak'     => 'Sans solution de repli éprouvée, une panne ou un changement de conditions d’un fournisseur toucherait directement l’activité.',
			'strong'   => 'Votre organisation dispose de solutions de repli crédibles.',
			'action'   => 'Imaginez une journée sans votre outil principal : qu’est-ce qui s’arrête, et comment continuer à travailler ?',
		),
	);
}

/**
 * Questions. Chaque option : [libellé, points de dépendance (0 à 6), réaction].
 * « dim » null = question non notée.
 *
 * @return array
 */
function jd_indice_questions() {
	return array(
		array(
			'id'      => 'q1',
			'dim'     => 'resilience',
			'type'    => 'single',
			'text'    => 'Demain matin, votre principal fournisseur numérique devient indisponible pendant 72 heures. Que se passe-t-il ?',
			'options' => array(
				array( 'Nous continuons presque normalement', 0, 'Bon signal : votre activité ne s’arrête pas avec un seul fournisseur.' ),
				array( 'Plusieurs activités sont ralenties', 2, 'L’indisponibilité ralentit le travail sans le bloquer. Reste à savoir quelles activités, et pour combien de temps.' ),
				array( 'Une activité importante s’arrête', 4, 'Une activité importante dépend d’un seul fournisseur. C’est précisément ce qu’un plan de continuité doit couvrir.' ),
				array( 'Une grande partie de l’organisation est paralysée', 6, 'Une grande partie du fonctionnement de l’organisation est concentrée chez un seul fournisseur.' ),
				array( 'Je n’en ai aucune idée', 5, 'Ne pas savoir est un signal en soi : la dépendance n’est pas cartographiée.' ),
			),
		),
		array(
			'id'      => 'q1b',
			'dim'     => null,
			'type'    => 'multi',
			'text'    => 'Quels fournisseurs seraient concernés ?',
			'help'    => 'Plusieurs réponses possibles. Cette question n’est pas notée.',
			'options' => array(
				array( 'Microsoft 365', 0, '' ),
				array( 'Google Workspace', 0, '' ),
				array( 'Adobe', 0, '' ),
				array( 'AWS', 0, '' ),
				array( 'OpenAI', 0, '' ),
				array( 'Anthropic', 0, '' ),
				array( 'Salesforce', 0, '' ),
				array( 'Notion', 0, '' ),
				array( 'Slack', 0, '' ),
				array( 'Autre', 0, '' ),
			),
		),
		array(
			'id'      => 'q2',
			'dim'     => 'fournisseurs',
			'type'    => 'single',
			'text'    => 'Combien de fournisseurs numériques sont indispensables au fonctionnement quotidien de votre activité ?',
			'options' => array(
				array( '0 à 2', 0, 'Un périmètre resserré, plus simple à gouverner.' ),
				array( '3 à 5', 2, 'Un nombre courant. L’enjeu est de savoir lesquels sont réellement critiques.' ),
				array( '6 à 10', 4, 'Chaque fournisseur ajoute un contrat, des conditions et un risque de changement.' ),
				array( 'Plus de 10', 6, 'Au-delà de dix, une cartographie des dépendances devient indispensable.' ),
				array( 'Aucune idée', 5, 'L’absence de visibilité constitue elle-même un risque.' ),
			),
		),
		array(
			'id'      => 'q3',
			'dim'     => 'donnees',
			'type'    => 'single',
			'text'    => 'Pour vos données les plus importantes, pouvez-vous les exporter dans un format réellement réutilisable ailleurs ?',
			'options' => array(
				array( 'Oui, facilement', 0, 'Vos données vous appartiennent aussi en pratique.' ),
				array( 'Partiellement', 2, 'Une partie de vos données resterait chez le fournisseur en cas de départ.' ),
				array( 'Techniquement oui, mais c’est compliqué', 4, 'Les données sont théoriquement exportables. En pratique, la sortie coûterait cher.' ),
				array( 'Non', 6, 'Vos données les plus importantes sont prisonnières d’un outil.' ),
				array( 'Je ne sais pas', 5, 'Personne n’a vérifié si les données pouvaient sortir. C’est pourtant un test simple à mener.' ),
			),
		),
		array(
			'id'      => 'q4',
			'dim'     => 'fournisseurs',
			'type'    => 'single',
			'text'    => 'Avez-vous déjà essayé de quitter un outil important ?',
			'options' => array(
				array( 'Oui, facilement', 0, 'Bon signal : vous avez déjà testé une solution alternative. La réversibilité n’est donc pas seulement théorique.' ),
				array( 'Oui, difficilement', 3, 'La sortie a été possible mais coûteuse : une information précieuse pour vos prochains choix.' ),
				array( 'Nous avons renoncé', 6, 'Un départ abandonné signale une dépendance forte, souvent liée aux données ou au contrat.' ),
				array( 'Jamais', 4, 'La réversibilité n’a jamais été mise à l’épreuve.' ),
				array( 'Nous ne saurions pas comment faire', 5, 'Sans procédure de sortie, chaque outil adopté devient plus difficile à quitter.' ),
			),
		),
		array(
			'id'      => 'q5',
			'dim'     => 'competences',
			'type'    => 'single',
			'text'    => 'Combien de processus essentiels reposent sur des outils ou des automatisations maîtrisés par une seule personne ?',
			'help'    => 'Tableaux partagés, automatisations Zapier ou Make, scripts, prompts, GPT personnalisés…',
			'options' => array(
				array( 'Aucun', 0, 'Le savoir-faire circule dans l’organisation.' ),
				array( 'Un ou deux', 2, 'Quelques points de fragilité : une absence ou un départ suffit à les révéler.' ),
				array( 'Plusieurs', 4, 'Une partie de l’infrastructure de l’organisation est invisible, et une seule personne la maîtrise.' ),
				array( 'La plupart', 6, 'L’organisation fonctionne sur une infrastructure de l’ombre que personne d’autre ne maîtrise.' ),
				array( 'Je ne sais pas', 5, 'Ces outils restent souvent invisibles jusqu’au jour où ils cessent de fonctionner.' ),
			),
		),
		array(
			'id'      => 'q6',
			'dim'     => 'ia',
			'type'    => 'single',
			'text'    => 'Vos équipes utilisent-elles ChatGPT, Claude, Gemini ou d’autres IA avec des informations professionnelles ?',
			'options' => array(
				array( 'Oui, avec un usage encadré', 0, 'Un usage encadré : des outils validés et des règles sur les données autorisées.' ),
				array( 'Oui, autorisé pour certaines données', 1, 'Une frontière existe entre ce qui peut être confié aux IA et le reste.' ),
				array( 'Oui, de manière informelle', 4, 'Point d’attention : l’IA est utilisée sans cadre, et les données circulent selon les habitudes de chacun.' ),
				array( 'Probablement, mais nous ignorons comment', 6, 'Point d’attention : des usages existent, mais personne ne sait lesquels.' ),
				array( 'Non, c’est interdit', 3, 'Une interdiction n’est pas une gouvernance : si le besoin existe, les usages se déplacent vers des comptes personnels.' ),
			),
		),
		array(
			'id'      => 'q7',
			'dim'     => 'ia',
			'type'    => 'single',
			'text'    => 'Savez-vous quelles données sont transmises aux modèles d’IA utilisés par vos équipes ?',
			'options' => array(
				array( 'Précisément', 0, 'Vous savez ce qui sort de l’organisation.' ),
				array( 'Globalement', 2, 'Une vision d’ensemble, à préciser outil par outil.' ),
				array( 'Seulement pour certains outils', 4, 'Les outils officiels sont connus, les autres échappent au suivi.' ),
				array( 'Non', 6, 'Point d’attention : vous utilisez des outils d’IA sans savoir précisément quelles données leur sont transmises.' ),
			),
		),
		array(
			'id'      => 'q8',
			'dim'     => 'fournisseurs',
			'type'    => 'single',
			'text'    => 'Quand un nouveau logiciel est adopté, qui décide ?',
			'options' => array(
				array( 'Un processus formalisé', 0, 'Les choix sont discutés et documentés.' ),
				array( 'Un responsable identifié', 1, 'Une personne arbitre. Reste à savoir sur quels critères.' ),
				array( 'Chaque équipe décide', 3, 'Chaque équipe choisit : les outils se multiplient, les doublons aussi.' ),
				array( 'Chacun utilise ce qu’il veut', 6, 'Sans décision collective, la dépendance s’installe outil par outil.' ),
				array( 'Ça dépend', 4, 'Des règles variables produisent des dépendances difficiles à suivre.' ),
			),
		),
		array(
			'id'      => 'q9',
			'dim'     => 'ia',
			'type'    => 'single',
			'text'    => 'Si votre principal outil d’IA augmentait ses prix de 300 % demain…',
			'options' => array(
				array( 'Une alternative est déjà testée', 0, 'Bon signal : vous pourriez négocier, ou partir.' ),
				array( 'Une migration serait possible', 2, 'Possible, mais pas encore éprouvée.' ),
				array( 'Ce serait compliqué', 4, 'La dépendance devient ici une question économique.' ),
				array( 'Notre activité serait menacée', 6, 'Une hausse de prix suffirait à mettre une activité en difficulté.' ),
				array( 'Nous ne savons pas', 5, 'Les conditions tarifaires des outils d’IA changent vite. Ne pas savoir expose à les subir.' ),
				array( 'Nous n’utilisons pas d’outil d’IA payant', 0, 'Pas d’exposition tarifaire directe pour l’instant.' ),
			),
		),
		array(
			'id'      => 'q10',
			'dim'     => 'competences',
			'type'    => 'single',
			'text'    => 'Les savoir-faire automatisés restent-ils documentés et compris par les équipes ?',
			'options' => array(
				array( 'Oui', 0, 'L’automatisation n’a pas effacé la compétence.' ),
				array( 'Partiellement', 2, 'Une partie du savoir-faire est passée dans les outils.' ),
				array( 'Rarement', 4, 'Ce que font les automatisations n’est plus vraiment compris.' ),
				array( 'Non', 6, 'Le savoir-faire a migré dans les outils : en cas de panne, il faudrait le reconstruire.' ),
				array( 'Nous n’automatisons rien', 0, 'Aucun savoir-faire enfoui dans des automatisations pour l’instant.' ),
			),
		),
		array(
			'id'        => 'q11',
			'dim'       => 'donnees',
			'type'      => 'criteria',
			'text'      => 'Lorsque vous choisissez un outil, évaluez-vous systématiquement…',
			'help'      => 'Plusieurs réponses possibles.',
			'options'   => array(
				array( 'Le coût', 0, '' ),
				array( 'La confidentialité', 0, '' ),
				array( 'La localisation des données', 0, '' ),
				array( 'L’interopérabilité', 0, '' ),
				array( 'La possibilité d’export', 0, '' ),
				array( 'Les alternatives open source', 0, '' ),
				array( 'L’empreinte environnementale', 0, '' ),
				array( 'Les conditions contractuelles', 0, '' ),
				array( 'Rien de tout cela', 0, '' ),
			),
			// Barème : 6 points moins un point par critère de maîtrise coché
			// (le coût n'en est pas un), plancher à 0. « Rien de tout cela » = 6.
			'none'      => 8,
			'neutral'   => array( 0 ),
			'feedbacks' => array(
				'high' => 'Des critères de choix solides, au-delà des fonctionnalités et du prix.',
				'mid'  => 'Certains critères de réversibilité manquent encore à vos choix.',
				'low'  => 'Les outils sont choisis surtout sur leurs fonctionnalités et leur prix.',
			),
		),
		array(
			'id'      => 'q12',
			'dim'     => 'resilience',
			'type'    => 'single',
			'text'    => 'Si vous deviez remplacer demain vos trois principaux outils numériques, sauriez-vous par où commencer ?',
			'options' => array(
				array( 'Oui, nous avons déjà un plan', 0, 'Votre organisation a anticipé.' ),
				array( 'Probablement', 2, 'Une intuition, pas encore un plan.' ),
				array( 'Difficilement', 4, 'Le remplacement deviendrait un chantier subi.' ),
				array( 'Absolument pas', 6, 'Aucune solution de repli n’est envisagée.' ),
			),
		),
	);
}

/**
 * Prologue personnel (non noté, non enregistré).
 *
 * @return array
 */
function jd_indice_prologue() {
	return array(
		array(
			'id'      => 'p1',
			'text'    => 'Combien de services utilisez-vous en vous connectant avec votre compte Google, Apple ou Microsoft ?',
			'options' => array( 'Aucun', '1 à 5', '6 à 20', 'Plus de 20', 'Je ne sais pas' ),
		),
		array(
			'id'      => 'p2',
			'text'    => 'Sauriez-vous récupérer vos photos, documents et contacts sans passer par votre fournisseur principal ?',
			'options' => array( 'Oui', 'Partiellement', 'Non', 'Je n’ai jamais essayé' ),
		),
		array(
			'id'      => 'p3',
			'text'    => 'Si votre compte principal était bloqué demain, combien de vos services personnels deviendraient difficiles d’accès ?',
			'options' => array( 'Aucun', 'Quelques-uns', 'La plupart', 'Je préfère ne pas y penser' ),
		),
	);
}

/**
 * Textes de l'interface.
 *
 * @return array
 */
function jd_indice_texts() {
	return array(
		'kicker'      => 'Indice de dépendance numérique',
		'title'       => 'Votre organisation pourrait-elle encore travailler si ses principaux fournisseurs changeaient brutalement leurs règles ?',
		'lead'        => '12 questions. 5 minutes. Découvrez de quoi votre organisation dépend réellement : outils, données, IA, compétences et fournisseurs.',
		'privacy'     => 'Le résultat s’affiche immédiatement, sans adresse e-mail ni donnée personnelle.',
		'start'       => 'Commencer →',
		'skipIntro'   => 'Passer directement à mon organisation',
		'prologueK'   => 'Échauffement · non noté',
		'transition'  => 'Nos vies personnelles dépendent déjà de quelques infrastructures. Dans une organisation, les mêmes dépendances ont des conséquences économiques, juridiques et opérationnelles beaucoup plus importantes.',
		'transitionB' => 'Regardons maintenant votre organisation →',
		'next'        => 'Question suivante →',
		'result'      => 'Voir mon résultat →',
		'continue'    => 'Continuer →',
		'back'        => '← Question précédente',
		'skip'        => 'Passer',
		'points'      => 'points de dépendance',
		'scoreTitle'  => 'Votre indice de dépendance numérique',
		'weak'        => 'Votre principale fragilité',
		'strong'      => 'Votre point fort',
		'investigate' => 'Le point à investiguer',
		'fragilities' => 'Vos principales fragilités',
		'actionK'     => 'À faire dès maintenant',
		'noStrong'    => 'Aucune dimension ne se détache nettement : la dépendance est répartie sur l’ensemble de l’organisation.',
		'unknown'     => 'Plusieurs réponses « je ne sais pas » : avant de réduire les dépendances, il faut les rendre visibles.',
		'providers'   => 'Fournisseurs cités',
		'ctaTitle'    => 'Transformer cette photographie en plan d’action ?',
		'ctaText'     => 'Ce test repère des signaux de dépendance à partir de vos déclarations. Une journée de diagnostic examine concrètement vos outils, workflows, données et usages d’IA.',
		'ctaList'     => array(
			'les dépendances critiques',
			'ce qu’il faut conserver',
			'ce qu’il faut sécuriser',
			'ce qui mérite d’être remplacé ou prototypé',
			'les 3 à 5 actions prioritaires',
		),
		'ctaListK'    => 'En une journée, nous identifions',
		'ctaPrice'    => 'Journée de diagnostic stratégique — à partir de 1 500 € HT',
		'ctaBook'     => 'Réserver une journée de diagnostic →',
		'ctaSample'   => 'Voir le contenu du livrable →',
		'mailTitle'   => 'Recevoir mon diagnostic synthétique par e-mail',
		'mailLabel'   => 'Adresse e-mail professionnelle',
		'mailContact' => 'Je souhaite être recontacté·e par journalism.design au sujet de ce résultat.',
		'mailNews'    => 'Je souhaite recevoir les publications et ressources de journalism.design.',
		'mailNote'    => 'Votre adresse sert à l’envoi de ce diagnostic et n’est pas conservée sur ce site. Elle n’est transmise à journalism.design que si vous cochez l’une des deux cases.',
		'mailSend'    => 'Recevoir le diagnostic →',
		'mailOk'      => 'C’est envoyé. Pensez à vérifier vos courriers indésirables.',
		'mailErr'     => 'L’envoi a échoué. Vérifiez l’adresse ou réessayez plus tard.',
		'mailPro'     => 'Merci d’utiliser une adresse professionnelle.',
		'shareTitle'  => 'Contribuer au baromètre de la dépendance numérique',
		'shareText'   => 'Vos réponses sont enregistrées sans adresse, sans IP et sans identifiant, pour produire des statistiques agrégées.',
		'shareBtn'    => 'Partager anonymement mes réponses',
		'shareOk'     => 'Merci, vos réponses anonymes ont été enregistrées.',
		'methodTitle' => 'Comment l’indice est calculé',
		'method'      => array(
			'Chaque réponse reçoit de 0 à 6 points de dépendance. Les points sont additionnés par dimension, puis ramenés sur 100.',
			'L’indice global est la moyenne des cinq dimensions, qui pèsent chacune 20 %.',
			'« Je ne sais pas » compte presque autant que la réponse la plus défavorable : le manque de visibilité est un risque en soi.',
			'Interdire l’IA n’obtient pas la note maximale : une interdiction sans alternative déplace les usages vers des outils personnels.',
			'Pour les critères de choix d’un outil, chaque critère de maîtrise coché retire un point (le coût seul n’en retire pas).',
			'Le test repère des signaux à partir de vos déclarations. Il ne remplace pas l’examen des outils, des contrats et des workflows.',
		),
		'restart'     => 'Recommencer le test',
		'close'       => 'Fermer',
		'noscript'    => 'Ce test fonctionne avec JavaScript. Vous pouvez aussi nous écrire directement.',
		'levels'      => array(
			array( 25, 'Dépendance maîtrisée' ),
			array( 45, 'Dépendance modérée' ),
			array( 65, 'Dépendance significative' ),
			array( 101, 'Dépendance critique' ),
		),
	);
}

/**
 * Calcule l'indice à partir des réponses (même règle que le JavaScript).
 *
 * @param array $answers id de question => index d'option (ou liste d'index).
 * @return array|null
 */
function jd_indice_compute( $answers ) {
	$dims  = jd_indice_dimensions();
	$score = array_fill_keys( array_keys( $dims ), 0 );
	$max   = array_fill_keys( array_keys( $dims ), 0 );

	foreach ( jd_indice_questions() as $q ) {
		if ( ! $q['dim'] ) {
			continue;
		}
		if ( ! isset( $answers[ $q['id'] ] ) ) {
			return null; // Questionnaire incomplet.
		}
		$a = $answers[ $q['id'] ];
		$max[ $q['dim'] ] += 6;
		if ( 'criteria' === $q['type'] ) {
			$a = array_map( 'intval', (array) $a );
			if ( in_array( $q['none'], $a, true ) ) {
				$pts = 6;
			} else {
				$count = count( array_diff( array_unique( $a ), $q['neutral'], array( $q['none'] ) ) );
				$pts   = max( 0, 6 - $count );
			}
		} else {
			$a = (int) $a;
			if ( ! isset( $q['options'][ $a ] ) ) {
				return null;
			}
			$pts = (int) $q['options'][ $a ][1];
		}
		$score[ $q['dim'] ] += $pts;
	}

	$result = array();
	foreach ( $score as $dim => $pts ) {
		$result[ $dim ] = $max[ $dim ] ? (int) round( 100 * $pts / $max[ $dim ] ) : 0;
	}
	$total = (int) round( array_sum( $result ) / count( $result ) );
	$label = '';
	foreach ( jd_indice_texts()['levels'] as $level ) {
		if ( $total < $level[0] ) {
			$label = $level[1];
			break;
		}
	}
	return array(
		'total' => $total,
		'label' => $label,
		'dims'  => $result,
	);
}

/**
 * Nettoie les réponses envoyées par le navigateur.
 *
 * @param mixed $raw Réponses brutes.
 * @return array
 */
function jd_indice_sanitize_answers( $raw ) {
	$clean = array();
	if ( ! is_array( $raw ) ) {
		return $clean;
	}
	foreach ( jd_indice_questions() as $q ) {
		if ( ! isset( $raw[ $q['id'] ] ) ) {
			continue;
		}
		$v = $raw[ $q['id'] ];
		if ( is_array( $v ) ) {
			$v = array_values( array_filter( array_map( 'absint', $v ), function ( $i ) use ( $q ) {
				return isset( $q['options'][ $i ] );
			} ) );
		} else {
			$v = absint( $v );
			if ( ! isset( $q['options'][ $v ] ) ) {
				continue;
			}
		}
		$clean[ $q['id'] ] = $v;
	}
	return $clean;
}

/* ---------------------------------------------------------------------------
 * Affichage : code court, popin, ressources
 * ------------------------------------------------------------------------- */

/**
 * URL de la page autonome.
 *
 * @return string
 */
function jd_indice_url() {
	$page = get_page_by_path( JD_INDICE_SLUG );
	return $page ? get_permalink( $page ) : home_url( '/' . JD_INDICE_SLUG . '/' );
}

/**
 * Conteneur de l'application.
 *
 * @param int $heading Niveau du titre (1 sur la page autonome, 2 en popin).
 * @return string
 */
function jd_indice_container( $heading = 2 ) {
	$t = jd_indice_texts();
	return '<div class="jd-indice" data-heading="' . (int) $heading . '">'
		. '<noscript><p class="jd-indice__noscript">' . esc_html( $t['noscript'] ) . ' <a href="' . esc_url( jd_url( 'contact' ) ) . '">Contact →</a></p></noscript>'
		. '</div>';
}

function jd_indice_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'titre' => '1' ), $atts, 'jd_indice' );
	jd_indice_enqueue();
	return jd_indice_container( '2' === $atts['titre'] ? 2 : 1 );
}
add_shortcode( 'jd_indice', 'jd_indice_shortcode' );

/**
 * Charge le script et ses données.
 */
function jd_indice_enqueue() {
	if ( wp_script_is( 'journalism-design-indice', 'enqueued' ) ) {
		return;
	}
	wp_enqueue_script(
		'journalism-design-indice',
		JD_URI . '/assets/js/indice.js',
		array(),
		JD_VERSION . '.' . filemtime( JD_DIR . '/assets/js/indice.js' ),
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);
	$questions = array_map(
		function ( $q ) {
			// Les points restent dans les données : le barème est public et expliqué.
			return $q;
		},
		jd_indice_questions()
	);
	wp_localize_script(
		'journalism-design-indice',
		'JD_INDICE',
		array(
			'dims'      => jd_indice_dimensions(),
			'questions' => $questions,
			'prologue'  => jd_indice_prologue(),
			'texts'     => jd_indice_texts(),
			'urls'      => array(
				'page'     => jd_indice_url(),
				'contact'  => jd_url( 'contact' ),
				'booking'  => html_entity_decode( jd_booking_url() ),
				'livrable' => jd_url( 'diagnostic-strategie', 'livrable' ),
				'rest'     => esc_url_raw( rest_url( 'jd/v1/indice/' ) ),
			),
		)
	);
}

/**
 * Popin : présente sur toutes les pages sauf la page autonome.
 * Tout lien vers la page autonome l'ouvre (le lien reste fonctionnel sans JS).
 */
function jd_indice_dialog() {
	if ( is_admin() || is_page( JD_INDICE_SLUG ) ) {
		return;
	}
	jd_indice_enqueue();
	$t = jd_indice_texts();
	echo '<dialog class="jd-indice-dialog" id="jd-indice-dialog" aria-label="' . esc_attr( $t['kicker'] ) . '">'
		. '<div class="jd-indice-dialog__bar"><a class="jd-indice-dialog__page" href="' . esc_url( jd_indice_url() ) . '">Ouvrir en pleine page ↗</a>'
		. '<button type="button" class="jd-indice-dialog__close">' . esc_html( $t['close'] ) . '</button></div>'
		. jd_indice_container( 2 ) // phpcs:ignore WordPress.Security.EscapeOutput
		. '</dialog>';
}
add_action( 'wp_footer', 'jd_indice_dialog' );

/* ---------------------------------------------------------------------------
 * REST : e-mail de synthèse et baromètre anonyme
 * ------------------------------------------------------------------------- */

function jd_indice_rest_routes() {
	register_rest_route(
		'jd/v1',
		'/indice/report',
		array(
			'methods'             => 'POST',
			'callback'            => 'jd_indice_rest_report',
			'permission_callback' => '__return_true',
		)
	);
	register_rest_route(
		'jd/v1',
		'/indice/share',
		array(
			'methods'             => 'POST',
			'callback'            => 'jd_indice_rest_share',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'jd_indice_rest_routes' );

/**
 * Limite de fréquence par visiteur. L'adresse IP n'est jamais enregistrée :
 * seule une empreinte salée, valable une heure, sert de compteur temporaire.
 *
 * @param string $action Nom de l'action.
 * @param int    $limit  Nombre d'appels par heure.
 * @return bool
 */
function jd_indice_rate_ok( $action, $limit ) {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'jd_rl_' . $action . '_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) ), 0, 20 );
	$n   = (int) get_transient( $key );
	if ( $n >= $limit ) {
		return false;
	}
	set_transient( $key, $n + 1, HOUR_IN_SECONDS );
	return true;
}

/**
 * Domaines de messagerie grand public refusés (adresse professionnelle demandée).
 *
 * @return string[]
 */
function jd_indice_free_domains() {
	return apply_filters(
		'jd_indice_free_domains',
		array( 'gmail.com', 'googlemail.com', 'yahoo.com', 'yahoo.fr', 'hotmail.com', 'hotmail.fr', 'outlook.com', 'outlook.fr', 'live.com', 'live.fr', 'msn.com', 'icloud.com', 'me.com', 'mac.com', 'aol.com', 'proton.me', 'protonmail.com', 'pm.me', 'gmx.com', 'gmx.fr', 'orange.fr', 'wanadoo.fr', 'free.fr', 'sfr.fr', 'neuf.fr', 'laposte.net', 'bbox.fr', 'numericable.fr', 'yandex.com', 'mail.com', 'tutanota.com', 'tuta.io' )
	);
}

/**
 * Texte de synthèse (e-mail).
 *
 * @param array $res Résultat de jd_indice_compute().
 * @return string
 */
function jd_indice_summary( $res ) {
	$dims = jd_indice_dimensions();
	$t    = jd_indice_texts();
	arsort( $res['dims'] );
	$strong = array_key_last( $res['dims'] );

	$out  = $t['scoreTitle'] . ' : ' . $res['total'] . ' / 100 — ' . $res['label'] . "\n\n";
	foreach ( $dims as $key => $dim ) {
		$out .= '- ' . $dim['label'] . ' : ' . $res['dims'][ $key ] . " / 100\n";
	}
	$floor   = $t['levels'][0][0];
	$fragile = array_slice( array_keys( array_filter( $res['dims'], function ( $v ) use ( $floor ) { return $v >= $floor; } ) ), 0, 3 );
	$out    .= $fragile ? "\n" . $t['fragilities'] . " :\n" : '';
	foreach ( $fragile as $i => $key ) {
		$out .= ( $i + 1 ) . '. ' . $dims[ $key ]['label'] . ' (' . $res['dims'][ $key ] . ' / 100) — ' . $dims[ $key ]['weak'] . "\n   " . $t['actionK'] . ' : ' . $dims[ $key ]['action'] . "\n";
	}
	$out .= "\n" . $t['strong'] . ' : ' . ( $res['dims'][ $strong ] <= 40 ? $dims[ $strong ]['strong'] : $t['noStrong'] ) . "\n\n";
	$out .= $t['ctaTitle'] . "\n" . $t['ctaText'] . "\n" . $t['ctaPrice'] . "\n" . html_entity_decode( jd_booking_url() ) . "\n\n";
	$out .= $t['methodTitle'] . " :\n- " . implode( "\n- ", $t['method'] ) . "\n\n";
	$out .= 'Refaire le test : ' . jd_indice_url() . "\n";
	return $out;
}

function jd_indice_rest_report( WP_REST_Request $req ) {
	if ( '' !== (string) $req->get_param( 'site' ) ) { // Pot de miel.
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}
	if ( ! jd_indice_rate_ok( 'report', 5 ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'rate' ), 429 );
	}
	$email = sanitize_email( (string) $req->get_param( 'email' ) );
	if ( ! is_email( $email ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'email' ), 400 );
	}
	$domain = strtolower( substr( strrchr( $email, '@' ), 1 ) );
	if ( in_array( $domain, jd_indice_free_domains(), true ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'pro' ), 400 );
	}
	$res = jd_indice_compute( jd_indice_sanitize_answers( $req->get_param( 'answers' ) ) );
	if ( ! $res ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'answers' ), 400 );
	}

	$site    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$summary = jd_indice_summary( $res );
	$sent    = wp_mail( $email, '[' . $site . '] Votre indice de dépendance numérique : ' . $res['total'] . ' / 100', $summary );

	$contact = (bool) $req->get_param( 'contact' );
	$news    = (bool) $req->get_param( 'news' );
	if ( $sent && ( $contact || $news ) ) {
		$admin = apply_filters( 'jd_contact_recipient', get_option( 'admin_email' ) );
		$body  = 'Adresse : ' . $email . "\n"
			. 'Souhaite être recontacté·e : ' . ( $contact ? 'oui' : 'non' ) . "\n"
			. 'Souhaite recevoir les publications : ' . ( $news ? 'oui' : 'non' ) . "\n\n"
			. $summary;
		wp_mail( $admin, '[' . $site . '] Indice de dépendance — ' . $email, $body, array( 'Reply-To: ' . $email ) );
	}
	return new WP_REST_Response( array( 'ok' => (bool) $sent ), $sent ? 200 : 500 );
}

/* Baromètre anonyme */

function jd_indice_table() {
	global $wpdb;
	return $wpdb->prefix . 'jd_indice';
}

function jd_indice_install_table() {
	if ( '1' === get_option( 'jd_indice_db' ) ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	dbDelta(
		'CREATE TABLE ' . jd_indice_table() . " (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			created date NOT NULL,
			total tinyint(3) unsigned NOT NULL,
			dims text NOT NULL,
			answers text NOT NULL,
			PRIMARY KEY  (id)
		) $charset;"
	);
	update_option( 'jd_indice_db', '1', false );
}

function jd_indice_rest_share( WP_REST_Request $req ) {
	if ( '' !== (string) $req->get_param( 'site' ) ) {
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}
	if ( ! jd_indice_rate_ok( 'share', 10 ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'rate' ), 429 );
	}
	$answers = jd_indice_sanitize_answers( $req->get_param( 'answers' ) );
	$res     = jd_indice_compute( $answers );
	if ( ! $res ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'answers' ), 400 );
	}
	jd_indice_install_table();
	global $wpdb;
	// Seule la date du jour est conservée, sans heure, pour limiter toute ré-identification.
	$ok = $wpdb->insert(
		jd_indice_table(),
		array(
			'created' => current_time( 'Y-m-d' ),
			'total'   => $res['total'],
			'dims'    => wp_json_encode( $res['dims'] ),
			'answers' => wp_json_encode( $answers ),
		),
		array( '%s', '%d', '%s', '%s' )
	);
	return new WP_REST_Response( array( 'ok' => (bool) $ok ), $ok ? 200 : 500 );
}

/* ---------------------------------------------------------------------------
 * Administration : Outils › Indice de dépendance
 * ------------------------------------------------------------------------- */

function jd_indice_admin_menu() {
	add_management_page( 'Indice de dépendance numérique', 'Indice de dépendance', 'manage_options', 'jd-indice', 'jd_indice_admin_page' );
}
add_action( 'admin_menu', 'jd_indice_admin_menu' );

/**
 * Export CSV des réponses anonymes.
 */
function jd_indice_export() {
	if ( ! isset( $_GET['page'], $_GET['jd_export'] ) || 'jd-indice' !== $_GET['page'] || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	check_admin_referer( 'jd_indice_export' );
	jd_indice_install_table();
	global $wpdb;
	$rows      = $wpdb->get_results( 'SELECT created, total, dims, answers FROM ' . jd_indice_table() . ' ORDER BY id ASC', ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL
	$questions = jd_indice_questions();
	$dims      = array_keys( jd_indice_dimensions() );
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=indice-dependance-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" );
	fputcsv( $out, array_merge( array( 'date', 'indice' ), $dims, wp_list_pluck( $questions, 'id' ) ) );
	foreach ( $rows as $row ) {
		$d    = json_decode( $row['dims'], true );
		$a    = json_decode( $row['answers'], true );
		$line = array( $row['created'], $row['total'] );
		foreach ( $dims as $k ) {
			$line[] = isset( $d[ $k ] ) ? $d[ $k ] : '';
		}
		foreach ( $questions as $q ) {
			$v = isset( $a[ $q['id'] ] ) ? $a[ $q['id'] ] : null;
			if ( null === $v ) {
				$line[] = '';
			} elseif ( is_array( $v ) ) {
				$line[] = implode( ' | ', array_map( function ( $i ) use ( $q ) { return $q['options'][ $i ][0]; }, $v ) );
			} else {
				$line[] = $q['options'][ $v ][0];
			}
		}
		fputcsv( $out, $line );
	}
	fclose( $out );
	exit;
}
add_action( 'admin_init', 'jd_indice_export' );

function jd_indice_admin_page() {
	jd_indice_install_table();
	global $wpdb;
	$rows = $wpdb->get_results( 'SELECT total, dims, answers FROM ' . jd_indice_table(), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL
	$n    = count( $rows );
	echo '<div class="wrap"><h1>Indice de dépendance numérique</h1>';
	echo '<p>Réponses partagées anonymement par les visiteurs (sans adresse, sans IP, sans identifiant ; date du jour uniquement). Page publique : <a href="' . esc_url( jd_indice_url() ) . '">' . esc_html( jd_indice_url() ) . '</a></p>';
	echo '<p><strong>' . (int) $n . '</strong> réponse(s) enregistrée(s).';
	if ( $n ) {
		echo ' <a class="button" href="' . esc_url( wp_nonce_url( admin_url( 'tools.php?page=jd-indice&jd_export=1' ), 'jd_indice_export' ) ) . '">Exporter en CSV</a>';
	}
	echo '</p>';
	if ( ! $n ) {
		echo '</div>';
		return;
	}
	$dims  = jd_indice_dimensions();
	$sum   = array_fill_keys( array_keys( $dims ), 0 );
	$total = 0;
	$dist  = array();
	foreach ( $rows as $row ) {
		$total += (int) $row['total'];
		$d      = json_decode( $row['dims'], true );
		foreach ( $sum as $k => $v ) {
			$sum[ $k ] += isset( $d[ $k ] ) ? (int) $d[ $k ] : 0;
		}
		foreach ( (array) json_decode( $row['answers'], true ) as $qid => $v ) {
			foreach ( (array) $v as $i ) {
				$dist[ $qid ][ $i ] = isset( $dist[ $qid ][ $i ] ) ? $dist[ $qid ][ $i ] + 1 : 1;
			}
		}
	}
	echo '<h2>Moyennes</h2><table class="widefat striped" style="max-width:640px"><tbody>';
	echo '<tr><th>Indice global</th><td>' . esc_html( round( $total / $n ) ) . ' / 100</td></tr>';
	foreach ( $dims as $k => $dim ) {
		echo '<tr><th>' . esc_html( $dim['label'] ) . '</th><td>' . esc_html( round( $sum[ $k ] / $n ) ) . ' / 100</td></tr>';
	}
	echo '</tbody></table><h2>Répartition des réponses</h2>';
	foreach ( jd_indice_questions() as $q ) {
		echo '<h3 style="max-width:760px">' . esc_html( $q['text'] ) . '</h3><table class="widefat striped" style="max-width:760px"><tbody>';
		foreach ( $q['options'] as $i => $opt ) {
			$c = isset( $dist[ $q['id'] ][ $i ] ) ? $dist[ $q['id'] ][ $i ] : 0;
			echo '<tr><td>' . esc_html( $opt[0] ) . '</td><td style="width:8em">' . (int) $c . ' (' . esc_html( round( 100 * $c / $n ) ) . ' %)</td></tr>';
		}
		echo '</tbody></table>';
	}
	echo '</div>';
}
