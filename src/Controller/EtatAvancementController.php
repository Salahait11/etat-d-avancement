<?php // src/Controller/EtatAvancementController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Validator;
use App\Model\EtatAvancementModel;
use App\Model\ModuleModel;
use App\Model\UtilisateurModel;
use App\Model\ObjectifPedagogiqueModel;
use App\Model\ContenuSeanceModel;
use App\Model\MoyenDidactiqueModel;
use App\Model\StrategieEvaluationModel;

class EtatAvancementController extends BaseController
{
    private EtatAvancementModel $etatModel;
    private ModuleModel $moduleModel;
    private UtilisateurModel $utilisateurModel;
    private ObjectifPedagogiqueModel $objectifModel;
    private ContenuSeanceModel $contenuModel;
    private MoyenDidactiqueModel $moyenModel;
    private StrategieEvaluationModel $strategieModel;

    public function __construct()
    {
        parent::__construct();
        $this->etatModel = new EtatAvancementModel();
        $this->moduleModel = new ModuleModel();
        $this->utilisateurModel = new UtilisateurModel();
        $this->objectifModel = new ObjectifPedagogiqueModel();
        $this->contenuModel = new ContenuSeanceModel();
        $this->moyenModel = new MoyenDidactiqueModel();
        $this->strategieModel = new StrategieEvaluationModel();
    }

    /**
     * Vérifie que l'utilisateur est connecté et est formateur ou admin
     */
    private function requireFormateur(): void
    {
        $this->requireLogin();
        
        if (!$this->isFormateur() && !$this->isAdmin()) {
            $this->setFlashMessage('error', 'Accès refusé. Vous devez être formateur ou administrateur pour accéder à cette page.');
            $this->redirect('/dashboard');
            exit;
        }
    }

    /**
     * Liste tous les états d'avancement
     */
    public function index(): void
    {
        $this->requireFormateur();
        
        // Récupérer les filtres depuis GET
        $filters = [
            'date_seance' => $_GET['date_seance'] ?? '',
            'module_id'   => $_GET['module_id'] ?? ''
        ];
        // Obtenir les états filtrés
        $etatsAvancement = $this->etatModel->findAllWithDetails($filters);
        // Liste des modules pour le filtre
        $modules = $this->moduleModel->findAllWithFiliere();
        // Afficher la liste avec filtres
        $this->render('etat_avancement/list', [
            'title'   => 'États d\'Avancement',
            'etatsAvancement' => $etatsAvancement,
            'modules' => $modules,
            'filters' => $filters
        ]);
    }

