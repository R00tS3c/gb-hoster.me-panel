<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Početna";
$fajl = "index";

$logovi = mysql_query("SELECT * FROM `logovi` WHERE `clientid` IS NULL ORDER BY `id` DESC LIMIT 20");
$logovisms = mysql_query("SELECT * FROM `billing_sms` ORDER BY `id` DESC LIMIT 20");

include("assets/header.php");

$admini = mysql_query("SELECT * FROM `admin` ORDER BY `status`");

$smajlici = "<a href='#' id='smajlici2' kod=':D'><img src='./assets/smajli/002.png' /></a> ".
			"<a href='#' id='smajlici' kod=':P'><img src='./assets/smajli/104.png' /></a> ".
			"<a href='#' id='smajlici' kod='o.o'><img src='./assets/smajli/012.png' /></a> ".
			"<a href='#' id='smajlici' kod=':)'><img src='./assets/smajli/001.png' /></a> ".
			"<a href='#' id='smajlici' kod='xD'><img src='./assets/smajli/xD.png' /></a> ".
			"<a href='#' id='smajlici' kod=':m'><img src='./assets/smajli/006.png' /></a> ".
			"<a href='#' id='smajlici' kod=';)'><img src='./assets/smajli/003.gif' /></a> <br />".
			"<a href='#' id='smajlici' kod=':o'><img src='./assets/smajli/004.png' /></a> ".
			"<a href='#' id='smajlici' kod='3:)'><img src='./assets/smajli/007.png' /></a> ".
			"<a href='#' id='smajlici' kod=':$'><img src='./assets/smajli/008.png' /></a> ".
			"<a href='#' id='smajlici' kod=':S'><img src='./assets/smajli/009.png' /></a> ".
			"<a href='#' id='smajlici' kod=':('><img src='./assets/smajli/010.png' /></a> ".
			"<a href='#' id='smajlici' kod=';('><img src='./assets/smajli/011.png' /></a> ".
			"<a href='#' id='smajlici' kod='<3'><img src='./assets/smajli/015.png' /></a> <br />".
			"<a href='#' id='smajlici' kod='</3'><img src='./assets/smajli/016.png' /></a> ".
			"<a href='#' id='smajlici' kod=':/'><img src='./assets/smajli/083.png' /></a> ".
			"<a href='#' id='smajlici' kod=':ninja'><img src='./assets/smajli/086.png' /></a> ".
			"<a href='#' id='smajlici' kod=':P'><img src='./assets/smajli/104.png' /></a> ".
			"<a href='#' id='smajlici' kod=':T'><img src='./assets/smajli/tuga.gif' /></a> ";
		
$crons = mysql_query( "SELECT * FROM `crons`" );
?>
      <div class="row">
		<div class="span12">


			<button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#statistikax">
			Pokaži ili sakrij statistiku
			</button><?php 	if(pristup()){	?><br /><br /><?php } ?>
			<div id="statistikax" class="collapse out">
				<?php 	if(pristup()){	?>
				<br />
				<b>Uplate koje čekaju proveru:</b> <a href="tiket_lista.php?vrsta=ceka_proveru"><z><?php echo br_statistika('ceka_uplatu'); ?> - Pogledaj</z></a>
				<br /><br />
				<?php } ?>				
				<table class="table table-striped table-bordered">
					<tbody>
						<tr>
							<td><b>Serveri:</b></td>
							<td><i class="icon-gamepad"></i> Svi serveri: <z><?php echo br_statistika('serveri'); ?></z></td>
							<td><i class="icon-gamepad"></i> Aktivni serveri: <z><?php echo br_statistika('serveri_aktivni'); ?></z></td>
							<td><i class="icon-gamepad"></i> Suspendovani serveri: <z><?php echo br_statistika('serveri_susp'); ?></z></td>
							<td colspan="2"><i class="icon-gamepad"></i> Istekli serveri: <z><?php echo br_statistika('serveri_istekli'); ?></z></td>
						</tr>	
						<tr style="background: white">
							<td><b>Klijenti:</b></td>
							<td><i class="icon-user"></i> Svi klijenti: <z><?php echo br_statistika('klijenti'); ?></z></td>
							<td><i class="icon-user"></i> Online klijenti: <z>10</z></td>
							<td><i class="icon-user"></i> Klijenti sa serverom: <z><?php echo br_statistika('klijenti_server'); ?></z></td>
							<td><i class="icon-user"></i> Klijenti sa aktivacijom: <z><?php echo br_statistika('klijenti_aktivacija'); ?></z></td>
							<td><i class="icon-user"></i> Aktivni klijenti: <z><?php echo br_statistika('klijenti_aktivni'); ?></z></td>
						</tr>
						<tr>
							<td><b>Tiketi:</b></td>
							<td><i class="icon-edit"></i> Svi tiketi: <z>24</z></td>
							<td><i class="icon-edit"></i> Novi tiketi: <z>0</z></td>
							<td><i class="icon-edit"></i> Odgovoreni tiketi: <z>24</z></td>
							<td><i class="icon-edit"></i> Zaključani tiketi: <z>0</z></td>
							<td><i class="icon-edit"></i> Prosleđeni tiketi: <z>0</z></td>
						</tr>
						<tr style="background: white">
							<td><b>Uplate:</b></td>
							<td><i class="icon-credit-card"></i> Na čekanju: <z><?php echo br_statistika('uplate_nacekanje'); ?></z></td>
							<td><i class="icon-credit-card"></i> Odbijene: <z><?php echo br_statistika('uplate_odbijene'); ?></z></td>
							<td><i class="icon-credit-card"></i> Validne: <z><?php echo br_statistika('uplate_validne'); ?></z></td>
							<td colspan="2"><i class="icon-credit-card"></i> Zarada: <z><?php echo br_statistika('zarada'); ?></z></td>
						</tr>
					</tbody>
				</table>
				Admini
				<table class="table table-striped table-bordered">
					<tbody>
