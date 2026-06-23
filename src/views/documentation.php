<?php
// ============================================================
// src/views/documentation.php
// Page documentation — Tuto pêche pour les débutants
// ============================================================
if (empty($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
$initiale = strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1));
$username = htmlspecialchars($_SESSION['username'] ?? 'Utilisateur');
$email    = htmlspecialchars($_SESSION['email']    ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebPêche — La pêche pour les débutants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/accueil.css">
    <link rel="stylesheet" href="/assets/css/documentation.css">
</head>
<body class="theme-light" id="app">
<div class="app-container">

    <!-- ══════════════════════════════════════
         SIDEBAR GAUCHE (identique à toutes les pages)
    ══════════════════════════════════════ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="user-card">
                <div class="user-avatar"><?= $initiale ?></div>
                <div class="user-info">
                    <span class="app-brand">WebPêche</span>
                    <span class="user-email"><?= $email ?: $username ?></span>
                </div>
            </div>
            <button class="btn-icon" id="btn-close" title="Fermer le menu">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <p class="nav-label">NAVIGATION</p>
            <a href="/accueil" class="nav-link">
                <i class="fa-solid fa-house"></i><span>Home Map</span>
            </a>
            <a href="/profil" class="nav-link">
                <i class="fa-regular fa-circle-user"></i><span>Profil</span>
            </a>
            <a href="/parametres" class="nav-link">
                <i class="fa-solid fa-gear"></i><span>Paramètres</span>
            </a>
            <a href="/documentation" class="nav-link active">
                <i class="fa-solid fa-book"></i><span>Documentation</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="theme-toggle">
                <button class="theme-btn active" id="btn-light">
                    <i class="fa-solid fa-sun"></i> Light
                </button>
                <button class="theme-btn" id="btn-dark">
                    <i class="fa-solid fa-moon"></i> Dark
                </button>
            </div>
        </div>
    </aside>

    <!-- ══════════════════════════════════════
         ZONE DOCUMENTATION
    ══════════════════════════════════════ -->
    <div class="doc-wrapper">

        <!-- Barre haute avec bouton ouvrir sidebar + titre de page -->
        <header class="doc-topbar">
            <button class="btn-icon" id="btn-open" title="Ouvrir le menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="doc-topbar-breadcrumb">
                <span>Documentation</span>
                <i class="fa-solid fa-chevron-right"></i>
                <span class="breadcrumb-active">Pêche pour les débutants</span>
            </div>
        </header>

        <div class="doc-layout">

            <!-- ── SOMMAIRE LATÉRAL (rubriques) ── -->
            <nav class="doc-toc" id="doc-toc">
                <p class="toc-label">Sur cette page</p>
                <ul>
                    <li><a href="#intro"       class="toc-link active"><i class="fa-solid fa-fish"></i> Introduction</a></li>
                    <li><a href="#materiel"     class="toc-link"><i class="fa-solid fa-toolbox"></i> Le matériel</a></li>
                    <li><a href="#ou-aller"     class="toc-link"><i class="fa-solid fa-map-location-dot"></i> Où aller pêcher</a></li>
                    <li><a href="#techniques"   class="toc-link"><i class="fa-solid fa-person-fishing"></i> Techniques de base</a></li>
                    <li><a href="#reglementation" class="toc-link"><i class="fa-solid fa-scale-balanced"></i> Réglementation</a></li>
                    <li><a href="#protegees"    class="toc-link"><i class="fa-solid fa-shield-halved"></i> Espèces protégées</a></li>
                    <li><a href="#invasives"    class="toc-link"><i class="fa-solid fa-triangle-exclamation"></i> Espèces invasives</a></li>
                    <li><a href="#videos"       class="toc-link"><i class="fa-brands fa-youtube"></i> Vidéos explicatives</a></li>
                </ul>

                <div class="toc-card-info">
                    <i class="fa-solid fa-circle-info"></i>
                    <p>Cette page est mise à jour régulièrement. En cas de doute sur une réglementation, consultez toujours la <a href="https://www.federationpeche.fr" target="_blank" rel="noopener">Fédération Nationale</a>.</p>
                </div>
            </nav>

            <!-- ── CONTENU PRINCIPAL ── -->
            <main class="doc-content">

                <!-- ─────────────────────────────────
                     HERO — titre de la page
                ───────────────────────────────── -->
                <div class="doc-hero">
                    <div class="doc-hero-tag">Guide complet</div>
                    <h1 class="doc-hero-title">La pêche pour les débutants</h1>
                    <p class="doc-hero-subtitle">Tout ce qu'il faut savoir pour commencer à pêcher en France : matériel, lieux, techniques, réglementation et espèces à connaître.</p>
                    <div class="doc-hero-meta">
                        <span><i class="fa-regular fa-clock"></i> ~15 min de lecture</span>
                        <span><i class="fa-solid fa-layer-group"></i> Niveau débutant</span>
                    </div>
                </div>

                <!-- ─────────────────────────────────
                     SECTION 1 — Introduction
                ───────────────────────────────── -->
                <section class="doc-section" id="intro">
                    <div class="section-label">01 — Introduction</div>
                    <h2>Pourquoi se mettre à la pêche ?</h2>
                    <p>La pêche est l'une des activités de plein air les plus pratiquées en France, avec plus de <strong>1,5 million de pêcheurs</strong> réguliers. C'est un loisir accessible à tous les âges, peu coûteux à démarrer, et qui permet de se reconnecter avec la nature tout en développant la patience et l'observation.</p>
                    <p>Contrairement à ce qu'on croit souvent, la pêche n'est pas réservée aux experts. Avec un équipement simple et quelques notions de base, vous pouvez attraper vos premiers poissons dès votre première sortie.</p>

                    <div class="doc-callout doc-callout--blue">
                        <i class="fa-solid fa-lightbulb"></i>
                        <div>
                            <strong>Bon à savoir</strong>
                            <p>En France, la pêche en eau douce nécessite l'adhésion à une Association Agréée de Pêche et de Protection du Milieu Aquatique (AAPPMA). Cette carte de pêche est votre premier achat obligatoire — environ <strong>90 €/an</strong> pour un adulte, bien moins pour les jeunes de moins de 18 ans.</p>
                        </div>
                    </div>
                </section>

                <!-- ─────────────────────────────────
                     SECTION 2 — Le matériel
                ───────────────────────────────── -->
                <section class="doc-section" id="materiel">
                    <div class="section-label">02 — Le matériel</div>
                    <h2>Le matériel nécessaire</h2>
                    <p>Inutile de dépenser des centaines d'euros pour débuter. Un kit de base bien choisi suffit amplement pour vos premières sorties.</p>

                    <h3>L'essentiel absolu</h3>

                    <div class="materiel-grid">
                        <div class="materiel-card">
                            <div class="materiel-icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></div>
                            <div class="materiel-body">
                                <h4>Canne à pêche télescopique</h4>
                                <p>Pour débuter, une canne télescopique entre 3 et 4 m est idéale. Simple à utiliser, pas de moulinet à maîtriser.</p>
                                <div class="materiel-links">
                                    <a href="https://www.decathlon.fr/p/canne-a-peche-coup-lakeside-1-telescopique-3m-5m/_/R-p-325163" target="_blank" rel="noopener" class="btn-link btn-link--decathlon"><i class="fa-solid fa-bag-shopping"></i> Decathlon (~15 €)</a>
                                </div>
                            </div>
                        </div>

                        <div class="materiel-card">
                            <div class="materiel-icon"><i class="fa-solid fa-circle-notch"></i></div>
                            <div class="materiel-body">
                                <h4>Fil de pêche (nylon)</h4>
                                <p>Un nylon de 0,18 à 0,22 mm de diamètre convient à la plupart des situations en eau douce pour débuter.</p>
                                <div class="materiel-links">
                                    <a href="https://www.decathlon.fr/p/nylon-peche-coup-lakeside-100m/_/R-p-325170" target="_blank" rel="noopener" class="btn-link btn-link--decathlon"><i class="fa-solid fa-bag-shopping"></i> Decathlon (~5 €)</a>
                                </div>
                            </div>
                        </div>

                        <div class="materiel-card">
                            <div class="materiel-icon"><i class="fa-solid fa-circle"></i></div>
                            <div class="materiel-body">
                                <h4>Flotteur & plombs</h4>
                                <p>Le flotteur vous indique quand un poisson mord. Choisissez un kit flotteur + plombs adapté à votre canne.</p>
                                <div class="materiel-links">
                                    <a href="https://www.decathlon.fr/p/kit-montage-coup-lakeside/_/R-p-325175" target="_blank" rel="noopener" class="btn-link btn-link--decathlon"><i class="fa-solid fa-bag-shopping"></i> Decathlon (~8 €)</a>
                                </div>
                            </div>
                        </div>

                        <div class="materiel-card">
                            <div class="materiel-icon"><i class="fa-solid fa-hook"></i></div>
                            <div class="materiel-body">
                                <h4>Hameçons</h4>
                                <p>Pour débuter, des hameçons de taille 10 à 14 couvrent la plupart des espèces courantes. Achetez-en en sachet.</p>
                                <div class="materiel-links">
                                    <a href="https://www.decathlon.fr/p/hamecon-peche-coup-simple-lakeside-taille-10/_/R-p-325180" target="_blank" rel="noopener" class="btn-link btn-link--decathlon"><i class="fa-solid fa-bag-shopping"></i> Decathlon (~3 €)</a>
                                </div>
                            </div>
                        </div>

                        <div class="materiel-card">
                            <div class="materiel-icon"><i class="fa-solid fa-wheat-awn"></i></div>
                            <div class="materiel-body">
                                <h4>Appâts</h4>
                                <p>Pour commencer : des vers de terre (les trouver dans votre jardin ou en acheter en animalerie) ou du pain de mie. Les vers fonctionnent avec quasiment toutes les espèces.</p>
                                <div class="materiel-links">
                                    <a href="https://www.decathlon.fr/p/appat-peche-vers-de-vase-1-boite/_/R-p-325185" target="_blank" rel="noopener" class="btn-link btn-link--decathlon"><i class="fa-solid fa-bag-shopping"></i> Decathlon (~4 €)</a>
                                </div>
                            </div>
                        </div>

                        <div class="materiel-card">
                            <div class="materiel-icon"><i class="fa-solid fa-scissors"></i></div>
                            <div class="materiel-body">
                                <h4>Boîte de pêche + épuisette</h4>
                                <p>Une petite boîte pour ranger vos accessoires + une épuisette pour sortir les poissons de l'eau proprement sans les blesser.</p>
                                <div class="materiel-links">
                                    <a href="https://www.decathlon.fr/p/epuisette-peche-coup-lakeside-manche-2m/_/R-p-325190" target="_blank" rel="noopener" class="btn-link btn-link--decathlon"><i class="fa-solid fa-bag-shopping"></i> Decathlon (~12 €)</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="doc-callout doc-callout--green">
                        <i class="fa-solid fa-wallet"></i>
                        <div>
                            <strong>Budget débutant complet</strong>
                            <p>Comptez entre <strong>40 et 60 €</strong> pour un kit de départ complet chez Decathlon ou dans un magasin de pêche spécialisé. Évitez les équipements bas de gamme (moins de 15 €ht le kit complet) qui cassent rapidement.</p>
                        </div>
                    </div>

                    <h3>Le matériel optionnel (mais utile)</h3>
                    <ul class="doc-list">
                        <li><strong>Seau ou nasse de gardiennage</strong> — pour garder les poissons vivants avant de les relâcher</li>
                        <li><strong>Siège de pêche pliable</strong> — pour les longues sessions (<a href="https://www.decathlon.fr/browse/c0-peche/_/N-1dqf6e5" target="_blank" rel="noopener">voir chez Decathlon</a>)</li>
                        <li><strong>Tapis de réception</strong> — protège le poisson quand vous l'amenez à terre</li>
                        <li><strong>Pince à hameçon</strong> — pour décrocher les hameçons sans blesser le poisson ni vos doigts</li>
                    </ul>
                </section>

                <!-- ─────────────────────────────────
                     SECTION 3 — Où aller pêcher
                ───────────────────────────────── -->
                <section class="doc-section" id="ou-aller">
                    <div class="section-label">03 — Où aller pêcher</div>
                    <h2>Où aller pêcher en France ?</h2>
                    <p>La France est un paradis pour la pêche en eau douce : rivières, lacs, étangs, canaux… Il existe des spots adaptés aux débutants partout sur le territoire.</p>

                    <h3>Les types d'eaux à connaître</h3>

                    <div class="type-eau-grid">
                        <div class="type-eau-card">
                            <div class="type-eau-header type-eau-header--1">
                                <i class="fa-solid fa-droplet"></i>
                                <span>1ère catégorie</span>
                            </div>
                            <p>Eaux rapides, froides, oxygénées. On y trouve surtout des <strong>truites et ombres</strong>. La pêche s'ouvre le 2e samedi de mars et ferme le 3e dimanche de septembre.</p>
                        </div>
                        <div class="type-eau-card">
                            <div class="type-eau-header type-eau-header--2">
                                <i class="fa-solid fa-water"></i>
                                <span>2ème catégorie</span>
                            </div>
                            <p>Eaux plus lentes, plus chaudes. On y trouve <strong>carpes, brochets, perches, sandres</strong>. Pas de saison fermée sauf pour certaines espèces.</p>
                        </div>
                    </div>

                    <div class="doc-callout doc-callout--yellow">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <div>
                            <strong>Utilisez notre carte WebPêche !</strong>
                            <p>Les spots repérés sur notre carte par la communauté sont parfaits pour trouver un endroit près de chez vous. <a href="/accueil">Voir la carte →</a></p>
                        </div>
                    </div>

                    <h3>Comment trouver les plans d'eau autorisés ?</h3>
                    <ul class="doc-list">
                        <li>Le site de votre <strong>AAPPMA locale</strong> (association de pêche de votre commune ou département) liste les parcours accessibles avec votre carte</li>
                        <li>Le site <a href="https://www.cartepeche.fr" target="_blank" rel="noopener">cartepeche.fr</a> — carte officielle de toutes les AAPPMA de France</li>
                        <li>Les <strong>étangs communaux</strong> souvent peu fréquentés et idéaux pour débuter</li>
                        <li>Les <strong>canaux navigables</strong> (Voies Navigables de France) — accessibles avec une carte nationale</li>
                    </ul>
                </section>

                <!-- ─────────────────────────────────
                     SECTION 4 — Techniques de base
                ───────────────────────────────── -->
                <section class="doc-section" id="techniques">
                    <div class="section-label">04 — Techniques</div>
                    <h2>Techniques de base</h2>

                    <h3>La pêche au coup (la plus simple)</h3>
                    <p>C'est la technique idéale pour débuter. On utilise un flotteur, et on laisse l'appât descendre à une profondeur définie. Quand le flotteur s'enfonce ou s'incline, c'est qu'un poisson mord !</p>

                    <div class="steps-list">
                        <div class="step">
                            <div class="step-num">1</div>
                            <div class="step-body">
                                <strong>Assemblez votre montage</strong>
                                <p>Enfilez les plombs sur le fil, puis le flotteur, nouez l'hameçon. Réglez la profondeur avec le stop-flotteur.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-num">2</div>
                            <div class="step-body">
                                <strong>Amorcez votre poste</strong>
                                <p>Lancez une petite boule d'amorce ou du pain émietté à l'endroit où vous voulez pêcher pour attirer les poissons.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-num">3</div>
                            <div class="step-body">
                                <strong>Accrochez l'appât</strong>
                                <p>Piquez le ver sur l'hameçon en le traversant une ou deux fois. Il doit rester bien accroché mais avoir encore du mouvement.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-num">4</div>
                            <div class="step-body">
                                <strong>Lancez et observez</strong>
                                <p>Lancez délicatement votre ligne, tendez légèrement et observez le flotteur. Patience — c'est tout l'art de la pêche !</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-num">5</div>
                            <div class="step-body">
                                <strong>Ferrez et récupérez</strong>
                                <p>Quand le flotteur plonge, relevez la canne d'un coup sec (le "ferrage"), puis remontez le poisson doucement.</p>
                            </div>
                        </div>
                    </div>

                    <h3>No-kill ou garder le poisson ?</h3>
                    <p>La pratique du <strong>no-kill</strong> (relâcher le poisson après capture) est fortement recommandée pour les débutants et contribue à préserver les populations. Si vous souhaitez garder un poisson pour le consommer, assurez-vous d'abord qu'il n'est pas protégé et qu'il dépasse la taille minimale légale de capture.</p>
                </section>

                <!-- ─────────────────────────────────
                     SECTION 5 — Réglementation
                ───────────────────────────────── -->
                <section class="doc-section" id="reglementation">
                    <div class="section-label">05 — Réglementation</div>
                    <h2>La réglementation en France</h2>

                    <div class="regle-grid">
                        <div class="regle-card">
                            <div class="regle-icon"><i class="fa-solid fa-id-card"></i></div>
                            <h4>Carte de pêche obligatoire</h4>
                            <p>Toute personne de plus de 12 ans doit être titulaire d'une carte de pêche (adhésion AAPPMA). Sans elle, vous risquez une amende.</p>
                            <a href="https://www.cartepeche.fr" target="_blank" rel="noopener" class="btn-link btn-link--blue">Acheter en ligne →</a>
                        </div>
                        <div class="regle-card">
                            <div class="regle-icon"><i class="fa-solid fa-calendar-days"></i></div>
                            <h4>Saisons de pêche</h4>
                            <p>En 1ère catégorie, la truite se pêche uniquement du 2e samedi de mars au 3e dimanche de septembre. En 2e catégorie, la plupart des espèces sont pêchables toute l'année.</p>
                        </div>
                        <div class="regle-card">
                            <div class="regle-icon"><i class="fa-solid fa-ruler"></i></div>
                            <h4>Tailles minimales</h4>
                            <p>Chaque espèce a une taille légale minimale de capture. Ex : truite fario → 23 cm, brochet → 60 cm, sandre → 40 cm. En dessous, relâchez obligatoirement.</p>
                        </div>
                        <div class="regle-card">
                            <div class="regle-icon"><i class="fa-solid fa-hashtag"></i></div>
                            <h4>Nombre de lignes</h4>
                            <p>Avec une carte de pêche standard, vous pouvez utiliser jusqu'à 4 lignes simultanément en 2e catégorie, 2 lignes en 1ère catégorie.</p>
                        </div>
                    </div>

                    <div class="doc-callout doc-callout--red">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <div>
                            <strong>Infractions les plus courantes</strong>
                            <p>Pêcher sans carte, pêcher hors saison, conserver un poisson en dessous de la taille légale ou prélever une espèce protégée sont des infractions passibles de plusieurs centaines d'euros d'amende et de confiscation du matériel.</p>
                        </div>
                    </div>
                </section>

                <!-- ─────────────────────────────────
                     SECTION 6 — Espèces protégées
                ───────────────────────────────── -->
                <section class="doc-section" id="protegees">
                    <div class="section-label">06 — Espèces protégées</div>
                    <h2>Comment savoir si une espèce est protégée ?</h2>
                    <p>Certaines espèces de poissons sont totalement protégées en France : leur capture, même accidentelle, doit être immédiatement suivie d'un relâcher sans manipulation inutile.</p>

                    <h3>Les espèces les plus connues totalement protégées</h3>

                    <div class="espece-list">
                        <div class="espece-row espece-row--protected">
                            <div class="espece-badge espece-badge--protected"><i class="fa-solid fa-shield-halved"></i> Protégée</div>
                            <div class="espece-info">
                                <strong>Bouvière (<em>Rhodeus amarus</em>)</strong>
                                <p>Petit poisson de 5-8 cm fréquentant les eaux douces calmes. Reconnaissable à sa ligne médiane colorée chez le mâle.</p>
                            </div>
                        </div>
                        <div class="espece-row espece-row--protected">
                            <div class="espece-badge espece-badge--protected"><i class="fa-solid fa-shield-halved"></i> Protégée</div>
                            <div class="espece-info">
                                <strong>Apron du Rhône (<em>Zingel asper</em>)</strong>
                                <p>Espèce critique, endémique au Rhône et à ses affluents. Extrêmement rare, strictement protégée.</p>
                            </div>
                        </div>
                        <div class="espece-row espece-row--protected">
                            <div class="espece-badge espece-badge--protected"><i class="fa-solid fa-shield-halved"></i> Protégée</div>
                            <div class="espece-info">
                                <strong>Écrevisses à pattes blanches (<em>Austropotamobius pallipes</em>)</strong>
                                <p>Espèce native française, en fort déclin. Toute capture, même involontaire, doit être signalée.</p>
                            </div>
                        </div>
                        <div class="espece-row espece-row--protected">
                            <div class="espece-badge espece-badge--protected"><i class="fa-solid fa-shield-halved"></i> Protégée</div>
                            <div class="espece-info">
                                <strong>Lamproie de Planer (<em>Lampetra planeri</em>)</strong>
                                <p>Ressemble à une anguille, mais c'est un poisson sans mâchoire. Protégée dans de nombreuses régions.</p>
                            </div>
                        </div>
                    </div>

                    <h3>Comment vérifier ?</h3>
                    <ul class="doc-list">
                        <li>Consultez l'<a href="https://inpn.mnhn.fr/accueil/index" target="_blank" rel="noopener">Inventaire National du Patrimoine Naturel (INPN)</a> — base officielle des espèces protégées en France</li>
                        <li>Le site de la <a href="https://www.federationpeche.fr/les-especes" target="_blank" rel="noopener">Fédération Nationale de la Pêche</a> liste les espèces par région</li>
                        <li>En cas de doute sur le terrain : <strong>relâchez toujours immédiatement</strong></li>
                    </ul>
                </section>

                <!-- ─────────────────────────────────
                     SECTION 7 — Espèces invasives
                ───────────────────────────────── -->
                <section class="doc-section" id="invasives">
                    <div class="section-label">07 — Espèces invasives</div>
                    <h2>Les espèces invasives : que faire ?</h2>
                    <p>À l'opposé des espèces protégées, certaines espèces invasives représentent une menace pour les écosystèmes aquatiques français. Leur relâcher dans un milieu naturel est <strong>interdit par la loi</strong>.</p>

                    <div class="doc-callout doc-callout--red">
                        <i class="fa-solid fa-ban"></i>
                        <div>
                            <strong>Interdiction absolue</strong>
                            <p>Il est interdit de relâcher une espèce invasive dans un milieu naturel. En cas de capture, euthanasier le poisson ou le remettre aux autorités compétentes (fédération de pêche locale).</p>
                        </div>
                    </div>

                    <div class="espece-list">
                        <div class="espece-row espece-row--invasive">
                            <div class="espece-badge espece-badge--invasive"><i class="fa-solid fa-triangle-exclamation"></i> Invasive</div>
                            <div class="espece-info">
                                <strong>Poisson-chat (<em>Ameiurus melas</em>)</strong>
                                <p>Originaire d'Amérique du Nord. Reconnaissable à ses "moustaches" (barbillons). Très prolifique, il concurrence les espèces locales. <strong>Ne jamais relâcher.</strong></p>
                            </div>
                        </div>
                        <div class="espece-row espece-row--invasive">
                            <div class="espece-badge espece-badge--invasive"><i class="fa-solid fa-triangle-exclamation"></i> Invasive</div>
                            <div class="espece-info">
                                <strong>Perche soleil (<em>Lepomis gibbosus</em>)</strong>
                                <p>Originaire d'Amérique du Nord également. Très colorée (bleu et orange), elle est envahissante dans de nombreux plans d'eau français.</p>
                            </div>
                        </div>
                        <div class="espece-row espece-row--invasive">
                            <div class="espece-badge espece-badge--invasive"><i class="fa-solid fa-triangle-exclamation"></i> Invasive</div>
                            <div class="espece-info">
                                <strong>Écrevisses américaines & signal</strong>
                                <p>Porteuses de la "peste des écrevisses", elles déciment les populations indigènes. Si vous en capturez, ne les relâchez pas.</p>
                            </div>
                        </div>
                        <div class="espece-row espece-row--invasive">
                            <div class="espece-badge espece-badge--invasive"><i class="fa-solid fa-triangle-exclamation"></i> Invasive</div>
                            <div class="espece-info">
                                <strong>Poisson rouge & carpe koï sauvages</strong>
                                <p>Des poissons d'aquarium relâchés dans la nature perturbent l'écosystème. C'est interdit — ne jamais relâcher vos animaux de compagnie aquatiques.</p>
                            </div>
                        </div>
                    </div>

                    <p>Pour signaler une espèce invasive, contactez votre <a href="https://www.cartepeche.fr" target="_blank" rel="noopener">fédération de pêche locale</a> ou utilisez l'application <a href="https://www.inpn.mnhn.fr/actualites/lire/10684" target="_blank" rel="noopener">Suricate de l'INPN</a>.</p>
                </section>

                <!-- ─────────────────────────────────
                     SECTION 8 — Vidéos
                ───────────────────────────────── -->
                <section class="doc-section" id="videos">
                    <div class="section-label">08 — Vidéos</div>
                    <h2>Vidéos explicatives en français</h2>
                    <p>Ces vidéos sélectionnées vous aideront à visualiser les techniques et à mieux comprendre la pêche en pratique.</p>

                    <div class="videos-grid">
                        <div class="video-card">
                            <div class="video-embed">
                                <iframe
                                    src="https://www.youtube.com/embed/M9SjRz66kB0"
                                    title="Apprendre à pêcher pour les débutants"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy">
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h4>Débuter la pêche au coup</h4>
                                <p>Montage de la ligne, amorçage, technique de lancer — les bases expliquées clairement.</p>
                                <span class="video-tag"><i class="fa-solid fa-person-fishing"></i> Technique</span>
                            </div>
                        </div>

                        <div class="video-card">
                            <div class="video-embed">
                                <iframe
                                    src="https://www.youtube.com/embed/nxJH7-8tqiI"
                                    title="Matériel de pêche débutant"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy">
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h4>Quel matériel choisir ?</h4>
                                <p>Tour d'horizon du matériel essentiel pour débuter sans se ruiner, avec des conseils concrets.</p>
                                <span class="video-tag"><i class="fa-solid fa-toolbox"></i> Matériel</span>
                            </div>
                        </div>

                        <div class="video-card">
                            <div class="video-embed">
                                <iframe
                                    src="https://www.youtube.com/embed/xJ7pFMVmVto"
                                    title="Réglementation pêche France"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    loading="lazy">
                                </iframe>
                            </div>
                            <div class="video-info">
                                <h4>La réglementation expliquée</h4>
                                <p>Carte de pêche, saisons, tailles légales — tout ce qu'il faut savoir pour pêcher légalement.</p>
                                <span class="video-tag"><i class="fa-solid fa-scale-balanced"></i> Réglementation</span>
                            </div>
                        </div>
                    </div>

                    <div class="doc-callout doc-callout--blue">
                        <i class="fa-brands fa-youtube"></i>
                        <div>
                            <strong>Chaînes YouTube recommandées</strong>
                            <p>Pour aller plus loin : <a href="https://www.youtube.com/@FishingChaos" target="_blank" rel="noopener">Fishing Chaos</a>, <a href="https://www.youtube.com/@carpcontact" target="_blank" rel="noopener">Carp Contact</a>, et les vidéos officielles de la <a href="https://www.youtube.com/@FedNatPeche" target="_blank" rel="noopener">Fédération Nationale de Pêche</a>.</p>
                        </div>
                    </div>
                </section>

                <!-- Pied de page de la doc -->
                <footer class="doc-footer">
                    <p>Contenu rédigé par l'équipe WebPêche. Pour toute question ou correction, contactez-nous.</p>
                    <a href="/accueil" class="btn-link btn-link--blue"><i class="fa-solid fa-map"></i> Retour à la carte</a>
                </footer>

            </main><!-- /doc-content -->
        </div><!-- /doc-layout -->
    </div><!-- /doc-wrapper -->

</div><!-- /app-container -->

<script>
(function () {
    // ── Thème clair / sombre ──────────────────────────────────
    const app    = document.getElementById('app');
    const btnL   = document.getElementById('btn-light');
    const btnD   = document.getElementById('btn-dark');
    const saved  = localStorage.getItem('webpeche_theme') || 'light';

    function applyTheme(t) {
        app.classList.toggle('theme-dark', t === 'dark');
        app.classList.toggle('theme-light', t !== 'dark');
        btnL.classList.toggle('active', t !== 'dark');
        btnD.classList.toggle('active', t === 'dark');
        localStorage.setItem('webpeche_theme', t);
    }
    applyTheme(saved);
    btnL.addEventListener('click', () => applyTheme('light'));
    btnD.addEventListener('click', () => applyTheme('dark'));

    // ── Sidebar ouvrir / fermer ───────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const btnOpen = document.getElementById('btn-open');
    const btnClose = document.getElementById('btn-close');

    btnOpen.addEventListener('click', () => { sidebar.classList.remove('collapsed'); btnOpen.style.display = 'none'; });
    btnClose.addEventListener('click', () => { sidebar.classList.add('collapsed'); btnOpen.style.display = 'flex'; });

    // ── Surlignage actif dans le sommaire au scroll ───────────
    const sections = document.querySelectorAll('.doc-section[id]');
    const tocLinks = document.querySelectorAll('.toc-link');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                tocLinks.forEach(l => l.classList.remove('active'));
                const active = document.querySelector(`.toc-link[href="#${entry.target.id}"]`);
                if (active) active.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    sections.forEach(s => observer.observe(s));

    // ── Scroll doux pour les ancres du sommaire ───────────────
    tocLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>
</body>
</html>
