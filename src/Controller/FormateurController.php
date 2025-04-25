<?php
// src/Controller/FormateurController.php

declare(strict_types=1);
namespace App\Controller;

use App\Core\BaseController;
use App\Model\FormateurModel;

class FormateurController extends BaseController
{
    private FormateurModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new FormateurModel();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $formateurs = $this->model->findAll();
        $this->render('formateur/list', [
            'title' => 'Gestion des Formateurs',
            'formateurs' => $formateurs
        ]);
    }

    public function add(): void
    {
        $this->requireAdmin();
        $users = $this->model->findUsersNotFormateur();
        $formData = ['id_utilisateur' => '', 'specialite' => ''];
        $this->render('formateur/add', [
            'title' => 'Ajouter un Formateur',
            'users' => $users,
            'formData' => $formData,
            'errors' => []
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('error', 'Méthode non autorisée.');
            $this->redirect('/formateurs');
            return;
        }
        $formData = [
            'id_utilisateur' => (int)($_POST['id_utilisateur'] ?? 0),
            'specialite' => trim($_POST['specialite'] ?? '')
        ];
        $errors = [];
        if (!$formData['id_utilisateur']) {
            $errors['id_utilisateur'] = 'Sélectionnez un utilisateur.';
        }
        if (empty($formData['specialite'])) {
            $errors['specialite'] = 'Spécialité requise.';
        }
        if ($errors) {
            $users = $this->model->findUsersNotFormateur();
            $this->render('formateur/add', [
                'title' => 'Ajouter un Formateur',
                'users' => $users,
                'formData' => $formData,
                'errors' => $errors
            ]);
            return;
        }
        if ($this->model->create($formData)) {
            $this->setFlashMessage('success', 'Formateur ajouté avec succès.');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de l\'ajout du formateur.');
        }
        $this->redirect('/formateurs');
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();
        $f = $this->model->findById($id);
        if (!$f) {
            $this->setFlashMessage('error', 'Formateur non trouvé.');
            $this->redirect('/formateurs');
            return;
        }
        $formData = ['specialite' => $f['specialite']];
        $this->render('formateur/edit', [
            'title' => 'Modifier Spécialité',
            'f' => $f,
            'formData' => $formData,
            'errors' => []
        ]);
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/formateurs');
            return;
        }
        $data = ['specialite' => trim($_POST['specialite'] ?? '')];
        $errors = [];
        if (empty($data['specialite'])) {
            $errors['specialite'] = 'Spécialité requise.';
        }
        if ($errors) {
            $f = $this->model->findById($id);
            $this->render('formateur/edit', [
                'title' => 'Modifier Spécialité',
                'f' => $f,
                'formData' => $data,
                'errors' => $errors
            ]);
            return;
        }
        if ($this->model->update($id, $data)) {
            $this->setFlashMessage('success', 'Spécialité mise à jour.');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la mise à jour.');
        }
        $this->redirect('/formateurs');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        if ($this->model->delete($id)) {
            $this->setFlashMessage('success', 'Formateur supprimé.');
        } else {
            $this->setFlashMessage('error', 'Erreur lors de la suppression.');
        }
        $this->redirect('/formateurs');
    }
}
