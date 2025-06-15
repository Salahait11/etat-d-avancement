<?php // src/Controller/EtatAvancementController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController;
use App\Core\Validator;
use App\Model\EtatAvancementModel;
use App\Model\ModuleModel;
use App\Model\UtilisateurModel;
use App\Model\ObjectifPedagogiqueModel;
use App\Model\MoyenDidactiqueModel;
use App\Model\StrategieEvaluationModel;
use App\Model\FormateurModel;

class EtatAvancementController extends BaseController
{
    private EtatAvancementModel $etatModel;
    private ModuleModel $moduleModel;
    private UtilisateurModel $utilisateurModel;
    private ObjectifPedagogiqueModel $objectifModel;
    private MoyenDidactiqueModel $moyenModel;
    private StrategieEvaluationModel $strategieModel;
    private FormateurModel $formateurModel;
    protected ?array $currentUser;

    public function __construct()
    {
        parent::__construct();
        $this->etatModel = new EtatAvancementModel();
        $this->moduleModel = new ModuleModel();
        $this->utilisateurModel = new UtilisateurModel();
        $this->objectifModel = new ObjectifPedagogiqueModel();
        $this->moyenModel = new MoyenDidactiqueModel();
        $this->strategieModel = new StrategieEvaluationModel();
        $this->formateurModel = new FormateurModel();
        $this->currentUser = $this->getCurrentUser();
    }

