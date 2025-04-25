<?php // src/Controller/ContenuSeanceController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Model\ContenuSeanceModel;
use App\Core\Validator;

class ContenuSeanceController extends BaseController
{
    private ContenuSeanceModel $contenuModel;

    public function __construct()
    {
        parent::__construct();
        $this->contenuModel = new ContenuSeanceModel();
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
     * Liste tous les contenus de séance
     */
    public function index(): void
    {
        $this->requireAdmin();

        // Recherche et pagination
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $total = $this->contenuModel->countAll($search);
        $offset = ($page - 1) * $limit;
        $contenus = $this->contenuModel->findPaged($limit, $offset, $search);
        $totalPages = (int)ceil($total / $limit);

        $this->render('contenu_seance/list', [
            'title' => 'Contenus de Séance',
            'contenus' => $contenus,
            'search' => $search,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un contenu de séance
     */
    public function add(): void
    {
        $this->requireAdmin();
        
        $this->render('contenu_seance/add', [
            'title' => 'Ajouter un Contenu de Séance',
            'formData' => [
                'contenu' => ''
            ],
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'ajout d'un contenu de séance
     */
    public function store(): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/contenus-seance');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'contenu' => trim($_POST['contenu'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateContenuData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('contenu_seance/add', [
                'title' => 'Ajouter un Contenu de Séance',
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Créer le contenu de séance
        $result = $this->contenuModel->create($formData);
        
        if ($result) {
            $this->setFlashMessage('success', 'Contenu de séance créé avec succès.');
            $this->redirect('/contenus-seance');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la création du contenu de séance.');
            $this->render('contenu_seance/add', [
                'title' => 'Ajouter un Contenu de Séance',
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la création du contenu de séance.']
            ]);
        }
    }

    /**
     * Affiche le formulaire d'édition d'un contenu de séance
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();
        
        $contenu = $this->contenuModel->findById($id);
        
        if (!$contenu) {
            $this->setFlashMessage('error', 'Contenu de séance non trouvé.');
            $this->redirect('/contenus-seance');
            return;
        }
        
        $this->render('contenu_seance/edit', [
            'title' => 'Modifier le Contenu de Séance',
            'contenu' => $contenu,
            'formData' => $contenu,
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'édition d'un contenu de séance
     */
    public function update(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/contenus-seance');
            return;
        }
        
        $contenu = $this->contenuModel->findById($id);
        
        if (!$contenu) {
            $this->setFlashMessage('error', 'Contenu de séance non trouvé.');
            $this->redirect('/contenus-seance');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'contenu' => trim($_POST['contenu'] ?? '')
        ];
        
        // Valider les données
        $errors = $this->validateContenuData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $this->render('contenu_seance/edit', [
                'title' => 'Modifier le Contenu de Séance',
                'contenu' => $contenu,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Mettre à jour le contenu de séance
        $success = $this->contenuModel->update($id, $formData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Contenu de séance mis à jour avec succès.');
            $this->redirect('/contenus-seance');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour du contenu de séance.');
            $this->render('contenu_seance/edit', [
                'title' => 'Modifier le Contenu de Séance',
                'contenu' => $contenu,
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la mise à jour du contenu de séance.']
            ]);
        }
    }

    /**
     * Supprime un contenu de séance
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
            $this->redirect('/contenus-seance');
            return;
        }
        
        $contenu = $this->contenuModel->findById($id);
        
        if (!$contenu) {
            $this->setFlashMessage('error', 'Contenu de séance non trouvé.');
            $this->redirect('/contenus-seance');
            return;
        }
        
        // Supprimer le contenu de séance
        $success = $this->contenuModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'Contenu de séance supprimé avec succès.');
        } else {
            $this->setFlashMessage('error', 'Impossible de supprimer ce contenu de séance car il est utilisé dans un ou plusieurs états d\'avancement.');
        }
        
        $this->redirect('/contenus-seance');
    }

    /**
     * Valide les données d'un contenu de séance
     * 
     * @param array $data Données à valider
     * @return array Tableau des erreurs
     */
    private function validateContenuData(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required('contenu', $data['contenu'])
            ->maxLength('contenu', $data['contenu'], 255);
        return $validator->getErrors();
    }
}
