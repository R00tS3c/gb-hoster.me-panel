<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled konzole";
$fajl = "srv-konzola";
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
	$_SESSION['msg2'] = "FDL Nema konzolu!";
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
?>

<style>
pre {
    white-space: pre-wrap;       /* CSS 3 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
</style>
<?php
if(!empty($_GET['log']) == 'view')
{
	if($server['igra'] == "2")
	{
		$filename = "ftp://$server[username]:$server[password]@$box[ip]:$box[ftpport]/server_log.txt";
		$text = "<pre>Console Data (<a href='gp-console_log.php?id=$serverid'>Full view</a>) - Last 1000 lines<hr>";
		$text .= file_get_contents($filename);
		echo $text;
	}
	else
	{
		if(!($con = ssh2_connect($boxip['ip'], $box['sshport']))) return $jezik['text292'];
		else 
		{
			if(!ssh2_auth_password($con, $server['username'], $server['password'])) return $jezik['text292'];
			else 
			{
				$stream = ssh2_exec($con,'tail -n 1000 screenlog.0'); 
				stream_set_blocking( $stream, true );
				
				$resp = '';
				
				while ($line=fgets($stream)) 
				{ 
				   if (!preg_match("/rm log.log/", $line) || !preg_match("/Creating bot.../", $line))
				   {
					   $resp .= $line; 
				   }
				} 
				
				if(empty( $resp )){ 
					$result_info = "Could not load console log";
			    }
			    else{ 
				      $result_info = $resp;
			    }
			}
		}

		$result_info = str_replace("/home", "", $result_info);
		$result_info = str_replace("/home", "", $result_info);
		$result_info = str_replace(">", "", $result_info);

		$text = "<pre>Console Data (<a href='gp-console_log.php?id=$serverid'>Full view</a>) - Last 1000 lines<hr>";
		$text .= htmlspecialchars($result_info);
		echo $text;
	}
}
else
{
	include("./assets/header.php");
	if(!($con = ssh2_connect($boxip['ip'], $box['sshport']))) return $jezik['text292'];
	else 
	{
		if(!ssh2_auth_password($con, $server['username'], $server['password'])) return $jezik['text292'];
		else 
		{
			$stream = ssh2_exec($con,'tail -n 1000 screenlog.0'); 
			stream_set_blocking( $stream, true );
			
			
			
			while ($line=fgets($stream)) 
			{ 
			   if (!preg_match("/rm log.log/", $line) || !preg_match("/Creating bot.../", $line))
			   {
				   $resp .= $line; 
			   }
			} 
			
			if(empty( $resp )){ 
				$result_info = "Could not load console log";
		    }
		    else{ 
			      $result_info = $resp;
		    }
		}
	}

	$result_info = str_replace("/home", "", $result_info);
	$result_info = str_replace("/home", "", $result_info);	
	$result_info = str_replace(">", "", $result_info);
?>

	<div class="row">
		<div class="span8">
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3>Server konzola</h3>
				</div>
				
				<div class="widget-content">
				<br />
					<table id="webftp">
						<tr>
							<th>Auto refresh every 5 sec!</th>
						</tr>
						<tr>
							<td>
								<div serverid="<?php echo $serverid; ?>" id="konzolaajax" style="max-width: 670px; width: 670px; word-wrap: break-word; overflow-y: scroll; overflow-x: hidden; max-height: 400px; height: 400px;">
				<?php
					if($server['igra'] == "2")
					{
						$filename = "ftp://$server[username]:$server[password]@$box[ip]:$box[ftpport]/server_log.txt";
						$text = "<pre>Console Data (<a href='gp-console_log.php?id=$serverid'>Full view</a>) - Last 1000 lines<hr>";
						$text .= file_get_contents($filename);
						echo $text;
					}
					else
					{
						$text = "<pre>Console Data (<a href='gp-console_log.php?id=$serverid'>Full view</a>) - Last 1000 lines<hr>";
						$text .= htmlspecialchars($result_info);
						echo $text;
					}
				?>
								</div>
				<?php
						if($server['igra'] == "1")
						{
							$rcona24 = cscfg('rcon_password', $serverid);
							if(!empty($rcona24)) {
		?>				
							<form id="rconsend" method="post" action="serverprocess.php">
								<input type="hidden" name="task" value="rcon" />
								<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
								<input id="inputrcon" name="rcon" type="text" placeholder="amx_kick NICK" style="width: 50%; height: 30px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); padding: 3px 10px; color: #FFF; font-size: 12px" />

							</form>
		<?php
							}
						}
						else if($server['igra'] == "3")
						{
							$rcon = mccfg('enable-rcon', $serverid);
							$rconpw = mccfg('rcon.password', $serverid);
							if($rcon == "true" AND !empty($rconpw)) {
		?>				
							<form id="rconsend" method="post" action="serverprocess.php">
								<input type="hidden" name="task" value="rcon" />
								<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
								<input id="inputrcon" name="rcon" type="text" placeholder="say Hello" style="width: 50%; height: 30px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); padding: 3px 10px; color: #FFF; font-size: 12px" />

							</form>
		<?php
							}	
						}	
				?>
							</td>
						</tr>				
					</table>
				</div>
				</div>
			</div>
			<?php 	include("srv-right.php"); ?>
		</div>
	</div>

<?php
include("./assets/footer.php");
}
?>