<?php // src/Controller/FiliereController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController; // Hérite des méthodes render, redirect, requireLogin etc.
use App\Model\FiliereModel; // Utilise le modèle Filiere
use PDO; // Requis pour l'injection de dépendance

class FiliereController extends BaseController
{
    private FiliereModel $filiereModel;

    // Le constructeur reçoit la connexion PDO et instancie le modèle
    public function __construct(PDO $pdo)
    {
        $this->filiereModel = new FiliereModel($pdo);
    }

    /**
     * Affiche la liste de toutes les filières.
     * Gère la route GET /filieres
     */
    public function list(): void
    {
        // 1. Sécurité : Vérifier si l'utilisateur est connecté
        $this->requireLogin();
        // !! Ajouter ici une vérification de RÔLE si nécessaire (ex: seul admin voit la liste)
        // if (!$this->hasRole('admin')) { $this->redirect('/dashboard'); /* Ou afficher erreur 403 */ }

        // 2. Récupérer les données via le Modèle
        $filieres = $this->filiereModel->findAll();

        // 3. Rendre la Vue en lui passant les données
        $this->render(
            'filiere/list', // Chemin vers le fichier de vue (sans .php)
            [
                'title' => 'Liste des Filières', // Données pour la vue
                'filieres' => $filieres
            ]
            // Utilise le layout par défaut ('layout.php')
        );
    }

    /**
     * Affiche le formulaire pour ajouter une nouvelle filière.
     * Gère la route GET /filieres/add
     */
    public function showAddForm(): void
    {
        $this->requireLogin();
        // !! Ajouter vérification de rôle (ex: seul admin peut ajouter)

        $this->render('filiere/add', ['title' => 'Ajouter une Filière']);
    }

    /**
     * Traite la soumission du formulaire d'ajout de filière.
     * Gère la route POST /filieres/add
     */
    public function processAddForm(): void
    {
        $this->requireLogin();
        // !! Ajouter vérification de rôle

        // 1. Vérifier que la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Normalement géré par le routeur, mais double sécurité
             $this->redirect('/filieres/add');
             return;
        }

        // 2. Récupérer les données du formulaire (avec nettoyage basique)
        //    Une validation plus robuste est NÉCESSAIRE dans une vraie application.
        $data = [
            'nom_filiere' => $this->input('nom_filiere', '', 'post'),
            'description' => $this->input('description', null, 'post'), // Peut être null/vide
            'niveau' => $this->input('niveau', '', 'post'),
            'duree_totale' => $this->input('duree_totale', 0, 'post')
        ];

        // 3. Validation (Exemple très simple - À AMÉLIORER !)
        $errors = [];
        if (empty($data['nom_filiere'])) {
            $errors['nom_filiere'] = 'Le nom de la filière est requis.';
        }
        if (empty($data['niveau'])) {
             $errors['niveau'] = 'Le niveau est requis.';
        }
        if (!is_numeric($data['duree_totale']) || (int)$data['duree_totale'] <= 0) {
             $errors['duree_totale'] = 'La durée totale doit être un nombre positif.';
        } else {
            // Assurer que c'est bien un entier pour le modèle
            $data['duree_totale'] = (int) $data['duree_totale'];
        }
        // Ajouter ici d'autres validations (longueur max, format, etc.)

        // 4. Si erreurs de validation, ré-afficher le formulaire avec les erreurs et les données saisies
        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->render('filiere/add', [
                'title' => 'Ajouter une Filière',
                'errors' => $errors,
                'formData' => $data // Renvoyer les données saisies pour pré-remplir
            ]);
            return; // Arrêter l'exécution ici
        }

        // 5. Si validation OK, tenter de créer via le Modèle
        $newId = $this->filiereModel->create($data);

        // 6. Gérer le résultat
        if ($newId) {
            // Succès ! Message flash et redirection vers la liste
            $this->setFlashMessage('success', 'Filière "' . htmlspecialchars($data['nom_filiere']) . '" ajoutée avec succès !');
            $this->redirect('/filieres');
        } else {
            // Échec de l'insertion (ex: nom dupliqué, erreur DB)
             // Le modèle a déjà loggué l'erreur technique.
             $this->setFlashMessage('error', "Erreur lors de l'ajout de la filière. Le nom existe peut-être déjà.");
             // Ré-afficher le formulaire avec les données pour correction
             $this->render('filiere/add', [
                 'title' => 'Ajouter une Filière',
                 'formData' => $data, // Garder les données saisies
                 'db_error' => true // Indicateur optionnel d'erreur DB
             ]);
        }
    }

    // --- Méthodes pour EDIT et DELETE à ajouter ici ---
    // showEditForm(int $id)
    // processEditForm(int $id)
    // deleteFiliere(int $id)

}