<?php
require_once 'config.php'; // use your PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['passkey'])) {
    $newPassword = trim($_POST['passkey']);

    try {
        // Get current password first
        $stmt = $pdo->query("SELECT current_password FROM key_cabinet_password WHERE id = 1");
        $row = $stmt->fetch();

        if ($row) {
            $currentPassword = $row['current_password'];

            // Update table: current -> previous, new -> current
            $update = $pdo->prepare("UPDATE key_cabinet_password 
                                     SET previous_password = :currentPassword,
                                         current_password = :newPassword,
                                         last_updated = NOW()
                                     WHERE id = 1");
            $update->execute(array(
                'currentPassword' => $currentPassword,
                'newPassword' => $newPassword
            ));

            header("Location: index.php?status=success");
            exit;
        } else {
            die("❌ No record found.");
        }
    } catch (PDOException $e) {
        die("❌ DB Error: " . $e->getMessage());
    }
} else {
    die("❌ Invalid request.");
}
?>