    /**
     * Affiche les détails d'un état d'avancement
     */
    public function view(int $id): void
    {
        $this->requireFormateur();
        
        $etat = $this->etatModel->findByIdWithDetails($id);
        
        if (!$etat) {
            $this->setFlashMessage('error', 'État d\'avancement non trouvé.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Récupérer les données liées
        $objectifs = $this->etatModel->getObjectifsByEtatId($id);
        $contenus = $this->etatModel->getContenusByEtatId($id);
        $moyens = $this->etatModel->getMoyensByEtatId($id);
        $strategies = $this->etatModel->getStrategiesByEtatId($id);
        
        $this->render('etat_avancement/view', [
            'title' => 'Détails de l\'État d\'Avancement',
            'etat' => $etat,
            'objectifs' => $objectifs,
            'contenus' => $contenus,
            'moyens' => $moyens,
            'strategies' => $strategies
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'un état d'avancement
     */
    public function add(): void
    {
        $this->requireFormateur();
        
        // Récupérer les listes nécessaires pour le formulaire
        $modules = $this->moduleModel->findAllWithFiliere();
        $formateurs = $this->utilisateurModel->findByRole('formateur');
        $objectifs = $this->objectifModel->findAll();
        $contenus = $this->contenuModel->findAll();
        $moyens = $this->moyenModel->findAll();
        $strategies = $this->strategieModel->findAll();
        
        $this->render('etat_avancement/add', [
            'title' => 'Ajouter un État d\'Avancement',
            'modules' => $modules,
            'formateurs' => $formateurs,
            'objectifs' => $objectifs,
            'contenus' => $contenus,
            'moyens' => $moyens,
            'strategies' => $strategies,
            'formData' => [
                'id_module' => '',
                'id_formateur' => $this->currentUser['id'] ?? '',
                'date_seance' => date('Y-m-d'),
                'duree_realisee' => '',
                'commentaire' => '',
                'difficultes' => '',
                'solutions' => ''
            ],
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'ajout d'un état d'avancement
     */
    public function store(): void
    {
        $this->requireFormateur();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'id_module' => (int)($_POST['id_module'] ?? 0),
            'id_formateur' => (int)($_POST['id_formateur'] ?? 0),
            'date_seance' => $_POST['date_seance'] ?? '',
            'duree_realisee' => (float)($_POST['duree_realisee'] ?? 0),
            'commentaire' => trim($_POST['commentaire'] ?? ''),
            'difficultes' => trim($_POST['difficultes'] ?? ''),
            'solutions' => trim($_POST['solutions'] ?? '')
        ];
        
        // Récupérer les données des éléments liés
        $formData['objectifs'] = $this->processCheckboxItems('objectif');
        $formData['contenus'] = $this->processCheckboxItems('contenu');
        $formData['moyens'] = $this->processCheckboxItems('moyen');
        $formData['strategies'] = $this->processCheckboxItems('strategie');
        
        // Valider les données
        $errors = $this->validateEtatData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $modules = $this->moduleModel->findAllWithFiliere();
            $formateurs = $this->utilisateurModel->findByRole('formateur');
            $objectifs = $this->objectifModel->findAll();
            $contenus = $this->contenuModel->findAll();
            $moyens = $this->moyenModel->findAll();
            $strategies = $this->strategieModel->findAll();
            
            $this->render('etat_avancement/add', [
                'title' => 'Ajouter un État d\'Avancement',
                'modules' => $modules,
                'formateurs' => $formateurs,
                'objectifs' => $objectifs,
                'contenus' => $contenus,
                'moyens' => $moyens,
                'strategies' => $strategies,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Créer l'état d'avancement
        $result = $this->etatModel->create($formData);
        
        if ($result) {
            $this->setFlashMessage('success', 'État d\'avancement créé avec succès.');
            $this->redirect('/etats-avancement/view/' . $result);
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la création de l\'état d\'avancement.');
            
            // Réafficher le formulaire avec les données
            $modules = $this->moduleModel->findAllWithFiliere();
            $formateurs = $this->utilisateurModel->findByRole('formateur');
            $objectifs = $this->objectifModel->findAll();
            $contenus = $this->contenuModel->findAll();
            $moyens = $this->moyenModel->findAll();
            $strategies = $this->strategieModel->findAll();
            
            $this->render('etat_avancement/add', [
                'title' => 'Ajouter un État d\'Avancement',
                'modules' => $modules,
                'formateurs' => $formateurs,
                'objectifs' => $objectifs,
                'contenus' => $contenus,
                'moyens' => $moyens,
                'strategies' => $strategies,
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la création de l\'état d\'avancement.']
            ]);
        }
    }

    /**
     * Affiche le formulaire d'édition d'un état d'avancement
     */
    public function edit(int $id): void
    {
        $this->requireFormateur();
        
        $etat = $this->etatModel->findByIdWithDetails($id);
        
        if (!$etat) {
            $this->setFlashMessage('error', 'État d\'avancement non trouvé.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Vérifier que l'utilisateur est le formateur associé ou un admin
        if (!$this->isAdmin() && $etat['id_formateur'] != $this->currentUser['id']) {
            $this->setFlashMessage('error', 'Vous ne pouvez modifier que vos propres états d\'avancement.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Récupérer les données liées
        $objectifs = $this->etatModel->getObjectifsByEtatId($id);
        $contenus = $this->etatModel->getContenusByEtatId($id);
        $moyens = $this->etatModel->getMoyensByEtatId($id);
        $strategies = $this->etatModel->getStrategiesByEtatId($id);
        
        // Récupérer les listes nécessaires pour le formulaire
        $modules = $this->moduleModel->findAllWithFiliere();
        $formateurs = $this->utilisateurModel->findByRole('formateur');
        $allObjectifs = $this->objectifModel->findAll();
        $allContenus = $this->contenuModel->findAll();
        $allMoyens = $this->moyenModel->findAll();
        $allStrategies = $this->strategieModel->findAll();
        
        // Préparer les données pour le formulaire
        $formData = [
            'id_module' => $etat['id_module'],
            'id_formateur' => $etat['id_formateur'],
            'date_seance' => $etat['date_seance'],
            'duree_realisee' => $etat['duree_realisee'],
            'commentaire' => $etat['commentaire'],
            'difficultes' => $etat['difficultes'],
            'solutions' => $etat['solutions'],
            'objectifs' => $this->prepareItemsForForm($objectifs),
            'contenus' => $this->prepareItemsForForm($contenus),
            'moyens' => $this->prepareItemsForForm($moyens),
            'strategies' => $this->prepareItemsForForm($strategies)
        ];
        
        $this->render('etat_avancement/edit', [
            'title' => 'Modifier l\'État d\'Avancement',
            'etat' => $etat,
            'modules' => $modules,
            'formateurs' => $formateurs,
            'objectifs' => $allObjectifs,
            'contenus' => $allContenus,
            'moyens' => $allMoyens,
            'strategies' => $allStrategies,
            'formData' => $formData,
            'errors' => []
        ]);
    }

    /**
     * Traite le formulaire d'édition d'un état d'avancement
     */
    public function update(int $id): void
    {
        $this->requireFormateur();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        $etat = $this->etatModel->findByIdWithDetails($id);
        
        if (!$etat) {
            $this->setFlashMessage('error', 'État d\'avancement non trouvé.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Vérifier que l'utilisateur est le formateur associé ou un admin
        if (!$this->isAdmin() && $etat['id_formateur'] != $this->currentUser['id']) {
            $this->setFlashMessage('error', 'Vous ne pouvez modifier que vos propres états d\'avancement.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Récupérer et nettoyer les données du formulaire
        $formData = [
            'id_module' => (int)($_POST['id_module'] ?? 0),
            'id_formateur' => (int)($_POST['id_formateur'] ?? 0),
            'date_seance' => $_POST['date_seance'] ?? '',
            'duree_realisee' => (float)($_POST['duree_realisee'] ?? 0),
            'commentaire' => trim($_POST['commentaire'] ?? ''),
            'difficultes' => trim($_POST['difficultes'] ?? ''),
            'solutions' => trim($_POST['solutions'] ?? '')
        ];
        
        // Récupérer les données des éléments liés
        $formData['objectifs'] = $this->processCheckboxItems('objectif');
        $formData['contenus'] = $this->processCheckboxItems('contenu');
        $formData['moyens'] = $this->processCheckboxItems('moyen');
        $formData['strategies'] = $this->processCheckboxItems('strategie');
        
        // Valider les données
        $errors = $this->validateEtatData($formData);
        
        if (!empty($errors)) {
            // En cas d'erreurs, réafficher le formulaire avec les erreurs
            $modules = $this->moduleModel->findAllWithFiliere();
            $formateurs = $this->utilisateurModel->findByRole('formateur');
            $objectifs = $this->objectifModel->findAll();
            $contenus = $this->contenuModel->findAll();
            $moyens = $this->moyenModel->findAll();
            $strategies = $this->strategieModel->findAll();
            
            $this->render('etat_avancement/edit', [
                'title' => 'Modifier l\'État d\'Avancement',
                'etat' => $etat,
                'modules' => $modules,
                'formateurs' => $formateurs,
                'objectifs' => $objectifs,
                'contenus' => $contenus,
                'moyens' => $moyens,
                'strategies' => $strategies,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        
        // Mettre à jour l'état d'avancement
        $success = $this->etatModel->update($id, $formData);
        
        if ($success) {
            $this->setFlashMessage('success', 'État d\'avancement mis à jour avec succès.');
            $this->redirect('/etats-avancement/view/' . $id);
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour de l\'état d\'avancement.');
            
            // Réafficher le formulaire avec les données
            $modules = $this->moduleModel->findAllWithFiliere();
            $formateurs = $this->utilisateurModel->findByRole('formateur');
            $objectifs = $this->objectifModel->findAll();
            $contenus = $this->contenuModel->findAll();
            $moyens = $this->moyenModel->findAll();
            $strategies = $this->strategieModel->findAll();
            
            $this->render('etat_avancement/edit', [
                'title' => 'Modifier l\'État d\'Avancement',
                'etat' => $etat,
                'modules' => $modules,
                'formateurs' => $formateurs,
                'objectifs' => $objectifs,
                'contenus' => $contenus,
                'moyens' => $moyens,
                'strategies' => $strategies,
                'formData' => $formData,
                'errors' => ['general' => 'Erreur lors de la mise à jour de l\'état d\'avancement.']
            ]);
        }
    }

    /**
     * Supprime un état d'avancement
     */
    public function delete(int $id): void
    {
        $this->requireFormateur();
        
        // Vérifier que la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée pour la suppression.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        $etat = $this->etatModel->findByIdWithDetails($id);
        
        if (!$etat) {
            $this->setFlashMessage('error', 'État d\'avancement non trouvé.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Vérifier que l'utilisateur est le formateur associé ou un admin
        if (!$this->isAdmin() && $etat['id_formateur'] != $this->currentUser['id']) {
            $this->setFlashMessage('error', 'Vous ne pouvez supprimer que vos propres états d\'avancement.');
            $this->redirect('/etats-avancement');
            return;
        }
        
        // Supprimer l'état d'avancement
        $success = $this->etatModel->delete($id);
        
        if ($success) {
            $this->setFlashMessage('success', 'État d\'avancement supprimé avec succès.');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la suppression de l\'état d\'avancement.');
        }
        
        $this->redirect('/etats-avancement');
    }

    /**
     * Traite les éléments de type checkbox du formulaire
     * 
     * @param string $prefix Préfixe des noms de champs (objectif, contenu, etc.)
     * @return array Tableau d'éléments avec ID et statut
     */
    private function processCheckboxItems(string $prefix): array
    {
        $items = [];
        
        if (isset($_POST[$prefix]) && is_array($_POST[$prefix])) {
            foreach ($_POST[$prefix] as $id => $status) {
                if ($status !== '') {
                    $items[] = [
                        'id' => (int)$id,
                        'statut' => $status
                    ];
                }
            }
        }
        
        return $items;
    }

    /**
     * Prépare les éléments pour l'affichage dans le formulaire d'édition
     * 
     * @param array $items Éléments récupérés de la base de données
     * @return array Tableau formaté pour le formulaire
     */
    private function prepareItemsForForm(array $items): array
    {
        $result = [];
        
        foreach ($items as $item) {
            $result[$item['id']] = $item['statut'];
        }
        
        return $result;
    }

    /**
     * Valide les données d'un état d'avancement
     * 
     * @param array $data Données à valider
     * @return array Tableau des erreurs
     */
    private function validateEtatData(array $data): array
    {
        $validator = new Validator();
        $validator
            ->required('id_module', $data['id_module'])
            ->numeric('id_module', $data['id_module'])
            ->positive('id_module', $data['id_module'])
            ->exists('id_module', $data['id_module'], fn(int $id): bool => (bool)$this->moduleModel->findById($id))
            ->required('id_formateur', $data['id_formateur'])
            ->numeric('id_formateur', $data['id_formateur'])
            ->positive('id_formateur', $data['id_formateur'])
            ->exists('id_formateur', $data['id_formateur'], fn(int $id): bool => (bool)$this->utilisateurModel->findById($id))
            ->required('date_seance', $data['date_seance'])
            ->exists('date_seance', $data['date_seance'], fn($val): bool => preg_match('/^\d{4}-\d{2}-\d{2}$/', $val) === 1)
            ->required('duree_realisee', $data['duree_realisee'])
            ->numeric('duree_realisee', $data['duree_realisee'])
            ->positive('duree_realisee', $data['duree_realisee'])
            ->required('objectifs', $data['objectifs'])
            ->required('contenus', $data['contenus']);
        return $validator->getErrors();
    }
}
