<?php




    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', null);


      # check if user is logged in
      if(!$session->read('loggedIn')){
          $registry->get('uri')->redirect();
      }

    # get loggedIn User user object
    $thisUser = unserialize($session->read('thisUser'));

    $totalSales = number_format(Sales::getTotal(date('Y-m-d'), $thisUser->get('id')));

    $totalCashSales = number_format(Sales::getTotalCash(date('Y-m-d'), $thisUser->get('id')));

    $totalPOSSales = number_format(Sales::getTotalPOS(date('Y-m-d'), $thisUser->get('id')));






/*******
    CanvasJS Chart
********/


# remove - 2 on deployment...was only used so more data can be fetched
$month = date('m');
$year = date('Y');



# fetch top sold Items
$topItems = StockItem::fetchTopSold($month, $year, 5);

//var_dump($topItems); die;

$dataPoints = array();

# Push the data into the array
foreach ($topItems as $value) {
    # code...
    $item = new StockItem(StockItem::fetchIdByCodeNo($value->codeNo));
    array_push($dataPoints, array(
        "y" => $value->total,
        "label" => $item->get('name')
        )
    );
}

#fetch total Expenses amount for this month
$totalExpenses = Expenses::sumTotalExpensesForMonth($month, $year);

# fetch expenses Summary
$expensesSummary = Expenses::fetchSummaryForMonth($month, $year, 10);

$dataPoints2 = array();

# Push the data into the array
foreach ($expensesSummary as $es) {
    $expense = new Expenses($es->id);
    array_push($dataPoints2, array(
        "y" => calculatePercentage($totalExpenses, $es->total),
        "indexLabel" => $expense->get('category')
        )
    );
}


#include header
$registry->get('includer')->render('header', array('css' => array(
    'css/vendor/animsition.min.css',
    'css/vendor/morphingsearch.css',
    'css/main.css'
), 'js' => array('js/jquery.min.js', 'js/canvasjs.min.js')));

#include Sidebar
$registry->get('includer')->render('sidebar', array());


 ?>

<!-- CONTENT -->
<section id="content">
    <div class="page dashboard-page">

        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Dashboard</h1>
                    <small class="text-muted">Welcome to Smart Sale</small> </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-12">
                <div class="row stats row-sm text-center">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="boxs panel padder-v item">
                            <div class="h1 text-info font-thin h1"><?php echo $totalCashSales; ?></div>
                            <span class="text-muted text-xs">Total Cash Amount</span>
                            <div class="top text-right w-full"><i class="fa fa-caret-down text-warning"></i> </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <a href="#" class="block panel padder-v bg-amethyst item"> <span class="text-white font-thin h1 block"><?php echo $totalPOSSales; ?> </span> <span class="text-muted text-xs text-white">Total POS Amount</span> <span class="bottom text-right w-full"> </span></a>
                    </div>

                    <div class="col-xs-12 m-b-md">
                        <div class="bg-info">
                            <div class="col dk padder-v">
                                <div class="font-thin h1 text-white"><span><?php echo $totalSales; ?></span></div>
                                <span class="text-white ">Total Amount Sold</span> </div>
                        </div>

                        <br />

                    </div>

                    <div class="col-xs-12">
                        <div class="r3_weather bg-hotpink mb-20">
                            <div class="wid-weather wid-weather-small">
                                <div class="location">
                                    <p style="font-size:20px" class="text-center"><i class="fa fa-bookmark icon-lg text-white"></i> <?php echo dateToString(today()); ?></p>
                                </div>
                                <div class="clearfix"></div>
                                <div class="degree">

                                    <div class="clearfix"></div>

                                </div>
                                <div class="clearfix"></div>
                                <div class="weekdays bg-white slim-scroll" style="">
                                    <h3 class="text-center" id="theTime" style="color:#999; font-size:50px; margin-top:80px; padding-bottom:85px"></h3>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php if(in_array($thisUser->get('activeAcct'), array(2,3))){ ?>

            <div class="col-md-7 col-xs-12">
                <section class="boxs">
                    <div class="boxs-header dvd dvd-btm">
                        <h1 class="custom-font"><strong>Top Sales </strong>( <?php echo date('F Y'); ?> )</h1>

                    </div>

                    <div class="boxs-body">
                        <div class="row" id="salesChartContainer" style="height: 250px; width: 80%; padding-left:3% "></div>
                    </div>
                </section>
            </div>

            <br /><br  />

            <div class="col-md-7 col-xs-12">
                <section class="boxs">
                    <div class="boxs-header dvd dvd-btm">
                        <h1 class="custom-font"><strong>Expenses </strong>Statistics</h1>
                    </div>

                    <div class="boxs-body">
                        <div class="row" id="expensesChartContainer" style="height: 250px; width: 80%; padding-left:3% "></div>
                    </div>
                </section>
            </div>

            <?php } ?>
        </div>



    </div>
</section>
<!--/ CONTENT -->
</div>

<script type="text/javascript">

function sivamtime() {
    now=new Date();
    hour=now.getHours();
    min=now.getMinutes();
    sec=now.getSeconds();

    if (min<=9) { min="0"+min; }
    if (sec<=9) { sec="0"+sec; }
    if (hour>12) { hour=hour-12; add="PM"; }
    else { hour=hour; add="AM"; }
    if (hour==12) { add="PM"; }

    time = ((hour<=9) ? "0"+hour : hour) + ":" + min + ":" + sec + " " + add;

    if (document.getElementById) { document.getElementById('theTime').innerHTML = time; }
    else if (document.layers) {
        document.layers.theTime.document.write(time);
        document.layers.theTime.document.close(); }

    setTimeout("sivamtime()", 1000);
}
window.onload = sivamtime;


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

var chart2 = new CanvasJS.Chart("expensesChartContainer",
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
                                // 'js/canvasjs.min.js'
                            )));

?>
