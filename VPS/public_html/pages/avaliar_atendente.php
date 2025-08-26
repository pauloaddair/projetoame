<?php
date_default_timezone_set('America/Sao_Paulo');
include_once('./include/head.php');
include_once('./include/conexao.php');
include_once('./include/funcoes.php');

// Chave secreta para a assinatura. Mantenha isso em segredo e não o exponha.
// Em um sistema de produção, isso viria de uma variável de ambiente.
define('HMAC_SECRET_KEY', 'ProjetoAME_ChaveSecreta_#2025@');

$mensagem = "";
$is_valid_link = false;

// Validação do link
if (isset($_GET['evento_id'], $_GET['candidato_id'], $_GET['sig'])) {
    $evento_id = $_GET['evento_id'];
    $candidato_id = $_GET['candidato_id'];
    $recebido_sig = $_GET['sig'];

    $data_string = "evento_id={$evento_id}&candidato_id={$candidato_id}";
    $calculado_sig = hash_hmac('sha256', $data_string, HMAC_SECRET_KEY);

    if (hash_equals($calculado_sig, $recebido_sig)) {
        $is_valid_link = true;
    }
}

// Lógica de submissão do formulário (POST)
if ($is_valid_link && $_SERVER['REQUEST_METHOD'] == "POST"){
    // Lógica para salvar a avaliação (a mesma que definimos anteriormente)
    $query_insert = sprintf("INSERT INTO avaliacoes (evento_id, candidato_id, pontualidade, asseio, socializacao, simpatia, compreensao_instrucoes, facilidade_orientacoes, foco_atividades, comportamento_geral, observacoes, avaliador_nome, avaliador_email, data_avaliacao) VALUES (%d, %d, %d, %d, %d, %d, %d, %d, %d, %d, '%s', '%s', '%s', NOW());",
        intval($_POST['evento_id']), intval($_POST['candidato_id']), 
        $_POST['pontualidade'] ?? 0, $_POST['asseio'] ?? 0, $_POST['socializacao'] ?? 0, $_POST['simpatia'] ?? 0, 
        $_POST['compreencao'] ?? 0, $_POST['facilidade'] ?? 0, $_POST['foco'] ?? 0, $_POST['comportamento'] ?? 0,
        mysqli_real_escape_string($conexao, $_POST['obs'] ?? ''),
        mysqli_real_escape_string($conexao, $_POST['avaliador'] ?? ''),
        mysqli_real_escape_string($conexao, $_POST['avaliadoremail'] ?? '')
    );

    if (mysqli_query($conexao, $query_insert)) {
        $mensagem = "Avaliação de <strong><em>". $_POST['nome'] ."</em></strong> salva com sucesso!";
        $is_valid_link = false; // Impede o reenvio do formulário
    } else {
        $mensagem = "Erro crítico ao salvar a avaliação no banco de dados: " . mysqli_error($conexao);
    }
}

// Lógica de exibição da página (GET)
?>
<body>
<div class="container p-1">
    <header class="mt-5 p-2 justify-content-md-center">
        <h1 class="mt-5 text-center">AVALIAÇÃO DE ATENDENTE</h1>
    </header>
    <main class="container">
        <div class="row">
            <div class="col-12">
                <?php if (!empty($mensagem)) {
                    echo "<div class='alert alert-info'><h2 class='text-center'>". $mensagem . "</h2></div>";
                } ?>

                <?php if ($is_valid_link) { 
                    $query_form = "SELECT c.candidato_id, c.nome, i.url, em.nome AS evento_nome, em.local, em.id AS evento_id
                                   FROM candidatos c
                                   LEFT JOIN imagens i ON c.imagem_id = i.imagem_id
                                   JOIN eventos_marcados em ON em.id = {$evento_id}
                                   WHERE c.candidato_id = {$candidato_id} LIMIT 1;";
                    
                    $resp_form = mysqli_query($conexao, $query_form);
                    $row = mysqli_fetch_array($resp_form);
                    if ($row) { ?>
                        <!-- O mesmo formulário de antes vai aqui -->
                        <form class="border border-light p-2" method="post">
                            <input type="hidden" name="candidato_id" value="<?php echo $row['candidato_id']; ?>">
                            <input type="hidden" name="evento_id" value="<?php echo $row['evento_id']; ?>">
                            <input type="hidden" name="nome" value="<?php echo $row['nome']; ?>">
                            <!-- ... resto do formulário ... -->
                             <div class="card mb-2 p-1">
                                <div class="card-header"><h1>FICHA DE AVALIAÇÃO:</h1>
                                    <h2>LOCAL:<?php echo $row['evento_nome']?><br><small><?php echo $row['local']?></small></h2><br>
                                    <small>por favor, responda de acordo com suas observações sobre o atendente abaixo</small>
                                </div>
                            </div>
                            <div class="card mb-2 p-1">
                                <div class="card-header">
                                    <div class="image-container"><img class="card-img-top" src="<?php echo $GLOBALS['app_web_root'] . $row['url']?>" alt="Card image cap"></div>
                                    <h1><?php echo $row['nome']?></h1>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>PONTUALIDADE</strong><br><?php for ($i = 1; $i <= 5; $i++): ?><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="pontualidade" value="<?php echo $i; ?>" required><label><img src="<?php echo $GLOBALS['app_web_root']; ?>img/aval_<?php echo $i; ?>.svg" width="32"></label></div><?php endfor; ?></li>
                                        <!-- ... outros critérios ... -->
                                    </ul>
                                </div>
                                <button class="btn bg-info rounded-pill m-3 p-1" type="submit">Salvar Avaliação</button>
                            </div>
                        </form>
                    <?php } else { echo "<div class='alert alert-danger'>Dados do evento ou atendente não encontrados.</div>"; }
                } else if (empty($mensagem)) { 
                    echo "<div class='alert alert-danger'><h1>Link inválido ou expirado.</h1><p>Por favor, acesse a avaliação através do link original fornecido para o evento.</p></div>";
                } ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>