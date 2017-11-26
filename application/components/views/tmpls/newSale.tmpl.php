<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', null);

    global $today;


      # check if user is logged in
      if(!$session->read('loggedIn')){
          $registry->get('uri')->redirect();
      }

    $thisUser = unserialize($session->read('thisUser'));

    # check user privilege
    # check user privilege
    if(!in_array($thisUser->get('activeAcct'), array(5))){
        $registry->get('uri')->redirect();
    }


    # check if any item is currently in sales docket
    $response = $registry->get('db')->query('select * from salesDocket where onHold = 1 order by id desc limit 1');
    //echo $response->transId; die;
    if(false === $response){

        # fetch next purchase no
        $result = $registry->get('db')->bindFetch('select lastInvioceNo as no from appCache where id = :id', array('id' => 1), array('no'));
        $invioceNo = (int)$result['no'] + 1;

    }else{

        $invioceNo = explode('-', $response->transId);
        $invioceNo = (int)$invioceNo[1] + 1;
    }


    # fetch sales docket
    $salesDocket = Sales::fetchDocket($thisUser->get('id'), 'INV-'. $invioceNo);

    # count transactions on hold
    $transactionsOnHoldCount = Sales::countTransactionsOnHoldForUser($thisUser->get('id'));


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/sweetalert/sweetalert2.css',
        'css/main.css'
    )));

    #include Sidebar
    $registry->get('includer')->render('sidebar', array());



?>

<style>

    #responseHolder{
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
        min-height: 50px;
        min-width: 90%;
        background: #f7f7f7;
        position: absolute;
        left:11px;
        top:38px;
        z-index: 1000;
        display: none;
    }

    .suggestion{
        padding: 4px;
        border-bottom: 1px dotted #ddd;
        cursor: pointer;
    }

    .suggestion:hover{
        color: #ff0000;
    }

    .highlight{
        color: #0044cc;
    }



</style>

