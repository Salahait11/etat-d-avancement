// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialiser les popovers Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Gérer les formulaires de confirmation de suppression
    var deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });

    // Gérer les formulaires de recherche
    var searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var searchInput = form.querySelector('input[type="search"]');
            if (searchInput && searchInput.value.trim() === '') {
                e.preventDefault();
                alert('Veuillez entrer un terme de recherche');
            }
        });
    });

    // Gérer les formulaires de filtrage
    var filterForms = document.querySelectorAll('.filter-form');
    filterForms.forEach(function(form) {
        form.addEventListener('change', function() {
            form.submit();
        });
    });

    // Gérer les tableaux triables
    var sortableTables = document.querySelectorAll('.sortable');
    sortableTables.forEach(function(table) {
        var headers = table.querySelectorAll('th[data-sort]');
        headers.forEach(function(header) {
            header.addEventListener('click', function() {
                var column = this.dataset.sort;
                var direction = this.dataset.direction === 'asc' ? 'desc' : 'asc';
                
                // Mettre à jour la direction
                headers.forEach(function(h) {
                    h.dataset.direction = '';
                    h.classList.remove('asc', 'desc');
                });
                this.dataset.direction = direction;
                this.classList.add(direction);
                
                // Trier le tableau
                sortTable(table, column, direction);
            });
        });
    });

    // Fonction pour trier un tableau
    function sortTable(table, column, direction) {
        var tbody = table.querySelector('tbody');
        var rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort(function(a, b) {
            var aValue = a.querySelector('td[data-' + column + ']').dataset[column];
            var bValue = b.querySelector('td[data-' + column + ']').dataset[column];
            
            if (direction === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });
        
        rows.forEach(function(row) {
            tbody.appendChild(row);
        });
    }

    // Gérer les modales de confirmation
    var confirmModals = document.querySelectorAll('.confirm-modal');
    confirmModals.forEach(function(modal) {
        modal.addEventListener('show.bs.modal', function(e) {
            var button = e.relatedTarget;
            var action = button.getAttribute('data-action');
            var message = button.getAttribute('data-message');
            
            var modalBody = modal.querySelector('.modal-body');
            modalBody.textContent = message || 'Êtes-vous sûr de vouloir effectuer cette action ?';
            
            var confirmButton = modal.querySelector('.confirm-button');
            confirmButton.setAttribute('data-action', action);
        });
    });

    // Gérer les notifications toast
    var toastElements = document.querySelectorAll('.toast');
    toastElements.forEach(function(toastElement) {
        var toast = new bootstrap.Toast(toastElement);
        toast.show();
    });
}); 