<?php // src/Controller/AuthController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Model\UtilisateurModel; // Utilise le modèle
// Pas besoin de use PDO ici

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
    // Rappel de la version avec DEBUG :
public function processLogin(): void
{
    echo "DEBUG: Entrée dans processLogin()...<br>"; // 1

    if ($this->isUserLoggedIn()) {
        echo "DEBUG: Déjà connecté, redirection vers dashboard...<br>";
        $this->redirect('/dashboard');
    }

    $email = $this->input('email', '', 'post');
    $password = $this->input('password', '', 'post');

    echo "DEBUG: Email reçu: " . htmlspecialchars($email) . "<br>"; // 2
    echo "DEBUG: Mot de passe reçu: " . (!empty($password) ? "[Présent]" : "[Vide]") . "<br>"; // 3

    // Validation ... (les echos de validation sont aussi utiles)
     if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
         echo "DEBUG: Validation Email échouée.<br>"; // V1
         $this->setFlashMessage('error', 'Adresse email invalide ou manquante.');
         $this->redirect('/login'); return;
     }
     if (empty($password)) {
         echo "DEBUG: Validation Mot de passe échouée.<br>"; // V2
         $this->setFlashMessage('error', 'Le mot de passe est requis.');
         $this->redirect('/login'); return;
     }


    echo "DEBUG: Validation OK. Appel de verifyLogin...<br>"; // 4
    $user = $this->userModel->verifyLogin($email, $password);

    if ($user) {
        echo "DEBUG: verifyLogin() a retourné SUCCÈS.<br>"; // 5a
    } else {
        echo "DEBUG: verifyLogin() a retourné ÉCHEC.<br>"; // 5b
    }

    if ($user) {
        // Succès
        echo "DEBUG: Connexion réussie. Préparation session...<br>"; // 6
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = [ /* ... */ ];
        echo "DEBUG: Session préparée. Redirection vers dashboard...<br>"; // 7
        $this->setFlashMessage('success', 'Connexion réussie ! ...');
        $this->redirect('/dashboard'); // << LA REDIRECTION FINALE

    } else {
        // Échec
        echo "DEBUG: Connexion échouée. Préparation flash et redirection login...<br>"; // 8
        $this->setFlashMessage('error', 'Identifiants invalides ou compte inactif.');
        $this->redirect('/login'); // << REDIRECTION SI ECHEC
    }
    echo "DEBUG: Fin de processLogin (ne devrait pas être atteint).<br>"; // 9
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
          $this->requireLogin(); // Assure que seul un utilisateur connecté peut voir ça
          $this->render('dashboard/index', ['title' => 'Tableau de Bord']);
     }

}