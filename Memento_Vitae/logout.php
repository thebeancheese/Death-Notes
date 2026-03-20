<?php
require_once 'includes/db.php';
$_SESSION = [];
session_destroy();
header("Location: login.php");
exit();
?>
