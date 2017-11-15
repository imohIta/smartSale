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


    $purchasesDataPoints = array();
    $topPurchasesDataPoints = array();

    for($i = 1; $i <= 12; $i++){
        $month = $i < 10 ? '0' . $i : $i;

        # fetch total Purchases for the month
        $totalPurchases = StockItem::getTotalPurchaseAmountForMonth($month, date('Y'));
        # push to salesDatapoints
        array_push($purchasesDataPoints, array(
            "label" => changeDateFormat($month, 'm', 'F'),
            "y" => is_null($totalPurchases) ? 0 : $totalPurchases
            // "y" => is_null($totalPurchases) ? rand(1000000, 6000000) : $totalPurchases
        ));

    }

    # fetch top purchased Items
    $topItems = StockItem::fetchTopPurchases(date('Y'), 5);

    # Push the data into the array
    foreach ($topItems as $value) {
        # code...
        $item = new StockItem(StockItem::fetchIdByCodeNo($value->codeNo));
        array_push($topPurchasesDataPoints, array(
            "y" => $value->total,
            "label" => $item->get('name')
            )
        );
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
                    <h1 class="font-thin h3 m-0">Purchases Summary</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-7">
                <section class="boxs ">
                    <div class="boxs-body">
                        <div class="row" id="purchasesChartContainer" style="height: 370px; width: 95%; padding-left:3% "></div>
                    </div>
                </section>
            </div>

            <div class="col-md-5">
                <section class="boxs ">
                    <div class="boxs-body">
                        <div class="row" id="topPurchasesChartContainer" style="height: 370px; width: 80%; padding-left:3% "></div>
                    </div>
                </section>
            </div>
        </div>
        <!-- End Row -->



    </div>
</section>
<!--/ CONTENT -->


<script>

$(function () {
    var chart2 = new CanvasJS.Chart("topPurchasesChartContainer", {
        animationEnabled: true,
        title:{
    		text: "Top five Purchases for Year " + <?php echo date('Y'); ?>
    	},
        axisY: {
    		title: "No of Bottles Purchased",
    		titleFontColor: "#4F81BC",
    		lineColor: "#4F81BC",
    		labelFontColor: "#4F81BC",
    		tickColor: "#4F81BC"
    	},

        data: [
        {
            type: "column",
            dataPoints: <?php echo json_encode($topPurchasesDataPoints, JSON_NUMERIC_CHECK); ?>
        }
        ]
    });
    chart2.render();
});

window.onload = function () {

    var chart = new CanvasJS.Chart("purchasesChartContainer", {
    	animationEnabled: true,
        title:{
    		text: "Purchases Amount for Year " + <?php echo date('Y'); ?>
    	},
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
    	data: [
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
