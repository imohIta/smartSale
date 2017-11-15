<?php

$baseUri = $registry->get('config')->get('baseUri');
$session = $registry->get('session');
$session->write('hasTable', true);


  # check if user is logged in
  if(!$session->read('loggedIn')){
      $registry->get('uri')->redirect();
  }

  $thisUser = unserialize($session->read('thisUser'));

  if(!$session->read('slipStaffId')){
      $registry->get('uri')->redirect($baseUri .'/staff/generatePaySlip');
  }

  $staff = new Staff($session->read('slipStaffId'));
  $session->write('slipStaffId', null);

 ?>

 <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/css/bootstrap3-5.css">

 <style>
 body {
 	background: #f0f0f0;
 	width: 100vw;
 	height: 100vh;
 	display: flex;
 	justify-content: center;
     padding: 20px;
     height: 100%;
     font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;

 }

 @import url('https://fonts.googleapis.com/css?family=Roboto:200,300,400,600,700');

 * {
 	/*font-family: 'Roboto', sans-serif;*/
     font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
 	font-size: 12px;
 	color: #444;
 }

 #payslip {
 	width: calc( 8.5in - 80px );
 	height: calc( 11in - 60px );
 	background: #fff;
 	padding: 30px 40px;
 }

 #title {
 	margin-bottom: 20px;
 	font-size: 38px;
 	font-weight: 400;
 }

 #title .mini-title{
     font-size:23px;
     font-weight: 200;
 }

 #scope {
 	border-top: 1px solid #ccc;
 	padding-top: 20px;
     padding-bottom: 10px;
 	/*display: flex;*/
 	/*justify-content: space-around;*/
 }

 #scope > .scope-entry {
 	text-align: left;
 }

 .pull-left {
     float:left;
 }
 .pull-right{
     float:right;
 }

 #scope > .scope-entry > .value {
 	font-size: 16px;
 	font-weight: 200;
 }

 #scope > .scope-entry > .value > .bold {
 	font-size: 18px;
 	font-weight: 400;
 }

 .content {
 	/*display: flex;*/
 	border-bottom: 1px solid #ccc;
 	height: 880px;
 }

 .container-panel-md{
   margin-left: auto;
   margin-right: auto;
   max-width: 100%;
   line-height: 0.6em;
 }

 .signatures{
     margin-top:70px;
     width:100%;
     margin-bottom:40px;
 }

 .sign{
     width:40%;
 }

 #printBtn{
     margin-botto:20px;
 }

 </style>

 <div id="payslip">
 	<div id="title">
         Prelizz Perfumery<br/>
         <span class="mini-title">Pay Slip</span>
     </div>
 	<div id="scope">

         <div class="scope-entry">
 			<div class="value"><span class="bold">Employee's Name:</span> <?php echo ucwords($staff->get('name')); ?></div>
 		</div>

         <div class="scope-entry">
 			<div class="value"><span class="bold">Department:</span> <?php echo $staff->get('role'); ?></div>
 		</div>

         <div class="scope-entry">
 			<div class="value"><span class="bold">Month:</span> <?php echo date('F') . ' ' .  date('Y'); ?></div>
 		</div>

 	</div>
 	<div class="content">

         <div class="container-panel-md">

           <p style='font-size:18px; margin-top:30px'>Entitlements</p>
           <table class="table">
               <tr style="font-weight: bold;">
                 <td width="85%">Description</td>
                 <td>Amount</td>
               </tr>
               <tr>
                 <td>Basic Salary</td>
                 <td class="td-num">=N= <?php echo number_format($staff->get('salary')); ?></td>
               </tr>
           </table>

           <p style='font-size:18px; margin-top:30px'>Deductions</p>
           <table class="table">
               <tr style="font-weight: bold;">
                 <td width="85%">Description</td>
                 <td>Amount</td>
               </tr>
               <tr>
                 <td>VAT ( 5% )</td>
                 <td class="td-num">=N= <?php $vat = (5/100) * $staff->get('salary'); echo number_format($vat); ?></td>
               </tr>
           </table>

          <p style='font-size:18px; margin-top:30px'>Subcharges</p>
             <table class="table">
                 <tr style="font-weight: bold;">
                   <td>Date</td>
                   <td width="68%" align="center">Reason</td>
                   <td>Amount</td>
                 </tr>
                 <?php
                    $totalSubcharges = 0;
                    foreach ($staff->getSubcharges(date('m'), date('Y')) as $value) {
                        $totalSubcharges += $value->amount;
                  ?>
                      <tr>
                       <td><?php echo $value->date; ?></td>
                       <td align="center"><?php echo $value->reason; ?></td>
                       <td class="td-num">=N= <?php echo number_format($value->amount); ?></td>
                     </tr>
                 <?php } ?>

                <tr>
                 <td colspan="2"><strong>Total</strong></td>
                 <td class="td-num">=N= <?php echo number_format($totalSubcharges); ?></td>
               </tr>
               </table>



               <div class="input-group">
                 <span class="input-group-addon"><i class="fa fa-angle-double-right"></i><strong>Total Amount Due</strong></span>
                     <span class="form-control" style="text-align:right; padding-right:30px; font-size:16px">=N= <?php echo number_format($staff->get('salary') - ($totalSubcharges + $vat)); ?></span>
              </div>

              <div class="signatures">

                  <div class="sign pull-left">
                      <p>Signature<p>
                      <p style="margin-top:70px; width:250px; border-bottom:1px solid #ccc"></p>
                      <p>Okon Edet</p>
                  </div>


                  <div class="sign pull-right">
                      <p>Signature<p>
                      <p style="margin-top:70px; width:250px; border-bottom:1px solid #ccc"></p>
                      <p>Accountant</p>
                  </div>

                  <br style="clear:both" />

              </div>


         </div>

         <button class="btn btn-warning" id="printBtn" onclick="PrintSlip()">Print</button>

         <a href="<?php echo $baseUri; ?>/staff/generatePaySlip" class="pull-right"><button class="btn btn-info"><< Back</button></a>

         <br /><br />


 	</div>
 </div>

 <script>

 function PrintSlip(){
     document.getElementById('printBtn').style.display = 'none';
     window.print();
     document.getElementById('printBtn').style.display = 'block';
 }

 </script>
