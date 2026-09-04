<!DOCTYPE html>
<html lang="pt-BR" class="notranslate" translate="no">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Language" content="pt-BR">
<meta name="google" content="notranslate">

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
<meta name="msapplication-TileImage" content="<?php echo $GLOBALS['app_web_root']; ?>img/ms-icon-144x144.png">
<meta name="theme-color" content="#ffffff">

<!-- Bootstrap core CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<!-- Material Design Bootstrap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.20.0/css/mdb.min.css">
<!-- Your custom styles -->
<link href="<?php echo $GLOBALS['app_web_root']; ?>css/style.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<?php if (!in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'])): ?>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9127792999705320" crossorigin="anonymous"></script>
<?php endif; ?>
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
		border: 1px solid #ffff00 !important;
	}
	
	/* Fonte Grande */
	body.fonte-grande {
		font-size: 120% !important;
	}
	body.fonte-grande h1 { font-size: 2.5rem !important; }
	body.fonte-grande h2 { font-size: 2.2rem !important; }
	body.fonte-grande h3 { font-size: 1.8rem !important; }
	body.fonte-grande h4 { font-size: 1.5rem !important; }
	body.fonte-grande p, 
	body.fonte-grande span, 
	body.fonte-grande a, 
	body.fonte-grande li, 
	body.fonte-grande td, 
	body.fonte-grande th {
		font-size: 1.25rem !important;
	}
</style>

<script>
	// Carrega preferências ao iniciar
	document.addEventListener('DOMContentLoaded', () => {
		if (localStorage.getItem('alto-contraste') === 'true') {
			document.body.classList.add('alto-contraste');
			document.documentElement.classList.add('alto-contraste');
		}
		if (localStorage.getItem('fonte-grande') === 'true') {
			document.body.classList.add('fonte-grande');
			document.documentElement.classList.add('fonte-grande');
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
</head>