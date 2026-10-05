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
3. **Mise à jour du thème** : une page dont le contenu inséré par le thème n'a pas été modifié reçoit automatiquement la nouvelle version (l'ancienne reste dans les révisions). Une page modifiée à la main, ou créée par une version antérieure à 1.2.0, est seulement signalée par une alerte : à remplacer depuis l'outil ci-dessous.
4. *Apparence › Contenu Journalism.design* : état de chaque page, boutons « Créer », « Remplir » ou « Remplacer ». Avant tout remplacement, la version actuelle est enregistrée dans les révisions de la page.

Le titre de la page (« Accueil », « Contact »…) n'est jamais affiché : seul le grand titre placé dans le contenu apparaît (par exemple « Concevoir un numérique utile, désirable et maîtrisé. » sur l'accueil).

| Page | Adresse | Statut à la création |
| --- | --- | --- |
| Accueil | `/accueil/` (page d'accueil) | publiée |
| Diagnostic & stratégie | `/diagnostic-strategie/` | publiée |
| Transformation & prototypage | `/transformation-prototypage/` | publiée |
| Gouvernance & souveraineté | `/gouvernance-souverainete/` | publiée |
| Formations | `/formations/` | publiée |
| Cas clients | `/cas-clients/` | **brouillon** : gabarit à remplir |
| À propos | `/a-propos/` | publiée |
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
| `gerald-holubowicz.jpg` (200 × 200, métadonnées EXIF retirées) | portrait de la biographie (page À propos) |

Les logos de l'en-tête et du pied de page sont des blocs Image : on peut les remplacer dans *Apparence › Éditeur › Compositions › Parties de modèle*.

## Formulaire de contact

Bloc *Code court* contenant `[jd_contact]` (page Contact). Les demandes sont envoyées par e-mail à l'adresse d'administration du site (*Réglages › Général*) et **ne sont pas stockées** dans la base. Pour un envoi fiable, configurer un SMTP (par exemple avec une extension d'envoi d'e-mails).

Les questions à choix se modifient avec le filtre `jd_contact_fields`, le destinataire avec `jd_contact_recipient`.

## À compléter

Les URL suivantes n'étaient pas connues au moment de la création du thème et pointent vers `#` :

- SYNTH (menu, pied de page, section SYNTH)
- Inférences (pied de page)
- Ressources (pied de page)

On peut les corriger directement dans l'éditeur, ou d'un coup avec un petit mu-plugin :

```php
<?php
add_filter( 'jd_external_urls', function ( $urls ) {
	$urls['synth']      = 'https://…';
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
│   └── setup-content.php  plan du site et création des pages
├── patterns/            une section = un fichier (32 sections + en-tête et pied de page)
├── tools/               generate-patterns.py (génère patterns/)
├── parts/               en-tête, pied de page
├── templates/           page (sans titre affiché), page-editorial (identique, compatibilité), single, index, archive, search, 404
└── assets/
    ├── css/theme.css
    ├── js/marquee.js    bandeau défilant
    ├── images/          logos
    └── fonts/           woff2 + licences OFL
```