<!--  CONTENT  -->
<section id="content">
    <div class="page page-tables-footable">
        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">New Sale</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>


        <!-- row -->
        <div class="row">
            <div class="col-md-12">

                <section class="boxs">

                    <div class="boxs-body example">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone"><strong>CUSTOMER DETAILS</strong></label>


                                    <input type="text" id="customerName" class="form-control col-sm-12" placeholder="Name" />

                                    <br />

                                    <input type="text" id="customerAddr" class="form-control col-sm-12" placeholder="Address" />
                                    <br />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone"><strong>SHIPPING DETAILS</strong></label>

                                    <textarea class="form-control col-sm-12" id="shippingAddr" style="height:100px" placeholder="Enter Shipping Address"></textarea>

                                    <br />

                                </div>
                            </div>


                            <div class="col-md-4">

                                <div class="form-group col-sm-12">

                                    <label for="phone"><strong>INVIOCE DETAILS</strong></label>

                                    <input type="text" id="invioceNo" class="form-control col-sm-12" placeholder="Invioce No" value="<?php echo $invioceNo; ?>" onkeyup="fetchPrevious(this.value, 'invioceNo');" autocomplete="off" />

                                    <input type="hidden" id="invioceNoHidden" value="<?php echo $invioceNo; ?>" />

                                    <br />

                                    <input type="text" id="date" class="form-control col-sm-12" placeholder="Date" value="<?php echo changeDateFormat($today); ?>" />

                                    <input type="hidden" id="dateHidden" value="<?php echo changeDateFormat($today); ?>" />

                                    <br />

                                </div>

                            </div>

                        </div>
                    </div>
                </section>

                <section class="boxs">

                    <div id="docket"></div>

                    <div class="boxs-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Bar Code</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Discount (%)</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>



                            <!-- hold count on transactions put on-hold -->
                            <input type="hidden" id="onHoldTransactionsCount" value="<?php echo $transactionsOnHoldCount; ?>" />

                            <!-- Hold docket count to make sure sale is not completed if docket is empty -->
                            <input type="hidden" id="docketCount" value="<?php echo count($salesDocket); ?>" />

                            <tbody id="docketHolder">


                                <?php
                                    if(count($salesDocket) > 0){

                                    $total = $totalQty = $totalPrice = $totalDiscount = $subTotal = 0;
                                    foreach($salesDocket as $docketItem){
                                        $totalQty += $docketItem->qty;
                                        $totalPrice += $docketItem->price;
                                        $totalDiscount += $docketItem->discount;
                                        $subTotal += ( $docketItem->qty * $docketItem->price);
                                        $total += $docketItem->total;

                                        $stockItem = new StockItem(StockItem::fetchIdByCodeNo($docketItem->codeNo));
                                ?>

                                        <tr id="row<?php echo $docketItem->id; ?>">
                                            <td width="15%"><?php echo $docketItem->codeNo; ?></td>
                                            <td width="45%"><?php echo $stockItem->get('name'); ?></td>
                                            <td><?php echo number_format($docketItem->qty); ?></td>
                                            <td><?php echo number_format($docketItem->price); ?></td>
                                            <td><?php echo number_format($docketItem->discount); ?></td>
                                            <td><?php echo number_format($docketItem->total); ?></td>
                                            <td>

                                                <button class="btn btn-primary btn-raised" style="padding:5px 10px; margin-top:-3px" title="Delete Item" onclick="deleteDocketItem('<?php echo $docketItem->id; ?>')"><i class="fa fa-remove" style="color:white"></i></button>

                                            </td>

                                        </tr>

                                <?php } ?>


                                    <tr>
                                        <td colspan="2"><h4><strong>Total</strong></h4></td>

                                        <td><strong><?php echo number_format($totalQty); ?></strong></td>
                                        <td><strong><?php echo number_format($totalPrice); ?></strong></td>
                                        <td><strong><?php echo number_format($totalDiscount); ?></strong></td>
                                        <td><strong><?php echo number_format($total); ?></strong></td>
                                        <td>

                                            <button class="btn btn-warning btn-raised" style="padding:5px 10px; margin-top:-3px"  title="Clear Docket" id="clearDocketBtn"><i class="fa fa-cogs" style="color:white"></i></button>

                                        </td>

                                    </tr>



                                <?php } ?>
                            </tbody>

                            <tbody id="formHolder" style="visibility:visible">


                                <tr style="background:#fff">
                                    <td>

                                        <form class="form-group" onsubmit="getItem(); return false;">

                                            <div class="form-group col-sm-12">

                                                <input type="text" id="codeNo" class="form-control" autofocus placeholder="Code No" onkeyup="mirrorValue(this.value, 'codeNo'); getItem();" autocomplete="off" style="margin-top:-22px">

                                            </div>
                                        </form>

                                    </td>
                                    <td>
                                        <div class="form-group col-sm-12">
                                            <input type="text" id="itemName" class="form-control"  placeholder="Item Name" onkeyup="fetchItem(this.value, false)" autocomplete="off" >
                                            <div id="responseHolder"></div>
                                            <input type="hidden" id="codeNoHidden" id="codeNoHidden">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-sm-12">
                                            <input type="text" id="qty" class="form-control" onkeyup="calculateTotal(event, this); "  placeholder="Qty" >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-sm-12">
                                            <input type="text" id="price" class="form-control" readonly  placeholder="Price" >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-sm-12">
                                            <input type="text" id="discount" class="form-control col-md-2" onkeyup="calculateAndSubtractDiscount(event, this); " placeholder="Discount" value="0" >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-sm-12">
                                            <input type="text" id="total" class="form-control" readonly placeholder="Total" >
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" onclick="addToDocket()" class="btn btn-raised btn-info" style="margin-top:25px; padding:5px 10px">Add</button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>




                </section>

                <section id="bottomDiv" class="boxs">

                    <div class="boxs-body">
                        <div class="row">
                            <div class="col-md-7" id="actionsHolder">

                                <p><strong>Payment Type</strong></p>
                                <select class="form-control pull-left" id="payType" style="width:130px; margin-top:18px">
                                    <option value="1">Cash</option>
                                    <option value="2">POS</option>
                                    <option value="3">Bank Transfer</option>
                                </select>

                                <button type="button" id="holdAndRecallBtn" class="btn btn-raised btn-warning" style="margin-left:45px; padding:10px"><i class="fa fa-repeat"></i> HOLD/RECALL</button>


                                <button type="button" id="emailInvioceBtn" class="btn btn-raised btn-info" style="margin-left:20px; padding:10px"><i class="fa fa-file-text"></i> EMAIL INVIOCE</button>


                                <button type="button" id="printInvioceBtn" class="btn btn-raised btn-success" style="margin-left:20px; padding:10px"><i class="fa fa-print"></i> PRINT INVIOCE</button>


                            </div>

                            <div id="totals" class="col-md-4 col-md-offset-1 text-right">

                                <p><strong>SubTotal : =N= <span id="subTotal"><?php echo isset($subTotal) ? number_format($subTotal) : 0; ?></span></strong></p>
                                <p><strong>Discount : % <span id="discount"><?php echo isset($subTotal) ? number_format($totalDiscount) : 0; ?></span></strong></p>
                                <p><strong>GrandTotal : =N= <span id="grandTotal"><?php echo isset($subTotal) ? number_format($total) : 0; ?></span></strong></p>

                            </div>

                        </div>
                    </div>
                </section>

            </div>

        <!-- end row> -->


    </div>
</section>
<!--/ CONTENT -->

</div>


<!-- Modal -->
<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display:none">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Transactions On-Hold</h4>
            </div>
            <div class="modal-body" id="modalBody">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invioce No.</th>
                        </tr>
                    </thead>
                    <tbody id="transHolder">


                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" id="dismissModalBtn" class="btn btn-raised btn-sm btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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
