<?php
session_start();

$fajl = "login";

include("konfiguracija.php");

$naslov = "Admin login";

if (isset($_GET['task'])) $task = mysql_real_escape_string($_GET['task']);

function AdminUlogovan()
{
	if (!empty($_SESSION['a_id']) && is_numeric($_SESSION['a_id']))
	{
		$verifikacija = mysql_query( "SELECT `username` FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'" );
		if (mysql_num_rows($verifikacija) == 1)
		{
			return TRUE;
		}
		unset($verifikacija);
	}
	return FALSE;
}

if (AdminUlogovan() == TRUE) { header("Location: index.php"); die(); }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>GB-HOSTER ADMIN LOGIN</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="../../vendors/iconfonts/simple-line-icon/css/simple-line-icons.css">
  <link rel="stylesheet" href="../../vendors/iconfonts/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="../../vendors/css/vendor.bundle.addons.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="../../css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="../../images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one">
        <div class="row w-100 mx-auto">
          <div class="col-lg-4 mx-auto">
            <div class="auto-form-wrapper">
              <form role="form" action="login_process.php" method="POST">
			  			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="return" value="<?php
		if (isset($_GET['return']))
		{
			echo htmlspecialchars($_GET['return'], ENT_QUOTES);
		}
?>" />

                <div class="form-group">
                  <label class="label">Username</label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="username" type="text" placeholder="Username">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="icon-check"></i></span>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="label">Password</label>
                  <div class="input-group">
                    <input name="sifra" type="password" class="form-control" placeholder="*********">
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="icon-check"></i></span>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <button class="btn btn-primary submit-btn btn-block">Login</button>
                </div>
                <div class="form-group d-flex justify-content-between">
                  <div class="form-check form-check-flat mt-0">
                    <label class="form-check-label">
                      <input type="checkbox" name="rememberMe" class="form-check-input" checked>
                      Keep me signed in
                    </label>
                  </div>
                  <a href="#" class="text-small forgot-password text-black">Forgot Password</a>
                </div>
              </form>
            </div>
            <ul class="auth-footer">
              <li><a href="#">Conditions</a></li>
              <li><a href="#">Help</a></li>
              <li><a href="#">Terms</a></li>
            </ul>
            <p class="footer-text text-center">Copyright © 2019 GB-HOSTER. All rights reserved.</p>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="../../vendors/js/vendor.bundle.base.js"></script>
  <script src="../../vendors/js/vendor.bundle.addons.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="../../js/template.js"></script>
  <!-- endinject -->
</body>

</html>
