<?php
session_start();
$ref="";
if (isset($_SERVER['HTTP_REFERER'])){
	$ref = $_SERVER['HTTP_REFERER'];	
}
$titulo = "Login";
include_once('include/head.php');
//	echo $titulo.'<br>-pages/'.$parametros[0].'.php'.'<br>'. $url . "<br>". $ref;
//	exit;
?>
<body>
    <div class="view full-page-intro" style="background-image: url('img/ame2024.jpg'); background-repeat: no-repeat; background-size: cover;"></div>
    <script src="/js/jquery-3.4.1.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/main.js"></script>
  </body>
</html>