<?php // src/Controller/AuthController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController; // Utilise le contrôleur de base
use App\Model\UtilisateurModel; // Utilise le modèle utilisateur
use PDO; // Requis car on l'injecte dans le constructeur

class AuthController extends BaseController // Hérite de BaseController
{
    private UtilisateurModel $userModel;

    public function __construct(PDO $pdo)
    {
        // Instancie le modèle en lui passant la connexion DB reçue
        $this->userModel = new UtilisateurModel($pdo);
    }

    /**
     * Gère la page d'accueil : redirige si connecté, sinon affiche login.
     */
    public function handleHomepage(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirect('/dashboard'); // Redirige vers une future page dashboard
        } else {
            $this->showLoginForm(); // Affiche le formulaire si non connecté
        }
    }

    /**
     * Affiche le formulaire de connexion (GET /login)
     */
    public function showLoginForm(): void
    {
        if ($this->isUserLoggedIn()) {
             $this->redirect('/dashboard'); // Redirige si déjà connecté
        }
        // Prépare les données pour la vue (juste le titre ici)
        $viewData = ['title' => 'Connexion'];
        // Récupère un éventuel message d'erreur flash
        $viewData['error'] = $this->getFlashMessage('error');

        $this->render('auth/login', $viewData); // Rend la vue auth/login.php (dans le layout par défaut)
    }

    /**
     * Traite la soumission du formulaire de connexion (POST /login)
     */
    public function processLogin(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirect('/dashboard'); // Ne devrait pas arriver si déjà connecté, mais sécurité
        }

        $email = $this->input('email', '', 'post');
        $password = $this->input('password', '', 'post');

        // Validation très simple (à améliorer plus tard)
        if (empty($email) || empty($password)) {
            $this->setFlashMessage('error', 'Email et mot de passe sont requis.');
            $this->redirect('/login'); // Redirige vers le formulaire
            return; // Important d'arrêter ici
        }

        // Tentative de vérification via le Modèle
        $user = $this->userModel->verifyLogin($email, $password);

        if ($user) {
            // Succès !
            session_regenerate_id(true); // Sécurité : régénérer l'ID de session
            // Stocker les infos essentielles en session
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email']
                // On ajoutera les rôles plus tard
            ];

            $this->setFlashMessage('success', 'Connexion réussie ! Bienvenue ' . htmlspecialchars($user['prenom']) . '.');
            $this->redirect('/dashboard'); // Rediriger vers la page protégée

        } else {
            // Échec
            $this->setFlashMessage('error', 'Identifiants invalides ou compte inactif.');
            $this->redirect('/login'); // Retour au formulaire
        }
    }

    /**
     * Déconnecte l'utilisateur (GET ou POST /logout)
     */
    public function logout(): void
    {
        $_SESSION = []; // Vide le tableau de session
        if (ini_get("session.use_cookies")) { // Supprime le cookie de session
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy(); // Détruit la session côté serveur
        $this->redirect('/login'); // Redirige vers la page de connexion
    }
}