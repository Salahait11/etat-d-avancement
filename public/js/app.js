/**
 * app.js - Script principal pour l'application État d'Avancement
 * Version 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des composants
    initDatepickers();
    initDeleteConfirmations();
    initFormValidation();
    initCollapsibleSections();
    initTabs();
    initDropdownMenus();
    
    // Afficher les messages flash avec animation
    animateFlashMessages();
});

/**
 * Initialise les datepickers pour les champs de date
 */
function initDatepickers() {
    // Vérifier si flatpickr est disponible
    if (typeof flatpickr !== 'undefined') {
        const dateInputs = document.querySelectorAll('.datepicker');
        if (dateInputs.length > 0) {
            flatpickr(dateInputs, {
                dateFormat: "Y-m-d",
                locale: "fr",
                allowInput: true,
                altInput: true,
                altFormat: "j F Y",
                disableMobile: false
            });
        }
    }
}

/**
 * Initialise les confirmations de suppression via modales Bootstrap
 */
function initDeleteConfirmations() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const itemName = this.getAttribute('data-item-name') || 'cet élément';
            
            // Créer la modale de confirmation dynamiquement
            const modalHTML = `
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-confirm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="icon-box">
                                    <i class="fas fa-times"></i>
                                </div>
                                <h4 class="modal-title" id="deleteConfirmModalLabel">Êtes-vous sûr ?</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Voulez-vous vraiment supprimer ${itemName} ? Cette action est irréversible.</p>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <a href="${url}" class="btn btn-danger">Supprimer</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Ajouter la modale au DOM
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = modalHTML;
            document.body.appendChild(modalContainer);
            
            // Afficher la modale
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
            
            // Nettoyer le DOM après la fermeture de la modale
            document.getElementById('deleteConfirmModal').addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modalContainer);
            });
        });
    });
}

/**
 * Initialise la validation de formulaire côté client
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Validation personnalisée pour les champs spécifiques
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('input', function() {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (emailRegex.test(this.value)) {
                this.setCustomValidity('');
            } else {
                this.setCustomValidity('Veuillez entrer une adresse email valide');
            }
        });
    });
    
    // Validation des mots de passe
    const passwordInputs = document.querySelectorAll('input[name="mot_de_passe"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.length < 8) {
                this.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    
    // Validation de la confirmation de mot de passe
    const confirmPasswordInputs = document.querySelectorAll('input[name="confirm_mot_de_passe"]');
    confirmPasswordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const password = this.form.querySelector('input[name="mot_de_passe"]').value;
            if (this.value !== password) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    });
}

/**
 * Initialise les sections repliables pour les formulaires longs
 */
function initCollapsibleSections() {
    const collapsibleHeaders = document.querySelectorAll('.collapsible-header');
    
    collapsibleHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.collapse-icon');
            
            // Toggle la visibilité du contenu
            if (content.style.display === 'none' || !content.style.display) {
                content.style.display = 'block';
                if (icon) icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                content.style.display = 'none';
                if (icon) icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        });
    });
}

/**
 * Initialise les onglets pour les formulaires complexes
 */
function initTabs() {
    const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');
    const tabContents = document.querySelectorAll('.tab-pane');
    const prevButtons = document.querySelectorAll('.btn-prev-tab');
    const nextButtons = document.querySelectorAll('.btn-next-tab');
    const submitButtons = document.querySelectorAll('.btn-submit');
    
    // Désactiver la navigation directe par les onglets (sauf pour le premier onglet)
    tabLinks.forEach((link, index) => {
        if (index === 0) {
            // Le premier onglet est toujours actif au chargement
            link.classList.add('active');
            const targetId = link.getAttribute('data-bs-target') || link.getAttribute('href');
            const targetContent = document.querySelector(targetId);
            if (targetContent) {
                targetContent.classList.add('show', 'active');
            }
        } else {
            // Désactiver les autres onglets
            link.classList.add('disabled');
            link.setAttribute('aria-disabled', 'true');
            
            // Empêcher la navigation directe
            link.addEventListener('click', function(e) {
                e.preventDefault();
                return false;
            });
        }
    });
    
    // Cacher le bouton d'enregistrement sur tous les onglets sauf le dernier
    submitButtons.forEach(button => {
        const tabPane = button.closest('.tab-pane');
        if (tabPane && tabPane.id !== 'commentaires') {
            button.style.display = 'none';
        }
    });
    
    // Gestion des boutons "Précédent"
    prevButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Trouver l'onglet actif actuel
            const activeTab = document.querySelector('.nav-tabs .nav-link.active');
            const activeIndex = Array.from(tabLinks).indexOf(activeTab);
            
            if (activeIndex > 0) {
                // Désactiver l'onglet actuel
                activeTab.classList.remove('active');
                const activeContent = document.querySelector('.tab-pane.active');
                activeContent.classList.remove('show', 'active');
                
                // Activer l'onglet précédent
                const prevTab = tabLinks[activeIndex - 1];
                prevTab.classList.add('active');
                const targetId = prevTab.getAttribute('data-bs-target') || prevTab.getAttribute('href');
                const targetContent = document.querySelector(targetId);
                if (targetContent) {
                    targetContent.classList.add('show', 'active');
                }
            }
        });
    });
    
    // Gestion des boutons "Suivant" avec validation
    nextButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Trouver l'onglet actif actuel
            const activeTab = document.querySelector('.nav-tabs .nav-link.active');
            const activeIndex = Array.from(tabLinks).indexOf(activeTab);
            const activeContent = document.querySelector('.tab-pane.active');
            
            // Valider que l'onglet actuel n'est pas vide
            let isValid = true;
            
            // Vérifier si c'est un onglet qui nécessite une validation
            if (activeContent.id === 'objectifs' || 
                activeContent.id === 'contenus' || 
                activeContent.id === 'moyens' || 
                activeContent.id === 'strategies') {
                
                // Vérifier si au moins un élément est sélectionné
                const checkboxes = activeContent.querySelectorAll('input[type="checkbox"]:checked');
                if (checkboxes.length === 0) {
                    isValid = false;
                    // Afficher un message d'erreur
                    let alertDiv = activeContent.querySelector('.alert-validation');
                    if (!alertDiv) {
                        alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger alert-validation mt-3';
                        alertDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> Veuillez sélectionner au moins un élément dans cette section.`;
                        activeContent.insertBefore(alertDiv, activeContent.firstChild);
                    }
                } else {
                    // Supprimer le message d'erreur s'il existe
                    const alertDiv = activeContent.querySelector('.alert-validation');
                    if (alertDiv) {
                        alertDiv.remove();
                    }
                }
            }
            
            if (isValid && activeIndex < tabLinks.length - 1) {
                // Désactiver l'onglet actuel
                activeTab.classList.remove('active');
                activeContent.classList.remove('show', 'active');
                
                // Activer l'onglet suivant
                const nextTab = tabLinks[activeIndex + 1];
                nextTab.classList.add('active');
                const targetId = nextTab.getAttribute('data-bs-target') || nextTab.getAttribute('href');
                const targetContent = document.querySelector(targetId);
                if (targetContent) {
                    targetContent.classList.add('show', 'active');
                }
            }
        });
    });
}

