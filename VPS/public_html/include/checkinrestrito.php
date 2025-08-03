	<div class="container my-3">
		<?php $msg = " o seu código de ingresso ";
		if(array_key_exists(1,$parametros)){
			$msg = " este código ";
		}
		?>
		<div class="row justify-content-center p-1">
			<div class="col-md-1 align-self-center"><img src="/img/card-2x1.png" height="32"></div>
		</div>
		<div class="jumbotron justify-content-center rounded-5 bg0 text-white p-1">
		  <h1 class="text-center text-uppercase tipo2 mt-1">Bem vindos!</h1>
			<?php if (isset($nome)){
				echo "<p class='text-center'>".$nome."</p>";
			}
			?>
		  <p class="lead text-center text-uppercase tipo1">É necessário apresentar <?php echo $msg?> a um funcionário autorizado na entrada do evento.</p>
		  <hr class="my-4">
<!--
		  <p class="text-center text-uppercase tipo1"><small>Identifique-se ou cadastre-se para receber autorização para acessá-la.</small></p>
		  <p class="lead text-center text-uppercase tipo3">
			<a class="btn bg6 btn-sm rounded-pill text-white" href="/login" role="button">Identifique-se</a>
		  </p>
-->
		</div>
	</div>