<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', true);


      # check if user is logged in
      if(!$session->read('loggedIn')){
          $registry->get('uri')->redirect();
      }

    $thisUser = unserialize($session->read('thisUser'));


    $b_day = '01'; $e_day = (date('m') == '02') ? '28' : '31';
    $b_month = date('m'); $e_month = date('m');
    $b_year = date('Y'); $e_year = date('Y');

    $sortSale = false;
    if($session->read('salesReportBeginDate')){

        $beginDate = $session->read('salesReportBeginDate');
        $endDate = $session->read('salesReportEndDate');



        $b_day = $session->read('salesReportDate-b-day');
        $b_month = $session->read('salesReportDate-b-month');
        $b_year = $session->read('salesReportDate-b-year');

        $e_day = $session->read('salesReportDate-e-day');
        $e_month = $session->read('salesReportDate-e-month');
        $e_year = $session->read('salesReportDate-e-year');


        $sortSale = true;

        $session->write('salesReportBeginDate', null);
        $session->write('salesReportEndDate', null);
        $session->write('salesReportDate-b-day', null);
        $session->write('salesReportDate-b-month', null);
        $session->write('salesReportDate-b-year', null);
        $session->write('salesReportDate-e-day', null);
        $session->write('salesReportDate-e-month', null);
        $session->write('salesReportDate-e-year', null);



        $transactions = Sales::fetchSortedTransactions(array(
            'beginDate' => $beginDate,
            'endDate' => $endDate
        ));

    }


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/footable/css/footable.core.min.css',
        'css/main.css'
    )));

    #include Sidebar
    $registry->get('includer')->render('sidebar', array());



?>



