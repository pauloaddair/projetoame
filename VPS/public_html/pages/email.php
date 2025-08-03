<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
var_dump(class_exists('PHPMailer\PHPMailer\PHPMailer'));

// require './vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Configuração do servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'mail.projetoame.org'; // Ex: smtp.projetoame.org ou SMTP do seu provedor
    $mail->SMTPAuth   = true;
    $mail->Username   = 'pauloadd@projetoame.org'; // Seu e-mail completo
    $mail->Password   = 'PittJusto@3802'; // Sua senha de e-mail
    $mail->SMTPSecure = 'tls'; // Ou 'ssl' dependendo do provedor
    $mail->Port       = 587; // 587 para TLS, 465 para SSL

    // Remetente e destinatário
    $mail->setFrom('noreply@projetoame.org', 'Seu Nome');
    $mail->addAddress('pauloadd@gmail.com', 'Nome do Destinatário');

    // Conteúdo do e-mail
    $mail->isHTML(true);
    $mail->Subject = 'Assunto do E-mail';
    $mail->Body    = 'Conteúdo em <b>HTML</b>';
    $mail->AltBody = 'Conteúdo em texto simples';

    $mail->send();
    echo 'E-mail enviado com sucesso!';
} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
?>
