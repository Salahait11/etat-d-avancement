# Projet État d'Avancement

## Description
Cette application MVC en PHP permet de gérer l'avancement pédagogique via plusieurs entités : modules, contenus de séance, moyens didactiques, stratégies d'évaluation, etc. Chaque entité dispose d'un CRUD complet et de contrôles d'accès (admin/formateur).

## Installation
1. Cloner le dépôt dans `c:/xampp/htdocs/etat-d-avancement`
2. Créer une base MySQL `gestion_ecoles` et configurer `config/database.php`
3. `composer install`
4. Vérifier `config/app.php` : définir `BASE_URL` sur `http://localhost/etat-d-avancement`
5. Activer le module `mod_rewrite` d’Apache
6. Lancer le serveur Apache & accéder à `http://localhost/etat-d-avancement`

## Structure du projet
```
/etat-d-avancement
├─ /public          # Point d'entrée, routage FastRoute
├─ /src
│  ├─ /Controller   # Logique de chaque entité
│  ├─ /Model        # Accès à la base (PDO)
│  └─ /View         # Vues PHP + Bootstrap
├─ /config          # config app & DB
├─ /vendor          # dépendances Composer
└─ .htaccess        # redirection vers public/
```

## Principales fonctionnalités
- CRUD Modules avec jointure Filière
- CRUD Contenus de séance
- CRUD Moyens didactiques
- CRUD Stratégies d'évaluation
- Recherche & pagination sur listes
- Contrôle d'accès Admin/Formateur
- Flash messages et validation serveur

## Routes clés
| Méthode | URL                          | Action controller                    |
| ------- | ---------------------------- | ------------------------------------ |
| GET     | `/modules`                   | ModuleController::list               |
| GET     | `/modules/add`               | ModuleController::add                |
| POST    | `/modules/store`             | ModuleController::store              |
| GET     | `/modules/edit/{id}`         | ModuleController::edit               |
| POST    | `/modules/update/{id}`       | ModuleController::update             |
| POST    | `/modules/delete/{id}`       | ModuleController::delete             |
| GET     | `/contenus-seance`           | ContenuSeanceController::index       |
| …       | …                            | …                                    |

## Tests recommandés
- PHPUnit pour models & controllers
- Tests fonctionnels (Selenium ou Laravel Dusk)

## Améliorations futures
- Protection CSRF sur tous les formulaires
- Log d’erreurs (Monolog)
- Migrations (Phinx)
- Internationalisation (i18n)
- API RESTful + documentation Swagger

---
*Généré automatiquement le 2025-04-25*
