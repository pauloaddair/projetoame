<?php
require_once '../config/database.php';
require_once '../includes/header.php';

$sql = "SELECT * FROM exhibitors ORDER BY company_name";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Expositores</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExhibitorModal">
            Adicionar Expositor
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Segmento</th>
                    <th>Origem do Lead</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['cnpj']); ?></td>
                    <td><?php echo htmlspecialchars($row['main_phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['segment']); ?></td>
                    <td><?php echo htmlspecialchars($row['lead_source']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editExhibitor(<?php echo $row['id']; ?>)">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteExhibitor(<?php echo $row['id']; ?>)">Excluir</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar Expositor -->
<div class="modal fade" id="addExhibitorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Expositor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addExhibitorForm">
                    <div class="mb-3">
                        <label class="form-label">Nome da Empresa</label>
                        <input type="text" class="form-control" name="company_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" class="form-control" name="cnpj">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input type="url" class="form-control" name="website">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="tel" class="form-control" name="main_phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <textarea class="form-control" name="address"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Segmento</label>
                        <input type="text" class="form-control" name="segment">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Origem do Lead</label>
                        <input type="text" class="form-control" name="lead_source">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="saveExhibitor()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>