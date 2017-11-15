<?php

    $baseUri = $registry->get('config')->get('baseUri');
    $session = $registry->get('session');
    $session->write('hasTable', true);


      # check if user is logged in
      if(!$session->read('loggedIn')){
          $registry->get('uri')->redirect();
      }

    # get loggedIn User user object
    $thisUser = unserialize($session->read('thisUser'));

    # check user privilege
    if(!in_array($thisUser->get('activeAcct'), array(2, 3))){
        $registry->get('uri')->redirect();
    }

    # fetch all supplier
    $suppliers = $registry->get('db')->query('select * from suppliers', array(), true);


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/footable/css/footable.core.min.css',
        'css/main.css'
    )));

    #include Sidebar
    $registry->get('includer')->render('sidebar', array());

?>


<!--  CONTENT  -->
<section id="content">
    <div class="page page-forms-common">

        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Supplier Purchase History</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-8">
                <section class="boxs">

                    <div class="boxs-body">

                        <form class="form-horizontal" role="form" >

                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Select Supplier</label>
                                <div class="col-sm-8">
                                    <select class="form-control mb-10" onchange="fetchPurchaseHistory(this.value)">
                                                <option value="0"></option>
                                                <?php
                                                    foreach ($suppliers as $supplier) {
                                                        # code...
                                                ?>
                                                    <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                                                <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </form>

                        <hr />

                        <div id="purchaseHolder" style="visibility:hidden">


                        </div>

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
                                'js/main.js',
                                'js/ctrl.js',
                                'js/supplier.js'
                            )));

?>
