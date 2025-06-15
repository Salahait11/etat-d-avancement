<?php // src/Controller/ModuleController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Validator;
use App\Model\ModuleModel;
// On aura besoin de FiliereModel pour les formulaires plus tard
use App\Model\FiliereModel;

class ModuleController extends BaseController
{
    private ModuleModel $moduleModel;
    private FiliereModel $filiereModel; // Ajouté pour les formulaires

    public function __construct()
    {
        parent::__construct(); // Initialise $this->db
        $this->moduleModel = new ModuleModel();
        $this->filiereModel = new FiliereModel(); // Instancier FiliereModel aussi
    }
    
    /**
     * Méthode pour vérifier l'accès Admin ou Formateur et rediriger si non autorisé
     */
    private function requireAdminOrFormateur(): void
    {
        $this->requireLogin(); // D'abord vérifier s'il est connecté
        if (!$this->isAdmin() && !$this->isFormateur()) { // Vérifier s'il est admin ou formateur
             $this->setFlashMessage('error', 'Accès non autorisé. Cette section est réservée aux administrateurs et formateurs.');
             $this->redirect('/dashboard'); // Rediriger vers le tableau de bord
        }
    }

    /**
     * Affiche la liste de tous les modules.
     * Gère GET /modules
     */
    public function list(): void
    {
        $this->requireAdminOrFormateur();

        // Recherche et pagination
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->moduleModel->countAllWithFiliere($search);
        $offset = ($page - 1) * $limit;
        $modules = $this->moduleModel->findPagedWithFiliere($limit, $offset, $search);
        
        // Ajouter l'information is_used pour chaque module
        foreach ($modules as &$module) {
            $module['is_used'] = $this->moduleModel->isUsedInEtatAvancement((int)$module['id']);
        }
        
        $totalPages = (int)ceil($total / $limit);

        // Rendre la vue avec données de pagination
        $this->render('module/list', [
            'title' => 'Liste des Modules',
            'modules' => $modules,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un module.
     * Gère GET /modules/add
     */
    public function add(): void
    {
        $this->requireAdminOrFormateur();
        
        // Récupérer toutes les filières pour le menu déroulant
        $filieres = $this->filiereModel->findAll();
        
        // Initialiser les données du formulaire vides
        $formData = [
            'intitule' => '',
            'objectif' => '',
            'duree' => '',
            'id_filiere' => ''
        ];
        
        // Rendre la vue du formulaire d'ajout
        $this->render('module/add', [
            'title' => 'Ajouter un Module',
            'filieres' => $filieres,
            'formData' => $formData,
            'errors' => []
        ]);
    }
    
    /**
     * Traite la soumission du formulaire d'ajout d'un module.
     * Gère POST /modules/store
     */
    public function store(): void
    {
        $this->requireAdminOrFormateur();
        
        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/modules');
            return;
        }
        
        // Récupérer les données du formulaire
        $formData = [
            'intitule' => trim($_POST['intitule'] ?? ''),
            'objectif' => trim($_POST['objectif'] ?? ''),
            'duree' => trim($_POST['duree'] ?? ''),
            'id_filiere' => trim($_POST['id_filiere'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateModuleData($formData);
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $filieres = $this->filiereModel->findAll();
            $this->render('module/add', [
                'title' => 'Ajouter un Module',
                'filieres' => $filieres,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Convertir les types de données
        $formData['duree'] = (int)$formData['duree'];
        $formData['id_filiere'] = (int)$formData['id_filiere'];
        
        // Créer le module
        $moduleId = $this->moduleModel->create($formData);
        
        if ($moduleId) {
            $this->setFlashMessage('success', 'Module ajouté avec succès.');
            $this->redirect('/modules');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de l\'ajout du module.');
            $filieres = $this->filiereModel->findAll();
            $this->render('module/add', [
                'title' => 'Ajouter un Module',
                'filieres' => $filieres,
                'formData' => $formData,
                'errors' => ['db' => 'Erreur lors de l\'enregistrement dans la base de données.']
            ]);
        }
    }
    
    /**
     * Affiche le formulaire de modification d'un module.
     * Gère GET /modules/edit/{id}
     * 
     * @param int $id L'ID du module à modifier.
     */
    public function edit(int $id): void
    {
        $this->requireAdminOrFormateur();
        
        // Récupérer le module avec sa filière
        $module = $this->moduleModel->findByIdWithFiliere($id);
        
        // Vérifier si le module existe
        if (!$module) {
            $this->setFlashMessage('error', 'Module non trouvé.');
            $this->redirect('/modules');
            return;
        }
        
        // Récupérer toutes les filières pour le menu déroulant
        $filieres = $this->filiereModel->findAll();
        
        // Rendre la vue du formulaire de modification
        $this->render('module/edit', [
            'title' => 'Modifier le Module',
            'module' => $module,
            'filieres' => $filieres,
            'formData' => $module,
            'errors' => []
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de modification d'un module.
     * Gère POST /modules/update/{id}
     * 
     * @param int $id L'ID du module à mettre à jour.
     */
    public function update(int $id): void
    {
        $this->requireAdminOrFormateur();
        
        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/modules');
            return;
        }
        
        // Vérifier si le module existe
        $module = $this->moduleModel->findByIdWithFiliere($id);
        if (!$module) {
            $this->setFlashMessage('error', 'Module non trouvé.');
            $this->redirect('/modules');
            return;
        }
        
        // Récupérer les données du formulaire
        $formData = [
            'intitule' => trim($_POST['intitule'] ?? ''),
            'objectif' => trim($_POST['objectif'] ?? ''),
            'duree' => trim($_POST['duree'] ?? ''),
            'id_filiere' => trim($_POST['id_filiere'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateModuleData($formData);
        
        // S'il y a des erreurs, réafficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $filieres = $this->filiereModel->findAll();
            $this->render('module/edit', [
                'title' => 'Modifier le Module',
                'module' => $module,
                'filieres' => $filieres,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Convertir les types de données
        $formData['duree'] = (int)$formData['duree'];
        $formData['id_filiere'] = (int)$formData['id_filiere'];
        
        // Mettre à jour le module
        $success = $this->moduleModel->update($id, $formData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Module mis à jour avec succès.');
            $this->redirect('/modules');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour du module.');
            $filieres = $this->filiereModel->findAll();
            $this->render('module/edit', [
                'title' => 'Modifier le Module',
                'module' => $module,
                'filieres' => $filieres,
                'formData' => $formData,
                'errors' => ['db' => 'Erreur lors de la mise à jour dans la base de données.']
            ]);
        }
    }
    
    /**
     * Supprime un module.
     * Gère POST /modules/delete/{id}
     * 
     * @param int $id L'ID du module à supprimer.
     */
    public function delete(int $id): void
    {
        $this->requireAdminOrFormateur();
        
        // Vérifier que la méthode est bien POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
            $this->redirect('/modules');
            return;
        }
        
        // Récupérer le module pour avoir son nom avant suppression
        $module = $this->moduleModel->findByIdWithFiliere($id);
        if (!$module) {
            $this->setFlashMessage('error', "Tentative de suppression d'un module inexistant (ID: {$id}).");
            $this->redirect('/modules');
            return;
        }

        // Vérifier si le module est utilisé dans un état d'avancement
        if ($this->moduleModel->isUsedInEtatAvancement($id)) {
            $this->setFlashMessage('error', 'Impossible de supprimer ce module car il est utilisé dans un ou plusieurs états d\'avancement.');
            $this->redirect('/modules');
            return;
        }
        
        // Supprimer le module
        $success = $this->moduleModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'Module "' . htmlspecialchars($module['intitule']) . '" a été supprimé.');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la suppression du module (ID: ' . $id . ').');
        }
        
        $this->redirect('/modules');
    }
    
    /**
     * Valide les données d'un module.
     * 
     * @param array $data Les données à valider.
     * @return array Les erreurs de validation.
     */
    private function validateModuleData(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required('intitule', $data['intitule'])
            ->maxLength('intitule', $data['intitule'], 100)
            ->maxLength('objectif', $data['objectif'], 500)
            ->required('duree', $data['duree'])
            ->numeric('duree', $data['duree'])
            ->positive('duree', $data['duree'])
            ->required('id_filiere', $data['id_filiere'])
            ->numeric('id_filiere', $data['id_filiere'])
            ->positive('id_filiere', $data['id_filiere'])
            ->exists('id_filiere', $data['id_filiere'], fn(int $id): bool => (bool)$this->filiereModel->findById($id));
        return $validator->getErrors();
    }

    /**
     * Affiche la liste des modules
     */
    public function index(): void
    {
        $this->requireAdminOrFormateur();
        
        // Récupérer les modules avec leurs filières
        $modules = $this->moduleModel->findAllWithFiliere();
        
        // Ajouter l'information is_used pour chaque module
        foreach ($modules as &$module) {
            $module['is_used'] = $this->moduleModel->isUsedInEtatAvancement((int)$module['id']);
        }
        
        $this->render('module/list', [
            'title' => 'Liste des Modules',
            'modules' => $modules,
            'search' => $_GET['search'] ?? ''
        ]);
    }

}
