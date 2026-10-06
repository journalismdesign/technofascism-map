# Journalism.design — thème WordPress

Thème blocs (Full Site Editing) pour journalism.design. Tout le contenu est construit en blocs Gutenberg et se modifie dans l'éditeur, sans code.

- WordPress 6.5 ou plus récent (testé sur 6.8.3), PHP 7.4 ou plus récent.
- Design system Journalism.design appliqué à toutes les pages (voir plus bas).
- Polices auto-hébergées (Inter Tight, Newsreader italique, JetBrains Mono — licence SIL OFL). Aucune ressource tierce n'est chargée.
- Pas d'extension requise.

## Installation

1. Zipper le dossier `journalism-design/` puis l'importer dans *Apparence › Thèmes › Ajouter › Téléverser*, ou le copier dans `wp-content/themes/`.
2. Activer le thème. À l'activation, les pages ci-dessous sont **préremplies avec leurs blocs Gutenberg** (aucune composition à choisir) :
   - les pages absentes sont créées, les pages vides sont remplies ;
   - une page qui existe déjà avec son propre contenu est conservée et signalée par une alerte dans l'administration ;
   - « Accueil » devient la page d'accueil ;
   - les permaliens passent en `/%postname%/` s'ils étaient au format par défaut.

   Le même préremplissage a lieu au premier passage dans l'administration après une mise à jour du thème, sans réactivation.
3. **Mise à jour du thème** (téléversement du nouveau .zip, puis « Remplacer l'actuel par la version téléversée ») : au premier passage dans l'administration, les pages qui contiennent le contenu du thème reçoivent automatiquement la nouvelle version, et une alerte le confirme. L'ancienne version de chaque page est conservée dans ses révisions. Seules les pages créées depuis la version 1.2.0 et modifiées à la main sont laissées telles quelles et signalées.
4. *Apparence › Contenu Journalism.design* : état de chaque page, boutons « Créer », « Remplir » ou « Remplacer ». Avant tout remplacement, la version actuelle est enregistrée dans les révisions de la page.

Le titre de la page (« Accueil », « Contact »…) n'est jamais affiché : seul le grand titre placé dans le contenu apparaît (par exemple « Concevoir un numérique utile, désirable et maîtrisé. » sur l'accueil).

