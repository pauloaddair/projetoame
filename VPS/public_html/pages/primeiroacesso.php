<?php
// pages/primeiroacesso.php - Tela de Ativação / Primeiro Acesso
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['ativacao_usuario_id'])) {
    header("Location: " . $GLOBALS['app_web_root'] . "login");
    exit();
}

$usuario_id = (int)$_SESSION['ativacao_usuario_id'];

// Busca dados do usuário
$q_user = "SELECT * FROM usuarios WHERE usuario_ID = $usuario_id";
$res_user = mysqli_query($conexao, $q_user);
if (!$res_user || mysqli_num_rows($res_user) == 0) {
    unset($_SESSION['ativacao_usuario_id']);
    header("Location: " . $GLOBALS['app_web_root'] . "login");
    exit();
}
$user_data = mysqli_fetch_assoc($res_user);

$nome = $user_data['nome'];
$email = $user_data['email'];
$telefone = $user_data['telefone'];

// Se telefone estiver vazio, busca nos candidatos vinculados
if (empty($telefone)) {
    $q_cand = "SELECT c.telefone, c.celular FROM candidatos c
               JOIN candidatos_usuarios cu ON c.candidato_id = cu.candidato_id
               WHERE cu.usuario_id = $usuario_id LIMIT 1";
    $res_cand = mysqli_query($conexao, $q_cand);
    if ($res_cand && mysqli_num_rows($res_cand) > 0) {
        $cand_data = mysqli_fetch_assoc($res_cand);
        $telefone = !empty($cand_data['celular']) ? $cand_data['celular'] : $cand_data['telefone'];
    }
}

// Funções de máscara
function maskEmail($email) {
    if (empty($email)) return "Não cadastrado";
    $parts = explode('@', $email);
    if (count($parts) < 2) return $email;
    $name = $parts[0];
    $domain = $parts[1];
    
    $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 4)) . substr($name, -2);
    if (strlen($name) <= 4) {
        $maskedName = substr($name, 0, 1) . str_repeat('*', max(0, strlen($name) - 1));
    }
    
    $maskedDomain = substr($domain, 0, 2) . str_repeat('*', max(0, strlen($domain) - 4)) . substr($domain, -2);
    if (strlen($domain) <= 4) {
        $maskedDomain = substr($domain, 0, 1) . str_repeat('*', max(0, strlen($domain) - 1));
    }
    
    return $maskedName . '@' . $maskedDomain;
}

function maskPhone($phone) {
    if (empty($phone)) return "Não cadastrado";
    $cleaned = preg_replace('/\D/', '', $phone);
    if (strlen($cleaned) < 10) return $phone;
    
    $ddd = substr($cleaned, 0, 2);
    $suffix = substr($cleaned, -4);
    
    if (strlen($cleaned) == 11) {
        return "(" . $ddd . ") 9" . str_repeat('*', 4) . "-" . $suffix;
    }
    return "(" . $ddd . ") " . str_repeat('*', 4) . "-" . $suffix;
}

$email_mascarado = maskEmail($email);
$telefone_mascarado = maskPhone($telefone);
?>

<div class="view full-page-intro" style="background-image: url('<?php echo $GLOBALS['app_web_root']; ?>img/ame2024.jpg'); background-repeat: no-repeat; background-size: cover; position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1;"></div>

<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5 bg-white">
                <div class="card-header bg-primary text-white text-center py-4 rounded-top">
                    <h3 class="font-weight-light my-1"><i class="fas fa-user-shield mr-2"></i>Primeiro Acesso</h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Olá, <strong><?php echo htmlspecialchars($nome); ?></strong>! Identificamos que este é o seu primeiro acesso ao sistema. Para garantir sua segurança, você precisa criar uma senha pessoal.
                    </p>
                    
                    <div class="alert alert-info text-center mb-4">
                        Escolha abaixo por qual canal você prefere receber o seu link seguro para ativação de senha.
                    </div>
                    
                    <div class="mb-4">
                        <!-- Opção WhatsApp -->
                        <div class="card border mb-3">
                            <div class="card-body d-flex align-items-center justify-content-between p-3">
                                <div>
                                    <h6 class="mb-0 font-weight-bold"><i class="fab fa-whatsapp text-success mr-2"></i> WhatsApp</h6>
                                    <small class="text-muted"><?php echo $telefone_mascarado; ?></small>
                                </div>
                                <button type="button" class="btn btn-success rounded-pill btn-sm send-token-btn" data-method="whatsapp" <?php echo empty($telefone) ? 'disabled' : ''; ?>>
                                    <i class="fas fa-paper-plane mr-1"></i> Enviar Link
                                </button>
                            </div>
                        </div>

                        <!-- Opção E-mail -->
                        <div class="card border mb-3">
                            <div class="card-body d-flex align-items-center justify-content-between p-3">
                                <div>
                                    <h6 class="mb-0 font-weight-bold"><i class="fas fa-envelope text-primary mr-2"></i> E-mail</h6>
                                    <small class="text-muted"><?php echo $email_mascarado; ?></small>
                                </div>
                                <button type="button" class="btn btn-primary rounded-pill btn-sm send-token-btn" data-method="email" <?php echo empty($email) ? 'disabled' : ''; ?>>
                                    <i class="fas fa-paper-plane mr-1"></i> Enviar Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mensagens de Feedback -->
                    <div id="feedback-alert" class="alert d-none text-center"></div>

                    <div class="text-center mt-3">
                        <a href="<?php echo $GLOBALS['app_web_root']; ?>login" class="small text-muted"><i class="fas fa-arrow-left mr-1"></i> Voltar para o Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $GLOBALS['app_web_root']; ?>js/jquery-3.4.1.min.js"></script>
<script>
$(document).ready(function() {
    $('.send-token-btn').click(function() {
        var button = $(this);
        var method = button.data('method');
        var originalText = button.html();
        
        // Desativa botões para evitar duplo clique
        $('.send-token-btn').prop('disabled', true);
        button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');
        
        $('#feedback-alert').addClass('d-none').removeClass('alert-success alert-danger alert-info');
        
        $.ajax({
            url: '<?php echo $GLOBALS['app_web_root']; ?>include/api_send_activation_token.php',
            type: 'POST',
            dataType: 'json',
            data: { method: method },
            success: function(response) {
                if (response.success) {
                    $('#feedback-alert')
                        .addClass('alert-success')
                        .removeClass('d-none')
                        .html('<i class="fas fa-check-circle mr-2"></i>' + response.message);
                    button.html('<i class="fas fa-check mr-1"></i> Enviado!');
                    button.removeClass('btn-success btn-primary').addClass('btn-outline-success');
                } else {
                    $('#feedback-alert')
                        .addClass('alert-danger')
                        .removeClass('d-none')
                        .html('<i class="fas fa-exclamation-triangle mr-2"></i>' + response.message);
                    button.html(originalText);
                    $('.send-token-btn').prop('disabled', false);
                }
            },
            error: function() {
                $('#feedback-alert')
                    .addClass('alert-danger')
                    .removeClass('d-none')
                    .html('<i class="fas fa-exclamation-triangle mr-2"></i> Ocorreu um erro ao processar sua solicitação. Tente novamente mais tarde.');
                button.html(originalText);
                $('.send-token-btn').prop('disabled', false);
            }
        });
    });
});
</script>
