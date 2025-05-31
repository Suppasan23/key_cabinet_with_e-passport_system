<?php
require_once 'config.php'; // connect to database

function getKeyCabinetPasswords($pdo) {
    try {
        $stmt = $pdo->query("SELECT current_password, previous_password FROM key_cabinet_password WHERE id = 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC); // Associative array for safe access

        return array(
            'current' => isset($row['current_password']) ? $row['current_password'] : 'N/A',
            'previous' => isset($row['previous_password']) ? $row['previous_password'] : 'N/A'
        );
    } catch (PDOException $e) {
        return array(
            'current' => 'Error',
            'previous' => 'Error'
        );
    }
}
?>
