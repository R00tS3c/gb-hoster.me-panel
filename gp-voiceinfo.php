<?php

$srw_file = "1";
$ts = "TeamSpeak3";

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

$ts_port = "10011";

require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');

$ip = $server_ip['ip'];

if($server['igra'] != "6")
	header("Location:gp-info.php?id=$server_id");

$tsAdmin = new ts3admin($ip, $ts_port);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {
	$tsAdmin->login($server['username'], $server['password']);
	$tsAdmin->selectServer($server['port']);
} else {
	$_SESSION['error'] = "Doslo je do greske.";
	header("Location: gp-voiceservers.php");
	die();
}

$ts_s_info 		= $tsAdmin->serverInfo();
if (isset($_POST['c_id']) && isset($_POST['poke_msg']) && isset($_POST['poke_true'])) {
	$Client_ID 	= $_POST['c_id'];
	$Poke_MSG 	= $_POST['poke_msg'];
	
	$poke_msg_ok = $tsAdmin->clientPoke($Client_ID, $Poke_MSG);
	
	if (!$poke_msg_ok) {
		$_SESSION['error'] = "Doslo je do greske.";
		header("Location: gp-voiceinfo.php?id=$server_id");
		die();
	} else {
		$_SESSION['info'] = "Uspesno ste izvrsili komandu.";
		header("Location: gp-voiceinfo.php?id=$server_id");
		die();
	}
}

if (isset($_POST['c_id']) && isset($_POST['kick_msg']) && isset($_POST['kick_true'])) {
	$Client_ID 	= $_POST['c_id'];
	$Kick_MSG 	= $_POST['kick_msg'];
	$kick_msg_ok = $tsAdmin->clientKick($Client_ID, 'server', $Kick_MSG);
	
	if (!$kick_msg_ok) {
		$_SESSION['error'] = "Doslo je do greske.";
		header("Location: gp-voiceinfo.php?id=$server_id");
		die();
	} else {
		$_SESSION['info'] = "Uspesno ste izvrsili komandu.";
		header("Location: gp-voiceinfo.php?id=$server_id");
		die();
	}
}

$Server_Online  = $ts_s_info['data']['virtualserver_status'];

if($Server_Online == 'online') {
	$Server_Online = "<span style='color:#54ff00;'>Online</span>"; 
} else {
	$Server_Online = "<span style='color:red;'>Server je offline.</span>";
}

$Server_Name 	= $ts_s_info['data']['virtualserver_name'];
$Server_Players = $ts_s_info['data']['virtualserver_clientsonline'].'/'.$ts_s_info['data']['virtualserver_maxclients'];

$ts_s_platform 	= $ts_s_info['data']['virtualserver_platform'];
$ts_s_version 	= $ts_s_info['data']['virtualserver_version'];
$ts_s_pass 		= $ts_s_info['data']['virtualserver_password'];

if ($ts_s_pass == '') {
	$ts_s_pass = "<span style='color:red;'>No</span>";
} else {
	$ts_s_pass = "<span style='color:#54ff00;'>Yes</span>";
}

$ts_s_autostart = $ts_s_info['data']['virtualserver_autostart'];

if ($ts_s_autostart == 1) {
	$ts_s_autostart = "<span style='color:#54ff00;'>Yes</span>";
} else {
	$ts_s_autostart = "<span style='color:red;'>No</span>";
}

