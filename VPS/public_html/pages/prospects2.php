<?php
include 'include/conexao.php';
include 'include/funcoes.php';
include 'include/head.php';
?>
<body>
    <div class="container mt-4">
        <h2>Registrar Prospect</h2>
        <form id="formProspect">
            <input type="hidden" id="prospect_id" name="prospect_id">
            <div class="mb-3 position-relative">
                <label for="evento" class="form-label">Evento</label>
                <input type="text" class="form-control" id="evento" name="evento" placeholder="Digite o nome do evento">
                <div id="lista-eventos" class="list-group position-absolute w-100" style="display: none;"></div>
            </div>
            <div class="mb-3 position-relative">
                <label for="expositor" class="form-label">Expositor</label>
                <input type="text" class="form-control" id="expositor" name="expositor" placeholder="Digite o nome do expositor">
                <div id="lista-expositores" class="list-group position-absolute w-100" style="display: none;"></div>
            </div>
            <div class="mb-3 position-relative">
                <label for="prospect" class="form-label">Prospect</label>
                <input type="text" class="form-control" id="prospect" name="prospect" placeholder="Buscar prospect">
                <div id="lista-prospects" class="list-group position-absolute w-100" style="display: none;"></div>
            </div>
            <div class="mb-3">
                <label for="anotacao" class="form-label">Nova Anotação</label>
                <textarea class="form-control" id="anotacao" name="anotacao" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>

        <h3 class="mt-4">Histórico de Anotações</h3>
        <div id="historicoAnotacoes" class="border p-3 rounded bg-light"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
include 'include/footer.php';
?>
</body>
<?php
include 'include/scripts.php';
?>
    <script>
        $(document).ready(function() {
            function carregarAnotacoes(prospect_id) {
                $.ajax({
                    url: "/include/buscar_anotacoes.php",
                    method: "POST",
                    data: {prospect_id: prospect_id},
                    success: function(data) {
                        $("#historicoAnotacoes").html(data);
                    }
                });
            }
            
            $("#evento").on("input", function() {
                let query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: "/include/buscar_evento.php",
                        method: "POST",
                        data: {query: query},
                        success: function(data) {
                            $("#lista-eventos").html(data).show();
                        }
                    });
                } else {
                    $("#lista-eventos").hide();
                }
            });
            
            $("#expositor").on("input", function() {
                let query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: "/include/buscar_expositor.php",
                        method: "POST",
                        data: {query: query},
                        success: function(data) {
                            $("#lista-expositores").html(data).show();
                        }
                    });
                } else {
                    $("#lista-expositores").hide();
                }
            });

            $("#prospect").on("input", function() {
                let query = $(this).val();
                if (query.length > 2) {
                    $.ajax({
                        url: "/include/buscar_prospects.php",
                        method: "POST",
                        data: {query: query},
                        success: function(data) {
                            $("#lista-prospects").html(data).show();
                        }
                    });
                } else {
                    $("#lista-prospects").hide();
                }
            });

            $("#formProspect").on("submit", function(event) {
                event.preventDefault();
                let formData = $(this).serialize();
				console.log("salvar prospect");
                $.ajax({
                    url: "/include/salvar_prospect.php",
                    method: "POST",
                    data: formData,
                    success: function(response) {
						alert("Prospect salvo com sucesso!");
                        $("#mensagemModal").modal("show");
                        carregarAnotacoes($("#prospect_id").val());
                    }
                });
            });
        });
    </script>
<?
include 'include/end.php';
?>
