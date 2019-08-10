<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$ts_port = "10011";

$fajl = "server";

$adjacents = 3;

if(isset($_GET['view'])) {
	$targetstrana = "serveri.php?view=".$_GET['view']; 
}

if(isset($_GET['masina'])) {
	$targetstrana = "serveri.php?masina=".$_GET['masina']; 
}

if(isset($_GET['ip'])) {
	$targetstrana = "serveri.php?ip=".$_GET['ip']; 
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
	if($_GET['view'] == "game") {
		
		require_once("../inc/libs/lgsl/lgsl_class.php");
		require_once("./libs/fivem.php");
		
		$naslov = "Lista svih Game Servera";
		$sql = "SELECT * FROM `serveri` WHERE `igra` != '6' AND `igra` != '7' ORDER BY id LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `igra` != '6' AND `igra` != '7'";
		
	} else if($_GET['view'] == "fdl") {
		
		$naslov = "Lista svih FastDL Servera";
		$sql = "SELECT * FROM `serveri` WHERE `igra` = '7' ORDER BY id LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `igra` = '7'";
		
	} else if($_GET['view'] == "ts3") {
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');
		
		$naslov = "Lista svih Team Speak 3 Servera";
		$sql = "SELECT * FROM `serveri` WHERE `igra` = '6' ORDER BY id LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `igra` = '6'";
		
	} else if($_GET['view'] == "all") {
		
		require_once("../inc/libs/lgsl/lgsl_class.php");
		require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');
		require_once("./libs/fivem.php");
		
		$naslov = "Lista svih FastDL Servera";
		$sql = "SELECT * FROM `serveri` ORDER BY id LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `serveri`";
		
	} else {
		header("Location: index.php");
	}
} else if(isset($_GET['masina'])) {
	require_once("../inc/libs/lgsl/lgsl_class.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');
	require_once("./libs/fivem.php");
	
	$naslov = "Lista svih Servera na Boxu : ".$_GET['masina'];
	$sql = "SELECT * FROM `serveri` WHERE `box_id` = '".$_GET['masina']."' ORDER BY id LIMIT $start, $limit";
	$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `box_id` = ".$_GET['masina'];
} else if(isset($_GET['ip'])) {
	require_once("../inc/libs/lgsl/lgsl_class.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');
	require_once("./libs/fivem.php");
	
	$naslov = "Lista svih Servera na IP-u : ".$_GET['ip'];
	$sql = "SELECT * FROM `serveri` WHERE `ip_id` = '".$_GET['ip']."' ORDER BY id LIMIT $start, $limit";
	$query = "SELECT COUNT(*) as num FROM `serveri` WHERE `ip_id` = ".$_GET['ip'];
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
if($laststrana > 1) {
	$pagination .= "<div class=\"pagination\" style=\"margin-left: 10px;\"><ul>";
	
	if ($strana > 1)
		$pagination.= "<li><a href=\"$targetstrana&amp;strana=$prev\">«</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>«</a></li>";
	
	if ($laststrana < 7 + ($adjacents * 2)) {
		for ($counter = 1; $counter <= $laststrana; $counter++) {
			if ($counter == $strana)
				$pagination.= "<li><a class=\"active\">$counter</a></li>";
			else
				$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";
		}
	} else if($laststrana > 5 + ($adjacents * 2)) {
		if($strana < 1 + ($adjacents * 2)) {
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$laststrana\">$laststrana</a></li>";		
		} else if($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2)) {
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++) {
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$laststrana\">$laststrana</a></li>";		
		} else {
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++) {
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
			}
		}
	}
	
	if ($strana < $counter - 1)
		$pagination.= "<li><a href=\"$targetstrana&amp;strana=$next\">»</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>»</a></li>";
	$pagination.= "</ul></div>\n";
}

include("assets/header.php");

?>
			<div class="widget stacked widget-table action-table">
				<div class="widget-header">
					<i class="icon-comment"></i>
					<h3><?php echo $naslov; ?></h3>
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
								$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
								if($_GET['view'] == "game") {
									if($row['igra'] == "1") { $igra = 'game-cs.png'; $querytype = "halflife"; }
									else if($row['igra'] == "2") { $igra = 'game-samp.png'; $querytype = "samp"; }
									else if($row['igra'] == "3") { $igra = 'game-minecraft.png'; $querytype = "minecraft"; }
									else if($row['igra'] == "9") { $igra = 'game-fivem.png'; }
									
									$istice = strtotime($row['istice']);
									
									if($row['igra'] != "9") {
										$serverl = lgsl_query_live($querytype, $boxip['ip'], NULL, $row['port'], NULL, 's');
										
										$srvigraci = @$serverl['s']['players'].'/'.@$serverl['s']['playersmax'];
										
										if(@$serverl['b']['status'] == '1') $srvonline = "Da";
										else $srvonline = "Ne";
									} else {
										if(fivemstatus($boxip['ip'], $row['port']) == true)
											$srvonline = "Da";
										else
											$srvonline = "Ne";
										
										$srvigraci = fivemplayers($boxip['ip'], $row['port']).'/'.$row['slotovi'];
									}
									$IPAdress = srvgrafik($boxip['ip'], $row['port']);
								} else if($_GET['view'] == "fdl") {
									$igra = 'game-fdl.png';
									
									$istice = strtotime($row['istice']);
									
									$srvigraci = "1/1";
									$srvonline = "Da";
									$IPAdress = $box['fdl_link']."/".$row['username']."/cstrike/";
								} else if($_GET['view'] == "ts3") {
									$igra = 'game-ts3.png';
									
									$istice = strtotime($row['istice']);
									
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
										$srvonline == "Ne";
									}
									$IPAdress = srvgrafik($boxip['ip'], $row['port']);
								} else {
									if($row['igra'] == "1") { $igra = 'game-cs.png'; $querytype = "halflife"; }
									else if($row['igra'] == "2") { $igra = 'game-samp.png'; $querytype = "samp"; }
									else if($row['igra'] == "3") { $igra = 'game-minecraft.png'; $querytype = "minecraft"; }
									else if($row['igra'] == "6") { $igra = 'game-ts3.png'; }
									else if($row['igra'] == "7") { $igra = 'game-fdl.png'; }
									else if($row['igra'] == "9") { $igra = 'game-fivem.png'; }
									
									$istice = strtotime($row['istice']);
									
									if($row['igra'] == "9") {
										if(fivemstatus($boxip['ip'], $row['port']) == true)
											$srvonline = "Da";
										else
											$srvonline = "Ne";
										
										$srvigraci = fivemplayers($boxip['ip'], $row['port']).'/'.$row['slotovi'];
										
										$IPAdress = srvgrafik($boxip['ip'], $row['port']);
									} else if($row['igra'] == "7") {
										$srvigraci = "1/1";
										$srvonline = "Da";
										$IPAdress = $box['fdl_link']."/".$row['username']."/cstrike/";
									} else if($row['igra'] == "6") {
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
											$srvonline == "Ne";
										}
										$IPAdress = srvgrafik($boxip['ip'], $row['port']);
									} else {
										$serverl = lgsl_query_live($querytype, $boxip['ip'], NULL, $row['port'], NULL, 's');
										
										$srvigraci = @$serverl['s']['players'].'/'.@$serverl['s']['playersmax'];
										
										if(@$serverl['b']['status'] == '1') $srvonline = "Da";
										else $srvonline = "Ne";
										$IPAdress = srvgrafik($boxip['ip'], $row['port']);
									}
								}
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><a href="srv-pocetna.php?id=<?php echo $row['id']; ?>"><m><?php echo $row['name']; ?></m></a></td>
								<td><?php echo $IPAdress; ?></td>
								<td><img src="./assets/img/<?php echo $igra; ?>" style="width:16px;height:16px;"/></td>
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
