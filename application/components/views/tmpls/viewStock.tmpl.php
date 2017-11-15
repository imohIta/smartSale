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

    # fetch current docket
    $stockItems = StockItem::fetchAll();

?>

<!--  CONTENT  -->
<section id="content">
    <div class="page page-tables-footable">
        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Items in Stock</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-12">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">
                        <div class="boxs-body">


                        </div>
                    </div>
                    <div class="boxs-body">

                        <?php

                                if(count($stockItems) == 0){

                         ?>
                         <div style="margin-bottom:150px">
                             <h3>No Item in Stock</h3>
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
                                    <th>Code No</th>
                                    <th>Item Name</th>
                                    <th>Qty In Stock</th>
                                    <?php
                                    if(in_array($thisUser->get('activeAcct'), array(2,3))){
                                    ?>
                                    <th>Cost Price</th>
                                    <?php } ?>
                                    <th>Wholesale Price</th>
                                    <th>Retail Price</th>


                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $count = 1; $totalQty = $totalCostPrice = $totalWholesalePrice = $totalRetailPrice = 0;
                                    foreach($stockItems as $stockItem){

                                      // $stockItem = new StockItem($stockItem);
                                     // var_dump($stockItem);

                                       $totalCostPrice += $stockItem->get('costPrice');
                                       $totalWholesalePrice += $stockItem->get('wholesalePrice');
                                       $totalRetailPrice += $stockItem->get('retailPrice');
                                       $totalQty += $stockItem->get('qtyInStock');
                                ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $stockItem->get('codeNo'); ?></td>
                                        <td><?php echo $stockItem->get('name'); ?></td>
                                        <td>
                                            <?php
                                                if($stockItem->get('qtyInStock') < 5){
                                            ?>
                                                <span class="label label-danger"><?php echo number_format($stockItem->get('qtyInStock')); ?></span>
                                            <?php
                                            }else{
                                                echo number_format($stockItem->get('qtyInStock'));
                                            }
                                            ?>
                                        </td>
                                        <?php
                                            if(in_array($thisUser->get('activeAcct'), array(2,3))){
                                        ?>
                                            <td><?php echo number_format($stockItem->get('costPrice')); ?></td>
                                        <?php } ?>
                                        <td><?php echo number_format($stockItem->get('wholesalePrice')); ?></td>
                                        <td><?php echo number_format($stockItem->get('retailPrice')); ?></td>

                                    </tr>

                                <?php

                                    $count++;
                                }

                                if(in_array($thisUser->get('activeAcct'), array(2,3))){

                                ?>

                                <tr>
                                    <td colspan="3"><h4><strong>Total</strong></h4></td>
                                    <td><h4><strong>
                                        <?php echo number_format($totalQty); ?>
                                    </strong></h4></td>

                                    <td><h4><strong>
                                        <?php echo number_format($totalCostPrice); ?>
                                    </strong></h4></td>

                                    <td><h4><strong>
                                        <?php echo number_format($totalWholesalePrice); ?>
                                    </strong></h4></td>

                                    <td><h4><strong>
                                        <?php echo number_format($totalRetailPrice); ?>
                                    </strong></h4></td>

                                </tr>

                                <tr>
                                    <td colspan="5"><h4><strong>Expected Profit</strong></h4></td>
                                    <td><h4><strong><?php echo number_format($totalWholesalePrice - $totalCostPrice); ?></strong></h4></td>
                                    <td><h4><strong><?php echo number_format($totalRetailPrice - $totalCostPrice); ?></strong></h4></td>
                                </tr>

                                <?php } ?>




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
