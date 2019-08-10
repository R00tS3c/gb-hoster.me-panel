<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$fajl = "tiket";

if(empty($_GET['id']) or !is_numeric($_GET['id'])) {
	header("Location: index.php");
}

$tiket_id = mysql_real_escape_string($_GET['id']);

$naslov = "Tiket - #".$tiket_id;

$tiket = mysql_query("SELECT * FROM `billing_tiketi` WHERE `id` = '$tiket_id'");

$tiket_inf = mysql_fetch_array($tiket);

$user_inf = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$tiket_inf['user_id']."'"));

$tiket_odgovori = mysql_query("SELECT * FROM `billing_tiketi_odgovori` WHERE `tiket_id` = '$tiket_id' ORDER BY `id`");

if($tiket_inf['status'] == "5" or $tiket_inf['status'] == "8" or $tiket_inf['status'] == "1"){
	mysql_query("UPDATE `tiketi` SET `status` = '4' WHERE `id` = '$tiket_id'");
}

include("assets/header.php");

$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Pregled tiketa #'.$tiket_id.'" WHERE id="'.$_SESSION["a_id"].'"');

$tiket_gledaju = mysql_query("SELECT * FROM admin WHERE lastactivityname = 'Pregled tiketa #".$tiket_id."'");

if(mysql_num_rows($tiket_gledaju) > 1) {
	$ext = ", ";
} else {
	$ext = "";
}


?>
 <link href="assets/js/plugins/faq/faq.css" rel="stylesheet"> 

       <div class="row">
 
		<div class="well well-small span12" id="pregledava">
		Pregledava: <?php	while($row = mysql_fetch_array($tiket_gledaju)) {	
								echo admin_ime_p_l($row['id']).''.$ext;
							}
					?>
		</div>
		
		<input type="hidden" id="tiket_id" value="<?php echo $tiket_id; ?>" ?>
 
      	<div class="span8">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3><?php echo $tiket_inf['naslov']; ?></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<h3>Pretraga</h3>
					
					<br />
					
					<div class="faq-list">
						<ul>
					<?php	while($row = mysql_fetch_array($tiket_odgovori)) {	
								if(!empty($row['admin_id'])) $admin = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '{$row['admin_id']}'");
					?>
							<li class="<?php echo $row['id']; ?>">
								<div id="div_1">
									<div id="avatar">
										<a href="#">
<?php
										if(!empty($row['admin_id'])) {
											echo '<img src="'.admin_avatar($row['admin_id']).'" id="pavatar" />';
										} else {
											echo '<img src="'.user_avatar($row['user_id']).'" id="pavatar" />';
										}
?>
										</a>
									</div>
								</div>
								<div id="div_3">
							<?php 	if(!empty($row['admin_id'])) {
										echo admin_ime_p_l($row['admin_id']);
									} else {
										echo user_imep($row['user_id']);
									}	?>
									 kaže: <span style="float: right; color:#9BA6A6; font-size: 11px; font-style: italic;">pre <?php echo time_elapsed_A($nowtime-$row['vreme_odgovora']).' - '.vreme($row['vreme_odgovora']); ?></span> <br />
									<div id="poruka">
										<?php echo makeClickableLinks($row['odgovor']); ?>
<?php 
										if(!empty($row['admin_id']) AND !empty($admin['signature'])) { echo'<hr><font style="color: rgba(0,0,0,0.6); font-size: 11px;">'.$admin['signature'].'</font>'; } ?>
									</div>
									
									<button class="btn btn-mini btn-primary" type="button" style="float: right;" onclick="izbrisiKomentar_Tiket(<?php echo $row['id']; ?>)">Izbriši</button>
								</div>
							</li>	
					<?php	}	?>
							<h5 style="margin: 3px 10px;padding: 0;"><m>ODGOVOR:</m> <span id="greskakoment"></span></h3>
							<textarea rows="7" id="odgtextareat" class="tiketkoment"></textarea>
							<input type="hidden" id="tiketid" value="<?php echo $tiket_id; ?>" />
							<input type="hidden" id="adminid" value="<?php echo $_SESSION['a_id']; ?>" />
							<input type="hidden" id="vreme" value="<?php echo time(); ?>" />
							<div class="btn btn-primary" onclick="dodajKomentar_BillingTiket()" style="margin: 0 10px 10px 10px;">Pošalji</div>						
						</ul>
					</div>
					
					
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->	
			
	    </div> <!-- /span8 -->

	    <div class="span4">
					
			<div class="widget widget-plain">
				
				<div class="widget-content">
				
					<a href="javascript:;" class="btn btn-large btn-support-ask">Status: <?php echo status_tiketa($tiket_inf['id']); ?></a>	
					
			<?php if($tiket_inf['status'] == "3") { ?>
					<form action="process.php" method="POST">
						<input type="hidden" name="task" value="billing_odkljucaj_tiket" />
						<input type="hidden" name="tiketid" value="<?php echo $tiket_id; ?>" />
						<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-success btn-support-ask">Otkljucaj tiket</button>
					</form>			
			<?php } else { ?>
					<form action="process.php" method="POST">
						<input type="hidden" name="task" value="billing_zakljucaj_tiket" />
						<input type="hidden" name="tiketid" value="<?php echo $tiket_id; ?>" />
						<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-danger btn-support-ask">Zakljucaj tiket</button>
					</form>
		      <?php } ?>
			  
					<a id="t_ban" href="javascript:;" class="btn btn-large btn-inverse btn-support-contact">Banuj klijenta</a>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->
			
			
			
			<div class="widget stacked widget-box">
				
				<div class="widget-header">	
					<img src="<?php echo user_avatar($user_inf['klijentid']); ?>" style="margin: -2px -5px 0 3px;" width="35" height="35" />
					<h3>
					#<?php echo $user_inf['klijentid'].' - '.$user_inf['ime'].' '.$user_inf['prezime']; ?>
					</h3>			
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					<p>Status: <m><?php echo get_status($user_inf['lastactivity']); ?></m></p>
					<p>Ime i prezime: <m><?php echo $user_inf['ime'].' '.$user_inf['prezime']; ?></m></p>
					<p>E-mail: <m><?php echo $user_inf['email']; ?></m></p>
					<p>Novac: <m><?php echo novac($user_inf['novac'], $user_inf['zemlja']); ?></m></p>
					<p>Zemlja: <m><?php echo $user_inf['zemlja']; ?></m></p>
					<p>Ip: <m><?php echo $user_inf['lastip']; ?></m></p>
					<p>Host: <m><?php echo $user_inf['lasthost']; ?></m></p>
				</div> <!-- /widget-content -->
				
			</div> <!-- /widget -->
			
		</div> <!-- /span4 -->
      	
      	
      	
      </div> <!-- /row -->

	  
<?php
include("assets/footer.php");
?>