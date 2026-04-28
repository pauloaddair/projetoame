<?php
// ativarperfil.php - Validação de token e definição de senha
// include_once('./include/head.php');
// include_once('./include/conexao.php');

$token = isset($parametros[1]) ? mysqli_real_escape_string($conexao, $parametros[1]) : '';
$valido = false;
$usuario_id = 0;

if (!empty($token)) {
    // Verifica se o token existe, não foi usado e não expirou
    $query = "SELECT * FROM tokens_acesso WHERE token = '$token' AND usado = 0 AND expira_em > NOW() LIMIT 1";
    $res = mysqli_query($conexao, $query);
    if ($row = mysqli_fetch_assoc($res)) {
        $valido = true;
        $usuario_id = $row['usuario_id'];
    }
}

// Processa a definição da senha
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nova_senha']) && $valido) {
    $senha = $_POST['nova_senha'];
    $confirmacao = $_POST['confirma_senha'];

    if ($senha === $confirmacao) {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        
        // Atualiza a senha do usuário
        $sql_update = "UPDATE usuarios SET senha = '$hash' WHERE usuario_ID = $usuario_id";
        if (mysqli_query($conexao, $sql_update)) {
            // Marca o token como usado
            mysqli_query($conexao, "UPDATE tokens_acesso SET usado = 1 WHERE token = '$token'");
            $msg = "<div class='alert alert-success'>Senha definida com sucesso! Redirecionando para seu perfil...</div>";
            echo "<script>setTimeout(() => { window.location.href='/meuperfil'; }, 2000);</script>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>As senhas não conferem.</div>";
    }
}
?>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3 class="font-weight-bold">Ativar Perfil AME</h3>
                            <p class="text-muted small">Defina sua senha de acesso para gerenciar o perfil.</p>
                        </div>

                        <?php if ($valido): ?>
                            <?php echo $msg; ?>
                            <form method="post">
                                <div class="md-form mb-4">
                                    <i class="fas fa-lock prefix grey-text"></i>
                                    <input type="password" name="nova_senha" id="nova_senha" class="form-control" required>
                                    <label for="nova_senha">Nova Senha</label>
                                </div>
                                <div class="md-form mb-4">
                                    <i class="fas fa-check-double prefix grey-text"></i>
                                    <input type="password" name="confirma_senha" id="confirma_senha" class="form-control" required>
                                    <label for="confirma_senha">Confirme a Senha</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block rounded-pill py-2 shadow-sm">Ativar Minha Conta</button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>
                                Link inválido ou expirado. Por favor, solicite um novo acesso na página de login.
                            </div>
                            <a href="/meuperfil" class="btn btn-outline-primary btn-block rounded-pill">Voltar ao Início</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once('./include/html_footer_scripts.php'); ?>
</body>
