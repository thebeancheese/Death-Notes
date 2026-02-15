<?php
// includes/db.php

$conn = mysqli_connect("localhost", "root", "", "deathnotes");
if (!$conn) {
    die("DB connection failed: " . mysqli_connect_error());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper (optional)
if (!function_exists("roleName")) {
    function roleName($rid) {
        $rid = (int)$rid;
        if ($rid === 1) return "Admin";
        if ($rid === 2) return "Staff";
        return "User";
    }
}
?>
