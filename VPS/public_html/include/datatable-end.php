<!--  <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>-->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>

  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="js/popper.min.js"></script>

<!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>


  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="js/mdb.min.js"></script>

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
			responsive: true
		});		
	});
	</script>
</body>
</html>