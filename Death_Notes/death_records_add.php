<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// User (role 3) cannot add
if ((int)$_SESSION["role_id"] === 3) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

// Load all USERS (role_id = 3) for applicant dropdown
$user_list = [];
$u_res = mysqli_query($conn, "SELECT user_id, full_name, email FROM users WHERE role_id = 3 ORDER BY full_name ASC");
if ($u_res) {
    while ($u = mysqli_fetch_assoc($u_res)) {
        $user_list[] = $u;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $deceased_name  = trim($_POST["deceased_name"]);
    $date_of_death  = $_POST["date_of_death"];
    $place_of_death = trim($_POST["place_of_death"]);
    $cause_of_death = trim($_POST["cause_of_death"]);
    $informant_name = trim($_POST["informant_name"]);
    $relationship   = trim($_POST["relationship"]);

    $applicant_user_id = (int)$_POST["applicant_user_id"]; // the user who will track status
    $created_by        = (int)$_SESSION["user_id"];         // staff/admin who encoded it

    // Validate applicant is a real USER role
    $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE user_id = ? AND role_id = 3");
    mysqli_stmt_bind_param($check, "i", $applicant_user_id);
    mysqli_stmt_execute($check);
    $check_res = mysqli_stmt_get_result($check);
    $valid_user = mysqli_fetch_assoc($check_res);
    mysqli_stmt_close($check);

    if (!$valid_user) {
        $message = "Please select a valid User applicant.";
    } else if ($deceased_name=="" || $date_of_death=="" || $place_of_death=="" || $cause_of_death=="" || $informant_name=="" || $relationship=="") {
        $message = "Please fill out all fields.";
    } else {
        $sql = "INSERT INTO death_records
                (deceased_name, date_of_death, place_of_death, cause_of_death, informant_name, relationship,
                 applicant_user_id, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssii",
            $deceased_name, $date_of_death, $place_of_death, $cause_of_death,
            $informant_name, $relationship, $applicant_user_id, $created_by
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: death_records_list.php?added=1");
            exit();
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Add Record</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="card" style="max-width:720px; text-align:left;">
    <h1 style="text-align:center;">Death Notes</h1>
    <h2 style="text-align:center;">Add Death Record</h2>

    <?php if ($message != "") echo "<p style='color:red; text-align:center;'>$message</p>"; ?>

    <form method="POST" onsubmit="return confirm('Are you sure you want to save this record?');">

      <label class="note">Applicant (User)</label>
      <select name="applicant_user_id" required>
        <option value="">Select User...</option>
        <?php foreach ($user_list as $u) { ?>
          <option value="<?php echo $u['user_id']; ?>">
            <?php echo htmlspecialchars($u['full_name'] . " (" . $u['email'] . ")"); ?>
          </option>
        <?php } ?>
      </select>

      <input type="text" name="deceased_name" placeholder="Deceased Name" required>
      <input type="date" name="date_of_death" required>
      <input type="text" name="place_of_death" placeholder="Place of Death" required>
      <input type="text" name="cause_of_death" placeholder="Cause of Death" required>
      <input type="text" name="informant_name" placeholder="Informant Name" required>
      <input type="text" name="relationship" placeholder="Relationship" required>

      <button type="submit">Save Record</button>
    </form>

    <div style="text-align:center; margin-top:12px;">
      <a class="smallbtn" href="dashboard.php">Back</a>
      <a class="smallbtn" href="death_records_list.php" style="margin-left:8px;">View Records</a>
    </div>
  </div>
</body>
</html>