if(isset($ts_s_info['data']['virtualserver_uptime'])) {
	$ts_s_uptime = $tsAdmin->convertSecondsToStrTime(($ts_s_info['data']['virtualserver_uptime']));
} else {
	$ts_s_uptime = '-';
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
                <div class="server_infoInfo" style="max-width: 350px;border:1px solid;border-color: #ba0000;margin-left:6%;padding-left:3%;">

                    <div class="SrwInfo_Info">
					                        <?php  
                            if (is_pin() == false) {
                                $provera_pin = "#pin-auth"; 
                            } else {
                                $provera_pin = "#edit_name";
                            }
                        ?> 
                        <label style="color: #bbb;font-size: 15px;margin-top: 17px;"><?php echo $jezik['28']; ?><strong style="color: #ba0000;">
                                <?php echo $server['name']; ?>
                                <button style="background:none;border:none;color:#fff;" type="button" data-toggle="modal" data-target="<?php echo $provera_pin; ?>"><span class="fa fa-edit"></span></button>
                        </strong></label>
						
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['29']; ?><strong style="color: #ba0000;">
                                <?php echo $server['istice']; ?>
                                <a href="produzi.php?id=<?php echo $server['id']; ?>" style="background:none;border:none;color:#fff;"><span class="fa fa-edit"></span></a>
                        </strong></label><br/>
							
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['119']; ?> <strong style="color: #ba0000;"><?php echo gp_igra($server['igra']); ?></strong></label>
						 <?php  
                                $location_ip = json_decode(file_get_contents("http://ip-api.com/json/".$server_ip['ip']));
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
                     </div>

<br>
                </div>
                <h5 class="pc-icon" style="float: right;margin: -300px 325px 0px 0px;font-size: 15px;"><?php echo $jezik['118']; ?></h5>
                <div class="server_infoInfo2" style="max-width: 350px;border:1px solid;border-color: #ba0000; float: right;margin-top: -26.5%;margin-right: 7%;padding-left:2%;">
                   
                    <div class="ServerInfoFTP" style="padding-top: 26px;padding-left:5px;">
					    <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['41']; ?> <strong style="color: #ba0000;font-size: 13px;"><?php echo $Server_Online; ?></strong></label><br/>
						
						<label style="color: #bbb;font-size: 15px;"><?php echo $jezik['43']; ?> <strong style="color: #ba0000;font-size: 13px;"><?php echo $Server_Name; ?></strong></label><br/>
						
						<label style="color: #bbb;font-size: 15px;"><?php echo $jezik['45']; ?> <strong style="color: #ba0000;font-size: 13px;"><?php echo $Server_Players; ?></strong></label><br/>
						
						<label style="color: #bbb;font-size: 15px;">Platform: <strong style="color: #ba0000;font-size: 13px;"><?php echo $ts_s_platform; ?></strong></label><br/>
						
						<label style="color: #bbb;font-size: 15px;">Server Vresion: <strong style="color: #ba0000;font-size: 13px;"><?php echo $ts_s_version; ?></strong></label><br/>
						
						<label style="color: #bbb;font-size: 15px;">Auto Start: <strong style="color: #ba0000;font-size: 13px;"><?php echo $ts_s_autostart; ?></strong></label><br/>
						
						<label style="color: #bbb;font-size: 15px;">UpTime: <strong style="color: #ba0000;font-size: 13px;"><?php echo $ts_s_uptime; ?></strong></label><br/>
						
                    </div>
                </div><br><br><br><br>
				<center>
				<div id="webftp" >
                                <table class="darkTable">

			                                    <tbody>

			                                        <tr>

			                                            <th>Name</th>

			                                            <th>IP</th>

			                                            <th>Action</th>

			                                        </tr>



			                                        <?php

														#get clientlist

														$clients = $tsAdmin->clientList('-uid -away -voice -times -groups -info -country -icon -ip -badges');

														

														#print clients to browser

														foreach($clients['data'] as $client) {

															$getip = $tsAdmin->clientList('-ip');

															if($client['client_type'] == '0') {

																$avatar = $tsAdmin->clientAvatar($client['client_unique_identifier']);

																?>



																	<tr>

																		<td>

																			<!--<img src="data:image/png;base64,<?php /*echo $avatar['data']; */ ?>" class="avatar_ts_tbl"> -->

																			<?php echo $client['client_nickname']; ?>

																		</td>

																		<td>

																			<img src="/img/icon/country/<?php echo $client['client_country']; ?>.png"> 

																			<?php echo $client['connection_client_ip']; ?>

																		</td>

																		<td style="width: 170px;">

						                                                	<li style="padding:0px 5px;border-radius: 0;">

						                                                		<a href="#" data-toggle="modal" data-target="#poke-auth_id_<?php echo $client['clid']; ?>">

							                                                		Poke <i class="glyphicon glyphicon-ok"></i>

							                                                	</a>

						                                                	</li>
																			
						                                                	<li style="padding:0px 5px;border-radius: 0;">

						                                                		<a href="#" data-toggle="modal" data-target="#kick-auth_id_<?php echo $client['clid']; ?>">

							                                                		Kick <i class="glyphicon glyphicon-ok"></i>

							                                                	</a>

						                                                	</li>

						                                                </td>

																	</tr>


																<?php 

															} ?>

<!-- POKE POPUP -->

<div id="poke-auth_id_<?php echo $client['clid']; ?>" class="modal fade" role="dialog">

	<div class="modal-dialog">

	    <div id="popUP"> 

	        <div class="popUP">

	            <form action="/gp-voiceinfo.php?id=<?php echo $server_id; ?>" method="POST" autocomplete="off" id="modal-poke-auth">

	                <fieldset>

	                    <h2>Poke <?php echo $client['client_nickname']; ?></h2>

	                    <ul>

	                        <li>

	                            <label>Message:</label>

	                            <input type="hidden" name="c_id" value="<?php echo $client['clid']; ?>">

	                            <input type="hidden" name="poke_true" value="true">

	                            <input type="text" name="poke_msg" value="" class="short">

	                        </li>

	                        <div class="space clear"></div>

	                        <li style="text-align:center;background:none;border:none;">

	                        	<button> <span class="fa fa-check-square-o"></span> Poke</button>

	                        </li>

	                    </ul>

	                </fieldset>

	            </form>

	        </div>        

	    </div>  

	</div>

</div>

<!-- KRAJ - POKE (POPUP) -->



<!-- POKE POPUP -->

<div id="kick-auth_id_<?php echo $client['clid']; ?>" class="modal fade" role="dialog">

	<div class="modal-dialog">

	    <div id="popUP"> 

	        <div class="popUP">

	            <form action="/gp-voiceinfo.php?id=<?php echo $server_id; ?>" method="POST" autocomplete="off" id="modal-kick-auth">

	                <fieldset>

	                    <h2>Kick <?php echo $client['client_nickname']; ?></h2>

	                    <ul>

	                        <li>

	                            <label>Message:</label>

	                            <input type="hidden" name="c_id" value="<?php echo $client['clid']; ?>">

	                            <input type="hidden" name="kick_true" value="true">

	                            <input type="text" name="kick_msg" value="" class="short">

	                        </li>

	                        <div class="space clear"></div>

	                        <li style="text-align:center;background:none;border:none;">

	                        	<button> <span class="fa fa-check-square-o"></span> Kick</button>

	                        </li>

	                    </ul>

	                </fieldset>

	            </form>

	        </div>        

	    </div>  

	</div>

</div>

<!-- KRAJ - POKE (POPUP) -->



														<?php }

													?>

			                                    </tbody>

			                                </table>
				</div></center>
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