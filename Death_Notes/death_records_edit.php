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

if (!isset($_GET["id"])) {
    header("Location: death_records_list.php");
    exit();
}

$id = (int)$_GET["id"];
$message = "";

// Load record
$stmt = mysqli_prepare($conn, "SELECT * FROM death_records WHERE record_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$record = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$record) {
    header("Location: death_records_list.php");
    exit();
}

// Update record
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $deceased_name  = trim($_POST["deceased_name"]);
    $date_of_death  = $_POST["date_of_death"];
    $place_of_death = trim($_POST["place_of_death"]);
    $cause_of_death = trim($_POST["cause_of_death"]);
    $informant_name = trim($_POST["informant_name"]);
    $relationship   = trim($_POST["relationship"]);

    if ($deceased_name=="" || $date_of_death=="" || $place_of_death=="" || $cause_of_death=="" || $informant_name=="" || $relationship=="") {
        $message = "Please fill out all fields.";
    } else {
        $upd = mysqli_prepare($conn,
            "UPDATE death_records
             SET deceased_name=?, date_of_death=?, place_of_death=?, cause_of_death=?, informant_name=?, relationship=?
             WHERE record_id=?"
        );
        mysqli_stmt_bind_param($upd, "ssssssi",
            $deceased_name, $date_of_death, $place_of_death, $cause_of_death, $informant_name, $relationship, $id
        );

        if (mysqli_stmt_execute($upd)) {
            $message = "Record updated successfully!";

            // refresh record values for display
            $record["deceased_name"] = $deceased_name;
            $record["date_of_death"] = $date_of_death;
            $record["place_of_death"] = $place_of_death;
            $record["cause_of_death"] = $cause_of_death;
            $record["informant_name"] = $informant_name;
            $record["relationship"] = $relationship;
        } else {
            $message = "Update failed: " . mysqli_error($conn);
        }
        mysqli_stmt_close($upd);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Edit Record</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="card" style="max-width:760px; text-align:left;">
  <h1 style="text-align:center;">Death Notes</h1>
  <h2 style="text-align:center;">Edit Record</h2>

  <?php if ($message != "") echo "<p style='color:lightgreen; text-align:center;'>$message</p>"; ?>

  <form method="POST">
    <input type="text" name="deceased_name" required
      value="<?php echo htmlspecialchars($record['deceased_name']); ?>" placeholder="Deceased Name">

    <input type="date" name="date_of_death" required
      value="<?php echo htmlspecialchars($record['date_of_death']); ?>">

    <input type="text" name="place_of_death" required
      value="<?php echo htmlspecialchars($record['place_of_death']); ?>" placeholder="Place of Death">

    <input type="text" name="cause_of_death" required
      value="<?php echo htmlspecialchars($record['cause_of_death']); ?>" placeholder="Cause of Death">

    <input type="text" name="informant_name" required
      value="<?php echo htmlspecialchars($record['informant_name']); ?>" placeholder="Informant Name">

    <input type="text" name="relationship" required
      value="<?php echo htmlspecialchars($record['relationship']); ?>" placeholder="Relationship">

    <button type="submit">Save Changes</button>
  </form>

  <div style="text-align:center; margin-top:14px;">
    <a class="smallbtn" href="death_records_list.php">Back to List</a>
    <a class="smallbtn" href="record_view.php?id=<?php echo $record['record_id']; ?>" style="margin-left:8px;">View</a>
  </div>
</div>

</body>
</html>
