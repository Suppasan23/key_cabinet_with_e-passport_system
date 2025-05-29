<?php
$host = 'linuxdb2.rmutsv.ac.th';
$dbname = 'eng_sql_key_cabinet_with_epassport';
$user = 'eng_sql';
$pass = '@8It;bL;dii,Lkl9iN@';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit("❌ PDO Connection failed: " . $e->getMessage());
}
?>
