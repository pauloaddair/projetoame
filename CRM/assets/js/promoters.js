function savePromoter() {
    const form = document.getElementById('addPromoterForm');
    const formData = new FormData(form);

    fetch('/api/save_promoter.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao salvar promotor: ' + data.message);
        }
    });
}

function editPromoter(id) {
    // Implementar edição
}

function deletePromoter(id) {
    if (confirm('Tem certeza que deseja excluir este promotor?')) {
        fetch(`/api/delete_promoter.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao excluir promotor: ' + data.message);
            }
        });
    }
}