/**
 * Anime les messages flash
 */
function animateFlashMessages() {
    const flashMessages = document.querySelectorAll('.alert');
    
    flashMessages.forEach(message => {
        message.classList.add('fade-in');
        
        // Ajouter un bouton de fermeture si non présent
        if (!message.querySelector('.btn-close')) {
            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'btn-close';
            closeButton.setAttribute('data-bs-dismiss', 'alert');
            closeButton.setAttribute('aria-label', 'Close');
            
            message.appendChild(closeButton);
        }
        
        // Auto-fermeture après 5 secondes pour les messages de succès
        if (message.classList.contains('alert-success')) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(message);
                bsAlert.close();
            }, 5000);
        }
    });
}

/**
 * Initialisation des menus déroulants avec fonctionnalités améliorées
 */
function initDropdownMenus() {
    // Sélectionner tous les éléments dropdown
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        // Ajouter une classe active quand le menu est ouvert
        dropdown.addEventListener('show.bs.dropdown', function() {
            this.classList.add('dropdown-active');
        });
        
        // Retirer la classe active quand le menu est fermé
        dropdown.addEventListener('hide.bs.dropdown', function() {
            this.classList.remove('dropdown-active');
        });
        
        // Ajouter un effet de survol aux éléments du menu
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach(item => {
            item.addEventListener('mouseenter', function() {
                // Ajouter une classe pour l'animation au survol
                this.classList.add('dropdown-item-hover');
            });
            
            item.addEventListener('mouseleave', function() {
                // Retirer la classe d'animation
                this.classList.remove('dropdown-item-hover');
            });
        });
    });
    
    // Ajouter une fonctionnalité pour garder le focus sur l'élément actif
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link, .dropdown-item');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href.split('/').pop())) {
            link.classList.add('active');
            
            // Si c'est un élément de dropdown, activer aussi le parent
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown) {
                const dropdownToggle = parentDropdown.querySelector('.dropdown-toggle');
                if (dropdownToggle) {
                    dropdownToggle.classList.add('active');
                }
            }
        }
    });
}

/**
 * Filtrage dynamique des tableaux
 */
function initTableFilters() {
    const tableFilters = document.querySelectorAll('.table-filter');
    
    tableFilters.forEach(filter => {
        filter.addEventListener('input', function() {
            const tableId = this.getAttribute('data-table');
            const table = document.getElementById(tableId);
            
            if (!table) return;
            
            const rows = table.querySelectorAll('tbody tr');
            const searchText = this.value.toLowerCase();
            
            rows.forEach(row => {
                let found = false;
                const cells = row.querySelectorAll('td');
                
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(searchText)) {
                        found = true;
                    }
                });
                
                row.style.display = found ? '' : 'none';
            });
        });
    });
}

/**
 * Gestion des sélections multiples dans les formulaires
 */
function initMultiSelects() {
    // Vérifier si Select2 est disponible
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez...',
            allowClear: true
        });
    }
}

/**
 * Fonction pour charger dynamiquement les données liées (ex: modules d'une filière)
 */
function loadRelatedData(sourceSelect, targetSelect, url) {
    const sourceElement = document.getElementById(sourceSelect);
    const targetElement = document.getElementById(targetSelect);
    
    if (!sourceElement || !targetElement) return;
    
    sourceElement.addEventListener('change', function() {
        const selectedValue = this.value;
        
        if (!selectedValue) {
            // Réinitialiser le select cible
            targetElement.innerHTML = '<option value="">Sélectionnez...</option>';
            return;
        }
        
        // Construire l'URL avec le paramètre
        const apiUrl = `${url}?id=${selectedValue}`;
        
        // Faire une requête AJAX
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                // Réinitialiser le select cible
                targetElement.innerHTML = '<option value="">Sélectionnez...</option>';
                
                // Ajouter les nouvelles options
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nom || item.intitule;
                    targetElement.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données:', error);
            });
    });
}
