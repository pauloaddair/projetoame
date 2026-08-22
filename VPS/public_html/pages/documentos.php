<?php
// pages/documentos.php - Portal de Documentos (Visão do Associado e Admin)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$titulo = "Portal de Documentos";
include_once('./include/head-datatable.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: " . $GLOBALS['app_web_root'] . "login");
    exit();
}

$usuario_id = (int)$_SESSION['id'];
$nivel_usuario = (int)$_SESSION['nivel'];
$is_admin = ($nivel_usuario >= 4);
?>
<body class="bg-light">
    <?php include_once('./include/nav.php'); ?>

    <div class="container mt-5 pt-4">
        <header class="mb-4">
            <h2 class="font-weight-bold"><i class="fas fa-folder-open text-primary mr-2"></i>Portal de Documentos</h2>
            <p class="text-muted">Envie ou faça o download de atestados, comprovantes e outros documentos importantes.</p>
        </header>

        <?php if ($is_admin): ?>
            <!-- ================= VISÃO DO ADMINISTRADOR ================= -->
            <div class="card shadow border-0 mb-5">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">Todos os Documentos Cadastrados</h5>
                    <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.location.href='/admin'"><i class="fas fa-arrow-left mr-1"></i> Painel Admin</button>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="table" class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Candidato / Atendente</th>
                                    <th>Documento</th>
                                    <th>Data de Envio</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $query_admin = "SELECT d.doc_id, d.url, d.descritivo, d.data, c.candidato_id, c.nome 
                                            FROM documentos d
                                            JOIN candidatos c ON d.candidato_id = c.candidato_id
                                            ORDER BY d.data DESC";
                            $resp_admin = mysqli_query($conexao, $query_admin);
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($resp_admin)):
                                $doc_url = $GLOBALS['app_web_root'] . $row['url'];
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>
                                        <a class="font-weight-bold" href="/editacandidato/<?php echo digitos($row['candidato_id']); ?>">
                                            <?php echo htmlspecialchars($row['nome']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $doc_url; ?>" target="_blank" class="text-primary font-weight-bold">
                                            <i class="far fa-file-pdf mr-1 text-danger"></i> <?php echo htmlspecialchars($row['descritivo']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['data'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ================= VISÃO DO ASSOCIADO / RESPONSÁVEL ================= -->
            <?php
            // Busca candidatos associados a este usuário
            $candidatos_vinculados = [];
            $q_cand = "SELECT c.candidato_id, c.nome FROM candidatos c
                       JOIN candidatos_usuarios cu ON c.candidato_id = cu.candidato_id
                       WHERE cu.usuario_id = $usuario_id";
            $res_cand = mysqli_query($conexao, $q_cand);
            while ($row = mysqli_fetch_assoc($res_cand)) {
                $candidatos_vinculados[] = $row;
            }
            
            if (empty($candidatos_vinculados)):
            ?>
                <div class="alert alert-warning text-center p-5">
                    <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                    <h4>Nenhum associado vinculado</h4>
                    <p class="text-muted mb-0">Você não possui candidatos ou atendentes vinculados à sua conta. Entre em contato com a nossa diretoria para realizar a associação.</p>
                </div>
            <?php else: 
                $cand_ids = array_column($candidatos_vinculados, 'candidato_id');
                $cand_ids_str = implode(',', $cand_ids);
                
                // Busca documentos vinculados aos candidatos desse usuário
                $query_docs = "SELECT d.doc_id, d.url, d.descritivo, d.data, c.nome as candidato_nome 
                               FROM documentos d
                               JOIN candidatos c ON d.candidato_id = c.candidato_id
                               WHERE d.candidato_id IN ($cand_ids_str)
                               ORDER BY d.data DESC";
                $res_docs = mysqli_query($conexao, $query_docs);
            ?>
                <div class="row">
                    <!-- Formulário de Upload (Esquerda) -->
                    <div class="col-lg-5 mb-4">
                        <div class="card shadow border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0 font-weight-bold"><i class="fas fa-cloud-upload-alt mr-2"></i>Enviar Documento</h5>
                            </div>
                            <div class="card-body p-4">
                                <form id="uploadDocForm" enctype="multipart/form-data">
                                    <?php if (count($candidatos_vinculados) > 1): ?>
                                        <div class="form-group mb-3">
                                            <label for="candidato_id" class="font-weight-bold">Para quem é este documento?</label>
                                            <select name="candidato_id" id="candidato_id" class="form-control" required>
                                                <?php foreach ($candidatos_vinculados as $c): ?>
                                                    <option value="<?php echo $c['candidato_id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="candidato_id" value="<?php echo $candidatos_vinculados[0]['candidato_id']; ?>">
                                        <div class="alert alert-light border mb-3">
                                            <strong>Destinatário:</strong> <?php echo htmlspecialchars($candidatos_vinculados[0]['nome']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group mb-3">
                                        <label for="tipo_doc" class="font-weight-bold">Tipo de Documento</label>
                                        <select name="tipo_doc" id="tipo_doc" class="form-control" required>
                                            <option value="Comprovante de Pagamento">Comprovante de Pagamento</option>
                                            <option value="Atestado de Presença">Atestado de Presença</option>
                                            <option value="Ficha Médica">Ficha Médica</option>
                                            <option value="Declaração Escolar">Declaração Escolar</option>
                                            <option value="Outros">Outros</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3 d-none" id="outros_descritivo_container">
                                        <label for="descritivo_custom" class="font-weight-bold">Descreva o Documento</label>
                                        <input type="text" name="descritivo_custom" id="descritivo_custom" class="form-control" placeholder="Ex: Contrato assinado">
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="documento_file" class="font-weight-bold">Selecionar Arquivo</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="documento_file" id="documento_file" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <label class="custom-file-label" for="documento_file">Escolher arquivo...</label>
                                        </div>
                                        <small class="text-muted">Formatos permitidos: PDF, JPG, PNG. Tamanho máximo: 10MB.</small>
                                    </div>

                                    <div id="uploadFeedback" class="alert d-none mb-3"></div>

                                    <button type="submit" class="btn btn-success btn-block rounded-pill py-2 shadow-sm">
                                        <i class="fas fa-upload mr-1"></i> Enviar Agora
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Listagem de Documentos Enviados (Direita) -->
                    <div class="col-lg-7">
                        <div class="card shadow border-0">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 font-weight-bold text-primary"><i class="fas fa-file-invoice mr-2"></i>Meus Documentos</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Associado</th>
                                                <th>Documento</th>
                                                <th>Data de Envio</th>
                                            </tr>
                                        </thead>
                                        <tbody id="docsListBody">
                                        <?php if (mysqli_num_rows($res_docs) == 0): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">Nenhum documento enviado até o momento.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php 
                                            while ($row = mysqli_fetch_assoc($res_docs)): 
                                                $doc_url = $GLOBALS['app_web_root'] . $row['url'];
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['candidato_nome']); ?></td>
                                                    <td>
                                                        <a href="<?php echo $doc_url; ?>" target="_blank" class="text-primary font-weight-bold">
                                                            <i class="far fa-file-alt text-secondary mr-1"></i> <?php echo htmlspecialchars($row['descritivo']); ?>
                                                        </a>
                                                    </td>
                                                    <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($row['data'])); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include_once('./include/footer-database.php'); ?>
</body>

<script src="<?php echo $GLOBALS['app_web_root']; ?>js/jquery-3.4.1.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializa o DataTable no modo admin se a tabela existir
    if ($('#table').length) {
        $('#table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            },
            "order": [[3, "desc"]]
        });
    }

    // Altera a label do arquivo customizado do Bootstrap
    $('#documento_file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Exibe/oculta campo customizado de descritivo se escolher "Outros"
    $('#tipo_doc').change(function() {
        if ($(this).val() === 'Outros') {
            $('#outros_descritivo_container').removeClass('d-none');
            $('#descritivo_custom').attr('required', true);
        } else {
            $('#outros_descritivo_container').addClass('d-none');
            $('#descritivo_custom').removeAttr('required').val('');
        }
    });

    // Submit de formulário de Upload
    $('#uploadDocForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var originalBtnHtml = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');
        $('#uploadFeedback').addClass('d-none').removeClass('alert-success alert-danger alert-info');
        
        $.ajax({
            url: '<?php echo $GLOBALS['app_web_root']; ?>include/api_upload_documento.php',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#uploadFeedback').addClass('alert-success').removeClass('d-none').html('<i class="fas fa-check-circle mr-2"></i>' + response.message);
                    
                    // Adiciona na listagem
                    var newRow = '<tr>' +
                        '<td>' + response.candidato_nome + '</td>' +
                        '<td>' +
                            '<a href="' + response.url + '" target="_blank" class="text-primary font-weight-bold">' +
                                '<i class="far fa-file-alt text-secondary mr-1"></i> ' + response.descritivo +
                            '</a>' +
                        '</td>' +
                        '<td class="small text-muted">' + response.data + '</td>' +
                    '</tr>';
                    
                    // Se a tabela estivesse vazia, remove a linha de "Nenhum documento"
                    if ($('#docsListBody').find('td[colspan="3"]').length) {
                        $('#docsListBody').empty();
                    }
                    
                    $('#docsListBody').prepend(newRow);
                    
                    // Reseta formulário
                    $('#uploadDocForm')[0].reset();
                    $('.custom-file-label').removeClass("selected").html("Escolher arquivo...");
                    $('#outros_descritivo_container').addClass('d-none');
                } else {
                    $('#uploadFeedback').addClass('alert-danger').removeClass('d-none').html('<i class="fas fa-exclamation-triangle mr-2"></i>' + response.message);
                }
                submitBtn.prop('disabled', false).html(originalBtnHtml);
            },
            error: function() {
                $('#uploadFeedback').addClass('alert-danger').removeClass('d-none').html('<i class="fas fa-exclamation-triangle mr-2"></i> Ocorreu um erro interno ao processar o upload.');
                submitBtn.prop('disabled', false).html(originalBtnHtml);
            }
        });
    });
});
</script>
<?php include_once('./include/end.php'); ?>
