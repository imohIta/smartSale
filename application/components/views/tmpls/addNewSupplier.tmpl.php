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
                    <h1 class="font-thin h3 m-0">Add New Supplier</h1>
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
                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/supplier/addNew">

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Supplier Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="name" class="form-control"  placeholder="">
                                    <!-- <p class="help-block mb-0">Example block-level help text here.</p> -->
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Contact Address</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="address" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Phone Number</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="phone" id="phone" onKeyUp="allowNosOnly(this.value, 'phone')" >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">Email Address</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" name="email" >
                                </div>
                            </div>



                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-10">
                                    <button name="submit" type="submit" class="btn btn-raised btn-info">Submit</button>
                                </div>
                            </div>
                        </form>
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
                                'js/main.js'
                            )));

?>
