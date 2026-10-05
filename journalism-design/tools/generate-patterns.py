#!/usr/bin/env python3
"""Génère les compositions (patterns) du thème Journalism.design.

Chaque fonction produit le balisage sérialisé exact d'un bloc Gutenberg,
pour que les pages restent valides dans l'éditeur.
"""
import json
import os

OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), '..', 'patterns')


def A(d):
    return (' ' + json.dumps(d, ensure_ascii=False, separators=(',', ':'))) if d else ''


def cls(*names):
    names = [n for n in names if n]
    return f' class="{" ".join(names)}"' if names else ''


def url(slug, anchor=''):
    a = f", '{anchor}'" if anchor else ''
    return f"<?php echo jd_url( '{slug}'{a} ); ?>"


EXT = {"synth": "<?php echo jd_external_url( 'synth' ); ?>"}


# ---------- blocs ----------

def P(html, c=None, align=None, size=None):
    a, k = {}, []
    if align:
        a['align'] = align
        k.append(f'has-text-align-{align}')
    if c:
        a['className'] = c
    if size:
        a['fontSize'] = size
    k2 = ([c] if c else []) + k + ([f'has-{size}-font-size'] if size else [])
    return f'<!-- wp:paragraph{A(a)} -->\n<p{cls(*k2)}>{html}</p>\n<!-- /wp:paragraph -->'


def H(level, html, c=None, align=None, text_align=None):
    a = {}
    if text_align:
        a['textAlign'] = text_align
    if level != 2:
        a['level'] = level
    if align:
        a['align'] = align
    if c:
        a['className'] = c
    k = ['wp-block-heading', f'has-text-align-{text_align}' if text_align else None,
         f'align{align}' if align else None, c]
    return f'<!-- wp:heading{A(a)} -->\n<h{level}{cls(*k)}>{html}</h{level}>\n<!-- /wp:heading -->'


def LIST(items, c=None, ordered=False):
    a = {}
    if ordered:
        a['ordered'] = True
    if c:
        a['className'] = c
    tag = 'ol' if ordered else 'ul'
    lis = '\n\n'.join(f'<!-- wp:list-item -->\n<li>{i}</li>\n<!-- /wp:list-item -->' for i in items)
    return f'<!-- wp:list{A(a)} -->\n<{tag}{cls("wp-block-list", c)}>{lis}</{tag}>\n<!-- /wp:list -->'


def BTN(text, href, style=None, external=False):
    a = {'className': f'is-style-{style}'} if style else {}
    t = ' target="_blank" rel="noreferrer noopener"' if external else ''
    return (f'<!-- wp:button{A(a)} -->\n<div{cls("wp-block-button", a.get("className"))}>'
            f'<a class="wp-block-button__link wp-element-button" href="{href}"{t}>{text}</a></div>\n<!-- /wp:button -->')


def BUTTONS(*btns, c=None, center=False):
    a = {}
    if c:
        a['className'] = c
    if center:
        a['layout'] = {'type': 'flex', 'justifyContent': 'center'}
    return f'<!-- wp:buttons{A(a)} -->\n<div{cls("wp-block-buttons", c)}>' + '\n\n'.join(btns) + '</div>\n<!-- /wp:buttons -->'


def GROUP(children, c=None, tag='div', align=None, bg=None, fg=None, layout='default', anchor=None, grid_min=None):
    a = {}
    if tag != 'div':
        a['tagName'] = tag
    if anchor:
        a['anchor'] = anchor
    if align:
        a['align'] = align
    if c:
        a['className'] = c
    if bg:
        a['backgroundColor'] = bg
    if fg:
        a['textColor'] = fg
    if layout == 'grid':
        a['layout'] = {'type': 'grid', 'minimumColumnWidth': grid_min or '16rem'}
    else:
        a['layout'] = {'type': layout}
    k = ['wp-block-group', f'align{align}' if align else None, c,
         f'has-{fg}-color' if fg else None, f'has-{bg}-background-color' if bg else None,
         'has-text-color' if fg else None, 'has-background' if bg else None]
    idattr = f' id="{anchor}"' if anchor else ''
    return (f'<!-- wp:group{A(a)} -->\n<{tag}{idattr}{cls(*k)}>' + '\n\n'.join(children) +
            f'</{tag}>\n<!-- /wp:group -->')


def COLUMN(children, width=None, valign=None, c=None):
    a = {}
    if valign:
        a['verticalAlignment'] = valign
    if width:
        a['width'] = width
    if c:
        a['className'] = c
    k = ['wp-block-column', f'is-vertically-aligned-{valign}' if valign else None, c]
    st = f' style="flex-basis:{width}"' if width else ''
    return f'<!-- wp:column{A(a)} -->\n<div{cls(*k)}{st}>' + '\n\n'.join(children) + '</div>\n<!-- /wp:column -->'


def COLUMNS(cols, c=None, align='wide', valign=None):
    a = {}
    if valign:
        a['verticalAlignment'] = valign
    if align:
        a['align'] = align
    if c:
        a['className'] = c
    k = ['wp-block-columns', f'align{align}' if align else None,
         f'are-vertically-aligned-{valign}' if valign else None, c]
    return f'<!-- wp:columns{A(a)} -->\n<div{cls(*k)}>' + '\n\n'.join(cols) + '</div>\n<!-- /wp:columns -->'


