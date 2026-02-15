<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role_id = (int)$_SESSION["role_id"];
$user_id = (int)$_SESSION["user_id"];

if ($role_id === 3) {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: death_records_list.php");
    exit();
}

$id = (int)$_GET["id"];

// Load record (and check permission for staff)
if ($role_id === 1) {
    $sql = "SELECT dr.record_id, dr.deceased_name, dr.created_by, u.full_name AS creator_name
            FROM death_records dr
            JOIN users u ON dr.created_by = u.user_id
            WHERE dr.record_id = ?
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
} else {
    $sql = "SELECT dr.record_id, dr.deceased_name, dr.created_by, u.full_name AS creator_name
            FROM death_records dr
            JOIN users u ON dr.created_by = u.user_id
            WHERE dr.record_id = ? AND dr.created_by = ?
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$record = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$record) {
    header("Location: death_records_list.php");
    exit();
}

// If confirmed delete
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($role_id === 1) {
        $del = mysqli_prepare($conn, "DELETE FROM death_records WHERE record_id = ?");
        mysqli_stmt_bind_param($del, "i", $id);
    } else {
        $del = mysqli_prepare($conn, "DELETE FROM death_records WHERE record_id = ? AND created_by = ?");
        mysqli_stmt_bind_param($del, "ii", $id, $user_id);
    }

    mysqli_stmt_execute($del);
    mysqli_stmt_close($del);

    header("Location: death_records_list.php?deleted=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Delete Record</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="card" style="max-width:720px; text-align:left;">
  <h1 style="text-align:center;">Death Notes</h1>
  <h2 style="text-align:center;">Delete Record</h2>

  <p class="note" style="text-align:center;">
    You are about to delete:
  </p>

  <div style="margin-top:12px;">
    <b>ID:</b> <?php echo $record["record_id"]; ?><br>
    <b>Deceased Name:</b> <?php echo htmlspecialchars($record["deceased_name"]); ?><br>
    <b>Created By:</b> <?php echo htmlspecialchars($record["creator_name"]); ?><br>
  </div>

  <p style="color:#ff6b6b; margin-top:14px;">
    This action cannot be undone.
  </p>

  <form method="POST">
    <button type="submit">Yes, Delete</button>
  </form>

  <div style="text-align:center; margin-top:12px;">
    <a class="smallbtn" href="death_records_list.php">Cancel</a>
  </div>
</div>

</body>
</html>
