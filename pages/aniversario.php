<?php
include_once('include/conexao.php');
// Mês atual
$mes_atual = date('m');

// Consulta SQL para obter os aniversariantes do mês atual
$sql = "SELECT c.nome, c.Nascimento, i.url
        FROM candidatos c
        JOIN imagens i ON c.imagem_id = i.imagem_id
        WHERE MONTH(c.Nascimento) = ?
        ORDER BY DAY(c.Nascimento) ASC";

// Preparação da consulta
if ($stmt = $conexao->prepare($sql)) {
    // Vincula o parâmetro
    $stmt->bind_param('i', $mes_atual);
    // Executa a consulta
    $stmt->execute();
    // Obtém o resultado
    $resultado = $stmt->get_result();
} else {
    echo "Erro na preparação da consulta: " . $mysqli->error;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Aniversariantes do Mês</title>
    <!-- CSS do Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .carousel-item {
            position: relative;
            text-align: center;
        }
        .carousel-item img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
        }
        .carousel-caption {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div id="carouselAniversariantes" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php
            $ativo = true;
            while ($row = $resultado->fetch_assoc()):
                $nome = htmlspecialchars($row['nome']);
                $data_nascimento = date('d/m/Y', strtotime($row['Nascimento']));
                $dia_aniversario = date('d', strtotime($row['Nascimento']));
                $caminho_imagem = htmlspecialchars($row['url']);
            ?>
            <div class="carousel-item <?php if ($ativo) echo 'active'; ?>">
                <img src="<?php echo $caminho_imagem; ?>" alt="Foto de <?php echo $nome; ?>">
                <div class="carousel-caption">
                    <h5><?php echo $nome; ?></h5>
                    <p>Aniversário: <?php echo $dia_aniversario; ?></p>
                </div>
            </div>
            <?php
                $ativo = false;
            endwhile;
            ?>
        </div>
        <a class="carousel-control-prev" href="#carouselAniversariantes" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Anterior</span>
        </a>
        <a class="carousel-control-next" href="#carouselAniversariantes" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Próximo</span>
        </a>
    </div>

    <!-- JS do Bootstrap e dependências -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
