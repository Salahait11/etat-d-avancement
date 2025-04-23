<?php // src/Controller/AuthController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController; // Utilise le contrôleur de base
use App\Model\UtilisateurModel; // Utilise le modèle utilisateur
use PDO; // Requis pour l'injection de dépendance PDO

class AuthController extends BaseController // Étend BaseController
{
    private UtilisateurModel $userModel;

    // Reçoit la connexion PDO depuis index.php
    public function __construct(PDO $pdo)
    {
        // Instancie le modèle en lui passant la connexion DB
        $this->userModel = new UtilisateurModel($pdo);
    }

    public function handleHomepage(): void
    {
        // À l'intérieur de la méthode du contrôleur, l'appel à $this->isUserLoggedIn() est VALIDE
        if ($this->isUserLoggedIn()) {
            $this->redirect('/dashboard');
        } else {
            // Réutilise la méthode existante pour afficher le formulaire
            $this->showLoginForm();
        }
    }
    public function showLoginForm(): void
    {
         // (Optionnel : légère redondance avec handleHomepage, on pourrait même faire
         // en sorte que GET /login appelle handleHomepage si on veut que / et /login soient identiques)
        if ($this->isUserLoggedIn()) {
             $this->redirect('/dashboard');
        }

        $errorMessage = $this->getFlashMessage('error');
        $this->render('auth/login', ['error' => $errorMessage]); // Ne pas spécifier de layout ici si render le fait par défaut
                                                               // S'assurer que la méthode render ajoute bien isLoggedIn etc.
                                                               // même si on n'utilise pas de layout explicite ici.
                                                               // Ou alors, spécifier le layout :
                                                               // $this->render('auth/login', ['error' => $errorMessage], 'layout');

    }

    /**
     * Traite la soumission du formulaire de connexion (Gère POST /login)
     */
    public function processLogin(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirect('/dashboard');
        }

        // 1. Récupérer email/password du POST (validation basique)
        $email = $this->input('email', '', 'post');
        $password = $this->input('password', '', 'post');

        // Validation très basique (à améliorer !)
        if (empty($email) || empty($password)) {
            $this->setFlashMessage('error', 'Email et mot de passe sont requis.');
            $this->redirect('/login');
            return;
        }

        // 2. Tenter la vérification via le Modèle
        $user = $this->userModel->verifyLogin($email, $password);

        // 3. Vérifier le résultat
        if ($user) {
            // Succès !
            // a) Régénérer l'ID de session (sécurité)
            session_regenerate_id(true);

            // b) Stocker les infos essentielles en session (PAS le mot de passe !)
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email']
                // Ajouter les rôles plus tard si nécessaire
            ];
            $_SESSION['logged_in'] = true; // Indicateur simple

            // c) Message flash de succès (optionnel)
            $this->setFlashMessage('success', 'Connexion réussie ! Bienvenue ' . htmlspecialchars($user['prenom']) . '.');

            // d) Rediriger vers une zone protégée
            $this->redirect('/dashboard');

        } else {
            // Échec
            $this->setFlashMessage('error', 'Identifiants invalides ou compte inactif.');
            $this->redirect('/login');
        }
    }

    /**
     * Déconnecte l'utilisateur (Gère GET /logout)
     */
    public function logout(): void
    {
        // 1. Vider la session
        $_SESSION = [];

        // 2. Supprimer le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 3. Détruire la session
        session_destroy();

        // 4. Rediriger vers la page de connexion
        $this->redirect('/login');
    }
}