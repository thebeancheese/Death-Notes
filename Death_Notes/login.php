<?php
require_once 'includes/db.php';

$message = "";

if (isset($_GET["registered"])) {
    $message = "Registration successful. Please login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $pass  = $_POST["password"];

    $q = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $res = mysqli_query($conn, $q);

    if ($res && mysqli_num_rows($res) == 1) {
        $u = mysqli_fetch_assoc($res);

        if ($u["status"] !== "active") {
            $message = "Account is not active.";
        } else if (password_verify($pass, $u["password"])) {
            $_SESSION["user_id"] = (int)$u["user_id"];
            $_SESSION["full_name"] = $u["full_name"];
            $_SESSION["role_id"] = (int)$u["role_id"];

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Death Notes - Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="card">
    <h1>Death Notes</h1>
    <h2>Login</h2>

    <?php if ($message != "") echo "<p style='color:lightgreen; text-align:center;'>$message</p>"; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>

    <a class="link" href="register.php">Create a User account</a>
  </div>
</body>
</html>
