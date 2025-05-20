# 🎓 Système de Suivi des États d'Avancement

[![PHP Version](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7+-green.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Stable-brightgreen.svg)]()

## 📝 Description
Système de gestion des états d'avancement des formations, permettant aux formateurs de suivre et documenter la progression des modules de formation. L'application utilise une architecture MVC (Modèle-Vue-Contrôleur) et implémente les bonnes pratiques de développement PHP moderne.

## ✨ Fonctionnalités Principales

### 👥 Gestion des Utilisateurs
- 🔐 Authentification sécurisée avec hachage des mots de passe
- 👮 Gestion des rôles (Administrateur, Formateur)
- 👤 Profils utilisateurs personnalisables
- 🔑 Système de récupération de mot de passe

### 📚 Suivi des Modules
- 📝 Création et gestion des modules de formation
- 🔗 Association avec les filières
- 📊 Suivi du taux de réalisation
- 📜 Historique des modifications

### 📈 États d'Avancement
- ✍️ Création d'états d'avancement détaillés
- 🎯 Suivi des objectifs pédagogiques
- 📚 Gestion des moyens didactiques
- 📋 Évaluation des stratégies pédagogiques
- 📄 Export des données en PDF

### 📊 Tableau de Bord
- 📈 Vue d'ensemble des statistiques
- ⏱️ Suivi en temps réel des progrès
- 📊 Graphiques et indicateurs de performance
- 🔍 Filtres et recherche avancée

## 🛠️ Prérequis Techniques

### 🖥️ Serveur
- 🐘 PHP 8.0 ou supérieur
  - Extensions requises : PDO, MySQL, JSON, mbstring
  - Configuration recommandée : `memory_limit = 256M`
- 🐬 MySQL 5.7 ou supérieur
  - InnoDB comme moteur de stockage
  - UTF-8 comme encodage par défaut
- 🌐 Serveur web (Apache/Nginx)
  - Module mod_rewrite activé (Apache)
  - Configuration SSL recommandée

### 🛠️ Outils de Développement
- 📦 Composer 2.0 ou supérieur
- 🔄 Git pour le contrôle de version
- 📦 Node.js et NPM (optionnel, pour les assets)

## 📥 Installation Détaillée

1. 🔧 Préparation de l'environnement :
```bash
# Installation des dépendances système
sudo apt-get update
sudo apt-get install php8.0 php8.0-mysql php8.0-mbstring php8.0-xml

# Installation de Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

2. 📦 Installation du projet :
```bash
# Cloner le dépôt
git clone [URL_DU_REPO]
cd etat-d-avancement

# Installer les dépendances
composer install --no-dev

# Configurer les permissions
chmod -R 755 public/
chmod -R 777 storage/
```

3. 🗄️ Configuration de la base de données :
```sql
-- Création de la base de données
CREATE DATABASE etat_avancement CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Création de l'utilisateur
CREATE USER 'etat_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON etat_avancement.* TO 'etat_user'@'localhost';
FLUSH PRIVILEGES;
```

4. ⚙️ Configuration de l'application :
```env
# .env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://votre-domaine.com

DB_HOST=localhost
DB_NAME=etat_avancement
DB_USER=etat_user
DB_PASS=votre_mot_de_passe

MAIL_HOST=smtp.votre-serveur.com
MAIL_PORT=587
MAIL_USERNAME=votre-email
MAIL_PASSWORD=votre-mot-de-passe
```

## 🏗️ Architecture du Projet

### 📁 Structure MVC
```
src/
├── Controller/           # Logique métier
│   ├── AuthController.php
│   ├── ModuleController.php
│   └── EtatAvancementController.php
├── Model/               # Accès aux données
│   ├── User.php
│   ├── Module.php
│   └── EtatAvancement.php
├── View/                # Templates
│   ├── auth/
│   ├── modules/
│   └── etats/
└── Core/               # Classes de base
    ├── Database.php
    ├── Router.php
    └── Session.php
```

### 💻 Exemple de Code

#### 🎮 Contrôleur
```php
class ModuleController extends BaseController
{
    public function index()
    {
        $modules = $this->moduleModel->findAll();
        return $this->view('modules/index', ['modules' => $modules]);
    }
}
```

#### 📦 Modèle
```php
class Module extends BaseModel
{
    public function findAll(): array
    {
        $sql = "SELECT m.*, f.nom as filiere_nom 
                FROM module m 
                LEFT JOIN filiere f ON m.id_filiere = f.id";
        return $this->db->query($sql)->fetchAll();
    }
}
```

## 🔒 Sécurité

### 🔐 Authentification
- 🔑 Utilisation de `password_hash()` et `password_verify()`
- 🔄 Sessions sécurisées avec régénération d'ID
- 🛡️ Protection contre les attaques par force brute

### 🛡️ Protection des Données
- ✅ Validation des entrées utilisateur
- 🚫 Échappement des sorties HTML
- 🛡️ Protection CSRF sur tous les formulaires
- 🔒 Headers de sécurité HTTP

## 🔧 Maintenance

### 💾 Sauvegardes
```bash
# Script de sauvegarde automatique
#!/bin/bash
mysqldump -u etat_user -p etat_avancement > backup_$(date +%Y%m%d).sql
```

### 📊 Surveillance
- 📝 Logs d'erreurs dans `storage/logs/`
- 📈 Monitoring des performances
- ⚠️ Alertes en cas d'erreur critique

## 📡 API Documentation

### 🔌 Endpoints Principaux
```json
{
  "modules": {
    "GET /api/modules": "Liste tous les modules",
    "POST /api/modules": "Crée un nouveau module",
    "GET /api/modules/{id}": "Récupère un module spécifique"
  },
  "etats": {
    "GET /api/etats": "Liste tous les états d'avancement",
    "POST /api/etats": "Crée un nouvel état"
  }
}
```

## 🤝 Support et Contribution

### 🐛 Rapporter un Bug
1. 🔍 Vérifier les issues existantes
2. 📝 Créer une nouvelle issue avec :
   - Description du problème
   - Étapes pour reproduire
   - Comportement attendu
   - Captures d'écran si pertinent

### 👥 Contribuer
1. 🍴 Fork le projet
2. 🌿 Créer une branche (`git checkout -b feature/amelioration`)
3. 💾 Commit les changements (`git commit -am 'Ajout d'une fonctionnalité'`)
4. 📤 Push la branche (`git push origin feature/amelioration`)
5. 🔄 Créer une Pull Request

## 📄 Licence
Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Auteurs
- [Salah Ait hammou] - Développeur principal



---
*Dernière mise à jour : 2025-05-20 01:16* 📅
