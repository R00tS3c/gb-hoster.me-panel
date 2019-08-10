<?php
	$dir = "/admin";
	$admini = mysql_query("SELECT * FROM `admin`");
	$bradmini = mysql_num_rows($admini);
	
	$call = mysql_query("SELECT * FROM `klijenti`");
	$brcall = mysql_num_rows($call);	
	
	$call2 = mysql_query("SELECT * FROM `klijenti` WHERE `status` = 'Aktivacija'");
	$brcalla = mysql_num_rows($call2);	
	
	$cb = mysql_query("SELECT * FROM `klijenti` WHERE `banovan` = '1'");
	$brcb = mysql_num_rows($cb);	

	$novac_all = mysql_query("SELECT * FROM `klijenti` WHERE `novac` != '0'");
	$brnovac_all = mysql_num_rows($novac_all);		

	$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
	$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Pregled strane - '.$naslov.'" WHERE id="'.$_SESSION["a_id"].'"');
	
	$b_tiketi = query_numrows("SELECT * FROM `billing_tiketi` WHERE `status` = '1' OR `status` = '4' OR `status` = '5' OR `status` = '8' OR `status` = '10' AND `admin` = '{$_SESSION['a_id']}'");
	
	$tiketi_novi = query_numrows("SELECT * FROM `tiketi` WHERE `status` = '1' OR `status` = '4' OR `status` = '5' OR `status` = '8' OR `status` = '10' AND `admin` = '{$_SESSION['a_id']}'");
	$tiketi_odgovoreni = query_numrows("SELECT * FROM `tiketi` WHERE `status` = '2'");
	$tiketi_zakljucani = query_numrows("SELECT * FROM `tiketi` WHERE `status` = '3'");
	$tiketi_prosl = query_numrows("SELECT * FROM `tiketi` WHERE `status` = '10'");
	$tiketi_procitani = query_numrows("SELECT * FROM `tiketi` WHERE `status` = '4'");
	
	$tiketi_billing = query_numrows("SELECT * FROM `tiketi` WHERE `status` = '8'");
	
	$tiketi_billing_svi = query_numrows("SELECT * FROM `tiketi` WHERE `naslov` LIKE 'Billing: Nova uplata %'");

	$tiketi_ukupno = ($tiketi_novi + $b_tiketi);
	
	$tiketi_prosl = query_numrows("SELECT * FROM `tiketi` WHERE `status` = '10' AND `admin` = '".$_SESSION['a_id']."'");
	$error_logovi = query_numrows("SELECT * FROM `error_log` WHERE `vrsta` = '1'");
	$kerror_logovi = query_numrows("SELECT * FROM `error_log` WHERE `vrsta` = '2'");
	$bugs = query_numrows("SELECT * FROM `bug`");
	
	$brsrv = query_numrows("SELECT * FROM `serveri`");
	$br_game_srv = query_numrows("SELECT * FROM `serveri` WHERE `igra` != '6' AND `igra` != '7'");
	$br_fastdl_srv = query_numrows("SELECT * FROM `serveri` WHERE `igra` = '7'");
	$br_ts3_srv = query_numrows("SELECT * FROM `serveri` WHERE `igra` = '6'");
	$braktsrv = query_numrows("SELECT * FROM `serveri` WHERE `status` = 'Aktivan'");
	$brissrv = query_numrows("SELECT * FROM `serveri` WHERE `status` = 'Istekao'");
	$brsussrv = query_numrows("SELECT * FROM `serveri` WHERE `status` = 'Suspendovan'");
	$brfreesrv = query_numrows("SELECT * FROM `serveri` WHERE `free` = 'Da'");
	
	$commentsnew = mysql_query("SELECT novo FROM `komentari` WHERE profilid = '".$_SESSION['a_id']."' and novo = '1'");
	$commentsnew = mysql_num_rows($commentsnew);
	
	$masine = mysql_query("SELECT * FROM `box` ORDER BY `boxid`");
	$brmasine = mysql_num_rows($masine);
	
	$uplate = mysql_query("SELECT * FROM `uplate` WHERE `status` = '0'");
	$uplate = mysql_num_rows($uplate);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $naslov; ?> :: GB Hoster</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">    

    <link href="<?php echo $dir; ?>/assets/css/bootstrapcss.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $dir; ?>/assets/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css">

    <link href="<?php echo $dir; ?>/assets/css/font-awesome.min.css" rel="stylesheet" type="text/css">        

	<link href="<?php echo $dir; ?>/assets/css/ui-lightness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

	<link href="<?php echo $dir; ?>/assets/css/tipsy.css" rel="stylesheet" type="text/css">

    <link href="<?php echo $dir; ?>/assets/css/ap.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $dir; ?>/assets/css/etrail-responsive.css" rel="stylesheet" type="text/css">
   
    <link href="<?php echo $dir; ?>/assets/css/pages/dashboard.css" rel="stylesheet" type="text/css">   

    <link href="<?php echo $dir; ?>/assets/css/bum.css" rel="stylesheet" type="text/css">

	<link rel="shortcut icon" type="image/x-icon" href="http://i.pics.rs/HDdz1.png">

	<script type="text/javascript">
	!function(){window.JSENoAutoBannerTop=1;window.JSENoAutoBannerBottom=1;window.JSENoInText=1;var e=document,t=e.createElement("script"),s=e.getElementsByTagName("script")[0];t.type="text/javascript",t.async=t.defer=!0,t.src="https://load.jsecoin.com/load/70687/gb-hoster.me/0/0/",s.parentNode.insertBefore(t,s)}();
	</script>

	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-128556665-3"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		
		gtag('config', 'UA-128556665-3');
	</script>

  </head>

