<?php
require_once 'includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["role_id"] != 1) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST["fullname"]);
    $email    = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    // Admin creates STAFF only (role_id = 2)
    $role_id = 2;

    if ($fullname == "" || $email == "" || $password == "") {
        $message = "Please fill out all fields.";
    } else {
        $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $q = "INSERT INTO users (full_name, email, password, role_id, status)
                  VALUES ('$fullname', '$email', '$hashed', $role_id, 'active')";
            if (mysqli_query($conn, $q)) {
                $message = "Staff account created successfully!";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Admin Create Staff</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="card">
    <h1>Death Notes</h1>
    <h2>Admin: Create Staff</h2>

    <?php if ($message != "") echo "<p style='color:lightgreen; text-align:center;'>$message</p>"; ?>

    <form method="POST">
      <input type="text" name="fullname" placeholder="Staff Full Name" required>
      <input type="email" name="email" placeholder="Staff Email" required>
      <input type="password" name="password" placeholder="Staff Password" required>
      <button type="submit">Create Staff</button>
    </form>

    <a class="link" href="dashboard.php">Back to Dashboard</a>
  </div>
</body>
</html>
