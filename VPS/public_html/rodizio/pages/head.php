<?PHP
include_once("include/conexao.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>

	<meta charset="utf-8">
<!--	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">-->


	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8"/>

	<!-- HTML Meta Tags -->
	<title>Projeto A.M.E. - <?echo $titulo?></title>
	<meta name="description" content="Associação Brasileira de Inclusão Através do Trabalho - A.M.E. - Atendentes Muito Especiais.">

	<!-- Facebook Meta Tags -->
	<meta property="og:site_name" content="Projeto A.M.E."/>
	<meta property="og:url" content="https://projetoame.org/atendentes/">
	<meta property="og:type" content="website">
	<meta property="og:title" content="Projeto A.M.E.">
	<meta property="og:description" content="Associação Brasileira de Inclusão Através do Trabalho - A.M.E. - Atendentes Muito Especiais.">
	<meta property="og:image" content="https://projetoame.org/atendentes/">

	<!-- Twitter Meta Tags -->
	<meta name="twitter:card" content="summary_large_image">
	<meta property="twitter:domain" content="contacte.me">
	<meta property="twitter:url" content="https://projetoame.org/atendentes/">
	<meta name="twitter:title" content="Associação Brasileira de Inclusão Através do Trabalho - A.M.E. - Atendentes Muito Especiais.">
	<meta name="twitter:description" content="Uma forma inovadora de trocar informações de contato e fazer novos relacionamentos.">
	<meta name="twitter:image" content="https://projetoame.org/atendentes/img/ms-icon-310x310.png">

	<!-- Meta Tags Generated via https://www.opengraph.xyz -->
      

	<link rel="apple-touch-icon" sizes="57x57" href="../atendentes/img/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="../atendentes/img/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="../atendentes/img/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="../atendentes/img/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="../atendentes/img/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="../atendentes/img/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="../atendentes/img/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="../atendentes/img/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="../atendentes/img/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="../atendentes/img/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="../atendentes/img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="../atendentes/img/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="../atendentes/img/favicon-16x16.png">
	<link rel="manifest" href="../atendentes/img/manifest.json">

	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="img/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
	

	<!-- Bootstrap core CSS -->
	<link href="../atendentes/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">

<!--	 Material Design Bootstrap -->
	<link href="../atendentes/css/mdb.min.css" rel="stylesheet">

	<!-- Your custom styles (optional) -->
	<link href="../atendentes/css/style.min.css" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.gstatic.com">

	<!-- Font Awesome -->
	<script src="https://kit.fontawesome.com/d067a28273.js" crossorigin="anonymous"></script>
<!--	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">-->
<style>
	.btn-primary {
		border-radius: 25px;
	}	
	.btn-info {
		border-radius: 25px;
	}	
</style>
	</head>