def SHORTCODE(s):
    return f'<!-- wp:shortcode -->\n{s}\n<!-- /wp:shortcode -->'


# ---------- composants du design system ----------

def kicker(t, align=None):
    return P(t, 'is-style-eyebrow', align=align)


def band(k, body, bg=None, c='', anchor=None):
    """Bande pleine largeur : kicker mono dans une colonne d'un quart, contenu à droite."""
    fg = {'ink': 'paper', 'accent': 'ink'}.get(bg)
    return GROUP([
        COLUMNS([
            COLUMN([kicker(k)], '25%', c='jd-band__kicker'),
            COLUMN(body, '75%', c='jd-band__body'),
        ], c='jd-band'),
    ], c=('jd-section ' + c).strip(), tag='section', align='full', bg=bg, fg=fg,
        layout='constrained', anchor=anchor)


def hero(k, title, cols, extra=None):
    children = [kicker(k), H(1, title, 'jd-hero__title', align='wide')]
    if cols:
        children.append(COLUMNS(cols, c='jd-hero__cols'))
    children += extra or []
    return GROUP(children, c='jd-section jd-hero', tag='section', align='full', layout='constrained')


def cell(title, paras, lead=None):
    ch = [H(3, title)]
    if lead:
        ch.append(P(lead, 'is-style-lead'))
    ch += [P(x) for x in paras]
    return GROUP(ch, c='is-style-principle')


def cells(items, c='', mn='15rem'):
    return GROUP(items, c=('jd-cells ' + c).strip(), layout='grid', grid_min=mn)


def card(num, title, href, text, more='Méthode et contenu →'):
    return GROUP([
        P(num, 'jd-card__num'),
        H(3, f'<a href="{href}">{title}</a>', 'jd-card__name'),
        P(text),
        P(f'<a href="{href}">{more}</a>', 'jd-link'),
    ], c='jd-card')


def panel(children):
    """Encadré fileté encre (tarifs, conditions)."""
    return GROUP(children, c='jd-panel')


def IMAGE(src_php, alt, c=None):
    a = {'sizeSlug': 'full', 'linkDestination': 'none'}
    if c:
        a['className'] = c
    return (f'<!-- wp:image{A(a)} -->\n<figure{cls("wp-block-image", "size-full", c)}>'
            f'<img src="{src_php}" alt="{alt}"/></figure>\n<!-- /wp:image -->')


CONTACT_BTN = lambda style=None: BTN('Prendre contact →', url('contact'), style)

# ---------- sections ----------

S = {}

# ---- Accueil
S['home-hero'] = ('Accueil — Ouverture', 'jd-sections', 'hero, accueil, ouverture', hero(
    'Conseil indépendant · Numérique et IA',
    'Concevoir un numérique utile, désirable et <em>maîtrisé.</em>',
    [
        COLUMN([
            P('Journalism.design aide les organisations à se servir du numérique et de l’intelligence artificielle sans remettre leurs données ni leurs savoir-faire entre les mains d’une poignée de fournisseurs.', 'is-style-lead'),
            P('Nous ne vendons aucun logiciel et ne touchons aucune commission. Le travail porte sur les usages réels des équipes, sur les dépendances qu’ils installent et sur les règles qu’une organisation se donne pour décider de ce qu’elle confie à une machine.'),
        ], '58%'),
        COLUMN([
            BUTTONS(CONTACT_BTN(), BTN('Lire notre position ↓', url('', 'position'), 'ghost')),
        ], '42%', 'bottom'),
    ]))

S['marquee'] = ('Bandeau défilant — principes', 'jd-sections', 'bandeau, défilant, principes, marquee', GROUP([
    P('Utile ✦ Désirable ✦ Maîtrisé ✦ Réversible ✦ Soutenable', 'jd-marquee__text'),
], c='jd-marquee', tag='section', align='full', bg='accent', fg='ink', layout='default'))

S['home-questions'] = ('Accueil — Notre position', 'jd-sections', 'constat, position, questions, stratégie', band(
    'Notre position', [
        H(2, 'La technologie n’est pas <em>une stratégie.</em>'),
        COLUMNS([
            COLUMN([
                P('Les organisations adoptent des outils numériques et des systèmes d’IA plus vite qu’elles ne fixent les règles de leur usage.', 'is-style-lead'),
                P('Microsoft, Google, Adobe, OpenAI, Anthropic, Notion, Salesforce et des services plus spécialisés prennent en charge des fonctions de plus en plus critiques. Chaque abonnement déplace un peu de contrôle sur les données, les formats et les prix vers des entreprises dont les intérêts ne coïncident pas forcément avec ceux de leurs clients.'),
                P('Ces choix pèsent ailleurs que dans le budget. Les grands modèles consomment de l’énergie, de l’eau et du matériel, et ils modifient le travail de celles et ceux qui les utilisent ; les dépendances qu’ils installent deviennent coûteuses à défaire.'),
            ], '45%'),
            COLUMN([
                P('La question n’est donc plus seulement :'),
                P('« Quel outil choisir ? »', 'is-style-question jd-strike'),
                P('Mais :'),
                LIST(['À quoi doit-il réellement servir ?', 'Quelles données peut-on lui confier ?',
                      'De quoi nous rend-il dépendants ?', 'Pourrons-nous encore travailler sans lui demain ?',
                      'Existe-t-il une alternative plus maîtrisable ?',
                      'Que voulons-nous automatiser — et que voulons-nous préserver ?'], 'is-style-index jd-questions'),
                P('Nous aidons les organisations à y répondre avant que les choix techniques ne deviennent des dépendances structurelles.', 'is-style-lead'),
            ], '55%'),
        ], c='jd-inner'),
    ], bg='ink', anchor='position'))

