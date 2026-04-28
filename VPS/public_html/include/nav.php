<!-- Navbar -->
<nav class="navbar fixed-top navbar-expand-lg navbar-light navbar-fixed  bg-primary bg-gradient">
<div class="container">

      <!-- Brand -->
      <a class="navbar-brand" href="<?php echo $app_web_root; ?>">
		 <img src="<?php echo $app_web_root; ?>img/android-icon-36x36.png" height="32" class="rounded-pill" ><strong class="text-success">A</strong>tendentes <strong class="text-warning">M</strong>uito <strong class="text-danger">E</strong>speciais
      </a>

      <!-- Collapse -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Links -->
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left -->
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="<?php echo $app_web_root; ?>">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
<!--
          <li class="nav-item">
            <a class="nav-link" href="/sobre">Sobre nós</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/servicos">Nossos serviços</a>
          </li>
-->
          <li class="nav-item">
            <a class="nav-link" href="<?php echo $app_web_root; ?>contato">Contato</a>
          </li>
        </ul>

        <!-- Right -->
        <ul class="navbar-nav nav-flex-icons">
<!--
          <li class="nav-item">
            <a href="" class="nav-link" target="_blank">
              <i class="fab fa-facebook-f"></i>
            </a>
          </li>
          <li class="nav-item">
            <a href="" class="nav-link" target="_blank">
              <i class="fab fa-twitter"></i>
            </a>
          </li>
          <li class="nav-item">
            <a href="" class="nav-link" target="_blank">
              <i class="fab fa-instagram"></i>
            </a>
          </li>
-->
			<?php $logado = "none";
            $login = "visible";
				$perfil = "Visitante";
				if (isset($_SESSION['nome'])){
					$perfil = $_SESSION['nome'];
                    $logado = "visible";
                    $login = "none";
				}
				$foto = "img/profile.png";
				if (isset($_SESSION['perfil'])){
					$foto = $_SESSION['perfil'];
				}
			?>
<div class="dropdown">
  <li class="nav-item avatar dropdown-toggle border border-gray rounded white-text p-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	  <img id = "fotouser" src="<?php echo $app_web_root; ?><?php echo $foto?>" class="rounded-circle z-depth-0 "
            alt="avatar image" height="32"><span id="nomeusuario-barra" class=" m-2"><?php echo $perfil?></span><span id="badgeicon" class="badge badge-danger ml-2 rounded" data-mdb-toggle="tooltip" title="0 notificações">0</span>  </li>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
<!--
    <a class="dropdown-item" href="<?php echo $app_web_root; ?>login">Login</a>
    <a class="dropdown-item" href="<?php echo $app_web_root; ?>cadastro">Cadastre-se</a>
    <a class="dropdown-item" href="privacidade">Política de privacidade</a>
-->
	<div id="div-login" style="display: <?php echo $login?>">
		<hr class="dropdown-divider" />
		<a class="dropdown-item mx-2" href="<?php echo $app_web_root; ?>login"><i class="fas fa-sign-in-alt light-blue-text mx-2"></i>Login</a>
		<a class="dropdown-item mx-2" href="<?php echo $app_web_root; ?>cadastro" role="button"><i class="fas fa-table light-blue-text mx-2"></i>Cadastre-se</a>
		<a class="dropdown-item mx-2" href="<?php echo $app_web_root; ?>privacidade" role="button"><i class="fas fa-user-secret light-blue-text mx-2"></i>Política de privacidade</a>
	</div>
	<div id="div-logado" style="display: <?php echo $logado?>">		
		<a class="dropdown-item" href="<?php echo $app_web_root; ?>perfil"><i class="fas fa-user-alt light-blue-text mx-2"></i>&nbsp;Perfil</a>
		<a class="dropdown-item" href="<?php echo $app_web_root; ?>admin"><i class="fas fa-columns light-blue-text mx-2"></i>Painel</a>
    <?php if (isset($_SESSION['nivel']) && $_SESSION['nivel'] >= 3): ?>
		<a class="dropdown-item" href="<?php echo $app_web_root; ?>atestados"><i class="fas fa-certificate light-blue-text mx-2"></i>&nbsp;Atestados</a>
    <?php endif; ?>
		<hr class="dropdown-divider" />
		<a class="dropdown-item" href="<?php echo $app_web_root; ?>logout"><i class="fas fa-sign-out-alt light-blue-text"></i>&nbsp;Sair</a>
	</div>
  </div>
</div>

<!--
      <li class="nav-item avatar dropdown">
        <a class="nav-link dropdown-toggle border border-light rounded" id="navbarDropdownMenuLink-55" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false">
          <img id = "fotouser" src="/img/android-icon-72x72.png" class="rounded-circle z-depth-0"
            alt="avatar image" height="32"><?php echo $perfil?><span class="badge badge-danger ml-2">0</span>
        </a>
      <div class="dropdown-menu dropdown-menu-lg-left dropdown-secondary"
          aria-labelledby="navbarDropdownMenuLink-55">
          <ul class="navbar-nav nav-flex-icons">
          <a class="dropdown-item" href="#"><spam id="nomeusuario">Login ou Cadastre-se</spam></a>
			<div id="div-login">
		  <a class="dropdown-item" href="#" class="mx-2" role="button" onClick="loginwithFacebook()"><i class="fab fa-facebook-f light-blue-text"></i>&nbsp;com o Facebook</a>
			<a class="dropdown-item" href="#" class="mx-2" role="button" onClick="loginwithTwitter()"><i class="fab fa-twitter light-blue-text"></i>&nbsp;com o Twitter</a>
			<a class="dropdown-item" href="#" class="mx-2" role="button" onClick="loginwithGoogle()"><i class="fab fa-google light-blue-text"></i>&nbsp;com o Google</a>
				</div>
       		<a class="dropdown-item" href="#">Seu porta-cartões<span class="badge badge-danger ml-2">0</span></a>
          <a class="dropdown-item" onClick="logout()" href="#">Sair</a>
		  </ul>
        </div>
      </li>
-->

          </li>

        </ul>



      </div>



    </div>

  </nav>
  <!-- Navbar -->
