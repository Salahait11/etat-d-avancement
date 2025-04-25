<?php // src/Controller/AuthController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Validator;
use App\Model\UtilisateurModel; // Utilise le modèle
use App\Model\FiliereModel;
use App\Model\ModuleModel;
use App\Model\EtatAvancementModel; // Pas besoin de use PDO ici

class AuthController extends BaseController // Hérite de BaseController
{
    private UtilisateurModel $userModel;

    // Le constructeur de BaseController est appelé automatiquement
    // et initialise $this->db (l'instance Database).
    // Nous instancions UtilisateurModel ici.
    public function __construct()
    {
        parent::__construct(); // Appelle le constructeur parent (qui initialise $this->db)
        $this->userModel = new UtilisateurModel(); // Le modèle Utilisateur utilisera aussi Database::getInstance()
    }

    /**
     * Gère la page d'accueil : redirige si connecté, sinon affiche login.
     * (Cette méthode peut être déplacée dans HomeController si on préfère)
     */
    public function handleHomepage(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirect('/dashboard');
        } else {
            // Si l'accueil doit être le login, appeler showLoginForm
             $this->showLoginForm();
             // Si l'accueil est une page publique gérée par HomeController:
             // (new HomeController())->index(); // Ou utiliser un meilleur système de routage/injection
        }
    }

    /**
     * Affiche le formulaire de connexion (GET /login)
     */
    public function showLoginForm(): void
    {
        if ($this->isUserLoggedIn()) {
             $this->redirect('/dashboard');
        }
        $viewData = [
            'title' => 'Connexion',
            // Récupère un éventuel message flash d'erreur pour l'afficher
            'flashError' => $this->getFlashMessage('error') // Optionnel, le layout le gère aussi
        ];
        $this->render('auth/login', $viewData); // Utilise la vue src/View/auth/login.php
    }

    /**
     * Traite la soumission du formulaire de connexion (POST /login)
     */
    public function processLogin(): void
    {
        if ($this->isUserLoggedIn()) {
            $this->redirect('/dashboard');
            return;
        }

        $email = $this->input('email', '', 'post');
        $password = $this->input('password', '', 'post');

        $validator = new Validator();
        $validator->required('email', $email)->email('email', $email)->required('password', $password);
        $errors = $validator->getErrors();
        if (!empty($errors)) {
            foreach ($errors as $message) {
                $this->setFlashMessage('error', $message);
            }
            $this->redirect('/login');
            return;
        }

        // Vérification des identifiants
        $user = $this->userModel->verifyLogin($email, $password);

        if ($user) {
            // Succès de la connexion
            session_regenerate_id(true); // Sécurité contre les attaques de fixation de session
            $_SESSION['logged_in'] = true;
            
            // --- Récupération et Stockage des Rôles ---
            // Utilise la nouvelle méthode de UtilisateurModel
            $roles = $this->userModel->getUserRoleNames($user['id']);
            // ----------------------------------------
            
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'roles' => $roles // <<< Stocke le tableau des noms de rôles
            ];
            
            $this->setFlashMessage('success', 'Connexion réussie ! Bienvenue ' . htmlspecialchars($user['prenom']) . '.');
            $this->redirect('/dashboard');
        } else {
            // Échec de la connexion
            $this->setFlashMessage('error', 'Identifiants invalides ou compte inactif.');
            $this->redirect('/login');
        }
    }

    /**
     * Déconnecte l'utilisateur (GET ou POST /logout)
     */
    public function logout(): void
    {
        // Vider toutes les variables de session
        $_SESSION = [];

        // Supprimer le cookie de session côté client
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, // Expiré dans le passé
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Détruire la session côté serveur
        session_destroy();

        // Optionnel : message flash de déconnexion
        // setFlashMessage('info', 'Vous avez été déconnecté.'); // Attention, session détruite avant ?

        // Rediriger vers la page de connexion
        $this->redirect('/login');
    }

    // Méthode pour afficher le dashboard (appelée par le routeur)
     public function showDashboard(): void
     {
          $this->requireLogin();
        // Récupération des statistiques
        $filieresCount = count((new FiliereModel())->findAll());
        $modulesCount = count((new ModuleModel())->findAllWithFiliere());
        $etatsCount = count((new EtatAvancementModel())->findAllWithDetails());
        $usersCount = count($this->userModel->findAll());
        
        // Récupérer les derniers suivis d'états d'avancement
        $latestEtats = array_slice((new EtatAvancementModel())->findAllWithDetails(), 0, 5);
        
        $this->render('dashboard/index', [
            'title' => 'Tableau de Bord',
            'stats' => [
                'filieres' => $filieresCount,
                'modules' => $modulesCount,
                'etats' => $etatsCount,
                'utilisateurs' => $usersCount,
            ],
            'latestEtats' => $latestEtats,
        ]);
     }

}