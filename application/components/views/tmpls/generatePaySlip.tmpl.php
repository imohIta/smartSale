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

    $staff = Staff::fetchAll();


?>

<!--  CONTENT  -->
<section id="content">
    <div class="page page-tables-footable">
        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Generate Play Slip ( <?php echo date('F') . ' ' .  date('Y'); ?> )</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-8">
                <section class="boxs ">
                    <div class="boxs-header dvd dvd-btm">
                        <div class="boxs-body">

                        </div>
                    </div>
                    <div class="boxs-body">

                        <?php

                            if(count($staff) == 0){

                         ?>
                             <div style="margin-bottom:150px">
                                 <h3>No Registered Staff</h3>
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
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $count = 1;
                                            foreach($staff as $key){
                                                $staff = new Staff($key);

                                        ?>
                                            <tr>
                                                <td><?php echo $count; ?></td>
                                                <td><?php echo $staff->get('name'); ?> </td>
                                                <td><?php echo $staff->get('role'); ?></td>
                                                <td>
                                                    <a href="<?php echo $baseUri; ?>/staff/generateStaffPaySlip/<?php echo $staff->get('id'); ?>"><button class="btn btn-primary btn-raised" style="padding:6px 8px; margin-top:-2px" data-toggle="tooltip" data-placement="top" title="" data-original-title="Generate Pay Slip" <i class="fa fa-print" style="color:white"></i> Generate Pay Slip</button></a>
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
                                // 'js/vendor/parsley/parsley.min.js',
                                'js/main.js'
                            )));

?>
