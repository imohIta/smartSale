<?php

    # check if user has already logged In
    if($registry->get('session')->read('loggedIn')){
        $registry->get('uri')->redirect($registry->get('config')->get('baseUri') . '/dashboard');
    }

    $baseUri = $registry->get('config')->get('baseUri');

?>

<!doctype html>
<html class="no-js" lang="">

<head>
<meta charset="utf-8" />
<link rel="shortcut icon" href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/favicon.png" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<title><?php echo $registry->get('config')->get('appTitle'); ?></title>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />

<!-- CSS Files -->
<link href="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/css/main.css" rel="stylesheet">
</head>

<body class="signup-page">
<div class="wrapper">
  <div class="header header-filter" style="background-image: url('<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/login-bg.jpg'); background-size: cover; background-position: top center;">
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 text-center">
          <div class="card card-signup">

            <form class="form" method="post" action="<?php echo $baseUri; ?>/login/authenticate" onsubmit="login()">
              <div class="header header-primary text-center">

                <h4>LOGIN</h4>

              </div>

              <img src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/images/ss-logo.png" style="margin-top:10px" />
              <p class="help-block">make sales with ease...</p>

              <?php
                  if($registry->get('session')->read('formMsg')){
                      echo $registry->get('session')->read('formMsg');
                      $registry->get('session')->write('formMsg', NULL);
                  }
              ?>

              <div class="content">
                <div class="form-group">
                  <input type="text" name="username" class="form-control underline-input" placeholder="Username">
                </div>
                <div class="form-group">
                  <input type="password" name="pwd" placeholder="Password" class="form-control underline-input">
                </div>

              </div>

              <br /><br />

              <div class="footer text-center">
              	<input type="submit" name="submit" class="btn btn-primary btn-raised" value="Login" />
              </div>

              <br /><br />
              <!-- <a href="forgotpass.html" class="btn btn-primary btn-wd btn-lg">Forgot Password?</a> -->
            </form>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer">
      <div class="container">
        <div class="col-lg-12 text-center">
          <div class="copyright"> &copy; <?php echo date('Y') ; ?>, Developed & managed by  <a href="http://oxygyn.xyz" target="_blank"><img src="<?php echo $baseUri; ?>/assets/images/logo-light.png" alt="Oxygyn" style="width:80px; height:32px; margin-top:10px" /></a> </div>
        </div>
      </div>
    </footer>
  </div>
</div>
</body>

<!--  Vendor JavaScripts -->
<script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/bundles/libscripts.bundle.js"></script>

<!--  Custom JavaScripts  -->
<script src="<?php echo $registry->get('config')->get('baseUri'); ?>/assets/js/main.js"></script>
<!--/ custom javascripts -->


</html>
