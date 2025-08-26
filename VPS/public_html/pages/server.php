<?php
session_start();
include_once('include/conexao.php');
include_once('include/head3.php');
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
		<? 
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
		<? 
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
		<? 
		echo "<pre>";
		print_r($_SERVER);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
<?
if (($_SERVER["REQUEST_METHOD"]=="POST")){
?>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingFour">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
        POST
      </button>
    </h2>
    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<? 
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
<?
  }
  ?>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingFive">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
        GET
      </button>
    </h2>
    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<? 
		echo "<pre>";
		print_r($_GET);
		echo "</pre>";
		?>
      </div>
    </div>
  </div>
</div>
		<a href="/logout" class="btn btn-info rounded-pill d-block">Logout</a>
</div>
<?
$ativ = "server";
include_once('include/footer3.php');
include_once('include/scripts3.php');
include_once('include/end3.php');
?>