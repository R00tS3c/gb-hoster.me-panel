<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled klijent profila";
$fajl = "klijenti";

if(empty($_GET['id']) or !is_numeric($_GET['id'])) {
	header("Location: index.php");
}

$id = mysql_real_escape_string($_GET['id']);

if(query_numrows("SELECT * FROM `klijenti` WHERE `klijentid` = '{$id}'") == 0)
{
	$_SESSION['msg-type'] = "error";
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Taj klijent ne postoji u bazi.";
	header("Location: klijenti.php");
	die();
}

$klijent = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$id."'"));

$sql =  "SELECT u.ime uime, u.prezime uprezime, u.avatar uavatar, u.klijentid uid, c.id cid, c.komentar ccomment, c.vreme cvreme, c.klijentid cuid, c.profilid profilid ".
		"FROM klijenti_komentari c, klijenti u ".
		"WHERE c.klijentid = '".$klijent['klijentid']."' AND u.klijentid = '".$klijent['klijentid']."' ".
		"ORDER BY c.vreme DESC ".
		"LIMIT 15";
						
$komentar = mysql_query($sql) or die(mysql_error());

$logovi = mysql_query("SELECT * FROM `logovi` WHERE `clientid` = '".$id."' ORDER BY `id` DESC LIMIT 20");

$serveri = mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '".$id."'");

$naruceniserveri = mysql_query("SELECT * FROM `serveri_naruceni` WHERE `klijentid` = '".$id."'");
$billingsms = mysql_query("SELECT * FROM `billing_sms` WHERE `username` = '".$klijent['username']."'");

$uplate = mysql_query("SELECT * FROM `billing` WHERE `klijentid` = '".$id."' ORDER BY `id` DESC LIMIT 7");

include("assets/header.php");


?>
 <link href="assets/js/plugins/faq/faq.css" rel="stylesheet">
<?php
		if($klijent['status'] == "Aktivacija") {
?>
		<div class="row">
			<div class="span12">
				<div class="alertt alert-info">
					<h4 class="alert-heading">Klijent nije aktiviran</h4>
					Ovaj klijent nije aktivirao svoj nalog, da bi mu aktivirali kliknite 
					<form action="process.php" method="post">
						<input type="hidden" name="task" value="klijent-aktiviraj" />
						<input type="hidden" name="klijentid" value="<?php echo $klijent['klijentid']; ?>" />
						<input type="submit" value="Ovde" style="background: none; color: rgb(58, 135, 173); font-size: 11px; border: 0;"  />
					</form>
				</div>
			</div>
		</div>
<?php
		}
