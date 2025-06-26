<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php'; // Composer autoload

function sendVerificationEmail($email, $token)
{
    $mail = new PHPMailer(true);

    try {
        // Cấu hình SMTP (điền info gmail của bạn)
        $mail->isSMTP();
        $mail->SMTPKeepAlive = true;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'no.reply.vgtutor@gmail.com';
        $mail->Password = 'psxp ijkl mlsr lmrw';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        $mail->setFrom('no.reply.vgtutor@gmail.com', 'VGtUtor');
        $mail->addAddress($email);

        // Build dynamic base URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $protocol . $host;

        $verifyLink = $baseUrl . "/vgtutor/verifyEmail/verify.php?email=" . urlencode($email) . "&token=" . $token;

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification - VGtUtor';
        $mail->Body =  'Hello,<br>
                        Please click the link below to verify your account:<br>
                        <a href="' . $verifyLink . '">
                        Verify Account
                        </a>
                        <br>Best regards,<br>VGtUtor Team';
        $mail->send();
        return true;
    } catch (Exception $e) {
        // echo "Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
