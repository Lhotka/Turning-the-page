      	
        <hr>

        <footer>

        </footer>
        <div class="copyright text-center bg-dark text-white py-2">
         <p>&copy; <?php echo date('Y'); ?> All Rights Reserved &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Designed by Filip Lhotka</a></p>
        </div>
 <!-- /container -->


    <!-- Dynamic Bootstrap core JavaScript -->
    <script type="text/javascript" src="<?php echo $baseUrl; ?>/bootstrap/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo $baseUrl; ?>/bootstrap/js/bootstrap.min.js"></script>
    <!-- previous
    <script type="text/javascript" src="./bootstrap/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
    -->
    
  </body>
</html>

<?php
    // Close the connection if it's open
    if (isset($conn)) {
        mysqli_close($conn);
    }
?>