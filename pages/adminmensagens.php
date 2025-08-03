<?php
$titulo = "Admin Mensagens";
include 'include/head.php';
?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function carregarMensagens() {
                $.ajax({
                    url: "/include/buscar_mensagens.php",
                    method: "GET",
                    success: function(data) {
                        $("#listaMensagens").html(data);
                    }
                });
            }

            $("#formMensagem").on("submit", function(event) {
                event.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "/include/salvar_mensagens.php",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        $("#mensagemModal").modal("show");
                        carregarMensagens();
                    }
                });
            });

            carregarMensagens();
        });
    </script>
<body>
    <div class="container mt-4">
        <h2>Administração de Mensagens</h2>
        <form id="formMensagem">
            <div class="mb-3">
                <label for="destinatario" class="form-label">Destinatário</label>
                <input type="text" class="form-control" id="destinatario" name="destinatario" required>
            </div>
            <div class="mb-3">
                <label for="mensagem" class="form-label">Mensagem</label>
                <textarea class="form-control" id="mensagem" name="mensagem" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
        </form>

        <h3 class="mt-4">Histórico de Mensagens</h3>
        <div id="listaMensagens" class="border p-3 rounded bg-light"></div>
    </div>
<?php
include 'include/footer.php';
?>
</body>
<?
include 'include/scripts.php';
include 'include/end.php';
include 'include/head.php';
?>
