<?php
session_start();
$ref="";
if (isset($_SERVER['HTTP_REFERER'])){
	$ref = $_SERVER['HTTP_REFERER'];	
}
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
echo $ref;
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
*/
// $ref = "/";
// exit;
$titulo = "Login";
include_once('include/head.php');
//	echo $titulo.'<br>-pages/'.$parametros[0].'.php'.'<br>'. $url . "<br>". $ref;
//	exit;
	if(isset($_COOKIE['usuario_id'])){
		$usuario_id = $_COOKIE['usuario_id'];
		$query = "select usuario_ID, login, nome, nivel, imagens.url 
		from usuarios 
		LEFT JOIN imagens
		ON usuarios.imagem_id = imagens.imagem_id
		WHERE usuario_id =".$usuario_id;
		$result = mysqli_query($conexao, $query);
		$row1 = mysqli_num_rows($result);
		setcookie("usuario_id", $row1["usuario_ID"], time()+14*24*60*60);
		$_SESSION['id'] = $row1["usuario_ID"];
		$_SESSION['usuario'] =  $row1["login"];
		$_SESSION['nome'] = $row1["nome"];
		$_SESSION['nivel'] = $row1["nivel"];
		$_SESSION['perfil'] = $row1["url"];
		header("Location: $ref");
		exit();
	}

?>
<body>
    <div class="view full-page-intro" style="background-image: url('img/ame2024.jpg'); background-repeat: no-repeat; background-size: cover;"></div>
    <div class="contents order-2 order-md-1">
      <div class="container">
<!--          <? echo $url ."<br>"?>-->
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
            <div class="mb-4">
              <h3>Identifique-se</h3>
              <p class="mb-4">Você precisa se identificar para entrar nesta área do sistema.</p>
            </div>
			<? 
			
			if (isset($_SESSION['nao_autenticado'])){
				if ($_SESSION['nao_autenticado']==true){
			
			?>
			  <p class="bg-warning rounded-pill text-white p-2 text-center"><strong>Atenção:</strong>usuário ou senha não conferem. Tente novamente</p>
			  <?
					}
				}
//              echo $ref . "<br>";
			  ?>
            <form action="include/valida.php" method="post">
			<input type="hidden" name="ref" id = "ref" value="<? echo $ref?>">
             <div class="form-group first">
<!--
                <label for="nome">nome</label>
                <input type="text" class="form-control" name="nome" id="nome">
-->
			<div class="md-form">
				<i class="fas fa-user prefix grey-text"></i>
				<input type="text" id="nome" name="nome" class="form-control" value="" autofocus>
				<label for="nome">Usuário</label>
			</div>

              </div>
              <div class="form-group last mb-3">
<!--
                <label for="password">senha</label>
                <input type="password" class="form-control" name="senha" id="senha">
-->
			<div class="md-form">
				<i class="fas fa-lock prefix grey-text"></i>
				<input type="password" id="senha" name="senha" class="form-control" value="">
				<label for="senha">Senha</label>
			</div>
                
              </div>
              
              <div class="d-flex mb-5 align-items-center">
				<div class="custom-control custom-switch">
					<input type="checkbox" class="custom-control-input" name="lembrar" id="lembrar" checked>
					<label class="custom-control-label" for="lembrar">Lembrar</label>
				</div>
<!--
                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                  <input type="checkbox" checked="checked"/>
                  <div class="control__indicator"></div>
                </label>
-->
                <span class="ml-auto"><a href="#" class="forgot-pass">Esqueceu sua senha?</a></span> 
              </div>

              <input type="submit" value="Log In" class="btn btn-block btn-primary rounded-pill">

              <span class="d-block text-center my-4 text-muted">&mdash; ou &mdash;</span>
              <div class="social-login">
				  <a href="/cadastro" class="d-flex justify-content-center align-items-center rounded-pill text-primary">Cadastre-se</a>
			  </div>
				<!--              
				  <div class="social-login">
					<a href="#" class="facebook btn d-flex justify-content-center align-items-center rounded-pill">
					  <span class="icon-facebook mr-3"></span> Login with Facebook
					</a>
					<a href="#" class="twitter btn d-flex justify-content-center align-items-center rounded-pill">
					  <span class="icon-twitter mr-3"></span> Login with  Twitter
					</a>
					<a href="#" class="google btn d-flex justify-content-center align-items-center rounded-pill">
					  <span class="icon-google mr-3"></span> Login with  Google
					</a>
				  </div>
				-->
            </form>
          </div>
        </div>
      </div>
    </div>    
    <script src="/js/jquery-3.4.1.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/main.js"></script>
  </body>
</html>