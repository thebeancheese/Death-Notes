<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role_id = (int)$_SESSION["role_id"];
$user_id = (int)$_SESSION["user_id"];

// Users can't access
if ($role_id === 3) {
    header("Location: dashboard.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: death_records_list.php");
    exit();
}

$id = (int)$_GET["id"];
$message = "";

// Fetch record (Admin sees all, Staff sees all as well if you want; keep all for now)
$sql = "SELECT
            dr.*,
            u.full_name AS creator_name
        FROM death_records dr
        JOIN users u ON dr.created_by = u.user_id
        WHERE dr.record_id = ?
        LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$record = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$record) {
    header("Location: death_records_list.php");
    exit();
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_status = $_POST["status"];

    $allowed = ["Pending", "Verified", "Approved", "Rejected"];
    if (!in_array($new_status, $allowed)) {
        $message = "Invalid status.";
    } else {
        $upd = mysqli_prepare($conn, "UPDATE death_records SET status = ? WHERE record_id = ?");
        mysqli_stmt_bind_param($upd, "si", $new_status, $id);

        if (mysqli_stmt_execute($upd)) {
            $message = "Status updated successfully!";

            // refresh record values
            $record["status"] = $new_status;
        } else {
            $message = "Error updating status: " . mysqli_error($conn);
        }
        mysqli_stmt_close($upd);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Record View</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .row { margin: 10px 0; }
    .label { color:#bdbdbd; font-size:12px; text-transform:uppercase; letter-spacing:1px; }
  </style>
</head>
<body>

<div class="card" style="max-width:760px; text-align:left;">
  <h1 style="text-align:center;">Death Notes</h1>
  <h2 style="text-align:center;">Record Details</h2>

  <?php if ($message != "") echo "<p style='color:lightgreen; text-align:center;'>$message</p>"; ?>

  <div class="row"><div class="label">Record ID</div><?php echo $record["record_id"]; ?></div>
  <div class="row"><div class="label">Deceased Name</div><?php echo htmlspecialchars($record["deceased_name"]); ?></div>
  <div class="row"><div class="label">Date of Death</div><?php echo $record["date_of_death"]; ?></div>
  <div class="row"><div class="label">Place of Death</div><?php echo htmlspecialchars($record["place_of_death"]); ?></div>
  <div class="row"><div class="label">Cause of Death</div><?php echo htmlspecialchars($record["cause_of_death"]); ?></div>
  <div class="row"><div class="label">Informant Name</div><?php echo htmlspecialchars($record["informant_name"]); ?></div>
  <div class="row"><div class="label">Relationship</div><?php echo htmlspecialchars($record["relationship"]); ?></div>
  <div class="row"><div class="label">Created By</div><?php echo htmlspecialchars($record["creator_name"]); ?></div>
  <div class="row"><div class="label">Date Submitted</div><?php echo $record["date_submitted"]; ?></div>

  <hr style="border:1px solid #2a2a2a; margin:16px 0;">

  <div class="row">
    <div class="label">Status</div>

    <form method="POST">
      <select name="status" required>
        <?php
          $statuses = ["Pending", "Verified", "Approved", "Rejected"];
          foreach ($statuses as $s) {
              $sel = ($record["status"] === $s) ? "selected" : "";
              echo "<option value=\"$s\" $sel>$s</option>";
          }
        ?>
      </select>

      <button type="submit">Update Status</button>
    </form>
  </div>

  <div style="text-align:center; margin-top:14px;">
    <a class="smallbtn" href="death_records_list.php">Back to List</a>
  </div>
</div>

</body>
</html>
