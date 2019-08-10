<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled profila";
$fajl = "mojprofil";

// PROFIL INFO
$id = $_SESSION['a_id'];
$sql = "SELECT * FROM admin WHERE id = '$id'";
$res = mysql_query($sql) or die(mysql_error());
	
$podatke = mysql_fetch_assoc($res);


if($podatke['status'] == "admin") {
	$rank = "<span style='color: " . $podatke['boja'] . "'>Vlasnik</span>";
} else if($podatke['status'] == "support") {
	$rank = "<span style='color: " . $podatke['boja'] . "'>Radnik</span>";
}

$komentari = mysql_query("SELECT * FROM `komentari` WHERE profilid = '".$_SESSION['a_id']."' ORDER BY `id` DESC");

$brojodg = query_numrows("SELECT * FROM `tiketi_odgovori` WHERE `admin_id` = '{$_SESSION['a_id']}'");

include("assets/header.php");
?>
      <div class="row">
      	
      	<div class="span6">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Pregled admin profila</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">	
					<table id="pregledprofilat">
						<tr>
							<th width="165px"></th>
							<th></th>
						</tr>
						<tr>
							<td>
								<div id="pavatar">
									<img src="<?php echo admin_avatar($id); ?>" id="pimg" />
								</div>	
							</td>
							<td>
								<p>Ime i prezime: <m><?php echo admin_ime_p_l($id); ?></m></p>
								<p>Username: <m><?php echo $podatke['username']; ?></m></p>
								<p>E-mail: <m><?php echo $podatke['email']; ?></m></p>
								<p>Rank: <m><?php echo $rank; ?></m></p>
								<p>Zadnja akcija: <m><?php echo $podatke['lastactivityname']; ?> - pre <?php echo time_elapsed_A($nowtime-$podatke['lastactivity']); ?></m></p>
								<p>Status: <m><?php echo get_status($podatke['lastactivity']); ?></m></p>
							</td>
						</tr>
					</table>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->					
			
	    </div> <!-- /span12 -->     
		
     	<div class="span6">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Statistika</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content" id="pregledprofil">	
					<p>Broj odgovora u tiketima: <m><a href="tiket_lista.php?vrsta=admin&id=<?php echo $id; ?>"><?php echo $brojodg; ?></a></m></p>
					<p>Online: <m>Uskoro</p>
					<p>Reputacija: <?php echo reputacija($id); ?></p>
					<p>Rank: <m><?php echo $rank; ?></m></p>
					<p>Zadnja akcija: <m><?php echo $podatke['lastactivityname']; ?> - pre <?php echo time_elapsed_A($nowtime-$podatke['lastactivity']); ?></m></p>
					<button style="margin-top: 5px;" type="submit" class="btn btn-warning btn" onclick="location = './profil'">
						<i class="icon-wrench" style="line-height: 19px;"></i> Podešavanje profila
					</button>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->					
			
	    </div> <!-- /span6 -->  

<div class="span6">
		
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista kreiranih servera</h3>
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
							$serveri = mysql_query("SELECT * FROM `serveri` WHERE `aid` = '".$id."'");
							if(mysql_num_rows($serveri) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema kreiranih servera.</m></td></tr>';
							}
							while($row = mysql_fetch_array($serveri)) {	
								$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$row['user_id']."'");
								$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
								
								if($row['igra'] == "1") { $igrai = "Counter-Strike 1.6"; $igra = 'game-cs.png'; }
								else if($row['igra'] == "2") { $igrai = "San Andreas Multiplayer"; $igra = 'game-samp.png'; }
								else if($row['igra'] == "3") { $igrai = "Minecraft"; $igra = 'game-minecraft.png'; }
								else if($row['igra'] == "4") { $igrai = "Call of Duty 4"; $igra = 'game-cod4.png'; }
								else if($row['igra'] == "7") { $igrai = "FastDL"; $igra = 'game-fdl.png'; }
																
								$istice = strtotime($row['istice']);
								$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><a href="srv-pocetna.php?id=<?php echo $row['id']; ?>"><m><?php echo $row['name']; ?></m></a></td>
								<td><?php if($row['igra'] == "7") echo $box['fdl_link']."/".$row['username']."/cstrike/"; else echo $boxip['ip'].":<m>".$row['port']."</m>"; ?></td>
								<td><img src="./assets/img/<?php echo $igra; ?>" /></td>
								<td><?php echo srv_status($row['status']); ?></m></td>									
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
							<h5 style="margin: 3px 10px;padding: 0;"><m>ODGOVOR:</m> <span id="greskakoment"></span></h3>
							<textarea rows="7" id="odgtextarea" class="komentar"></textarea>
							<input type="hidden" id="admin" value="<?php echo $_SESSION['a_id']; ?>" />
							<input type="hidden" id="adminid" value="<?php echo $_SESSION['a_id']; ?>" />
							<input type="hidden" id="vreme" value="<?php echo time(); ?>" />
							<div class="btn btn-primary" onclick="dodajKomentar()" style="margin: 0 10px 10px 10px;">Pošalji</div>						
					<?php	
							if(mysql_num_rows($komentari) == 0) {
								echo '<li style="padding: 20px;"><m>Trenutno nema komentara.</m></li>';
							}
							while($row = mysql_fetch_array($komentari)) {	?>
							<li id="<?php echo $row['id']; ?>"<?php if(query_numrows("SELECT * FROM `komentari` WHERE `novo` = '1' AND `id` = '".$row['id']."'") == 1) echo ' style="background: rgba(255,255,0, 0.2);"'; ?>>
								<div id="div_1">
									<div id="avatar">
										<a href="admin_pregled.php?id=<?php echo $row['adminid']; ?>"><img src="<?php echo admin_avatar($row['adminid']); ?>" id="pavatar" /></a>
									</div>
								</div>
								<div id="div_2">
									<a href="admin_pregled.php?id=<?php echo $row['adminid']; ?>"><?php echo admin_ime_p_l($row['adminid']); ?></a> kaže: <?php if(query_numrows("SELECT * FROM `komentari` WHERE `novo` = '1' AND `id` = '".$row['id']."'") == 1) echo ' - NOVO'; ?><span style="float: right; color:#9BA6A6; font-size: 11px; font-style: italic;">pre <?php echo time_elapsed_A($nowtime-$row['vreme']).' - '.vreme($row['vreme']); ?></span> <br />
									<div id="poruka"><?php echo $row['komentar']; ?></div>
									<?php	if($_SESSION['a_id'] == $row['profilid'] or pristup()) {	?>
									<button class="btn btn-mini btn-primary" type="button" style="float: right;" onclick="izbrisiKomentar('<?php echo $row['id']; ?>')">Izbriši</button>
									<?php	}	?>
								</div>
							</li>	
					<?php	}	?>
						</ul>
					</div>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->
			
<?php	if(pristup()) {	
			$logovi = mysql_query("SELECT * FROM `logovi` WHERE `adminid` = '{$id}' ORDER BY `vreme` DESC LIMIT 50");
?>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Logovi</h3>
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
<?php	}	?>				
      	
      </div> <!-- /row -->
</div>
<?php
mysql_query("UPDATE `komentari` SET novo = '0' WHERE profilid = '".$_SESSION['a_id']."'");
include("assets/footer.php");
?>
