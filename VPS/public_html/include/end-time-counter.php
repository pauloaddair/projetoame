  <!-- SCRIPTS -->

  <!-- JQuery -->
  <script type="text/javascript" src="/js/jquery-3.4.1.min.js"></script>

  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="../pages/js/popper.min.js"></script>

  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="/js/bootstrap.min.js"></script>

  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="/js/mdb.min.js"></script>

  <!-- Initializations -->
  <script type="text/javascript">

    // Animations initialization
    new WOW().init();
  </script>

  <!-- Time Counter -->
  <script type="text/javascript">

    // Set the date we're counting down to
    var countDownDate = new Date();

    // countDownDate.setDate(countDownDate.getDate() + 19);
    var countDownDate = new Date("Aug 14, 2023 13:00").getTime();

    // Update the count down every 1 second
    var x = setInterval(function () {

      // Get todays date and time
      var now = new Date().getTime();

      // Find the distance between now an the count down date
      var distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Display the result in the element with id="demo"
      document.getElementById("time-counter").innerHTML = days + "d " + hours + "h " +
        minutes + "m ";


      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("time-counter").innerHTML = "EXPIRED";
      }
    }, 1000);
  </script>
</body>
</html>