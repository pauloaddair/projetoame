<?php
// include_once('include/conexao.php');
// include_once('include/head.php');

$msg = "";
$sucesso = false;

// Verifica se está logado
if (!isset($_SESSION['id'])) {
    header("Location: login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = $_SESSION['id'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($nova_senha !== $confirmar_senha) {
        $msg = "As novas senhas não conferem.";
    } else {
        $novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $update = "UPDATE usuarios SET senha = '$novo_hash' WHERE usuario_ID = $id";
        if (mysqli_query($conexao, $update)) {
            $msg = "Senha definida com sucesso! Redirecionando...";
            $sucesso = true;
            header("refresh:2;url=/");
        } else {
            $msg = "Erro ao definir senha: " . mysqli_error($conexao);
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
                        <h3 class="text-center mb-4">Bem-vindo(a)!</h3>
                        <p class="text-center">Esta é a sua primeira vez por aqui. Por favor, defina sua senha de acesso:</p>
                        <?php if ($msg): ?>
                            <div class="alert <?php echo $sucesso ? 'alert-success' : 'alert-danger'; ?>">
                                <?php echo $msg; ?>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="md-form">
                                <input type="password" name="nova_senha" class="form-control" required>
                                <label>Nova Senha</label>
                            </div>
                            <div class="md-form">
                                <input type="password" name="confirmar_senha" class="form-control" required>
                                <label>Confirmar Nova Senha</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block rounded-pill">Definir Senha</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include_once('include/footer.php'); ?>
<?php include_once('include/scripts.php'); ?>
