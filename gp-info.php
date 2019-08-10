<?php

$srw_file = "1";

include 'connect_db.php';
include './fnc/fivemlib.php';

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

//LGSL - SERVER INFO
require './inc/libs/lgsl/lgsl_class.php';

$ss_ip = $server_ip['ip'];
$ss_port = $server['port'];
$info = mysql_fetch_array(mysql_query("SELECT * FROM `lgsl` WHERE ip='$ss_ip' AND q_port='$ss_port' AND c_port='$ss_port'"));

if($server['igra'] == "6")
	header("Location:gp-voiceinfo.php?id=$server_id");

if($server['igra'] == "7")
	$new_ip = ipadresa($server['id']);

if($server['igra'] == "1") { $igras = "halflife"; }
else if($server['igra'] == "2") { $igras = "samp"; }
else if($server['igra'] == "4") { $igras = "callofduty4"; }
else if($server['igra'] == "3") { $igras = "minecraft"; }
else if($server['igra'] == "5") { $igras = "mta"; }
else if($server['igra'] == "9") { $igras = "FiveM"; }

if($server['igra'] == "5") {
    $serverl = lgsl_query_live($igras, $info['ip'], NULL, $server['port']+123, NULL, 's');
} else if($server['igra'] != "7" && $server['igra'] != "9") {
    $serverl = lgsl_query_live($igras, $info['ip'], NULL, $server['port'], NULL, 's');
} else if($server['igra'] == "7") {
	$server_onli = "<span style='color:#54ff00;'>Online</span>"; 
}

if((@$serverl['b']['status'] == '1') && $server['igra'] != "7" && $server['igra'] != "9") {
    $server_onli = "<span style='color:#54ff00;'>Online</span>"; 
} 
else if(($server['startovan'] == "1") && $server['igra'] == 9 && fivemstatus($server_ip['ip'], $server["port"])==true) {
    $server_onli = "<span style='color:#54ff00;'>Online</span>"; 
    $server_play = fivemplayers($server_ip['ip'], $server["port"]).'/'.$server["slotovi"];
    }
    else if(fivemstatus($server_ip['ip'], $server["port"])==false) {
$server_onli = "<span style='color:red;'>Server je offline.</span>";
$server_play = "<span style='color:red;'>OFFLINE</span>";
    }
