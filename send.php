<?php
// To install from scratch (to create composer files): composer require phpmailer/phpmailer
// To install based on composer files: composer install

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$config = parse_ini_file('config/config.ini');

$mail = new PHPMailer();

$mail->isSMTP();
$mail->Host = $config['mailtrap_host'];
$mail->SMTPAuth = true;
$mail->Username = $config['mailtrap_user'];
$mail->Password = $config['mailtrap_password'];
$mail->SMTPSecure = 'tls';
$mail->Port = 2525;

$mail->setFrom('from@example.com', 'First Last');
$mail->addReplyTo('towho@example.com', 'John Doe');
$mail->addAddress('helpdesk@company.com', 'Helpdesk');

$mail->isHTML(true);

$mail->Subject = "PHPMailer SMTP test";
$mail->addEmbeddedImage('logo.png', 'logo');
$mail->Body = '<img src="cid:logo"> Mail body in HTML';
$mail->AltBody = 'This is the plain text version of the email content';

if (!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
echo PHP_EOL;
