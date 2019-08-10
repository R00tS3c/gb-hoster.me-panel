<?php
session_start();

include("konfiguracija.php");
include("includes.php");

/*samo_vlasnik($_SESSION['a_id']);
if($_SESSION['a_id'] > 2) {
	header("Location: index.php");
}*/

$naslov = "Pregled masine";
$fajl = "box";

$cron = mysql_fetch_assoc(mysql_query( "SELECT `value` FROM `config` WHERE `setting` = 'lastcronrun' LIMIT 1" ));
$box_lista = mysql_query("SELECT * FROM `box` ORDER BY boxid") or die(mysql_error());

include("assets/header.php");

if(vlasnik($_SESSION['a_id'])) 
{
	$masinex = mysql_query("SELECT * FROM `box`");
	while($row = mysql_fetch_array($masinex)) 
	{
		$srvxp = query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '{$row['boxid']}'");

		if($row['maxsrv'] <= $srvxp)
		{
?>
		<div class="alertt alertt-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4 class="alertt-heading">UPOZORENJE!</h4>
			Mašina <?php echo $row['name'].' - '.$row['ip']; ?> je presla svoj limit od <?php echo $row['maxsrv']; ?> servera.
		</div><br />
<?php
		}
	}
}

?>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista masina</h3>
<?php echo 'Zadnji update'; ?> : <span class="label"><?php echo formatDate($cron['value']); ?></span><?php
if ($cron['value'] == 'Never')
{
	echo "\t\t\t<br />Cron job nije podesen!";
}
?>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Ime</th>
								<th>Ip</th>
								<th>Serveri</th>
								<th>Net status</th>
								<th>Ramemorija</th>
								<th>Cpu usage</th>
								<th>Uptime</th>
								<th>HDD</th>
								<th>Serveri</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($box_lista) == 0) {
								echo'<tr><td colspan="5"><m>asd.</m></td></tr>';
							}
							while($row = mysql_fetch_array($box_lista)) 
							{
								$brserveri = query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '".$row['boxid']."'");								
								$brsrv = query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '".$row['boxid']."'");
							
							$cache = unserialize(gzuncompress($row['cache']));
							?>
							<tr>
								<td>#<?php echo $row['boxid']; ?></td>
								<td><a href="box.php?id=<?php echo $row['boxid']; ?>"><?php echo $row['name']; ?></a></td>
								<td><?php echo $row['ip']; ?></td>
								<td><a href="serveri.php?masina=<?php echo $row['boxid']; ?>"><m>#<?php echo $brsrv; ?></m></a></td>
								<td><?php echo formatStatus(getStatus($row['ip'], $row['sshport'])); ?></td>
								<td><span class="badge badge-<?php

								if ($cache["{$row['boxid']}"]['ram']['usage'] < 65) {
									echo 'info';
								} else if ($cache["{$row['boxid']}"]['ram']['usage'] < 85) {
									echo 'warning';
								} else { echo 'important'; }

								?>"><?php echo $cache["{$row['boxid']}"]['ram']['usage']; ?>&nbsp;%</span></td>
								<td><span class="badge badge-<?php

								if ($cache["{$row['boxid']}"]['cpu']['usage'] < 65) {
									echo 'info';
								} else if ($cache["{$row['boxid']}"]['cpu']['usage'] < 85) {
									echo 'warning';
								} else { echo 'important'; }

								?>"><?php echo $cache["{$row['boxid']}"]['cpu']['usage']; ?>&nbsp;%</span></td>								
								<td><span class="badge badge-success"><?php echo $cache["{$row['boxid']}"]['uptime']['uptime']; ?></span></td>
								<td><span class="badge badge-<?php

								if ($cache["{$row['boxid']}"]['hdd']['usage'] < 65) {
									echo 'info';
								} else if ($cache["{$row['boxid']}"]['hdd']['usage'] < 85) {
									echo 'warning';
								} else { echo 'important'; }

								?>"><?php echo $cache["{$row['boxid']}"]['hdd']['usage']; ?>&nbsp;%</span></td>		
								<td><?php echo $brserveri . '/' . $row['maxsrv']; ?></td>					
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>						
								
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->		
<?php
include("assets/footer.php");
?>
