<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Resumo de Contatos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        .chart-container { 
            width: 45%; 
            display: inline-block; 
            vertical-align: top; 
            margin: 20px; 
        }
        canvas { max-width: 100%; }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
	<div class="container">
		<header><h1 class="text-center">Resumo dos contatos</h1>
			<hr>
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/admin/relatorios">Relatórios</a></li>
				<li class="breadcrumb-item" aria-current="page">Resumo</li>
				<li class="breadcrumb-item active"><a href="/admin/eventos">Contatos</a></li>
			  </ol>
			</nav>
		</header>
		<h1>Dashboard - Resumo de Contatos</h1>
		<div class="chart-container">
			<h2>Status dos Leads</h2>
			<canvas id="leadsChart"></canvas>
		</div>
		<div class="chart-container">
			<h2>Estandes por Evento</h2>
			<canvas id="expositoresChart"></canvas>
		</div>
	</div>

    <script>
        // Configurar o gráfico de pizza (Leads por Status)
        function carregarLeads() {
            $.get('/include/resumo_leads.php', function(data) {
                const ctx = document.getElementById('leadsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: [
                                '#FF6384', // Pendente
                                '#36A2EB', // Em andamento
                                '#4BC0C0', // Convertido
                                '#FFCE56'  // Perdido
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'Distribuição dos Leads por Status' }
                        }
                    }
                });
            });
        }

        // Configurar o gráfico de barras (Expositores por Evento)
        function carregarExpositores() {
            $.get('/include/resumo_expositores.php', function(data) {
                const ctx = document.getElementById('expositoresChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Número de Estandes',
                            data: data.values,
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Quantidade' } },
                            x: { title: { display: true, text: 'Eventos' } }
                        },
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Estandes por Evento' }
                        }
                    }
                });
            });
        }

        // Carregar os gráficos ao abrir a página
        $(document).ready(function() {
            carregarLeads();
            carregarExpositores();
        });
    </script>
</body>
</html>