<?php
if (isset($_SESSION['nivel']) && ($_SESSION['nivel']=='5')){
  ?>
<div class="accordion" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        Sessão
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<?php echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";			  
		?>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingThree">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
        Cookies
      </button>
    </h2>
    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<?php echo "<pre>";
		print_r($_COOKIE);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
        Servidor
      </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<?php echo "<pre>";
		print_r($_SERVER);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
<?php if (($_SERVER["REQUEST_METHOD"]=="POST")){
?>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingFour">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
        POST
      </button>
    </h2>
    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<?php echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
<?php }
  ?>
<?php if ($_SERVER["REQUEST_METHOD"]=="GET"){
?>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingFive">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
        GET
      </button>
    </h2>
    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<?php echo "<pre>";
		print_r($_GET);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
<?php }
?>
</div>

  <?php }
?>
<!--Footer-->
  <footer class="page-footer text-center font-small wow fadeIn fridahh-bg">
<!--    <hr class="my-4">-->
    <!--Copyright-->
    <div class="footer-copyright py-3">
		site desenvolvido por <a href="https://novaeratec.com.br" target="_blank">Nova Era Tecnologia</a> | © 2016-2025 Copyright:
		<a href="https://projetoame.org" target="_blank"> Associação Brasileira de Inclusão Através do Trabalho - A.M.E. </a>
    </div>
    <!--/.Copyright-->
  </footer>
  <!--/.Footer-->

