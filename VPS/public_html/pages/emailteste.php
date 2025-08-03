<?php
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
$mail = new PHPMailer;
$mail->isSMTP();
$mail->SMTPDebug = 2;
$mail->Host = 'mail.projetoame.org';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = 'noreply@projetoame.org';
$mail->Password = 'PittJusto@3802';
$mail->setFrom('noreply@projetoame.org', 'Projeto AME');
$mail->addReplyTo('pauloadd@gmail.com', 'Paulo Addair');
$mail->addAddress('pauloadd@hotmail.com', 'Paulo Addair');
$mail->Subject = 'Checking if PHPMailer works';
//$mail->msgHTML(file_get_contents('message.html'), __DIR__);
$mail->Body = 'This is just a plain text message body';
//$mail->addAttachment('attachment.txt');
if (!$mail->send()) {
echo 'Mailer Error:'. $mail->ErrorInfo;
} else {
echo 'The email message was sent.';
}
?>