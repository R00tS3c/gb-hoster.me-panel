<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled modova";
$fajl = "srv-modovi";
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

if(CheckBoxStatus($serverid) == "Offline") {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Mašina je OFFLINE!";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

if($server['igra'] == "1") { $igras = "halflife"; $igra = "<img src='./assets/img/game-cs.png' /> Counter-Strike 1.6"; }
else if($server['igra'] == "2") { $igras = "samp"; $igra = "<img src='./assets/img/game-samp.png' /> San Andreas Multiplayer"; }
else if($server['igra'] == "3") { $igras = "minecraft"; $igra = "<img src='./assets/img/game-minecraft.png' /> Minecraft"; }
else if($server['igra'] == "6") { header("Location:ts-pocetna.php?id=$serverid"); }
else if($server['igra'] == "7") {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "FDL Nema Mod-ove!";
	$_SESSION['msg-type'] = 'error';
	header("Location: srv-pocetna.php?id=".$server['id']);
	die();
}
else if($server['igra'] == "3") { $igras = "fivem"; $igra = "<img src='./assets/img/game-fivem.png' /> FiveM"; }

require("../inc/libs/lgsl/lgsl_class.php");	
	
if($server['startovan'] == "1" && $server['igra'] != "9")
{
	if($server['igra'] == "5") $serverl = lgsl_query_live($igras, $boxip['ip'], NULL, $server['port']+123, NULL, 's');
	else $serverl = lgsl_query_live($igras, $boxip['ip'], NULL, $server['port'], NULL, 's');
	$srvmapa = @$serverl['s']['map'];
	$srvime = @$serverl['s']['name'];
	$srvigraci = @$serverl['s']['players'].'/'.@$serverl['s']['playersmax'];
	
	if($server['igra'] == "2") $gt = @$serverl['s']['game'];
}

if(@$serverl['b']['status'] == '1' && $server['igra'] != "9") $srvonline = "Da";
else $srvonline = "Ne";


if($srvonline == "Da") $online = '<span style="color: green;">Online</span>';
else if($srvonline == "Ne") $online = '<span style="color: red;">Offline</span>';

if($server['igra'] == "1")
{
	$sql = "SELECT * FROM `modovi` WHERE `igra` = '1' AND `sakriven` = '0' ORDER BY `ime`";
}
else if($server['igra'] == "2")
{
	$sql = "SELECT * FROM `modovi` WHERE `igra` = '2' AND `sakriven` = '0' ORDER BY `ime`";
}
else if($server['igra'] == "3")
{
	$sql = "SELECT * FROM `modovi` WHERE `igra` = '3' AND `sakriven` = '0' ORDER BY `ime`";
}
else if($server['igra'] == "4")
{
	$sql = "SELECT * FROM `modovi` WHERE `igra` = '4' AND `sakriven` = '0' ORDER BY `ime`";
}
else if($server['igra'] == "9")
{
	$sql = "SELECT * FROM `modovi` WHERE `igra` = '9' AND `sakriven` = '0' ORDER BY `ime`";
}
$mod = mysql_query($sql);

include("./assets/header.php");
?>
	<div class="row">
		<div class="span8">
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3>Server modovi</h3>
				</div>
				
				<div class="widget-content">
					<div id="infox">
						<i class="icon-cogs"></i>
						<p id="h5">Mod lista</p><br />
						<p>Izaberite mod i instalirajte/unistalirajte.</p><br />
						<p style="margin-top: -3px;">Instalirajte ili izbrišite neki mod sa servera.</p>
					</div>
					<br />

					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th>Ime moda</th>
								<th>Deskripcija</th>
								<th>Mapa</th>
								<th>Akcija</th>
							</tr>
						</thead>
						<tbody>
<?php	
						if(mysql_num_rows($mod) == 0) echo'<tr><td colspan="4">Nema nijedan mod.</td></tr>';
						while($row = mysql_fetch_array($mod))
						{
?>							
							<tr>
								<td><?php echo $row['ime']; ?></td>
								<td><z><?php echo $row['opis']; ?></z></td>
								<td><?php echo $row['mapa']; ?></td>
<?php
								if($server['mod'] == $row['id'])
								{
?>
								<td align="right">
										<button type="submit" id="ah" style="color: red;">
											<?php echo $jezik['text336']; ?>
										</button>
								</td>
<?php
								}
								else
								{
?>
								<td align="right">
									<form action="serverprocess.php" method="post">
										<input type="hidden" name="task" value="promena-moda" />
										<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
										<input type="hidden" name="modid" value="<?php echo $row['id']; ?>" />
										<button type="submit" id="ah" style="color: green;">
											<i class="icon-plus"></i> <?php echo $jezik['text338']; ?>
										</button>
									</form>
								</td>					
<?php
								}
?>

							</tr>
<?php	
						}				
?>					
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php 	include("srv-right.php"); ?>	
	</div>
<?php
include("./assets/footer.php");
?>