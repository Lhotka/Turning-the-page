      	<hr>

      	<footer>
      	    <div class="copyright text-center bg-dark text-white py-2">
      	        <p>&copy; <?php echo date('Y'); ?> Vse pravice pridržane. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Izdelava strani - Filip Lhotka</a></p>
      	    </div>
      	</footer>

      	<script>
      	    function adjustMainMargin() {
      	        var navbarHeight = document.querySelector('.navbar-fixed-top').offsetHeight;
      	        document.getElementById('main').style.marginTop = navbarHeight + 'px';
      	    }

      	    window.addEventListener('load', adjustMainMargin);
      	    window.addEventListener('resize', adjustMainMargin);
      	</script>

      	<!-- Dynamic Bootstrap core JavaScript -->
      	<script src="<?php echo $baseUrl; ?>/bootstrap/js/jquery.min.js"></script>
      	<script type="text/javascript" src="<?php echo $baseUrl; ?>/bootstrap/js/jquery-2.1.4.min.js"></script>
      	<script type="text/javascript" src="<?php echo $baseUrl; ?>/bootstrap/js/bootstrap.min.js"></script>

      	</div>
      	</body>

      	</html>

      	<?php
            // Close the connection if it's open
            if (isset($conn)) {
                mysqli_close($conn);
            }
            ?>