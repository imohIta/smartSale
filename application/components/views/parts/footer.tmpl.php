<?php
    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
?>

<!-- Vendor JavaScripts -->
<?php echo $js; ?>


<?php

    if($session->read('hasTable')){

 ?>

     <!--  Page Specific Scripts for table pages -->
     <script type="text/javascript">
     	$(window).load(function(){
     		$('.footable').footable();
     	});
     </script>
     <!--/ Page Specific Scripts -->

 <?php } 

$session->write('hasTable', null);

?>

</body>

</html>
