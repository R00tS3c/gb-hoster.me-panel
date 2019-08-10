<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled plugina";
$fajl = "srv-plugini";
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
	$_SESSION['msg2'] = "FDL Nema Plugin-e!";
	$_SESSION['msg-type'] = 'error';
	header("Location: srv-pocetna.php?id=".$server['id']);
	die();
}

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

if(@$serverl['b']['status'] == '1') $srvonline = "Da";
else $srvonline = "Ne";


if($srvonline == "Da") $online = '<span style="color: green;">Online</span>';
else if($srvonline == "Ne") $online = '<span style="color: red;">Offline</span>';

if($server['igra'] == "1")
{
	$sql = "SELECT * FROM `plugins` ORDER BY `ime`";
}
else
{
	$_SESSION['msg1'] = "Greška.";
	$_SESSION['msg2'] = "Samo CS 1.6 serveri imaju pristup ovome.";
	$_SESSION['msg-type'] = "error";
	header("Location: srv-pocetna.php?id=".$serverid);
	die();
}

$mod = mysql_query($sql);

include("./assets/header.php");
?>
	<div class="row">
		<div class="span8">
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3>Server plugins</h3>
				</div>
				
				<div class="widget-content">
					<div id="infox">
						<i class="icon-cog"></i>
						<p id="h5">Plugin lista</p><br />
						<p>Izaberite plugin i instalirajte/unistalirajte.</p><br />
						<p style="margin-top: -3px;">Instalirajte ili izbrišite neki plugin.</p>
					</div>
					<br />

					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th>Ime plugina</th>
								<th>Deskripcija</th>
								<th>Akcija</th>
							</tr>
						</thead>
						<tbody>
<?php	
						if(mysql_num_rows($mod) == 0) echo'<tr><td colspan="3">Nema nijedan plugin.</td></tr>';
						while($row = mysql_fetch_array($mod))
						{
?>							
							<tr>
								<td><?php echo $row['ime']; ?></td>
								<td><z><?php echo $row['deskripcija']; ?></z></td>
<?php
								$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/cstrike/addons/amxmodx/configs/{$row['prikaz']}";

								if (file_exists($fajl))
								{
?>					
								<td align="right">
									<form action="serverprocess.php" method="post">
										<input type="hidden" name="task" value="plugin-remove" />
										<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
										<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
										<button onclick="loading('<?php echo $jezik['text343']; ?>...')" type="submit" id="ah" style="color: red;">
											<i class="icon-remove"></i> <?php echo $jezik['text344']; ?>
										</button>
									</form>					
								</td>
<?php
								}
								else
								{
?>
								<td align="right">
									<form action="serverprocess.php" method="post">
										<input type="hidden" name="task" value="plugin-add" />
										<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
										<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
										<button onclick="loading('<?php echo $jezik['text345']; ?>...')" type="submit" id="ah" style="color: green;">
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