# 🎣 Project WebPêche

**Une plateforme communautaire de partage de spots de pêche**  
Développé par **4 étudiants** dans le cadre d'un projet académique.

---

## 📋 Description

**WebPêche** est une application web moderne permettant aux pêcheurs de :

- Découvrir et partager des **spots de pêche** sur une carte interactive (Leaflet)
- Ajouter des spots avec géolocalisation, description, espèces et note
- Noter et commenter les spots
- Filtrer les spots par espèce de poisson
- Accéder à une documentation complète pour débutants

Le projet propose une interface agréable avec thème clair/sombre et une expérience utilisateur fluide.

---

## ✨ Fonctionnalités principales

### 🗺️ Carte Interactive
- Affichage des spots avec marqueurs
- Recherche locale + recherche géographique (Nominatim)
- Filtre par espèce de poisson
- Mode placement : clic sur la carte pour ajouter un spot
- Popup et panneau latéral détaillé

### 👤 Gestion Utilisateurs
- Inscription et connexion
- "Se souvenir de moi" (jetons sécurisés)
- Profil et paramètres
- Suppression de ses propres spots (ou en tant qu'admin)

### ⭐ Interactions Communautaires
- Notation par étoiles (1 à 5)
- Commentaires détaillés
- Note moyenne recalculée automatiquement

### 📚 Documentation
- Guide complet pour débutants (matériel, techniques, réglementation, espèces protégées/invasives, astuces pro…)
- Vidéos intégrées

### 🎨 Interface & Expérience
- Design moderne et responsive
- Thème clair / sombre
- Sidebar rétractable
- Panneau latéral "Voir plus"

---

## 🛠️ Technologies utilisées

- **Backend** : PHP 8 + PDO
- **Base de données** : MySQL
- **Frontend** : HTML5, CSS3 (variables CSS), JavaScript vanilla
- **Cartographie** : Leaflet.js + OpenStreetMap
- **Autoloading** : Composer (PSR-4)
- **Icônes** : Font Awesome 6

---

## 🚀 Installation

### 1. Cloner le projet
```bash
git clone <URL_DU_REPO>
cd ProjectWebPeche-main
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Base de données

1. Créer la base de données :
   ```sql
   CREATE DATABASE projectwebpeche CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Importer le fichier SQL :
   ```bash
   mysql -u root -p ProjectWebPeche < db/ProjectWebPeche.sql
   ```

> **Compte Admin** :  
> Email : `admin@webpeche.fr`  
> Mot de passe : `password123`

### 4. Lancer le projet

Placez le dossier dans votre serveur local (XAMPP, WAMP, Laragon…) et accédez à :

**`http://localhost/ProjectWebPeche-main/public/`**

---

## 📁 Structure principale

```
ProjectWebPeche/
├── public/                 # Point d'entrée + assets
├── src/
│   ├── controllers/        # Contrôleurs
│   ├── views/              # Vues
│   └── config/             # Configuration DB + View
├── db/
│   └── ProjectWebPeche.sql # Structure + données test
├── composer.json
└── README.md
```

---

## 👥 Auteurs

Ce projet a été réalisé par **4 étudiants** :

- Eden
- Andi
- Anthon
- Adem

---

## 📌 Perspectives d'amélioration

- Upload d'images pour les spots
- Système de favoris
- Géolocalisation de l'utilisateur
- Notifications
- Version mobile (PWA)
- Modération avancée

---

## 📄 Licence

Projet réalisé dans un cadre **éducatif**. Tous droits réservés.

---

**🐟 Bonne pêche et bonne découverte !**
