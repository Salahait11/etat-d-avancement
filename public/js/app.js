/**
 * Fonctionnalités générales de l'application
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialisation des popovers Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Gestion des alertes auto-fermantes
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });

    // Gestion des formulaires avec validation Bootstrap
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

    // Gestion des confirmations de suppression
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', event => {
            if (!confirm(button.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    // Gestion des filtres de recherche
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        }, 500));
    });
});

/**
 * Fonction utilitaire pour limiter la fréquence d'exécution d'une fonction
 * @param {Function} func Fonction à exécuter
 * @param {number} wait Délai d'attente en millisecondes
 * @returns {Function} Fonction avec délai
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Fonction utilitaire pour formater les dates
 * @param {string} date Date à formater
 * @param {string} format Format souhaité
 * @returns {string} Date formatée
 */
function formatDate(date, format = 'DD/MM/YYYY') {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Fonction utilitaire pour afficher les messages flash
 * @param {string} message Message à afficher
 * @param {string} type Type de message (success, error, warning, info)
 */
function showFlashMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            const closeButton = alertDiv.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    }
}

// Test de chargement JavaScript
console.log('Application JavaScript chargé');
