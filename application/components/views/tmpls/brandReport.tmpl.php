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

    $brandId = '';

    $sortSale = false;
    if($session->read('brandReportBeginDate')){

        $beginDate = $session->read('brandReportBeginDate');
        $endDate = $session->read('brandReportEndDate');



        $b_day = $session->read('brandReportDate-b-day');
        $b_month = $session->read('brandReportDate-b-month');
        $b_year = $session->read('brandReportDate-b-year');

        $e_day = $session->read('brandReportDate-e-day');
        $e_month = $session->read('brandReportDate-e-month');
        $e_year = $session->read('brandReportDate-e-year');

        $brandId = $session->read('brandReport-brand');


        $sortSale = true;

        $session->write('brandReportBeginDate', null);
        $session->write('brandReportEndDate', null);
        $session->write('brandReport-brand', null);
        $session->write('brandReportDate-b-day', null);
        $session->write('brandReportDate-b-month', null);
        $session->write('brandReportDate-b-year', null);
        $session->write('brandReportDate-e-day', null);
        $session->write('brandReportDate-e-month', null);
        $session->write('brandReportDate-e-year', null);


        $allSalesForBrand = Sales::fetchAllSalesForBrand(array(
            'beginDate' => $beginDate,
            'endDate' => $endDate,
            'brandId' => $brandId
        ));


        $transactionsSum = Sales::fetchSortedSalesByBrand(array(
            'beginDate' => $beginDate,
            'endDate' => $endDate,
            'brandId' => $brandId
        ));

        # graph data
        $dataPoints = array();
        foreach ($transactionsSum as $value) {
            # code...
            $item = new StockItem(StockItem::fetchIdByCodeNo($value->codeNo));
            array_push($dataPoints, array(
                "y" => $value->totalQty,
                "label" => $item->get('name')
                ));
        }

    }else{

        $dataPoints = array();

        foreach(StockItem::fetchBrands(20) as $brand){
            $totalQtySold = Sales::fetchTotalSalesByBrand(array(
                'brandId' => $brand->id,
                'beginDate' => date('Y') . '-' . date('m') . '-01',
                'endDate' => date('Y') . '-' . date('m') . '-31',
                'limit' => 20
            ));
            array_push($dataPoints, array(
                "y" => $totalQtySold,
                "label" => StockItem::getBrandName($brand->id)
                ));
        }

        //var_dump($dataPoints2); die;
        //die;

    }


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/footable/css/footable.core.min.css',
        'css/main.css'
        // 'css/bootstrap3-5.css',
        // 'css/reportPrint.css'
    ), 'js' => array('js/jquery.min.js', 'js/canvasjs.min.js')));

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
                    <h1 class="font-thin h3 m-0">Brands Report</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">

            <div class="col-md-12">
                <section class="boxs ">
                    <div class="boxs-body">

                        <form method="post" action="<?php echo $baseUri; ?>/report/brand" class="form-inline" role="form">

                            <label style="margin-left:20px; color:#F15F79; font-size:15px;">Begin Date</label>

                            <div class="form-group">
                                <select class="form-control mb-10" name="b-day" style="width:50px; margin-left:10px" required>

                                    <?php
                                        for($i = 1; $i <= 31; $i++){
                                            $value = $i < 10 ? '0' . $i : $i;
                                            $selected = ($b_day == (int)$value) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                    <?php } ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control mb-10" name="b-month" style="width:110px; margin-left:10px" required>

                                    <?php
                                        for($i = 1; $i <= 12; $i++){
                                        $value = $i < 10 ? '0' . $i : $i;
                                        $selected = ($b_month == $value) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo changeDateFormat($value, 'm', 'F'); ?></option>
                                    <?php } ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control mb-10" name="b-year" style="width:60px; margin-left:10px" required>
                                    <?php
                                        for($i = date('Y'); $i <= date('Y') + 5; $i++){
                                        $value = $i < 10 ? '0' . $i : $i;
                                        $selected = ($b_year == $value) ? 'selected' : '';
                                    ?>

                                       <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                     <?php } ?>
                                </select>
                            </div>


                            <label style="margin-left:30px; color:#F15F79; font-size:15px;">End Date</label>

                            <div class="form-group">

                                <select class="form-control mb-10" name="e-day" style="width:50px; margin-left:8px" required>

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
                                <select class="form-control mb-10" name="e-month" style="width:110px" required>

                                    <?php
                                        for($i = 1; $i <= 12; $i++){
                                        $value = $i < 10 ? '0' . $i : $i;
                                        $selected = ($e_month == $value) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo changeDateFormat($value, 'm', 'F'); ?></option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="form-group" style="margin-left:10px">
                                <select class="form-control mb-10" name="e-year" style="width:60px" required>
                                        <?php
                                            for($i = date('Y'); $i <= date('Y') + 5; $i++){
                                            $value = $i < 10 ? '0' . $i : $i;
                                            $selected = ($e_year == $value) ? 'selected' : '';
                                        ?>

                                           <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                         <?php } ?>
                                </select>
                            </div>

                            <label style="margin-left:30px; color:#F15F79; font-size:15px;">Brand</label>

                            <div class="form-group" style="margin-left:10px">
                                <select class="form-control mb-10" name="brandId" style="width:200px" required>
                                    <?php
                                        foreach(StockItem::fetchBrands() as $brand){
                                            $selected = ( $brandId == $brand->id ) ? 'selected' : '';
                                     ?>
                                        <option value="<?php echo $brand->id; ?>" <?php echo $selected; ?>><?php echo $brand->name; ?></option>
                                     <?php } ?>
                                </select>
                            </div>


                            <button style="margin-left:50px" type="submit" name="submit" class="btn btn-raised btn-primary">Sort</button>
                        </form>

                    </div>
                </section>
            </div>

            <?php if($sortSale){ ?>

                <div id="reportSlip-Holder">

                <style>

                    .content{
                        text-align:center
                    }

                    .content > .table{
                        margin: 0 auto;
                    }

                    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                        padding: 8px;
                        line-height: 1.42857143;
                        vertical-align: top;
                        border-top: 1px solid #ddd;
                    }

                    #reportSlip{
                        display:none; width:700px; text-align:center; font-size: 12px; margin:40px auto;
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                    }
                    #reportSlip-title{
                        margin-bottom: 20px; font-size: 38px; font-weight: 400;
                    }
                    .mini-title{ font-size:23px; font-weight: 200; }

                </style>

                <!-- Hidden Div ( Content to be printed ) -->

                <div id="reportSlip">
                	<div id="reportSlip-title">
                        Prelizz Perfumery<br/>

                        <span class="mini-title" style="">Brand Sales Report ( <?php echo StockItem::getBrandName($brandId); ?> )</span>
                        <br />

                        <span class="mini-title" style="font-size:23px;
                        font-weight: 200;"><?php echo changeDateFormat($beginDate, 'Y-m-d', 'jS M Y') . ' - ' . changeDateFormat($endDate,'Y-m-d', 'jS M Y'); ?></span>
                    </div>
                    <div class="content">
                               <table class="table">
                                   <tr style="font-weight: bold;">
                                     <td ><p style='font-size:16px;'>Date</p></td>
                                     <td width="50%" align="center"><p style='font-size:16px;'>Description</p></td>
                                     <td><p style='font-size:16px;'>Qty</p></td>
                                     <td><p style='font-size:16px;'>Amount</p></td>
                                   </tr>

                                   <?php
                                   $totalAmt = $totalQty = 0;
                                   foreach ($allSalesForBrand as $value) {
                                       # code...
                                       $sale = new Sales($value);
                                       $totalQty += $sale->get('qty');
                                       $totalAmt += $sale->get('price');
                                   ?>
                                   <tr>
                                       <td><?php echo changeDateFormat($sale->get('date')); ?></td>
                                       <td align="center"><?php echo $sale->get('item')->get('name'); ?></td>
                                       <td class="td-num"><?php echo number_format($sale->get('qty')); ?></td>
                                       <td class="td-num"><?php echo number_format($sale->get('price')); ?></td>
                                   </tr>
                                   <?php } ?>

                                  <tr>
                                   <td colspan="2"><p style='font-size:18px;'><strong>Total</strong></p></td>
                                   <td class="td-num"><p style='font-size:18px;'><strong><?php echo number_format($totalQty); ?></strong></p></td>
                                   <td class="td-num"><p style='font-size:18px;'><strong><?php echo number_format($totalAmt); ?></strong></p></td>
                                 </tr>
                              </table>
                    </div>
                </div>
            </div>

                <!-- End hidden Div -->


                <div class="col-md-6">
                    <section class="boxs ">
                        <div class="boxs-body">
                            <h4 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Sales Details (<?php echo changeDateFormat($beginDate, 'Y-m-d', 'jS M Y') . ' - ' . changeDateFormat($endDate,'Y-m-d', 'jS M Y'); ?>)</strong></h4>

                            <div class="form-group pull-right">
                                <label for="filter" style="padding-top: 5px; font-size:14px"><strong>Search Table:</strong></label>
                                <input id="filter"  style="font-size:14px; padding-left:7px" type="text" class="form-control rounded input-sm w-md mb-10 inline-block"/>
                            </div>

                            <div >

                                <table id="searchTextResults" data-filter="#filter" data-page-size="1000" class="footable table table-custom">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Qty</td>
                                            <th>Amount</th>
                                            <th>Sold By</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $totalAmt = $totalQty = 0;
                                        foreach ($allSalesForBrand as $value) {
                                            # code...
                                            $sale = new Sales($value);
                                            $totalQty += $sale->get('qty');
                                            $totalAmt += $sale->get('price');
                                        ?>
                                        <tr>
                                            <td><?php echo changeDateFormat($sale->get('date')); ?></td>
                                            <td><?php echo $sale->get('item')->get('name'); ?></td>
                                            <td><?php echo number_format($sale->get('qty')); ?></td>
                                            <td><?php echo number_format($sale->get('price')); ?></td>
                                            <td><?php echo $sale->get('staff')->get('name'); ?></td>
                                        </tr>
                                        <?php } ?>
                                        <tr>
                                            <td colspan="2"><h3>Total</h3></td>
                                            <td><h3><?php echo number_format($totalQty); ?></h3></td>
                                            <td colspan="2"><h3><?php echo number_format($totalAmt); ?></h3></td>

                                        </td>

                                    </tbody>
                                    <!-- <tfoot class="hide-if-no-paging">
                                        <tr>

                                            <td colspan="6" class="text-right"><ul class="pagination">
                                                </ul></td>
                                        </tr>
                                    </tfoot> -->
                                </table>
                            </div>

                            <button class="btn btn-raised btn-warning" id="printBtn" onclick="printSalesDetails()">Print</button>

                        </div>
                    </section>
                </div>
                <div class="col-md-6">
                    <section class="boxs ">
                        <div class="boxs-body">
                            <h4 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Top Selling <?php echo StockItem::getBrandName($brandId); ?> Perfumes (<?php echo changeDateFormat($beginDate, 'Y-m-d', 'jS M Y') . ' - ' . changeDateFormat($endDate, 'Y-m-d', 'jS M Y'); ?>) </strong></h4>

                            <div class="row" id="salesChartContainer" style="height: 350px; width: 80%; padding-left:3% "></div>

                        </div>
                    </section>
                </div>


            <?php }else{ ?>

                <div class="col-md-12">
                    <section class="boxs ">
                        <div class="boxs-body">
                            <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Top Selling Brands ( <?php echo date('F Y'); ?> )</strong></h3>

                            <div class="row" id="salesChartContainer" style="height: 350px; width: 80%; padding-left:3% "></div>

                        </div>
                    </section>
                </div>

            <?php } ?>



        <!-- </div> -->


        </div>
        <!-- End Row -->



    </div>
</section>
<!--/ CONTENT -->

<script type="text/javascript">

$(function () {
    var chart = new CanvasJS.Chart("salesChartContainer", {
        animationEnabled: true,
        axisY: {
    		title: "No of Bottles Sold",
    		titleFontColor: "#4F81BC",
    		lineColor: "#4F81BC",
    		labelFontColor: "#4F81BC",
    		tickColor: "#4F81BC"
    	},

        data: [
        {
            type: "column",
            dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }
        ]
    });
    chart.render();



});


</script>

<?php
    $registry->get('includer')->render('footer', array('js' => array(
                                'bundles/libscripts.bundle.js',
                                'bundles/vendorscripts.bundle.js',
                                'js/vendor/footable/footable.all.min.js',
                                'js/main.js',
                                'js/ctrl.js',
                                'js/sales.js'
                            )));

?>
