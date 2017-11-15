<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', true);


      # check if user is logged in
      if(!$session->read('loggedIn')){
          $registry->get('uri')->redirect();
      }

    $thisUser = unserialize($session->read('thisUser'));


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/footable/css/footable.core.min.css',
        'css/main.css'
    )));

    #include Sidebar
    $registry->get('includer')->render('sidebar', array());

    $date = today();
    $day = date('d');
    $month = date('m');
    $year = date('Y');


    if($session->read('salesDate')){

        $date = $session->read('salesDate');

        $day = $session->read('salesDate-day');
        $month = $session->read('salesDate-month');
        $year = $session->read('salesDate-year');

        $session->write('salesDate', null);
        $session->write('salesDate-day', null);
        $session->write('salesDate-month', null);
        $session->write('salesDate-year', null);

    }

    $sales = Sales::fetchAll($date, $thisUser->get('id'));

?>

<!--  CONTENT  -->
<section id="content">
    <div class="page page-tables-footable">
        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Sales Log ( <?php echo datetoString($date); ?> )</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-10">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">
                        <div class="boxs-body">

                            <h1 class="custom-font">Select Date</h1>
                            <form class="form-inline" role="form" action="<?php echo $baseUri; ?>/sales/viewAll" method="post">



                                <div class="form-group">
                                    <select class="form-control mb-10" name="day" style="width:100px" required>

                                                <?php
                                                    for($i = 1; $i <= 31; $i++){
                                                        $value = $i < 10 ? '0' . $i : $i;

                                                        $selected = ($day == (int)$value) ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo $value; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                                                <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group" style="margin-left:15px">
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
                    <div class="boxs-body">

                        <?php

                                if(count($sales) == 0){

                         ?>
                         <div style="margin-bottom:150px">
                             <h3>No Recorded Sales for <?php echo datetoString($date); ?></h3>
                        </div>

                         <?php }else{ ?>


                        <div class="form-group pull-right">
                            <label for="filter" style="padding-top: 5px; font-size:14px"><strong>Search Table:</strong></label>
                            <input id="filter"  style="font-size:14px; padding-left:7px" type="text" class="form-control rounded input-sm w-md mb-10 inline-block"/>
                        </div>
                        <table id="searchTextResults" data-filter="#filter" data-page-size="20" class="footable table table-custom">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Time</th>
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
                                        <td><?php echo timeToString($sale->time); ?></td>
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

                        <?php } ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>
<!--/ CONTENT -->

</div>

<?php
    $registry->get('includer')->render('footer', array('js' => array(
                                'bundles/libscripts.bundle.js',
                                'bundles/vendorscripts.bundle.js',
                                'js/vendor/footable/footable.all.min.js',
                                // 'js/vendor/parsley/parsley.min.js',
                                'js/main.js'
                            )));

?>
