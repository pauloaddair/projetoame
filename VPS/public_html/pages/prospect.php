<?php
// include 'include/conexao.php';
include 'include/funcoes.php';
// include 'include/head.php';
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
                <label for="prospect" class="form-label">Contato</label>
                <input type="text" class="form-control" id="contato" name="contato" placeholder="Buscar prospect">
                <div id="lista-prospects" class="list-group position-absolute w-100" style="display: none;"></div>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Número de telefone">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="E-mail do contato">
            </div>
            <div class="mb-3">
                <label for="redes" class="form-label">Rede Social</label>
                <input type="text" class="form-control" id="redes" name="redes" placeholder="Instagram, Facebook, etc.">
            </div>
            <div class="mb-3">
                <label for="agendamento" class="form-label">Agendar contato</label>
                <input type="date" class="form-control" id="agendamento" name="agendamento">
            </div>
            <div class="mb-3">
                <label for="anotacao" class="form-label">Anotação</label>
                <textarea class="form-control" id="anotacao" name="anotacao" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>

        <h3 class="mt-4">Histórico de Anotações</h3>
        <div id="historicoAnotacoes" class="border p-3 rounded bg-light"></div>
    </div>
	<?php 
	$sql = "SELECT * FROM mensagens;";
	$msgs = mysqli_query($conexao,$sql);
	?>
	<div class="modal fade" id="mensagemModal" tabindex="-1" aria-labelledby="mensagemModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="mensagemModalLabel">Enviar Mensagem</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="prospect_id" value=""> <!-- ID do prospect salvo -->

					<label for="meio_contato" class="form-label">Escolha o meio de contato:</label>
					<select id="meio_contato" class="form-control">
						<option value="whatsapp">WhatsApp</option>
						<option value="email">E-mail</option>
						<option value="instagram">Instagram</option>
						<option value="facebook">Facebook</option>
					</select>

					<label for="mensagem_padrao" class="form-label mt-3">Escolha uma mensagem padrão:</label>
					<select id="mensagem_padrao" class="form-control">
						<option value="">Selecione...</option>
						<!-- Mensagens serão carregadas aqui via AJAX -->
						<?php 
						while ($row=mysqli_fetch_array($msgs)){
						?>
						<option value="<?php echo $row['mensagem']?>"><?php echo $row['titulo']?></option>
						<?php 						
						}
						?>
					</select>
					<input type="hidden" id="mensagem_customizada" value="">
					<label for="mensagem" class="form-label mt-3">Ou escreva sua própria mensagem:</label>
					<textarea id="mensagem" class="form-control" rows="3"></textarea>

					<button id="enviarMensagem" class="btn btn-primary mt-3">Enviar</button>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
include 'include/footer.php';
include 'include/scripts.php';
?>
</body>
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

			$.ajax({
				url: "/include/salvar_prospect.php",
				method: "POST",
				data: formData,
				dataType: "json",
				success: function (response) {
					if (response.success) {
						$("#mensagemModal").modal("show"); // Exibir modal após o salvamento
					} else {
						alert("Erro ao salvar prospect: " + response.mensagem);
					}
				},
				error: function () {
					alert("Houve um erro ao tentar salvar.");
				}
			});
        });
			
		$("#mensagemModal").on("show.bs.modal", function () {
			alert('Modal show');
			$.ajax({
				url: "/include/buscar_msg_padrao.php",
				method: "GET",
				dataType: "json",
				success: function (data) {
					let select = $("#mensagem_padrao");
					select.empty();
					select.append('<option value="">Selecione...</option>');
					data.forEach(msg => {
						select.append(`<option value="${msg.mensagem}">${msg.titulo}</option>`);
					});
				}
			});
		});

		$("#meio_contato").change(function () {
			let meio = $(this).val();
			let mensagem = $("#mensagem_customizada").val();
			$("#mensagem").val(mensagem);
			if (meio === "whatsapp") {
				mensagem = encodeURIComponent(mensagem);
			} else if (meio === "email") {
				mensagem = "Assunto: Novo Contato\n\n" + mensagem;
			}

			$("#mensagem_customizada").val(mensagem);
		});
			
		$("#enviarMensagem").click(function () {
			let meio = $("#meio_contato").val();
			let mensagem = $("#mensagem_customizada").val();
			let prospectId = $("#prospect_id").val();

			if (!mensagem.trim()) {
				alert("Por favor, escolha ou escreva uma mensagem.");
				return;
			}

			$.ajax({
				url: "/include/buscar_contato.php",
				method: "GET",
				data: { id: prospectId },
				dataType: "json",
				success: function (data) {
					let contato = data.telefone;
					let email = data.email;

					if (meio === "whatsapp") {
						window.open(`https://wa.me/${contato}?text=${encodeURIComponent(mensagem)}`, "_blank");
					} else if (meio === "email") {
						window.open(`mailto:${email}?subject=Contato&body=${encodeURIComponent(mensagem)}`, "_blank");
					} else if (meio === "instagram") {
						alert("Copie e envie a mensagem manualmente no Instagram.");
					} else if (meio === "facebook") {
						alert("Copie e envie a mensagem manualmente no Messenger.");
					}
				}
			});
		});

	});
    </script>
<?php 
include 'include/end.php';
?>
