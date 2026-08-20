<?php
$titulo = "Emissão de Atestados";
// include_once('./include/conexao.php');
// include_once('./include/funcoes.php');
include_once('./include/head-datatable.php');

if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] < 3) {
    include_once('./pages/restrito.php');
    include_once('./include/scripts.php');
    include_once('./include/end.php');
    exit;
}

$candidato_id = 0;
if (array_key_exists(1, $parametros)) {
    $candidato_id = intval($parametros[1]);
}

$candidato = null;
if ($candidato_id > 0) {
    $sql_cand = "SELECT * FROM candidatos WHERE candidato_id = $candidato_id";
    $res_cand = mysqli_query($conexao, $sql_cand);
    $candidato = mysqli_fetch_assoc($res_cand);
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<body>
    <?php include_once('./include/nav.php'); ?>
    <div class="container mt-5">
        <header class="p-4 bg-light rounded mb-4">
            <h1 class="text-center">Gerenciamento de Atestados</h1>
        </header>

        <?php if (!$candidato): ?>
            <div class="card p-4">
                <h4>Pesquisar Atendente</h4>
                <form action="/atestados" method="GET" onsubmit="window.location.href='/atestados/' + document.getElementById('sel_candidato').value; return false;">
                    <div class="form-group">
                        <select class="form-control select2" id="sel_candidato" name="candidato_id" style="width: 100%;">
                            <option value="">Selecione um atendente...</option>
                            <?php
                            $sql_list = "SELECT candidato_id, nome FROM candidatos ORDER BY nome ASC";
                            $res_list = mysqli_query($conexao, $sql_list);
                            while ($row = mysqli_fetch_assoc($res_list)) {
                                echo "<option value='".digitos($row['candidato_id'])."'>".$row['nome']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Selecionar</button>
                </form>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">Dados do Atendente</div>
                        <div class="card-body">
                            <h5><?php echo $candidato['nome']; ?></h5>
                            <p><strong>CPF:</strong> <?php echo $candidato['CPF']; ?></p>
                            <p><strong>E-mail:</strong> <?php echo $candidato['Email']; ?></p>
                            <a href="/atestados" class="btn btn-sm btn-outline-secondary">Trocar Atendente</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">Atividades e Cursos</div>
                        <div class="card-body">
                            <table class="table table-sm table-hover" id="atividades_table">
                                <thead>
                                    <tr>
                                        <th>Atividade/Evento</th>
                                        <th>Período</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Busca eventos onde o aluno tem disponibilidade
                                    $sql_ev = "SELECT DISTINCT em.id, em.nome, em.inicio, em.final 
                                               FROM eventos_marcados em
                                               JOIN horarios h ON em.id = h.evento_id
                                               JOIN disponibilidade d ON h.horario_id = d.atividade_id
                                               WHERE d.candidato_id = $candidato_id
                                               ORDER BY em.inicio DESC";
                                    $res_ev = mysqli_query($conexao, $sql_ev);
                                    
                                    while ($ev = mysqli_fetch_assoc($res_ev)) {
                                        $id_ev = $ev['id'];
                                        $data_ini = date('d/m/Y', strtotime($ev['inicio']));
                                        $data_fim = date('d/m/Y', strtotime($ev['final']));
                                        
                                        // Verifica se foi escalado em alguma aula desse evento
                                        $sql_esc = "SELECT COUNT(*) as total FROM disponibilidade d 
                                                    JOIN horarios h ON d.atividade_id = h.horario_id 
                                                    WHERE d.candidato_id = $candidato_id AND h.evento_id = $id_ev AND d.escalado = 1";
                                        $res_esc = mysqli_query($conexao, $sql_esc);
                                        $esc = mysqli_fetch_assoc($res_esc);
                                        $foi_escalado = ($esc['total'] > 0);
                                        
                                        echo "<tr>";
                                        echo "<td>".$ev['nome']."</td>";
                                        echo "<td>$data_ini a $data_fim</td>";
                                        echo "<td>".($foi_escalado ? '<span class="badge badge-success">Escalado/Participou</span>' : '<span class="badge badge-info">Inscrito</span>')."</td>";
                                        echo "<td>
                                                <form action='/gerar_atestado' method='POST' target='_blank' style='display:inline;'>
                                                    <input type='hidden' name='candidato_id' value='$candidato_id'>
                                                    <input type='hidden' name='evento_id' value='$id_ev'>
                                                    <input type='hidden' name='tipo' value='matricula'>
                                                    <button type='submit' class='btn btn-xs btn-primary' title='Atestado de Matrícula'><i class='fas fa-file-contract'></i> Matrícula</button>
                                                </form>
                                                <form action='/gerar_atestado' method='POST' target='_blank' style='display:inline;'>
                                                    <input type='hidden' name='candidato_id' value='$candidato_id'>
                                                    <input type='hidden' name='evento_id' value='$id_ev'>
                                                    <input type='hidden' name='tipo' value='participacao'>
                                                    <button type='submit' class='btn btn-xs btn-success' title='Atestado de Participação'><i class='fas fa-certificate'></i> Participação</button>
                                                </form>
                                              </td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">Documentos Emitidos</div>
                        <div class="card-body">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Documento</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_docs = "SELECT * FROM documentos WHERE candidato_id = $candidato_id AND (descritivo LIKE 'Atestado%' OR descritivo LIKE 'Confirmação%') ORDER BY doc_id DESC";
                                    $res_docs = mysqli_query($conexao, $sql_docs);
                                    while ($doc = mysqli_fetch_assoc($res_docs)) {
                                        echo "<tr>";
                                        echo "<td>---</td>"; // Tabela documentos parece não ter data_envio visível, talvez precise checar
                                        echo "<td>".$doc['descritivo']."</td>";
                                        echo "<td><a href='/".$doc['url']."' target='_blank' class='btn btn-xs btn-info'>Ver</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once('./include/footer-database.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            $('#atividades_table').DataTable({
                "order": [],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                }
            });
        });
    </script>
</body>
<?php
include_once('./include/scripts.php');
include_once('./include/end.php');
?>
