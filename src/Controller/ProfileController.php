<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Model\UtilisateurModel;
use App\Model\FormateurModel;

class ProfileController extends BaseController
{
    private UtilisateurModel $utilisateurModel;
    private FormateurModel $formateurModel;

    public function __construct()
    {
        parent::__construct();
        $this->utilisateurModel = new UtilisateurModel();
        $this->formateurModel = new FormateurModel();
    }

    /**
     * Affiche le profil de l'utilisateur connecté
     */
    public function index(): void
    {
        $this->requireLogin();
        
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            $this->redirect('/login');
            return;
        }

        // Récupérer les informations détaillées de l'utilisateur
        $userDetails = $this->utilisateurModel->findById($currentUser['id']);
        
        // Si l'utilisateur est un formateur, récupérer ses informations supplémentaires
        $formateurDetails = null;
        if ($this->isFormateur()) {
            $formateurDetails = $this->formateurModel->findByUserId($currentUser['id']);
        }

        $this->render('profile/index', [
            'title' => 'Mon Profil',
            'user' => $userDetails,
            'formateur' => $formateurDetails,
            'isAdmin' => $this->isAdmin(),
            'isFormateur' => $this->isFormateur()
        ]);
    }

    /**
     * Affiche le formulaire de modification du profil
     */
    public function edit(): void
    {
        $this->requireLogin();
        
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            $this->redirect('/login');
            return;
        }

        $userDetails = $this->utilisateurModel->findById($currentUser['id']);
        $formateurDetails = null;
        
        if ($this->isFormateur()) {
            $formateurDetails = $this->formateurModel->findByUserId($currentUser['id']);
        }

        $this->render('profile/edit', [
            'title' => 'Modifier mon profil',
            'user' => $userDetails,
            'formateur' => $formateurDetails,
            'errors' => $_SESSION['form_errors'] ?? [],
            'success' => $_SESSION['flash_messages']['success'] ?? null,
            'isFormateur' => $this->isFormateur(),
            'isAdmin' => $this->isAdmin()
        ]);
    }

    /**
     * Met à jour les informations du profil
     */
    public function update(): void
    {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("ProfileController::update - Méthode non autorisée: " . $_SERVER['REQUEST_METHOD']);
            $this->redirect('/profile');
            return;
        }

        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            error_log("ProfileController::update - Utilisateur non connecté");
            $this->redirect('/login');
            return;
        }

        error_log("ProfileController::update - Début de la mise à jour pour l'utilisateur ID: " . $currentUser['id']);

        // Données de base pour tous les utilisateurs
        $data = [
            'id' => $currentUser['id'],
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];

        error_log("ProfileController::update - Données de base: " . json_encode($data));

        // Gestion du mot de passe
        $ancienMotDePasse = trim($_POST['ancien_mot_de_passe'] ?? '');
        $nouveauMotDePasse = trim($_POST['mot_de_passe'] ?? '');
        $confirmationMotDePasse = trim($_POST['confirmation_mot_de_passe'] ?? '');

        if (!empty($nouveauMotDePasse)) {
            error_log("ProfileController::update - Tentative de mise à jour du mot de passe");
            
            // Vérifier que l'ancien mot de passe est correct
            $user = $this->utilisateurModel->findById($currentUser['id']);
            if (!$user) {
                error_log("ProfileController::update - Utilisateur non trouvé");
                $_SESSION['form_errors'] = ['ancien_mot_de_passe' => 'Erreur lors de la vérification du mot de passe'];
                $this->redirect('/profile/edit');
                return;
            }

            // Vérifier le mot de passe
            if (!isset($user['mot_de_passe']) || !password_verify($ancienMotDePasse, $user['mot_de_passe'])) {
                error_log("ProfileController::update - Ancien mot de passe incorrect");
                $_SESSION['form_errors'] = ['ancien_mot_de_passe' => 'L\'ancien mot de passe est incorrect'];
                $this->redirect('/profile/edit');
                return;
            }
            
            if (strlen($nouveauMotDePasse) < 6) {
                error_log("ProfileController::update - Nouveau mot de passe trop court: " . strlen($nouveauMotDePasse) . " caractères");
                $_SESSION['form_errors'] = ['mot_de_passe' => 'Le mot de passe doit contenir au moins 6 caractères'];
                $this->redirect('/profile/edit');
                return;
            }

            if ($nouveauMotDePasse !== $confirmationMotDePasse) {
                error_log("ProfileController::update - Les nouveaux mots de passe ne correspondent pas");
                $_SESSION['form_errors'] = ['mot_de_passe' => 'Les mots de passe ne correspondent pas'];
                $this->redirect('/profile/edit');
                return;
            }

            $data['mot_de_passe'] = $nouveauMotDePasse;
            error_log("ProfileController::update - Mot de passe validé et ajouté aux données");
        }

        // Validation des données
        $errors = $this->validateProfileData($data);
        
        if (!empty($errors)) {
            error_log("ProfileController::update - Erreurs de validation: " . json_encode($errors));
            $_SESSION['form_errors'] = $errors;
            $this->redirect('/profile/edit');
            return;
        }

        // Mise à jour des informations de base
        $success = $this->utilisateurModel->update($data['id'], $data);
        
        if ($success) {
            error_log("ProfileController::update - Mise à jour réussie pour l'utilisateur ID: " . $data['id']);
            
            // Mettre à jour la session avec les nouvelles informations
            $_SESSION['user']['nom'] = $data['nom'];
            $_SESSION['user']['prenom'] = $data['prenom'];
            $_SESSION['user']['email'] = $data['email'];
            
            // Si c'est un formateur, mettre à jour la spécialité
            if ($this->isFormateur() && isset($_POST['specialite'])) {
                $formateurData = [
                    'specialite' => trim($_POST['specialite'])
                ];
                error_log("ProfileController::update - Mise à jour de la spécialité du formateur: " . json_encode($formateurData));
                $this->formateurModel->updateByUserId($currentUser['id'], $formateurData);
            }
            
            $this->setFlashMessage('success', 'Profil mis à jour avec succès');
        } else {
            error_log("ProfileController::update - Échec de la mise à jour pour l'utilisateur ID: " . $data['id']);
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour du profil');
        }

        $this->redirect('/profile');
    }

    /**
     * Valide les données du profil
     */
    private function validateProfileData(array $data): array
    {
        $errors = [];
        error_log("ProfileController::validateProfileData - Début de la validation des données: " . json_encode($data));

        // Validation du nom
        if (empty($data['nom'])) {
            $errors['nom'] = 'Le nom est obligatoire';
            error_log("ProfileController::validateProfileData - Erreur: nom vide");
        } elseif (strlen($data['nom']) > 50) {
            $errors['nom'] = 'Le nom ne doit pas dépasser 50 caractères';
            error_log("ProfileController::validateProfileData - Erreur: nom trop long (" . strlen($data['nom']) . " caractères)");
        }

        // Validation du prénom
        if (empty($data['prenom'])) {
            $errors['prenom'] = 'Le prénom est obligatoire';
            error_log("ProfileController::validateProfileData - Erreur: prénom vide");
        } elseif (strlen($data['prenom']) > 50) {
            $errors['prenom'] = 'Le prénom ne doit pas dépasser 50 caractères';
            error_log("ProfileController::validateProfileData - Erreur: prénom trop long (" . strlen($data['prenom']) . " caractères)");
        }

        // Validation de l'email
        if (empty($data['email'])) {
            $errors['email'] = 'L\'email est obligatoire';
            error_log("ProfileController::validateProfileData - Erreur: email vide");
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email n\'est pas valide';
            error_log("ProfileController::validateProfileData - Erreur: email invalide (" . $data['email'] . ")");
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'L\'email ne doit pas dépasser 100 caractères';
            error_log("ProfileController::validateProfileData - Erreur: email trop long (" . strlen($data['email']) . " caractères)");
        }

        // Validation du mot de passe si fourni
        if (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
            if (strlen($data['mot_de_passe']) < 6) {
                $errors['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères';
                error_log("ProfileController::validateProfileData - Erreur: mot de passe trop court (" . strlen($data['mot_de_passe']) . " caractères)");
            }
        }

        error_log("ProfileController::validateProfileData - Fin de la validation. Erreurs trouvées: " . json_encode($errors));
        return $errors;
    }
} 