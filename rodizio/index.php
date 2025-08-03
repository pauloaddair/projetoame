<?PHP
	session_start();
	setlocale (LC_ALL, 'pt_BR.utf-8');
    date_default_timezone_set('America/Sao_Paulo');
	include_once("../include/head.php");
	$nome = "";
	$mensagem = "";
	$logado = "none";
	$login = "visible";
	$foto = "/img/ms-icon-310x310.png";
	$perfil = "Visitante";
	if (isset($_SESSION['nome'])){
		$nome = $_SESSION['nome'];
		$logado = "visible";
		$login = "none";
	}
	if (isset($_SESSION['perfil'])){
		$foto = $_SESSION['perfil'];
	}
?>
<!DOCTYPE html>
<html lang="pt-br">
<?PHP
//include_once("./include/header.php");
?>
<body>
<div class="container p-1">
	<header class="mt-5 p-2 justify-content-md-center">
		<!--<img src="img/logo-brown.png" class="img-thumbnail">-->
<h1 class="mt-5 text-center">ATENDENTES MUITO ESPECIAIS</h1>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="/atendentes">Próximos eventos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Rodízio</li>
  </ol>
</nav>
	</header>
<main class="container">
<form class="border border-light p-2" method="post">
	<div class="row">
		<div class="col-12">
			<div class="card mb-2 p-1">
				<div class="card-header"><h1>SITUAÇÃO DO RODÍZIO</h1>
		<?php if($mensagem<>""){
			echo "<span class='alert'><p>". $mensagem . "</p></span>";
			}
		?>
				</div>
				<div class="card-body">
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Atendentes</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Treinamento</button>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
		<table class="table table-hover">
			<thead class="blue-gradient text-white">
			<tr><td>pos.#</td><td>Atendente</td></tr>
			</thead>
				<tr>
				<td>01</td><td>Nathalia Moraes
				</td>
				</tr>
				<tr>
				<td>02</td><td>Rafael Campos Guidini
				</td>
				</tr>
				<tr>
				<td>03</td><td>Renato Campos Bistafa
				</td>
				</tr>
				<tr>
				<td>04</td><td>Caique Vinieri
				</td>
				</tr>
				<tr>
				<td>05</td><td>Paulo de Souza Campos Neto
				</td>
				</tr>
				<tr>
				<td>06</td><td>Giovanna Miguel Guzzo
				</td>
				</tr>
				<tr>
				<td>07</td><td>Larissa Sthefanie Pelarin
				</td>
				</tr>
				<tr>
				<td>08</td><td>Natalia Evangelista
				</td>
				</tr>
				<tr>
				<td>09</td><td>Anna Lisa de Almeida Monte
				</td>
				</tr>
				<tr>
				<td>10</td><td>Juliana Bessa Adorno
				</td>
				</tr>
				<tr>
				<td>11</td><td>Beatriz Borborema Marques
				</td>
				</tr>
				<tr>
				<td>12</td><td>Marcella Provenzano Daniel
				</td>
				</tr>
				<tr>
				<td>13</td><td>Alessandra Mendonça Chiocchetti
				</td>
				</tr>
				<tr>
				<td>14</td><td>Andre Carollo Hernandes
				</td>
				</tr>
				<tr>
				<td>15</td><td>Gustavo Henrique
				</td>
				</tr>
				<tr>
				<td>16</td><td>Fabio Vieira Jardim Grandizolli
				</td>
				</tr>
				<tr>
				<td>17</td><td>Caio Henrique Teixeira Borba
				</td>
				</tr>
				<tr>
				<td>18</td><td>Isabelle Maia Silva
				</td>
				</tr>
				<tr>
				<td>19</td><td>Isadora Brigo Santos
				</td>
				</tr>
				<tr>
				<td>20</td><td>Charlize Silva Prieto de Souza
				</td>
				</tr>
				<tr>
				<td>21</td><td>Giovana Monden
				</td>
				</tr>
				<tr>
				<td>22</td><td>Luiz Renato
				</td>
				</tr>
				<tr>
				<td>23</td><td>Guilherme Bricks
				</td>
				</tr>
				<tr>
				<td>24</td><td>Rafaela Fiori Ramos
				</td>
				</tr>
				<tr>
				<td>25</td><td>Nicolas Ramos Valente
				</td>
				</tr>
				<tr>
				<td>26</td><td>Vinicius Gonzales
				</td>
				</tr>
				<tr>
				<td>27</td><td>Henrique Blankenburg Cipriano Martins da Silva
				</td>
				</tr>
				<tr>
				<td>28</td><td>Bianca Santos Silva
				</td>
				</tr>
				<tr>
				<td>29</td><td>Cibele Andaluz e Dias
				</td>
				</tr>
				<tr>
				<td>30</td><td>Thiago Silva e Sousa
				</td>
				</tr>
				<tr>
				<td>31</td><td>Isabelly Karoline de Souza Moreira
				</td>
				</tr>
				<tr>
				<td>32</td><td>Senielly Costa Silva
				</td>
				</tr>
				<tr>
				<td>33</td><td>Carolina Kanashiro Silva
				</td>
				</tr>
		</table>
	</div>
	<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
		<table class="table table-hover">
			<thead class="blue-gradient text-white">
			<tr><td>pos.#</td><td>Treinandos</td></tr>
			</thead>
				<tr>
				<td>34</td><td>Luiz Guilherme (Guiga)
				</td>
				</tr>
				<tr>
				<td>35</td><td>Nicole Miranda Listori
				</td>
				</tr>
				<tr>
				<td>36</td><td>Giovanna Alves de Campos
				</td>
				</tr>
				<tr>
				<td>37</td><td>Diego de Sampaio Carvalho
				</td>
				</tr>
				<tr>
				<td>38</td><td>Mayra Parreira Lee Citti
				</td>
				</tr>
				<tr>
				<td>39</td><td>Michael Douglas Ferreira
				</td>
				</tr>
				<tr>
				<td>40</td><td>Renato Garcia Marino
				</td>
				</tr>
				<tr>
				<td>41</td><td>Giovana de Lucia Bartolomei
				</td>
				</tr>
				<tr>
				<td>42</td><td>Maria Cláudia Sampaio Guimarães
				</td>
				</tr>
				<tr>
				<td>43</td><td>João Vitor Ribeiro Neves
				</td>
				</tr>
				<tr>
				<td>44</td><td>Tiago Cesar Pim
				</td>
				</tr>
		</table>
	</div>
