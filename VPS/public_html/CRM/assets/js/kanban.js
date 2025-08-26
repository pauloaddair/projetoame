document.addEventListener('DOMContentLoaded', function() {
    // Initialize drag and drop
    const cards = document.querySelectorAll('.opportunity-card');
    const stages = document.querySelectorAll('.kanban-stage');

    cards.forEach(card => {
        card.setAttribute('draggable', true);
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
    });

    stages.forEach(stage => {
        stage.addEventListener('dragover', handleDragOver);
        stage.addEventListener('drop', handleDrop);
    });

    // Handle entity type change
    document.querySelector('select[name="entity_type"]').addEventListener('change', function() {
        loadEntities(this.value);
    });

    // Load events
    loadEvents();
});

function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.id);
    e.target.classList.add('dragging');
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDrop(e) {
    e.preventDefault();
    const opportunityId = e.dataTransfer.getData('text/plain');
    const newStage = e.target.closest('.kanban-stage').dataset.stage;

    updateOpportunityStage(opportunityId, newStage);
}

function updateOpportunityStage(opportunityId, newStage) {
    fetch('../api/update_opportunity_stage.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            opportunity_id: opportunityId,
            stage: newStage
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`.opportunity-card[data-id="${opportunityId}"]`);
            const newStageElement = document.querySelector(`.kanban-stage[data-stage="${newStage}"]`);
            newStageElement.appendChild(card);
        } else {
            alert('Erro ao atualizar o estágio da oportunidade');
        }
    });
}

function loadEntities(entityType) {
    fetch(`../api/get_entities.php?type=${entityType}`)
        .then(response => response.json())
        .then(data => {
            const select = document.querySelector('select[name="entity_id"]');
            select.innerHTML = '<option value="">Selecione...</option>';
            data.forEach(entity => {
                select.innerHTML += `<option value="${entity.id}">${entity.company_name}</option>`;
            });
        });
}

function loadEvents() {
    fetch('../api/get_events.php')
        .then(response => response.json())
        .then(data => {
            const select = document.querySelector('select[name="event_id"]');
            select.innerHTML = '<option value="">Selecione...</option>';
            data.forEach(event => {
                select.innerHTML += `<option value="${event.id}">${event.name}</option>`;
            });
        });
}

document.getElementById('saveOpportunity').addEventListener('click', function() {
    const form = document.getElementById('opportunityForm');
    const formData = new FormData(form);

    fetch('../api/create_opportunity.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao criar oportunidade');
        }
    });
});