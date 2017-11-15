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
    if($session->read('salesBeginDate')){

        $beginDate = $session->read('salesBeginDate');
        $endDate = $session->read('salesEndDate');



        $b_day = $session->read('salesDate-b-day');
        $b_month = $session->read('salesDate-b-month');
        $b_year = $session->read('salesDate-b-year');

        $e_day = $session->read('salesDate-e-day');
        $e_month = $session->read('salesDate-e-month');
        $e_year = $session->read('salesDate-e-year');

        $user = $session->read('salesDate-user');

        $sortSale = true;

        $session->write('salesBeginDate', null);
        $session->write('salesEndDate', null);
        $session->write('salesDate-b-day', null);
        $session->write('salesDate-b-month', null);
        $session->write('salesDate-b-year', null);
        $session->write('salesDate-e-day', null);
        $session->write('salesDate-e-month', null);
        $session->write('salesDate-e-year', null);
        $session->write('salesDate-user', null);


        $transactions = Sales::fetchSortedTransactions(array(
            'beginDate' => $beginDate,
            'endDate' => $endDate,
            'userId' => $user
        ));

        $sales = Sales::fetchSorted(array(
            'beginDate' => $beginDate,
            'endDate' => $endDate,
            'userId' => $user
        ));

        $totalCash = $totalPos = $totalDiscount = $total = $totalCostPrice = $profit = $profitPercent = 0;
        # quickly loop tru sales to get totals

        foreach ($transactions as $trans) {
            # code...
            $total += ($trans->grandTotal + $trans->discount);
            $totalDiscount += $trans->discount;
            switch ($trans->payType) {
                case 1:
                    # cash sale...
                    $totalCash += $trans->grandTotal;
                    break;
                case 2:
                    # POS sale...
                    $totalPos += $trans->grandTotal;
                    break;

            }
        }


        foreach ($sales as $sale) {
            # create new stock Item
            $stockItem = new StockItem(StockItem::fetchIdByCodeNo($sale->codeNo));
            $totalCostPrice += $stockItem->get('costPrice');
        }

        $profit = ($totalCash + $totalPos) - $totalCostPrice;

        $profitPercent = ($profit == 0) ? 0 : ( $profit * 100) / $totalCostPrice;


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
                    <h1 class="font-thin h3 m-0">Staff Sales Report</h1>
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


                            <form class="form-inline" role="form" action="<?php echo $baseUri; ?>/sales/sort" method="post">


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

                                <br /><br />

                                <p><strong>Select Staff</strong></p>
                                <div class="form-group" class="col-md-12">
                                    <select class="form-control mb-10" name="user" style="width:280px" required>
                                                <?php
                                                    foreach (AppUser::fetchSalesReps() as $appUser) {
                                                        # code...
                                                        $selected = ($appUser->id == $user) ? 'selected' : '';
                                                ?>
                                                   <option value="<?php echo $appUser->id; ?>" <?php echo $selected; ?>><?php echo $appUser->name; ?></option>
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

        <div class="col-md-8" id="summary">
            <section class="boxs ">
                <div class="boxs-header dvd dvd-btm">

                    <div class="boxs-body">

                        <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Sales Report for <?php $user = new AppUser($user); echo $user->get('name'); ?>  </strong></h3>


                        <div class="row col-md-12">
                            <div class="pull-left"><h4><strong>Total Cash Amount</strong></h4></div>
                            <div class="pull-right">
                                <h3><strong>=N= <span > <?php if(isset($totalCash)){ echo number_format($totalCash); } ?></span></strong></h3>

                            </div>
                        </div>

                        <br /><hr />

                        <div class="row col-md-12">
                            <div class="pull-left"><h4><strong>Total POS Amount</strong></h4></div>
                            <div class="pull-right">
                                <h3><strong>=N= <span> <?php if(isset($totalPos)){ echo number_format($totalPos); } ?></span></strong></h3>

                            </div>
                        </div>

                        <br /><hr />

                        <div class="row col-md-12">
                            <div class="pull-left"><h4><strong>Total Discount</strong></h4></div>
                            <div class="pull-right">
                                <h3><strong>=N= <span id="subTotal"> <?php if(isset($totalDiscount)){ echo number_format($totalDiscount); } ?></span></strong></h3>

                            </div>
                        </div>

                        <br /><hr />

                        <div class="row col-md-12">
                            <div class="pull-left"><h4><strong>Total Amount</strong></h4></div>
                            <div class="pull-right">
                                <h3><strong> <span class="label label-success" style="font-size:16px">=N= <?php if(isset($total)){ echo number_format($total); } ?></span></strong></h3>

                            </div>
                        </div>

                        <br /><hr />

                        <div class="row col-md-12">
                            <div class="pull-left"><h4><strong>Profit</strong></h4></div>
                            <div class="pull-right">
                                <h3><strong> <span class="label label-warning" style="font-size:16px">=N= <?php if(isset($profit)){ echo number_format($profit); } ?></span>


                                <span class="label label-danger" style="font-size:16px; margin-left:10px"><?php if(isset($profitPercent)){ echo number_format($profitPercent); } ?>%</span>

                                </strong></h3>

                            </div>
                        </div>


                        <br style="clear:both" /><br />

                    </div>


                </div>

            </section>
            </div>


            <div class="col-md-8">

                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">

                        <div class="boxs-body">
                                <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Sales Details</strong></h3>

                                <div class="form-group pull-right">
                                    <label for="filter" style="padding-top: 5px; font-size:14px"><strong>Search Table:</strong></label>
                                    <input id="filter"  style="font-size:14px; padding-left:7px" type="text" class="form-control rounded input-sm w-md mb-10 inline-block"/>
                                </div>
                                <table id="searchTextResults" data-filter="#filter" data-page-size="20" class="footable table table-custom">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Date</th>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                            $count = 1; $total = 0;
                                            foreach($sales as $sale){
                                               // $stockItem = new StockItem($stockItem);

                                                $s = new Sales($sale);
                                                $amt = $sale->qty * $sale->price;
                                                $total += $amt;
                                        ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $sale->date; ?></td>
                                                <td><?php echo $s->get('item')->get('name'); ?></td>
                                                <td><?php echo number_format($sale->qty); ?></td>
                                                <td><?php echo number_format($sale->price); ?></td>
                                                <td><?php echo number_format($amt); ?></td>
                                            </tr>

                                        <?php $count++; } ?>

                                        <tr>
                                            <td colspan="6" class="text-right"><h4 style="padding-right:20px"><strong>Total : </strong>=N= <?php echo number_format($total); ?></h4></td>
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
                                // 'js/vendor/parsley/parsley.min.js',
                                'js/main.js'
                            )));

?>
