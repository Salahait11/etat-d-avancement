# Gestion des Écoles

Système de gestion des écoles permettant de gérer les filières, les modules, les apprenants, les formateurs et les évaluations.

## Fonctionnalités

- Gestion des filières de formation
- Gestion des modules et leurs contenus
- Gestion des apprenants et des formateurs
- Gestion des sessions de formation
- Gestion des évaluations et des compétences
- Suivi des objectifs pédagogiques
- Statistiques et rapports

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Composer (pour la gestion des dépendances)
- Serveur web (Apache, Nginx, etc.)

## Installation

1. Cloner le dépôt :
```bash
git clone https://github.com/votre-username/gestion_ecoles.git
cd gestion_ecoles
```

2. Installer les dépendances :
```bash
composer install
```

3. Configurer l'environnement :
```bash
cp .env.example .env
```
Puis modifiez le fichier `.env` avec vos paramètres de base de données et autres configurations.

4. Créer la base de données :
```bash
mysql -u root -p
CREATE DATABASE gestion_ecoles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. Importer le schéma de la base de données :
```bash
mysql -u root -p gestion_ecoles < database/schema.sql
```

## Configuration

### Base de données

Modifiez les paramètres de connexion dans le fichier `.env` :

```env
DB_HOST=localhost
DB_NAME=gestion_ecoles
DB_USER=votre_utilisateur
DB_PASS=votre_mot_de_passe
DB_CHARSET=utf8mb4
```

### Application

Configurez les paramètres de l'application dans le fichier `.env` :

```env
APP_NAME="Gestion des Écoles"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost
APP_KEY=votre_clé_secrète
```

## Structure du projet

```
gestion_ecoles/
├── config/             # Fichiers de configuration
├── models/             # Modèles de données
├── controllers/        # Contrôleurs
├── views/             # Vues
├── public/            # Fichiers publics
│   ├── css/          # Feuilles de style
│   ├── js/           # Scripts JavaScript
│   └── images/       # Images
├── logs/              # Fichiers de logs
├── tests/             # Tests unitaires
├── vendor/            # Dépendances
├── .env              # Variables d'environnement
├── .env.example      # Exemple de variables d'environnement
├── .gitignore        # Fichiers ignorés par Git
├── composer.json     # Configuration Composer
└── README.md         # Documentation
```

## Utilisation

1. Démarrer le serveur de développement :
```bash
php -S localhost:8000 -t public
```

2. Accéder à l'application :
```
http://localhost:8000
```

## Sécurité

- Les mots de passe sont hashés avec `password_hash()`
- Protection contre les injections SQL avec PDO
- Protection CSRF sur les formulaires
- Validation des données côté serveur
- Gestion sécurisée des sessions

## Contribution

1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Créer une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## Support

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub. 