<?php
require_once 'config.php';

// Use PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Mailer {
    // Send verification email to the user
    // The email contains a link with a token for verification
    public static function sendVerificationEmail($email, $token) {
        $mail = new PHPMailer(true);
        try {
            // Configure SMTP settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            // Set the sender and recipient
            // Use SMTP_USER as the sender's email address
            $mail->setFrom(SMTP_USER, 'Vinica Restaurant');
            $mail->addAddress($email);
            // Configure the email content
            // Set the email format to HTML
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->AltBody = "Click <a href='http://localhost/VINICA/verify?token=$token'>here</a> to verify your email. 
                The link expires in 1 hour. If you did not request this, please ignore this email.";
            $mail->Body = "Click <a href='http://localhost/VINICA/verify?token=$token'>here</a> to verify your email. 
                The link expires in 1 hour. If you did not request this, please ignore this email.";
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        }
    }
    // Send password recovery email to the user
    public static function sendRecoveryEmail($email, $token) {
        $mail = new PHPMailer(true);
        try {
            // Configure SMTP settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            // Set the sender and recipient
            $mail->setFrom(SMTP_USER, 'Vinica Restaurant');
            // Use SMTP_USER as the sender's email address
            $mail->addAddress($email);
            // Configure the email content
            // Set the email format to HTML
            $mail->isHTML(true);
            $mail->Subject = 'Password Recovery';
            $mail->AltBody = "Click <a href='http://localhost/VINICA/reset?token=$token'>here</a> to reset your password. 
                The link expires in 1 hour. If you did not request this, please ignore this email.";
            $mail->Body = "Click <a href='http://localhost/VINICA/reset?token=$token'>here</a> to reset your password. 
                The link expires in 1 hour. If you did not request this, please ignore this email.";
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>