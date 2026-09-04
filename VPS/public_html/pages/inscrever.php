<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

$titulo = "Inscrição de Atendente — Projeto AME";
$inscrito = "Novo Inscrito";
$msg = "";
$status = "";

$dados = [
    'nome' => '',
    'responsavel' => '',
    'Email' => '',
    'Telefone' => '',
    'Nascimento' => '',
    'genero' => '',
    'mensagem' => '',
    'Curriculo' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $responsavel = isset($_POST['responsavel']) ? trim($_POST['responsavel']) : '';
    $email = isset($_POST['Email']) ? strtolower(trim($_POST['Email'])) : '';
    $telefone = isset($_POST['Telefone']) ? trim($_POST['Telefone']) : '';
    $nascimento = isset($_POST['Nascimento']) ? trim($_POST['Nascimento']) : '';
    $genero = isset($_POST['genero']) ? trim($_POST['genero']) : '';
    $mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';

    $dados['nome'] = $nome;
    $dados['responsavel'] = $responsavel;
    $dados['Email'] = $email;
    $dados['Telefone'] = $telefone;
    $dados['Nascimento'] = $nascimento;
    $dados['genero'] = $genero;
    $dados['mensagem'] = $mensagem;

    if (empty($nome) || empty($responsavel) || empty($email) || empty($telefone)) {
        $msg = "<div class='alert alert-warning text-center rounded-pill shadow-sm'>Por favor, preencha todos os campos obrigatórios (Nome, Responsável, E-mail e Telefone).</div>";
        $status = "error";
    } else {
        $nome_esc = mysqli_real_escape_string($conexao, $nome);
        $resp_esc = mysqli_real_escape_string($conexao, $responsavel);
        $email_esc = mysqli_real_escape_string($conexao, $email);
        $tel_esc = mysqli_real_escape_string($conexao, $telefone);
        $nasc_esc = mysqli_real_escape_string($conexao, $nascimento);
        $gen_esc = mysqli_real_escape_string($conexao, $genero);
        $msg_esc = mysqli_real_escape_string($conexao, $mensagem);
        $ip_esc = mysqli_real_escape_string($conexao, $_SERVER['REMOTE_ADDR'] ?? '');

        // Verifica se o e-mail já existe
        $query_check = "SELECT candidato_id, nome FROM candidatos WHERE Email = '$email_esc' LIMIT 1";
        $r_check = mysqli_query($conexao, $query_check);

        if ($r_check && mysqli_num_rows($r_check) > 0) {
            // Atualiza registro existente
            $row_c = mysqli_fetch_assoc($r_check);
            $candidato_id = (int)$row_c['candidato_id'];
            
            $query_up = "UPDATE candidatos SET 
                            `nome` = '$nome_esc', 
                            `responsavel` = '$resp_esc', 
                            `Telefone` = '$tel_esc', 
                            `Nascimento` = '$nasc_esc', 
                            `genero` = '$gen_esc', 
                            `mensagem` = '$msg_esc', 
                            `IP` = '$ip_esc' 
                         WHERE candidato_id = $candidato_id";
            mysqli_query($conexao, $query_up);
            $cadastrar = 0;
        } else {
            // Novo registro: calcula próximo rodízio
            $q_rod = "SELECT (COALESCE(MAX(rodizio), 0) + 1) AS max FROM candidatos";
            $r_rod = mysqli_query($conexao, $q_rod);
            $row_rod = mysqli_fetch_assoc($r_rod);
            $rodizio = (int)$row_rod['max'];

            // Novos inscritos entram como INATIVOS (ativo = -1) para moderação prévia
            $query_in = "INSERT INTO candidatos (`inscrito`, `IP`, `rodizio`, `ativo`, `nome`, `responsavel`, `Email`, `Telefone`, `Nascimento`, `genero`, `mensagem`, `data_inscricao`) 
                         VALUES ('candidato', '$ip_esc', $rodizio, -1, '$nome_esc', '$resp_esc', '$email_esc', '$tel_esc', '$nasc_esc', '$gen_esc', '$msg_esc', NOW())";
            mysqli_query($conexao, $query_in);
            $candidato_id = mysqli_insert_id($conexao);
            $cadastrar = 1;

            // Cria / atualiza usuário
            $temp = explode(" ", $responsavel);
            $sobrenome = (count($temp) > 1) ? trim(after($temp[0], $responsavel)) : '';
            $login = function_exists('slugify') ? slugify($responsavel, '', 10) : strtolower($temp[0]);

            $query_usr = "SELECT usuario_ID FROM usuarios WHERE email = '$email_esc' LIMIT 1";
            $r_usr = mysqli_query($conexao, $query_usr);
            if ($r_usr && mysqli_num_rows($r_usr) > 0) {
                $row_usr = mysqli_fetch_assoc($r_usr);
                $usuario_id = (int)$row_usr['usuario_ID'];
                $query_up_u = "UPDATE usuarios SET `login` = '$login', `telefone` = '$tel_esc', `nome` = '{$temp[0]}', `sobrenome` = '$sobrenome' WHERE usuario_ID = $usuario_id";
                mysqli_query($conexao, $query_up_u);
            } else {
                $query_in_u = "INSERT INTO usuarios (`nome`, `login`, `senha`, `email`, `telefone`, `sobrenome`, `nivel`) 
                              VALUES ('{$temp[0]}', '$login', NULL, '$email_esc', '$tel_esc', '$sobrenome', 1)";
                mysqli_query($conexao, $query_in_u);
                $usuario_id = mysqli_insert_id($conexao);
            }

            // Atualiza vínculo do candidato com o usuário
            if ($usuario_id > 0 && $candidato_id > 0) {
                mysqli_query($conexao, "UPDATE candidatos SET `usuario_id` = $usuario_id WHERE candidato_id = $candidato_id");
                
                // Insere o vínculo na tabela de relacionamento candidatos_usuarios
                mysqli_query($conexao, "INSERT INTO candidatos_usuarios (candidato_id, usuario_id, is_responsavel_principal) VALUES ($candidato_id, $usuario_id, 1) ON DUPLICATE KEY UPDATE is_responsavel_principal = 1");
            }
        }

        // Upload de Currículo / Documento (se enviado)
        $nomearquivo = "";
        if (!empty($_FILES["Curriculo"]["tmp_name"])) {
            $upload_name = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $_FILES['Curriculo']['name']);
            $nomearquivo = "docs/" . $upload_name;
            $dir_base = before("pages", __DIR__);
            $destino = $dir_base . $nomearquivo;

            if (move_uploaded_file($_FILES['Curriculo']['tmp_name'], $destino) || copy($_FILES['Curriculo']['tmp_name'], $destino)) {
                $desc_doc = mysqli_real_escape_string($conexao, "Currículo / Foto - " . $nome);
                $q_doc = "INSERT INTO `documentos` (`candidato_id`, `url`, `descritivo`) VALUES ($candidato_id, '$nomearquivo', '$desc_doc')";
                mysqli_query($conexao, $q_doc);
                $doc_id = mysqli_insert_id($conexao);

                if ($doc_id > 0) {
                    mysqli_query($conexao, "UPDATE candidatos SET `anexo_id` = $doc_id WHERE candidato_id = $candidato_id");
                }
            }
        }

        // Envio de Notificação por E-mail (PHPMailer)
        require_once before('/public_html', __DIR__) . '/vendor/autoload.php';

        $to = "pauloadd@novaeratec.com.br";
        $subject = 'Nova Inscrição de Atendente: ' . $nome;
        
        $body = '<h3>Nova Inscrição Cadastrada (Pendente de Aprovação)</h3>';
        $body .= '<strong>Nome do Atendente:</strong> ' . htmlspecialchars($nome) . '<br>';
        $body .= '<strong>Responsável:</strong> ' . htmlspecialchars($responsavel) . '<br>';
        $body .= '<strong>E-mail:</strong> ' . htmlspecialchars($email) . '<br>';
        $body .= '<strong>Telefone:</strong> ' . htmlspecialchars($telefone) . '<br>';
        $body .= '<strong>Nascimento:</strong> ' . htmlspecialchars($nascimento) . '<br>';
        $body .= '<strong>Gênero:</strong> ' . htmlspecialchars($genero) . '<br>';
        if (!empty($mensagem)) {
            $body .= '<strong>Observações:</strong> ' . nl2br(htmlspecialchars($mensagem)) . '<br>';
        }
        if (!empty($nomearquivo)) {
            $body .= '<br><a href="https://projetoame.org/' . $nomearquivo . '" target="_blank">📄 Ver Documento / Currículo Anexado</a><br>';
        }
        $body .= '<br><hr><strong>Dados de Auditoria:</strong><br>';
        $body .= 'IP: ' . ($_SERVER['REMOTE_ADDR'] ?? '') . '<br>';
        $body .= 'Navegador: ' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . '<br>';
        $body .= 'Data/Hora: ' . date('d/m/Y H:i') . '<br>';
        
        // Botões de moderação rápida para o administrador
        $body .= '<br><hr><h3>⚡ Ações Rápidas de Moderação:</h3>';
        $body .= '<p style="margin-top:10px;">';
        $body .= '<a href="https://projetoame.org/admin/ativarcandidato/' . $candidato_id . '" style="background:#28a745; color:#ffffff; padding:10px 18px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block; margin-right:10px;">✅ Aprovar e Ativar Candidato</a> ';
        $body .= '<a href="https://projetoame.org/excluircandidato/' . $candidato_id . '" style="background:#dc3545; color:#ffffff; padding:10px 18px; border-radius:6px; text-decoration:none; font-weight:bold; display:inline-block;">❌ Excluir Registro</a>';
        $body .= '</p>';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'mail.projetoame.org';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'noreply@projetoame.org';
            $mail->Password   = 'PittJusto@3802';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('noreply@projetoame.org', 'Projeto AME - Inscrições');
            $mail->addAddress($to);
            if (!empty($email)) {
                $mail->addAddress($email);
            }
            $mail->addCC('regina.rjr@hotmail.com');

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar email de inscricao: " . $mail->ErrorInfo);
        }

        $status = "success";
        $inscrito = htmlspecialchars($nome);
        $msg = "<p class='lead mb-0'>Os dados de <strong>" . htmlspecialchars($nome) . "</strong> foram enviados com sucesso e estão em análise pela equipe de coordenação!</p>";
    }
}
?>
<?php include_once('./include/nav.php'); ?>

