# ENSAF Alumni Portal

## Description
Site web dynamique pour l'École Nationale des Sciences Appliquées de Fès (ENSAF), développé avec PHP et MySQL. Cette plateforme connecte les étudiants actuels, les anciens élèves, les professeurs et l'administration dans un écosystème digital interactif.

## Fonctionnalités Principales

### 1. Système d'Authentification Multi-Utilisateurs
- Espace Étudiants: Accès aux cours, notes, et ressources pédagogiques
- Espace Alumni: Réseau professionnel et opportunités de carrière
- Espace Enseignants: Gestion des cours et communication avec les étudiants
- Espace Administration: Gestion globale du site et des utilisateurs

### 2. Gestion de Contenu Dynamique
- **Actualités**: Dernières informations concernant l'école et ses activités
- **Événements**: Calendrier des événements à venir (conférences, workshops, etc.)
- **Opportunités**: Offres de stages, emplois et projets professionnels
- **Formations**: Présentation détaillée des filières et cursus

### 3. Système de Contribution et Modération
- Les utilisateurs peuvent soumettre des événements et opportunités
- Workflow d'approbation: tout contenu soumis nécessite l'approbation d'un administrateur
- Notifications automatiques pour les administrateurs et contributeurs

### 4. Fonctionnalités Interactives
- Filtrage dynamique des contenus (alumni par promotion/filière, événements par type)
- Formulaire de contact avec validation
- Partage sur réseaux sociaux
- Galerie photos et vidéos des événements passés

## Technologies Utilisées
- **Frontend**: HTML5, CSS3, JavaScript (avec animations et transitions)
- **Backend**: PHP 8.0+
- **Base de données**: MySQL
- **Librairies**: jQuery, FontAwesome, Bootstrap (composants)
- **Sécurité**: Protection CSRF, validation des entrées, sanitization des données

## Structure de la Base de Données
- `users`: Stockage des utilisateurs et leurs informations
- `departments`: Filières et spécialités de l'école
- `news`: Actualités de l'école
- `events`: Événements à venir et passés
- `opportunities`: Offres de stages et d'emploi
- `alumni`: Informations spécifiques sur les anciens étudiants
- `testimonials`: Témoignages d'alumni
- `gallery`: Photos des événements et activités

## Structure des Fichiers
- `index.php`: Page d'accueil du site
- `includes/`: Composants réutilisables (header, footer, navigation)
- `admin/`: Interface d'administration
- `config/`: Configuration de la base de données et constantes
- `assets/`: Ressources statiques (CSS, JS, images)
- `functions/`: Fonctions utilitaires PHP
- `pages/`: Pages principales du site (about, contact, etc.)
- `login.php`: Système d'authentification
- `profile.php`: Gestion de profil utilisateur
- `events.php`: Liste et détails des événements
- `opportunities.php`: Liste des opportunités professionnelles
- `alumni.php`: Répertoire des anciens étudiants

## Installation et Déploiement
1. Cloner le dépôt: `git clone [URL_du_repo]`
2. Importer la base de données: Utiliser le fichier `setup_database.php`
3. Configurer la connexion à la base de données dans `config.php`
4. Assurer que le serveur web a les permissions d'écriture sur les dossiers de téléchargement
5. Accéder au site via le navigateur

## Utilisation Administrateur
1. Se connecter avec les identifiants admin (créés lors de l'installation)
2. Accéder au panneau d'administration via `admin/index.php`
3. Gérer les utilisateurs, modérer le contenu et approuver les soumissions

## Développement Futur
- Implémentation d'un système de messagerie interne
- Module de mentorat entre alumni et étudiants
- API pour applications mobiles
- Intégration avec les systèmes d'information de l'école

## Sécurité
- Toutes les entrées utilisateurs sont validées et nettoyées
- Mots de passe hashés avec algorithmes sécurisés
- Protection contre injections SQL, XSS et CSRF
- Journalisation des activités sensibles

## Équipe de Développement
Ce projet a été réalisé par:
- **Rehab**
- **Walid**
- **Yahya**

## Licence
Tous droits réservés. Ce code est la propriété de l'ENSAF et de ses développeurs. 