<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', null);


  # check if user is logged in
  if(!$session->read('loggedIn')){
      $registry->get('uri')->redirect();
  }

    $thisUser = unserialize($session->read('thisUser'));

    # check user privilege
    if(!in_array($thisUser->get('activeAcct'), array(2,3))){
        $registry->get('uri')->redirect();
    }


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/sweetalert/sweetalert2.css',
        'css/main.css'
    )));

    #include Sidebar
    $registry->get('includer')->render('sidebar', array());

    # fetch sales docket
    $salesDocket = Sales::fetchDocket(date('Y-m-d'), $thisUser->get('id'));

?>

<style>

    #responseHolder{
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
        min-height: 50px;
        min-width: 320px;
        background: #f7f7f7;
        position: absolute;
        left:14px;
        top:60px;
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
                    <h1 class="font-thin h3 m-0">Stock Card</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">


            <div class="col-md-8">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">

                        <?php
                            if($registry->get('session')->read('formMsg')){
                                echo $registry->get('session')->read('formMsg');
                                $registry->get('session')->write('formMsg', NULL);
                            }
                        ?>

                        <div class="boxs-body">

                            <form class="form-group" onsubmit="getItem(); return false;"role="form" id="form1">
								<div class="form-group col-md-6">
									<label for="username">Code No </label>
									<input type="text" name="" id="codeNo" class="form-control" autofocus placeholder="Enter Item Code No or Scan Item Bar Code" onkeyup="mirrorValue(this.value, 'codeNo'); getItem();" autocomplete="off">
								</div>
                            </form>

                            <form name="form1" role="form" id="form1" method="post" action="<?php echo $baseUri; ?>/stock/stockCard">

                                <input name="codeNo" type="hidden" id="codeNoHidden" />

								<div class="form-group col-md-6">
									<label for="email">Item Name </label>
									<input type="text" id="itemName" name="itemName" class="form-control"  placeholder="Enter Item Name to Search" class="form-control" style="margin-top:-4px;" onkeyup="fetchItem(this.value, false)" autocomplete="off">

                                    <div id="responseHolder"></div>
								</div>

								<div class="row">
									<div class="form-group col-md-3" style="margin-left:15px">
										<label for="password">Cost Price</label>
										<input type="text" name="costPrice" id="costPrice" class="form-control" required onkeyup="allowNosOnly(this.value, 'costPrice')">
									</div>
									<div class="form-group col-md-3">
										<label for="passwordConfirm">Wholesale Price </label>
										<input type="text" name="wholesalePrice" id="wholesalePrice" class="form-control" required onkeyup="allowNosOnly(this.value, 'wholesalePrice')">
									</div>
									<div class="form-group col-md-3">
										<label for="phone">Retail Price </label>
										<input type="text" name="retailPrice" id="retailPrice" class="form-control" required onkeyup="allowNosOnly(this.value, 'wholesalePrice')" >
									</div>
								</div>


                                <div class="row">
									<div class="form-group col-md-3" style="margin-left:15px">
										<label for="password">Group</label>
                                        <select name="groupId" id="groupId" class="form-control mb-10" required>

                                            <?php
                                                # fetch all stock groups
                                                foreach (StockItem::fetchCategories() as $value) {
                                                    # code...

                                             ?>

											<option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                            <?php } ?>
										</select>
									</div>

                                    <div class="form-group col-md-3" style="margin-left:15px">
										<label for="password">Brand</label>
                                        <select name="brandId" id="brandId" class="form-control mb-10" required>

                                            <?php
                                                # fetch all perfumeBrands
                                                $perfumeBrands = $registry->get('db')->query('select * from perfumeBrands', array(), true);
                                                foreach ($perfumeBrands as $brand) {
                                                    # code...
                                             ?>

											<option value="<?php echo $brand->id; ?>"><?php echo $brand->name; ?></option>
                                            <?php } ?>
										</select>
									</div>

									<div class="form-group col-md-3">
										<label for="passwordConfirm">Tax ( =N= )</label>
										<input type="text" name="tax" id="tax" class="form-control" value="0" required onkeyup="allowNosOnly(this.value, 'tax')">
									</div>

								</div>

                                <br />


                                <button type="submit" name="submit" class="btn btn-raised btn-info" style="padding:10px 30px; margin-left:15px">Submit</button>


							</form>


                        </div>
                    </div>

                </section>

            </div>



            <div class="col-md-4" id="infoHolder" style="display: none">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">

                        <div class="boxs-body">

                            <br /><br />

                            <div class="row col-md-12">
                                <div class="pull-left"><h3>Qty in Stock : </h3></div>
                                <div class="pull-right">
                                    <h3 id="qtyInStock"></h3>
                                </div>
                            </div>

                            <br /><hr /><br />

                            <div class="row col-md-12">
                                <div class="pull-left"><h3>Last Purchase Date : </h3></div>
                                <div class="pull-right">
                                    <h3 id="lastPurchaseDate"></h3>
                                </div>
                            </div>

                            <br /><hr /><br />

                            <div class="row col-md-12">
                                <div class="pull-left"><h3>Last Sold Date : </h3></div>
                                <div class="pull-right">
                                    <h3 id="lastSoldDate"></h3>
                                </div>
                            </div>

                            <br /><hr /><br />


                            <div class="row col-md-12">
                                <div class="pull-left"><h3>Last Cost Price : </h3></div>
                                <div class="pull-right">
                                    <h3 id="lastCostPrice"></h3>
                                </div>

                            </div>

                            <br /><hr /><br />



                            <br style="clear:both" />

                        </div>
                    </div>
                </section>
            </div>

        <!-- </div> -->



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
                                'js/stock.js'
                            )));

?>
