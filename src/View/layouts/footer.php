        </main>
    </div>

    <footer>
        <div class="container">
            <p>© <?php echo date('Y'); ?> Gestion Écoles v2.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Utilisation de BASE_URL pour le JS -->
    <script src="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/js/script.js"></script>
</body>
</html>