<?php					while($row = mysql_fetch_array($admini)) 
						{ 
							
?>
						<tr style="background: white">
							<td><a target="_blank" href="admin_pregled.php?id=<?php echo $row['id']; ?>"><?php echo admin_ime($row['id']); ?></a></td>
							<td><i class="icon-gamepad"></i> Tiket odgovori: <z><?php echo query_numrows("SELECT * FROM `tiketi_odgovori` WHERE `admin_id` = '{$row['id']}'"); ?></z></td>
							<td><i class="icon-gamepad"></i> Reputacija: <?php echo reputacija($row['id']); ?></td>
							<td><i class="icon-gamepad"></i> Status: <?php echo get_status($row['lastactivity']); ?></td>
							<td><z><?php echo $row['lastactivityname']; ?> - pre <?php echo time_elapsed_A($nowtime-$row['lastactivity']); ?></z></td>
						</tr>	
<?php					} ?>
					</tbody>
				</table>
			</div>
		</div><br /><br />
      	<div class="span6" style="width: 580px;">			
			
			<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-signal"></i>				
					<h3>Chat</h3>
					<input type="text" placeholder="Zabranjen spam i vredjanje..." style="margin-top: 5px;" onsubmit="Chat_Send()" id="chat_text" />
				</div> <!-- /widget-header -->				
				<div class="widget-content">				
					<div id="chat_main">
						<div id="chat_messages">
							<ul>

							</ul>
						</div>		
					</div>
			<?php 	if(pristup()){	?>
					<input class="btn btn-primary" type="button" value="Izbrisi sve poruke" style="margin: 15px 0px 5px 0px;" onclick="Chat_IzbrisiSve()" />
			<?php	}	?>
					<input class="btn btn-primary smajli" type="button" data-content="<?php echo $smajlici; ?>" value="Smajli" style="margin: 15px 0px 5px 0px;" />
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->			
			
      		
	    </div> <!-- /span6 -->
      	
		
		<div class="span6" style="margin: 0; width: 440px;">	
      					
			<div class="widget stacked" style="width: 210px; margin-left: 13px;">
					
				<div class="widget-header">
					<i class="icon-signal"></i>				
					<h3>Admini online</h3>
					
				</div> <!-- /widget-header -->				
				<div class="widget-content">	
					<div id="chat_main">
						<div id="onlinea">
							<ul>
							
							</ul>
						</div>	
					</div>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->
			
			<div class="widget stacked" style="width: 210px; float: right; margin-top: -333px; margin-left: 0; margin-right: 0;">
					
				<div class="widget-header">
					<i class="icon-signal"></i>				
					<h3>Korisnici online</h3>
					
				</div> <!-- /widget-header -->				
				<div class="widget-content">	
					<div id="chat_main">
						<div id="clanovi">
							<ul>
							
							</ul>
						</div>	
					</div>
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->			
					
			
								
	      </div> <!-- /span6 -->

		  	  
