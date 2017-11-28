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
if(!in_array($thisUser->get('privilege'), array(2,3,5))){
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

?>

<style>

    #responseHolder{
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
        min-height: 50px;
        min-width: 100%;
        background: #f7f7f7;
        position: absolute;
        left:0px;
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
    <div class="page page-forms-common">

        <!-- bradcome -->
        <div class="bg-light lter b-b wrapper-md mb-10">
            <div class="row">
                <div class="col-sm-6 col-xs-12">
                    <h1 class="font-thin h3 m-0">Remove Bad Item</h1>
                    <small class="text-muted">&nbsp;</small> </div>
            </div>
        </div>

        <!-- row -->
        <div class="row">
            <div class="col-md-6">
                <section class="boxs">
                    <div class="dvd dvd-btm">


                        <?php
                            if($registry->get('session')->read('formMsg')){
                                echo $registry->get('session')->read('formMsg');
                                $registry->get('session')->write('formMsg', NULL);
                            }
                        ?>

                    </div>
                    <div class="boxs-body">

                        <form class="form-group" onsubmit="getItem(); return false;">

                            <div class="form-group">
                                <input type="text" name="itemQuery" id="itemQuery" class="form-control"  placeholder="Enter Item Name or Scan Item Bar Code" autofocus  onkeyup="fetchItem(this.value)" autocomplete="off">

                                <div id="responseHolder"></div>

                            </div>

                            <br style="clear:both" />

                        </form>


                        <div id="formHolder" style="display: none;">

                            <form class="form-horizontal" role="form" method="post" action="<?php echo $baseUri; ?>/stock/removeBadItem">

                                <input type="hidden" id="codeNo" name="codeNo" class="form-control" >

                                <div id="priceHolder">

                                    <div class="form-group" id="stockQtyHolder">
                                        <label for="inputPassword3" class="col-sm-4 control-label">Qty In Stock</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="stockQty" readonly >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-4 control-label">Qty to Remove</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="qty" name="qty" onkeyup="allowNosOnly(this.value, 'qty');" required >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputPassword3" class="col-sm-4 control-label">Reason for Removal</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="reason" name="reason" required >
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="col-sm-offset-4 col-sm-8">
                                            <button name="submit" type="submit" class="btn btn-raised btn-info">Remove</button>
                                        </div>
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
                                'bundles/sweetalertscripts.bundle.js',
                                'js/main.js',
                                'js/ctrl.js',
                                'js/removeBadItems.js'
                            )));

?>