<div class="container mt-5 pt-4 mb-5">
    <header class="text-center mb-4">
        <h1 class="font-weight-bold text-primary"><i class="fas fa-user-plus mr-2"></i>Ficha de Inscrição</h1>
        <p class="text-muted">Associação Brasileira de Inclusão Através do Trabalho — Projeto A.M.E.</p>
        
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center bg-transparent p-0">
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>atendentes">Atendentes</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($inscrito); ?></li>
            </ol>
        </nav>
    </header>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            <?php if ($status === "success"): ?>
                <!-- Card de Sucesso -->
                <div class="card shadow-lg border-0 rounded-lg text-center p-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="h3 font-weight-bold text-success mb-3">Inscrição Realizada com Sucesso!</h2>
                        <div class="alert alert-success bg-light text-dark border-0 p-3 mb-4 rounded">
                            <?php echo $msg; ?>
                        </div>
                        <p class="text-muted">
                            Nossa equipe entrará em contato através do WhatsApp ou E-mail informado assim que a moderação for concluída.
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                            <a href="<?php echo $GLOBALS['app_web_root']; ?>" class="btn btn-outline-primary rounded-pill px-4 m-1">
                                <i class="fas fa-home mr-1"></i> Voltar ao Início
                            </a>
                            <a href="<?php echo $GLOBALS['app_web_root']; ?>atendentes" class="btn btn-primary rounded-pill px-4 m-1">
                                <i class="fas fa-calendar-alt mr-1"></i> Ver Atividades & Escalas
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulário de Inscrição -->
                <?php if (!empty($msg) && $status === "error"): ?>
                    <div class="mb-4">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h5 class="mb-0 font-weight-bold"><i class="fas fa-id-card mr-2"></i>Cadastro de Novo Atendente</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted text-center mb-4">
                            Preencha os campos abaixo com os dados do candidato e do responsável para participar das atividades e eventos do Projeto AME.
                        </p>

                        <form method="post" action="<?php echo $GLOBALS['app_web_root']; ?>inscrever" enctype="multipart/form-data">
                            
                            <!-- Dados do Atendente -->
                            <h6 class="font-weight-bold text-primary mb-3 pb-1 border-bottom"><i class="fas fa-user mr-2"></i>1. Dados do Atendente</h6>
                            
                            <div class="md-form mb-4">
                                <i class="fas fa-user prefix grey-text"></i>
                                <input type="text" id="nome" name="nome" class="form-control" required value="<?php echo htmlspecialchars($dados['nome']); ?>">
                                <label for="nome">Nome Completo do Inscrito/Atendente *</label>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="md-form mb-4">
                                        <i class="fas fa-calendar prefix grey-text"></i>
                                        <input type="date" id="Nascimento" name="Nascimento" class="form-control" value="<?php echo htmlspecialchars($dados['Nascimento']); ?>">
                                        <label for="Nascimento" class="active">Data de Nascimento</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="md-form mb-4">
                                        <i class="fas fa-venus-mars prefix grey-text"></i>
                                        <select class="form-control" id="genero" name="genero" style="padding-left: 2.5rem; height: calc(2.25rem + 10px);">
                                            <option value="" disabled <?php echo empty($dados['genero']) ? 'selected' : ''; ?>>Selecione o Gênero</option>
                                            <option value="masculino" <?php echo ($dados['genero'] === 'masculino') ? 'selected' : ''; ?>>Masculino</option>
                                            <option value="feminino" <?php echo ($dados['genero'] === 'feminino') ? 'selected' : ''; ?>>Feminino</option>
                                            <option value="outro" <?php echo ($dados['genero'] === 'outro') ? 'selected' : ''; ?>>Outro / Não informar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Dados do Responsável -->
                            <h6 class="font-weight-bold text-primary mt-4 mb-3 pb-1 border-bottom"><i class="fas fa-user-shield mr-2"></i>2. Responsável & Contato</h6>

                            <div class="md-form mb-4">
                                <i class="fas fa-user-tie prefix grey-text"></i>
                                <input type="text" id="responsavel" name="responsavel" class="form-control" required value="<?php echo htmlspecialchars($dados['responsavel']); ?>">
                                <label for="responsavel">Nome do Responsável Legal *</label>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="md-form mb-4">
                                        <i class="fas fa-envelope prefix grey-text"></i>
                                        <input type="email" id="Email" name="Email" class="form-control" required value="<?php echo htmlspecialchars($dados['Email']); ?>">
                                        <label for="Email">E-mail do Responsável *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="md-form mb-4">
                                        <i class="fas fa-phone prefix grey-text"></i>
                                        <input type="tel" id="Telefone" name="Telefone" class="form-control" required placeholder="(11) 99999-9999" value="<?php echo htmlspecialchars($dados['Telefone']); ?>">
                                        <label for="Telefone">Telefone / WhatsApp de Contato *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Documentação & Observações -->
                            <h6 class="font-weight-bold text-primary mt-4 mb-3 pb-1 border-bottom"><i class="fas fa-file-alt mr-2"></i>3. Documentos & Informações Complementares</h6>

                            <div class="form-group mb-4">
                                <label for="Curriculo" class="small font-weight-bold text-muted mb-1"><i class="fas fa-paperclip mr-1"></i> Currículo, Foto ou Laudo (Opcional - PDF, DOC, JPG, PNG)</label>
                                <input type="file" class="form-control-file border p-2 rounded" id="Curriculo" name="Curriculo">
                            </div>

                            <div class="md-form mb-4">
                                <i class="fas fa-comment-dots prefix grey-text"></i>
                                <textarea id="mensagem" name="mensagem" class="form-control md-textarea" rows="3"><?php echo htmlspecialchars($dados['mensagem']); ?></textarea>
                                <label for="mensagem">Observações, habilidades ou necessidades especiais (opcional)</label>
                            </div>

                            <div class="text-center mt-4">
                                <button class="btn btn-primary btn-block btn-lg rounded-pill shadow-sm font-weight-bold" type="submit">
                                    <i class="fas fa-paper-plane mr-2"></i> Concluir e Enviar Inscrição
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <p class="small text-muted">
                                    Já é cadastrado? <a href="<?php echo $GLOBALS['app_web_root']; ?>atendentes" class="font-weight-bold">Acesse as Atividades</a> | <a href="<?php echo $GLOBALS['app_web_root']; ?>atualizar_email">Atualize seu E-mail</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
include_once('./include/footer-botton.php');
include_once('./include/html_footer_scripts.php');
include_once('./include/end.php');
?>