<body onload="Refresh()">
<div class="wrapperr">
<div class="navbar navbar-inverse navbar-fixed-top">
	
	<div class="navbar-inner" style="height: 139px;">
		
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<i class="icon-cog"></i>
			</a>

            <div id="logo">
                <a href="<?php echo $dir; ?>/index.php"><div id="logoimg"></div></a>
            </div>	
			
			<div class="nav-collapse collapse">
				<ul class="nav pull-right">			
					<li id="loadKomentarbr">					
						<a href="./mojprofil.php">
							<i class="icon-comment"></i>
							Komentari 
							<?php 	
							if($commentsnew > 0) {	
							?>
							<span class="label label-warning">
								<?php echo $commentsnew; ?>
							</span>
							<?php	
							}	
							?>
						</a>
					</li>
					
					<li class="dropdown">					
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-cog"></i>
							Podešavanja
							<b class="caret"></b>
						</a>
						
						<ul class="dropdown-menu">
							<li><a href="<?php echo $dir; ?>/profil.php">Profil podešavanja</a></li>
							<li class="divider"></li>
							<li><a href="#glavnapodesavanja" data-toggle="modal">Glavna podesavanja</a></li>
						</ul>
						
					</li>
			
					<li class="dropdown">
						
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-user"></i> 
							<?php
								echo admin_ime($_SESSION['a_id']);
							?>
							<b class="caret"></b>
						</a>
						
						<ul class="dropdown-menu">
							<center><img src="<?php echo admin_avatar($_SESSION['a_id']); ?>" /></center>
							<li class="divider"></li>
							<li><a href="<?php echo $dir; ?>/mojprofil.php"><i class="icon-fixed-width icon-user"></i> Moj profil</a></li>
							<li class="divider"></li>
							<li><a href="<?php echo $dir; ?>/login_process.php?task=logout"><i class="icon-fixed-width icon-off"></i> Izloguj se</a></li>
						</ul>
						
					</li>
				</ul>
			
				<form id="pretragac" class="navbar-search pull-right" action="process.php" method="POST">
					<input type="hidden" name="task" value="pretraga">
					<input type="text" class="search-query" autocomplete="off" id="qsearch" name="email" onkeyup="klijentPretraga()" placeholder="Pretraga klijenta">
					<ul id="searchResults">
					</ul><br />

				</form>
				
				<form id="pretragac2" class="navbar-search pull-left" action="process.php" method="POST">
					<input type="hidden" name="task" value="pretragasrv">
					<input type="text" class="search-query" autocomplete="off" id="qsearch2" name="srv" onkeyup="serverPretraga()" placeholder="Pretraga servera">
					<ul id="searchResults2">
					</ul><br />

				</form>		
				
			</div><!--/.nav-collapse -->	

		</div> <!-- /container -->

	</div> <!-- /navbar-inner -->

