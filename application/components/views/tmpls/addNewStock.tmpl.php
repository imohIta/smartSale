<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', true);


  # check if user is logged in
  if(!$session->read('loggedIn')){
      $registry->get('uri')->redirect();
  }

    $thisUser = unserialize($session->read('thisUser'));

    # check user privilege
    if(!in_array($thisUser->get('activeAcct'), array(2,3,4))){
        $registry->get('uri')->redirect();
    }

    $itemFound = false;
    $searchQuery = '';
    $codeNo = '';

    # set default search type...this is used to know if the user scanned a barcode or used the Item name smart search
    $searchType = 0;

    $itemName = '';
    $itemPrice = '';
    $itemQty = $id = '';

    # check if item is found
    if($session->read('foundItem')){

        $foundItem = unserialize($session->read('foundItem'));
        $searchQuery = $session->read('searchQuery');
        $searchType = $session->read('searchType');
        $itemFound = true;

        $itemName = $foundItem->get('name');
        $itemPrice = $foundItem->get('price');
        $itemQty = '';
        $id = $foundItem->get('id');

        $codeNo = $foundItem->get('codeNo');

        $session->write('foundItem', null);
        $session->write('searchQuery', null);
        $session->write('searchType', null);

    }


    # fetch current docket
    $docket = StockItem::fetchPurchaseDocket(date('Y-m-d'), $thisUser->get('id'));


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/footable/css/footable.core.min.css',
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
        min-width: 370px;
        background: #f7f7f7;
        position: absolute;
        left:13px;
        top:35px;
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
                    <h1 class="font-thin h3 m-0">Add New Stock</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-6">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">


                        <div class="boxs-body">

                            <form class="form-horizontal" onsubmit="getItem(); return false;">

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Item Name or Barcode</label>
                                    <div class="col-md-9" style="position: relative">
                                        <input type="text" name="itemQuery" id="itemQuery" class="form-control" placeholder="Search for Item or Scan Bar Code" required autocomplete="off"  autofocus  />

                                        <div id="responseHolder"></div>

                                    </div>
                                </div>



                            </form>

                            <div class="form-horizontal" id="formHolder" style="display: none">

                                <input type="hidden" id="codeNo" name="codeNo" value="<?php echo $codeNo; ?>">

                                <div class="form-group" id="nameHolder" style="display: none">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="itemName" class="form-control"  placeholder="" id="itemName">
                                        <!-- <p class="help-block mb-0">Example block-level help text here.</p> -->
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Item Price</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="price" id="price" value="<?php echo $itemPrice; ?>" onkeyup="allowNosOnly(this.value, 'price')" autocomplete="off">
                                        <!-- <p class="help-block mb-0">Example block-level help text here.</p> -->
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Qty</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="qty" id="qty" onkeyup="allowNosOnly(this.value, 'qty')" autocomplete="off">

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Item Unit</label>
                                    <div class="col-sm-9">
                                        <select class="form-control mb-10" name="unit" id="unit" required>
                                            <option value="1">No</option>
                                            <option value="2">Pieces</option>
                                            <option value="3">Dozen</option>
                                            <option value="4">Cartoon</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-19">
                                        <button id="addToDocket" class="btn btn-raised btn-info" style="padding:10px 15px">Add to Docket</button>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                    <div class="boxs-body">




                    </div>
                </section>

            </div>

        <!-- </div> -->

        <div class="col-md-6" id="docketHolderMain" style="display:none">
            <section class="boxs ">
                <div class="boxs-header dvd dvd-btm">

                    <div class="boxs-body">

                        <h3 style="padding-bottom:4px; border-bottom:1px solid #ccc; display:block; margin-bottom:30px"><strong>Current Stock Docket ( <?php echo date('d-m-Y'); ?> )</strong></h3>

                        <div id="docketHolder">

                            <?php
                                # show docket if it contains any item
                                if(count($docket) > 0){
                            ?>

                                <table class="table table-striped">

                                    <thead>
                                        <tr>
                                            <th width="250">Item Name</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        foreach($docket as $docketItem){
                                        ?>

                                                <tr id="row<?php echo $docketItem->id; ?>">
                                                    <td><?php echo $docketItem->itemName; ?></td>
                                                    <td><?php echo number_format($docketItem->qty); ?></td>
                                                    <td><?php echo number_format($docketItem->price); ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-raised" style="padding:5px 10px; margin-top:-3px" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Item" onclick="deleteDocketItem('<?php echo $docketItem->id; ?>')"><i class="fa fa-remove" style="color:white"></i></button>

                                                    </td>

                                                </tr>

                                        <?php } ?>

                                    </tbody>


                                </table>

                            <?php } ?>

                        </div>

                        <br />

                        <?php
                            $display = count($docket) > 0 ? 'block' : 'none';
                        ?>

                        <div id="docketOptions" style="margin-top:30px; display:<?php echo $display; ?>">

                            <button type="button" class="btn btn-danger btn-raised waves-effect waves-light" id="clearDocketBtn" style="padding:7px 15px">Clear Docket</button>

                            <button type="button" class="btn btn-success btn-raised waves-effect waves-light" id="addItemsToStock" style="margin-left:30px; padding:7px 15px" >Add Items to Stock</button>
                        </div>


                        <br style="clear:both" /><br />

                    </div>
                </div>
            </section>
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
                                'bundles/sweetalertscripts.bundle.js',
                                'js/main.js',
                                'js/ctrl.js',
                                'js/addNewStock.js'
                            )));

?>
