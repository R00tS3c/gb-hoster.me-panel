<?php

$srw_file = "1";

include 'connect_db.php';

if (is_login() == false) {
    $_SESSION['error'] = "Niste logirani!";
    header("Location: /home");
    die();
} else {
    $server_id = $_GET['id'];
    $proveri_server = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));

    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));
    $server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));

    if (!$proveri_server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemate ovlaščenje za isti.";
        header("Location: /gp-home.php");
        die();
    }
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

            <div id="server_info_infor2">
                <!-- Server meni precice -->
                <div class="space" style="margin-top: 20px;"></div>

                <div id="ftp_container">
                    <div id="ftp_header" style="margin: 0px 0px 0px 30px;">
											<div id="left_header">
                            <div>
                                <img src="/img/icon/gp/gp-plugins.png" style="margin-left:10px;">
                            </div> 
						<h2 style="margin-left: 7%;margin-top: -4%;">Modovi</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 7%;">Ovde mozete instalirati ili obrisati modove sa vaseg servera.</h3>
                        <div class="space" style="margin-top: 60px;"></div>
                        </div>
                        
                    </div>              
                    <div id="plugin_body" style="display: block;padding: 10px;*margin-bottom: 10px;">
                        <?php  
                            $gp_mods = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '$server[igra]' ORDER BY `ime`");

                            while($row = mysql_fetch_array($gp_mods)) {

                                $mod_id = htmlspecialchars(addslashes($row['id']));
                                $ime = htmlspecialchars(addslashes($row['ime']));
                                $opis = htmlspecialchars(addslashes($row['opis']));
                                $mod_putanja = htmlspecialchars(addslashes($row['putanja']));
								
								if ($server['mod'] == $mod_id) {
									$action = "gp-mods.php?id=$server_id";
									$background = ";background: #54ff00;";
									$button = "INSTALIRAN";
								}
								
								if ($server['mod'] != $mod_id) {
									$action = "process.php?task=promeni_mod";
									$background = ";";
									$button = "INSTALIRAJ";
								}
								?>
								<li style="border: 1px solid #8f8f8f;background: #ba00003d;list-style-type: none;max-height: 300px;padding: 11px 0px 0px 0px;margin-bottom: 10px;">
									<p><strong style="font-size: 15px;"><?php echo $ime; ?></strong></p>
									
                                    <p style="width: 85%"><?php echo nl2br($opis); ?></p>
                                    <?php
                                        $mmod_token = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                                        $_SESSION['mmod_token'] = $mmod_token;
                                    ?>

									<form id="form<?php echo $mod_id; ?>" style="" action="<?php echo $action; ?>" method="POST">
										<input hidden type="text" name="mod_id" value="<?php echo $mod_id; ?>">
										<input hidden type="text" name="server_id" value="<?php echo $server['id']; ?>">
                                        <input hidden type="text" name="mmod_token" value="<?php echo $mmod_token; ?>">
										<?php if (is_pin() == false) { ?>
											<a href="javascript:{}"><div class="modbutton" style="margin: 1% 88%<?php echo $background; ?>" type="button" data-toggle="modal" data-target="#pin-auth" ><?php echo $button; ?></div></a>
										<?php } else { ?>
											<a href="javascript:{}" onclick="document.getElementById('form<?php echo $mod_id; ?>').submit();"><div class="modbutton" style="margin: 1% 88%<?php echo $background; ?>" ><?php echo $button; ?></div></a>
										<?php } ?>
									</form> 
								</li>
                        <?php } ?>
                    </div>
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