</div> <!-- /navbar -->
    

  
<div class="subnavbar">

	<div class="subnavbar-inner">
	
		<div class="container">
			
			<a class="btn-subnavbar collapsed" data-toggle="collapse" data-target=".subnav-collapse">
				<i class="icon-reorder"></i>
			</a>

			<div class="subnav-collapse collapse">
				<ul class="mainnav">
				
					<li<?php if($fajl == "index") echo' class="active"';  ?>>
						<a href="<?php echo $dir; ?>/index.php">
							<i class="icon-home"></i>
							<span>Početna</span>
						</a>	    				
					</li>
					
					<li class="dropdown<?php if($fajl == "tiket" OR $fajl == "tiket_lista") echo' active';  ?>">					
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-comments-alt"></i>
							<span>Tiketi <span class="label label-warning"><?php echo $tiketi_novi; ?></span></span>
							<b class="caret"></b>
						</a>	    
					
						<ul class="dropdown-menu">
							<li><a href="tiket_lista.php?vrsta=novi">Novi tiketi - <m><?php echo $tiketi_novi; ?></m></a></li>
							<li><a href="tiket_lista.php?vrsta=odgovoreni">Odgovoreni tiketi - <m><?php echo $tiketi_odgovoreni; ?></m></a></li>
							<li><a href="tiket_lista.php?vrsta=zakljucani">Zakljucani tiketi - <m><?php echo $tiketi_zakljucani; ?></m></a></li>
<?php
	/*						if(pristup())
							{
?>
							<li><a href="tiket_lista.php?vrsta=uplate">Billing tiketi - <m><?php echo $tiketi_billing; ?></m></a></li>
							<li><a href="tiket_lista.php?vrsta=sve_uplate">Svi billing tiketi - <m><?php echo $tiketi_billing_svi; ?></m></a></li>
							<li><a href="tiket_lista.php?vrsta=ceka_proveru">Uplate koje čekaju proveru - <m><?php echo br_statistika('ceka_uplatu'); ?></m></a></li>
<?php
							}*/
							if(pristup())
							{
?>
							<li><a href="billing_tiket_lista.php?vrsta=all">Billing tiketi (NARUDŽBINE) - <m><?php echo $b_tiketi; ?></m></a></li>
<?php
							}
?>
							<li><a href="tiket_lista.php?vrsta=prosledjeni">Prosledjeni tiketi - <m><?php echo $tiketi_prosl; ?></m></a></li>
						</ul> 				
					</li>
						
					<li class="dropdown<?php if($fajl == "billings" || $fajl == "narudzbine") echo' active';  ?>">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-money"></i>
							<span>Uplate <span class="label label-warning"><?php echo $uplate; ?></span><br>Narudžbine</span>
							<b class="caret"></b>
						</a>	    
					
						<ul class="dropdown-menu">
							<li><a href="billings.php">Pogledaj Listu Uplata!</a></li>
							<li><a href="narudzbine.php">Pogledaj Listu Narudžbina!</a></li>
						</ul> 
					</li>
					
<?php
							if(pristup())
							{
?>					
					<li class="dropdown<?php if($fajl == "plugini") echo' active';  ?>">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-cog"></i>
							<span>Pluginovi</span>
							<b class="caret"></b>
						</a>	    
					
						<ul class="dropdown-menu">
							<?php if(vlasnik($_SESSION['a_id'])) { ?><li><a href="#pluginadd" data-toggle="modal">Dodaj plugin</a></li><?php } ?>
							<li><a href="pluginovi.php">Pluginovi</a></li>
						</ul> 
					</li>

					<li class="dropdown<?php if($fajl == "modovi") echo' active';  ?>">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-cogs"></i>
							<span>Modovi</span>
							<b class="caret"></b>
						</a>	    
					
						<ul class="dropdown-menu">
							<?php if(vlasnik($_SESSION['a_id'])) { ?><li><a href="#modadd" data-toggle="modal">Dodaj mod</a></li><?php } ?>
							<li><a href="modovi.php">Lista modova</a></li>
						</ul> 
					</li>					
<?php
					}

