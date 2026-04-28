<?php
include_once('./include/funcoes.php');
// include_once('./include/conexao.php');
// include_once('./include/head.php');

$msg = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email_antigo = isset($_POST['email_antigo']) ? strtolower(trim($_POST['email_antigo'])) : '';
    $email_novo = isset($_POST['email_novo']) ? strtolower(trim($_POST['email_novo'])) : '';

    if (!empty($email_antigo) && !empty($email_novo)) {
        // Verifica se o e-mail antigo existe
        $query_antigo = "SELECT candidato_id, nome FROM candidatos WHERE Email LIKE ?";
        $stmt_antigo = mysqli_prepare($conexao, $query_antigo);
        mysqli_stmt_bind_param($stmt_antigo, 's', $email_antigo);
        mysqli_stmt_execute($stmt_antigo);
        $result_antigo = mysqli_stmt_get_result($stmt_antigo);

        if (mysqli_num_rows($result_antigo) > 0) {
            $row = mysqli_fetch_assoc($result_antigo);
            $candidato_id = $row['candidato_id'];
            $nome_candidato = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $row['nome']);
            if (!$nome_candidato) $nome_candidato = $row['nome']; // Fallback

            // Testa se novo email já existe
            $query_novo = "SELECT candidato_id FROM candidatos WHERE Email LIKE ?";
            $stmt_novo = mysqli_prepare($conexao, $query_novo);
            mysqli_stmt_bind_param($stmt_novo, 's', $email_novo);
            mysqli_stmt_execute($stmt_novo);
            $result_novo = mysqli_stmt_get_result($stmt_novo);
            
            if (mysqli_num_rows($result_novo) > 0) {
                 $msg = "<p class='text-center'><span class='mt-1 p-2 bg-warning rounded-pill text-dark'>O novo e-mail informado já está cadastrado para outro atendente.</span></p>";
                 $status = "error";
            } else {
                // Efetua a atualização
                $update_query = "UPDATE candidatos SET Email = ? WHERE candidato_id = ?";
                $stmt_update = mysqli_prepare($conexao, $update_query);
                mysqli_stmt_bind_param($stmt_update, 'si', $email_novo, $candidato_id);
                
                if (mysqli_stmt_execute($stmt_update)) {
                    $msg = "<h2 class='text-center'>Atendente: " . htmlspecialchars($nome_candidato, ENT_QUOTES, 'UTF-8') . "</h2>";
                    $msg .= "<p class='mt-1 p-2 text-center bg-success rounded-pill text-white'>E-mail atualizado com sucesso!</p>";
                    $msg .= "<p class='text-center'>Você já pode usar seu novo e-mail para <a href='/atendentes' class='btn btn-sm btn-primary'>Acessar aos Agendamentos</a></p>";
                    $status = "success";
                    
                    // SEND NOTIFICATION EMAIL TO ADMIN
                    require before('/public_html',__DIR__) . '/vendor/autoload.php';
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'mail.projetoame.org';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'noreply@projetoame.org';
                        $mail->Password   = 'PittJusto@3802';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;
                        
                        $from = "noreply@projetoame.org";
                        $to = "admin@projetoame.org";
                        $mail->setFrom($from, 'Sistema AME');
                        $mail->addAddress($to);
                        $mail->addCC('pauloadd@novaeratec.com.br'); // Keeping paulo on CC just in case

                        $mail->isHTML(true);
                        $mail->Subject = "Atualizacao de E-mail: " . $nome_candidato;

                        $body = "<h2>Alerta de Seguran&ccedil;a - Altera&ccedil;&atilde;o de E-mail</h2>";
                        $body .= "<p>O seguinte atendente atualizou seu e-mail no sistema:</p>";
                        $body .= "<ul>";
                        $body .= "<li><strong>Nome:</strong> " . $nome_candidato . "</li>";
                        $body .= "<li><strong>E-mail Antigo:</strong> " . $email_antigo . "</li>";
                        $body .= "<li><strong>Novo E-mail:</strong> " . $email_novo . "</li>";
                        $body .= "</ul>";
                        $body .= "<hr><p><strong>Dados de Auditoria:</strong></p>";
                        $body .= "<ul><li><strong>IP:</strong> " . $_SERVER['SERVER_ADDR'] . "</li>";
                        $body .= "<li><strong>Navegador:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "</li>";
                        $body .= "<li><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</li></ul>";

                        $mail->Body = $body;
                        $mail->send();
                        
                    } catch (Exception $e) {
                         // We don't want to alert the user of this, just fail silently on email side as the DB updated fine.
                         error_log("Erro ao enviar email de notificacao: " . $mail->ErrorInfo);
                    }

                } else {
                    $msg = "<p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Erro ao atualizar no banco de dados. Tente novamente.</span></p>";
                    $status = "error";
                }
            }
        } else {
            $msg = "<h2 class='text-center'>Atendente não encontrado</h2>";
            $msg .= "<p class='text-center'><span class='mt-1 p-2 bg-danger rounded-pill text-white'>Não encontramos o seu E-mail Antigo na base de dados.</span><p class='text-center'>Verifique se digitou corretamente. Se você ainda não é inscrito, clique <a href='https://projetoame.org/inscrever' class='btn btn-sm btn-primary'>AQUI</a></p>";
            $status = "error";
        }
    } else {
        $msg = "<p class='text-center'><span class='mt-1 p-2 bg-warning rounded-pill text-dark'>Por favor, preencha ambos os campos.</span></p>";
        $status = "error";
    }
}
?>
<body>
    <div class="container">
        <header>
            <h1 class="text-center mt-3">Atualizar E-mail de Acesso</h1>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/atendentes">Atividades</a></li>
                <li class="breadcrumb-item"><a href="/rodizio">Rodizio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Atualizar E-mail</li>
              </ol>
            </nav>
        </header>

        <div class="row mt-3 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Caixa de Mensagens -->
                <?php if (!empty($msg)): ?>
                    <div class="mb-4">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulário: Só exibe se não houve sucesso -->
                <?php if ($status !== "success"): ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-center text-muted mb-4">Caso você tenha mudado de endereço de e-mail e não consiga acessar o painel de atividades, informe seus dados abaixo para atualizarmos seu cadastro.</p>
                        
                        <form method="post" action="/atualizar_email">
                            <div class="md-form">
                                <i class="fas fa-envelope prefix grey-text"></i>
                                <input type="email" id="email_antigo" name="email_antigo" class="form-control" required
                                       <?php if(isset($_POST['email_antigo']) && $status !== "success") { echo " value='".htmlspecialchars($_POST['email_antigo'], ENT_QUOTES, 'UTF-8')."'"; } ?>>
                                <label for="email_antigo">E-mail Antigo (o que você usava antes)</label>
                            </div>

                            <div class="md-form mt-4">
                                <i class="fas fa-envelope-open prefix grey-text"></i>
                                <input type="email" id="email_novo" name="email_novo" class="form-control" required
                                       <?php if(isset($_POST['email_novo']) && $status !== "success") { echo " value='".htmlspecialchars($_POST['email_novo'], ENT_QUOTES, 'UTF-8')."'"; } ?>>
                                <label for="email_novo">Novo E-mail</label>
                            </div>

                            <div class="text-center mt-4">
                                <button class="btn btn-block rounded-pill btn-primary" type="submit">Atualizar Meu E-mail</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php
include_once('./include/footer.php');
?>
</body>
<?php
include_once('./include/scripts.php');
?>
<?php include_once('./include/end.php'); ?>
