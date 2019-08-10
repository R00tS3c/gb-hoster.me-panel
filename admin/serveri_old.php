<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$ts_port = "10101";
$ts_port_try_2 = "11101";
$ts_port_try_3 = "10011";

$fajl = "server";

$adjacents = 3;

if(isset($_GET['masina'])) {
	$targetstrana = "serveri.php?view={$_GET['view']}&masina={$_GET['masina']}"; 
	if(isset($_GET['ip'])) $targetstrana = "serveri.php?view={$_GET['view']}&masina={$_GET['masina']}&ip={$_GET['ip']}"; 
} else if(isset($_GET['drzava'])) {
	$targetstrana = "serveri.php?view={$_GET['view']}&drzava={$_GET['drzava']}"; 
	if(isset($_GET['ip'])) $targetstrana = "serveri.php?view={$_GET['view']}&drzava={$_GET['drzava']}&ip={$_GET['ip']}"; 
} else if(isset($_GET['order'])) {
	$targetstrana = "serveri.php?view={$_GET['view']}&order={$_GET['order']}"; 
} else {
	$targetstrana = "serveri.php?view=".$_GET['view']; 
}

$limit = 15; 

if(empty($_GET['strana'])) {
	$start = 0;	
	$strana = 1;
} else if(!isset($_GET['strana'])) {
	$start = 0; 	
	$strana = 0;
} else {
	$start = ($_GET['strana'] - 1) * $limit; 
	$strana = $_GET['strana'];
}

if(isset($_GET['view'])) {
	if(isset($_GET['game'])) {
		$game = mysql_real_escape_string($_GET['game']);
		
		if(!is_numeric($game)){
			header("Location: index.php");
			die();
		}
		
		if($_GET['view'] == "game") {
			$naslov = "Lista servera";
			$sql = "SELECT * FROM `serveri` WHERE `igra` = '$game' ORDER BY `id` LIMIT $start, $limit";
			$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `igra` = '$game'";
			$ikona = "icon-credit-card";
			$itikete = "billing ";
		} else {
			header("Location: index.php");
			die();
		}
	} else if(isset($_GET['drzava'])) {
		$drzava = mysql_real_escape_string($_GET['drzava']);
		
		if(query_numrows("SELECT `klijentid` FROM `klijenti` WHERE `zemlja` = '{$drzava}'") == 0) header("Location: index.php");
		
		if($_GET['view'] == "all") {
			$naslov = "Lista svih servera";
			$sql = "SELECT k.klijentid klijentid, s.user_id user_id, s.ip_id ip_id, s.igra igra, s.istice istice, s.port port, ".
				   "s.cache cache, s.id id, s.name name, s.status status ".
				   "FROM serveri s, klijenti k ".
				   "WHERE k.zemlja = '{$drzava}' AND s.user_id = k.klijentid ".
				   "ORDER BY `id` LIMIT $start, $limit";
			$query = "SELECT COUNT(*) as num ".
					 "FROM serveri s, klijenti k ".
					 "WHERE k.zemlja = '{$drzava}' AND s.user_id = k.klijentid";
			$ikona = "icon-comment";
			$itikete = "nove ";
		} else {
			header("Location: index.php");
			die();
		}
	} else if(isset($_GET['masina'])) {
		if(isset($_GET['ip'])) {
			$ipm = mysql_real_escape_string($_GET['ip']);
			if(!is_numeric($ipm)) header("Location: index.php");
			if(query_numrows("SELECT * FROM `boxip` WHERE `ipid` = '".$ipm."'") != 1) header("Location: index.php");
			if($_GET['view'] == "all") {
				$naslov = "Lista svih servera";
				$sql = "SELECT * FROM `serveri` WHERE `ip_id` = '".$ipm."' ORDER BY `id` LIMIT $start, $limit";
				$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `box_id` = '".$ipm."'";
				$ikona = "icon-comment";
				$itikete = "nove ";
			} else {
				header("Location: index.php");
				die();
			}
		} else {
			$masina = mysql_real_escape_string($_GET['masina']);
			if(!is_numeric($masina)) header("Location: index.php");
			if(query_numrows("SELECT * FROM `box` WHERE `boxid` = '".$masina."'") != 1) header("Location: index.php");
			if($_GET['view'] == "all") {
				$naslov = "Lista svih servera";
				$sql = "SELECT * FROM `serveri` WHERE `box_id` = '".$masina."' ORDER BY `id` LIMIT $start, $limit";
				$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `box_id` = '".$masina."'";
				$ikona = "icon-comment";
				$itikete = "nove ";
			} else {
				header("Location: index.php");
			}
		}		
	} else {
		if($_GET['view'] == "all") {
			$order = "ORDER BY id";
			if(isset($_GET['order'])) {
				$order = "ORDER BY istice ";				
				if($_GET['order'] == "asc") $order .= "ASC";
				else $order .= "DESC";
			}

			$naslov = "Lista svih servera";
			//$sql = "SELECT * FROM `serveri` {$order} LIMIT $start, $limit";
			$sql = "SELECT * FROM `serveri` WHERE `igra` != '6' {$order} LIMIT $start, $limit";
			$query = "SELECT COUNT(*) as num FROM `serveri`";
			$ikona = "icon-comment";
			$itikete = "nove ";
		} else if($_GET['view'] == "aktivni") {
			$naslov = "Lista aktivinih servera";
			$sql = "SELECT * FROM `serveri` WHERE `status` = 'Aktivan' ORDER BY `id` LIMIT $start, $limit";
			$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `status` = 'Aktivan'";
			$ikona = "icon-credit-card";
			$itikete = "billing ";
		} else if($_GET['view'] == "suspendovani") {
			$naslov = "Lista suspendovanih servera";
			$sql = "SELECT * FROM `serveri` WHERE `status` = 'Suspendovan' ORDER BY `id` LIMIT $start, $limit";
			$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `status` = 'Suspendovan'";
			$ikona = "icon-credit-card";
			$itikete = "billing ";
		} else if($_GET['view'] == "istekli") {
			$naslov = "Lista suspendovanih servera";
			$sql = "SELECT * FROM `serveri` WHERE `status` = 'Istekao' ORDER BY `id` LIMIT $start, $limit";
			$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `status` = 'Istekao'";
			$ikona = "icon-credit-card";
			$itikete = "billing ";
		} else if($_GET['view'] == "free") {
			$naslov = "Lista free servera";
			$sql = "SELECT * FROM `serveri` WHERE `free` = 'Da' ORDER BY `id` LIMIT $start, $limit";
			$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `free` = 'Da'";
			$ikona = "icon-credit-card";
			$itikete = "billing ";
		} else {
			header("Location: index.php");
		}
	}
} else {
	header("Location: index.php");
}

