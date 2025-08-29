<!--Footer-->
 	<footer class="page-footer text-center font-small primary-color-dark darken-2 mt-4 wow fadeIn">

    <!--Call to action-->
<!--
    <div class="pt-4">
      <a class="btn btn-outline-white" href="/sobre" target="_blank" role="button">SAIBA+
        <i class="fas fa-graduation-cap ml-2"></i>
      </a>
    </div>
-->
	
	<!--/.Call to action-->

<!--    <hr class="my-4">-->

    <!-- Social icons -->
<!--
    <div class="pb-4">
      <a href="https://www.facebook.com/projetoatendentesmuitoespeciais" target="_blank">
        <i class="fab fa-facebook-f mr-3"></i>
      </a>

      <a href="https://twitter.com/projetoameorg" target="_blank">
        <i class="fab fa-twitter mr-3"></i>
      </a>

      <a href="https://www.instagram.com/atendentesmuitoespeciais/" target="_blank">
        <i class="fab fa-instagram mr-3"></i>
      </a>

    </div>
-->
    <!-- Social icons -->

    <!--Copyright-->
    <div class="footer-copyright py-3">
		site desenvolvido por <a href="https://novaeratec.com.br" target="_blank">Nova Era Tecnologia</a> | © 2016-2025 Copyright:
		<a href="https://projetoame.org" target="_blank"> Associação Brasileira de Inclusão Através do Trabalho - A.M.E. </a>
    </div>
    <!--/.Copyright-->
  </footer>
  <!--/.Footer-->
  <!-- SCRIPTS -->

  <!-- JQuery -->
  <script src="https://kit.fontawesome.com/d067a28273.js" crossorigin="18592B3C-6385-48D7-8605-5E63E600000B"></script>
  <!-- JQuery -->
  <script type="text/javascript" src="/js/jquery-3.4.1.min.js"></script>
<!--  <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>-->
<!--<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>-->

  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="<?php echo $GLOBALS['app_web_root']?>js/popper.min.js"></script>

<!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="<?php echo $GLOBALS['app_web_root']?>js/bootstrap.min.js"></script>


  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="<?php echo $GLOBALS['app_web_root']?>js/mdb.min.js"></script>

  <!-- DataTable JavaScript -->
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css"></script>
  <!-- Initializations -->
  <script type="text/javascript">

    // Animations initialization
    new WOW().init();
  </script>

  <!-- Time Counter -->
  <script type="text/javascript">

/*
*/
	$(document).ready(function () {
//		$('#example').DataTable();
		var table = new DataTable('#table', {
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
			},
			colReorder: true,
		});		
	});
	</script>

