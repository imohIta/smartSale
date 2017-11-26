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
    $b_month = date('m') - 1; $e_month = date('m');
    $b_year = date('Y'); $e_year = date('Y');

    $beginDate = $b_year . '-' . $b_month . '-' . $b_day;
    $endDate = $e_year . '-' . $e_month . '-' . $e_day;


    $sortSale = true;
    if($session->read('salesGroupReportBeginDate')){

        $beginDate = $session->read('salesGroupReportBeginDate');
        $endDate = $session->read('salesGroupReportEndDate');



        $b_day = $session->read('salesGroupReportDate-b-day');
        $b_month = $session->read('salesGroupReportDate-b-month');
        $b_year = $session->read('salesGroupReportDate-b-year');

        $e_day = $session->read('salesGroupReportDate-e-day');
        $e_month = $session->read('salesGroupReportDate-e-month');
        $e_year = $session->read('salesGroupReportDate-e-year');


        //$sortSale = true;

        $session->write('salesGroupReportBeginDate', null);
        $session->write('salesGroupReportEndDate', null);
        $session->write('salesGroupReportDate-b-day', null);
        $session->write('salesGroupReportDate-b-month', null);
        $session->write('salesGroupReportDate-b-year', null);
        $session->write('salesGroupReportDate-e-day', null);
        $session->write('salesGroupReportDate-e-month', null);
        $session->write('salesGroupReportDate-e-year', null);


        // $totalCash = $totalPos = $totalDiscount = $total = $totalCostPrice = $profit = $profitPercent = 0;
        // # quickly loop tru sales to get totals
        //
        // foreach ($transactions as $trans) {
        //     # code...
        //     $total += ($trans->grandTotal + $trans->discount);
        //     $totalDiscount += $trans->discount;
        //     switch ($trans->payType) {
        //         case 1:
        //             # cash sale...
        //             $totalCash += $trans->grandTotal;
        //             break;
        //         case 2:
        //             # POS sale...
        //             $totalPos += $trans->grandTotal;
        //             break;
        //
        //     }
        // }
        //
        //
        // foreach ($sales as $sale) {
        //     # create new stock Item
        //     $stockItem = new StockItem(StockItem::fetchIdByCodeNo($sale->codeNo));
        //     $totalCostPrice += $stockItem->get('costPrice');
        // }
        //
        // $profit = ($totalCash + $totalPos) - $totalCostPrice;
        //
        // $profitPercent = ($profit == 0) ? 0 : ( $profit * 100) / $totalCostPrice;


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
                    <h1 class="font-thin h3 m-0">Stock Group Sales Report</h1>
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


                            <form class="form-inline" role="form" action="<?php echo $baseUri; ?>/report/stockGroup" method="post">


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

                                <br /><br />

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
                                <!-- <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Sales Details</strong></h3> -->

                                <div class="form-group pull-right">
                                    <label for="filter" style="padding-top: 5px; font-size:14px"><strong>Search Table:</strong></label>
                                    <input id="filter"  style="font-size:14px; padding-left:7px" type="text" class="form-control rounded input-sm w-md mb-10 inline-block"/>
                                </div>
                                <table id="searchTextResults" data-filter="#filter" data-page-size="20" class="footable table table-custom">
                                    <thead>
                                        <tr>
                                            <th>Group</th>
                                            <th>Discount =N=</th>
                                            <th>Sales =N=</th>
                                            <th>Sales %</th>
                                            <th>Cost =N=</th>
                                            <th>Profit =N=</th>
                                            <th>Profit %</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php

                                            $salesDetails = (object)Sales::fetchTotalSalesForDateRange(array(
                                                'beginDate' => $beginDate,
                                                'endDate' => $endDate
                                            ));

                                            $totalSales = $totalDiscount = $totalCost = $totalProfit = 0;


                                            foreach(StockItem::fetchCategories() as $group){

                                                $groupSales = (object) Sales::fetchSalesTotalByStockCategory(array(
                                                    'beginDate' => $beginDate,
                                                    'endDate' => $endDate,
                                                    'categoryId' => $group->id
                                                ));

                                                $totalDiscount += $groupSales->totalDiscount;
                                                $totalSales += $groupSales->totalSales;

                                                # calculate cost of all

                                                # fetch all codeNo
                                                $result = $registry->get('db')->query('select `codeNo` from `sales` where `codeNo` in ( select `codeNo` from `stockCard` where `groupId` = :group )', array('group' => $group->id), true);

                                                $totalGroupCost = 0;
                                                foreach ($result as $value) {
                                                    # code...
                                                    $totalGroupCost += $registry->get('db')->bindFetch('select costPrice from stockCard where codeNo = :codeNo', array('codeNo' => $value->codeNo), array('costPrice'))['costPrice'];
                                                }

                                                $totalCost += $totalGroupCost;
                                                $profit = $groupSales->totalSales - $totalGroupCost;
                                                $profitPercent = ($profit == 0) ? 0 : ( $profit * 100) / $totalGroupCost;
                                                $totalProfit += $profit;



                                        ?>

                                        <tr>
                                            <td><?php echo $group->name; ?></td>
                                            <td><?php echo number_format($groupSales->totalDiscount); ?></td>
                                            <td><?php echo number_format($groupSales->totalSales); ?></td>
                                            <td><?php echo ($groupSales->totalSales == 0 ) ? 0 : round(($groupSales->totalSales * 100) / $salesDetails->totalSales, 2); ?> %</td>
                                            <td><?php echo number_format($totalGroupCost); ?></td>
                                            <td><?php echo number_format($profit); ?></td>
                                            <td><?php echo ($profitPercent == 0 ) ? 0 : round($profitPercent, 2); ?> %</td>
                                        </tr>


                                        <?php } ?>

                                        <tr>
                                            <td><h4><strong>Total : </strong></h4></td>
                                            <td><h4><strong><?php echo number_format($totalDiscount); ?></strong></h4></td>
                                            <td><h4><strong><?php echo number_format($totalSales); ?></strong></h4></td>
                                            <td>&nbsp;</td>
                                            <td><strong><h4><?php echo number_format($totalCost); ?></strong></h4></td>
                                            <td><strong><h4><?php echo number_format($totalProfit); ?></strong></h4></td>
                                            <td></td>
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
