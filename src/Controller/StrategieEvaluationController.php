<?php // src/Controller/StrategieEvaluationController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Model\StrategieEvaluationModel;
use App\Core\Validator;

class StrategieEvaluationController extends BaseController
{
    private StrategieEvaluationModel $strategieModel;

    public function __construct()
    {
        parent::__construct();
        $this->strategieModel = new StrategieEvaluationModel();
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
     * Liste toutes les stratégies d'évaluation
     */
    public function index(): void
    {
        $this->requireAdmin();
        $strategies = $this->strategieModel->findAll();
        
        // Ajouter l'information is_used pour chaque stratégie
        foreach ($strategies as &$strategie) {
            $strategie['is_used'] = $this->strategieModel->isUsedInEtatsAvancement((int)$strategie['id']);
        }
        
        $this->render('strategie_evaluation/list', [
            'title' => 'Liste des Stratégies d\'Évaluation',
            'strategies' => $strategies
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'une stratégie d'évaluation
     */
    public function add(): void
    {
        $this->requireAdmin();
        
        $this->render('strategie_evaluation/add', [
            'title' => 'Ajouter une Stratégie d\'Évaluation',
            'formData' => [
                'strategie' => '',
                'description' => ''
            ],
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'ajout d'une stratégie d'évaluation
     */
    public function store(): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/strategies-evaluation');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'strategie' => trim($_POST['strategie'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateStrategieData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('strategie_evaluation/add', [
                'title' => 'Ajouter une Stratégie d\'Évaluation',
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Créer la stratégie d'évaluation
        $result = $this->strategieModel->create($formData);
        
        if ($result) {
            $this->setFlashMessage('success', 'Stratégie d\'évaluation créée avec succès.');
            $this->redirect('/strategies-evaluation');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la création de la stratégie d\'évaluation.');
            $this->render('strategie_evaluation/add', [
                'title' => 'Ajouter une Stratégie d\'Évaluation',
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la création de la stratégie d\'évaluation.']
            ]);
        }
    }

    /**
     * Affiche le formulaire d'édition d'une stratégie d'évaluation
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();
        
        $strategie = $this->strategieModel->findById($id);
        
        if (!$strategie) {
            $this->setFlashMessage('error', 'Stratégie d\'évaluation non trouvée.');
            $this->redirect('/strategies-evaluation');
            return;
        }
        
        $this->render('strategie_evaluation/edit', [
            'title' => 'Modifier la Stratégie d\'Évaluation',
            'strategie' => $strategie,
            'formData' => $strategie,
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'édition d'une stratégie d'évaluation
     */
    public function update(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/strategies-evaluation');
            return;
        }
        
        $strategie = $this->strategieModel->findById($id);
        
        if (!$strategie) {
            $this->setFlashMessage('error', 'Stratégie d\'évaluation non trouvée.');
            $this->redirect('/strategies-evaluation');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'strategie' => trim($_POST['strategie'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateStrategieData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('strategie_evaluation/edit', [
                'title' => 'Modifier la Stratégie d\'Évaluation',
                'strategie' => $strategie,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Mettre à jour la stratégie d'évaluation
        $success = $this->strategieModel->update($id, $formData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Stratégie d\'évaluation mise à jour avec succès.');
            $this->redirect('/strategies-evaluation');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour de la stratégie d\'évaluation.');
            $this->render('strategie_evaluation/edit', [
                'title' => 'Modifier la Stratégie d\'Évaluation',
                'strategie' => $strategie,
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la mise à jour de la stratégie d\'évaluation.']
            ]);
        }
    }

    /**
     * Supprime une stratégie d'évaluation
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
            $this->redirect('/strategies-evaluation');
            return;
        }
        
        $strategie = $this->strategieModel->findById($id);
        
        if (!$strategie) {
            $this->setFlashMessage('error', 'Stratégie d\'évaluation non trouvée.');
            $this->redirect('/strategies-evaluation');
            return;
        }
        
        // Supprimer la stratégie d'évaluation
        $success = $this->strategieModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'Stratégie d\'évaluation supprimée avec succès.');
        } else {
            $this->setFlashMessage('error', 'Impossible de supprimer cette stratégie d\'évaluation car elle est utilisée dans un ou plusieurs états d\'avancement.');
        }
        
        $this->redirect('/strategies-evaluation');
    }

    /**
     * Valide les données d'une stratégie d'évaluation
     * 
     * @param array $data Données à valider
     * @return array Tableau des erreurs
     */
    private function validateStrategieData(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required('strategie', $data['strategie'])
            ->maxLength('strategie', $data['strategie'], 255)
            ->maxLength('description', $data['description'] ?? '', 1000);
        return $validator->getErrors();
    }
}