?>
       <div class="row">
		<div class="span8">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Pregled klijent profila</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">	
					<table id="pregledprofilat">
						<tbody><tr>
							<th width="165px" style="height: 0;"></th>
							<th width="250px" style="height: 0;"></th>
							<th style="height: 0;"></th>
						</tr>
						<tr>
							<td>
								<div id="pavatar">
									<img src="<?php echo user_avatar($klijent['klijentid']); ?>" id="pimg">
								</div>	
							</td>
							<td>
								<p>Ime i prezime: <m><?php echo user_imep($klijent['klijentid']); ?></m></p>
								<p>Username: <m><?php echo $klijent['username']; ?></m></p>
								<p>E-mail: <m><?php echo $klijent['email']; ?></m></p>
								<p>Novac: <m><?php echo getMoney($klijent['klijentid'], true); ?></m></p>
								<p>Status: <m><?php echo $klijent['status']; ?></m></p>
								<p>Online status: <m><?php echo get_status($klijent['lastactivity']); ?></m></p>
							</td>
							<td>
								<p>Zadnji ip: <m><?php echo $klijent['lastip']; ?></m></p>
								<p>Ip host: <m><?php echo $klijent['lasthost']; ?></m></p>
								<p>Zadnji login: <m><?php echo $klijent['lastlogin']; ?></m></p>	
								<p>Zadnja akcija: <m><?php echo vreme($klijent['lastactivity']); ?></m></p>		
								<p>Registrovan: <m><?php echo $klijent['kreiran']; ?></m></p>	
								<p>Drzava: <m><?php echo $klijent['zemlja']; ?></m></p>	
							</td>
						</tr>
					</tbody></table>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->					
			
	    </div>		
	    <div class="span4">
					
			<div class="widget widget-plain">
				
				<div class="widget-content">
				
					<a href="javascript:;" class="btn btn-large btn-support-ask">Status: <?php if($klijent['banovan'] == "1") echo '<span style="color: red;">Banovan</span>'; else echo get_status($klijent['lastactivity']); ?></a>
					
					<form action="process.php" method="POST">
						<input type="hidden" name="task" value="klijent_delete" />
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<button type="submit" style="width: 100%" class="btn btn-large btn-danger btn-support-ask">Izbrisi klijenta</button>	
					</form>					

					<a href="#klijentedit" data-toggle="modal" class="btn btn-large btn-info btn-support-ask">Edit klijenta</a>
					
					<a id="gp_ban" href="javascript:;" class="btn btn-large btn-inverse btn-support-contact">Banuj klijenta</a>	
					
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->
	
		</div> <!-- /span4 -->
      	
     	<div class="span6">
		
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista servera <a class="btn btn-mini btn-success" href="serveradd.php?klijent=<?php echo $id; ?>">+</a></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="20px" class="tip" title="test">ID</th>
								<th>Ime servera</th>
								<th>Ip adresa</th>
								<th>Igra</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($serveri) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema servera.</m></td></tr>';
							}
							while($row = mysql_fetch_array($serveri)) {	
								$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$row['user_id']."'");
								$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
								
								if($row['igra'] == "1") { $igrai = "Counter-Strike 1.6"; $igra = 'game-cs.png'; }
								else if($row['igra'] == "2") { $igrai = "San Andreas Multiplayer"; $igra = 'game-samp.png'; }
								else if($row['igra'] == "3") { $igrai = "Minecraft"; $igra = 'game-minecraft.png'; }
								else if($row['igra'] == "4") { $igrai = "Call of Duty 4"; $igra = 'game-cod4.png'; }
								else if($row['igra'] == "6") { $igrai = "TeamSpeak3"; $igra = 'game-ts3.png'; }
								else if($row['igra'] == "7") { $igrai = "FastDL"; $igra = 'game-fdl.png'; }
								else if($row['igra'] == "8") { $igrai = "SinusBot"; $igra = 'game-sinusbot.png'; }
								else if($row['igra'] == "9") { $igrai = "FiveM"; $igra = 'game-fivem.png'; }
																
								$istice = strtotime($row['istice']);
								$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><a href="srv-pocetna.php?id=<?php echo $row['id']; ?>"><m><?php echo $row['name']; ?></m></a></td>
								<td><?php if($row['igra'] == "7") echo $box['fdl_link']."/".$row['username']."/cstrike/"; else echo $boxip['ip'].":<m>".$row['port']."</m>"; ?></td>
								<td><img src="./assets/img/<?php echo $igra; ?>" style="width:16px;height:16px;" /></td>
								<td><?php echo srv_status($row['status']); ?></m></td>									
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->					
      	
		</div>
		
     	<div class="span6">
		
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista uplata</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="20px" class="tip" title="test">ID</th>
								<th>Iznos</th>
								<th>Datum</th>
								<th>Vreme</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($uplate) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema uplate.</m></td></tr>';
							}
							while($row = mysql_fetch_array($uplate)) {	
								$tiket = query_fetch_assoc("SELECT * FROM `tiketi` WHERE `billing` = '".$row['id']."'");
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><a href="tiket.php?id=<?php echo $tiket['id']; ?>"><m><?php echo getMoney($klijent['klijentid'], true, $row['iznos'] ); /*novac($row['iznos'], $klijent['zemlja']);*/  ?></m></a></td>
								<td><a href="tiket.php?id=<?php echo $tiket['id']; ?>"><m><?php echo $row['datum']; ?></m></a></td>
								<td><?php echo vreme($row['vreme']); ?></td>
								<td><?php echo $row['status']; ?></td>									
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->					
      	
		</div>		

		<div class="span12">
		
     		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Komentari</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					<div id="akomentari">
						<ul>
					<?php	
							if(mysql_num_rows($komentar) == 0) {
								echo '<li style="padding: 20px;"><m>Trenutno nema komentara.</m></li>';
							}
							while($row = mysql_fetch_array($komentar)) {											
							?>
							<li id="<?php echo $row['cid']; ?>">
								<div id="div_1">
									<div id="avatar">
										<a href="admin_pregled.php?id=<?php echo $row['uid']; ?>"><img src="<?php echo user_avatar($row['uid']); ?>" id="pavatar" /></a>
									</div>
								</div>
								<div id="div_2">
									<?php echo user_imep($row['cuid']); ?> kaže: <span style="float: right; color:#9BA6A6; font-size: 11px; font-style: italic;">pre <?php echo time_elapsed_A($nowtime-$row['cvreme']).' - '.vreme($row['cvreme']); ?></span> <br />
									<div id="poruka"><?php echo $row['ccomment']; ?></div>
									<?php	if($_SESSION['a_id'] == $row['profilid'] or pristup()) {	?>
									<button class="btn btn-mini btn-primary" type="button" style="float: right;" onclick="izbrisiKomentarc('<?php echo $row['cid']; ?>')">Izbriši</button>
									<?php	}	?>
								</div>
							</li>	
					<?php	}	?>
						</ul>
					</div>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->				
<?php	if(pristup()) {	?>
 

<?php	}	?>			
			
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Logovi - <a href="klogovi.php?id=<?php echo $id; ?>">Cela lista</a></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="15px" class="tip" title="test">ID</th>
								<th>Poruka</th>
								<th>IP</th>
								<th>Vreme</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($logovi) == 0) echo '<tr><td colspan="5">Trenutno nema logova za ovog klijenta.</td></tr>';
							while($row = mysql_fetch_array($logovi)) 
							{	
								$row['message'] = str_replace("gp-srv-pocetna.php", "srv-pocetna.php", $row['message']);
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo $row['message']; ?></td>
								<td><?php echo $row['ip']; ?></td>
								<td><?php echo vreme($row['vreme']); ?></m></td>
							</tr>	
					<?php	}	?>
							</tbody>
						</table>
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->		




