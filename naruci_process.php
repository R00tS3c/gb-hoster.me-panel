<?php
include('fnc/ostalo.php');
include("includes/func.server.inc.php");

/* INSTALL SERVER */

if (isset($_GET['task']) && $_GET['task'] == "billing_srv_install") {
	if ($_SESSION['userid'] == "") {
		header("Location: /home");
		die();
	}
	
	if (is_demo() == false) {
		$_SESSION['info'] = "Ova opicja nije dozvoljena demo klijentu!";
		header("Location: $_SERVER[HTTP_REFERER]");
		die();
	}
	
	$billing_id 		= htmlspecialchars(mysql_real_escape_string(addslashes($_POST['billing_id'])));
	if ($billing_id == "") {
		$_SESSION['error'] = "Greska. - BILLING ID (GP)";
		header("Location: gp-billing.php");
		die();
	}
	
	$billing_option = mysql_query("SELECT * FROM `billing` WHERE `id` = '$billing_id' AND `klijentid` = '$_SESSION[userid]'");
	if (!mysql_num_rows($billing_option) > 0) {
		$_SESSION['error'] = "Greska. Ovaj billing ne postoji ili nije vas.";
		header("Location: gp-billing.php");
		die();
	}
	
	$billing_i = mysql_fetch_array($billing_option);
	
	if ($billing_i['klijentid'] == $_SESSION['userid']) {
		if ($billing_i['BillingStatus'] != "0") {
			$game = $billing_i['game'];
			$lokacija = $billing_i['lokacija'];
			
			if($lokacija == 1) {
				$lokacija_baza = "Lite - Njemacka";
				$lokacija = 1;
			} else if($lokacija == 2) {
				$lokacija_baza = "Lite - Poljska";
				$lokacija = 1;
			} else if($lokacija == 3) {
				$lokacija_baza = "Lite - Francuska";
				$lokacija = 1;
			} else if($lokacija == 4) {
				$lokacija_baza = "Premium - Bugarska";
				$lokacija = 2;
			} else if($lokacija == 5) {
				$lokacija_baza = "Premium - BiH";
				$lokacija = 2;
			} else {
				$lokacija_baza = "Lite - Njemacka";
				$lokacija = 1;
			}
			
			if($game == "Counter-Strike 1.6") {
				$igra = "1";
			} else if ($game == "GTA San Andreas") {
				$igra = "2";
			} else if ($game == "Minecraft") {
				$igra = "3";
			} else if ($game == "Team-Speak 3") {
				$igra = "4";
			} else if ($game == "SinusBot") {
				$igra = "5";
			} else if ($game == "FastDL") {
				$igra = "6";
			} else {
				$_SESSION['error'] = "Ovu igru trenutno nemamo u ponudi!";
				header("Location: gp-billing-w.php?id=".$billing_id);
				die();
			}
			
			if($igra == "4" || $igra == "5") {
				$slotovi = $billing_i['slotovi'];
				
				if($igra == "4") {
					$tiket_naslov = "Billing : $game";
					$tiket_text = "Pozdrav!<br>Narucio sam $game Server<br>Slotovi : $slotovi<br>Lokacija : $lokacija_baza";
				} else {
					$tiket_naslov = "Billing : $game";
					$tiket_text = "Pozdrav!<br>Narucio sam $game<br>Broj botova : $slotovi";
				}
				$d_v = date("h.m.s, d-m-Y");
				
				$new_tiket = mysql_query("INSERT INTO `billing_tiketi` (`id`, `admin_id`, `server_id`, `user_id`, `status`, `prioritet`, `vrsta`, `datum`, `naslov`, `poruka`, `billing`, `admin`, `otvoren`) VALUES ('$billing_id', NULL, '0', '$_SESSION[userid]', '1', '1', '1', '$d_v', '$tiket_naslov', '$tiket_text', '0', '', '');");
				
				if (!$new_tiket) {
					$_SESSION['error'] = "Greska. - PISANJE TIKETA (GP)";
					header("Location: gp-billing-w.php?id=".$billing_id);
					die();
				} else {
					$id = $billing_id;
					$d_v = time();
					$tiket_odg = mysql_query("INSERT INTO `billing_tiketi_odgovori` (`id`, `tiket_id`, `user_id`, `admin_id`, `odgovor`, `vreme_odgovora`) VALUES (NULL, '$id', '$_SESSION[userid]', NULL, '$tiket_text', '$d_v');");
					
					$update = mysql_query("UPDATE `billing` SET `BillingStatus` = '2' WHERE `id` = '$billing_id'");
					
					$_SESSION['ok'] = "Uspesno ste poslali tiket za instalaciju servera.";
					header("Location: gp-billing-w.php?id=".$billing_id);
					die();
				}
			} else {
			
			if ($igra != "6") {
				if(query_numrows("SELECT * FROM `box` WHERE `lokacija` = '$lokacija' AND `fdl` = '0' ORDER BY RAND() LIMIT 1") == 0) {
					$_SESSION['error'] = "Dogodila se greska prilikom odabira masine!";
					header("Location: gp-billing-w.php?id=".$billing_id);
					die();
				}
				
				$slotovi = $billing_i['slotovi'];
				$box = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `lokacija` = '$lokacija' AND `fdl` = '0' ORDER BY RAND() LIMIT 1"));
				$boxip = mysql_fetch_array(mysql_query("SELECT * FROM `boxip` WHERE `boxid` = '$box[boxid]' ORDER BY RAND() LIMIT 1"));
				/*
				$_SESSION['error'] = "Premium : $lokacija | Box ID : $box[boxid]";
				header("Location: gp-billing-w.php?id=".$billing_id);
				die();*/
			} else {
				if(query_numrows("SELECT * FROM `box` WHERE `lokacija` = '$lokacija' AND `fdl` = '1' ORDER BY RAND() LIMIT 1") == 0) {
					$_SESSION['error'] = "Dogodila se greska prilikom odabira masine!";
					header("Location: gp-billing-w.php?id=".$billing_id);
					die();
				}
				$slotovi = "1";
				$box = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `lokacija` = '$lokacija' AND `fdl` = '1' ORDER BY RAND() LIMIT 1"));
				$boxip = mysql_fetch_array(mysql_query("SELECT * FROM `boxip` WHERE `boxid` = '$box[boxid]' ORDER BY RAND() LIMIT 1"));
				/*
				$_SESSION['error'] = "Premium : $lokacija | Box ID : $box[boxid]";
				header("Location: gp-billing-w.php?id=".$billing_id);
				die();*/
			}
			
			for($port = 27015; $port <= 29999; $port++) {
				if(query_numrows("SELECT * FROM `serveri` WHERE `ip_id` = '".$boxip['ipid']."' AND `port` = '".$port."' LIMIT 1") == 0) {
					require("./inc/libs/lgsl/lgsl_class.php");
					
					$serverl = lgsl_query_live('halflife', $boxip['ip'], NULL, $port, NULL, 's');
					
					if(@$serverl['b']['status'] == '1') $srvonline = "Da";
					else $srvonline = "Ne";
					
					if($srvonline == "Ne") {
						$portcs = $port;
						break;
					}
				}
			}
			
			for($port = 7777; $port <= 9999; $port++) {
				if(query_numrows("SELECT * FROM `serveri` WHERE `ip_id` = '".$boxip['ipid']."' AND `port` = '".$port."' LIMIT 1") == 0) {
					require("./inc/libs/lgsl/lgsl_class.php");
					
					$serverl = lgsl_query_live('samp', $boxip['ip'], NULL, $port, NULL, 's');
					
					if(@$serverl['b']['status'] == '1') $srvonline = "Da";
					else $srvonline = "Ne";	
					
					if($srvonline == "Ne") {
						$portsamp = $port;
						break;
					}
				}
			}
			
			for($port = 25565; $port <= 25999; $port++) {
				if(query_numrows("SELECT * FROM `serveri` WHERE `ip_id` = '".$boxip['ipid']."' AND `port` = '".$port."' LIMIT 1") == 0) {
					require("./inc/libs/lgsl/lgsl_class.php");
					
					$serverl = lgsl_query_live('minecraft', $boxip['ip'], NULL, $port, NULL, 's');
					
					if(@$serverl['b']['status'] == '1') $srvonline = "Da";
					else $srvonline = "Ne";	
					
					if($srvonline == "Ne") {
						$portmc = $port;
						break;
					}
				}
			}
			
			if($igra == "1") {
				$port = $portcs;
			} else if ($igra == "2") {
				$port = $portsamp;
			} else if ($igra == "3") {
				$port = $portmc;
			} else if ($igra == "6") {
				$port = "0";
			} else {
				$_SESSION['error'] = "Ovu igru trenutno nemamo u ponudi!";
				header("Location: gp-billing-w.php?id=".$billing_id);
				die();
			}
			if ($igra != "6") {
				$mod = $billing_i['srw_mod'];
			} else {
				$mod = "6";
			}
			$klijentid = $_SESSION['userid'];
			$imeservera = $billing_i['srw_name'];
			
			$boxid = $box['boxid'];
			$ipid = $boxip['ipid'];
			
			$provera_username = query_numrows("SELECT `id` FROM `serveri` WHERE `user_id` = '".$_SESSION['userid']."'");  
			
			$username_proveren = 'srv_'.$klijentid.'_'.randomSifra(5).'';
			
			if(query_numrows("SELECT * FROM `serveri` WHERE `username` = '{$username_proveren}'") != 0) {
				$username_proveren = 'srv_'.$klijentid.'_'.randomSifra(5).'';
			}
			
			$username = $username_proveren;
			
			$sifra = randomSifra(8);
			if ($igra != "6") {
				$mapa = query_fetch_assoc("SELECT `mapa` FROM `modovi` WHERE `id` = '".$mod."'");
				$mapa = $mapa['mapa'];
			} else {
				$mapa = "";
			}
			
			$mjeseci = $billing_i['placaza'];
			
			$vreme = (time() + (60 * 60 * 24 * 31 * $mjeseci));
			$vreme = date("Y-m-d, H:i", $vreme);
			
			$time = explode(",", $vreme);
			$datum = $time[0];
			$istice = $datum;
			
			if ($igra != "6") {
				$komanda = query_fetch_assoc("SELECT `komanda`, `putanja` FROM `modovi` WHERE `id` = '".$mod."'");
				$komandaa = $komanda['komanda'];
			} else {
				$komandaa = "";
			}
			
			$ipi = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$ipid."'");
			$boxi = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$boxid."'");
			if ($igra != "6") {
				$modi = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."'");
			}
			
			require_once("./inc/libs/phpseclib/Crypt/AES.php");
			$aes = new Crypt_AES();
			$aes->setKeyLength(256);
			$aes->setKey(CRYPT_KEY);		
			
			$masina_pw = $aes->decrypt($boxi['password']);
			
			$ipetx = $ipi['ip'];
			
			$ipsa = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ip` = '$boxi[ip]'");
			
			if($igra == "2") { $ipid = $ipsa['ipid']; $ipetx = $boxi['ip']; }
			
			$ssh_dodavanje = ssh_dodaj_server($ipi['ip'], $boxi['sshport'], $boxi['login'], $masina_pw, $username, $sifra, $mod);
			
			if($ssh_dodavanje == "uspesno") {
				if ($igra != "6") {
					$s = explode("|", $modi['cena']);
					$slotcena = $s[0];
					$cena = ($s[1]*$slotovi)."";
				} else {
					$cena = $billing_i['iznos'];
					$igra = "7";
				}
				$aid = $_SESSION['a_id'];
				$result = mysql_query("INSERT INTO `serveri` SET
					`user_id` = '".$klijentid."',
					`box_id` = '".$boxid."',
					`ip_id` = '".$ipid."',
					`name` = '".$imeservera."',
					`mod` = '".$mod."',
					`map` = '".$mapa."',
					`port` = '".$port."',
					`fps` = '333',
					`slotovi` = '".$slotovi."',
					`username` = '".$username."',
					`password` = '".$sifra."',
					`istice` = '".$istice."',
					`status` = 'Aktivan',
					`startovan` = '0',
					`free` = 'Ne',
					`cena` = '".$cena."',
					`komanda` = '".$komandaa."',
					`igra` = '".$igra."',
					`aid` = '".$aid."'");
				
				$serverid = mysql_insert_id();
				
				$result2 = mysql_query("DELETE FROM `lgsl` WHERE `id` = '".$serverid."'");
				
				if($igra == "1") $querytype = "halflife";
				else if($igra == "2") $querytype = "samp";
				else if($igra == "3") $querytype = "minecraft";
				if($igra != "7") {
				$result3 = mysql_query( "INSERT INTO `lgsl` SET
					`id` = '".$serverid."',
					`type` = '".$querytype."',
					`ip` = '".$ipetx."',
					`c_port` = '".$port."',
					`q_port` = '".$port."',
					`s_port` = '0',
					`zone` = '0',
					`disabled` = '0',
					`comment` = '".$imeservera."',
					`status` = '0',
					`cache` = '',
					`cache_time` = ''" );
				}
				$result4 = mysql_query("DELETE FROM `billing` WHERE `id` = '".$billing_id."'");
				
				$_SESSION['ok'] = "Uspesno ste instalirali server.";
				header("Location: gp-info.php?id=".$serverid);
				die();
			}
			else
			{
				$_SESSION['error'] = "$ssh_dodavanje";
				header("Location: gp-billing-w.php?id=".$billing_id);
				die();
			}
			}
		}
	} else {
		$_SESSION['error'] = "Nemas pristup.";
		header("Location: gp-billing.php");
		die();
	}
}

function query_basic($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{	
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'1')
					") or die(mysql_error());
	}
}

function query_numrows($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'1')
					") or die(mysql_error());
	}
	return (mysql_num_rows($result));
}

function query_fetch_assoc($query)
{
	$result = mysql_query($query);
	if ($result == FALSE)
	{
		$greska = mysql_real_escape_string(mysql_error());
		mysql_query("INSERT INTO error_log (number, string, file, line, datum, vrsta) 
					VALUES ('1', 
							'{$greska}', 
							'mysql_greska', 
							'mysql_greska',
							'".time()."',
							'1')
					") or die(mysql_error());
	}
	return (mysql_fetch_assoc($result));
}

?>