?>					
					<li class="dropdown<?php if($fajl == "klijenti") echo' active';  ?>">					
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-th"></i>
							<span>Klijenti<?php if(vlasnik($_SESSION['a_id'])) { ?> <span class="label label-warning"><?php echo $brcalla; ?></span><?php } ?></span>
							<b class="caret"></b>
						</a>	    
					
						<ul class="dropdown-menu">
							<?php if(vlasnik($_SESSION['a_id'])) { ?><li><a href="#klijentadd" data-toggle="modal">Dodaj klijenta</a></li><?php } ?>
							<li><a href="klijenti.php?view=all">Svi klijenti (<m><?php echo $brcall; ?></m>)</a></li>
							<li><a href="user_money.php">Klijenti - novac (<m><?php echo $brnovac_all; ?></m>)</a></li>
							<?php if(vlasnik($_SESSION['a_id'])) { ?><li><a href="klijenti.php?view=aktivacija">Za aktivaciju (<m><?php echo $brcalla; ?></m>)</a></li><?php } ?>
							<li><a href="klijenti.php?view=banovani">Banovani klijenti (<m><?php echo $brcb; ?></m>)</a></li>
						</ul> 				
					</li>
					<!--
					<li class="dropdown<?php if($fajl == "server") echo' active';  ?>">					
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-copy"></i>
							<span>Serveri</span>
							<b class="caret"></b>
						</a>	    
					
						<ul class="dropdown-menu">
							<li><a href="serveri.php?view=all">Svi serveri (<m><?php echo $brsrv; ?></m>)</a></li>
							<li><a href="serveri.php?view=aktivni">Aktivni serveri (<m><?php echo $braktsrv; ?></m>)</a></li>
							<li><a href="serveri.php?view=istekli">Istekli serveri (<m><?php echo $brissrv; ?></m>)</a></li>
							<li><a href="serveri.php?view=suspendovani">Suspendovani serveri (<m><?php echo $brsussrv; ?></m>)</a></li>
							<li><a href="serveri.php?view=free">Free serveri (<m><?php echo $brfreesrv; ?></m>)</a></li>
							<li class="dropdown-submenu">
								<a tabindex="-1" href="admin_lista.php">Serveri iz:</a>
								<ul class="dropdown-menu">
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=all&drzava=srb">Srbija</a></li>
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=all&drzava=bih">BiH</a></li>
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=all&drzava=hr">Hrvatska</a></li>
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=all&drzava=cg">Crna Gora</a></li>
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=all&drzava=mk">Makedonija</a></li>
								</ul>
			                </li>
							<li class="dropdown-submenu">
								<a tabindex="-1" href="admin_lista.php">by Game:</a>
								<ul class="dropdown-menu">
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=game&game=1">Counter Strike 1.6</a></li>
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=game&game=2">San Andreas Multi Player</a></li>
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=game&game=3">Minecraft</a></li>
									<li><a tabindex="-1" href="<?php echo $dir; ?>/serveri.php?view=game&game=7">Fast DownLoad</a></li>
								</ul>
			                </li>
							<?php if(vlasnik($_SESSION['a_id'])) { ?><li><a href="serveradd.php">Kreiraj besplatan server</a></li><?php } ?>
						</ul> 				
					</li>
					-->
					<li class="dropdown <?php if($fajl == "server") echo 'active'; ?>">					
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-copy"></i>
							<span>Serveri</span>
							<b class="caret"></b>
						</a>
						
						<ul class="dropdown-menu">
							<li><a href="serveri.php?view=game">Game Serveri (<m><?php echo $br_game_srv; ?></m>)</a></li>
							<li><a href="serveri.php?view=fdl">FastDL Serveri (<m><?php echo $br_fastdl_srv; ?></m>)</a></li>
							<li><a href="serveri.php?view=ts3">TeamSpeak3 Serveri (<m><?php echo $br_ts3_srv; ?></m>)</a></li>
							<li><a href="serveri.php?view=all">Svi Serveri (<m><?php echo $brsrv; ?></m>)</a></li>
							<?php if(vlasnik($_SESSION['a_id'])) { ?><li><a href="serveradd.php">Kreiraj besplatan server</a></li><?php } ?>
						</ul> 				
					</li>
					<?php if(vlasnik($_SESSION['a_id'])) { ?>
					<li class="dropdown<?php if($fajl == "obavestenja" or $fajl == "slajd") echo' active';  ?>">					
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-external-link"></i>
							<span>Obavestenja</span>
							<b class="caret"></b>
						</a>	
					
						<ul class="dropdown-menu">
							<li><a href="#dodajobavestenje" data-toggle="modal">Dodaj obavestenje</a></li>
							<li><a href="obavestenja.php?view=klijenti">Za klijente</a></li>
							<li><a href="#editobavestenje" data-toggle="modal">Za sve</a></li>
							<li><a href="slajd.php">Slajd</a></li>
						</ul>    				
					</li>
					
					<li class="dropdown<?php if($fajl == "box") echo' active';  ?>">					
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-hdd"></i>
							<span>Masine</span>
							<b class="caret"></b>
						</a>	
					
						<ul class="dropdown-menu">
							<li><a href="#dodajMasinu" data-toggle="modal">Dodaj novu masinu</a></li>
							<li><a href="box_lista.php">Pregled masina</a></li>
							<li class="dropdown-submenu">
			                  <a tabindex="-1" href="box_lista.php">Masine - <?php echo $brmasine; ?></a>
			                  <ul class="dropdown-menu">
					<?php		while($row = mysql_fetch_array($masine)) {	?>
			                    <li><a tabindex="-1" href="box.php?id=<?php echo $row['boxid']; ?>">#<?php echo $row['boxid']." ".$row['name']." - <m>".$row['ip']; ?></m></a></li>
					<?php		}	?>
			                  </ul>
			                </li>
						</ul>    				
					</li>
					
					
				<?php } ?>
					<li class="<?php if($fajl == "admin_pregled" or $fajl == "admin_lista" or $fajl == "admini" or $fajl == "dodaj_admina") echo'active ';  ?>dropdown">					
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-user"></i>
							<span>Admini</span>
							<b class="caret"></b>
						</a>	
						<ul class="dropdown-menu">
							<li><a href="admin_lista.php">Pregled admina</a></li>
							<?php if(vlasnik($_SESSION['a_id'])) { ?><li><a href="#adminadd" data-toggle="modal">Dodaj novog admina</a></li><?php } ?>
							<li class="dropdown-submenu">
			                  <a tabindex="-1" href="admin_lista.php">Admini - <?php echo $bradmini; ?></a>
			                  <ul class="dropdown-menu">
						<?php	while($row = mysql_fetch_array($admini)) {	?>
			                    <li><a tabindex="-1" href="<?php echo $dir; ?>/admin_pregled.php?id=<?php echo $row['id']; ?>">#<?php echo $row['id']; ?> <?php echo admin_ime($row['id']) . ' - ' . get_status($row['lastactivity']); ?></a></li>
						<?php	}	?>
			                  </ul>
			                </li>
						</ul>   
					</li>
					
					<li class="<?php if($fajl == "reputacija" OR $fajl == "error_log" OR $fajl == "kerror_log" OR $fajl == "bug") echo'active ';  ?>dropdown">
						<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
							<i class="icon-align-justify"></i>
							<span>Ostalo</span>
						</a>
						<ul class="dropdown-menu">
							<li><a href="reputacija.php"><i class="icon-align-justify"></i> Reputacija</a></li>
							<?php if(vlasnik($_SESSION['a_id'])) { ?>
							<li class="divider"></li>
							<li><a href="<?php echo $dir; ?>/error_log.php"><i class="icon-warning-sign"></i> Error log <span class="label label-warning"><?php echo $error_logovi; ?></span></a></li>
							<li><a href="<?php echo $dir; ?>/kerror_log.php"><i class="icon-warning-sign"></i> Klijent error log <span class="label label-warning"><?php echo $kerror_logovi; ?></span></a></li>
							<li class="divider"></li>
							<li><a href="bug.php"><i class="icon-th"></i> Bugovi <span class="label label-warning"><?php echo $bugs; ?></span></a></li>
							<li class="divider"></li>
							<li><a href="#mailtoall" data-toggle="modal"><i class="icon-forward"></i> Mail</a></li>
						<?php } ?>
						</ul>
					</li>				
				</ul>
			</div> <!-- /.subnav-collapse -->

		</div> <!-- /container -->
	
	</div> <!-- /subnavbar-inner -->

