function saveEvent() {
    const form = document.getElementById('addEventForm');
    const formData = new FormData(form);

    fetch('/api/save_event.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao salvar evento: ' + data.message);
        }
    });
}

function editEvent(id) {
    // Implementar edição
}

function deleteEvent(id) {
    if (confirm('Tem certeza que deseja excluir este evento?')) {
        fetch(`/api/delete_event.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao excluir evento: ' + data.message);
            }
        });
    }
}