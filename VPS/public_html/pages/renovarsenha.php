<?php
// session_start(); (Assumindo que o sistema já tem a sessão ativa no topo das páginas)
// include_once('include/conexao.php');
// include_once('include/head.php');

$msg = "";
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = $_SESSION['id'];
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($nova_senha !== $confirmar_senha) {
        $msg = "As novas senhas não conferem.";
    } else {
        // Buscar senha atual no banco
        $query = "SELECT senha FROM usuarios WHERE usuario_ID = $id";
        $result = mysqli_query($conexao, $query);
        $row = mysqli_fetch_assoc($result);
        $hash_atual = $row['senha'];

        $valido = false;
        // Valida contra o MD5 ou Bcrypt atual
        if (password_verify($senha_atual, $hash_atual) || md5($senha_atual) === $hash_atual) {
            $valido = true;
        }

        if ($valido) {
            $novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $update = "UPDATE usuarios SET senha = '$novo_hash' WHERE usuario_ID = $id";
            if (mysqli_query($conexao, $update)) {
                $msg = "Senha atualizada com sucesso!";
                $sucesso = true;
            } else {
                $msg = "Erro ao atualizar senha: " . mysqli_error($conexao);
            }
        } else {
            $msg = "Senha atual incorreta.";
        }
    }
}
?>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Renovar Senha</h3>
                        <?php if ($msg): ?>
                            <div class="alert <?php echo $sucesso ? 'alert-success' : 'alert-danger'; ?>">
                                <?php echo $msg; ?>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="md-form">
                                <input type="password" name="senha_atual" class="form-control" required>
                                <label>Senha Atual</label>
                            </div>
                            <div class="md-form">
                                <input type="password" name="nova_senha" class="form-control" required>
                                <label>Nova Senha</label>
                            </div>
                            <div class="md-form">
                                <input type="password" name="confirmar_senha" class="form-control" required>
                                <label>Confirmar Nova Senha</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block rounded-pill">Atualizar Senha</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include_once('include/footer.php'); ?>
<?php include_once('include/scripts.php'); ?>