    /**
     * Vérifie que l'utilisateur est connecté et est formateur ou admin
     */
    private function requireFormateur(): void
    {
        error_log("Vérification du formateur");
        error_log("Session utilisateur : " . print_r($_SESSION['user'] ?? 'non connecté', true));
        
        $this->requireLogin();
        
        if (!$this->isFormateur() && !$this->isAdmin()) {
            error_log("Accès refusé - L'utilisateur n'est ni formateur ni admin");
            $this->setFlashMessage('error', 'Accès refusé. Vous devez être formateur ou administrateur pour accéder à cette page.');
            $this->redirect('/dashboard');
            exit;
        }
        error_log("Utilisateur autorisé");
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
            'filters' => $filters,
            'isAdmin' => $this->isAdmin(),
            'currentUser' => $this->currentUser
        ]);
    }

    /**
     * Affiche les détails d'un état d'avancement
     *
     * @param int $id ID de l'état d'avancement
     * @return void
     */
    public function view(int $id): void
    {
        try {
            error_log("Début de la méthode view pour l'ID : " . $id);
            
            // Récupérer l'état d'avancement avec ses détails
            $etat = $this->etatModel->findByIdWithDetails($id);
            error_log("État d'avancement récupéré : " . print_r($etat, true));
            
            if (!$etat) {
                error_log("État d'avancement non trouvé pour l'ID : " . $id);
                $this->setFlashMessage('error', 'État d\'avancement non trouvé');
                $this->redirect('/etats-avancement');
                return;
            }

            // Récupérer les objectifs, moyens et stratégies
            $objectifs = $this->etatModel->getObjectifsByEtatId($id);
            $moyens = $this->etatModel->getMoyensByEtatId($id);
            $strategies = $this->etatModel->getStrategiesByEtatId($id);
            
            error_log("Objectifs récupérés : " . print_r($objectifs, true));
            error_log("Moyens récupérés : " . print_r($moyens, true));
            error_log("Stratégies récupérées : " . print_r($strategies, true));

            // Formater les données pour l'affichage
            $formattedData = [
                'etat' => [
                    'id' => $etat['id'],
                    'module' => $etat['module_intitule'] ?? 'Non spécifié',
                    'formateur' => $etat['formateur_nom'] ?? 'Non spécifié',
                    'date' => $etat['date'] ? date('d/m/Y', strtotime($etat['date'])) : 'Non spécifiée',
                    'heure' => $etat['heure'] ?? 'Non spécifiée',
                    'nbr_heure' => $etat['nbr_heure'] ?? 'Non spécifiée',
                    'nbr_heure_cumulee' => $etat['nbr_heure_cumulee'] ?? 'Non spécifiée',
                    'taux_realisation' => $etat['taux_realisation'] ?? 0,
                    'disposition' => $etat['disposition'] ? 'Oui' : 'Non',
                    'commentaire' => $etat['commentaire'] ?? 'Aucun commentaire',
                    'difficultes' => $etat['difficultes'] ?? 'Aucune difficulté',
                    'solutions' => $etat['solutions'] ?? 'Aucune solution',
                    'contenu_seance' => $etat['contenu_seance'] ?? 'Aucun contenu',
                    'id_formateur' => $etat['id_formateur'] ?? null
                ],
                'objectifs' => array_map(function($obj) {
                    return [
                        'id' => $obj['id'],
                        'libelle' => $obj['libelle'],
                        'statut' => $this->formatStatut($obj['statut'] ?? 'non_atteint', 'objectif')
                    ];
                }, $objectifs),
                'moyens' => array_map(function($moyen) {
                    return [
                        'id' => $moyen['id'],
                        'libelle' => $moyen['libelle'],
                        'statut' => $this->formatStatut($moyen['statut'] ?? 'non_utilise', 'moyen')
                    ];
                }, $moyens),
                'strategies' => array_map(function($strategy) {
                    return [
                        'id' => $strategy['id'],
                        'libelle' => $strategy['libelle'],
                        'statut' => $this->formatStatut($strategy['statut'] ?? 'non_appliquee', 'strategie')
                    ];
                }, $strategies)
            ];
            
            error_log("Données formatées pour la vue : " . print_r($formattedData, true));

            // Récupérer les informations de l'utilisateur connecté
            $currentUser = $this->getCurrentUser();
            $isAdmin = $currentUser && isset($currentUser['role']) && $currentUser['role'] === 'admin';

            // Afficher la vue
            $this->render('etat_avancement/view', [
                'title' => 'Détails de l\'État d\'Avancement',
                'data' => $formattedData,
                'currentUser' => $currentUser,
                'isAdmin' => $isAdmin
            ]);

        } catch (\Exception $e) {
            error_log("Erreur dans la méthode view : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            $this->setFlashMessage('error', 'Une erreur est survenue lors de l\'affichage des détails');
            $this->redirect('/etats-avancement');
        }
    }

    /**
     * Formate le statut pour l'affichage
     * 
     * @param string $statut Statut à formater
     * @param string $type Type d'élément (objectif, moyen, strategie)
     * @return string Statut formaté
     */
    private function formatStatut(string $statut, string $type): string
    {
        $statuts = [
            'objectif' => [
                'atteint' => 'Atteint',
                'en_cours' => 'En cours',
                'non_atteint' => 'Non atteint',
                'realise' => 'Réalisé'
            ],
            'moyen' => [
                'utilise' => 'Utilisé',
                'non_utilise' => 'Non utilisé'
            ],
            'strategie' => [
                'appliquee' => 'Appliquée',
                'non_appliquee' => 'Non appliquée',
                'utilise' => 'Utilisée'
            ]
        ];

        return $statuts[$type][$statut] ?? $statut;
    }

    /**
     * Affiche le formulaire d'ajout d'un état d'avancement
     */
    public function add(): void
    {
        $this->requireFormateur();
        
        // Initialiser le token CSRF
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Récupérer les listes nécessaires pour le formulaire
        $modules = $this->moduleModel->findAllWithFiliere();
        $formateurs = $this->formateurModel->findAll();
        $objectifs = $this->objectifModel->findAll();
        $moyens = $this->moyenModel->findAll();
        $strategies = $this->strategieModel->findAll();
        
        $this->render('etat_avancement/add', [
            'title' => 'Ajouter un État d\'Avancement',
            'modules' => $modules,
            'formateurs' => $formateurs,
            'objectifs' => $objectifs,
            'moyens' => $moyens,
            'strategies' => $strategies,
            'formData' => [
                'id_module' => '',
                'id_formateur' => $this->currentUser['id'] ?? '',
                'date' => date('Y-m-d'),
                'heure' => date('H:i'),
                'nbr_heure_cumulee' => '',
                'nbr_heure' => '',
                'disposition' => '',
                'observation' => '',
                'taux_realisation' => '',
                'commentaire' => '',
                'difficultes' => '',
                'solutions' => '',
                'contenu_seance' => '',
                'objectifs' => [],
                'moyens' => [],
                'strategies' => []
            ],
            'errors' => []
        ]);
    }

    /**
     * Enregistre un nouvel état d'avancement
     */
    public function store(): void
    {
        $this->requireFormateur();
        
        error_log("=== DÉBUT DU TRAITEMENT STORE ===");
        error_log("Données POST reçues : " . print_r($_POST, true));
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            error_log("Utilisateur non connecté");
            $this->redirect('/login');
        }

        // Vérifier si c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Méthode non POST : " . $_SERVER['REQUEST_METHOD']);
            $this->redirect('/etats-avancement');
        }

        // Récupérer les données du formulaire
        $data = [
            'id_module' => $_POST['id_module'] ?? null,
            'id_formateur' => $_POST['id_formateur'] ?? null,
            'date' => $_POST['date'] ?? null,
            'heure' => $_POST['heure'] ?? null,
            'nbr_heure' => $_POST['nbr_heure'] ?? null,
            'nbr_heure_cumulee' => $_POST['nbr_heure_cumulee'] ?? 0,
            'taux_realisation' => $_POST['taux_realisation'] ?? 0,
            'disposition' => $_POST['disposition'] ?? 0,
            'commentaire' => $_POST['commentaire'] ?? '',
            'difficultes' => $_POST['difficultes'] ?? '',
            'solutions' => $_POST['solutions'] ?? '',
            'contenu_seance' => $_POST['contenu_seance'] ?? '',
            'objectifs' => [],
            'moyens' => [],
            'strategies' => []
        ];

        error_log("Données formatées : " . print_r($data, true));

        // Formater les objectifs
        if (isset($_POST['objectifs']) && is_array($_POST['objectifs'])) {
            foreach ($_POST['objectifs'] as $id => $statut) {
                if (!empty($statut)) {
                    $data['objectifs'][] = [
                        'id' => (int)$id,
                        'statut' => $statut
                    ];
                }
            }
        }
        error_log("Objectifs formatés : " . print_r($data['objectifs'], true));

        // Formater les moyens
        if (isset($_POST['moyens']) && is_array($_POST['moyens'])) {
            foreach ($_POST['moyens'] as $id => $statut) {
                if (!empty($statut)) {
                    $data['moyens'][] = [
                        'id' => (int)$id,
                        'statut' => $statut
                    ];
                }
            }
        }
        error_log("Moyens formatés : " . print_r($data['moyens'], true));

        // Formater les stratégies
        if (isset($_POST['strategies']) && is_array($_POST['strategies'])) {
            foreach ($_POST['strategies'] as $id => $statut) {
                if (!empty($statut)) {
                    $data['strategies'][] = [
                        'id' => (int)$id,
                        'statut' => $statut
                    ];
                }
            }
        }
        error_log("Stratégies formatées : " . print_r($data['strategies'], true));

        // Valider les données
        $errors = $this->validateEtatData($data);
        error_log("Erreurs de validation : " . print_r($errors, true));

        if (!empty($errors)) {
            error_log("Erreurs de validation détectées, redirection vers le formulaire");
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
            $this->redirect('/etats-avancement/add');
            return;
        }

        // Créer l'état d'avancement
        error_log("Tentative de création de l'état d'avancement");
        $etatId = $this->etatModel->create($data);

        if ($etatId) {
            error_log("État d'avancement créé avec succès, ID : " . $etatId);
            $_SESSION['flash_messages']['success'][] = 'État d\'avancement créé avec succès.';
            $this->redirect('/etats-avancement/view/' . $etatId);
        } else {
            error_log("Échec de la création de l'état d'avancement");
            $_SESSION['flash_messages']['error'][] = 'Erreur lors de la création de l\'état d\'avancement.';
            $_SESSION['form_data'] = $data;
            $this->redirect('/etats-avancement/add');
        }
        error_log("=== FIN DU TRAITEMENT STORE ===");
    }

    /**
     * Affiche le formulaire d'édition d'un état d'avancement
     */
    public function edit(int $id): void
    {
        $this->requireFormateur();
        
        // Récupérer l'état d'avancement avec tous ses détails
        $etat = $this->etatModel->findByIdWithDetails($id);
        if (!$etat) {
            $this->setFlashMessage('error', 'État d\'avancement non trouvé.');
            $this->redirect('/etats-avancement');
            return;
        }

        // Récupérer les données nécessaires
        $modules = $this->moduleModel->findAll();
        $formateurs = $this->formateurModel->findAll();
        $objectifs = $this->objectifModel->findAll();
        $moyens = $this->moyenModel->findAll();
        $strategies = $this->strategieModel->findAll();

        // Récupérer les données associées
        $objectifsAssocies = $this->etatModel->getObjectifs($id);
        $moyensAssocies = $this->etatModel->getMoyens($id);
        $strategiesAssociees = $this->etatModel->getStrategies($id);

        // Préparer les données du formulaire
        $formData = [
            'id_module' => $etat['id_module'],
            'id_formateur' => $etat['id_formateur'],
            'date' => $etat['date'],
            'heure' => date('H:i', strtotime($etat['heure'])),
            'nbr_heure' => $etat['nbr_heure'],
            'nbr_heure_cumulee' => $etat['nbr_heure_cumulee'],
            'taux_realisation' => $etat['taux_realisation'],
            'disposition' => $etat['disposition'],
            'commentaire' => $etat['commentaire'],
            'difficultes' => $etat['difficultes'],
            'solutions' => $etat['solutions'],
            'contenu_seance' => $etat['contenu_seance'],
            'objectifs' => $this->prepareItemsForForm($objectifsAssocies),
            'moyens' => $this->prepareItemsForForm($moyensAssocies),
            'strategies' => $this->prepareItemsForForm($strategiesAssociees)
        ];

        // Récupérer les erreurs de session si elles existent
        $errors = $_SESSION['form_errors'] ?? [];
        $sessionFormData = $_SESSION['form_data'] ?? [];

        // Fusionner les données de session avec les données du formulaire
        if (!empty($sessionFormData)) {
            $formData = array_merge($formData, $sessionFormData);
        }

        // Nettoyer les données de session
        unset($_SESSION['form_errors'], $_SESSION['form_data']);

        $this->render('etat_avancement/edit', [
            'title' => 'Modifier l\'état d\'avancement',
            'etat' => $etat,
            'modules' => $modules,
            'formateurs' => $formateurs,
            'objectifs' => $objectifs,
            'moyens' => $moyens,
            'strategies' => $strategies,
            'formData' => $formData,
            'errors' => $errors
        ]);
    }

    /**
     * Traite le formulaire d'édition d'un état d'avancement
     */
    public function update($id) {
        error_log("=== DÉBUT DE LA REQUÊTE ===");
        error_log("URI demandée : " . $_SERVER['REQUEST_URI']);
        error_log("Méthode HTTP : " . $_SERVER['REQUEST_METHOD']);
        error_log("Chemin de base : " . BASE_URL);
        error_log("Route calculée : " . $_SERVER['REQUEST_URI']);
        error_log("ID de l'état d'avancement à mettre à jour : " . $id);

        // Vérification de l'autorisation
        $this->requireFormateur();
        
        // Récupération et formatage des données
        $heure = $_POST['heure'] ?? null;
        if ($heure) {
            // Convertir l'heure en format datetime
            $date = $_POST['date'] ?? date('Y-m-d');
            $heure = date('Y-m-d H:i:s', strtotime($date . ' ' . $heure));
        }

        $data = [
            'id_module' => $_POST['id_module'] ?? null,
            'id_formateur' => $_POST['id_formateur'] ?? null,
            'date' => $_POST['date'] ?? null,
            'heure' => $heure,
            'nbr_heure' => $_POST['nbr_heure'] ?? null,
            'nbr_heure_cumulee' => $_POST['nbr_heure_cumulee'] ?? null,
            'taux_realisation' => $_POST['taux_realisation'] ?? null,
            'disposition' => isset($_POST['disposition']) ? 1 : 0,
            'commentaire' => $_POST['commentaire'] ?? '',
            'difficultes' => $_POST['difficultes'] ?? '',
            'solutions' => $_POST['solutions'] ?? '',
            'contenu_seance' => $_POST['contenu_seance'] ?? ''
        ];

        error_log("Données formatées pour la mise à jour : " . print_r($data, true));

        // Formatage des objectifs
        $objectifs = [];
        if (isset($_POST['objectifs']) && is_array($_POST['objectifs'])) {
            foreach ($_POST['objectifs'] as $objectifId => $statut) {
                if (!empty($statut)) {
                    $objectifs[] = [
                        'id' => (int)$objectifId,
                        'statut' => $statut
                    ];
                }
            }
        }
        error_log("Objectifs formatés : " . print_r($objectifs, true));

        // Formatage des moyens
        $moyens = [];
        if (isset($_POST['moyens']) && is_array($_POST['moyens'])) {
            foreach ($_POST['moyens'] as $moyenId => $statut) {
                if (!empty($statut)) {
                    $moyens[] = [
                        'id' => (int)$moyenId,
                        'statut' => $statut
                    ];
                }
            }
        }
        error_log("Moyens formatés : " . print_r($moyens, true));

        // Formatage des stratégies
        $strategies = [];
        if (isset($_POST['strategies']) && is_array($_POST['strategies'])) {
            foreach ($_POST['strategies'] as $strategieId => $statut) {
                if (!empty($statut)) {
                    $strategies[] = [
                        'id' => (int)$strategieId,
                        'statut' => $statut
                    ];
                }
            }
        }
        error_log("Stratégies formatées : " . print_r($strategies, true));

        // Validation des données
        $errors = $this->validateEtatData($data);
        if (!empty($errors)) {
            $this->setFlashMessage('error', implode('<br>', $errors));
            $this->redirect("/etats-avancement/edit/$id");
            return;
        }

        try {
            // Mise à jour de l'état d'avancement
            $success = $this->etatModel->update((int)$id, $data, $objectifs, $moyens, $strategies);
            
            if ($success) {
                $this->setFlashMessage('success', 'État d\'avancement mis à jour avec succès');
                $this->redirect("/etats-avancement/view/$id");
            } else {
                $this->setFlashMessage('error', 'Erreur lors de la mise à jour de l\'état d\'avancement');
                $this->redirect("/etats-avancement/edit/$id");
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la mise à jour : " . $e->getMessage());
            $this->setFlashMessage('error', 'Une erreur est survenue lors de la mise à jour');
            $this->redirect("/etats-avancement/edit/$id");
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
     * Valide les données de l'état d'avancement
     * 
     * @param array $data Données à valider
     * @return array Tableau des erreurs
     */
    private function validateEtatData(array $data): array
    {
        $errors = [];

        // Validation des champs obligatoires
        if (empty($data['id_module'])) {
            $errors['id_module'] = 'Le module est obligatoire.';
        }
        if (empty($data['id_formateur'])) {
            $errors['id_formateur'] = 'Le formateur est obligatoire.';
        }
        if (empty($data['date'])) {
            $errors['date'] = 'La date est obligatoire.';
        }
        if (empty($data['heure'])) {
            $errors['heure'] = 'L\'heure est obligatoire.';
        }
        if (empty($data['nbr_heure'])) {
            $errors['nbr_heure'] = 'Le nombre d\'heures est obligatoire.';
        } elseif (!is_numeric($data['nbr_heure']) || $data['nbr_heure'] <= 0) {
            $errors['nbr_heure'] = 'Le nombre d\'heures doit être un nombre positif.';
        }

        // Validation du taux de réalisation
        if (isset($data['taux_realisation']) && (!is_numeric($data['taux_realisation']) || $data['taux_realisation'] < 0 || $data['taux_realisation'] > 100)) {
            $errors['taux_realisation'] = 'Le taux de réalisation doit être un nombre entre 0 et 100.';
        }

        // Validation des heures cumulées
        if (isset($data['nbr_heure_cumulee']) && (!is_numeric($data['nbr_heure_cumulee']) || $data['nbr_heure_cumulee'] < 0)) {
            $errors['nbr_heure_cumulee'] = 'Les heures cumulées doivent être un nombre positif.';
        }

        return $errors;
    }

    /**
     * Valide le token CSRF
     * 
     * @throws \Exception Si le token CSRF est invalide
     */
    private function validateCsrf(): bool
    {
        error_log("Validation du token CSRF");
        error_log("Token POST : " . ($_POST['csrf_token'] ?? 'non fourni'));
        error_log("Token Session : " . ($_SESSION['csrf_token'] ?? 'non défini'));
        
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            error_log("Token CSRF invalide ou manquant");
            return false;
        }
        error_log("Token CSRF valide");
        return true;
    }

    /**
     * Récupère une liste depuis le cache ou la base de données
     * 
     * @param string $type Type de liste (module, formateur, etc.)
     * @return array Liste des éléments
     */
    private function getCachedList(string $type): array
    {
        $cacheKey = "list_{$type}";
        if (!isset($_SESSION[$cacheKey])) {
            $modelProperty = $type . 'Model';
            if (property_exists($this, $modelProperty)) {
                $_SESSION[$cacheKey] = $this->$modelProperty->findAll();
            } else {
                throw new \InvalidArgumentException("Type de liste invalide: {$type}");
            }
        }
        return $_SESSION[$cacheKey];
    }

    /**
     * Nettoie le cache des listes
     */
    private function clearListCache(): void
    {
        $types = ['module', 'formateur', 'objectif', 'contenu', 'moyen', 'strategie'];
        foreach ($types as $type) {
            unset($_SESSION["list_{$type}"]);
        }
    }
}
