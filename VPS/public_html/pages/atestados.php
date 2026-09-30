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
        <header class="p-4 bg-light rounded mb-4 text-center border shadow-sm">
            <h1 class="font-weight-bold text-dark mb-1"><i class="fas fa-file-signature text-primary mr-2"></i>Gerenciamento de Atestados e Certificados</h1>
            <p class="text-muted mb-0 font-weight-bold" style="font-size: 0.95rem;">
                Associação Brasileira de Inclusão pelo Trabalho – Atendentes Muito Especiais (AME) &bull; CNPJ nº 32.131.752/0001-88
            </p>
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
                                    $sql_ev = "SELECT DISTINCT em.id, em.nome, em.inicio, em.final, em.tipo, em.tipo_evento, em.linha_corte_presenca 
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
                                        $linha_corte = !empty($ev['linha_corte_presenca']) ? intval($ev['linha_corte_presenca']) : 75;
                                        
                                        // Total de aulas/horarios escalados
                                        $sql_esc = "SELECT COUNT(*) as total FROM disponibilidade d 
                                                    JOIN horarios h ON d.atividade_id = h.horario_id 
                                                    WHERE d.candidato_id = $candidato_id AND h.evento_id = $id_ev AND d.escalado = 1";
                                        $res_esc = mysqli_query($conexao, $sql_esc);
                                        $esc = mysqli_fetch_assoc($res_esc);
                                        $tot_escalado = intval($esc['total'] ?? 0);
                                        $foi_escalado = ($tot_escalado > 0);

                                        // Total de presenças confirmadas
                                        $sql_pres = "SELECT COUNT(*) as total FROM presenca 
                                                     WHERE candidato_id = $candidato_id AND evento_id = $id_ev AND presente = 1";
                                        $res_pres = mysqli_query($conexao, $sql_pres);
                                        $pres_row = mysqli_fetch_assoc($res_pres);
                                        $tot_presencas = intval($pres_row['total'] ?? 0);

                                        // Fallback para eventos confirmados no modelo legado (sem horario_id preenchido)
                                        if ($tot_presencas === 0 && $foi_escalado) {
                                            $sql_leg = "SELECT presente FROM presenca WHERE candidato_id = $candidato_id AND evento_id = $id_ev AND (horario_id IS NULL OR horario_id = 0) AND presente = 1 LIMIT 1";
                                            $res_leg = mysqli_query($conexao, $sql_leg);
                                            if ($res_leg && mysqli_num_rows($res_leg) > 0) {
                                                $tot_presencas = $tot_escalado;
                                            }
                                        }

                                        $frequencia_pct = ($tot_escalado > 0) ? round(($tot_presencas / $tot_escalado) * 100) : 0;
                                        $apto_pleno = ($frequencia_pct >= $linha_corte && $tot_presencas > 0);

                                        if (!$foi_escalado) {
                                            $status_badge = '<span class="badge badge-info">Inscrito</span>';
                                        } elseif ($apto_pleno) {
                                            $status_badge = '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Apto Certificado ('.$tot_presencas.'/'.$tot_escalado.' - '.$frequencia_pct.'%)</span>';
                                        } else {
                                            $status_badge = '<span class="badge badge-warning text-dark"><i class="fas fa-exclamation-triangle mr-1"></i>Frequência '.$frequencia_pct.'% ('.$tot_presencas.'/'.$tot_escalado.')</span>';
                                        }
                                        
                                        echo "<tr>";
                                        echo "<td><strong>".$ev['nome']."</strong></td>";
                                        echo "<td>$data_ini a $data_fim</td>";
                                        echo "<td>$status_badge</td>";
                                        echo "<td>
                                                <form action='/gerar_atestado' method='POST' target='_blank' style='display:inline;'>
                                                    <input type='hidden' name='candidato_id' value='$candidato_id'>
                                                    <input type='hidden' name='evento_id' value='$id_ev'>
                                                    <input type='hidden' name='tipo' value='matricula'>
                                                    <button type='submit' class='btn btn-xs btn-primary' title='Atestado de Matrícula'><i class='fas fa-file-contract'></i> Matrícula</button>
                                                </form>";

                                        if ($apto_pleno) {
                                            echo " <form action='/gerar_atestado' method='POST' target='_blank' style='display:inline;'>
                                                    <input type='hidden' name='candidato_id' value='$candidato_id'>
                                                    <input type='hidden' name='evento_id' value='$id_ev'>
                                                    <input type='hidden' name='tipo' value='participacao'>
                                                    <button type='submit' class='btn btn-xs btn-success' title='Certificado de Conclusão Integral (Freq. {$frequencia_pct}%)'><i class='fas fa-certificate'></i> Certificado Pleno</button>
                                                  </form>";
                                        } elseif ($foi_escalado) {
                                            echo " <form action='/gerar_atestado' method='POST' target='_blank' style='display:inline;'>
                                                    <input type='hidden' name='candidato_id' value='$candidato_id'>
                                                    <input type='hidden' name='evento_id' value='$id_ev'>
                                                    <input type='hidden' name='tipo' value='parcial'>
                                                    <button type='submit' class='btn btn-xs btn-warning text-dark font-weight-bold' title='Declaração de Horas Parciais (Freq. {$frequencia_pct}% abaixo do corte de {$linha_corte}%)'><i class='fas fa-file-alt'></i> Declaração Parcial</button>
                                                  </form>";
                                        }

                                        echo "</td>";
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