<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Naruceni serveri</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="15px" class="tip" title="test">ID</th>
								<th>Igra</th>
								<th>Slotovi</th>
								<th>Cijena</th>
                                                                <th>Status</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($naruceniserveri) == 0) echo '<tr><td colspan="5">Trenutno nema narucenih servera za ovog klijenta.</td></tr>';
							while($row = mysql_fetch_array($naruceniserveri)) 
							{	
								$row['igra'] = str_replace("1", "Cs 1.6", $row['igra']);
                                                                $row['igra'] = str_replace("2", "SAMP", $row['igra']);
                                                                $row['igra'] = str_replace("3", "MC", $row['igra']);
                                                                $row['igra'] = str_replace("4", "COD", $row['igra']);

					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo $row['igra']; ?></td>
								<td><?php echo $row['slotovi']; ?></td>
                                                                <td><?php echo $row['cena']; ?>	&euro;</td>
								<td><?php echo $row['status']; ?></m></td>
							</tr>	
					<?php	}	?>
							</tbody>
						</table>
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->	



<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>SMS Uplate</h3>
				</div> <!-- /widget-header -->
				
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
					<?php	
							if(mysql_num_rows($billingsms) == 0) echo '<tr><td colspan="5">Trenutno nema narucenih servera za ovog klijenta.</td></tr>';
							while($rowsms = mysql_fetch_array($billingsms)) 
							{	?>
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
					<?php	}	?>
							</tbody>
						</table>
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->	

			
			
	    </div> <!-- /span12 -->   
		




	  
<?php
include("assets/footer.php");
?>