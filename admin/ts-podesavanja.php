<?php
session_start();

include("konfiguracija.php");
include("includes.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');

$naslov = "Pregled servera";
$fajl = "ts-podesavanja";
$ts_srv = "1";

$ts_port = "10011";

if(logged_in()) {
	
} else {
	header("Location: ./login");
	die();
}

if(empty($_GET['id']) or !is_numeric($_GET['id'])) 
{
	header("Location: index.php");
	die();
}

samo_vlasnik($_SESSION['a_id']);

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

$boxovi = mysql_query("SELECT * FROM `box`");

if(CheckBoxStatus($serverid) == "Offline") {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Mašina je OFFLINE!";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

if($server['igra'] != "6") {
	header("Location:srv-podesavanja.php?id=$serverid&masina=$_GET[masina]");
}

$igra = "<img src='./assets/img/game-ts3.png' /> Team Speak 3";

$ip = ipadresabezportaap($server['id']);
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

$istice = explode("-", $server['istice']);
$istice = $istice['1'].'/'.$istice['2'].'/'.$istice['0'];

include("assets/header.php");

?>
	<div class="row">
		<div class="span8">
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3>Server podešavanja</h3>
				</div>
				
				<div class="widget-content">
<?php
					if(!isset($_GET['masina']))
					{
?>
					<form action="srv-podesavanja.php" method="get">
						<div class="control-group">
							<label for="datum">Masina</label>
							<div class="controls">
								<input type="hidden" name="id" value="<?php echo $serverid; ?>" />
								<select class="span7" name="masina" onchange="this.form.submit();">
									<option value="0" disabled selected="selected">Izaberi masinu</option>
<?php							while($row = mysql_fetch_array($boxovi)) {	?>
									<option value="<?php echo $row['boxid']; ?>">#<?php echo $row['boxid'].' '.$row['name'].' - '.$row['ip']; if($row['boxid'] == $box['boxid']) echo ' - SADASNJA'; ?></option>
<?php							}	?>
								</select>
							</div>
						</div>
					</form>
<?php
					}
					else if(isset($_GET['masina']))
					{
					
						$masina = mysql_real_escape_string($_GET['masina']);
						if(!is_numeric($masina)) { header("Location: srv-podesavanja.php?id=".$serverid); die(); }
						$ipovi = mysql_query("SELECT * FROM `boxip` WHERE `boxid` = '{$masina}'");						
?>
					<form action="srv-podesavanja.php" method="get">
						<div class="control-group">
							<label for="datum">Masina</label>
							<div class="controls">
								<input type="hidden" name="id" value="<?php echo $serverid; ?>" />
								<select class="span7" name="masina" onchange="this.form.submit();">
<?php							while($row = mysql_fetch_array($boxovi)) {	?>
									<option value="<?php echo $row['boxid']; ?>"<?php if($row['boxid'] == $masina) echo ' selected="selected"'; ?>>#<?php echo $row['boxid'].' '.$row['name'].' - '.$row['ip']; if($row['boxid'] == $box['boxid']) echo ' - SADASNJA'; ?></option>
<?php							}	?>
								</select>
							</div>
						</div>	
					</form>
					<form action="serverprocess.php" method="post">
						<input type="hidden" name="task" value="srv-podesavanja" />
						<input type="hidden" name="id" value="<?php echo $serverid; ?>" />
						<input type="hidden" name="masina" value="<?php echo $masina; ?>" />
					
						<div class="control-group">
							<label for="datum">IP Masine</label>
							<div class="controls">
								<select class="span7" name="ip">
<?php							while($row = mysql_fetch_array($ipovi)) {	?>
									<option value="<?php echo $row['ipid']; ?>"<?php if($row['ip'] == $boxip['ip']) echo ' selected="selected"'; ?>><?php echo $row['ip']; if($row['ip'] == $boxip['ip']) echo ' - SADASNJA'; ?></option>
<?php							}	?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label for="free">Free</label>
							<div class="controls">
								<select class="span7" name="free">
									<option <?php if($server['free'] == "Da") echo 'selected="selected" ' ?>value="Da">Da</option>
									<option <?php if($server['free'] == "Ne") echo 'selected="selected" ' ?>value="Ne">Ne</option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Slotovi</label>
							<div class="controls">
								<input type="text" name="slotovi" class="span7" value="<?php echo $server['slotovi']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Ime servera</label>
							<div class="controls">
								<input type="text" name="ime" class="span7" value="<?php echo $server['name']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Port</label>
							<div class="controls">
								<input type="text" name="port" class="span7" value="<?php echo $server['port']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Istice</label>
							<div class="controls">
								<input type="text" id="datum" name="istice" class="span7" value="<?php echo $istice; ?>" />
							</div>
						</div>
						<div class="control-group">
							<button class="btn btn-warning" type="submit"><i class="icon-arrow-right"></i> Promeni</button>
						</div>						
					</form>						
<?php
					}
?>

				</div>
			</div>
		</div>
<?php 	include("ts-right.php"); ?>
	</div>
<?php
include("assets/footer.php");
?>