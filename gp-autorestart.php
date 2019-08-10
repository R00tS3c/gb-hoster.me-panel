<?php
include 'connect_db.php';

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $server_id = $_GET['id'];
    $proveri_server = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));

    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));
    
    if (!$proveri_server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemas ovlascenje za isti.";
        header("Location: /gp-home.php");
        die();
    }

    $info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
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
<div id="wraper" style="/*background: rgba(0,0,0,0.7);*/ box-sizing: border-box; max-width: 1002px; color: #fff !important; /*margin: 0px 0;*/">
    <div id="ServerBox" style="border: 1px solid #ba0000; background: #000000b5;">
                
<div id="gamenav">
            <ul>
                <li><a href="gp-home.php">News</a></li>
                <li><a href="gp-servers.php">Servers</a></li>
                <li><a href="gp-voiceservers.php">Voice Server</a></li>
                <li><a href="gp-settings.php">Settings</a></li>
                <li><a href="gp-iplog.php">IP Log</a></li>
                <li><a href="client_process.php?task=logout">Logout</a></li> 
            </ul>
</div>
				<div id="panelnav">
                <?php include('style/server_nav_precice.php'); ?>
                </div>

        <div id="server_info_infor">
<div class="space" style="margin-top: 20px;"></div>
            <div id="server_info_infor2">
                <div id="ftp_container">
					 <div id="ftp_header" style="margin: 0px 0px 0px 30px;">
											<div id="left_header">
                            <div>
                                <img src="/img/icon/gp/gp-config.png" style="margin-left:10px;">
                            </div> 
						<h2 style="margin-left: 7%;margin-top: -4%;">Autorestart</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 7%;">Ovde mozete podesiti vreme kada zelite da vam se server automatski restartuje svaki dan</h3>
                        <div class="space" style="margin-top: 30px;"></div>
                        </div>
                        
                    </div>            
                        <form id="form" action="process.php?task=auto_rs_edit" method="POST">
                            <input type="hidden" name="server_id" value="<?php echo $server['id']; ?>" />
                             <div id="select">
                            <select name="autorestart" style="width: 160px;background-color: #ba0000;margin: 1px 4px 15px 30px;text-overflow: clip;">
                                <option value="-1">DISABLED</option>
                                <option value="00">00:00</option>
                                <option value="01">01:00</option>
                                <option value="02">02:00</option>
                                <option value="03">03:00</option>
                                <option value="04">04:00</option>
                                <option value="05">05:00</option>
                                <option value="06">06:00</option>
                                <option value="07">07:00</option>
                                <option value="08">08:00</option>
                                <option value="09">09:00</option>
                                <option value="10">10:00</option>
                                <option value="11">11:00</option>
                                <option value="12">12:00</option>
                                <option value="13">13:00</option>
                                <option value="14">14:00</option>
                                <option value="15">15:00</option>
                                <option value="16">16:00</option>
                                <option value="17">17:00</option>
                                <option value="18">18:00</option>
                                <option value="19">19:00</option>
                                <option value="20">20:00</option>
                                <option value="21">21:00</option>
                                <option value="22">22:00</option>
                                <option value="23">23:00</option>
                            </select>
                            </div>
                            <a href="javascript:{}" onclick="document.getElementById('form').submit();"><div class="divbutton" style="margin-top: -5%;margin-right: 2%;float: right;">SACUVAJ</div></a>
                        </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>