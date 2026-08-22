<?php
// pages/ativarperfil.php - Validação de token e definição de senha usando a tabela usuarios
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$token = isset($parametros[1]) ? mysqli_real_escape_string($conexao, $parametros[1]) : '';
$valido = false;
$usuario_id = 0;
$user_row = null;

if (!empty($token)) {
    // Verifica se o token existe e não expirou na tabela usuarios
    $query = "SELECT * FROM usuarios WHERE token_ativacao = '$token' AND token_expira > NOW() LIMIT 1";
    $res = mysqli_query($conexao, $query);
    if ($res && mysqli_num_rows($res) == 1) {
        $user_row = mysqli_fetch_assoc($res);
        $valido = true;
        $usuario_id = (int)$user_row['usuario_ID'];
    }
}

// Processa a definição da senha
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nova_senha']) && $valido) {
    $senha = $_POST['nova_senha'];
    $confirmacao = $_POST['confirma_senha'];

    if ($senha === $confirmacao) {
        // Criptografia segura com Bcrypt
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Atualiza a senha e limpa o token
        $sql_update = "UPDATE usuarios SET senha = '$hash', token_ativacao = NULL, token_expira = NULL WHERE usuario_ID = $usuario_id";
        if (mysqli_query($conexao, $sql_update)) {
            // Efetua login automático na sessão
            $_SESSION['id'] = $user_row['usuario_ID'];
            $_SESSION['usuario'] = !empty($user_row['login']) ? $user_row['login'] : $user_row['email'];
            $_SESSION['nome'] = $user_row['nome'];
            $_SESSION['nivel'] = $user_row['nivel'];
            
            // Busca imagem de perfil
            $_SESSION['perfil'] = "img/profile.png";
            if (!empty($user_row['imagem_id'])) {
                $q_img = "SELECT url FROM imagens WHERE imagem_id = " . (int)$user_row['imagem_id'];
                $res_img = mysqli_query($conexao, $q_img);
                if ($res_img && mysqli_num_rows($res_img) > 0) {
                    $row_img = mysqli_fetch_assoc($res_img);
                    $_SESSION['perfil'] = $row_img['url'];
                }
            }

            // Limpa a sessão temporária de ativação
            unset($_SESSION['ativacao_usuario_id']);

            $msg = "<div class='alert alert-success'><i class='fas fa-check-circle mr-2'></i>Senha cadastrada com sucesso! Você foi logado no sistema. Redirecionando...</div>";
            echo "<script>setTimeout(() => { window.location.href='" . $GLOBALS['app_web_root'] . "meuperfil'; }, 2500);</script>";
        } else {
            $msg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle mr-2'></i>Erro ao registrar a senha no banco de dados.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle mr-2'></i>As senhas não conferem.</div>";
    }
}
?>
<body class="bg-light">
    <div class="view full-page-intro" style="background-image: url('<?php echo $GLOBALS['app_web_root']; ?>img/ame2024.jpg'); background-repeat: no-repeat; background-size: cover; position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1;"></div>

    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-lg mt-5 bg-white">
                    <div class="card-header bg-primary text-white text-center py-4 rounded-top">
                        <h3 class="font-weight-light my-1"><i class="fas fa-key mr-2"></i>Ativar Conta</h3>
                    </div>
                    <div class="card-body p-5">
                        <?php if ($valido): ?>
                            <div class="text-center mb-4">
                                <h5 class="font-weight-bold">Olá, <?php echo htmlspecialchars($user_row['nome']); ?></h5>
                                <p class="text-muted small">Crie uma nova senha forte de acesso para a sua conta.</p>
                            </div>

                            <?php echo $msg; ?>
                            <form method="post">
                                <div class="md-form mb-4">
                                    <i class="fas fa-lock prefix grey-text"></i>
                                    <input type="password" name="nova_senha" id="nova_senha" class="form-control" required autofocus>
                                    <label for="nova_senha">Nova Senha</label>
                                </div>
                                <div class="md-form mb-4">
                                    <i class="fas fa-check-double prefix grey-text"></i>
                                    <input type="password" name="confirma_senha" id="confirma_senha" class="form-control" required>
                                    <label for="confirma_senha">Confirme a Senha</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block rounded-pill py-2 shadow-sm">Cadastrar Senha e Entrar</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3 text-warning"></i><br>
                                Este link de ativação é inválido ou já expirou.
                            </div>
                            <a href="<?php echo $GLOBALS['app_web_root']; ?>login" class="btn btn-outline-primary btn-block rounded-pill">Voltar ao Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once('./include/html_footer_scripts.php'); ?>
</body>