<!--
	 		<div class="span12">
		  
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>SMS Logovi - <a href="#">Cela lista</a></h3>
				</div> 
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Klijent User</th>
								<th>Text poruke</th>
								<th>Broj klijenta</th>
								<th>Operator</th>
								<th>Status</th>
								<th>Cijena</th>
								<th>Vrijeme</th>
								<th>Akcija</th>
							</tr>
						</thead>
						<tbody>
					<?php	while($rowsms = mysql_fetch_array($logovisms)) {
								if (strpos($row['message'],'Neuspešan login.') !== false) {
									if(pristup()){
?>
							<tr>
								<td><?php echo $rowsms['id']; ?></td>
								<td><?php echo $rowsms['keyword']; ?> <?php echo $rowsms['message']; ?></td>
								<td><a href="admin_pregled.php?id=<?php echo $row['adminid']; ?>"><?php echo log_ime($rowsms['adminid']); ?></a></td>
								<td><?php echo $rowsms['ip']; ?></td>
								<td><?php echo vreme($rowsms['time']); ?></m></td>
							</tr>	
<?php									
									}
								}
								else
								{
					?>
							<tr>
								<td>#<?php echo $rowsms['id']; ?></td>
								<td><?php echo $rowsms['username']; ?></td>
								<td><?php echo $rowsms['keyword']; ?> <?php echo $rowsms['message']; ?></td>
								<td><?php echo $rowsms['sender']; ?></td>
								<td><?php echo $rowsms['operator']; ?></td>
								<td><?php echo $rowsms['status']; ?></td>
								<td><?php $fee = 0.00 * $rowsms['price'];
	  $rowsms['price'] = round($rowsms['price'] - $fee,2); echo $rowsms['price']; ?> <?php echo $rowsms['currency']; ?> (-0%)</td>
								<td><?php echo vreme($rowsms['time']); ?></m></td>
								<td class="td-actions" style="width: 92px;"><form action="process.php" method="POST">
										<input type="hidden" name="task" value="sms-ok" />
										<input type="hidden" name="id" value="<?php echo $rowsms['id'] ?>" />
										<button title="Potvrdi SMS" type="submit" class="btn btn-small btn-success">
										<i class="btn-icon-only icon-ok"></i>										
										</button>
									</form>
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="sms-failed" />
										<input type="hidden" name="id" value="<?php echo $rowsms['id'] ?>" />
										<button title="Odbij sms" type="submit" class="btn btn-small btn-danger">
										<i class="btn-icon-only icon-remove"></i>										
										</button>
									</form></td>
							</tr>	
					<?php		}
							}	?>
							</tbody>
						</table>
					
				</div>
			
			</div>
      	
      </div> --> 
<?php									
						
			if(pristup()){
								
?>
	  <div class="span12">
		  
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Billing logs - <a href="#">Cela lista</a></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Log</th>
								<th>Admin</th>
								<th>Time</th>
							</tr>
						</thead>
						<tbody>
					<?php	
					
					$logovi2 = mysql_query("SELECT * FROM `billing_log` WHERE `clientid` ORDER BY `logid` DESC LIMIT 20");
					
					while($row2 = mysql_fetch_array($logovi2)) {
						
						$datum = maketime($row2['time'],1);
                        $time = maketime($row2['time']);
					 
					    $admin = $row2['adminid'];
						
						if ($admin == "0") 
							$admin = "Panel";
							
?>
							<tr>
								<td><?php echo $row2['logid']; ?></m></td>
								<td><?php echo $row2['text']; ?></m></td>
								<td><a href="admin_pregled.php?id=<?php echo $admin; ?>"><?php echo log_ime($admin); ?></a></td>
								<td><?php echo $datum; ?></m></td>
							</tr>	
<?php									
						
						
						}
								
?>
							</tbody>
						</table>
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->			
      	
      </div> <!-- /row -->
	 <?php									
						
						
						}
								
?>




		<div class="span12">
		  
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Logovi - <a href="./logovi">Cela lista</a></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Poruka</th>
								<th>Admin</th>
								<th>IP</th>
								<th>Vreme</th>
							</tr>
						</thead>
						<tbody>
					<?php	while($row = mysql_fetch_array($logovi)) {
								if (strpos($row['message'],'Neuspešan login.') !== false) {
									if(pristup()){
?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo $row['message']; ?></td>
								<td><a href="admin_pregled.php?id=<?php echo $row['adminid']; ?>"><?php echo log_ime($row['adminid']); ?></a></td>
								<td><?php echo $row['ip']; ?></td>
								<td><?php echo vreme($row['vreme']); ?></m></td>
							</tr>	
<?php									
									}
								}
								else
								{
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo $row['message']; ?></td>
								<td><a href="admin_pregled.php?id=<?php echo $row['adminid']; ?>"><?php echo log_ime($row['adminid']); ?></a></td>
								<td><?php echo $row['ip']; ?></td>
								<td><?php echo vreme($row['vreme']); ?></m></td>
							</tr>	
					<?php		}
							}	?>
							</tbody>
						</table>
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->			
      	
      </div> <!-- /row -->
	  <div class="span12">
		  
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Cronovi</h3>
				</div>
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Cron Name</th>
								<th>Last Cron Update</th>
							</tr>
						</thead>
						<tbody><?php
						while($row = mysql_fetch_array($crons)) { ?>
							<tr>
								<td><?php echo $row['id']; ?></td>
								<td><?php echo $row['cron_name'].".php"; ?></m></td>
								<td><?php echo formatDate($row['cron_value']); ?></a></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div> <!-- /widget-content -->
			</div> <!-- /widget -->			
		</div> <!-- /row --> 
	</div>
<?php
include("assets/footer.php");
?>
