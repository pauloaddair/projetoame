  <nav class="navbar fixed-top navbar-expand-lg navbar-dark scrolling-navbar">
    <div class="container">

      <!-- Brand -->
      <a class="navbar-brand" href="/">
        <img src="../assets/contacteme.png" alt=""><strong>CONTACTE.ME</strong>
      </a>

      <!-- Collapse -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Links -->
      <div class="collapse navbar-collapse" id="navbarSupportedContent">

        <!-- Left -->
        <ul class="navbar-nav mr-auto">
          <li class="nav-item <?php echo ($menu0)?>">
            <a class="nav-link" href="/">Inicio
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item <?php echo ($menu1)?>">
            <a class="nav-link" href="sobre.php">Sobre nós</a>
          </li>
          <li class="nav-item <?php echo ($menu2)?>">
            <a class="nav-link" href="revenda.php">Seja uma revenda</a>
          </li>
          <li class="nav-item <?php echo ($menu3)?>">
            <a class="nav-link" href="funcionamento.php">Como funciona</a>
          </li>
          <li class="nav-item <?php echo ($menu4)?>">
            <a class="nav-link" href="precos.php">Preços</a>
          </li>
        </ul>

        <!-- Right -->
        <ul class="navbar-nav nav-flex-icons">
          <li class="nav-item">
            <a href="https://www.facebook.com/novaeratec" class="nav-link" target="_blank">
              <i class="fab fa-facebook-f"></i>
            </a>
          </li>
          <li class="nav-item">
            <a href="https://twitter.com/novaeratec" class="nav-link" target="_blank">
              <i class="fab fa-twitter"></i>
            </a>
          </li>
        </ul>

      </div>
      <li class="nav-item avatar dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-55" data-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false">
          <img id = "fotouser" src="../img/profile.jpg" class="rounded-circle z-depth-0"
            alt="avatar image" height="32"><span class="badge badge-danger ml-2">0</span>
        </a>
          <ul class="navbar-nav nav-flex-icons">
      <div class="dropdown-menu dropdown-menu-lg-left dropdown-secondary"
          aria-labelledby="navbarDropdownMenuLink-55">
          <a class="dropdown-item" href="#"><spam id="nomeusuario">Login ou Cadastre-se</spam></a>
			<div id="div-login">
		  <a class="dropdown-item" href="#" class="mx-2" role="button" onClick="loginwithFacebook()"><i class="fab fa-facebook-f light-blue-text"></i>&nbsp;com o Facebook</a>
			<a class="dropdown-item" href="#" class="mx-2" role="button" onClick="loginwithTwitter()"><i class="fab fa-twitter light-blue-text"></i>&nbsp;com o Twitter</a>
			<a class="dropdown-item" href="#" class="mx-2" role="button" onClick="loginwithGoogle()"><i class="fab fa-google light-blue-text"></i>&nbsp;com o Google</a>
				</div>
       		<a class="dropdown-item" href="#">Seu porta-cartões<span class="badge badge-danger ml-2">0</span></a>
          <a class="dropdown-item" onClick="logout()" href="#">Sair</a>
        </div>
      </li>
		</ul>
    </div>
  </nav>
