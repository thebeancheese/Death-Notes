<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ((int)$_SESSION["role_id"] !== 3) {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: user_status.php");
    exit();
}

$user_id = (int)$_SESSION["user_id"];
$id = (int)$_GET["id"];

$sql = "SELECT *
        FROM death_records
        WHERE record_id = ? AND applicant_user_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$record = mysqli_fetch_assoc($res);

if (!$record) {
    header("Location: user_status.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - View Record</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .row { margin: 10px 0; }
    .label { color:#bdbdbd; font-size:12px; text-transform:uppercase; letter-spacing:1px; }
  </style>
</head>
<body>

<div class="card" style="max-width:720px; text-align:left;">
  <h1 style="text-align:center;">Death Notes</h1>
  <h2 style="text-align:center;">Record Details</h2>

  <div class="row"><div class="label">Record ID</div><?php echo $record["record_id"]; ?></div>
  <div class="row"><div class="label">Deceased Name</div><?php echo htmlspecialchars($record["deceased_name"]); ?></div>
  <div class="row"><div class="label">Date of Death</div><?php echo $record["date_of_death"]; ?></div>
  <div class="row"><div class="label">Place of Death</div><?php echo htmlspecialchars($record["place_of_death"]); ?></div>
  <div class="row"><div class="label">Cause of Death</div><?php echo htmlspecialchars($record["cause_of_death"]); ?></div>
  <div class="row"><div class="label">Informant Name</div><?php echo htmlspecialchars($record["informant_name"]); ?></div>
  <div class="row"><div class="label">Relationship</div><?php echo htmlspecialchars($record["relationship"]); ?></div>
  <div class="row"><div class="label">Status</div><?php echo htmlspecialchars($record["status"]); ?></div>
  <div class="row"><div class="label">Date Submitted</div><?php echo $record["date_submitted"]; ?></div>

  <div style="text-align:center; margin-top:14px;">
    <a class="smallbtn" href="user_status.php">Back to Status List</a>
  </div>
</div>

</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
