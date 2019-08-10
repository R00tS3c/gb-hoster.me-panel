<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled servera";
$fajl = "srv-pocetna";
$srv = "1";

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
$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$server['mod']."'");
$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$server['user_id']."'");

if(CheckBoxStatus($serverid) == "Offline") {
	$_SESSION['msg1'] = "Gre�ka";
	$_SESSION['msg2'] = "Ma�ina je OFFLINE!";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

if($server['igra'] == "1") { $igras = "halflife"; $igra = "<img src='./assets/img/game-cs.png' /> Counter-Strike 1.6"; }
else if($server['igra'] == "2") { $igras = "samp"; $igra = "<img src='./assets/img/game-samp.png' /> San Andreas Multiplayer"; }
else if($server['igra'] == "4") { $igras = "callofduty4"; $igra = "<img src='./assets/img/game-cod4.png' /> Call Of Duty 4"; }
else if($server['igra'] == "3") { $igras = "minecraft"; $igra = "<img src='./assets/img/game-minecraft.png' /> Minecraft"; }
else if($server['igra'] == "5") { $igras = "mta"; $igra = "<img src='./assets/img/game-mta.png' /> Multi Theft Auto"; }
else if($server['igra'] == "6") { header("Location:ts-pocetna.php?id=$serverid"); }
else if($server['igra'] == "7") { $igras = "fdl"; $igra = "<img src='./assets/img/game-fdl.png' /> FastDL"; }
else if($server['igra'] == "9") { $igras = "fivem"; $igra = "<img src='./assets/img/game-fivem.png' style='width:16px;height:16px;' /> FiveM"; }

require("../inc/libs/lgsl/lgsl_class.php");	
	
if($server['startovan'] == "1" && $server['igra'] != "7" && $server['igra'] != "9") {
	if($server['igra'] == "5") $serverl = lgsl_query_live($igras, $boxip['ip'], NULL, $server['port']+123, NULL, 's');
	else $serverl = lgsl_query_live($igras, $boxip['ip'], NULL, $server['port'], NULL, 's');
	$srvmapa = @$serverl['s']['map'];
	$srvime = @$serverl['s']['name'];
	$srvigraci = @$serverl['s']['players'].'/'.@$serverl['s']['playersmax'];
	
	if($server['igra'] == "2") $gt = @$serverl['s']['game'];
}

if($server['igra'] == "7") {
	$srvonline = "Da";
} else  {
	if(@$serverl['b']['status'] == '1') $srvonline = "Da";
	else $srvonline = "Ne";
}

if($srvonline == "Da") $online = '<span style="color: green;">Online</span>';
else if($srvonline == "Ne") $online = '<span style="color: red;">Offline</span>';

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
								<?php if($server['igra'] != "7") {?>
								<p><i class="icon-home"></i> Default mapa: <?php echo $server['map']; ?></p>
								<?php }?>
								<p><i class="icon-calendar"></i> Vazi do: <?php echo srv_istekao($server['id']); ?></p>
								<p><i class="icon-gamepad"></i> Igra: <?php echo $igra; ?></p>
								<p><i class="icon-cog"></i> Mod: <?php echo $mod['ime']; ?></p>
								<?php if($server['igra'] != "7") {?>
								<p><i class="icon-cogs"></i> FPS: <?php echo $server['fps']; ?></p>
								<p><i class="icon-bar-chart"></i> Ip:port: <?php echo $boxip['ip'].":".$server['port']; ?></p>
								<?php } else  {?>
								<p><i class="icon-bar-chart"></i> FastDL Link: <?php echo $box['fdl_link']."/".$server['username']."/cstrike/"; ?></p>
								<?php }?>
								<p><i class="icon-tasks"></i> Masina: <a href="box.php?id=<?php echo $box['boxid']; ?>"><?php echo $box['name']; ?></a></p>
								<strong>Konektovanje</strong>
								<p><i class="icon-forward"></i> WinSPC (sftp): <a href="sftp://<?php echo $server['username']; ?>:<?php echo $server['password']; ?>@<?php echo $boxip['ip'].":".$box['sshport']; ?>">Konektuj se kao user</a></p>
								<p><i class="icon-forward"></i> Terminal (ssh): <a href="ssh://<?php echo $server['username']; ?>:<?php echo $server['password']; ?>@<?php echo $boxip['ip'].":".$box['sshport']; ?>">Konektuj se kao user</a></p>
								<p><i class="icon-forward"></i> WinSPC (ftp): <a href="ftp://<?php echo $server['username']; ?>:<?php echo $server['password']; ?>@<?php echo $boxip['ip'].":".$box['ftpport']; ?>">Konektuj se kao user</a></p>
								<?php if($server['igra'] != "7") {?>
								<strong>GRAFIK</strong>
								<img src="srv-grafik.php?id=<?php echo $serverid; ?>" />
								<?php } ?>
							</td>
							<td>
								<strong>INFO</strong>
								<?php if($server['igra'] != "7") {?>
								<p><i class="icon-th"></i> Slotova: <?php echo $server['slotovi']; ?></p>
								<?php }?>
								<p><i class="icon-th-large"></i> Status: <?php echo srv_status($server['status']); ?></p>
								<p><i class="icon-edit-sign"></i> Cena: <?php echo $cena; ?></p>
								<p><i class="icon-user"></i> Klijent: <?php echo user_ime($klijent['klijentid']); ?></p><br />

								<strong>FTP INFO</strong>
								<p><i class="icon-tasks"></i> Host: <?php echo $boxip['ip']; ?></p>
								<p><i class="icon-user"></i> Username: <?php echo $server['username']; ?> <?php if(pristup()){?><form action="serverprocess.php" method="post"><input type="hidden" name="task" value="updateuser" /><input type="hidden" name="serverid" value="<?php echo $serverid; ?>" /><button type="submit" class="btn btn-mini btn-info" style="position: absolute; margin-top: -32px; margin-left:185px;">Update usera</button></form><form action="serverprocess.php" method="post"><input type="hidden" name="task" value="chown" /><input type="hidden" name="serverid" value="<?php echo $serverid; ?>" /><button type="submit" class="btn btn-mini btn-info" style="position: absolute; margin-top: -42px; margin-left:270px;">Chown</button></form></p><?php } ?>
								<p><i class="icon-flag"></i> Password: <?php echo $server['password']; ?></p>
								<p><i class="icon-puzzle-piece"></i> Port: <?php echo $box['ftpport']; ?></p><br />
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
			<?php if($server['igra'] != "7" && $server['igra'] != "9") {?>	
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista igraca</h3>
				</div>
				
				<div class="widget-content">

<?php	
$fields_show = array("Nick", "Skor", "Ubistva", "Smrti", "Tim", "Ping", "Bot", "Vreme");
$fields_hide = array("teamindex", "pid", "pbguid");
$fields_other = TRUE;

$lgsl_server_id = $serverid;

if($server['igra'] == "5") $serverl2 = lgsl_query_cached($igras, $boxip['ip'], $server['port'], $server['port']+123, $server['port'], "sep", $lgsl_server_id);
else $serverl2 = lgsl_query_cached($igras, $boxip['ip'], $server['port'], $server['port'], $server['port'], "sep", $lgsl_server_id);

$fields = lgsl_sort_fields($serverl2, $fields_show, $fields_hide, $fields_other);
$serverl2 = lgsl_sort_players($serverl2);
$serverl2 = lgsl_sort_extras($serverl2);
$misc = lgsl_server_misc($serverl2);
$serverl2 = lgsl_server_html($serverl2);

if (empty($serverl2['p']) || !is_array($serverl2['p']))
{
	$output = "<table class='table table-striped table-bordered'><tr><td>Trenutno nema online igraca!</td></tr></table>";
}
else
{
	$output = "
	<table class='table table-striped table-bordered'>
	<thead>
	<tr>";

	foreach ($fields as $field)
	{
		$field = ucfirst($field);
		$output .= "
		<th><b> {$field} </b></th>\r\n";
	}
	
	$output .= "<th>Kick igraca</th>";

	$output .= "
	</tr>
	</thead>
	<tbody>";

	foreach ($serverl2['p'] as $player_key => $player)
	{
		$output .= "
		<tr>";

		foreach ($fields as $field)
		{
			$output .= "
			<td> {$player[$field]} </td>";
		}
		
		$output .= "
		<td>
			<form id='asd123' method='post' action='serverprocess.php'>
				<input type='hidden' name='task' value='kick-igraca' />
				<input type='hidden' name='serverid' value='".$serverid."' />
				<input type='hidden' name='nick' value='".$player['name']."' />
				<button type='submit' class='btn btn-mini btn-warning'><i class='icon-remove'></i> Kick</button>
			</form>
		</td>";

		$output .= "
		</tr>";
	}

	$output .= "
	</tbody>
	</table>";
}

echo $output;
?>

					
				</div>
			
			</div>		
			<?php }?>
		</div>
<?php 	include("srv-right.php"); ?>
	</div>
<?php
include("assets/footer.php");
?>