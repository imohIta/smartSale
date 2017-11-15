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
    if(!in_array($thisUser->get('activeAcct'), array(2,3))){
        $registry->get('uri')->redirect();
    }

    # fetch Purchase docket
    $purchaseDocket = StockItem::fetchPurchaseDocket(date('Y-m-d'), $thisUser->get('id'));



    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/sweetalert/sweetalert2.css',
        // 'js/vendor/footable/css/footable.core.min.css',
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
                    <h1 class="font-thin h3 m-0">Add New Purchase</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <div class="row">

        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-10">

                <section class="boxs">

                    <div class="boxs-body example">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group is-focused">
                                    <label for="phone"><strong>SUPPLIER</strong></label>
                                    <input type="text" readonly id="supplierOld" style="display:none" class="form-control col-sm-12" />
                                    <select class="form-control mb-10 col-sm-12" id="supplier" style="display:block">

                                        <?php
                                            # fetch all suppliers
                                            $suppliers = $registry->get('db')->query('select * from suppliers', array(), true);

                                            foreach ($suppliers as $supplier) {
                                                # code...

                                        ?>
                                            <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                                        <?php } ?>
                                    </select>
                                    <br />
                                </div>
                            </div>

                            <div class="col-md-4 col-md-offset-4">

                                <div class="form-group col-sm-12">

                                    <?php
                                        # fetch next purchase no
                                        $result = $registry->get('db')->bindFetch('select lastPurchaseNo as no from appCache where id = :id', array('id' => 1), array('no'));
                                        $purchaseId = (int)$result['no'] + 1;
                                     ?>

                                     <div >
                                         <table>
                                             <tr>
                                                 <td><span style="color:#F15F79">Purchase No. : </span></td>
                                                 <td>
                                                     <input type="text" id="purchaseNo" class="form-control" value="<?php echo $purchaseId; ?>" onkeyup="fetchPurchase(this.value, 'purchaseNo');" autocomplete="off" style="width:130px; margin-left:12px" >
                                                 </td>
                                             </tr>

                                             <tr>
                                                 <td><span style="color:#F15F79">Date : </span></td>
                                                 <td>
                                                     <input type="text" id="date" class="form-control" value="<?php echo changeDateFormat($today); ?>"  style="width:130px; margin-left:12px" >
                                                 </td>
                                             </tr>
                                         </table>

                                     </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </section>

                <section class="boxs">

                    <div class="boxs-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Code No</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Cost Price</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody id="docketHolder">
                                <?php
                                    if(count($purchaseDocket) > 0){

                                    $totalQty = $totalCost = 0;
                                    foreach($purchaseDocket as $docketItem){
                                        $totalQty += $docketItem->qty;
                                        $totalCost += $docketItem->price;
                                ?>

                                        <tr id="row<?php echo $docketItem->id; ?>">
                                            <td><?php echo $docketItem->codeNo; ?></td>
                                            <td width="50%"><?php echo $docketItem->itemName; ?></td>
                                            <td><?php echo number_format($docketItem->qty); ?></td>
                                            <td><?php echo number_format($docketItem->price); ?></td>
                                            <td>

                                                <button class="btn btn-primary btn-raised" style="padding:5px 10px; margin-top:-3px" title="Delete Item" onclick="deleteDocketItem('<?php echo $docketItem->id; ?>')"><i class="fa fa-remove" style="color:white"></i></button>

                                            </td>

                                        </tr>

                                <?php } ?>


                                    <tr>
                                        <td colspan="2"><h4><strong>Total</strong></h4></td>

                                        <td><?php echo number_format($totalQty); ?></td>
                                        <td><?php echo number_format($totalCost); ?></td>
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
                                            <input type="text" id="qty" class="form-control" onkeyup="addItemToDocket(event, this)"  placeholder="Qty" >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group col-sm-12">
                                            <input type="text" id="price" class="form-control" onkeyup="addItemToDocket(event, this)"  placeholder="Cost Price" >
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" onclick="addToDocket()" class="btn btn-raised btn-info" style="margin-top:25px; padding:5px 10px">Add</button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="boxs-body">
                        <button type="button" id="addItemsToStockBtn" class="btn btn-raised btn-success" style="margin-top:25px; padding:10px">SAVE</button>
                    </div>

                </section>



            </div>

        <!-- end row> -->




    </div>
</section>
<!--/ CONTENT -->

</div>

<?php
    $registry->get('includer')->render('footer', array('js' => array(
                                'bundles/libscripts.bundle.js',
                                'bundles/vendorscripts.bundle.js',
                                'bundles/sweetalertscripts.bundle.js',
                                'js/main.js',
                                'js/ctrl.js',
                                'js/purchase.js'
                            )));

?>
