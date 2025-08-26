<?php $ativ = "acesso restrito";
?>
<div class="container my-3">
	<div class="row justify-content-center p-1">
		<div class="col-md-1 align-self-center"><img src="<?php echo $GLOBALS['app_web_root']; ?>img/apple-icon-152x152.png" height="32"></div>
	</div>
	<div class="jumbotron justify-content-center rounded-5 bg-danger text-white p-1">
	  <h1 class="text-center text-uppercase tipo2 mt-1">Área restrita!</h1>
	  <p class="lead text-center text-uppercase tipo1">Você precisa se identificar para acessar essa área.</p>
	  <hr class="my-4">
	  <p class="text-center text-uppercase tipo1"><small>Identifique-se ou cadastre-se para receber autorização para acessá-la.</small></p>
	  <p class="lead text-center text-uppercase tipo3">
		<a class="btn bg-primary btn-sm rounded-pill text-white" href="<?php echo $GLOBALS['app_web_root']; ?>login" role="button">Identifique-se</a>
	  </p>
	</div>
</div>