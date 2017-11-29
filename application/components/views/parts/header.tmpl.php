<?php
  $baseUri = $registry->get('config')->get('baseUri');
  $session = $registry->get('session');

  # get loggedIn User user object
  $thisUser = unserialize($session->read('thisUser'));

?>

<!doctype html>
<html class="no-js" lang="">

<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo $registry->get('config')->get('appTitle'); ?></title>
<link rel="icon" type="image/ico" href="<?php echo $baseUri; ?>/assets/images/favicon.png" />
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Stylesheets  -->

<?php echo $css; ?>


<?php echo $js; ?>

</head>
<body id="oakleaf" class="main_Wrapper">

    <div style="display:none" id="baseUriHolder"><?php echo $baseUri; ?></div>

<div id="wrap" class="animsition">
    <!-- HEADER Content -->
    <section id="header">
        <header class="clearfix">
            <!-- Branding -->
            <div class="branding"> <a class="brand" href="index-2.html"><span>smartSale</span></a> <a role="button" tabindex="0" class="offcanvas-toggle visible-xs-inline"><i class="fa fa-bars"></i></a> </div>
            <!-- Branding end -->

            <!-- Left-side navigation -->
            <ul class="nav-left pull-left list-unstyled list-inline">
                <li class="leftmenu-collapse"><a role="button" tabindex="0" class="collapse-leftmenu"><i class="fa fa-arrow-circle-o-left"></i></a></li>
            </ul>
            <!-- Left-side navigation end -->

            <!-- Search -->
            <div id="morphsearch" class="morphsearch">

				<span class="morphsearch-close"></span>
			</div>
            <!-- /morphsearch -->
			<div class="overlay"></div>
            <!-- Search end -->

            <!-- Right-side navigation -->
            <ul class="nav-right pull-right list-inline">

                <?php
                    if($thisUser->get('activeAcct') == 2){
                 ?>

                <li class="toggle-right-leftmenu" data-toggle="tooltip" data-placement="left" title="" data-original-title="General Settings"><a role="button" tabindex="0"><i class="fa fa-gear"></i></a></li>

                <?php } ?>

            </ul>
            <!-- Right-side navigation end -->
        </header>
    </section>
    <!--/ HEADER Content  -->
