<?php
require_once 'includes/db.php';

$message = "";
$message_type = "success";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");

    if ($email === "") {
        $message = "Please enter your email address.";
        $message_type = "error";
    } else {
        $user = findUserByEmail($email);
        if ($user && !empty($user["email_verified_at"])) {
            $reset_link = createPasswordResetLink((int)$user["user_id"]);
            [$sent, $mail_error] = deliverActionLink(
                $user["email"],
                $user["full_name"],
                    "Reset your Memento Vitae password",
                "Use the secure link below to set a new password.",
                $reset_link
            );
            if (!$sent && mailConfigured()) {
                $message = "We found the account, but the reset email could not be sent right now.";
                $message_type = "error";
            } else if (!$sent) {
                $message = "SMTP is not configured yet, so reset email sending is unavailable.";
                $message_type = "error";
            }
        }

        if ($message === "") {
            $message = "If the email exists and is verified, a reset link has been sent.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Memento Vitae - Forgot Password</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="card">
    <h1>Memento Vitae</h1>
    <h2>Forgot Password</h2>

    <?php if ($message !== "") { ?>
      <div class="alert alert-<?php echo e($message_type); ?>"><?php echo e($message); ?></div>
    <?php } ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Registered Email" value="<?php echo e($email); ?>" required>
      <button type="submit">Send Reset Link</button>
    </form>

    <a class="link" href="login.php">Back to Login</a>
  </div>
</body>
</html>
