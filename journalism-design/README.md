# Journalism.design — thème WordPress

Thème blocs (Full Site Editing) pour journalism.design. Tout le contenu est construit en blocs Gutenberg et se modifie dans l'éditeur, sans code.

- WordPress 6.5 ou plus récent (testé sur 6.8.3), PHP 7.4 ou plus récent.
- Polices auto-hébergées (Archivo, Newsreader, IBM Plex Mono — licence SIL OFL). Aucune ressource tierce n'est chargée.
- Pas d'extension requise.

## Installation

1. Zipper le dossier `journalism-design/` puis l'importer dans *Apparence › Thèmes › Ajouter › Téléverser*, ou le copier dans `wp-content/themes/`.
2. Activer le thème. À l'activation, les pages ci-dessous sont **préremplies avec leurs blocs Gutenberg** (aucune composition à choisir) :
   - les pages absentes sont créées, les pages vides sont remplies ;
   - une page qui existe déjà avec son propre contenu est conservée et signalée par une alerte dans l'administration ;
   - « Accueil » devient la page d'accueil ;
   - les permaliens passent en `/%postname%/` s'ils étaient au format par défaut.

   Le même préremplissage a lieu au premier passage dans l'administration après une mise à jour du thème, sans réactivation.
3. *Apparence › Contenu Journalism.design* : état de chaque page, boutons « Créer », « Remplir » ou « Remplacer ». Avant tout remplacement, la version actuelle est enregistrée dans les révisions de la page.

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
- **Couleurs et typographies** : *Apparence › Éditeur › Styles*. Couleur d'accent : cyan `#00FFE0`. Trop claire pour du texte sur fond papier, elle sert d'aplat (boutons, bandeau d'appel à l'action, pastilles, surlignage au survol) avec du texte noir, et de couleur de texte sur fonds sombres.
- **Styles de blocs du thème** (panneau *Styles* d'un bloc sélectionné) :
  - Paragraphe : Surtitre (mono), Chapô, Question (grand titre), Encadré tarif
  - Liste : Index à filets, Flèches, Étiquettes, Deux colonnes à filets
  - Groupe : Carte, Filet épais au-dessus, Principe (numéroté)
  - Bouton : Cyan, Contour
  - Colonnes : Colonnes séparées par des filets

## Logos

Trois versions dans `assets/images/` (fond transparent, marges rognées) :

| Fichier | Usage |
| --- | --- |
| `logo-journalism-design.png` (noir + cyan) | en-tête, page de connexion |
| `logo-journalism-design-fond-sombre.png` (blanc + cyan) | pied de page (fond noir) |
| `logo-journalism-design-noir.png` (noir) | version imprimée |

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
├── patterns/            une section = un fichier (33 sections)
├── parts/               en-tête, pied de page
├── templates/           page (sans titre affiché), page-editorial (identique, compatibilité), single, index, archive, search, 404
└── assets/
    ├── css/theme.css
    ├── images/          logos
    └── fonts/           woff2 + licences OFL
```