</div> <!-- /subnavbar -->
    
    
<div class="main">

    <div class="container">	
	
<?php
require("./assets/libs/phpseclib/SSH2.php");

if (!class_exists('Net_SSH2')) {
?>
		<div class="alertt alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<h4 class="alert-heading">SSH2 GRESKA</h4>
			SSH2 extenzija nije instalirana.
		</div>
<?php
}

if (isset($_SESSION['msg1']) && isset($_SESSION['msg2']) && isset($_SESSION['msg-type'])) {
?>
			<div class="alert alert-<?php
	switch ($_SESSION['msg-type']) {
		case 'block':
			echo 'block';
			break;

		case 'error':
			echo 'error';
			break;

		case 'success':
			echo 'success';
			break;

		case 'info':
			echo 'info';
			break;
	}
?>">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<h4 class="alert-heading"><?php echo $_SESSION['msg1']; ?></h4>
				<?php echo $_SESSION['msg2']; ?>
			</div>
<?php
	unset($_SESSION['msg1']);
	unset($_SESSION['msg2']);
	unset($_SESSION['msg-type']);
}
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

if(isset($srv)) 
{
	if($srv == "1")
	{
?>	
	<h3 style="margin-top: -25px;"><?php echo $server['name']; ?></h3>
    <div class="navbar">
		<div class="navbar-inner">
			<ul class="nav">
				<li style="margin-left: -20px;"<?php if($fajl == "srv-pocetna") echo ' class="active"'; ?>><a href="srv-pocetna.php?id=<?php echo $server['id']; ?>"><i class="icon-home"></i> Server info</a></li>
				<li <?php if($fajl == "srv-podesavanja") echo ' class="active"'; ?>><a href="srv-podesavanja.php?id=<?php echo $server['id']; ?>&masina=<?php echo $server['box_id']; ?>"><i class="icon-cogs"></i> Podešavanja</a></li>
				<li <?php if($fajl == "srv-webftp") echo ' class="active"'; ?>><a href="srv-webftp.php?id=<?php echo $server['id']; ?>"><i class="icon-folder-open"></i> WebFTP</a></li>
				<?php if($server['igra'] != "7") {?>
				<li <?php if($fajl == "srv-plugini") echo ' class="active"'; ?>><a href="srv-plugini.php?id=<?php echo $server['id']; ?>"><i class="icon-cog"></i> Plugini</a></li>
				<li <?php if($fajl == "srv-modovi") echo ' class="active"'; ?>><a href="srv-modovi.php?id=<?php echo $server['id']; ?>"><i class="icon-cogs"></i> Modovi</a></li>
				<li <?php if($fajl == "srv-konzola") echo ' class="active"'; ?>><a href="srv-konzola.php?id=<?php echo $server['id']; ?>"><i class="icon-th-list"></i> Konzola</a></li>
				<?php }?>
			</ul>
		</div>
    </div>
<?php
	}
}


if(isset($ts_srv)) 
{
	if($ts_srv == "1")
	{
?>	
	<h3 style="margin-top: -25px;"><?php echo $server['name']; ?></h3>
    <div class="navbar">
		<div class="navbar-inner">
			<ul class="nav">
				<li style="margin-left: -20px;"<?php if($fajl == "ts-pocetna") echo ' class="active"'; ?>><a href="ts-pocetna.php?id=<?php echo $server['id']; ?>"><i class="icon-home"></i> Server info</a></li>
				<li <?php if($fajl == "ts-podesavanja") echo ' class="active"'; ?>><a href="ts-podesavanja.php?id=<?php echo $server['id']; ?>&masina=<?php echo $server['box_id']; ?>"><i class="icon-cogs"></i> Podešavanja</a></li>
			</ul>
		</div>
    </div>
<?php
	}
}

?>

