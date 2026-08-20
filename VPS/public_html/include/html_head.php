<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
$site_title = isset($titulo) ? htmlspecialchars($titulo) : 'A.B.I.A.T. — Projeto A.M.E.';
$meta_og_title = isset($og_title) ? htmlspecialchars($og_title) : 'Projeto A.M.E. - Atendentes Muito Especiais';
$meta_og_desc = isset($og_description) ? htmlspecialchars($og_description) : 'Associação Brasileira de Inclusão Através do Trabalho - A.M.E.';
$meta_og_img = isset($og_image) ? htmlspecialchars($og_image) : 'https://projetoame.org/img/ame2023.jpg';
$meta_og_url = isset($og_url) ? htmlspecialchars($og_url) : 'https://projetoame.org/';
?>

<!-- HTML Meta Tags -->
<title><?php echo $site_title; ?></title>
<meta name="description" content="<?php echo $meta_og_desc; ?>">

<!-- Facebook / OpenGraph Meta Tags -->
<meta property="og:site_name" content="Projeto A.M.E."/>
<meta property="og:url" content="<?php echo $meta_og_url; ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo $meta_og_title; ?>">
<meta property="og:description" content="<?php echo $meta_og_desc; ?>">
<meta property="og:image" content="<?php echo $meta_og_img; ?>">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:domain" content="projetoame.org">
<meta property="twitter:url" content="<?php echo $meta_og_url; ?>">
<meta name="twitter:title" content="<?php echo $meta_og_title; ?>">
<meta name="twitter:description" content="<?php echo $meta_og_desc; ?>">
<meta name="twitter:image" content="<?php echo $meta_og_img; ?>">

<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="<?php echo $GLOBALS['app_web_root']; ?>img/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $GLOBALS['app_web_root']; ?>img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $GLOBALS['app_web_root']; ?>img/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $GLOBALS['app_web_root']; ?>img/favicon-16x16.png">
<link rel="manifest" href="<?php echo $GLOBALS['app_web_root']; ?>img/manifest.json">

<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/img/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">

<!-- Bootstrap core CSS -->
<link href="<?php echo $GLOBALS['app_web_root']; ?>css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<!-- Material Design Bootstrap -->
<link href="<?php echo $GLOBALS['app_web_root']; ?>css/mdb.min.css" rel="stylesheet">
<!-- Your custom styles -->
<link href="<?php echo $GLOBALS['app_web_root']; ?>css/style.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9127792999705320" crossorigin="anonymous"></script>
<!-- Font Awesome & Bootstrap Icons -->
<script src="https://kit.fontawesome.com/d067a28273.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
	.btn-primary { border-radius: 25px; }	
	.btn-info { border-radius: 25px; }

	/* Alto Contraste */
	body.alto-contraste {
		background-color: #000000 !important;
		color: #ffffff !important;
	}
	body.alto-contraste .card, 
	body.alto-contraste .navbar, 
	body.alto-contraste footer, 
	body.alto-contraste div, 
	body.alto-contraste section, 
	body.alto-contraste table,
	body.alto-contraste tr,
	body.alto-contraste td,
	body.alto-contraste th,
	body.alto-contraste h1,
	body.alto-contraste h2,
	body.alto-contraste h3,
	body.alto-contraste h4,
	body.alto-contraste h5,
	body.alto-contraste h6,
	body.alto-contraste p,
	body.alto-contraste span,
	body.alto-contraste a {
		background-color: #000000 !important;
		color: #ffff00 !important; /* Texto em amarelo */
		border-color: #ffff00 !important;
	}
	body.alto-contraste a {
		text-decoration: underline !important;
	}
	body.alto-contraste button, 
	body.alto-contraste .btn {
		background-color: #000000 !important;
		color: #ffff00 !important;
		border: 2px solid #ffff00 !important;
	}
	
	/* Fonte Grande */
	body.fonte-grande {
		font-size: 1.25rem !important;
	}
	body.fonte-grande p, 
	body.fonte-grande td, 
	body.fonte-grande li, 
	body.fonte-grande span, 
	body.fonte-grande input, 
	body.fonte-grande select, 
	body.fonte-grande textarea {
		font-size: 1.25rem !important;
	}
	body.fonte-grande h1 { font-size: 2.5rem !important; }
	body.fonte-grande h2 { font-size: 2rem !important; }
	body.fonte-grande h3 { font-size: 1.75rem !important; }
</style>

<script>
	// Executa imediatamente para evitar flash visual
	(function() {
		const contrast = localStorage.getItem('alto-contraste');
		const fontSize = localStorage.getItem('fonte-grande');
		if (contrast === 'true') {
			document.documentElement.classList.add('alto-contraste');
		}
		if (fontSize === 'true') {
			document.documentElement.classList.add('fonte-grande');
		}
	})();
	
	window.addEventListener('DOMContentLoaded', () => {
		if (document.documentElement.classList.contains('alto-contraste')) {
			document.body.classList.add('alto-contraste');
		}
		if (document.documentElement.classList.contains('fonte-grande')) {
			document.body.classList.add('fonte-grande');
		}
	});

	function toggleContrast() {
		const isContrast = document.body.classList.toggle('alto-contraste');
		document.documentElement.classList.toggle('alto-contraste', isContrast);
		localStorage.setItem('alto-contraste', isContrast);
	}

	function toggleFontSize() {
		const isLarge = document.body.classList.toggle('fonte-grande');
		document.documentElement.classList.toggle('fonte-grande', isLarge);
		localStorage.setItem('fonte-grande', isLarge);
	}
</script>

<!-- Barra Flutuante de Acessibilidade -->
<div id="barra-acessibilidade" style="position: fixed; top: 120px; left: 10px; z-index: 99999; display: flex; flex-direction: column; gap: 8px;">
	<button onclick="toggleContrast()" class="btn btn-sm btn-dark px-2 py-1 m-0" title="Alto Contraste" style="border-radius: 4px; font-size: 12px; box-shadow: 0px 2px 5px rgba(0,0,0,0.3); border: 1px solid #ccc; background-color: #333; color: #fff;"><i class="fas fa-adjust"></i> Contraste</button>
	<button onclick="toggleFontSize()" class="btn btn-sm btn-info px-2 py-1 m-0" title="Aumentar Fonte" style="border-radius: 4px; font-size: 12px; box-shadow: 0px 2px 5px rgba(0,0,0,0.3); border: 1px solid #ccc; background-color: #17a2b8; color: #fff;"><i class="fas fa-font"></i> A +</button>
</div>

<!-- VLibras Widget -->
<div vlibras-widget class="vlibras-widget">
	<div class="vlibras-widget-wrapper">
		<div class="vlibras-widget-content">
			<div class="vlibras-widget-content-buttons">
				<div class="vlibras-widget-content-buttons-button vlibras-widget-content-buttons-button-close"></div>
			</div>
		</div>
	</div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
	new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>