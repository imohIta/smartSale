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
    if(!in_array($thisUser->get('activeAcct'), array(2,3,4,5))){
        $registry->get('uri')->redirect();
    }

    $staffData = null;

    if($session->read('staffData')){
        $staffData = unserialize($session->read('staffData'));
        $session->write('staffData', null);
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
                    <h1 class="font-thin h3 m-0">Edit Staff Profile</h1>
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

                        <form class="form-inline" role="form" action="<?php echo $baseUri; ?>/staff/editProfile" method="post">

                            <h4 style="margin-left:30%;">Select Staff</h4>

                            <div class="form-group" >

                                <select class="form-control mb-10 col-md-offset-5" name="staffId" style="width:220px" required>

                                    <?php
                                        foreach(Staff::fetchAll() as $key){

                                            $staff = new Staff($key)
                                    ?>
                                    <option value="<?php echo $staff->get('id'); ?>"><?php echo $staff->get('name'); ?></option>
                                    <?php } ?>

                                </select>
                            </div>

                            <input type="submit" name="submit" class="btn btn-raised btn-primary" style="margin-left:15px; padding:10px" value="Fetch Data" >
                        </form>

                        <hr />

                        <?php if(!is_null($staffData)){ ?>

                        <div style="display:block">

                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/staff/editProfile">

                                <input type="hidden" name="id" value="<?php echo $staff->get('id'); ?>" />

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-3 control-label">Staff Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control"  value="<?php echo $staff->get('name'); ?>" >
                                        <!-- <p class="help-block mb-0">Example block-level help text here.</p> -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Phone Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="phone" id="phone" onKeyUp="allowNosOnly(this.value, 'phone')" value="<?php echo $staff->get('phone'); ?>" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Residential Address</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="address" value="<?php echo $staff->get('address'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Gender</label>
                                    <div class="col-sm-8">

                                        <select class="form-control" name="gender" style="width:120px">
                                            <option value="F" <?php if($staff->get('gender') == 'F'){ echo 'selected'; } ?>>Female</option>
                                            <option value="M" <?php if($staff->get('gender') == 'M'){ echo 'selected'; } ?>>Male</option>
                                        </select>
                                    </div>
                                </div>

                                <hr />

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Department</label>
                                    <div class="col-sm-8">
                                        <select class="form-control mb-10" name="dept" style="width:120px" required>
                                                    <?php
                                                        $privileges = $registry->get('db')->query('select * from privileges', array(), true);
                                                        foreach ($privileges as $privilege) {
                                                            # code...
                                                            if($privilege->id != 1 && $privilege->id != 2){

                                                                $selected = $privilege->id == $staff->get('dept') ? 'selected' : '';
                                                    ?>
                                                            <option value="<?php echo $privilege->id; ?>" <?php echo $selected; ?>><?php echo $privilege->privilege; ?></option>
                                                    <?php } } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputPassword3" class="col-sm-3 control-label">Salary</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="salary" name="salary" onKeyUp="allowNosOnly(this.value, 'salary')" value="<?php echo $staff->get('salary'); ?>">
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-10">
                                        <button name="submit" type="submit" class="btn btn-raised btn-info">Submit</button>
                                    </div>
                                </div>
                            </form>

                        </div>

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
                                'bundles/bootstrapscripts.bundle.js',
                                'js/vendor/summernote/summernote.min.js',
                                'js/main.js',
                                'js/ctrl.js'
                            )));

?>
