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


    /*******
        CanvasJS Chart
    ********/


    # set date and year. remove - 2 on deployment...was only used so more data can be fetched
    $month = date('m');
    $year = date('Y');


    #fetch total Expenses amount for this month
    $totalExpenses = Expenses::sumTotalExpensesForMonth($month, $year);

    # fetch expenses Summary
    $expensesSummary = Expenses::fetchSummaryForMonth($month, $year, 10);

    $dataPoints = array();
    $dataPoints2 = array();

    # Push the data into the array
    foreach ($expensesSummary as $es) {

        $expense = new Expenses($es->id);

        array_push($dataPoints2, array(
            "y" => calculatePercentage($totalExpenses, $es->total),
            "indexLabel" => $expense->get('category')
            )
        );

        array_push($dataPoints, array(
            "y" => $es->total,
            "label" => $expense->get('category')
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
                    <h1 class="font-thin h3 m-0">Expenses Summary ( <?php echo date('F Y'); ?> )</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-6">
                <section class="boxs ">
                    <div class="boxs-body">
                        <div class="row" id="barChartContainer" style="height: 370px; width: 80%; padding-left:3% "></div>
                    </div>
                </section>
            </div>

            <div class="col-md-6">
                <section class="boxs ">
                    <div class="boxs-body">
                        <div class="row" id="pieChartContainer" style="height: 370px; width: 80%; padding-left:3% "></div>
                    </div>
                </section>
            </div>
        </div>
        <!-- End Row -->

        <!-- row -->
        <div class="row">

        </div>
        <!-- End Row -->



    </div>
</section>
<!--/ CONTENT -->

<script type="text/javascript">

$(function () {
    var chart = new CanvasJS.Chart("barChartContainer", {
        animationEnabled: true,
        axisY: {
    		title: "Amount",
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

var chart2 = new CanvasJS.Chart("pieChartContainer",
{
    animationEnabled: true,
	legend: {
		maxWidth: 450,
		itemWidth: 120
	},
	data: [
	{
		type: "pie",
		showInLegend: true,
		legendText: "{indexLabel}",
		dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
	}
	]
});
chart2.render();

</script>





<?php
    $registry->get('includer')->render('footer', array('js' => array(
                                'bundles/libscripts.bundle.js',
                                'bundles/vendorscripts.bundle.js',
                                'js/main.js'
                            )));
    $session->write('hasChart', null);

?>