S['commitments'] = ('Ce qui nous engage', 'jd-sections', 'engagements, éthique, indépendance, sobriété', band(
    'Ce qui nous engage', [
        H(2, 'Une position <em>avant une offre</em>'),
        cells([
            cell('Indépendance', ['Aucun éditeur ni aucune plateforme ne nous rémunère. Recommander un logiciel plutôt qu’un autre ne nous rapporte rien.']),
            cell('Proportion', ['Confier à un grand modèle génératif une tâche qu’un script simple accomplit mobilise de l’énergie et crée une dépendance sans contrepartie. Nous dimensionnons les outils selon le besoin et intégrons leur coût environnemental à l’analyse.']),
            cell('Réversibilité', ['Une organisation doit pouvoir changer de fournisseur, récupérer ses données et garder ses compétences. Nous vérifions cette possibilité avant l’adoption d’un outil, plutôt qu’au moment de partir.']),
            cell('Ouverture', ['Logiciels libres, standards ouverts et communs numériques permettent d’inspecter les systèmes et de garder une alternative. Nous les privilégions quand ils répondent au besoin, et nous expliquons les compromis quand un outil propriétaire s’impose.']),
            cell('Regard journalistique', ['Le travail de <em>SYNTH</em>, le média que nous éditons, sur les entreprises technologiques, l’IA et leurs effets sur le travail et l’environnement nourrit chaque mission.']),
        ], 'jd-cells--3'),
    ]))

S['approach'] = ('Notre approche — cinq principes', 'jd-sections', 'approche, principes, valeurs', band(
    'Méthode', [
        H(2, 'Cinq principes <em>de travail</em>'),
        cells([
            cell('Utile', ['La technologie doit répondre à un problème réel. Nous partons des usages, des métiers et des contraintes de l’organisation, et non des fonctionnalités disponibles sur le marché.']),
            cell('Désirable', ['Une transformation numérique réussie améliore le travail, les produits ou les services. La réduction des coûts, à elle seule, ne fait pas une vision.']),
            cell('Maîtrisé', ['Une organisation doit savoir où se trouvent ses données, comment fonctionnent ses outils et de quels fournisseurs elle dépend.']),
            cell('Réversible', ['Pouvoir changer de fournisseur, exporter ses données et conserver ses compétences constitue une capacité stratégique.']),
            cell('Soutenable', ['Les décisions intègrent leurs conséquences économiques, humaines, sociales et environnementales.']),
        ], 'jd-cells--3'),
    ]))

S['levels-overview'] = ('Trois niveaux d’intervention (cartes)', 'jd-sections', 'expertises, interventions, niveaux', band(
    'Interventions', [
        H(2, 'Trois niveaux <em>d’intervention</em>'),
        GROUP([
            card('01', 'Diagnostic &amp; stratégie', url('diagnostic-strategie'), 'Comprendre ses usages, ses dépendances et ses marges de manœuvre.'),
            card('02', 'Transformation &amp; prototypage', url('transformation-prototypage'), 'Construire des alternatives qui fonctionnent réellement.'),
            card('03', 'Gouvernance &amp; souveraineté numérique', url('gouvernance-souverainete'), 'Choisir ses dépendances plutôt que les subir.'),
        ], c='jd-cells jd-cells--3 jd-cards', layout='grid', grid_min='15rem'),
    ], anchor='expertises'))

S['terrains'] = ('Nos terrains d’intervention', 'jd-sections', 'clients, secteurs, terrains', band(
    'Pour qui', [
        H(2, 'Nos terrains <em>d’intervention</em>'),
        P('Nous travaillons surtout avec des organisations dont l’activité repose sur la production et la circulation de l’information.', 'is-style-lead'),
        LIST(['Médias', 'Agences', 'Studios', 'Directions communication', 'Équipes marketing', 'Institutions',
              'Organisations culturelles', 'Associations', 'Structures de l’ESS', 'Établissements d’enseignement',
              'Entreprises disposant d’équipes éditoriales ou créatives'], 'is-style-columns'),
        P('Les mêmes questions d’IA, de données et de dépendance numérique se posent ailleurs, et nos méthodes s’y appliquent aussi.', 'jd-note'),
    ]))

S['formations-teaser'] = ('Formations — encart', 'jd-sections', 'formation, ateliers', band(
    'Formations', [
        H(2, 'Comprendre pour <em>pouvoir choisir.</em>'),
        COLUMNS([
            COLUMN([P('Une organisation ne maîtrise pas ses technologies si seules quelques personnes en comprennent le fonctionnement.', 'is-style-lead')], '60%'),
            COLUMN([BUTTONS(BTN('Voir les formations →', url('formations'), 'ghost'))], '40%', 'bottom'),
        ], c='jd-inner', valign='bottom'),
    ]))

