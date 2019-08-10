<?php
session_start();

include("konfiguracija.php");
include("includes.php");
require_once '../inc/libs/GameQ.php';

$naslov = "Pregled servera";
$fajl = "srv-podesavanja";
$srv = "1";

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
$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$server['user_id']."'");

$boxovi = mysql_query("SELECT * FROM `box`");
$modovics = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '1'");
$modovisamp = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '2'");
$modovimc = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '3'");
$modovimta = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '5'");
$modovifdl = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '7'");
$modovifivem = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '9'");

if(CheckBoxStatus($serverid) == "Offline") {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Mašina je OFFLINE!";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

if($server['igra'] == "1") { $igras = "cs"; $igra = "<img src='./assets/img/game-cs.png' /> Counter-Strike 1.6"; }
else if($server['igra'] == "2") { $igras = "samp"; $igra = "<img src='./assets/img/game-samp.png' /> San Andreas Multiplayer"; }
else if($server['igra'] == "3") { $igras = "cs"; $igra = "<img src='./assets/img/game-minecraft.png' /> Minecraft"; }
else if($server['igra'] == "7") { $igras = "fdl"; $igra = "<img src='./assets/img/game-fdl.png' /> FastDL"; }
if($server['igra'] == "6") { header("Location:ts-podesavanja.php?id=$serverid&masina=$_GET[masina]"); }

if($server['igra'] != "7" && $server['igra'] != "9") {
	$servers = array(
	'server' => array($igras, $boxip['ip'], $server['port'])
	);
	
	$gq = new GameQ();
	
	$gq->addServers($servers);
	
	$gq->setOption('timeout', 200);
	
	$gq->setFilter('normalise');
	$gq->setFilter('sortplayers', 'gq_ping');
	
	$results = $gq->requestData();
	
	foreach ($results as $id => $data) {
		if($data['gq_online'] == "1") $srvonline = "Da";
		else if($data['gq_online'] == "0") $srvonline = "Ne";
		$srvmapa = $data['gq_mapname'];
		$srvime = $data['gq_hostname'];
		$gt = $data['gq_gametype'];
		$igraci = $data['players'];
		$srvigraci = $data['gq_numplayers']."/".$data['gq_maxplayers'];
	}
	
	if($srvonline == "Da") $online = '<span style="color: green;">Online</span>';
	else if($srvonline == "Ne") $online = '<span style="color: red;">Offline</span>';
} else {
		$srvonline = "Da";
		$igraci = "1";
		$srvigraci = "1/1";
	$online = '<span style="color: green;">Online</span>';
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
							<label for="datum">Mod</label>
							<div class="controls">
								<select class="span7" name="mod">

<?php							if($mod['igra'] == "1") {
									while($row = mysql_fetch_array($modovics)) {	?>
									<option value="<?php echo $row['id']; ?>"<?php if($row['id'] == $mod['id']) echo ' selected="selected"'; ?>><?php echo $row['ime']; ?></option>
<?php							
									}
								}	
								else if($mod['igra'] == "2")
								{
									while($row = mysql_fetch_array($modovisamp)) {	?>
									<option value="<?php echo $row['id']; ?>"<?php if($row['id'] == $mod['id']) echo ' selected="selected"'; ?>><?php echo $row['ime']; ?></option>
<?php							
									}								
								}
								else if($mod['igra'] == "3")
								{
									while($row = mysql_fetch_array($modovimc)) {	?>
									<option value="<?php echo $row['id']; ?>"<?php if($row['id'] == $mod['id']) echo ' selected="selected"'; ?>><?php echo $row['ime']; ?></option>
<?php							
									}								
								}
								else if($mod['igra'] == "5")
								{
									while($row = mysql_fetch_array($modovimta)) {	?>
									<option value="<?php echo $row['id']; ?>"<?php if($row['id'] == $mod['id']) echo ' selected="selected"'; ?>><?php echo $row['ime']; ?></option>
<?php							
									}								
								}
								else if($mod['igra'] == "7")
								{
									while($row = mysql_fetch_array($modovifdl)) {	?>
									<option value="<?php echo $row['id']; ?>"<?php if($row['id'] == $mod['id']) echo ' selected="selected"'; ?>><?php echo $row['ime']; ?></option>
<?php							
									}
									
								}
								else if($mod['igra'] == "9")
								{
									while($row = mysql_fetch_array($modovifivem)) {	?>
									<option value="<?php echo $row['id']; ?>"<?php if($row['id'] == $mod['id']) echo ' selected="selected"'; ?>><?php echo $row['ime']; ?></option>
<?php							
									}								
								}
?>

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
						<?php
						if($server['igra'] != "7") {
						?>
						<div class="control-group">
							<label for="datum">Slotovi</label>
							<div class="controls">
								<input type="text" name="slotovi" class="span7" value="<?php echo $server['slotovi']; ?>" />
							</div>
						</div>
						<?php
						}
						?>
						<div class="control-group">
							<label for="datum">Ime servera</label>
							<div class="controls">
								<input type="text" name="ime" class="span7" value="<?php echo $server['name']; ?>" />
							</div>
						</div>
						<?php
						if($server['igra'] != "7") {
						?>
						<div class="control-group">
							<label for="datum">Default mapa</label>
							<div class="controls">
								<input type="text" name="map" class="span7" value="<?php echo $server['map']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Port</label>
							<div class="controls">
								<input type="text" name="port" class="span7" value="<?php echo $server['port']; ?>" />
							</div>
						</div>
						<?php
						}
						?>
						<div class="control-group">
							<label for="datum">Username</label>
							<div class="controls">
								<input readonly="readonly" type="text" name="username" class="span7" value="<?php echo $server['username']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Password</label>
							<div class="controls">
								<input type="text" name="password" class="span7" value="<?php echo $server['password']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Istice</label>
							<div class="controls">
								<input type="text" id="datum" name="istice" class="span7" value="<?php echo $istice; ?>" />
							</div>
						</div>
						<?php
						if($server['igra'] != "7") {
						?>
						<div class="control-group">
							<label for="datum">FPS</label>
							<div class="controls">
								<input type="text" name="fps" class="span7" value="<?php echo $server['fps']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label for="datum">Komanda</label>
							<div class="controls">
								<textarea type="text" name="komanda" class="span7"><?php echo $server['komanda']; ?></textarea>
							</div>
						</div>
						<?php
						}
						?>
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
<?php 	include("srv-right.php"); ?>
	</div>
<?php
include("assets/footer.php");
?>