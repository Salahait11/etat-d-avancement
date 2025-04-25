<?php // src/Controller/ObjectifPedagogiqueController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Model\ObjectifPedagogiqueModel;

class ObjectifPedagogiqueController extends BaseController
{
    private ObjectifPedagogiqueModel $objectifModel;

    public function __construct()
    {
        parent::__construct();
        $this->objectifModel = new ObjectifPedagogiqueModel();
    }

    /**
     * Vérifie que l'utilisateur est admin, sinon redirige
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        
        if (!$this->isAdmin()) {
            $this->setFlashMessage('error', 'Accès refusé. Vous devez être administrateur pour accéder à cette page.');
            $this->redirect('/dashboard');
            exit;
        }
    }

    /**
     * Liste tous les objectifs pédagogiques
     */
    public function index(): void
    {
        $this->requireAdmin();
        
        $objectifs = $this->objectifModel->findAll();
        
        $this->render('objectif_pedagogique/list', [
            'title' => 'Objectifs Pédagogiques',
            'objectifs' => $objectifs
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un objectif pédagogique
     */
    public function add(): void
    {
        $this->requireAdmin();
        
        $this->render('objectif_pedagogique/add', [
            'title' => 'Ajouter un Objectif Pédagogique',
            'formData' => [
                'objectif' => '',
                'description' => ''
            ],
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'ajout d'un objectif pédagogique
     */
    public function store(): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/objectifs-pedagogiques');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'objectif' => trim($_POST['objectif'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateObjectifData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('objectif_pedagogique/add', [
                'title' => 'Ajouter un Objectif Pédagogique',
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Créer l'objectif pédagogique
        $result = $this->objectifModel->create($formData);
        
        if ($result) {
            $this->setFlashMessage('success', 'Objectif pédagogique créé avec succès.');
            $this->redirect('/objectifs-pedagogiques');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la création de l\'objectif pédagogique.');
            $this->render('objectif_pedagogique/add', [
                'title' => 'Ajouter un Objectif Pédagogique',
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la création de l\'objectif pédagogique.']
            ]);
        }
    }

    /**
     * Affiche le formulaire d'édition d'un objectif pédagogique
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();
        
        $objectif = $this->objectifModel->findById($id);
        
        if (!$objectif) {
            $this->setFlashMessage('error', 'Objectif pédagogique non trouvé.');
            $this->redirect('/objectifs-pedagogiques');
            return;
        }
        
        $this->render('objectif_pedagogique/edit', [
            'title' => 'Modifier l\'Objectif Pédagogique',
            'objectif' => $objectif,
            'formData' => $objectif,
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'édition d'un objectif pédagogique
     */
    public function update(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/objectifs-pedagogiques');
            return;
        }
        
        $objectif = $this->objectifModel->findById($id);
        
        if (!$objectif) {
            $this->setFlashMessage('error', 'Objectif pédagogique non trouvé.');
            $this->redirect('/objectifs-pedagogiques');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'objectif' => trim($_POST['objectif'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateObjectifData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('objectif_pedagogique/edit', [
                'title' => 'Modifier l\'Objectif Pédagogique',
                'objectif' => $objectif,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Mettre à jour l'objectif pédagogique
        $success = $this->objectifModel->update($id, $formData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Objectif pédagogique mis à jour avec succès.');
            $this->redirect('/objectifs-pedagogiques');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour de l\'objectif pédagogique.');
            $this->render('objectif_pedagogique/edit', [
                'title' => 'Modifier l\'Objectif Pédagogique',
                'objectif' => $objectif,
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la mise à jour de l\'objectif pédagogique.']
            ]);
        }
    }

    /**
     * Supprime un objectif pédagogique
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
            $this->redirect('/objectifs-pedagogiques');
            return;
        }
        
        $objectif = $this->objectifModel->findById($id);
        
        if (!$objectif) {
            $this->setFlashMessage('error', 'Objectif pédagogique non trouvé.');
            $this->redirect('/objectifs-pedagogiques');
            return;
        }
        
        // Supprimer l'objectif pédagogique
        $success = $this->objectifModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'Objectif pédagogique supprimé avec succès.');
        } else {
            $this->setFlashMessage('error', 'Impossible de supprimer cet objectif pédagogique car il est utilisé dans un ou plusieurs états d\'avancement.');
        }
        
        $this->redirect('/objectifs-pedagogiques');
    }

    /**
     * Valide les données d'un objectif pédagogique
     * 
     * @param array $data Données à valider
     * @return array Tableau des erreurs
     */
    private function validateObjectifData(array $data): array
    {
        $errors = [];
        
        // Valider l'objectif
        if (empty($data['objectif'])) {
            $errors['objectif'] = 'L\'objectif est requis.';
        } elseif (strlen($data['objectif']) > 255) {
            $errors['objectif'] = 'L\'objectif ne doit pas dépasser 255 caractères.';
        }
        
        // La description est optionnelle, mais si elle est fournie, elle ne doit pas être trop longue
        if (isset($data['description']) && strlen($data['description']) > 1000) {
            $errors['description'] = 'La description ne doit pas dépasser 1000 caractères.';
        }
        
        return $errors;
    }
}
