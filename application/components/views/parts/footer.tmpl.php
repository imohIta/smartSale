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

<style>
    .txt1{
        font-family: roboto, "helvetica hue";
        font-size:24px;
        color:#666;
    }

</style>

<div id="cover" style="display:none; width:100%; height:100%; background:#fff; z-index:20001; opacity:0.95; position:absolute; top:0; left:0">

    <div style="width:360px; height:160px; margin:auto; margin-top:15%; text-align:center">
        <img src="<?php echo $baseUri; ?>/assets/images/831.gif" style="width:150px; height:150px" />
        <p class="txt1">Sync in progress<p>
        <p class="txt1">Please Wait</p>
    </div>

</div>


</body>

</html>