<!--  CONTENT  -->
<section id="content">
    <div class="page page-tables-footable">
        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Sales Report</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">


            <div class="col-md-4">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">




                            <?php
                                if($registry->get('session')->read('formMsg')){
                                    echo $registry->get('session')->read('formMsg');
                                    $registry->get('session')->write('formMsg', NULL);
                                }
                            ?>




                        <div class="boxs-body">

                            <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Set Sort Parameter</strong></h3>


                            <form class="form-inline" role="form" action="<?php echo $baseUri; ?>/report/sales" method="post">


                                <p><strong>Begin Date<strong></p>
                                <div class="form-group">

                                    <select class="form-control mb-10" name="b-day" style="width:80px" required>

                                        <?php
                                            for($i = 1; $i <= 31; $i++){
                                                $value = $i < 10 ? '0' . $i : $i;
                                                $selected = ($b_day == (int)$value) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="form-group" style="margin-left:10px">
                                    <select class="form-control mb-10" name="b-month" style="width:80px" required>

                                        <?php
                                            for($i = 1; $i <= 12; $i++){
                                            $value = $i < 10 ? '0' . $i : $i;
                                            $selected = ($b_month == $value) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="form-group" style="margin-left:10px">
                                    <select class="form-control mb-10" name="b-year" style="width:100px"
                                                data-parsley-trigger="change"
                                                required>
                                                <?php
                                                    for($i = date('Y'); $i <= date('Y') + 5; $i++){
                                                    $value = $i < 10 ? '0' . $i : $i;
                                                    $selected = ($b_year == $value) ? 'selected' : '';
                                                ?>

                                                   <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                 <?php } ?>
                                    </select>
                                </div>

                                <br /><br />

                                <p><strong>End Date</strong></p>
                                <div class="form-group">

                                    <select class="form-control mb-10" name="e-day" style="width:80px" required>

                                        <?php
                                            for($i = 1; $i <= 31; $i++){
                                                $value = $i < 10 ? '0' . $i : $i;
                                                $selected = ($e_day == (int)$value) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="form-group" style="margin-left:10px">
                                    <select class="form-control mb-10" name="e-month" style="width:80px" required>

                                        <?php
                                            for($i = 1; $i <= 12; $i++){
                                            $value = $i < 10 ? '0' . $i : $i;
                                            $selected = ($e_month == $value) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                        <?php } ?>

                                    </select>
                                </div>

                                <div class="form-group" style="margin-left:10px">
                                    <select class="form-control mb-10" name="e-year" style="width:100px" required>
                                                <?php
                                                    for($i = date('Y'); $i <= date('Y') + 5; $i++){
                                                    $value = $i < 10 ? '0' . $i : $i;
                                                    $selected = ($e_year == $value) ? 'selected' : '';
                                                ?>

                                                   <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                 <?php } ?>
                                    </select>
                                </div>

                                <br />

                                <button type="submit" name="submit" class="btn btn-raised btn-primary" style=" padding:10px">Sort</button>
                            </form>


                        </div>
                    </div>

                </section>

            </div>

        <!-- </div> -->
        <?php if($sortSale){ ?>

            <div class="col-md-8">

                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">

                        <div class="boxs-body">
                                <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Sales Details</strong></h3>

                                <div class="form-group pull-right">
                                    <label for="filter" style="padding-top: 5px; font-size:14px"><strong>Search Table:</strong></label>
                                    <input id="filter"  style="font-size:14px; padding-left:7px" type="text" class="form-control rounded input-sm w-md mb-10 inline-block"/>
                                </div>
                                <table id="searchTextResults" data-filter="#filter" data-page-size="40" class="footable table table-custom">
                                    <thead>
                                        <tr>
                                            <th>Bar Code</th>
                                            <th width="40%">Details</th>
                                            <th>Qty</th>
                                            <th>Amount</th>
                                            <th>Discount</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                            $grandTotal = 0;
                                            foreach($transactions as $transaction){

                                                $grandTotal += $transaction->grandTotal;

                                               # fetch sale detail for this transactions
                                               $sales = Sales::fetchByTransId($transaction->transId);

                                               # get seller
                                               $seller = new AppUser($transaction->userId);


                                        ?>
                                                <tr style="background:#F9F9F9; margin-top:10px">
                                                    <td colspan="6">
                                                        <p style="color:#E56B6B">
                                                            <span>Invioce No : <?php echo $transaction->transId; ?></span>
                                                            <span style="margin-left:10px"><?php echo strtoupper($seller->get('name')); ?></span>
                                                            <span style="
                                                            margin-left:10px"><?php echo changeDateFormat($transaction->date); ?>  <?php //echo timeToString($transaction->time); ?></span>
                                                    </td>
                                                </tr>

                                                <?php
                                                    $totalAmt = $totalQty = 0;
                                                    foreach (@$sales as $value) {
                                                        # code...
                                                        $sale = new Sales($value);
                                                        $totalAmt += ($sale->get('qty') * $sale->get('price')) - $sale->get('discount');
                                                        $totalQty += $sale->get('qty');

                                                 ?>

                                                 <tr style="background:#fff">
                                                     <td><?php echo $sale->get('item')->get('codeNo'); ?></td>
                                                     <td><?php echo $sale->get('item')->get('name'); ?></td>
                                                     <td><?php echo number_format($sale->get('qty')); ?></td>
                                                     <td><?php echo number_format($sale->get('price')); ?></td>
                                                     <td><?php echo number_format($sale->get('discount')); ?></td>
                                                     <td><?php echo number_format(($sale->get('qty') * $sale->get('price')) - $sale->get('discount')); ?></td>
                                                 </tr>


                                        <?php } ?>

                                            <tr style="background:#fff">
                                                <td colspan="2"><strong>Total</strong></td>
                                                <td><strong><?php echo number_format($totalQty); ?></strong></td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td><strong><?php echo number_format($totalAmt); ?></strong></td>
                                            </tr>

                                        <?php } ?>

                                        <tr>
                                            <td colspan="5"><h2><strong>Grand Total : </strong></h2></td>
                                            <td><h2><strong><?php echo number_format($grandTotal); ?></h2></td>
                                        </tr>



                                    </tbody>

                                    <tfoot class="hide-if-no-paging">
                                        <tr>

                                            <td colspan="6" class="text-right"><ul class="pagination">
                                                </ul></td>
                                        </tr>
                                    </tfoot>
                                </table>
                        </div>
                    </div>
                </section>

            </div>

        <?php } ?>


        </div>
        <!-- End Row -->



    </div>
</section>
<!--/ CONTENT -->





<?php
    $registry->get('includer')->render('footer', array('js' => array(
                                'bundles/libscripts.bundle.js',
                                'bundles/vendorscripts.bundle.js',
                                'js/vendor/footable/footable.all.min.js',
                                'js/main.js'
                            )));

?>