else {
    if (($server['startovan'] == "1") && $server['igra'] != "7") {
        $server_onli = "<span style='color:red;'>Server je offline.</span>";
    } 
}
if($server['igra'] != "6" && $server['igra'] != "9") {
	$server_mapa = @$serverl['s']['map'];
	$server_name = @$serverl['s']['name'];
	$server_play = @$serverl['s']['players'].'/'.@$serverl['s']['playersmax'];
	
	if ($server_name == "") {
		$server_name = "n/a";
	}
	if ($server_mapa == "") {
		$server_mapa = "n/a";
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

<?php include ("style/gpmenu.php"); ?>
                <div id="panelnav">
                <?php include('style/server_nav_precice.php'); ?>
                </div>
        <div id="server_info_infor">

            <div id="server_info_infor2">
                <!-- Server meni precice -->
                <div class="space"></div>

                <h5 style="margin: 1px 1px 22px 62px;font-size: 15px;"><?php echo $jezik['27']; ?></h5>
                <div class="server_infoInfo" style="max-width: 380px;border:1px solid;border-color: #ba0000;margin-left:6%;padding-left:3%;">

                    <div class="SrwInfo_Info">
					                        <?php  
                            if (is_pin() == false) {
                                $provera_pin = "#pin-auth"; 
                            } else {
                                $provera_pin = "#edit_name";
                            }
                        ?> 
                        <label style="color: #bbb;font-size: 15px;">
                            <br>
                            <?php echo $jezik['28']; ?><strong style="color: #ba0000;">
                                <?php echo $server['name']; ?>
                                <button style="background:none;border:none;color:#fff;" type="button" data-toggle="modal" data-target="<?php echo $provera_pin; ?>"><span class="fa fa-edit"></span></button>
                        </strong></label>
						
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['29']; ?><strong style="color: #ba0000;">
                                <?php echo $server['istice']; ?>
                        </strong></label><br/>
							
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['30']; ?> <strong style="color: #ba0000;"><?php echo gp_igra($server['igra']); ?></strong></label>
						 <?php  
                                $location_ip = json_decode(file_get_contents("http://ip-api.com/json/".$ss_ip));
                                $ip_gp_lokacija = $location_ip->countryCode;
                                $ip_gp_loc_name = $location_ip->country;
                            ?>
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['31']; ?><strong style="color: #ba0000;" title="<?php echo $ip_gp_loc_name; ?>" data-toggle="tooltip" data-placement="right">
                                <?php echo $ip_gp_lokacija; ?> 
                                <i class="fa fa-chevron-right" style="font-size: 15px;"></i>
                                <img src="img/icon/country/<?php echo $ip_gp_lokacija; ?>.png">
                            </strong></label><br/>
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['32']; ?><strong style="color: #ba0000;"><?php echo $server_ip['ip'].':'.$server['port']; ?></strong></label><br/>
                        
						
						<label style="color: #bbb;font-size: 15px;"><?php echo $jezik['47']; ?><strong style="color: #ba0000;"><?php echo mod_ime($server['mod']); ?></strong></label><br/>
                        
                        <?php
                            $serverStatus = $server['status'];  
                            if ($serverStatus == "Aktivan") {
                                $serverStatus = "<span style='color: #54ff00;'> Aktivan </span>";
                            } else if($serverStatus == "Suspendovan") {
                                $serverStatus = "<span style='color: #ffd800;'> Suspendovan </span>";
                            } else {
                                $serverStatus = "<span style='color: red;'> Neaktivan </span>";
                            }
                        ?> 
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['33']; ?><strong style="color: #ba0000;"><?php echo $serverStatus; ?></strong></label>
                        <br>
                        <br>
                     </div>
                </div>
                <h5 class="pc-icon" style="float: right;margin: -316px 325px 0px 0px;font-size: 15px;"><?php echo $jezik['34']; ?></h5>
                <div class="server_infoInfo2" style="max-width: 350px;border:1px solid;border-color: #ba0000; float: right;margin-top: -28.3%;margin-right: 7%;padding-left:2%;width: 350px;">
                   
                    <div class="ServerInfoFTP" style="padding-left:5px;">
                        <br>
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['35']; ?> <strong style="color: #ba0000;font-size: 13px;"><?php echo $server_ip['ip']; ?></strong></label><br/>

                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['36']; ?> <strong style="color: #ba0000;font-size: 13px;">21</strong></label><br/>

                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['37']; ?> <strong style="color: #ba0000;font-size: 13px;"><?php echo $server['username']; ?></strong></label>
                        <br/>

                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['38']; ?>                             <strong style="color: #ba0000;font-size: 13px;">
                                <?php if (is_pin() == false) { ?>
                                   <?php echo $jezik['39']; ?>
                                   <i class="fa fa-chevron-right" style="font-size: 15px;"></i>
                                                               <?php if (is_pin() == false) { ?>
                            <a style="cursor: pointer;" type="button" data-toggle="modal" data-target="#pin-auth"><?php echo $jezik['40']; ?></a>
                            <?php } ?>
                                <?php } else { echo $server['password'];  ?>
                                                            <a style="cursor: pointer;" type="button" data-toggle="modal" data-target="#ftp-pw">Promeni FTP sifru</a> <?php } ?>
                            </strong></label>
                    
                    </div>
                        <br>
                </div>
                <h5 class="pc-icon" style="float: right;margin: -110px 285px 0px 0px;font-size: 15px;">
                    Server Status <button style="background: none; border:none;"><i class="fa fa-refresh"></i></button></h5>
                <div class="server_infoInfo3" style="max-width: 350px;border: 1px solid;border-color: #ba0000;margin-left: 5%;padding-left: 2%;float: right;width: 350px;margin-right: 7%;margin-top:-7%;">
                                <br>
                    <div class="ServerInfoFTP" style="line-height: 30px;">
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['41']; ?>
                        <span><strong style="color: #ba0000;"><?php echo $server_onli; ?></strong></span></label>
                        <?php if ($server['startovan'] == "1") {
                            if (@$serverl['b']['status'] == '0' && $server['igra'] != 9) { ?>
                                <label style="color: #bbb;font-size: 15px;">Moguce resenje:</label>
                                <span><strong style="color: #ba0000;">Izbacite zadnji plugin koji ste dodali.</strong></span> 
                        <?php } } ?> 
                        <?php                         if($server['igra'] != "6" && $server['igra'] != "9") { ?>
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['43']; ?>


                        <span><strong style="color: #ba0000;"><?php echo $server_name; ?></strong></span> </label>
                        
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['44']; ?>
                        <span><strong style="color: #ba0000;"><?php echo $server_mapa; ?></strong></span></label><?php } ?>
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['45']; ?> 
                        <span><strong style="color: #ba0000;"><?php echo $server_play; ?></strong></span></label> 
                        <?php                         if($server['igra'] != "6" && $server['igra'] != "9") { ?>
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['46']; ?>
                        <span><strong style="color: #ba0000;"><?php echo $server['rank']; ?></strong></span></label>
                        <?php } ?>
                    </div>
                    <br>
                </div>
			<?php	if($server['igra'] != "6" && $server['igra'] != "9") { ?>

                <div class="grafik" style="margin: 50px 0px 0px 60px;">
					 <h5 class="server-activity" style="color:#fff;font-size: 18px;">Banner by GameTracker.xyz</h5>
                     <a href="https://www.gametracker.xyz/server_info/<?php echo $server_ip['ip'];?>:<?php echo $server['port'];?>"><img style="background: transparent url(//i.imgur.com/iOLR4Iu.gif) center no-repeat;width: 45%;" src="http://gb-hoster.me/api_baner.php?ip=<?php echo $server_ip['ip'];?>&port=<?php echo $server['port'];?>" alt="GRAFIK" class="grafik_img"></a>
				</div>
				<?php } ?>
                <!-- server ftp precice -->
                <?php include('style/server_precice.php'); ?>
                <div class="space" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>
    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>