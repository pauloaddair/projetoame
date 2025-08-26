<?php
session_start();
include_once('include/conexao.php');
include_once('include/head.php');
?>
<body>
	<div class="container">
<div class="accordion" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        Sessão
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<?php 
		echo "<pre>";
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
		<?php 
		echo "<pre>";
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
		<?php 
		echo "<pre>";
		print_r($_SERVER);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
</div>
		<a href="/logout" class="btn btn-info rounded-pill d-block">Logout</a>
</div>
<?php
	$ativ = "server";
include_once('include/footer.php');
include_once('include/scripts.php');
include_once('include/end.php');
?>