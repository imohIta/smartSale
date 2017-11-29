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
    if(!in_array($thisUser->get('activeAcct'), array(2,3))){
        $registry->get('uri')->redirect();
    }

    # fetch perfume brands
    $perfumeBrands = $registry->get('db')->query('select * from perfumebrands', array(), true);

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


<!--  CONTENT  -->
<section id="content">
    <div class="page page-forms-common">

        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Add Perfume Brand</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-6">
                <section class="boxs">

                    <?php
                        if($registry->get('session')->read('formMsg')){
                            echo $registry->get('session')->read('formMsg');
                            $registry->get('session')->write('formMsg', NULL);
                        }
                    ?>


                    <div class="boxs-body">

                        <br /><br />

                        <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/stock/addBrand">

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 control-label">Brand Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" class="form-control"  placeholder="">
                                    <!-- <p class="help-block mb-0">Example block-level help text here.</p> -->
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button name="submit" type="submit" class="btn btn-raised btn-info">Submit</button>
                                </div>
                            </div>
                        </form>

                        <br /><br /><br />

                    </div>
                </section>
            </div>


            <div class="col-md-6">
                <section class="boxs">
                    <div class="boxs-header dvd dvd-btm">
                        <h3>Brands</h3>
                    </div>
                    <div class="boxs-body">
                        <?php
                            if(count($perfumeBrands) == 0){
                         ?>
                         <div style="margin-bottom:150px">
                             <h3>No Registered Perfume Brand</h3>
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
                                    <th>Brand Name</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $count = 1;
                                    foreach($perfumeBrands as $brand){
                                ?>
                                    <tr id="brand<?php echo $brand->id; ?>">
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $brand->name; ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-raised" style="padding:5px 10px; margin-top:-3px; margin-left:15px" title="Delete Brand" onclick="confirmDeleteBrand('<?php echo $brand->id; ?>')"><i class="fa fa-remove" style="color:white"></i></button>
                                        </td>
                                    </tr>

                                <?php $count++; } ?>


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
                                'bundles/sweetalertscripts.bundle.js',
                                'js/main.js',
                                'js/ctrl.js',
                                'js/stock.js'
                            )));

?>
