<?php
require_once 'includes/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST["fullname"]);
    $email    = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    // Public registration always USER
    $role_id = 3;

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
                header("Location: login.php?registered=1");
                exit();
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
  <title>Death Notes - Register</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="card">
    <h1>Death Notes</h1>
    <h2>Create Account (User)</h2>

    <div class="note">This form creates a <b>User</b> account only.</div>

    <?php if ($message != "") echo "<p style='color:red; text-align:center;'>$message</p>"; ?>

    <form method="POST">
      <input type="text" name="fullname" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>

    <a class="link" href="login.php">Already have an account? Login</a>
  </div>
</body>
</html>
