<?php
session_start();

include("konfiguracija.php");
include("includes.php");

samo_vlasnik($_SESSION['a_id']);

$naslov = "Pregled masine";
$fajl = "box";
$fajlx = "box";

//if($_SESSION['a_id'] > 2) {
//	header("Location: ./index.php");
//}


if(empty($_GET['id']) or !is_numeric($_GET['id'])) {
	header("Location: index.php");
}

$id = mysql_real_escape_string($_GET['id']);

if(query_numrows("SELECT * FROM `box` WHERE `boxid` = '".$id."'") == "0")
{
	$_SESSION['msg-type'] = "error";
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Mašina sa tim ID-om ne postoji.";
}

$ip_lista = mysql_query("SELECT * FROM `boxip` WHERE `boxid` = '{$id}'");

$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$id."'");

$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '".$id."'");

$brserveri = query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '".$id."'");

$cache = unserialize(gzuncompress($box['cache']));

$igracimax = query_fetch_assoc("SELECT SUM(`slotovi`) as igracimax FROM `serveri` WHERE `box_id` = '{$id}'");
$igracimax = $igracimax['igracimax'];

include("assets/header.php");

if($box['fdl'] == "1") {
	$fdl_status = "Online";
} else {
	$fdl_status = "Offline";
}
?>
<script>
function masinabrisi()
{
	if (confirm('Da li ste sigurno da zelite da izbrisete ovu masinu? \nBrisanje masine podrazumeva i brisanje svih servera na njoj.')) {
		$("#mdel").form.submit();
	} else {}
}
</script>

 <link href="assets/js/plugins/faq/faq.css" rel="stylesheet"> 

       <div class="row">
		<div class="span8">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Pregled masine</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">	
					<table id="pregledprofilat">
						<tbody><tr>
							<th width="165px" style="height: 0;"></th>
							<th width="300px" style="height: 0;"></th>
							<th style="height: 0;"></th>
						</tr>
						<tr>
							<td>
								<p>Ime: <m><?php echo $box['name']; ?></m></p>
								<p>Ip: <m><?php echo $boxip['ip']; ?></m></p>
								<p>Login: <m><?php echo $box['login']; ?></m></p>
								<p>OS: <m><?php echo $cache["{$box['boxid']}"]['os']['os']; ?></m></p>
								<p>FDL Status: <m><?php echo $fdl_status; ?></p>
								<p>Serveri: <m><?php echo $brserveri.'/'.$box['maxsrv']; ?></m></p>
							</td>
							<td>
								<p>Igraci: <m><?php echo $cache["{$box['boxid']}"]['players']['players'].'/'.$igracimax; ?></m></p>							
								<p>Kernel: <m><?php echo $cache["{$box['boxid']}"]['kernel']['kernel']; ?></m></p>	
								<p>CPU: <m><?php echo $cache["{$box['boxid']}"]['cpu']['proc']; ?></m></p>		
								<p>Jezgra: <m><?php echo $cache["{$box['boxid']}"]['cpu']['cores']; ?></m></p>	
								<p>Ramemorija: <m><?php echo file_size($cache["{$box['boxid']}"]['ram']['total']); ?></m></p>	
								<p>Hard disk: <m><?php echo file_size($cache["{$box['boxid']}"]['hdd']['total']); ?></m></p>	
							</td>
							<td>						
								<p>Status: <m><?php echo formatStatus(getStatus($boxip['ip'], $box['sshport'])); ?></m></p>
								<p>CPU Usage: <m><span class="badge badge-<?php
								if ($cache["{$box['boxid']}"]['cpu']['usage'] < 65) {
									echo 'info';
								} else if ($cache["{$box['boxid']}"]['cpu']['usage'] < 85) {
									echo 'warning';
								} else { echo 'important'; }

								?>"><?php echo $cache["{$box['boxid']}"]['cpu']['usage']; ?>&nbsp;%</span></m></p>
								<p>RAM Usage: <m><span class="badge badge-<?php
								if ($cache["{$box['boxid']}"]['ram']['usage'] < 65) {
									echo 'info';
								} else if ($cache["{$box['boxid']}"]['ram']['usage'] < 85) {
									echo 'warning';
								} else { echo 'important'; }

								?>"><?php echo $cache["{$box['boxid']}"]['ram']['usage']; ?>&nbsp;%</span></m></p>	
								<p>HDD Usage: <m><span class="badge badge-<?php
								if ($cache["{$box['boxid']}"]['hdd']['usage'] < 65) {
									echo 'info';
								} else if ($cache["{$box['boxid']}"]['hdd']['usage'] < 85) {
									echo 'warning';
								} else { echo 'important'; }

								?>"><?php echo $cache["{$box['boxid']}"]['hdd']['usage']; ?>&nbsp;%</span></m></p>		
								<p>Uptime: <m><span class="badge badge-success"><?php echo $cache["{$box['boxid']}"]['uptime']['uptime']; ?></span></m></p>	
								<p>Load average: <m><span class="badge badge-<?php
								if ($cache["{$box['boxid']}"]['loadavg']['loadavg'] < 6.50) {
									echo 'info';
								} else if ($cache["{$box['boxid']}"]['loadavg']['loadavg'] < 8.50) {
									echo 'warning';
								} else { echo 'important'; }
								

	$loadavg2 = str_replace("Unknown HZ value! (28) Assume 100.
Warning: /boot/System.map-3.10.9-xxxx-grs-ipv6-64 has an incorrect kernel version.
 ", "", $cache["{$box['boxid']}"]['loadavg']['loadavg']);
	$loadavg2 = str_replace("Unknown HZ value! (776) Assume 100.
			Warning: /boot/System.map-3.10.9-xxxx-grs-ipv6-64 has an incorrect kernel version.", "", $cache["{$box['boxid']}"]['loadavg']['loadavg']);
	$loadavg2 = str_replace("Unknown HZ value! (28) Assume 100.
Warning: /boot/System.map-3.10.9-xxxx-grs-ipv6-64 has an incorrect kernel version.
 ", "", $cache["{$box['boxid']}"]['loadavg']['loadavg']);	

								?>"><?php echo $loadavg2; ?>&nbsp;%</span></m></p>	
							</td>
						</tr>
					</tbody></table>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->		

      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Grafici</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">	
					<table id="pregledprofilat">
						<tr>
							<th>Load average</th>
						</tr>
						<tr>
							<td>
								<img src="box-grafik.php?id=<?php echo $box['boxid']; ?>" />
							</td>
						</tr>
					</tbody></table>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->		

			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3><font style="float: left;">Lista IP adresa - </font><form action="process.php" method="post" style="float: left;">
												<input type="hidden" name="task" value="ipadd" />
												<input type="hidden" name="boxid" value="<?php echo $id; ?>" />
												<input name="ip" class="span2" type="text" placeholder="IP Adresa" />
												<button class="btn btn-mini btn-primary" type="submit">Dodaj</button>
											</form>
					</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="20px">ID</th>
								<th>IP Adresa</th>
								<th>Broj servera</th>
								<th class="td-actions"></th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($ip_lista) == 0) {
								echo'<tr><td colspan="5"><m>Nema ip adrese.</m></td></tr>';
							}
							
							while($row = mysql_fetch_array($ip_lista)) 
							{
								$brsrv = query_numrows("SELECT `ip_id` FROM `serveri` WHERE `ip_id` = '{$row['ipid']}'");
							?>
							<tr>
								<td>#<?php echo $row['ipid']; ?></td>	
								<td><?php echo $row['ip']; ?></td>
								<td><a target="_blank" href="serveri.php?ip=<?php echo $row['ipid']; ?>">#<?php echo $brsrv; ?></a></td>
								<td class="td-actions">
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="ipdel" />
										<input type="hidden" name="ipid" value="<?php echo $row['ipid']; ?>" />
										<input type="hidden" name="boxid" value="<?php echo $row['boxid']; ?>" />
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-remove"></i>										
										</button>
									</form>
								</td>
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>						
								
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->					
			
	    </div>		
	    <div class="span4">
					
			<div class="widget widget-plain">
				
				<div class="widget-content">
				
					<a href="javascript:;" class="btn btn-large btn-support-ask">Status: <?php echo getStatus($boxip['ip'], $box['sshport']); ?></a>
					
					<form id="mdel" action="process.php" method="POST">
						<input type="hidden" name="task" value="masina_delete" />
						<input type="hidden" name="id" value="<?php echo $box['boxid']; ?>" />
						<button type="submit" onClick="masinabrisi();" style="width: 100%" class="btn btn-large btn-danger btn-support-ask">Izbrisi mašinu</button>	
					</form>					

					<a href="#editMasinu" data-toggle="modal" class="btn btn-large btn-info btn-support-ask">Promeni mašinu</a>

					
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->
	
		</div> <!-- /span4 -->
 	
      </div> <!-- /row -->

	  
<?php
include("assets/footer.php");
?>
