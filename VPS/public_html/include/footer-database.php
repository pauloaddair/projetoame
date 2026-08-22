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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Bootstrap tooltips (Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

  <!-- Bootstrap core JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

  <!-- MDB core JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.20.0/js/mdb.min.js"></script>

  <!-- DataTable JavaScript -->
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

  <!-- Initializations -->
  <script type="text/javascript">
    if (typeof WOW !== 'undefined') {
      new WOW().init();
    }
  </script>

  <script type="text/javascript">
    $(document).ready(function () {
      if ($('#table').length && !$.fn.DataTable.isDataTable('#table')) {
        $('#table').DataTable({
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
          },
          colReorder: true,
          responsive: true
        });
      }
    });
  </script>

