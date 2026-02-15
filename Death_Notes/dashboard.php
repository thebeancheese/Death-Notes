<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role_id = $_SESSION["role_id"];
$role_name = "User";
if ($role_id == 1) $role_name = "Admin";
if ($role_id == 2) $role_name = "Staff";
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Dashboard</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="card" style="max-width:820px; text-align:left;">
    <h1 style="text-align:center;">Death Notes</h1>
    <h2 style="text-align:center;">Dashboard</h2>

    <p class="note" style="text-align:center;">
      Welcome, <b><?php echo htmlspecialchars($_SESSION["full_name"]); ?></b> |
      Role: <b><?php echo $role_name; ?></b>
    </p>

    <div class="grid">
      <?php if ($role_id == 1) { ?>
        <div class="tile">
          <h3>Death Records</h3>
          <p>View all records and manage submissions.</p>
          <a class="smallbtn" href="death_records_list.php">Open Records List</a>
          <a class="smallbtn" href="death_records_add.php" style="margin-left:8px;">Add Record</a>
        </div>

        <div class="tile">
          <h3>Admin Tools</h3>
          <p>Create Staff accounts securely (no public role selection).</p>
          <a class="smallbtn" href="admin_create_account.php">Create Staff Account</a>
        </div>

      <?php } elseif ($role_id == 2) { ?>
        <div class="tile">
          <h3>Staff: Death Records</h3>
          <p>Create records and view the records you submitted.</p>
          <a class="smallbtn" href="death_records_add.php">Add Record</a>
          <a class="smallbtn" href="death_records_list.php" style="margin-left:8px;">Records List</a>
        </div>

      <?php } else { ?>
        <div class="tile">
          <h3>My Application Status</h3>
          <p>View your submitted records and their current status. View only.</p>
          <a class="smallbtn" href="user_status.php">View My Status</a>
        </div>
      <?php } ?>
    </div>

    <div style="margin-top:16px; text-align:center;">
      <a class="smallbtn" href="logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
