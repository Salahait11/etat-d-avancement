<?php // src/Controller/UtilisateurController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Model\UtilisateurModel;
use App\Model\RoleModel;

class UtilisateurController extends BaseController
{
    private UtilisateurModel $utilisateurModel;
    private RoleModel $roleModel;

    public function __construct()
    {
        parent::__construct();
        $this->utilisateurModel = new UtilisateurModel();
        $this->roleModel = new RoleModel();
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
     * Liste tous les utilisateurs
     */
    public function index(): void
    {
        $this->requireAdmin();
        
        $utilisateurs = $this->utilisateurModel->findAll();
        
        // Pour chaque utilisateur, récupérer ses rôles
        foreach ($utilisateurs as &$utilisateur) {
            $utilisateur['roles'] = $this->utilisateurModel->getUserRoleNames((int)$utilisateur['id']);
        }
        
        $this->render('utilisateur/list', [
            'title' => 'Gestion des Utilisateurs',
            'utilisateurs' => $utilisateurs
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un utilisateur
     */
    public function create(): void
    {
        $this->requireAdmin();
        
        // Récupérer tous les rôles disponibles
        $roles = $this->roleModel->findAll();
        
        $this->render('utilisateur/create', [
            'title' => 'Ajouter un Utilisateur',
            'roles' => $roles,
            'formData' => [
                'nom' => '',
                'prenom' => '',
                'email' => '',
                'statut' => 'actif',
                'roles' => []
            ],
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'ajout d'un utilisateur
     */
    public function store(): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/utilisateurs');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
            'confirmation_mot_de_passe' => $_POST['confirmation_mot_de_passe'] ?? '',
            'statut' => $_POST['statut'] ?? 'actif',
            'roles' => $_POST['roles'] ?? []
        ];
        
        // Valider les données
        $errors = $this->validateUserData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $roles = $this->roleModel->findAll();
            $this->render('utilisateur/create', [
                'title' => 'Ajouter un Utilisateur',
                'roles' => $roles,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Créer l'utilisateur
        $userId = $this->utilisateurModel->create([
            'nom' => $formData['nom'],
            'prenom' => $formData['prenom'],
            'email' => $formData['email'],
            'mot_de_passe' => $formData['mot_de_passe'],
            'statut' => $formData['statut']
        ]);
        
        if (!$userId) {
            $this->setFlashMessage('error', 'Erreur lors de la création de l\'utilisateur. L\'email est peut-être déjà utilisé.');
            $roles = $this->roleModel->findAll();
            $this->render('utilisateur/create', [
                'title' => 'Ajouter un Utilisateur',
                'roles' => $roles,
                'formData' => $formData,
                'errors' => ['email' => 'Cet email est déjà utilisé.']
            ]);
            return;
        }
        
        // Attribuer les rôles sélectionnés
        if (!empty($formData['roles'])) {
            $this->utilisateurModel->updateRoles($userId, array_map('intval', $formData['roles']));
        }
        
        $this->setFlashMessage('success', 'Utilisateur créé avec succès.');
        $this->redirect('/utilisateurs');
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     */
    public function edit(int $id): void
    {
        $this->requireAdmin();
        
        $utilisateur = $this->utilisateurModel->findById($id);
        
        if (!$utilisateur) {
            $this->setFlashMessage('error', 'Utilisateur non trouvé.');
            $this->redirect('/utilisateurs');
            return;
        }
        
        // Récupérer les rôles de l'utilisateur
        $userRoleIds = $this->utilisateurModel->getUserRoleIds($id);
        
        // Récupérer tous les rôles disponibles
        $roles = $this->roleModel->findAll();
        
        $formData = $utilisateur;
        $formData['roles'] = $userRoleIds;
        
        $this->render('utilisateur/edit', [
            'title' => 'Modifier l\'Utilisateur',
            'utilisateur' => $utilisateur,
            'roles' => $roles,
            'formData' => $formData,
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'édition d'un utilisateur
     */
    public function update(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/utilisateurs');
            return;
        }
        
        $utilisateur = $this->utilisateurModel->findById($id);
        
        if (!$utilisateur) {
            $this->setFlashMessage('error', 'Utilisateur non trouvé.');
            $this->redirect('/utilisateurs');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
            'confirmation_mot_de_passe' => $_POST['confirmation_mot_de_passe'] ?? '',
            'statut' => $_POST['statut'] ?? 'actif',
            'roles' => $_POST['roles'] ?? []
        ];
        
        // Valider les données (sans exiger de mot de passe)
        $errors = $this->validateUserData($formData, false);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $roles = $this->roleModel->findAll();
            $userRoleIds = $this->utilisateurModel->getUserRoleIds($id);
            $this->render('utilisateur/edit', [
                'title' => 'Modifier l\'Utilisateur',
                'utilisateur' => $utilisateur,
                'roles' => $roles,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Préparer les données à mettre à jour
        $updateData = [
            'nom' => $formData['nom'],
            'prenom' => $formData['prenom'],
            'email' => $formData['email'],
            'statut' => $formData['statut']
        ];
        
        // Ajouter le mot de passe uniquement s'il est fourni
        if (!empty($formData['mot_de_passe'])) {
            $updateData['mot_de_passe'] = $formData['mot_de_passe'];
        }
        
        // Mettre à jour l'utilisateur
        $success = $this->utilisateurModel->update($id, $updateData);
        
        if (!$success) {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour de l\'utilisateur. L\'email est peut-être déjà utilisé.');
            $roles = $this->roleModel->findAll();
            $this->render('utilisateur/edit', [
                'title' => 'Modifier l\'Utilisateur',
                'utilisateur' => $utilisateur,
                'roles' => $roles,
                'formData' => $formData,
                'errors' => ['email' => 'Cet email est déjà utilisé.']
            ]);
            return;
        }
        
        // Mettre à jour les rôles
        $this->utilisateurModel->updateRoles($id, array_map('intval', $formData['roles']));
        
        $this->setFlashMessage('success', 'Utilisateur mis à jour avec succès.');
        $this->redirect('/utilisateurs');
    }

    /**
     * Supprime un utilisateur
     */
    public function delete(int $id): void
    {
        $this->requireAdmin();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
            $this->redirect('/utilisateurs');
            return;
        }
        
        $utilisateur = $this->utilisateurModel->findById($id);
        
        if (!$utilisateur) {
            $this->setFlashMessage('error', 'Utilisateur non trouvé.');
            $this->redirect('/utilisateurs');
            return;
        }
        
        // Empêcher la suppression de son propre compte
        if ((int)$utilisateur['id'] === (int)($_SESSION['user']['id'] ?? 0)) {
            $this->setFlashMessage('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('/utilisateurs');
            return;
        }
        
        // Supprimer l'utilisateur
        $success = $this->utilisateurModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la suppression de l\'utilisateur.');
        }
        
        $this->redirect('/utilisateurs');
    }

    /**
     * Valide les données utilisateur
     * 
     * @param array $data Données à valider
     * @param bool $requirePassword Indique si le mot de passe est requis (true pour création, false pour mise à jour)
     * @return array Tableau des erreurs
     */
    private function validateUserData(array $data, bool $requirePassword = true): array
    {
        $errors = [];
        
        // Valider le nom
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est requis.';
        } elseif (strlen($data['nom']) > 50) {
            $errors['nom'] = 'Le nom ne doit pas dépasser 50 caractères.';
        }
        
        // Valider le prénom
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est requis.';
        } elseif (strlen($data['prenom']) > 50) {
            $errors['prenom'] = 'Le prénom ne doit pas dépasser 50 caractères.';
        }
        
        // Valider l'email
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est requis.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email n\'est pas valide.';
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'L\'email ne doit pas dépasser 100 caractères.';
        }
        
        // Valider le mot de passe
        if ($requirePassword) {
            if (empty($data['mot_de_passe'])) {
                $errors['mot_de_passe'] = 'Le mot de passe est requis.';
            } elseif (strlen($data['mot_de_passe']) < 6) {
                $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            }
            
            if ($data['mot_de_passe'] !== $data['confirmation_mot_de_passe']) {
                $errors['confirmation_mot_de_passe'] = 'Les mots de passe ne correspondent pas.';
            }
        } else {
            // Si le mot de passe n'est pas requis mais qu'il est fourni
            if (!empty($data['mot_de_passe'])) {
                if (strlen($data['mot_de_passe']) < 6) {
                    $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
                }
                
                if ($data['mot_de_passe'] !== $data['confirmation_mot_de_passe']) {
                    $errors['confirmation_mot_de_passe'] = 'Les mots de passe ne correspondent pas.';
                }
            }
        }
        
        // Valider le statut
        if (!in_array($data['statut'], ['actif', 'inactif'])) {
            $errors['statut'] = 'Le statut doit être "actif" ou "inactif".';
        }
        
        return $errors;
    }
}