S['synth'] = ('SYNTH — média', 'jd-sections', 'synth, média, journalisme', band(
    'SYNTH', [
        IMAGE("<?php echo esc_url( get_theme_file_uri( 'assets/images/logo-synth-fond-sombre.png' ) ); ?>",
              'Synth.', 'jd-synth-logo'),
        H(2, 'Observer les rapports de pouvoir <em>créés par la technologie</em>'),
        COLUMNS([
            COLUMN([
                P('Journalism.design édite <strong><em>SYNTH</em></strong>, un média indépendant consacré aux conséquences politiques, économiques, sociales, culturelles et environnementales des technologies contemporaines.', 'is-style-lead'),
                BUTTONS(BTN('Découvrir SYNTH ↗', EXT['synth'], 'ghost', external=True)),
            ], '50%'),
            COLUMN([
                P('Ce travail journalistique nourrit en permanence notre compréhension :'),
                LIST(['des infrastructures numériques', 'des entreprises technologiques', 'des systèmes d’intelligence artificielle',
                      'de leurs modèles économiques', 'des enjeux environnementaux', 'des transformations du travail',
                      'et des rapports de pouvoir qu’ils produisent'], 'is-style-arrows'),
                P('<strong>SYNTH observe ces transformations.</strong><br><strong>Journalism.design aide les organisations à agir face à elles.</strong>', 'jd-motto'),
            ], '50%'),
        ], c='jd-inner'),
    ], bg='ink'))

S['first-step'] = ('Prendre contact (appel à l’action)', 'jd-cta', 'cta, contact', GROUP([
    kicker('Prendre contact', align='center'),
    H(2, 'Commençons par le problème, <em>pas par l’outil.</em>', text_align='center'),
    P('Décrivez votre situation : les outils en place, ce qui coince, ce que vous tenez à préserver. Un premier échange permet de savoir si nous pouvons être utiles.', 'is-style-lead', align='center'),
    BUTTONS(BTN('Écrire →', url('contact')), center=True),
], c='jd-section jd-cta', tag='section', align='full', bg='accent', fg='ink', layout='constrained'))

# ---- Diagnostic & stratégie
S['diagnostic-hero'] = ('Diagnostic & stratégie — Ouverture', 'jd-sections', 'diagnostic, stratégie, hero', hero(
    'Expertise 01', 'Diagnostic &amp; <em>stratégie</em>', [
        COLUMN([P('Comprendre ses usages, ses dépendances et ses marges de manœuvre.', 'is-style-question')], '50%'),
        COLUMN([
            P('Nous regardons comment votre organisation se sert réellement du numérique et de l’intelligence artificielle, y compris là où personne ne l’a déclaré.', 'is-style-lead'),
            P('La cartographie couvre les outils, les workflows, les données, les irritants et les dépendances, ainsi que les usages qui se sont installés de manière informelle.'),
            P('Le diagnostic mesure les marges de manœuvre qui restent à l’organisation face à ses fournisseurs, une question que les audits informatiques classiques laissent généralement de côté.'),
        ], '50%'),
    ]))

S['diagnostic-mission'] = ('Diagnostic & stratégie — Objectifs et contenu de mission', 'jd-sections', 'diagnostic, mission, cartographie', band(
    'Objectif', [
        H(2, 'Ce que le diagnostic <em>établit</em>'),
        COLUMNS([
            COLUMN([LIST(['ce qui fonctionne', 'ce qui constitue une dépendance', 'ce qui présente un risque',
                          'ce qui pourrait être amélioré', 'et les transformations qui méritent réellement d’être engagées'],
                         'is-style-index jd-questions')], '55%'),
            COLUMN([
                H(6, 'Selon le contexte, la mission peut inclure'),
                LIST(['Cartographie des workflows', 'Cartographie des outils et fournisseurs', 'Analyse des usages IA existants',
                      'Identification des données sensibles', 'Analyse des dépendances numériques', 'Évaluation de la réversibilité',
                      'Identification des opportunités d’automatisation',
                      'Analyse des solutions européennes, libres ou open source pertinentes',
                      'Priorisation des transformations', 'Feuille de route'], 'is-style-arrows'),
            ], '45%'),
        ], c='jd-inner'),
    ], bg='ink'))

S['diagnostic-day'] = ('Diagnostic & stratégie — Une première journée (tarif)', 'jd-cta', 'tarif, journée, diagnostic, prix', band(
    'Pour commencer', [
        H(2, 'Une journée <em>pour y voir clair</em>'),
        COLUMNS([
            COLUMN([
                P('Pour une première approche, nous proposons une <strong>journée de diagnostic stratégique</strong> : entretiens, analyse des principaux workflows, restitution.', 'is-style-lead'),
                P('Elle permet d’identifier les premiers enjeux et de décider, en connaissance de cause, s’il vaut la peine d’aller plus loin.'),
            ], '50%'),
            COLUMN([panel([
                kicker('Tarif'),
                P('À partir de 1&nbsp;500&nbsp;€&nbsp;HT.', 'is-style-price'),
                P('Les diagnostics approfondis et les accompagnements font l’objet d’un devis, établi selon le périmètre, la taille des équipes et les problèmes identifiés.'),
                BUTTONS(CONTACT_BTN()),
            ])], '50%'),
        ], c='jd-inner'),
    ]))

