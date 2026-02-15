<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$role_id = (int)$_SESSION["role_id"];
if ($role_id === 3) {
    header("Location: dashboard.php");
    exit();
}

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$added_msg = isset($_GET["added"]) ? "Record added successfully!" : "";
$deleted_msg = isset($_GET["deleted"]) ? "Record deleted successfully!" : "";

if ($search !== "") {
    $sql = "SELECT
                dr.record_id,
                dr.deceased_name,
                dr.status,
                dr.date_submitted,
                u.full_name AS creator_name,
                u.role_id   AS creator_role
            FROM death_records dr
            JOIN users u ON dr.created_by = u.user_id
            WHERE dr.deceased_name LIKE ?
            ORDER BY dr.date_submitted DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, "s", $like);
} else {
    $sql = "SELECT
                dr.record_id,
                dr.deceased_name,
                dr.status,
                dr.date_submitted,
                u.full_name AS creator_name,
                u.role_id   AS creator_role
            FROM death_records dr
            JOIN users u ON dr.created_by = u.user_id
            ORDER BY dr.date_submitted DESC";
    $stmt = mysqli_prepare($conn, $sql);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Records List</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .wrap { width: 92%; max-width: 1100px; margin: 20px auto; }
    .toprow { display:flex; gap:10px; flex-wrap:wrap; justify-content:space-between; align-items:center; }
    .toprow form { display:flex; gap:10px; flex-wrap:wrap; margin:0; }
    table { width:100%; border-collapse:collapse; background:#1a1a1a; border:1px solid #2a2a2a; }
    th, td { padding:12px; border-bottom:1px solid #2a2a2a; text-align:left; }
    th { color:#fff; }
    .msg { text-align:center; color:lightgreen; margin: 8px 0 14px; }
    .pill {
      display:inline-block;
      padding:4px 10px;
      border:1px solid #333;
      border-radius:999px;
      font-size:12px;
      color:#e5e5e5;
      background:#2a2a2a;
      margin-left:8px;
    }
    a.action { color:#ff4d4d; text-decoration:none; font-weight:700; }
    a.action:hover { text-decoration:underline; }
  </style>
</head>
<body>

<div class="wrap">
  <div class="card" style="max-width:none; text-align:left;">
    <h1 style="text-align:center;">Death Notes</h1>
    <h2 style="text-align:center;">Death Records List</h2>

    <?php
      if ($added_msg != "") echo "<div class='msg'>$added_msg</div>";
      if ($deleted_msg != "") echo "<div class='msg'>$deleted_msg</div>";
    ?>

    <div class="toprow">
      <div>
        <a class="smallbtn" href="dashboard.php">Back</a>
        <a class="smallbtn" href="death_records_add.php" style="margin-left:8px;">Add Record</a>
      </div>

      <form method="GET">
        <input type="text" name="search" placeholder="Search deceased name..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" style="width:auto; padding:10px 14px;">Search</button>
      </form>
    </div>

    <div style="margin-top:14px;">
      <table>
        <tr>
          <th>ID</th>
          <th>Deceased Name</th>
          <th>Status</th>
          <th>Date Submitted</th>
          <th>Created By</th>
          <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?php echo $row["record_id"]; ?></td>
            <td><?php echo htmlspecialchars($row["deceased_name"]); ?></td>
            <td><?php echo htmlspecialchars($row["status"]); ?></td>
            <td><?php echo $row["date_submitted"]; ?></td>
            <td>
              <?php echo htmlspecialchars($row["creator_name"]); ?>
              <span class="pill"><?php echo roleName((int)$row["creator_role"]); ?></span>
            </td>
            <td>
              <a class="action" href="record_view.php?id=<?php echo $row["record_id"]; ?>">View</a>
              &nbsp;|&nbsp;
              <a class="action" href="death_records_edit.php?id=<?php echo $row["record_id"]; ?>">Edit</a>
              &nbsp;|&nbsp;
              <a class="action" href="death_records_delete.php?id=<?php echo $row["record_id"]; ?>">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>

<?php mysqli_stmt_close($stmt); ?>
