<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled servera";
$fajl = "ts-pocetna";
$ts_srv = "1";
$ts = "TeamSpeak";

$ts_port = "10011";

if(logged_in()) {
	
} else {
	header("Location: ./login");
}

if(empty($_GET['id']) or !is_numeric($_GET['id'])) 
{
	header("Location: index.php");
}

$serverid = mysql_real_escape_string($_GET['id']);

if(query_numrows("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'") == 0)
{
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Taj server ne postoji.";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$server['user_id']."'");

if(CheckBoxStatus($serverid) == "Offline") {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Mašina je OFFLINE!";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

if($server['igra'] != "6")
	header("Location:srv-pocetna.php?id=$serverid");

$igra = "<img src='./assets/img/game-ts.png' /> TeamSpeak3";

$ip = ipadresabezportaap($server['id']);

require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');
$tsAdmin = new ts3admin($ip, $ts_port);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {
	$tsAdmin->login($server['username'], $server['password']);
	$tsAdmin->selectServer($server['port']);
} else {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Doslo je do greske.";
	$_SESSION['msg-type'] = 'error';
	header("Location: serveri.php?view=all");
	die();
}

$ts_s_info 		= $tsAdmin->serverInfo();
if (isset($_POST['c_id']) && isset($_POST['poke_msg']) && isset($_POST['poke_true'])) {
	$Client_ID 	= $_POST['c_id'];
	$Poke_MSG 	= $_POST['poke_msg'];
	
	$poke_msg_ok = $tsAdmin->clientPoke($Client_ID, $Poke_MSG);
	
	if (!$poke_msg_ok) {
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = "Doslo je do greske.";
		$_SESSION['msg-type'] = 'error';
		header("Location: ts-pocetna.php?id=$serverid");
		die();
	} else {
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Uspesno ste izvrsili komandu..";
		$_SESSION['msg-type'] = 'success';
		header("Location: ts-pocetna.php?id=$serverid");
		die();
	}
}

if (isset($_POST['c_id']) && isset($_POST['kick_msg']) && isset($_POST['kick_true'])) {
	$Client_ID 	= $_POST['c_id'];
	$Kick_MSG 	= $_POST['kick_msg'];
	$kick_msg_ok = $tsAdmin->clientKick($Client_ID, 'server', $Kick_MSG);
	
	if (!$kick_msg_ok) {
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = "Doslo je do greske.";
		$_SESSION['msg-type'] = 'error';
		header("Location: ts-pocetna.php?id=$serverid");
		die();
	} else {
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Uspesno ste izvrsili komandu..";
		$_SESSION['msg-type'] = 'success';
		header("Location: ts-pocetna.php?id=$serverid");
		die();
	}
}

$Server_Online  = $ts_s_info['data']['virtualserver_status'];

if($Server_Online == 'online') {
	$Server_Online = "<span style='color:#54ff00;'>Online</span>"; 
} else {
	if ($server['startovan'] == "1") {
		$Server_Online = "<span style='color:red;'>Server je offline.</span>";
	} else {
		$Server_Online = "<span style='color:red;'>Server je stopiran u panelu.</span>";
	}
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

$novac = explode(" ", $server['cena']);

function drzava_valuta($d)
{
	if($d == "srb") $drzava = "din";
	else if($d == "hr") $drzava = "kn";
	else if($d == "bih") $drzava = "km";
	else if($d == "mk") $drzava = "den";
	else if($d == "cg" || $d == "other") $drzava = "�";
	return $drzava;
}

function price_by_slot($clientid, $igra, $srvid) {
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '{$srvid}'");
	
	$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$clientid}'");
	
	$cenaslota = query_fetch_assoc("SELECT `cena` FROM `modovi` WHERE `igra` = '{$igra}'");
	$cenaslota = explode("|", $cenaslota['cena']);
	
	if($klijent['zemlja'] == "srb") $cena = $cenaslota[0];
	else if($klijent['zemlja'] == "hr") $cena = $cenaslota[3];
	else if($klijent['zemlja'] == "bih") $cena = $cenaslota[4];
	else if($klijent['zemlja'] == "mk") $cena = $cenaslota[2];
	else if($klijent['zemlja'] == "cg") $cena = $cenaslota[1];
	else if($klijent['zemlja'] == "other") $cena = $cenaslota[1];
	if($igra == 7)
		$server['slotovi'] = 1;
	
	$out = round($cena * $server['slotovi'], 2);
	$out = number_format($out, 2);
	
	$out = $out." ".drzava_valuta($klijent['zemlja']); 
	return $out;
}

$cena = price_by_slot($klijent['klijentid'], $server['igra'], $server['id'] );

include("assets/header.php");

?>

	<div class="row">
		<div class="span8">				
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3>Server info</h3>
				</div>
				
				<div class="widget-content">
					<table>
						<tr>
							<th style="width: 300px;"></th>
							<th style="width: 300px;"></th>
						</tr>
						<tr style="vertical-align: top;">
							<td>
								<strong>INFO</strong>
								<p><i class="icon-th-large"></i> Ime servera: <?php echo $server['name']; ?></p>
								<p><i class="icon-calendar"></i> Vazi do: <?php echo srv_istekao($server['id']); ?></p>
								<p><i class="icon-gamepad"></i> Igra: <?php echo $igra; ?></p>
								<p><i class="icon-bar-chart"></i> Ip:port: <?php echo $boxip['ip'].":".$server['port']; ?></p>
								<p><i class="icon-tasks"></i> Masina: <a href="box.php?id=<?php echo $box['boxid']; ?>"><?php echo $box['name']; ?></a></p>
							</td>
							<td>
								<strong>INFO</strong>
								<p><i class="icon-th"></i> Slotova: <?php echo $server['slotovi']; ?></p>
								<p><i class="icon-th-large"></i> Status: <?php echo srv_status($server['status']); ?></p>
								<p><i class="icon-edit-sign"></i> Cena: <?php echo $cena; ?></p>
								<p><i class="icon-user"></i> Klijent: <?php echo user_ime($klijent['klijentid']); ?></p><br />
								<br />
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-file"></i>
					<h3>Napomena</h3>
				</div>
				
				<div class="widget-content" style="padding: 10px 10px 0 10px;">
					<form action="serverprocess.php" method="post">
						<input type="hidden" name="task" value="napomena" />
						<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
						<textarea name="napomena" style="width:98%;" rows="4" cellpading="150"><?php echo $server['napomena']; ?></textarea>
						<button type="submit" class="btn btn-warning">Sačuvaj</button>
					</form>
				</div>
			</div>
			<div class="widget stacked widget-table action-table">
				
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista igraca</h3>
				</div>
				
				<div class="widget-content">
			                                <table class='table table-striped table-bordered'>

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

																			<img src="/assets/img/icon/country/<?php echo $client['client_country']; ?>.png"> 

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

	            <form action="/admin/ts-pocetna.php?id=<?php echo $serverid; ?>" method="POST" autocomplete="off" id="modal-poke-auth">

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

	            <form action="/admin/ts-pocetna.php?id=<?php echo $serverid; ?>" method="POST" autocomplete="off" id="modal-kick-auth">

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
				</div>
			</div>
		</div>
<?php 	include("ts-right.php"); ?>
	</div>
<?php
include("assets/footer.php");
?>