# ---- Transformation & prototypage
S['transformation-hero'] = ('Transformation & prototypage — Ouverture', 'jd-sections', 'transformation, prototypage, hero', hero(
    'Expertise 02', 'Transformation &amp; <em>prototypage</em>', [
        COLUMN([P('Construire des alternatives qui fonctionnent réellement.', 'is-style-question')], '50%'),
        COLUMN([
            P('Une transformation numérique commence mal quand elle commence par le choix d’un logiciel.', 'is-style-lead'),
            P('Elle devrait partir d’une question :'),
            P('<span class="jd-hl">Qu’essayons-nous d’améliorer ?</span>', 'is-style-question'),
        ], '50%'),
    ], extra=[COLUMNS([
        COLUMN([
            P('Journalism.design conçoit et teste avec vos équipes d’autres façons de travailler, en combinant selon les cas :', 'is-style-lead'),
            P('Remplacer par principe toutes les technologies propriétaires n’aurait pas de sens. L’enjeu est de pouvoir choisir à nouveau, en connaissant le prix de chaque option.'),
        ], '50%'),
        COLUMN([LIST(['intelligence artificielle', 'automatisation', 'outils existants', 'solutions européennes',
                      'open source', 'logiciels libres', 'standards ouverts', 'infrastructures ou modèles maîtrisés'],
                     'is-style-tags')], '50%'),
    ], c='jd-split')]))

S['transformation-trajectories'] = ('Transformation — Trois trajectoires possibles', 'jd-sections', 'optimiser, hybrider, migrer, trajectoires', band(
    'Trajectoires', [
        H(2, 'Trois trajectoires <em>possibles</em>'),
        cells([
            cell('Optimiser', ['Simplifier les workflows, supprimer les tâches inutiles, introduire des automatismes ou de nouveaux usages.'], lead='Mieux utiliser l’environnement existant.'),
            cell('Hybrider', ['Conserver certains outils tout en introduisant progressivement des solutions plus ouvertes ou mieux maîtrisées pour les fonctions sensibles.']),
            cell('Migrer', ['Remplacer certains composants lorsque leur coût, leur dépendance ou leur niveau de risque deviennent problématiques.']),
        ], 'jd-cells--3'),
    ], bg='ink'))

S['transformation-prototype'] = ('Transformation — Nous prototypons avant de déployer', 'jd-sections', 'prototype, mission, déploiement', band(
    'Méthode', [
        H(2, 'Nous prototypons <em>avant de déployer</em>'),
        COLUMNS([
            COLUMN([
                P('Une démonstration technologique peut impressionner sans résister au travail quotidien.', 'is-style-lead'),
                P('Nous testons donc les solutions dans les conditions réelles de l’organisation, avec les personnes qui devront s’en servir.'),
            ], '45%'),
            COLUMN([
                H(6, 'Une mission peut comprendre'),
                LIST(['Analyse d’un workflow', 'Conception d’un nouveau processus', 'Prototype', 'Comparaison de plusieurs solutions',
                      'Tests avec les équipes', 'Analyse des erreurs et cas limites', 'Procédures de contrôle', 'Documentation',
                      'Formation', 'Plan de déploiement'], 'is-style-index', ordered=True),
            ], '55%'),
        ], c='jd-inner'),
    ]))

S['transformation-ia'] = ('Transformation — IA & production', 'jd-sections', 'IA, production, usages', band(
    'Usages', [
        H(2, 'IA &amp; <em>production</em>'),
        P('Nous intervenons notamment sur les usages suivants.', 'is-style-lead'),
        LIST(['Recherche et documentation', 'Analyse de corpus', 'Transcription', 'Traitement de documents', 'Préparation éditoriale',
              'Production multimodale', 'Image et vidéo', 'Storyboard', 'Motion design', 'Contrôle qualité',
              'Transformation de données', 'Maquette vers production', 'Automatisation de tâches répétitives',
              'Diffusion et déclinaisons'], 'is-style-tags'),
        P('Pour chacun, nous examinons aussi ce qu’il ne faut pas confier à une machine : les tâches qui engagent un jugement humain et les données qui ne doivent pas sortir de l’organisation.'),
    ], bg='ink'))

S['transformation-desirable'] = ('Transformation — Une alternative doit être désirable', 'jd-sections', 'alternative, désirable, souveraineté, design', band(
    'Parti pris', [
        H(2, 'Une alternative doit <em>être désirable</em>'),
        COLUMNS([
            COLUMN([
                P('Une solution ouverte, européenne ou souveraine qui dégrade le travail des équipes finit par être abandonnée.', 'is-style-lead'),
                P('Nous cherchons le compromis le plus tenable entre efficacité, autonomie et responsabilité, en sachant qu’aucun environnement numérique n’est irréprochable.'),
            ], '50%'),
            COLUMN([
                H(6, 'Notre approche croise'),
                LIST(['souveraineté', 'design produit', 'usages', 'organisation', 'expérience utilisateur'], 'is-style-index jd-questions'),
                panel([
                    P('Les missions sont construites <strong>sur devis</strong>, après analyse du périmètre et des objectifs.'),
                    BUTTONS(CONTACT_BTN()),
                ]),
            ], '50%'),
        ], c='jd-inner'),
    ]))

