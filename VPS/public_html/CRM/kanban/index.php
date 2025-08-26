<?php
require_once '../config/database.php';
require_once '../includes/header.php';

$stages = [
    'backlog' => 'Backlog/Ideias',
    'initial_prospecting' => 'Prospecção Inicial',
    'contact_made' => 'Contato Realizado/Qualificação',
    'proposal_sent' => 'Proposta Enviada',
    'negotiation' => 'Negociação',
    'closed_won' => 'Fechado Ganho',
    'closed_lost' => 'Fechado Perdido',
    'standby' => 'Standby/Follow-up Futuro'
];
?>

<div class="container-fluid">
    <h1 class="my-4">Pipeline de Prospecção</h1>
    
    <div class="row">
        <div class="col-12 mb-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newOpportunityModal">
                Nova Oportunidade
            </button>
        </div>
    </div>

    <div class="kanban-board row flex-nowrap overflow-auto">
        <?php foreach ($stages as $stage_key => $stage_name): ?>
        <div class="col-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><?php echo $stage_name; ?></h5>
                </div>
                <div class="card-body kanban-stage" data-stage="<?php echo $stage_key; ?>">
                    <?php
                    $sql = "SELECT o.*, 
                            CASE 
                                WHEN o.entity_type = 'promoter' THEN p.company_name
                                ELSE e.company_name
                            END as company_name
                            FROM opportunities o
                            LEFT JOIN promoters p ON o.entity_type = 'promoter' AND o.entity_id = p.id
                            LEFT JOIN exhibitors e ON o.entity_type = 'exhibitor' AND o.entity_id = e.id
                            WHERE o.stage = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('s', $stage_key);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($opportunity = $result->fetch_assoc()):
                    ?>
                    <div class="card mb-2 opportunity-card" data-id="<?php echo $opportunity['id']; ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($opportunity['title']); ?></h6>
                            <p class="card-text">
                                <?php echo htmlspecialchars($opportunity['company_name']); ?>
                                <br>
                                <small class="text-muted">
                                    <?php echo ucfirst($opportunity['entity_type']); ?>
                                </small>
                            </p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- New Opportunity Modal -->
<div class="modal fade" id="newOpportunityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Oportunidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="opportunityForm">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select class="form-select" name="entity_type" required>
                            <option value="promoter">Promotor</option>
                            <option value="exhibitor">Expositor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Empresa</label>
                        <select class="form-select" name="entity_id" required>
                            <!-- Will be populated via AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Evento</label>
                        <select class="form-select" name="event_id">
                            <!-- Will be populated via AJAX -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveOpportunity">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>