<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', true);


  # check if user is logged in
  if(!$session->read('loggedIn')){
      $registry->get('uri')->redirect();
  }

    $thisUser = unserialize($session->read('thisUser'));

    # check user privilege
    if(!in_array($thisUser->get('activeAcct'), array(2,3))){
        $registry->get('uri')->redirect();
    }


    $month = date('m');
    $year = date('Y');

    if($session->read('PL-month')){
        $month = $session->read('PL-month');
        $year = $session->read('PL-year');

        $session->write('PL-month', null);
        $session->write('PL-year', null);
    }

    # fetch total sales for selected month
    $totalSales = Sales::getTotalForMonth($month, $year);

    # fetch total cost of goods for this month
    $totalCostOfGoods = StockItem::fetchTotalCostOfGoods($month, $year);

    # fetch total expeses
    $totalExpenses = Expenses::fetchAllForMonth($month, $year);

    /* fetch totatl salaries to be paid this month */

    # fetch total salaries
    $totalSalaries = Staff::fetchTotalSalaries();

    # fetch total staff subcharges for this month
    $totalSubcharges = Staff::fetchTotalSubcharges($month, $year);

    # calculate total salaries that is due to be paid dis month
    $totalSalariesDue = $totalSalaries - $totalSubcharges;

    
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
                    <?php
                        $dateObj = DateTime::createFromFormat('m', $month);
                     ?>
                    <h1 class="font-thin h3 m-0">Profit and Loss ( <?php echo $dateObj->format('F') . ' ' . date('Y'); ?> )</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-10">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">

                        <div class="boxs-body">

                            <h1 class="custom-font"><stong>Select Date</strong></h1>
                            <form class="form-inline" role="form" action="<?php echo $baseUri; ?>/accounting/profitNLoss" method="post">


                                <div class="form-group">
                                    <select class="form-control mb-10" name="month" style="width:100px" required>

                                                <?php
                                                    for($i = 1; $i <= 12; $i++){
                                                    $value = $i < 10 ? '0' . $i : $i;
                                                    $selected = ($month == $value) ? 'selected' : '';
                                                ?>
                                                    <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group" style="margin-left:15px">
                                    <select class="form-control mb-10" name="year" style="width:100px" required>
                                                <?php
                                                    for($i = date('Y'); $i <= date('Y') + 5; $i++){
                                                    $value = $i < 10 ? '0' . $i : $i;
                                                    $selected = ($year == $value) ? 'selected' : '';
                                                ?>

                                                   <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                 <?php } ?>
                                    </select>
                                </div>

                                <button type="submit" name="submit" class="btn btn-raised btn-primary" style="margin-left:15px; padding:10px">Sort</button>
                            </form>

                        </div>
                    </div>
                </scetion>
            </div>
        </div>
        <!-- row ends  -->



        <!-- row -->
        <div class="row">
            <div class="col-md-5">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">
                        <div class="boxs-body">

                            <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Incoming Cash</strong></h3>

                            <div class="row col-md-12">
                                <div class="pull-left"><h3>Total Sales</h3></div>
                                <div class="pull-right">
                                    <h3><strong>=N= <span id="subTotal"> <?php echo number_format($totalSales); ?></span></strong></h3>
                                </div>
                            </div>

                            <br /><hr /><br />

                            <div class="row col-md-12" style="margin-top:104px">
                                <div class="pull-left"><h3 style="font-size:18px"><Strong>Total Incoming Cash</strong></h3></div>
                                <div class="pull-right">
                                    <?php
                                        $totalIncomingCash = $totalSales;
                                     ?>
                                    <span class="label label-success" style="font-size:18px"> =N= <?php echo number_format($totalIncomingCash);  ?></span>
                                </div>
                            </div>

                            <br style="clear:both" /><br />



                        </div>
                    </div>

                </section>

            </div>

        <!-- </div> -->

        <div class="col-md-5" >
            <section class="boxs ">
                <div class="boxs-header dvd dvd-btm">

                    <div class="boxs-body">

                        <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Outgoing Cash</strong></h3>

                        <div class="row col-md-12">
                            <div class="pull-left"><h3>Total Cost of Goods</h3></div>
                            <div class="pull-right">
                                <h3><strong>=N= <span id="subTotal"> <?php echo number_format($totalCostOfGoods); ?></span></strong></h3>
                            </div>
                        </div>

                        <br /><hr />

                        <div class="row col-md-12">
                            <div class="pull-left"><h3>Total Expenses</h3></div>
                            <div class="pull-right">
                                <h3><strong>=N= <span> <?php echo number_format($totalExpenses); ?></span></strong></h3>
                            </div>
                        </div>

                        <br /><hr />

                        <div class="row col-md-12">
                            <div class="pull-left"><h3>Total Salaries Payouts</h3></div>
                            <div class="pull-right">
                                <h3><strong>=N= <span > <?php echo number_format($totalSalariesDue);  ?></span></strong></h3>
                            </div>
                        </div>

                        <br /><hr />

                        <div class="row col-md-12">
                            <div class="pull-left"><h3 style="font-size:18px"><strong>Total Outgoing Cash</strong></h3></div>
                            <div class="pull-right">

                                <?php
                                    $totalOutgoingCash = $totalCostOfGoods + $totalExpenses + $totalSalariesDue;
                                 ?>
                                <span class="label label-danger" style="font-size:18px"> =N= <?php echo number_format($totalOutgoingCash); ?></span>
                            </div>
                        </div>



                        <br style="clear:both" /><br />

                    </div>
                </div>
            </section>
        </div>

    </div>



    <!-- row -->
    <div class="row">
        <div class="col-md-10">
            <section class="boxs ">
                <div class="boxs-header dvd dvd-btm">

                    <div class="boxs-body text-center" style="paddin:20px">

                        <?php
                                $label = $totalIncomingCash >= $totalOutgoingCash ? 'PROFIT' : 'LOSS';
                         ?>
                        <h1 style="font-size:26px"><strong>TOTAL <?php echo $label; ?></strong> : <span class="label label-info" style="font-size:26px; margin-left:20px">=N= <?php echo number_format(abs($totalIncomingCash - $totalOutgoingCash)); ?></span></h1>

                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- row ends  -->


</section>
<!--/ CONTENT -->

</div>

<?php
    $registry->get('includer')->render('footer', array('js' => array(
                                'bundles/libscripts.bundle.js',
                                'bundles/vendorscripts.bundle.js',
                                'bundles/sweetalertscripts.bundle.js',
                                'js/main.js',
                                'js/ctrl.js',
                                'js/sales.js'
                            )));

?>
