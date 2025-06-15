<?php // src/Controller/MoyenDidactiqueController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Model\MoyenDidactiqueModel;
use App\Core\Validator;

class MoyenDidactiqueController extends BaseController
{
    private MoyenDidactiqueModel $moyenModel;

    public function __construct()
    {
        parent::__construct();
        $this->moyenModel = new MoyenDidactiqueModel();
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
     * Liste tous les moyens didactiques
     */
    public function index(): void
    {
        $this->requireAdmin();
        $moyens = $this->moyenModel->findAll();
        
        // Ajouter l'information is_used pour chaque moyen
        foreach ($moyens as &$moyen) {
            $moyen['is_used'] = $this->moyenModel->isUsedInEtatsAvancement((int)$moyen['id']);
        }
        
        $this->render('moyen_didactique/list', [
            'title' => 'Liste des Moyens Didactiques',
            'moyens' => $moyens
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un moyen didactique
     */
    public function add(): void
    {
        $this->requireAdmin();
        
        $this->render('moyen_didactique/add', [
            'title' => 'Ajouter un Moyen Didactique',
            'formData' => [
                'moyen' => '',
                'description' => ''
            ],
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'ajout d'un moyen didactique
     */
    public function store(): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/moyens-didactiques');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'moyen' => trim($_POST['moyen'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateMoyenData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('moyen_didactique/add', [
                'title' => 'Ajouter un Moyen Didactique',
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Créer le moyen didactique
        $result = $this->moyenModel->create($formData);
        
        if ($result) {
            $this->setFlashMessage('success', 'Moyen didactique créé avec succès.');
            $this->redirect('/moyens-didactiques');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la création du moyen didactique.');
            $this->render('moyen_didactique/add', [
                'title' => 'Ajouter un Moyen Didactique',
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la création du moyen didactique.']
            ]);
        }
    }

    /**
     * Affiche le formulaire d'édition d'un moyen didactique
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();
        
        $moyen = $this->moyenModel->findById($id);
        
        if (!$moyen) {
            $this->setFlashMessage('error', 'Moyen didactique non trouvé.');
            $this->redirect('/moyens-didactiques');
            return;
        }
        
        $this->render('moyen_didactique/edit', [
            'title' => 'Modifier le Moyen Didactique',
            'moyen' => $moyen,
            'formData' => $moyen,
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'édition d'un moyen didactique
     */
    public function update(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/moyens-didactiques');
            return;
        }
        
        $moyen = $this->moyenModel->findById($id);
        
        if (!$moyen) {
            $this->setFlashMessage('error', 'Moyen didactique non trouvé.');
            $this->redirect('/moyens-didactiques');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'moyen' => trim($_POST['moyen'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateMoyenData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('moyen_didactique/edit', [
                'title' => 'Modifier le Moyen Didactique',
                'moyen' => $moyen,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Mettre à jour le moyen didactique
        $success = $this->moyenModel->update($id, $formData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Moyen didactique mis à jour avec succès.');
            $this->redirect('/moyens-didactiques');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour du moyen didactique.');
            $this->render('moyen_didactique/edit', [
                'title' => 'Modifier le Moyen Didactique',
                'moyen' => $moyen,
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la mise à jour du moyen didactique.']
            ]);
        }
    }

    /**
     * Supprime un moyen didactique
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
            $this->redirect('/moyens-didactiques');
            return;
        }
        
        $moyen = $this->moyenModel->findById($id);
        
        if (!$moyen) {
            $this->setFlashMessage('error', 'Moyen didactique non trouvé.');
            $this->redirect('/moyens-didactiques');
            return;
        }
        
        // Supprimer le moyen didactique
        $success = $this->moyenModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'Moyen didactique supprimé avec succès.');
        } else {
            $this->setFlashMessage('error', 'Impossible de supprimer ce moyen didactique car il est utilisé dans un ou plusieurs états d\'avancement.');
        }
        
        $this->redirect('/moyens-didactiques');
    }

    /**
     * Valide les données d'un moyen didactique
     * 
     * @param array $data Données à valider
     * @return array Tableau des erreurs
     */
    private function validateMoyenData(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required('moyen', $data['moyen'])
            ->maxLength('moyen', $data['moyen'], 255)
            ->maxLength('description', $data['description'] ?? '', 1000);
        return $validator->getErrors();
    }
}
