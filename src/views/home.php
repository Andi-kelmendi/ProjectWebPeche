<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/home.css">
    <title>WebPêche</title>
</head>
<body>

    <!-- NAVBAR -->
    <nav>
        <a href="/" class="nav-logo">
        <img src="../assets/img/logo.png" alt="WebPêche" width="50">
        </a>
        <div class="liens">
            <ul class="nav-links">
                <li><a href="/">Accueil</a></li>
                <li><a href="/carte">Carte</a></li>
                <li><a href="/support">Support</a></li>
            </ul>
            <a href="/login" class="btn-connect">Se connecter</a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <!-- TEXTE GAUCHE -->
        <div class="hero-text">
            <h1>Découvrez les Meilleurs endroits de Pêche</h1>
            <p>Partagez vos expériences, trouvez les meilleurs endroits de pêche et connectez-vous avec une communauté passionnée de pêche.</p>
            <div class="hero-buttons">
                <a href="/spots" class="btn-secondary">Explorer les spots</a>
                <a href="/register" class="btn-primary">Rejoindre la Communauté</a>
            </div>
        </div>

        <!-- IMAGE (2 téléphones) -->
        <div class="hero-image">
            <img src="../assets/img/phone.png" alt="Aperçu de l'application">
        </div>
    </section>

    <!-- =====================================================
         SPOTS DE PÊCHE POPULAIRES
    ===================================================== -->
    <section class="spots-section">
        <h2>Spots de Pêche Populaires</h2>
        <p class="section-subtitle">Découvrez les endroits les mieux notés par notre communauté</p>

        <div class="spots-grid">

            <div class="spot-card">
                <div class="spot-img">
                    <img src="../assets/img/imgHome/lac1.svg" alt="Rivière des Truites">
                    <span class="spot-rating">⭐ 4.8</span>
                </div>
                <div class="spot-info">
                    <h3>Rivière des Truites</h3>
                    <p class="spot-species">Espèces présentes : truite fario, ombre chevalier</p>
                    <p class="spot-region">📍 Alpes-Maritimes</p>
                </div>
            </div>

            <div class="spot-card">
                <div class="spot-img">
                    <img src="../assets/img/imgHome/lac2.svg" alt="Lac de Monteynard">
                    <span class="spot-rating">⭐ 4.8</span>
                </div>
                <div class="spot-info">
                    <h3>Lac de Monteynard – Isère</h3>
                    <p class="spot-species">Espèces présentes : truite fario, saumon de fontaine</p>
                    <p class="spot-region">📍 Alpes-Maritimes</p>
                </div>
            </div>

            <div class="spot-card">
                <div class="spot-img">
                    <img src="../assets/img/imgHome/lac3.svg" alt="Lac du Salagou">
                    <span class="spot-rating">⭐ 4.8</span>
                </div>
                <div class="spot-info">
                    <h3>Lac du Salagou – Hérault</h3>
                    <p class="spot-species">Espèces présentes : truite fario, ombre commun, saumon de fontaine</p>
                    <p class="spot-region">📍 Alpes-Maritimes</p>
                </div>
            </div>

        </div>
    </section>

    <!-- =====================================================
         REJOINDRE LA COMMUNAUTÉ
    ===================================================== -->
    <section class="community-section">
        <div class="community-left">
            <h2>Rejoindre Notre Communauté</h2>
            <p class="section-subtitle">Découvrez une communauté passionné par la pêche</p>

            <div class="community-features">
                <div class="feature">
                    <h3>Discussions Passionnées</h3>
                    <p>Echangez conseils, techniques et histoires avec pêcheurs Expérimentés</p>
                </div>
                <div class="feature">
                    <h3>Partagez vos Prises</h3>
                    <p>Montrez vos plus belles captures et inspirez la communauté</p>
                </div>
                <div class="feature">
                    <h3>Organisez des Sorties</h3>
                    <p>Planifiez des sorties de pêche en groupe et rencontrez de nouveaux amis</p>
                </div>
            </div>
        </div>

        <div class="community-right">
            <img src="../assets/img/imgHome/team-pecheur.svg" alt="Communauté WebPêche">
        </div>
    </section>

    <!-- =====================================================
        BAS DE PAGE
    ===================================================== -->
    <section class="cta-section">
        <h2>Rejoindre Notre Communauté</h2>
        <p>Découvrez une communauté passionné par la pêche</p>
        <a href="/register" class="btn-cta">Commencer l'Aventure</a>
    </section>

</body>
</html>