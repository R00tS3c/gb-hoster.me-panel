<?php
$page = "sms";
include_once("connect_db.php");
header('Content-Type: charset=UTF-8');

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<?php include ("assets/php/head.php"); ?>
<?php include('style/err_script.php'); ?>
<body>
		    <div id="content">
        <div id="TOP">
            <div id="header">
                <a id="logo" href="/"></a>
<?php include ("style/login_provera.php"); ?>
	            </div>
<?php include ("style/navigacija.php"); ?>
        </div>
<div id="wraper" style="background: rgba(0,0,0,0.7); box-sizing: border-box; max-width: 1002px; color: #fff !important; margin: 0px 0;">
    <div id="ServerBox">
        <div id="server_info_infor">

            <div id="server_info_infor2">
                <!-- Server meni precice -->
                <div class="space" style="margin-top: 20px;"></div>

                <div id="ftp_container">
                    <div id="ftp_header">
						  <div id="ftp_header">
											<div id="left_header">
                            <div>
                                <img src="/img/icon/gp/gp-plugins.png" style="margin-left:10px;">
                            </div> 
						<h2 style="margin-left: 6%;margin-top: -5%;">SMS</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Platite putem SMS-a</h3>
                        <div class="space" style="margin-top: 60px;"></div>
                        </div>
                        
                    </div>              
                    </div>              
				
		<script src='https://assets.fortumo.com/fmp/fortumopay.js' type='text/javascript'></script>
<a id="fmp-button" href="#" rel="3f30f8361bda27732a80fe29b982842f/<?php echo $_SESSION['userid']; ?>"><img src="https://assets.fortumo.com/fmp/fortumopay_150x50_red.png" width="150" height="50" alt="Mobile Payments by Fortumo" border="0" /></a>


            </div>
        </div>
    </div>

    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>


</body>
</html>
