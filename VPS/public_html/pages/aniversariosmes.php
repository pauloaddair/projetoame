<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Aniversariantes do Mês</title>
    <!-- CSS do Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- JavaScript do Bootstrap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .carousel-item {
            position: relative;
            text-align: center;
        }
        .carousel-item img {
            width: 100%;
            height: auto;
            max-height: 500px;
            object-fit: contain;
        }
        .carousel-caption {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            padding: 10px;
        }
		.carousel-inner {
			display: flex;
			justify-content: center;
		}

		.carousel-item {
			flex: 0 0 70%;
			margin-right: 15px;
		}

		.carousel-item-next,
		.carousel-item-prev {
			display: flex;
		}

		.carousel-item-next:not(.carousel-item-left),
		.carousel-item-prev:not(.carousel-item-right) {
			transform: translateX(0);
		}
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center my-3">
            <button id="prevMonth" class="btn btn-primary">&laquo; Anterior</button>
            <h2 id="monthName"></h2>
            <button id="nextMonth" class="btn btn-primary">Próximo &raquo;</button>
        </div>

        <!-- Carrossel -->
        <div id="aniversariantesCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner" id="carouselContent">
                <!-- Conteúdo será carregado via AJAX -->
            </div>
            <!-- Controles do Carrossel -->
            <a class="carousel-control-prev" href="#aniversariantesCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#aniversariantesCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Próximo</span>
            </a>
        </div>
</div>

    <script>
        $(document).ready(function() {
            // Mês atual
            let currentMonth = new Date().getMonth() + 1;

            // Função para carregar os aniversariantes do mês
            function loadAniversariantes(month) {
                $.ajax({
                    url: '/include/get_aniversarios.php',
                    type: 'GET',
                    data: { mes: month },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#carouselContent').html(response.html);
                            $('#monthName').text(response.monthName);
                        } else {
                            $('#carouselContent').html('<div class="carousel-item active"><p>Nenhum aniversariante encontrado.</p></div>');
                            $('#monthName').text(response.monthName);
                        }
                    },
                    error: function() {
                        $('#carouselContent').html('<div class="carousel-item active"><p>Erro ao carregar os dados.</p></div>');
                        $('#monthName').text('Erro');
                    }
                });
            }

            // Carrega os aniversariantes do mês atual ao carregar a página
            loadAniversariantes(currentMonth);

            // Navegação entre os meses
            $('#prevMonth').click(function() {
                currentMonth = currentMonth === 1 ? 12 : currentMonth - 1;
                loadAniversariantes(currentMonth);
            });

            $('#nextMonth').click(function() {
                currentMonth = currentMonth === 12 ? 1 : currentMonth + 1;
                loadAniversariantes(currentMonth);
            });
        });
    </script>
</body>
</html>
