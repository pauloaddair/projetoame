<?php
// pages/trocafoto-usuario.php - Troca foto de perfil do próprio usuário
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once('./include/funcoes.php');
$titulo = "Alterar Foto de Perfil";
include_once('./include/head.php');

// Verifica se está logado
if (!isset($_SESSION['id'])) {
    header("Location: " . $GLOBALS['app_web_root'] . "login");
    exit();
}

$usuario_id = (int)$_SESSION['id'];

// Busca foto atual do usuário
$query = "SELECT u.nome, u.sobrenome, i.url FROM usuarios u
          LEFT JOIN imagens i ON u.imagem_id = i.imagem_id
          WHERE u.usuario_id = " . $usuario_id;

$resp = mysqli_query($conexao, $query);
$icone = "img/profile.png";
$nome_completo = "";

if ($resp && mysqli_num_rows($resp) > 0) {
    $row = mysqli_fetch_assoc($resp);
    $nome_completo = $row['nome'] . ' ' . $row['sobrenome'];
    if (!empty($row['url'])) {
        $icone = $row['url'];
    }
}

$erro_msg = "";
$sucesso_msg = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $tnpFile = $_FILES["file"]["tmp_name"] ?? '';
    if (!empty($tnpFile)) {
        // Valida se o arquivo é imagem
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_info = pathinfo($_FILES['file']['name']);
        $extension = strtolower($file_info['extension'] ?? '');
        
        if (!in_array($extension, $allowed_extensions)) {
            $erro_msg = "Formato de arquivo inválido. Apenas JPG, JPEG, PNG e GIF são permitidos.";
        } else {
            // Define o nome do arquivo usando timestamp para evitar colisões
            $safe_filename = "user_" . $usuario_id . "_" . time() . "." . $extension;
            $nomearquivo = "img/" . $safe_filename;
            $dir = before("pages", __DIR__);
            
            if (copy($_FILES['file']['tmp_name'], $dir . $nomearquivo)) {
                // Insere imagem no banco
                $query_img = "INSERT INTO `imagens` (`url`) VALUES ('" . mysqli_real_escape_string($conexao, $nomearquivo) . "')";
                if (mysqli_query($conexao, $query_img)) {
                    $imagem_id = $conexao->insert_id;
                    
                    // Atualiza a tabela usuarios
                    $query_user = "UPDATE `usuarios` SET `imagem_id` = '" . $imagem_id . "' WHERE usuario_id = " . $usuario_id;
                    if (mysqli_query($conexao, $query_user)) {
                        // Atualiza a sessão para refletir imediatamente no cabeçalho/nav
                        $_SESSION['perfil'] = $nomearquivo;
                        $sucesso_msg = "Foto de perfil atualizada com sucesso!";
                        $icone = $nomearquivo;
                    } else {
                        $erro_msg = "Erro ao atualizar perfil do usuário: " . mysqli_error($conexao);
                    }
                } else {
                    $erro_msg = "Erro ao registrar imagem no banco: " . mysqli_error($conexao);
                }
            } else {
                $erro_msg = "Não foi possível copiar o arquivo para o servidor.";
            }
        }
    } else {
        $erro_msg = "Por favor, selecione um arquivo de imagem.";
    }
}
?>
<body class="bg-light">
    <?php include_once('./include/nav.php'); ?>
    <div class="container mt-5 pt-4">
        <header class="mb-4">
            <h2 class="font-weight-bold"><i class="fas fa-camera text-primary mr-2"></i>Alterar Minha Foto de Perfil</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="<?php echo $GLOBALS['app_web_root']; ?>meuperfil">Meu Perfil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Alterar Foto</li>
                </ol>
            </nav>
        </header>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0">
                    <div class="card-body p-4 text-center">
                        <h4 class="font-weight-bold mb-4"><?php echo htmlspecialchars($nome_completo); ?></h4>
                        
                        <div class="mb-4">
                            <img src="<?php echo $GLOBALS['app_web_root'] . $icone; ?>" class="rounded-circle img-thumbnail shadow-sm" style="width: 150px; height: 150px; object-fit: cover;" alt="Foto de Perfil">
                        </div>

                        <?php if (!empty($erro_msg)): ?>
                            <div class="alert alert-danger text-left rounded-pill"><i class="fas fa-exclamation-triangle mr-2"></i><?php echo $erro_msg; ?></div>
                        <?php endif; ?>

                        <?php if (!empty($sucesso_msg)): ?>
                            <div class="alert alert-success text-left rounded-pill"><i class="fas fa-check-circle mr-2"></i><?php echo $sucesso_msg; ?></div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data" class="mt-4">
                            <div class="custom-file mb-3">
                                <input type="file" class="custom-file-input" name="file" id="file" accept="image/*" required>
                                <label class="custom-file-label text-left" for="file" data-browse="Escolher">Selecionar Imagem...</label>
                            </div>
                            <small class="form-text text-muted mb-4">Escolha um arquivo JPG, PNG ou GIF. Tamanho recomendado: quadrado 300x300 pixels.</small>
                            
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo $GLOBALS['app_web_root']; ?>meuperfil" class="btn btn-outline-secondary rounded-pill px-4">Voltar</a>
                                <button class="btn btn-primary rounded-pill px-4 shadow" type="submit">Atualizar Foto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once('./include/footer-database.php'); ?>
</body>
<script src="<?php echo $GLOBALS['app_web_root']; ?>js/jquery-3.4.1.min.js"></script>
<script src="<?php echo $GLOBALS['app_web_root']; ?>js/bootstrap.min.js"></script>
<script>
    // Atualiza o texto do arquivo selecionado no input customizado
    $('#file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
</script>
<?php 
include_once('./include/end.php');
?>
