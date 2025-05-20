<?php // src/Controller/FiliereController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Validator;
use App\Model\FiliereModel;

class FiliereController extends BaseController
{
    private FiliereModel $filiereModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->filiereModel = new FiliereModel();
    }
    
    /**
     * Méthode pour vérifier l'accès Admin et rediriger si non autorisé
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin(); // D'abord vérifier s'il est connecté
        if (!$this->isAdmin()) { // Ensuite vérifier s'il est admin
             $this->setFlashMessage('error', 'Accès non autorisé. Cette section est réservée aux administrateurs.');
             $this->redirect('/dashboard'); // Rediriger vers le tableau de bord
        }
    }
    
    /**
     * Affiche la liste des filières
     */
    public function index(): void
    {
        $this->requireAdmin();
        
        $filieres = $this->filiereModel->findAll();
        
        $this->render('filiere/index', [
            'title' => 'Liste des Filières',
            'filieres' => $filieres
        ]);
    }
    
    /**
     * Affiche les détails d'une filière
     */
    public function show(int $id): void
    {
        $this->requireAdmin();
        $filiere = $this->filiereModel->findById($id);
        if (!$filiere) {
            $this->setFlashMessage('error', 'Filière non trouvée.');
            $this->redirect('/filieres');
            return;
        }
        $this->render('filiere/show', [
            'title' => 'Détails de la Filière',
            'filiere' => $filiere
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'une filière
     */
    public function create(): void
    {
        $this->requireAdmin();
        
        $this->render('filiere/create', [
            'title' => 'Ajouter une Filière',
            'formData' => [
                'nom_filiere' => '',
                'description' => '',
                'niveau' => '',
                'duree_totale' => ''
            ]
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de création
     */
    public function store(): void
    {
        $this->requireAdmin();
        
        // Récupération des données du formulaire
        $formData = [
            'nom_filiere' => $this->input('nom_filiere', '', 'post'),
            'description' => $this->input('description', '', 'post'),
            'niveau' => $this->input('niveau', '', 'post'),
            'duree_totale' => $this->input('duree_totale', 0, 'post')
        ];
        
        // Validation des données
        $errors = $this->validateFiliereData($formData);
        
        if (!empty($errors)) {
            // Afficher à nouveau le formulaire avec les erreurs
            $this->render('filiere/create', [
                'title' => 'Ajouter une Filière',
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Vérifier si le nom de filière existe déjà
        if ($this->filiereModel->existsByName($formData['nom_filiere'])) {
            $this->render('filiere/create', [
                'title' => 'Ajouter une Filière',
                'formData' => $formData,
                'errors' => ['nom_filiere' => 'Ce nom de filière existe déjà.']
            ]);
            return;
        }
        
        // Créer la filière
        $filiereId = $this->filiereModel->create($formData);
        
        if ($filiereId) {
            $this->setFlashMessage('success', 'La filière a été créée avec succès.');
            $this->redirect('/filieres');
        } else {
            $this->render('filiere/create', [
                'title' => 'Ajouter une Filière',
                'formData' => $formData,
                'errors' => ['general' => 'Une erreur est survenue lors de la création de la filière.']
            ]);
        }
    }
    
    /**
     * Affiche le formulaire de modification d'une filière
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();
        
        $filiere = $this->filiereModel->findById($id);
        
        if (!$filiere) {
            $this->setFlashMessage('error', 'Filière non trouvée.');
            $this->redirect('/filieres');
            return;
        }
        
        $this->render('filiere/edit', [
            'title' => 'Modifier la Filière',
            'filiere' => $filiere,
            'formData' => $filiere
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de modification
     */
    public function update(int $id): void
    {
        $this->requireAdmin();
        
        $filiere = $this->filiereModel->findById($id);
        
        if (!$filiere) {
            $this->setFlashMessage('error', 'Filière non trouvée.');
            $this->redirect('/filieres');
            return;
        }
        
        // Récupération des données du formulaire
        $formData = [
            'nom_filiere' => $this->input('nom_filiere', '', 'post'),
            'description' => $this->input('description', '', 'post'),
            'niveau' => $this->input('niveau', '', 'post'),
            'duree_totale' => $this->input('duree_totale', 0, 'post')
        ];
        
        // Validation des données
        $errors = $this->validateFiliereData($formData);
        
        if (!empty($errors)) {
            // Afficher à nouveau le formulaire avec les erreurs
            $this->render('filiere/edit', [
                'title' => 'Modifier la Filière',
                'filiere' => $filiere,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Vérifier si le nom de filière existe déjà (en excluant l'ID actuel)
        if ($this->filiereModel->existsByName($formData['nom_filiere'], $id)) {
            $this->render('filiere/edit', [
                'title' => 'Modifier la Filière',
                'filiere' => $filiere,
                'formData' => $formData,
                'errors' => ['nom_filiere' => 'Ce nom de filière existe déjà.']
            ]);
            return;
        }
        
        // Mettre à jour la filière
        $success = $this->filiereModel->update($id, $formData);
        
        if ($success) {
            $this->setFlashMessage('success', 'La filière a été mise à jour avec succès.');
            $this->redirect('/filieres');
        } else {
            $this->render('filiere/edit', [
                'title' => 'Modifier la Filière',
                'filiere' => $filiere,
                'formData' => $formData,
                'errors' => ['general' => 'Une erreur est survenue lors de la mise à jour de la filière.']
            ]);
        }
    }
    
    /**
     * Supprime une filière
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();
        
        $filiere = $this->filiereModel->findById($id);
        
        if (!$filiere) {
            $this->setFlashMessage('error', 'Filière non trouvée.');
            $this->redirect('/filieres');
            return;
        }
        
        // Tenter de supprimer la filière
        $success = $this->filiereModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'La filière a été supprimée avec succès.');
        } else {
            $this->setFlashMessage('error', 'Impossible de supprimer cette filière car elle est associée à des modules.');
        }
        
        $this->redirect('/filieres');
    }
    
    /**
     * Affiche le formulaire pour modifier une filière existante.
     * Gère la route GET /filieres/edit/{id}
     *
     * @param int $id L'ID de la filière à modifier (vient de l'URL).
     */
    public function showEditForm(int $id): void
    {
        $this->requireLogin();
        // Vérification de rôle si nécessaire

        // 1. Récupérer les données actuelles de la filière depuis le modèle
        $filiere = $this->filiereModel->findById($id);

        // 2. Vérifier si la filière existe
        if ($filiere === false) {
            $this->setFlashMessage('error', "La filière avec l'ID {$id} n'a pas été trouvée.");
            $this->redirect('/filieres'); // Rediriger vers la liste
            return;
        }

        // 3. Rendre la vue du formulaire d'édition en passant les données actuelles
        $this->render('filiere/edit', [
            'title' => 'Modifier la Filière : ' . htmlspecialchars($filiere['nom_filiere']),
            'filiere' => $filiere // Les données pour pré-remplir le formulaire
            // On pourrait aussi passer ici $errors et $formData si la soumission échoue
        ]);
    }

    /**
     * Traite la soumission du formulaire de modification de filière.
     * Gère la route POST /filieres/edit/{id}
     *
     * @param int $id L'ID de la filière à modifier (vient de l'URL).
     */
    public function processEditForm(int $id): void
    {
        $this->requireLogin();
        // Vérification de rôle si nécessaire

        // 0. Vérifier si la filière existe (sécurité, évite de traiter un ID invalide)
        $existingFiliere = $this->filiereModel->findById($id);
        if ($existingFiliere === false) {
             $this->setFlashMessage('error', "Tentative de modification d'une filière inexistante (ID: {$id}).");
             $this->redirect('/filieres');
             return;
        }

        // 1. Vérifier que la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             $this->redirect('/filieres/edit/' . $id);
             return;
        }

        // 2. Récupérer les données soumises
        $data = [
            'nom_filiere' => $this->input('nom_filiere', '', 'post'),
            'description' => $this->input('description', null, 'post'),
            'niveau' => $this->input('niveau', '', 'post'),
            'duree_totale_str' => $this->input('duree_totale', '', 'post')
        ];

        // 3. Validation (similaire à l'ajout, mais contexte différent si besoin)
        $errors = [];
         if (empty($data['nom_filiere'])) { $errors['nom_filiere'] = 'Le nom est obligatoire.'; }
         // Ajouter d'autres validations...
         if (empty($data['niveau'])) { $errors['niveau'] = 'Le niveau est obligatoire.'; }
         $duree = 0;
         if (empty($data['duree_totale_str'])) { $errors['duree_totale'] = 'La durée est obligatoire.'; }
         elseif (!ctype_digit($data['duree_totale_str'])) { $errors['duree_totale'] = 'La durée doit être un nombre.'; }
         else { $duree = (int)$data['duree_totale_str']; if ($duree <= 0) { $errors['duree_totale'] = 'La durée doit être positive.'; }}

         // 4. Si erreurs, ré-afficher le formulaire d'édition
         if (!empty($errors)) {
             $this->setFlashMessage('error', 'Veuillez corriger les erreurs.');
             // Rendre la vue edit, en repassant les données erronées ($data)
             // et les données originales ($existingFiliere) si besoin pour le titre par ex.
             $this->render('filiere/edit', [
                 'title' => 'Modifier Filière - Erreurs',
                 'errors' => $errors,
                 'filiere' => array_merge($existingFiliere, ['id' => $id]), // Utilise les données originales comme base
                 'formData' => $data // Prépollution avec les données soumises erronées
             ]);
             return;
         }

        // 5. Préparer les données pour la mise à jour du modèle
         $updateData = [
             'nom_filiere' => $data['nom_filiere'],
             'description' => $data['description'],
             'niveau'      => $data['niveau'],
             'duree_totale'=> $duree
         ];

        // 6. Tenter la mise à jour via le Modèle
        $success = $this->filiereModel->update($id, $updateData);

        // 7. Gérer le résultat
        if ($success) {
            $this->setFlashMessage('success', 'Filière "' . htmlspecialchars($updateData['nom_filiere']) . '" mise à jour avec succès.');
            $this->redirect('/filieres'); // Rediriger vers la liste
        } else {
             $this->setFlashMessage('error', 'Erreur lors de la mise à jour de la filière. Le nom existe peut-être déjà ou une erreur serveur est survenue.');
              // Ré-afficher le formulaire d'édition avec les données soumises
              $this->render('filiere/edit', [
                 'title' => 'Modifier Filière - Erreur DB',
                 'filiere' => array_merge($existingFiliere, ['id' => $id]),
                 'formData' => $data
             ]);
        }
    }

    /**
     * Supprime une filière.
     * Gère la route POST /filieres/delete/{id}
     *
     * @param int $id L'ID de la filière à supprimer.
     */
    public function deleteFiliere(int $id): void
    {
        $this->requireAdmin();

        // 1. Vérifier que la méthode est POST (sécurité importante pour les suppressions)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
             $this->redirect('/filieres');
             return;
        }

        // 2. Vérifier si la filière existe avant de tenter de supprimer (optionnel mais propre)
         $filiere = $this->filiereModel->findById($id);
         if ($filiere === false) {
             $this->setFlashMessage('error', "Tentative de suppression d'une filière inexistante (ID: {$id}).");
             $this->redirect('/filieres');
             return;
         }

        // 3. Tenter la suppression via le Modèle
        $success = $this->filiereModel->delete($id);

        // 4. Gérer le résultat
        if ($success) {
            $this->setFlashMessage('success', 'Filière "' . htmlspecialchars($filiere['nom_filiere']) . '" (ID: ' . $id . ') a été supprimée.');
        } else {
            // Une erreur s'est produite (peut-être une contrainte FK non gérée par CASCADE?)
            $this->setFlashMessage('error', 'Erreur lors de la suppression de la filière (ID: ' . $id . '). Elle est peut-être liée à d\'autres données.');
        }

        // 5. Rediriger vers la liste dans tous les cas (succès ou échec de suppression)
        $this->redirect('/filieres');
    }

    /**
     * Valide les données d'une filière
     */
    private function validateFiliereData(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required('nom_filiere', $data['nom_filiere'])
            ->maxLength('nom_filiere', $data['nom_filiere'], 100)
            ->required('niveau', $data['niveau'])
            ->maxLength('niveau', $data['niveau'], 50)
            ->required('duree_totale', $data['duree_totale'])
            ->numeric('duree_totale', $data['duree_totale'])
            ->positive('duree_totale', $data['duree_totale']);
        return $validator->getErrors();
    }
}
