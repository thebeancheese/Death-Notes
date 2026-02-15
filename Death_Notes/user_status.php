<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Only USER role (3)
if ((int)$_SESSION["role_id"] !== 3) {
    header("Location: dashboard.php");
    exit();
}

$user_id = (int)$_SESSION["user_id"];

$sql = "SELECT record_id, deceased_name, status, date_submitted
        FROM death_records
        WHERE applicant_user_id = ?
        ORDER BY date_submitted DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - My Status</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .wrap { width: 92%; max-width: 980px; margin: 20px auto; }
    table { width:100%; border-collapse:collapse; background:#1a1a1a; border:1px solid #2a2a2a; }
    th, td { padding:12px; border-bottom:1px solid #2a2a2a; text-align:left; }
    th { color:#fff; }
  </style>
</head>
<body>

<div class="wrap">
  <div class="card" style="max-width:none; text-align:left;">
    <h1 style="text-align:center;">Death Notes</h1>
    <h2 style="text-align:center;">My Application Status</h2>

    <div style="text-align:center; margin-bottom:12px;">
      <a class="smallbtn" href="dashboard.php">Back</a>
    </div>

    <table>
      <tr>
        <th>ID</th>
        <th>Deceased Name</th>
        <th>Status</th>
        <th>Date Submitted</th>
        <th>Action</th>
      </tr>

      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?php echo $row["record_id"]; ?></td>
          <td><?php echo htmlspecialchars($row["deceased_name"]); ?></td>
          <td><?php echo htmlspecialchars($row["status"]); ?></td>
          <td><?php echo $row["date_submitted"]; ?></td>
          <td><a href="user_record_view.php?id=<?php echo $row["record_id"]; ?>">View</a></td>
        </tr>
      <?php } ?>
    </table>

  </div>
</div>

</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