| Page | Adresse | Statut à la création |
| --- | --- | --- |
| Accueil | `/accueil/` (page d'accueil) | publiée |
| Diagnostic & stratégie | `/diagnostic-strategie/` | publiée |
| Transformation & prototypage | `/transformation-prototypage/` | publiée |
| Gouvernance & souveraineté | `/gouvernance-souverainete/` | publiée |
| Formations | `/formations/` | publiée |
| Cas clients | `/cas-clients/` | publiée (missions récentes et références) |
| À propos | `/a-propos/` | publiée |
| SYNTH | `/synth/` | publiée (bouton vers https://synthmedia.fr) |
| Indice de dépendance numérique | `/indice-dependance-numerique/` | publiée (questionnaire) |
| Contact | `/contact/` | publiée |
| Mentions légales | `/mentions-legales/` | **brouillon** : gabarit à remplir |

## Modifier le site

- **Textes des pages** : *Pages › modifier*. Chaque section est un groupe de blocs ordinaire : on peut la déplacer, la dupliquer ou la supprimer.
- **Ajouter une section** : dans l'outil d'insertion, onglet *Compositions*, catégories « Journalism.design — Sections » et « Appels à l'action ».
- **Menu, en-tête, pied de page** : *Apparence › Éditeur › Compositions › Parties de modèle*.
- **Styles de blocs du thème** (panneau *Styles* d'un bloc sélectionné) :
  - Paragraphe : Kicker [ mono ], Chapô, Affirmation en capitales, Encadré tarif
  - Liste : Index à filets, Puces carrées, Pastilles, Deux colonnes à filets
  - Groupe : Cellule numérotée
  - Bouton : par défaut (encre, ombre cyan), Secondaire (crème cerclé), Contact (cyan cerclé)
- **Titres en deux temps** : dans un titre, mettre la fin en *italique* (Ctrl/Cmd + I) produit la chute en italique serif (en italique grotesque cyan sur fond encre).

## Design system

Le thème applique le design system « Le design de journalism.design » :

| Élément | Règle |
| --- | --- |
| Couleurs | Crème `#F4EFE7`, encre `#0A0F0E`, cyan signal `#01FFE0`. Sarcelle `#0DD4B9` (couleur du logo) réservée aux très grands corps sur crème. Pas de dégradé, pas de transparence. |
| Typographie | Grotesque noir en capitales serrées pour les titres (Inter Tight 800, interlettrage −0,045 em) ; italique serif pour la chute des titres et les noms (Newsreader) ; mono pour les kickers, numéros et mentions (JetBrains Mono). Texte courant 17 px. |
| Grille | Bandes pleine largeur qui alternent crème, encre et cyan ; kicker `[ … ]` dans une colonne d'un quart, contenu à droite ; filets encre de 1 px ; cellules qui partagent leurs filets, numérotées 01, 02, 03. |
| Composants | Boutons rectangulaires à ombre dure de 6 px qui glissent vers leur ombre au survol ; pastilles arrondies (seule forme arrondie) ; liens internes en gras, flèche finale, entre deux filets ; un seul aplat cyan par vue ; bandeau défilant cyan séparé par ✦. |
| Signes | Flèches Unicode (→ aller vers, ↓ descendre, ↗ lien sortant), point médian dans la navigation, pas d'icônes ni d'emoji. |

Le cyan n'est jamais utilisé en petit texte sur fond crème.

Le contenu créé par une version antérieure à 1.2.0 utilisait des couleurs retirées depuis (ardoise, crème foncé…). Des règles de compatibilité en fin de `theme.css` les ramènent à la palette actuelle, pour qu'aucun texte ne se retrouve illisible avant le remplacement des pages.

Les sections (`patterns/`) sont générées par `tools/generate-patterns.py` (Python 3, sans dépendance) pour garantir un balisage Gutenberg valide : modifier ce script puis le relancer plutôt que d'éditer les fichiers à la main.

## Logos

Trois versions dans `assets/images/` (fond transparent, marges rognées) :

| Fichier | Usage |
| --- | --- |
| `logo-journalism-design.png` (noir + cyan) | en-tête, pied de page, page de connexion |
| `logo-journalism-design-fond-sombre.png` (blanc + cyan) | disponible pour les fonds encre |
| `logo-journalism-design-noir.png` (noir) | version imprimée |
| `logo-synth-fond-sombre.png` (blanc + cyan) | section SYNTH (fond encre) |
| `gerald-holubowicz.jpg` (200 × 200, métadonnées EXIF retirées) | portrait de la biographie (page À propos) |

Les logos de l'en-tête et du pied de page sont des blocs Image : on peut les remplacer dans *Apparence › Éditeur › Compositions › Parties de modèle*.

## Indice de dépendance numérique

Auto-diagnostic en 12 questions (5 dimensions : fournisseurs, données, compétences, IA & automatisation, résilience), précédé d'un échauffement personnel facultatif de 3 questions, non noté et non enregistré.

- **Accès** : page autonome `/indice-dependance-numerique/` (code court `[jd_indice]`). Sur toutes les autres pages, tout lien vers cette adresse ouvre le questionnaire en popin ; sans JavaScript, le lien mène simplement à la page. Le bandeau de l'accueil (sous l'ouverture) et la page Diagnostic y renvoient.
- **Barème** : chaque réponse vaut de 0 à 6 points de dépendance ; chaque dimension est ramenée sur 100 et pèse 20 % de l'indice. Le barème est affiché aux visiteurs (« Comment l'indice est calculé »). Questions, points, réactions et textes sont définis dans `inc/indice.php`, source unique pour l'interface, l'e-mail et le baromètre.
- **Résultat** : indice /100, niveau, radar et barres par dimension, trois conclusions (fragilité, point fort, point à investiguer), proposition de journée de diagnostic, lien vers le contenu du livrable.
- **Données** : aucune donnée demandée avant le résultat. L'envoi par e-mail est facultatif, n'accepte que des adresses professionnelles et ne conserve pas l'adresse ; une copie n'est transmise à journalism.design que si le visiteur coche « être recontacté·e » ou « recevoir les publications » (deux consentements distincts).
- **Baromètre** : le visiteur peut partager ses réponses anonymement (ni adresse, ni IP, ni identifiant ; date du jour seulement). Résultats agrégés et export CSV : *Outils › Indice de dépendance*.
- La liste des domaines de messagerie grand public refusés se modifie avec le filtre `jd_indice_free_domains`.

## Formulaire de contact

Bloc *Code court* contenant `[jd_contact]` (page Contact).

**Avec Contact Form 7 (recommandé)** : installer et activer l'extension Contact Form 7. Le thème crée alors automatiquement le formulaire « Journalism.design — Premier échange » (mêmes champs, mise en forme du design system) et `[jd_contact]` l'affiche. Destinataire, objet, corps du message, accusé de réception, messages d'erreur : tout se règle dans *Contact › Formulaires*. Les questions à choix unique utilisent des cases exclusives (une seule réponse possible, champ facultatif). Si le formulaire est supprimé, il est recréé à la prochaine visite de l'administration.

**Sans Contact Form 7** : un formulaire natif prend le relais. Les demandes sont envoyées par e-mail à l'adresse d'administration du site (*Réglages › Général*) et **ne sont pas stockées** dans la base. Les questions se modifient avec le filtre `jd_contact_fields`, le destinataire avec `jd_contact_recipient`.

Dans les deux cas, configurer un SMTP (par exemple avec une extension d'envoi d'e-mails) pour un envoi fiable.

## Formations

La page Formations présente trois niveaux (Sensibiliser, Structurer, Transformer) et une FAQ en blocs *Détails* (dépliables), modifiables dans l'éditeur.

## À compléter

Les URL suivantes ne sont pas encore connues ; tant qu'elles valent `#`, les liens correspondants sont masqués dans le pied de page :

- Inférences
- Ressources

La page Mentions légales reste en brouillon (gabarit à remplir) ; son lien n'apparaît dans le pied de page qu'une fois la page publiée. Même chose pour « Confidentialité », qui dépend de la page de politique de confidentialité de WordPress (*Réglages › Confidentialité*).

L'entrée « SYNTH ↗ » du menu et du pied de page mène à la page `/synth/` du site ; les boutons « Découvrir SYNTH ↗ » mènent à https://synthmedia.fr (nouvel onglet).

On peut les corriger directement dans l'éditeur, ou d'un coup avec un petit mu-plugin :

```php
<?php
add_filter( 'jd_external_urls', function ( $urls ) {
	$urls['inferences'] = 'https://…';
	$urls['ressources'] = 'https://…';
	return $urls;
} );
```

Le filtre s'applique à l'en-tête et au pied de page (tant qu'ils n'ont pas été modifiés dans l'éditeur de site), ainsi qu'aux pages créées après sa mise en place.

## Structure

```
journalism-design/
├── style.css            en-tête du thème
├── theme.json           palette, typographies, espacements, styles globaux
├── functions.php
├── inc/
│   ├── helpers.php        URLs internes et externes
│   ├── block-styles.php   styles de blocs
│   ├── contact-form.php   formulaire [jd_contact]
│   ├── setup-content.php  plan du site et création des pages
│   └── indice.php         indice de dépendance numérique (questionnaire, e-mail, baromètre)
├── patterns/            une section = un fichier (32 sections + en-tête et pied de page)
├── tools/               generate-patterns.py (génère patterns/)
├── parts/               en-tête, pied de page
├── templates/           page (sans titre affiché), page-editorial (identique, compatibilité), single, index, archive, search, 404
└── assets/
    ├── css/theme.css
    ├── js/marquee.js    bandeau défilant
    ├── js/indice.js     questionnaire de l'indice
    ├── images/          logos
    └── fonts/           woff2 + licences OFL
```
