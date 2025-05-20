<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'gestion_ecoles';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $conn = new PDO($dsn, $username, $password, $options);

    // Configuration de l'admin
    $admin_data = [
        'nom' => 'Admin',
        'prenom' => 'System',
        'email' => 'admin@system.com',
        'mot_de_passe' => password_hash('1234', PASSWORD_DEFAULT),
        'statut' => 'actif',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Insertion dans la table utilisateur
    $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, statut, created_at, updated_at) 
            VALUES (:nom, :prenom, :email, :mot_de_passe, :statut, :created_at, :updated_at)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nom', $admin_data['nom']);
    $stmt->bindParam(':prenom', $admin_data['prenom']);
    $stmt->bindParam(':email', $admin_data['email']);
    $stmt->bindParam(':mot_de_passe', $admin_data['mot_de_passe']);
    $stmt->bindParam(':statut', $admin_data['statut']);
    $stmt->bindParam(':created_at', $admin_data['created_at']);
    $stmt->bindParam(':updated_at', $admin_data['updated_at']);
    
    if ($stmt->execute()) {
        // Récupérer l'ID de l'utilisateur inséré
        $id_utilisateur = $conn->lastInsertId();

        // ID du rôle (5 pour admin)
        $id_role = 5;

        // Insertion dans la table utilisateur_roles
        $sql_roles = "INSERT INTO utilisateur_roles (id_utilisateur, id_roles, created_at) 
                      VALUES (:id_utilisateur, :id_roles, :created_at)";
        
        $stmt_roles = $conn->prepare($sql_roles);
        $stmt_roles->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt_roles->bindParam(':id_roles', $id_role);
        $stmt_roles->bindParam(':created_at', $admin_data['created_at']);
        
        if ($stmt_roles->execute()) {
            echo "L'utilisateur administrateur a été créé avec succès et le rôle a été attribué !\n";
            echo "Email: admin@system.com\n";
            echo "Mot de passe: 1234\n";
        } else {
            echo "Erreur lors de l'attribution du rôle à l'administrateur.\n";
        }
    } else {
        echo "Erreur lors de la création de l'administrateur.\n";
    }

} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

$conn = null;
exit(0);