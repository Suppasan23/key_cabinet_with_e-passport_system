<?php
session_start();
session_destroy();
$basePath = dirname($_SERVER['SCRIPT_NAME']); // e.g., "/myfolder"
header("Location: $basePath/index.php");
exit;
?>
