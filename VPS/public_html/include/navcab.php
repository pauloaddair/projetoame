<div class="row justify-content-md-center text-center p-1">
		<div class="col col-md-2">
			<a href = '/'><img src="/img/card-2x1-nobg.png" height="96"></a>
		</div>
		<div class="col col-md-4">
			<h1 class="text-center"><?php echo $titulo?></h1>
		</div>
		<div class="col col-md-2">
	<?php $perfil = "img/ms-icon-310x310.png";
			if($_SESSION['perfil']<>""){
				$perfil = $_SESSION['perfil'];
			}
		echo "<a href = '/perfil'><img src='/".$perfil."' class='img-thumbnail rounded-circle mx-auto' width=48>";
		echo "<p>".$_SESSION['nome']."</p></a>";
	?>
			<a href="/logout"><i class='fas fa-arrow-right ms-5' data-toggle='tooltip' data-placement='right' title='Sair'></i></a>
		</div>
	</div>
