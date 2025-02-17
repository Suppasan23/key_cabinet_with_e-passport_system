<?php
session_start();
session_destroy();
$basePath = dirname($_SERVER['SCRIPT_NAME']); /* echo $basePath; */ 
header("refresh: 0; url=$basePath");
?>