</div>
					

<!--				<small>marque sua disponibilidade abaixo e forneça seus dados</small></div>-->
		</div>
		</div>
		</div>
      <hr>
		</div>
	</form>
	</main>
	</div>
 <!-- JQuery -->
  <script type="text/javascript" src="./js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="./js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="./js/mdb.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="./js/popper.min.js"></script>
  <!-- SCRIPTS -->
  <script src="https://kit.fontawesome.com/d067a28273.js" crossorigin="18592B3C-6385-48D7-8605-5E63E600000B"></script>
    <!-- Adicionando Javascript -->
    <script>

        $(document).ready(function() {

            function limpa_formulário_cep() {
                // Limpa valores do formulário de cep.
                $("#endereco").val("");
//                $("#bairro").val("");
                $("#complemento").val("");
                $("#cidade").val("");
                $("#uf").val("");
//                $("#ibge").val("");
            }
            
			//Quando o campo cep perde o foco.
            $("#cep").blur(function() {

                //Nova variável "cep" somente com dígitos.
                var cep = $(this).val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = /^[0-9]{8}$/;

                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {

                        //Preenche os campos com "..." enquanto consulta webservice.
                        $("#endereco").val("...");
//                        $("#bairro").val("...");
                        $("#cidade").val("...");
                        $("#uf").val("...");
//                        $("#ibge").val("...");

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                $("#endereco").val(dados.logradouro);
//                                $("#bairro").val(dados.bairro);
                                $("#cidade").val(dados.localidade);
                                $("#uf").val(dados.uf);
                                $("#complemento").focus();
//                                $("#ibge").val(dados.ibge);
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                limpa_formulário_cep();
                                alert("CEP não encontrado.");
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
                        limpa_formulário_cep();
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    //cep sem valor, limpa formulário.
                    limpa_formulário_cep();
                }
            });
			
		});
		
    </script>
</body>

</html>