# ---- Gouvernance & souveraineté
S['gouvernance-hero'] = ('Gouvernance & souveraineté — Ouverture', 'jd-sections', 'gouvernance, souveraineté, hero', hero(
    'Expertise 03', 'Gouvernance &amp; <em>souveraineté numérique</em>', [
        COLUMN([
            P('Choisir ses dépendances plutôt que les subir.', 'is-style-question'),
            P('Aucune organisation n’est totalement indépendante technologiquement.', 'is-style-lead'),
            P('La souveraineté consiste à connaître ses dépendances et à garder la possibilité d’en sortir.'),
        ], '50%'),
        COLUMN([
            H(6, 'Elle suppose de savoir'),
            LIST(['de quoi l’on dépend', 'pourquoi', 'avec quelles conséquences', 'et comment reprendre la main si nécessaire'],
                 'is-style-index jd-questions'),
            P('Nous aidons les organisations à se doter de règles et de stratégies qui leur laissent la maîtrise de leurs données, de leurs outils et de leur capacité de production.'),
        ], '50%'),
    ]))

S['gouvernance-reversibilite'] = ('Gouvernance — Le droit à la réversibilité numérique', 'jd-sections', 'réversibilité, portabilité, dépendance', band(
    'Le droit à la réversibilité numérique', [
        P('Avant d’adopter un nouvel outil, une organisation devrait pouvoir répondre à une question simple :', 'is-style-lead'),
        H(2, 'Pourrons-nous encore fonctionner sans lui <em>dans trois ans ?</em>', 'jd-big-question'),
        P('Cela suppose notamment d’examiner :'),
        LIST(['la portabilité des données', 'les formats utilisés', 'l’interopérabilité', 'les compétences conservées en interne',
              'la propriété intellectuelle', 'la localisation des données', 'la juridiction applicable',
              'la dépendance économique', 'la capacité à migrer'], 'is-style-columns'),
    ], bg='ink'))

S['gouvernance-doctrine'] = ('Gouvernance — Une doctrine numérique & IA', 'jd-sections', 'doctrine, IA, questions, politique', band(
    'Doctrine', [
        H(2, 'Une doctrine <em>numérique &amp; IA</em>'),
        P('Nous aidons les organisations à formaliser leurs choix par écrit, pour qu’ils tiennent face aux changements d’équipe et aux nouvelles offres du marché.', 'is-style-lead'),
        H(6, 'Parmi les questions traitées'),
        LIST(['Quels outils peuvent être utilisés ?', 'Quels types de données peuvent être confiés à quels fournisseurs ?',
              'Quand privilégier une solution européenne ?', 'Quand privilégier un logiciel libre ou open source ?',
              'Dans quels cas un SaaS propriétaire reste-t-il pertinent ?', 'Quand faut-il envisager un modèle d’IA local ou privé ?',
              'Quels usages de l’IA nécessitent une validation humaine ?', 'Quelles compétences doivent rester dans l’organisation ?',
              'Quels formats doivent rester ouverts ?', 'Quelles solutions de repli prévoir ?',
              'Comment évaluer les nouveaux outils qui apparaîtront demain ?'], 'is-style-index', ordered=True),
    ]))

S['gouvernance-regles'] = ('Gouvernance — Des règles utilisables', 'jd-cta', 'règles, livrables, charte, politique', band(
    'Livrables', [
        H(2, 'Des règles <em>applicables</em>'),
        P('Une politique numérique ne sert à rien si les équipes ne peuvent pas l’appliquer.', 'is-style-lead'),
        COLUMNS([
            COLUMN([
                H(6, 'Nous pouvons notamment produire'),
                LIST(['Doctrine numérique et IA', 'Charte d’usage', 'Classification des données', 'Matrice usages / données / fournisseurs',
                      'Critères de sélection technologique', 'Politique de réversibilité', 'Politique de transparence',
                      'Procédures de contrôle humain', 'Référentiel de validation', 'Gouvernance de mise à jour', 'Plan de formation'],
                     'is-style-tags'),
            ], '55%'),
            COLUMN([panel([
                P('Ces missions sont réalisées <strong>sur devis</strong>, selon la taille de l’organisation, son environnement numérique et le niveau d’accompagnement nécessaire.'),
                BUTTONS(CONTACT_BTN()),
            ])], '45%'),
        ], c='jd-inner'),
    ], bg='ink'))

# ---- Formations
S['formations-hero'] = ('Formations — Ouverture', 'jd-sections', 'formation, hero', hero(
    'Formations', 'Comprendre pour <em>pouvoir choisir.</em>', [
        COLUMN([P('Une organisation ne maîtrise pas ses technologies si seules quelques personnes en comprennent le fonctionnement.', 'is-style-lead')], '50%'),
        COLUMN([P('Nous concevons des formations, des ateliers et des conférences pour que les équipes comprennent les systèmes qu’elles utilisent et prennent part aux décisions qui les concernent.')], '50%'),
    ]))