$total_stranas = mysql_fetch_array(mysql_query($query));
$total_stranas = $total_stranas[num];
		
	

	
$result = mysql_query($sql) or die($sql);
	

if ($strana == 0) $strana = 1;					
$prev = $strana - 1;							
$next = $strana + 1;							
$laststrana = ceil($total_stranas/$limit);		
$lpm1 = $laststrana - 1;						
	
$pagination = "";
if($laststrana > 1)
{	
	$pagination .= "<div class=\"pagination\" style=\"margin-left: 10px;\"><ul>";
	//previous button
	if ($strana > 1) 
		$pagination.= "<li><a href=\"$targetstrana&amp;strana=$prev\">«</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>«</a></li>";	
	
	//strana	
	if ($laststrana < 7 + ($adjacents * 2))	//not enough stranas to bother breaking it up
	{	
		for ($counter = 1; $counter <= $laststrana; $counter++)
		{
			if ($counter == $strana)
				$pagination.= "<li><a class=\"active\">$counter</a></li>";
			else
				$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
		}
	}
	elseif($laststrana > 5 + ($adjacents * 2))	//enough stranas to hide some
	{
		if($strana < 1 + ($adjacents * 2))		
		{
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$laststrana\">$laststrana</a></li>";		
		}
		elseif($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2))
		{
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++)
			{
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$laststrana\">$laststrana</a></li>";		
		}
		else
		{
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++)
			{
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
			}
		}
	}
		
	//next button
	if ($strana < $counter - 1)
		$pagination.= "<li><a href=\"$targetstrana&amp;strana=$next\">»</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>»</a></li>";
	$pagination.= "</ul></div>\n";	
		
}

include("assets/header.php");

?>
			<div class="widget stacked widget-table action-table">
<?php
				if($_GET['view'] == "all") {
					if($_GET['order'] == "asc") $order = "desc";
					else $order = "asc";
					echo '<a href="serveri.php?view=all&order='.$order.'">Sortiraj po datumu isteka</a>';
				}
