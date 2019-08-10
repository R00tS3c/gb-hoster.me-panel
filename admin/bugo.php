<?php
session_start();

include("konfiguracija.php");
include("includes.php");

samo_vlasnik($_SESSION['a_id']);

$naslov = "Bug";
$fajl = "bug";

if(empty($_GET['id']) or !is_numeric($_GET['id'])) {
	header("Location: index.php");
}

$tiket_id = mysql_real_escape_string($_GET['id']);

$tiket = mysql_query("SELECT * FROM `bug` WHERE `id` = '$tiket_id'") or die(mysql_error());

$tiket_inf = mysql_fetch_array($tiket);

$user_inf = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$tiket_inf['klijentid']."'"));

include("assets/header.php");

$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Pregled bug reporta #'.$tiket_id.'" WHERE id="'.$_SESSION["a_id"].'"');



?>
 <link href="assets/js/plugins/faq/faq.css" rel="stylesheet"> 

       <div class="row">

      	<div class="span8">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3><?php echo $tiket_inf['naslov']; ?></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					<div class="faq-list">
						<ul>
							<li>
								<div id="div_1">
									<div id="avatar">
										<a href="#">
<?php
											echo '<img src="../avatari/'.user_avatar($user_inf['klijentid']).'" id="pavatar" />';
?>										
										</a>
									</div>
								</div>
								<div id="div_3">
<?php
										echo user_imep($user_inf['klijentid']);
?>
									 kaže: <span style="float: right; color:#9BA6A6; font-size: 11px; font-style: italic;">pre <?php echo time_elapsed_A($nowtime-$tiket_inf['vreme']).' - '.vreme($tiket_inf['vreme']); ?></span> <br />
									<div id="poruka"><?php echo $tiket_inf['text']; ?></div>

								</div>
							</li>
							<form method="post" action="process.php">
								<input type="hidden" name="task" value="emailbug" />
								<h5 style="margin: 3px 10px;padding: 0;"><m>ODGOVOR:</m></h3>
								<textarea style="width: 660px; margin-left: 10px;" rows="7" name="odgovor"></textarea>
								<input type="hidden" name="id" value="<?php echo $tiket_inf['id']; ?>" />
								<input type="hidden" name="email" value="<?php echo $user_inf['email']; ?>" />
								<input type="hidden" name="text" value="<?php echo $tiket_inf['text']; ?>" />
								<input type="hidden" name="naslov" value="<?php echo $tiket_inf['naslov']; ?>" />
								<input type="hidden" name="klijentid" value="<?php echo $tiket_inf['klijentid']; ?>" />
								<button class="btn btn-primary" type="submit" style="margin: 0 10px 10px 10px;">Pošalji</button>
							</form>
						</ul>
					</div>
					
					
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->	
			
	    </div> <!-- /span8 -->

	    <div class="span4">
			
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
