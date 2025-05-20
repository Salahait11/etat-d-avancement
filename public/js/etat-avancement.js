/**
 * Gestion des onglets et validation du formulaire d'état d'avancement
 */
class EtatAvancementForm {
    constructor() {
        console.log('Initialisation du formulaire');
        this.form = document.getElementById('etatAvancementForm');
        this.tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
        this.tabContents = document.querySelectorAll('.tab-pane');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.submitBtn = document.getElementById('submitBtn');
        this.currentTabIndex = 0;

        console.log('Éléments trouvés:', {
            form: this.form,
            tabs: this.tabs.length,
            tabContents: this.tabContents.length,
            prevBtn: this.prevBtn,
            nextBtn: this.nextBtn,
            submitBtn: this.submitBtn
        });

        this.init();
    }

    init() {
        console.log('Initialisation des événements');
        this.initDatepickers();
        this.initEventListeners();
        this.showTab(0);
    }

    initDatepickers() {
        const dateInputs = document.querySelectorAll('.etat-avancement-form input[type="date"]');
        dateInputs.forEach(input => {
            if (input.value) {
                input.value = new Date(input.value).toISOString().split('T')[0];
            }
        });
    }

    initEventListeners() {
        // Gestion des boutons de navigation
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                if (this.currentTabIndex > 0) {
                    this.showTab(this.currentTabIndex - 1);
                }
            });
        }

        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                if (this.currentTabIndex < this.tabs.length - 1) {
                    this.showTab(this.currentTabIndex + 1);
                }
            });
        }

        // Gestion des clics sur les onglets
        this.tabs.forEach((tab, index) => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                this.showTab(index);
            });
        });

        // Navigation au clavier
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft' && this.currentTabIndex > 0) {
                this.showTab(this.currentTabIndex - 1);
            } else if (e.key === 'ArrowRight' && this.currentTabIndex < this.tabs.length - 1) {
                this.showTab(this.currentTabIndex + 1);
            }
        });

        // Gestion de la recherche dans les listes
        const searchInputs = document.querySelectorAll('input[id^="search"]');
        searchInputs.forEach(input => {
            input.addEventListener('input', (e) => this.filterList(e.target));
        });

        // Validation du formulaire
        if (this.form) {
            this.form.onsubmit = (e) => {
                e.preventDefault();
                if (this.validateAllTabs()) {
                    this.form.submit();
                }
            };
        }

        // Restaurer l'onglet actif au chargement
        const savedTab = localStorage.getItem('activeTab');
        if (savedTab !== null) {
            this.showTab(parseInt(savedTab));
        }
    }

    /**
     * Affiche l'onglet spécifié et met à jour les boutons de navigation
     * @param {number} index - Index de l'onglet à afficher
     */
    showTab(index) {
        if (index < 0 || index >= this.tabs.length) {
            return;
        }

        // Désactiver tous les onglets
        this.tabs.forEach(tab => {
            tab.classList.remove('active');
            tab.setAttribute('aria-selected', 'false');
        });
        
        // Désactiver tous les contenus
        this.tabContents.forEach(content => {
            content.classList.remove('active', 'show');
        });
        
        // Activer l'onglet sélectionné
        const selectedTab = this.tabs[index];
        selectedTab.classList.add('active');
        selectedTab.setAttribute('aria-selected', 'true');
        
        // Activer le contenu correspondant
        const selectedContent = this.tabContents[index];
        selectedContent.classList.add('active', 'show');
        
        // Mettre à jour l'index actuel
        this.currentTabIndex = index;
        
        // Mettre à jour la visibilité des boutons
        this.updateButtonVisibility();
        
        // Sauvegarder l'onglet actif dans le localStorage
        localStorage.setItem('activeTab', index);
        
        // Déclencher l'événement show.bs.tab de Bootstrap
        const event = new Event('show.bs.tab', { bubbles: true });
        selectedTab.dispatchEvent(event);
    }

    /**
     * Met à jour la visibilité des boutons de navigation
     */
    updateButtonVisibility() {
        // Afficher tous les boutons par défaut
        this.prevBtn.style.display = 'block';
        this.nextBtn.style.display = 'block';
        this.submitBtn.style.display = 'block';
        
        // Masquer le bouton précédent sur le premier onglet
        if (this.currentTabIndex === 0) {
            this.prevBtn.style.display = 'none';
        }
        
        // Masquer le bouton suivant sur le dernier onglet
        if (this.currentTabIndex === this.tabs.length - 1) {
            this.nextBtn.style.display = 'none';
            this.submitBtn.style.display = 'block';
        } else {
            this.submitBtn.style.display = 'none';
        }
    }

    validateCurrentTab() {
        const currentTab = this.tabContents[this.currentTabIndex];
        const requiredFields = currentTab.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                this.showError(currentTab, `Le champ ${field.getAttribute('name')} est obligatoire`);
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    validateAllTabs() {
        let isValid = true;
        const originalTabIndex = this.currentTabIndex;

        // Valider tous les onglets
        for (let i = 0; i < this.tabContents.length; i++) {
            this.showTab(i);
            if (!this.validateCurrentTab()) {
                isValid = false;
                break;
            }
        }

        // Retourner à l'onglet d'origine
        this.showTab(originalTabIndex);
        return isValid;
    }

    filterList(input) {
        const searchTerm = input.value.toLowerCase();
        const listGroup = input.closest('.tab-pane').querySelector('.list-group');
        const items = listGroup.querySelectorAll('.list-group-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    showError(tabPane, message) {
        let errorDiv = tabPane.querySelector('.alert-danger');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger mt-3';
            tabPane.insertBefore(errorDiv, tabPane.firstChild);
        }
        errorDiv.textContent = message;
    }
}

// Initialisation du formulaire
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM chargé, création du formulaire');
    new EtatAvancementForm();
}); 