?>

				<div class="widget-header">
					<i class="<?php echo $ikona; ?>"></i>
					<h3><?php echo ucwords($_GET['view']); ?> serveri</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table cellspacing="1" id="myTable2" class="table table-striped table-bordered tablesorter">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Ime servera</th>
								<th>Ip adresa</th>
								<th>Igra</th>
								<th>Klijent</th>
								<th>Ističe</th>
								<th>Status</th>
								<th>Free</th>
								<th>Igrači</th>
								<th>Kreirao</th>
								<?php if(vlasnik($_SESSION['a_id'])) { echo '<th class="td-actions" style="width: 92px;">Napomena</th>'; } ?>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($result) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema servera.</m></td></tr>';
							}
							while($row = mysql_fetch_array($result)) {	
								$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$row['user_id']."'");
								$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
								
								if($row['igra'] == "1") { $igras = "cs"; $igrai = "Counter-Strike 1.6"; $igra = 'game-cs.png'; }
								else if($row['igra'] == "2") { $igras = "samp"; $igrai = "San Andreas Multiplayer"; $igra = 'game-samp.png'; }
								else if($row['igra'] == "4") { $igras = "cod"; $igrai = "Call of Duty 4"; $igra = 'game-cod4.png'; }
								else if($row['igra'] == "3") { $igras = "minecraft"; $igrai = "Minecraft"; $igra = 'game-minecraft.png'; }
								else if($row['igra'] == "6") {
									$igras = "ts";
									$igrai = "TeamSpeak3";
									$igra = 'game-ts3.png';
								} else if($row['igra'] == "7") { $igras = "fdl"; $igrai = "FastDL"; $igra = 'game-fdl.png'; }
								
								$istice = strtotime($row['istice']);
								if($row['igra'] != "6") {
									require_once("../inc/libs/lgsl/lgsl_class.php");	
									
									if($row['igra'] == "1") $querytype = "halflife";
									else if($row['igra'] == "2") $querytype = "samp";
									else if($row['igra'] == "4") $querytype = "callofduty4";
									else if($row['igra'] == "3") $querytype = "minecraft";
									else if($row['igra'] == "5") $querytype = "mta";
													
									if($row['igra'] == "5") {
										$serverl = lgsl_query_live($querytype, $boxip['ip'], NULL, $row['port']+123, NULL, 's');
									} else if($row['igra'] == "7") {
										
									} else {
										$serverl = lgsl_query_live($querytype, $boxip['ip'], NULL, $row['port'], NULL, 's');
									}
									if($row['igra'] == "7") {
										$srvigraci = "1/1";
										$srvonline = "Da";
									} else {
										$srvigraci = @$serverl['s']['players'].'/'.@$serverl['s']['playersmax'];
										
										if(@$serverl['b']['status'] == '1') $srvonline = "Da";
										else $srvonline = "Ne";			
										
										$cache = unserialize(gzuncompress($row['cache']));
									}
								}
								if($row['igra'] == "6") {
									require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');
									
									$ip = ipadresabezportaap($row['id']);
									
									$tsAdmin = new ts3admin($ip, $ts_port);
									
									if($tsAdmin->getElement('success', $tsAdmin->connect())) {
										$tsAdmin->login($row['username'], $row['password']);
										$tsAdmin->selectServer($row['port']);
										
										$ts_s_info 		= $tsAdmin->serverInfo();
										
										$Server_Online  = $ts_s_info['data']['virtualserver_status'];
										
										if($Server_Online == 'online') {
											$srvonline = "Da";
											$srvigraci = $ts_s_info['data']['virtualserver_clientsonline'].'/'.$ts_s_info['data']['virtualserver_maxclients'];
										}
									} else {
										$tsAdmin = new ts3admin($ip, $ts_port_try_2);
										if($tsAdmin->getElement('success', $tsAdmin->connect())) {
											$tsAdmin->login($row['username'], $row['password']);
											$tsAdmin->selectServer($row['port']);
											
											$ts_s_info 		= $tsAdmin->serverInfo();
											
											$Server_Online  = $ts_s_info['data']['virtualserver_status'];
											
											if($Server_Online == 'online') {
												$srvonline = "Da";
												$srvigraci = $ts_s_info['data']['virtualserver_clientsonline'].'/'.$ts_s_info['data']['virtualserver_maxclients'];
											}
										} else {
											$tsAdmin = new ts3admin($ip, $ts_port_try_3);
											if($tsAdmin->getElement('success', $tsAdmin->connect())) {
												$tsAdmin->login($row['username'], $row['password']);
												$tsAdmin->selectServer($row['port']);
												
												$ts_s_info 		= $tsAdmin->serverInfo();
												
												$Server_Online  = $ts_s_info['data']['virtualserver_status'];
												
												if($Server_Online == 'online') {
													$srvonline = "Da";
													$srvigraci = $ts_s_info['data']['virtualserver_clientsonline'].'/'.$ts_s_info['data']['virtualserver_maxclients'];
												}
											} else {
												$srvonline == "Ne";
											}
										}
									}
									
								}
								$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><a href="srv-pocetna.php?id=<?php echo $row['id']; ?>"><m><?php echo $row['name']; ?></m></a></td>
								<td><?php if($row['igra'] == "7") echo $box['fdl_link']."/".$row['username']."/cstrike/"; else echo srvgrafik($boxip['ip'], $row['port']); ?></td>
								<td><img src="./assets/img/<?php echo $igra; ?>"  /></td>
								<td><?php echo user_imep($row['user_id']); ?></td>
								<td><?php echo srv_istekao($row['id']); ?></td>
								<td><?php echo srv_status($row['status']); ?></td>
								<td><?php echo srv_free($row['id']); ?></td>
								<td><?php if($srvonline == "Da") echo $srvigraci; else echo '<span style="color: red;">Offline</span>'; ?></td>
								<td><?php echo admin_ime_p_l($row['aid']); ?></td>
<?php 
								if(vlasnik($_SESSION['a_id'])) {
									echo '<td class="td-actions" style="width: 92px;">';
									if($row['napomena'] != null) {
										echo $row['napomena'];
									} else { echo '<font color="red">-//-</font>'; }
									echo '</td>';
								}
?>									
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>		
						<?php echo $pagination; ?>
					
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->	
<?php
include("assets/footer.php");
?>
