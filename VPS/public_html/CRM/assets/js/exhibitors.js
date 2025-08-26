function saveExhibitor() {
    const form = document.getElementById('addExhibitorForm');
    const formData = new FormData(form);

    fetch('/api/save_exhibitor.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao salvar expositor: ' + data.message);
        }
    });
}

function editExhibitor(id) {
    // Implementar edição
}

function deleteExhibitor(id) {
    if (confirm('Tem certeza que deseja excluir este expositor?')) {
        fetch(`/api/delete_exhibitor.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao excluir expositor: ' + data.message);
            }
        });
    }
}