S['formations-list'] = ('Formations — Exemples', 'jd-sections', 'formation, ateliers, catalogue', band(
    'Formations', [
        H(2, '<em>Exemples</em>'),
        cells([
            cell('Comprendre l’intelligence artificielle', ['Modèles, données, probabilités, limites, biais, hallucinations et enjeux économiques.']),
            cell('IA &amp; métiers', ['Identifier les usages pertinents et les tâches qu’il n’est pas souhaitable d’automatiser.']),
            cell('Données, confidentialité &amp; IA', ['Comprendre ce qui arrive réellement aux informations envoyées dans les différents services.']),
            cell('Souveraineté numérique', ['Comprendre les dépendances technologiques et les stratégies permettant de les réduire.']),
            cell('Logiciel libre, open source &amp; communs', ['Comprendre les modèles ouverts et leur intérêt stratégique.']),
            cell('Construire une doctrine IA', ['Travailler collectivement sur les règles et critères de l’organisation.']),
        ], 'jd-cells--3'),
        COLUMNS([
            COLUMN([
                P('Chaque intervention est adaptée au niveau des équipes, aux métiers concernés et aux objectifs de l’organisation.', 'is-style-lead'),
                P('<strong>Formations et parcours collectifs sur devis.</strong>'),
            ], '60%'),
            COLUMN([BUTTONS(CONTACT_BTN())], '40%', 'bottom'),
        ], c='jd-inner', valign='bottom'),
    ], bg='ink'))

# ---- À propos
S['about-hero'] = ('À propos — Ouverture', 'jd-sections', 'à propos, hero, conviction', hero(
    'À propos', 'Éditorial. Produit. Technologie. <em>Organisation.</em>', [
        COLUMN([P('Journalism.design accompagne les organisations dont les pratiques sont transformées par le numérique et l’intelligence artificielle.', 'is-style-lead')], '50%'),
        COLUMN([
            P('Notre approche repose sur une conviction :'),
            '<!-- wp:quote {"className":"jd-conviction"} -->\n<blockquote class="wp-block-quote jd-conviction">' +
            P('Une organisation doit pouvoir bénéficier des technologies sans perdre la capacité de comprendre, choisir et gouverner les systèmes dont elle dépend.') +
            '</blockquote>\n<!-- /wp:quote -->',
        ], '50%'),
    ]))

S['about-gerald'] = ('À propos — Gérald Holubowicz (biographie)', 'jd-sections', 'biographie, fondateur, portrait, équipe', band(
    'Fondateur', [
        H(2, 'Gérald <em>Holubowicz</em>'),
        COLUMNS([
            COLUMN([IMAGE("<?php echo esc_url( get_theme_file_uri( 'assets/images/gerald-holubowicz.jpg' ) ); ?>",
                          'Portrait de Gérald Holubowicz', 'jd-portrait')], '28%'),
            COLUMN([
                P('Journaliste, auteur, réalisateur et spécialiste de l’innovation éditoriale, Gérald Holubowicz travaille depuis plus de quinze ans à l’intersection des médias et des technologies numériques.', 'is-style-lead'),
                P('Photojournaliste puis auteur de projets documentaires interactifs, il a cofondé le studio Chewbahat et présidé Storycode Paris, où il a organisé plus d’une centaine de conférences et une vingtaine d’ateliers consacrés aux nouvelles écritures.'),
                P('Il a ensuite travaillé sur des enjeux de produit et d’innovation éditoriale pour Libération, Condé Nast et le Groupe Les Échos–Le Parisien.'),
                P('Depuis 2017, ses recherches portent notamment sur les deepfakes, les médias synthétiques puis l’intelligence artificielle générative et leurs conséquences sur l’information, le travail et les industries créatives.'),
                P('Il intervient aujourd’hui auprès d’organisations pour les aider à transformer cette compréhension critique en décisions, méthodes et outils concrets.'),
            ], '72%'),
        ], c='jd-inner'),
    ]))

S['about-independence'] = ('À propos — Nous ne vendons pas de technologie', 'jd-sections', 'indépendance, neutralité, partenaires', band(
    'Indépendance', [
        H(2, 'Nous ne vendons pas <em>de technologie.</em>', 'jd-big-question'),
        COLUMNS([
            COLUMN([
                P('Journalism.design est indépendant des éditeurs, plateformes et fournisseurs qu’il évalue.', 'is-style-lead'),
                P('Nous ne sommes pas rémunérés pour recommander un logiciel plutôt qu’un autre.'),
                P('Quand la mise en œuvre demande des compétences spécialisées en développement, infrastructure, cybersécurité, migration ou intégration, nous travaillons avec les équipes existantes ou avec des partenaires choisis pour l’occasion.'),
            ], '50%'),
            COLUMN([
                H(6, 'Notre rôle consiste à aider une organisation à déterminer'),
                LIST(['ce dont elle a réellement besoin', 'quels compromis elle accepte', 'quelles dépendances elle souhaite éviter',
                      'et quelles capacités elle veut conserver en interne'], 'is-style-index jd-questions'),
            ], '50%'),
        ], c='jd-inner'),
    ], bg='ink'))

S['about-open-source'] = ('Notre rapport à l’open source et aux communs', 'jd-sections', 'open source, logiciel libre, communs', band(
    'Open source et communs', [
        H(2, 'L’ouverture est un moyen de conserver <em>du pouvoir d’agir.</em>'),
        COLUMNS([
            COLUMN([
                P('Les logiciels libres, l’open source, les standards ouverts et les communs numériques sont souvent de puissants outils de souveraineté.', 'is-style-lead'),
                P('Nous les privilégions lorsqu’ils répondent réellement aux besoins de l’organisation, sans en faire une solution universelle.'),
                P('Un outil propriétaire est parfois le meilleur choix. Notre travail consiste alors à rendre visibles les compromis que l’organisation accepte en le choisissant.'),
            ], '50%'),
            COLUMN([
                H(6, 'Ils permettent notamment'),
                LIST(['d’inspecter les systèmes', 'de conserver l’accès aux données', 'de réduire certaines dépendances',
                      'de mutualiser des ressources', 'de favoriser l’interopérabilité', 'et de maintenir des alternatives'], 'is-style-arrows'),
            ], '50%'),
        ], c='jd-inner'),
    ]))

