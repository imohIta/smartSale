<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');



      # check if user is logged in
      if(!$session->read('loggedIn')){
          $registry->get('uri')->redirect();
      }

    $thisUser = unserialize($session->read('thisUser'));

    # check user privilege
    if(!in_array($thisUser->get('activeAcct'), array(2,3))){
        $registry->get('uri')->redirect();
    }


    $salesDataPoints = array();
    $purchasesDataPoints = array();

    for($i = 1; $i <= 12; $i++){
        $month = $i < 10 ? '0' . $i : $i;

        # fetch total sales fot this month
        $transactionsTotal = Sales::getTotalForMonth($month, date('Y'));

        # push to salesDatapoints
        array_push($salesDataPoints, array(
            "label" => changeDateFormat($month, 'm', 'M'),
            "y" => is_null($transactionsTotal) ? 0 : $transactionsTotal
            // "y" => is_null($transactionsTotal) ? rand(1000000, 6000000) : $transactionsTotal
        ));

        # fetch total Purchases for the month
        $totalPurchases = StockItem::getTotalPurchaseAmountForMonth($month, date('Y'));
        # push to salesDatapoints
        array_push($purchasesDataPoints, array(
            "label" => changeDateFormat($month, 'm', 'M'),
            "y" => is_null($totalPurchases) ? 0 : $totalPurchases
            // "y" => is_null($totalPurchases) ? rand(1000000, 6000000) : $totalPurchases
        ));

    }



    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'css/main.css'
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
                    <h1 class="font-thin h3 m-0">Sales & Purchase Summary</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-12">
                <section class="boxs ">
                    <div class="boxs-body">
                        <div class="row" id="chartContainer" style="height: 370px; width: 95%; padding-left:3%; margin-top:40px "></div>
                    </div>
                </section>
            </div>
        </div>
        <!-- End Row -->



    </div>
</section>
<!--/ CONTENT -->


<script>
window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
    	animationEnabled: true,
    	axisY: {
    		title: "Amount =N=",
    		titleFontColor: "#4F81BC",
    		lineColor: "#4F81BC",
    		labelFontColor: "#4F81BC",
    		tickColor: "#4F81BC"
    	},
    	toolTip: {
    		shared: true
    	},
    	legend: {
    		cursor: "pointer",
    		itemclick: toggleDataSeries
    	},
    	data: [{
    		type: "column",
    		name: "Sales",
    		showInLegend: true,
    		yValueFormatString: "=N= #,##0.#",
    		dataPoints: <?php echo json_encode($salesDataPoints, JSON_NUMERIC_CHECK); ?>
    	}
        ,
    	{
    		type: "column",
    		name: "Purchases",
    		showInLegend: true,
    		yValueFormatString: "=N= #,##0.#",
    		dataPoints: <?php echo json_encode($purchasesDataPoints, JSON_NUMERIC_CHECK); ?>
    	}
    ]
});
chart.render();

function toggleDataSeries(e) {
	if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
		e.dataSeries.visible = false;
	} else {
		e.dataSeries.visible = true;
	}
	e.chart.render();
}

}

</script>


<?php
    $registry->get('includer')->render('footer', array('js' => array(
                                'bundles/libscripts.bundle.js',
                                'bundles/vendorscripts.bundle.js',
                                'js/main.js'
                            )));


?>
