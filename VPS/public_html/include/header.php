  <nav class="navbar fixed-top navbar-expand-lg navbar-dark  blue-gradient scrolling-navbar">

    <div class="container">

      <!-- Brand -->
      <a class="navbar-brand" href="sobre.html" target="_blank">
		 <img src="img/android-icon-36x36.png" height="48">Projeto <strong>A.M.E.</strong>
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
            <a class="nav-link" href="#">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="" target="_blank">Sobre nós</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="" target="_blank">Nossos serviços</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="" target="_blank">Contato</a>
          </li>
        </ul>

		  <!-- Right -->
        <ul class="navbar-nav nav-flex-icons">
          <li class="nav-item">
			  <a href="https://www.facebook.com/projetoatendentesmuitoespeciais" target="_blank">
				<i class="fab fa-facebook-f text-white mr-3"></i>
			  </a>
          </li>
          <li class="nav-item">
			  <a href="https://twitter.com/projetoameorg" target="_blank">
				<i class="fab fa-twitter text-white mr-3"></i>
			  </a>
          </li>
          <li class="nav-item">
			  <a href="https://www.youtube.com/channel/UCqTfVItS3lDOJhCeQVPkB3w" target="_blank">
				<i class="fab fa-youtube text-white mr-3"></i>
			  </a>
         </li>
          <li class="nav-item">
            <a href="sobre.html" class="nav-link border border-light rounded"
              target="_blank">
				<?php $profile = "img/profile.jpg";
				If (isset($_SESSION['profile'])){
					$profile = $_SESSION['profile'];
				}
				$nome = "Visitante";
				If ($_SESSION['nome']<>""){
					$nome = $_SESSION['nome'];
				}
				?>
              <img src="<?php echo $profile?>" height="24" class="rounded-circle mr-1"><?php echo $nome?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Navbar -->
