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

    # fetch incomig cash
    $incomingCash = $registry->get('db')->query('select * from incomingcash order by id desc', array(), true);

?>

<!--  CONTENT  -->
<section id="content">
    <div class="page page-tables-footable">
        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Incoming Cash</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-8">
                <section class="boxs ">

                    <div class="boxs-body">

                        <?php

                                if(count($incomingCash) == 0){

                         ?>
                         <div style="margin-bottom:150px">
                             <h3>No recorded incoming cash</h3>
                        </div>

                         <?php }else{ ?>


                        <div class="form-group pull-right">
                            <label for="filter" style="padding-top: 5px; font-size:14px"><strong>Search Table:</strong></label>
                            <input id="filter"  style="font-size:14px; padding-left:7px" type="text" class="form-control rounded input-sm w-md mb-10 inline-block"/>
                        </div>
                        <table id="searchTextResults" data-filter="#filter" data-page-size="20" class="footable table table-custom">
                            <thead>
                                <tr>

                                    <th>Date</th>
                                    <th>Source</th>
                                    <th>Amount</th>
                                    <th>Added By</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $count = 1;
                                    foreach($incomingCash as $ic){
                                      $user = new AppUser($ic->staffId);
                                ?>
                                    <tr>
                                        <td><?php echo $ic->date; ?></td>
                                        <td><?php echo $ic->source ?></td>
                                        <td><?php echo number_format($ic->amount); ?></td>
                                        <td><?php echo $user->get('name'); ?></td>
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
                                'js/main.js'
                            )));

?>