S['about-responsable'] = ('Un numérique responsable n’est pas un numérique frugal par principe', 'jd-sections', 'numérique responsable, environnement, sobriété', band(
    'Environnement', [
        H(2, 'Un numérique responsable n’est pas un numérique <em>frugal par principe</em>'),
        P('Il s’agit de proportionner les moyens technologiques aux besoins.', 'is-style-lead'),
        P('Faire accomplir par un grand modèle génératif une tâche qu’un script simple réglerait n’a souvent aucun intérêt. Conserver indéfiniment des volumes considérables de données non plus.'),
        P('Nous intégrons la question environnementale à l’analyse des architectures, des usages et des fournisseurs, partout où elle pèse dans la décision.'),
    ]))

# ---- Contact
S['contact'] = ('Contact — Premier échange (formulaire)', 'jd-sections, jd-cta', 'contact, formulaire, devis', hero(
    'Premier échange', 'Commençons par le problème, <em>pas par l’outil.</em>', [
        COLUMN([P('Vous n’avez pas besoin de savoir quelle prestation demander.', 'is-style-lead')], '50%'),
        COLUMN([
            P('Décrivez votre situation, les outils actuellement utilisés et les difficultés que vous voulez résoudre.'),
            P('Nous verrons ensemble si le sujet relève d’un diagnostic ponctuel, d’un accompagnement plus large ou d’une autre expertise que la nôtre.'),
        ], '50%'),
    ]) + '\n\n' + band('Formulaire', [
        COLUMNS([
            COLUMN([SHORTCODE('[jd_contact]')], '62%'),
            COLUMN([panel([
                kicker('Première étape'),
                P('Vous pouvez aussi commencer par une <strong>journée de diagnostic stratégique</strong>.'),
                P('À partir de 1&nbsp;500&nbsp;€&nbsp;HT.', 'is-style-price'),
                P('Elle permet d’examiner votre situation avec un regard extérieur, d’identifier les principaux enjeux et de déterminer les étapes suivantes.'),
                P('Les accompagnements plus larges font toujours l’objet d’une proposition et d’un <strong>devis adaptés au périmètre de la mission.</strong>', size='s'),
            ])], '38%', c='jd-sticky-col'),
        ], c='jd-inner'),
    ], c='jd-contact'))

# ---- Cas clients (gabarits)
S['cases-hero'] = ('Cas clients — Ouverture', 'jd-sections', 'cas clients, références, hero', hero(
    'Cas clients', 'Cas <em>clients</em>', [
        COLUMN([P('[À rédiger : texte d’introduction de la page.]', 'is-style-lead')], '60%'),
    ]))

S['case-study'] = ('Cas client — fiche (gabarit à remplir)', 'jd-sections', 'cas client, étude de cas, référence', band(
    '[Secteur] · [Expertise mobilisée]', [
        H(2, '[Nom de l’organisation]'),
        LIST(['[Durée]', '[Taille de l’équipe]'], 'is-style-tags'),
        cells([
            GROUP([H(6, 'Contexte'), P('[Situation de départ, outils utilisés, difficultés rencontrées.]')], c='is-style-principle'),
            GROUP([H(6, 'Intervention'), P('[Ce qui a été fait, avec qui, selon quelle méthode.]')], c='is-style-principle'),
            GROUP([H(6, 'Résultats'), P('[Effets constatés, décisions prises, livrables.]')], c='is-style-principle'),
        ], 'jd-cells--3'),
    ]))

S['legal'] = ('Mentions légales (gabarit à remplir)', 'jd-sections', 'mentions légales, éditeur, hébergeur', hero(
    'Mentions légales', 'Mentions <em>légales</em>', None) + '\n\n' + band('Informations', [
        H(4, 'Éditeur du site'),
        P('[Raison sociale, forme juridique, capital, adresse du siège, SIRET, numéro de TVA intracommunautaire.]'),
        H(4, 'Directeur de la publication'),
        P('[Nom et coordonnées.]'),
        H(4, 'Hébergement'),
        P('[Nom, adresse et téléphone de l’hébergeur.]'),
        H(4, 'Contact'),
        P('[Adresse e-mail de contact.]'),
    ], c='jd-prose'))


def write():
    for slug, (title, cats, kw, body) in S.items():
        php = f"""<?php
/**
 * Title: {title}
 * Slug: journalism-design/{slug}
 * Categories: {cats}
 * Keywords: {kw}
 *
 * Fichier généré : la structure suit le design system Journalism.design.
 *
 * @package journalism-design
 */
?>
{body}
"""
        with open(os.path.join(OUT, slug + '.php'), 'w') as f:
            f.write(php)
    print(len(S), 'compositions écrites')


if __name__ == '__main__':
    write()
