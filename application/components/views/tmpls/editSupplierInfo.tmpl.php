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

    # check user privilege
    if(!in_array($thisUser->get('activeAcct'), array(2))){
        $registry->get('uri')->redirect();
    }

    # fetch all supplier
    $suppliers = $registry->get('db')->query('select * from suppliers', array(), true);


    #include header
    $registry->get('includer')->render('header', array('css' => array(
        'css/vendor/animsition.min.css',
        'js/vendor/chosen/chosen.css',
        'js/vendor/summernote/summernote.css',
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
                    <h1 class="font-thin h3 m-0">Edit Supplier Information</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-6">
                <section class="boxs">
                    <div class="boxs-body">


                        <?php
                            if($registry->get('session')->read('formMsg')){
                                echo $registry->get('session')->read('formMsg');
                                $registry->get('session')->write('formMsg', NULL);
                            }
                        ?>

                    </div>
                    <div class="boxs-body">

                        <form class="form-horizontal" role="form" >

                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Select Supplier</label>
                                <div class="col-sm-8">
                                    <select class="form-control mb-10" onchange="setSupplier(this.value)">
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

                        <div id="formHolder" style="visibility:hidden">

                            <form class="form-horizontal"  method="post" action="<?php echo $baseUri; ?>/supplier/editInfo">

                                <input type="hidden" id="id" name="id" />

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Supplier Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="name" name="name" class="form-control"  placeholder="">
                                        <!-- <p class="help-block mb-0">Example block-level help text here.</p> -->
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Contact Address</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="address" id="address" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="col-sm-3 control-label">Phone Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="phone" id="phoneNo" onKeyUp="allowNosOnly(this.value, 'phoneNo')">
                                    </div>
                                </div>



                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Email Address</label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" name="email" id="email" >
                                    </div>
                                </div>



                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-10">

                                        <input  name="edit" type="submit" class="btn btn-raised btn-info" value="Edit" />

                                        <button name="delete" type="submit" class="btn btn-raised btn-danger" onclick="return confirm('Are you Sure you want to delete this supplier?')" style="margin-left:100px">Delete</button>

                                    </div>
                                </div>
                            </form>

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
                                'bundles/bootstrapscripts.bundle.js',
                                'js/vendor/summernote/summernote.min.js',
                                'js/main.js',
                                'js/ctrl.js',
                                'js/supplier.js'
                            )));

?>
