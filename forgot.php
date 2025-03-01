


<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '
profile.php'; // If using Composer
// require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                 // Set mailer to use SMTP
    $mail->Host       = 'smtp.example.com';         // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                        // Enable SMTP authentication
    $mail->Username   = 'your_email@example.com';   // SMTP username
    $mail->Password   = 'your_password';             // SMTP password
    $mail->SMTPSecure = 'tls';                        // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587;                         // TCP port to connect to

    //Recipients
    $mail->setFrom('sa6382927@gmail.com', 'Mailer');
    $mail->addAddress('recipient@example.com');     // Add a recipient

    // Content
    $mail->isHTML(true);                             // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

// request_reset.php
session_start();
// Include database connection
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Get the email from the form

    // Check if email exists in the users table
    $stmt = $conn->prepare("SELECT id FROM usertable WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a reset token
        $token = bin2hex(random_bytes(16));

        // Insert token into password_resets table
        $stmt = $conn->prepare("INSERT INTO `password-reset` (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        // Send email with reset link
        $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;
        $to = $email;
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: " . $resetLink;
        $headers = "From: no-reply@yourdomain.com\r\n";

        mail($to, $subject, $message, $headers);

        echo "A password reset link has been sent to your email address.";
    } else {
        echo "No account found with that email address.";
    }

    $stmt->close();
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Request Password Reset</button>
</form>
<?php
// reset_password.php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'] ?? ''; // Use null coalescing operator to prevent undefined index warning
    $newPassword = $_POST['password'] ?? ''; // Use null coalescing operator to prevent undefined index warning

    // Validate the token
    $stmt = $conn->prepare("SELECT email FROM `password-reset` WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        // Update the user's password
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE usertable SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $newPasswordHash, $email);
        $stmt->execute();

        // Remove the used token
        $stmt = $conn->prepare("DELETE FROM `password-reset` WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo "Your password has been successfully reset.";
    } else {
        echo "Invalid or expired token.";
    }

    $stmt->close();
}
?>

<form method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
    <input type="password" name="password" placeholder="Enter new password" required>
    <button type="submit">Reset Password</button>
</form>
