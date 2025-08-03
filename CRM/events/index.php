<?php
require_once '../config/database.php';
require_once '../includes/header.php';

$sql = "SELECT e.*, p.company_name as promoter_name 
        FROM events e 
        LEFT JOIN promoters p ON e.promoter_id = p.id 
        ORDER BY e.start_date";
$result = $conn->query($sql);

$promoters_sql = "SELECT id, company_name FROM promoters ORDER BY company_name";
$promoters_result = $conn->query($promoters_sql);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Eventos</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
            Adicionar Evento
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Local</th>
                    <th>Promotor</th>
                    <th>Segmento Principal</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['start_date'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['end_date'])); ?></td>
                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                    <td><?php echo htmlspecialchars($row['state']); ?></td>
                    <td><?php echo htmlspecialchars($row['venue']); ?></td>
                    <td><?php echo htmlspecialchars($row['promoter_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['main_segment']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editEvent(<?php echo $row['id']; ?>)">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteEvent(<?php echo $row['id']; ?>)">Excluir</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar Evento -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="mb-3">
                        <label class="form-label">Nome do Evento</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Início</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data Fim</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cidade</label>
                        <input type="text" class="form-control" name="city">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <input type="text" class="form-control" name="state">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Local</label>
                        <input type="text" class="form-control" name="venue">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Promotor</label>
                        <select class="form-control" name="promoter_id">
                            <option value="">Selecione um promotor</option>
                            <?php while($promoter = $promoters_result->fetch_assoc()): ?>
                                <option value="<?php echo $promoter['id']; ?>">
                                    <?php echo htmlspecialchars($promoter['company_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" class="form-control" name="website">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Segmento Principal</label>
                        <input type="text" class="form-control" name="main_segment">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lista de Expositores</label>
                        <textarea class="form-control" name="exhibitors_list"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea class="form-control" name="observations"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="saveEvent()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>