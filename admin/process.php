<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("konfiguracija.php");
include("includes.php");

if (isset($_POST['task']))
{
	$task = mysql_real_escape_string($_POST['task']);
}

else if (isset($_GET['task']))
{
	$task = mysql_real_escape_string($_GET['task']);
}

switch (@$task)
{
	case 'addobavestenje':
		samo_vlasnik($_SESSION['a_id']);
		$naslov = mysql_real_escape_string($_POST['naslov']);
		$naslov = htmlspecialchars($naslov);

		$tekst = nl2br(htmlspecialchars($_POST['tekst']));
		$tekst = mysql_real_escape_string($tekst);		
		
		$vrsta = mysql_real_escape_string($_POST['vrsta']);
		
		if(empty($naslov))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Niste popunili polje 'Naslov'.";
			$_SESSION['msg-type'] = "error";
			header("Location: index.php");
			die();
		}
		
		if(empty($tekst))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Niste popunili polje 'Tekst'.";
			$_SESSION['msg-type'] = "error";
			header("Location: index.php");
			die();
		}
		
		if($vrsta == "1")
		{
			mysql_query("INSERT INTO `obavestenja` (naslov, poruka, vrsta, datum) VALUES('".$naslov."', '".$tekst."', '".$vrsta."', '".time()."')");
		}
		else if($vrsta == "2")
		{
			mysql_query("UPDATE `obavestenja` SET `naslov` = '".$naslov."', `poruka` = '".$tekst."', `vrsta` = '".$vrsta."', `datum` = '".time()."'");
		}
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Dodao obavestenje" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Dodao obavestenje <m>".$naslov."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Dodali ste novo obavestenje.";
		$_SESSION['msg-type'] = "success";
		header("Location: index.php");	
		die();
		
	break;
	
	case 'editobavestenje':
		samo_vlasnik($_SESSION['a_id']);
		$naslov = mysql_real_escape_string($_POST['naslov']);
		$naslov = htmlspecialchars($naslov);
		
		$tekst = mysql_real_escape_string($_POST['tekst']);

		if(isset($_POST['id'])) $id = mysql_real_escape_string($_POST['id']);
		
		if(empty($naslov))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Niste popunili polje 'Naslov'.";
			$_SESSION['msg-type'] = "error";
			header("Location: obavestenja.php?view=klijenti");
			die();
		}
		
		if(empty($tekst))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Niste popunili polje 'Tekst'.";
			$_SESSION['msg-type'] = "error";
			header("Location: obavestenja.php?view=klijenti");
			die();
		}
		
		if(isset($id)) mysql_query("UPDATE `obavestenja` SET `naslov` = '".$naslov."', `poruka` = '".$tekst."', `datum` = '".time()."' WHERE `id` = '{$id}'");
		else  mysql_query("UPDATE `obavestenja` SET `naslov` = '".$naslov."', `poruka` = '".$tekst."', `datum` = '".time()."' WHERE `vrsta` = '2'");
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Promenio obavestenje" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Promenio obavestenje <m>".$naslov."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Izmenili ste obavestenje.";
		$_SESSION['msg-type'] = "success";
		header("Location: obavestenja.php?view=klijenti");		
		die();
	break;	
	
	case 'delobavestenje':
		samo_vlasnik($_SESSION['a_id']);
		$id = $_POST['id'];
		
		mysql_query("DELETE FROM `obavestenja` WHERE `id` = '".$id."'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Izbrisao obavestenje" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Izbrisao obavestenje";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Izbrisali ste obavestenje.";
		$_SESSION['msg-type'] = "success";		
		
		echo'uspesno';
	break;
	
	case 'izbrisi_errorlog':
		samo_fs($_SESSION['a_id']);
		$delete = mysql_query("DELETE FROM `error_log`");
		
		if($delete) {
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Izbrisali ste sve error logove.";
			$_SESSION['msg-type'] = "success";
			
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje error logova" WHERE id="'.$_SESSION["a_id"].'"');
			
			$sql = mysql_query("SELECT * FROM admin WHERE id = '".$_SESSION["a_id"]."'");
			$info = mysql_fetch_array($sql);
			
			$poruka = "Izbrisao sve error logove.";
			alog($info["id"], $poruka, $info['fname'].' '.$info['lname'], fuckcloudflare());			
			
			header("Location: ./errorlog");
			die();
		} else {
			mysql_error();
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ne mogu izvrisiti query.";
			$_SESSION['msg-type'] = "error";			
			header("Location: ./errorlog");
			die();
		}
	break;
	
	case 'izbrisi_logove':
		samo_fs($_SESSION['a_id']);
		$delete = mysql_query("DELETE FROM `logovi`");
		
		if($delete) {
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Izbrisali ste sve logove.";
			$_SESSION['msg-type'] = "success";
			
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje logova" WHERE id="'.$_SESSION["a_id"].'"');
			
			$sql = mysql_query("SELECT * FROM admin WHERE id = '".$_SESSION["a_id"]."'");
			$info = mysql_fetch_array($sql);
			
			$poruka = "Izbrisao sve logove.";
			alog($info["id"], $poruka, $info['fname'].' '.$info['lname'], fuckcloudflare());
			
			header("Location: ./logovi");
			die();
		} else {
			mysql_error();
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ne mogu izvrisiti query.";
			$_SESSION['msg-type'] = "error";			
			header("Location: ./logovi");
			die();
		}
	break;
	
	case 'komentar':
		$admin_id = mysql_real_escape_string($_POST['admin_id']);
		$komentar = mysql_real_escape_string(nl2br(htmlspecialchars($_POST['komentar'])));
		$profil_id = mysql_real_escape_string($_POST['profil_id']);
		$vreme = mysql_real_escape_string($_POST['vreme']);
		
		if(empty($komentar)) {
			echo 'Niste uneli komentar';
		}elseif(empty($vreme)) {
			echo 'Vreme ne postoji';
		}elseif(empty($admin_id)) {
			echo 'Admin id nije unet';
		}elseif(empty($profil_id)) {
			echo 'Profil id nije unet';
		}elseif(!empty($komentar)) {
			$sql = mysql_query("INSERT INTO komentari (adminid, komentar, profilid, vreme) VALUES ('" . $admin_id . "', '" . $komentar . "', '" . $profil_id . "', '" . $vreme . "')");
			
			if($sql) {
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Pisanje komentara na profil #'.$profil_id.'" WHERE id="'.$_SESSION["a_id"].'"');			
				echo 'uspesno';
			} else {
				echo 'greska';
			}
		
		}	
	break;
	
	case 'delkomentar':
		$id = mysql_real_escape_string($_POST['id']);
		
		if($id != $_SESSION['a_id'])
		{
			$admin = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$id."'");
	
			if($admin['status'] == "support")
			{
				echo 'Nemate pristup ovome';
				die();
			}
		}	
		
		$sql = mysql_query("DELETE FROM `komentari` WHERE id = '".$id."'");
			
		if($sql) {
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje komentara" WHERE id="'.$_SESSION["a_id"].'"');					
			echo 'uspesno';
		} else {
			echo 'greska';
		}
		
	break;
	
	case 'delkomentarc':
		$id = mysql_real_escape_string($_POST['id']);
		
		$sql = mysql_query("DELETE FROM `klijenti_komentari` WHERE id = '".$id."'");
			
		if($sql) {
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje klijent komentara" WHERE id="'.$_SESSION["a_id"].'"');					
			echo 'uspesno';
		} else {
			echo 'greska';
		}
		
	break;	
	
	case 'komentar_tiket':
		$admin_id = mysql_real_escape_string($_POST['admin_id']);
		$komentar = mysql_real_escape_string(nl2br(htmlspecialchars($_POST['komentar'])));
		$tiket_id = mysql_real_escape_string($_POST['tiket_id']);
		$vreme = mysql_real_escape_string($_POST['vreme']);
		
		if(empty($komentar)) {
			echo 'Niste uneli komentar';
		}elseif(empty($vreme)) {
			echo 'Vreme ne postoji';
		}elseif(empty($admin_id)) {
			echo 'Admin id nije unet';
		}elseif(empty($tiket_id)) {
			echo 'Tiket id nije unet';
		}elseif(!empty($komentar)) {
			$sql = mysql_query("INSERT INTO tiketi_odgovori (tiket_id, admin_id, odgovor, vreme_odgovora) VALUES ('" . $tiket_id . "', '" . $admin_id . "', '" . $komentar . "', '" . $vreme . "')") or die(mysql_error());
			
			mysql_query("UPDATE `tiketi` SET `status` = '2' WHERE `id` = '".$tiket_id."'") or die(mysql_error());
			
			if($sql) {
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Odgovor na tiket #'.$tiket_id.'" WHERE id="'.$_SESSION["a_id"].'"');			
				echo 'uspesno';
			} else {
				echo 'greska';
			}
		
		}	
	break;
	
	case 'delkomentar_tiket':
		$id = mysql_real_escape_string($_POST['id']);
		
		$sql = mysql_query("DELETE FROM `tiketi_odgovori` WHERE id = '".$id."'");
			
		if($sql) {
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje odgovora u tiketima" WHERE id="'.$_SESSION["a_id"].'"');			
			echo 'uspesno';
		} else {
			echo 'greska';
		}
		
	break;
	
	case 'izbrisi_admina':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		
		$info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$id."'"));
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));
		
		$sql = mysql_query("DELETE FROM `admin` WHERE id = '".$id."'");
		
		if($sql) {
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje admina" WHERE id="'.$_SESSION["a_id"].'"');		
		
			$poruka = "Izbrisao admina ( <m>#".$info['id']." ".$info['fname']." ".$info['lname']."</m> )";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());		
			
			mysql_query("DELETE FROM `tiketi` WHERE `admin_id` = '{$id}'");
			mysql_query("DELETE FROM `tiketi_odgovori` WHERE `admin_id` = '{$id}'");
			mysql_query("DELETE FROM `chat_messages` WHERE `admin_id` = '{$id}'");
			mysql_query("DELETE FROM `komentari` WHERE `adminid` = '{$id}'");
			mysql_query("DELETE FROM `reputacija` WHERE `adminid` = '{$id}'");
			mysql_query("DELETE FROM `logovi` WHERE `adminid` = '{$id}'");
			mysql_query("DELETE FROM `komentari` WHERE `adminid` = '{$id}'");
		
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Admin <m>( # ".$id." )</m> je izbrisan.";
			$_SESSION['msg-type'] = "success";					
		} else {
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ne mogu izbrisati admina.";
			$_SESSION['msg-type'] = "error";			
		}

		header("Location: admin_lista.php");
		die();
	break;
	
	case 'promeni_profil':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		$username = mysql_real_escape_string($_POST['username']);
		
		$info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$id."'"));
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
		
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Prebacio se na profil ( <m>#'.$info['id'].' '.$info['fname'].' '.$info['lname'].'</m> )" WHERE id="'.$_SESSION["a_id"].'"');		
		
		$poruka = "Prebacio se na profil ( <m>#".$info['id']." ".$info['fname']." ".$info['lname']."</m> )";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());	

		
		$_SESSION['a_username'] = $username;
		$_SESSION['a_id'] = $id;		
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Prebacili ste se na drugi profil.";
		$_SESSION['msg-type'] = "success";	
		
		header("Location: index.php");
		die();
	break;
	
	case 'uplata':
		samo_vlasnik($_SESSION['a_id']);
		$status = $_POST['statust'];
		$tiketid = $_POST['tiketid'];
		$userid = $_POST['userid'];
		

		if($status == "2"){
			$info = mysql_query("SELECT `billing` FROM `tiketi` WHERE `id` = '".$tiketid."'") or die(mysql_error());
			$info = mysql_fetch_array($info);
	
			$info2 = mysql_query("SELECT `iznos` FROM `billing` WHERE `id` = '".$info['billing']."'") or die(mysql_error());
			$info2 = mysql_fetch_array($info2);
			
			$info3 = mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$userid."'") or die(mysql_error());
			$info3 = mysql_fetch_array($info3);			
	
			$novacxy = str_replace(" din", "", $info3['novac']);
			$novacxy = str_replace(" €", "", $novacxy);
	
			$novac = $info2['iznos'] + $novacxy;
			
			mysql_query("UPDATE `klijenti` SET `novac` = '".$novac."' WHERE `klijentid` = '".$userid."'") or die(mysql_error());
			mysql_query("UPDATE `billing` SET `status` = 'Ceka proveru' WHERE `id` = '".$info['billing']."'") or die(mysql_error());
			mysql_query("UPDATE `tiketi` SET `naslov` = 'Billing: Nova uplata - Ceka proveru' WHERE `id` = '".$tiketid."'") or die(mysql_error());
			
			$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Potvrda uplate" WHERE id="'.$_SESSION["a_id"].'"');				
			
			$poruka = "Čeka proveru uplata u iznosu od ".$info2['iznos']." evra, Klijent: <m>".$info3['ime']." ".$info3['prezime']."</m>";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());
			
			$adminid = $_SESSION['a_id'];
			$time = time();
			
			$log = "Prihvacena uplata za provjeru u iznosu od {$info2[iznos]} EUR, Klijent: <a href=\"/admin/klijent.php?id={$userid}\">#{$info3['ime']} {$info3['prezime']}</a>";
			mysql_query("INSERT INTO `billing_log` (`clientid`,`text`,`adminid`,`time`) VALUES ('$info3[klijentid]','$log','$adminid','$time')");
		
			$_SESSION['msg1'] = "Uspesno";
			$_SESSION['msg2'] = "Stavili ste uplatu na cekanju provere.";
			$_SESSION['msg-type'] = "success";
			
			header("Location: tiket.php?id=".$tiketid."");		
			die();
		}		
		if($status == "1"){
			$info = mysql_query("SELECT `billing` FROM `tiketi` WHERE `id` = '".$tiketid."'") or die(mysql_error());
			$info = mysql_fetch_array($info);
	
			$info2 = mysql_query("SELECT `iznos` FROM `billing` WHERE `id` = '".$info['billing']."'") or die(mysql_error());
			$info2 = mysql_fetch_array($info2);
			
			$info3 = mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$userid."'") or die(mysql_error());
			$info3 = mysql_fetch_array($info3);			

			mysql_query("UPDATE `billing` SET `status` = 'Leglo' WHERE `id` = '".$info['billing']."'") or die(mysql_error());
			mysql_query("UPDATE `tiketi` SET `naslov` = 'Billing: Nova uplata - Leglo' WHERE `id` = '".$tiketid."'") or die(mysql_error());
			
			$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Potvrda uplate" WHERE id="'.$_SESSION["a_id"].'"');				
			
			$poruka = "Potvrdio uplatu u iznosu od ".$info2['iznos']." evra, Klijent: <m>".$info3['ime']." ".$info3['prezime']."</m>";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
			
			$adminid = $_SESSION['a_id'];
			$time = time();
			
			$client = mysql_fetch_array(mysql_query("SELECT * FROM klijenti WHERE klijentid='$userid'"));
	        if ($client['klijentid']=="") die("Greska! Nepostojeci klijent!");
		
		    $clientcurrency = mysql_fetch_array(mysql_query("SELECT * FROM `billing_currency` WHERE `zemlja`='{$client['zemlja']}'"));
			if ($clientcurrency['cid']=="") die("Morate podesiti valutu ovom klijentu!");
		
		    $ueurima = round($info2['iznos']/$clientcurrency['multiply'],2);
			
		    $starostanje = $client['novac'] * $clientcurrency['multiply'];
			$novostanje = ($client['novac']+$ueurima) * $clientcurrency['multiply'];
			
			 
			//$log = "Prihvacena uplata u iznosu od {$info2[iznos]} EUR, Klijent: <a href=\"/admin/klijent.php?id={$userid}\">#{$info3['ime']} {$info3['prezime']}</a> (Staro stanje: {$starostanje} {$clientcurrency['sign']}, Novo: {$novostanje} {$clientcurrency['sign']})";
			
			$log = "Prihvacena uplata u iznosu od {$info2[iznos]} EUR, Klijent: <a href=\"/admin/klijent.php?id={$userid}\">#{$info3['ime']} {$info3['prezime']}</a>";
			mysql_query("INSERT INTO `billing_log` (`clientid`,`text`,`adminid`,`time`) VALUES ('$info3[klijentid]','$log','$adminid','$time')");
			
			$_SESSION['msg1'] = "Uspesno";
			$_SESSION['msg2'] = "Potvrdili ste uplatu klijentu.";
			$_SESSION['msg-type'] = "success";
			
			header("Location: tiket.php?id=".$tiketid."");		
			die();
		}
		if($status == "0"){
			$info = mysql_query("SELECT `billing` FROM `tiketi` WHERE `id` = '".$tiketid."'") or die(mysql_error());
			$info = mysql_fetch_array($info);
			
			mysql_query("UPDATE `billing` SET `status` = 'Nije leglo' WHERE `id` = '".$info['billing']."'") or die(mysql_error());
			
			mysql_query("UPDATE `tiketi` SET `naslov` = 'Billing: Nova uplata - Nije leglo' WHERE `id` = '".$tiketid."'") or die(mysql_error());
	
			$_SESSION['msg1'] = "Uspesno";
			$_SESSION['msg2'] = "Odbili ste uplatu klijentu.";
			$_SESSION['msg-type'] = "error";	
			header("Location: tiket.php?id=".$tiketid."");
			die();
		}		
		
		$_SESSION['msg1'] = "Uspesno";
		$_SESSION['msg2'] = "Potvrdili ste uplatu klijentu.";
		$_SESSION['msg-type'] = "success";
		
		header("Location: tiket.php?id=".$tiketid."");
		die();
	break;
	
	case 'addmasina':
		samo_vlasnik($_SESSION['a_id']);
		$ip = mysql_real_escape_string($_POST['ipmas']);
		$lokacija = mysql_real_escape_string($_POST['lok']);
		$datacentar = mysql_real_escape_string($_POST['datacentar']);
		$ssh2port = mysql_real_escape_string($_POST['ssh2port']);
		$root = mysql_real_escape_string($_POST['root']);
		$pw = mysql_real_escape_string($_POST['pw']);
		$fdl = mysql_real_escape_string($_POST['fdl']);
		$fdl_link = mysql_real_escape_string($_POST['fdl_link']);

		include("./assets/libs/phpseclib/SSH2.php");
		
		$error = "";
		
		if (!validateIP($ip))
		{
			$error = "Neispravan ip";
		}
		if (query_numrows( "SELECT `boxid` FROM `box` WHERE `ip` = '".$ip."' && `login` = '".$root."'" ) != 0)
		{
			$error = "Taj ip vec postoji sa takvim login podatcima";
		}
		if (empty($root))
		{
			$error = "Niste uneli SSH2 Login.";
		}
		if (empty($pw))
		{
			$error = "SSH2 Sifra nije uneta.";
		}
		if (empty($ssh2port))
		{
			$ssh2port = 22;
		}
		else if (!is_numeric($ssh2port))
		{
			$error = "SSH2 Port nije ispravan.";
		}
		if($fdl == "1" && empty($fdl_link))
		{
			$error = "FDL Link nije unet!";
		}
		if($fdl == "0")
		{
			$fdl_link = "FastDL Offline";
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: index.php" );
			die();
		}		
		
		$ssh = new Net_SSH2($ip, $ssh2port);
		
		if (!$ssh->login($root, $pw))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Netacni root login podatcii.";
			$_SESSION['msg-type'] = "error";	

			header("Location: index.php");
			die();
		}
		else 
		{
			require_once("./assets/libs/phpseclib/Crypt/AES.php");
			$aes = new Crypt_AES();
			$aes->setKeyLength(256);
			$aes->setKey(CRYPT_KEY);			
			//
			mysql_query( "INSERT INTO box SET
				`name` = '".$lokacija." - ".$datacentar."',
				`ip` = '".$ip."',
				`login` = '".$root."',
				`password` = '".mysql_real_escape_string($aes->encrypt($pw))."',
				`sshport` = '".$ssh2port."',
				`fdl` = '".$fdl."',
				`fdl_link` = '".$fdl_link."'");
			###
			$boxid = mysql_insert_id();
			###
			//Addin box ip
			mysql_query( "INSERT INTO boxip SET
				`boxid` = '".$boxid."',
				`ip` = '".$ip."'" );
			//Cache
			$boxCache =	array(
				$boxid => array(
					'players'	=> array('players' => 0),

					'bandwidth'	=> array('rx_usage' => 0,
										 'tx_usage' => 0,
										 'rx_total' => 0,
										 'tx_total' => 0),

					'cpu'		=> array('proc' => '',
										 'cores' => 0,
										 'usage' => 0),

					'ram'		=> array('total' => 0,
										 'used' => 0,
										 'free' => 0,
										 'usage' => 0),

					'loadavg'	=> array('loadavg' => '0.00'),
					'hostname'	=> array('hostname' => ''),
					'os'		=> array('os' => ''),
					'date'		=> array('date' => ''),
					'kernel'	=> array('kernel' => ''),
					'arch'		=> array('arch' => ''),
					'uptime'	=> array('uptime' => ''),

					'swap'		=> array('total' => 0,
										 'used' => 0,
										 'free' => 0,
										 'usage' => 0),

					'hdd'		=> array('total' => 0,
										 'used' => 0,
										 'free' => 0,
										 'usage' => 0)
				)
			);
			mysql_query( "UPDATE box SET `cache` = '".mysql_real_escape_string(gzcompress(serialize($boxCache), 2))."' WHERE `boxid` = '".$boxid."'" );
			###	

			$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Dodao mašinu <m>#'.$boxid.' - '.$ip.'</m>" WHERE id="'.$_SESSION["a_id"].'"');				
			
			$poruka = "Dodao mašinu <m>#".$boxid." - ".$ip."</m>";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());	
			
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Masina je uspesno dodata.";
			$_SESSION['msg-type'] = "success";	
			//
			header("Location: index.php");
			die();
		}		
	break;
	
	case 'editmasina':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		$ip = mysql_real_escape_string($_POST['ipmas']);
		$datacentar = mysql_real_escape_string($_POST['datacentar']);
		$ssh2port = mysql_real_escape_string($_POST['ssh2port']);
		$root = mysql_real_escape_string($_POST['root']);
		$maxsrv = mysql_real_escape_string($_POST['maxsrv']);
		$pw = mysql_real_escape_string($_POST['pw']);
		$fdl = mysql_real_escape_string($_POST['fdl']);
		$fdl_link = mysql_real_escape_string($_POST['fdl_link']);

		include("./assets/libs/phpseclib/SSH2.php");
		
		$error = "";
		if (!validateIP($ip))
		{
			$error = "Neispravan ip";
		}
		
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$id."'");
		
		if(empty($ssh2port) || $ssh2port == "Na ti kurac!") {
			$ssh2port = $box['sshport'];
		}
		
		require_once("./assets/libs/phpseclib/Crypt/AES.php");
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		$ssh = new Net_SSH2($ip, $ssh2port);
		
		$pw2 = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$id."'");
		$ips = $pw2['ip'];
		
		if(empty($pw))
		{
			$pww = $pw2['password'];
			$pw = $aes->decrypt($pww);
		}
		
		if($ips != $ip)
		{
			if (mysql_num_rows(mysql_query( "SELECT `boxid` FROM `box` WHERE `ip` = '".$ip."' && `login` = '".$root."'" )) != 0)
			{
				$error = "Taj ip vec postoji sa takvim login podatcima";
			}
		}
		if (empty($root))
		{
			$error = "Niste uneli SSH2 Login.";
		}
		if (empty($maxsrv))
		{
			$error = "Niste uneli broj maksimalnih servera.";
		}
		if (!is_numeric($ssh2port))
		{
			$error = "SSH2 Port nije ispravan.";
		}
		if($fdl == "1" && empty($fdl_link))
		{
			$error = "FDL Link nije unet!";
		}
		if($fdl == "1" && $fdl_link == "FastDL Offline")
		{
			$error = "FDL Link nije unet!";
		}
		if($fdl == "0")
		{
			$fdl_link = "FastDL Offline";
		}
		###
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: index.php" );
			die();
		}		
		if (!$ssh->login($root, $pw))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Netacni root login podatci.";
			$_SESSION['msg-type'] = "error";	

			header("Location: index.php");
			die();
		}
		else 
		{			
			//
			mysql_query( "UPDATE boxip SET
				`boxid` = '".$id."',
				`ip` = '".$ip."' WHERE `ip` = '".$ips."'" );
							
			mysql_query( "UPDATE box SET
				`name` = '".$datacentar."',
				`ip` = '".$ip."',
				`login` = '".$root."',
				`maxsrv` = '".$maxsrv."',
				`password` = '".mysql_real_escape_string($aes->encrypt($pw))."',
				`sshport` = '".$ssh2port."',
				`fdl` = '".$fdl."',
				`fdl_link` = '".$fdl_link."' WHERE `boxid` = '".$id."'" );

			$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Promenio mašinu <m>#'.$id.' - '.$ip.'</m>" WHERE id="'.$_SESSION["a_id"].'"');				
			
			$poruka = "Promenio mašinu <m>#".$id." - ".$ip."</m>";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());	
			
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Masina je uspesno promenjena.";
			$_SESSION['msg-type'] = "success";	
			//
			header("Location: index.php");
			die();
		}		
	break;	
	
	case 'klijent_add':
		samo_vlasnik($_SESSION['a_id']);
		$username = mysql_real_escape_string($_POST['username']);
		$username = htmlspecialchars($username);
		
		$ime = mysql_real_escape_string($_POST['ime']);
		$ime = htmlspecialchars($ime);
		
		$email = mysql_real_escape_string($_POST['email']);
		$email = htmlspecialchars($email);
		
		$drzava = mysql_real_escape_string($_POST['zemlja']);
		$drzava = htmlspecialchars($drzava);
		
		$sifra = mysql_real_escape_string($_POST['password']);
		$sifra = htmlspecialchars($sifra);
		
		if(empty($username))
		{
			$error = "Niste uneli korisnicko ime.";
		}
		
		if(mysql_num_rows(mysql_query("SELECT `username` FROM `klijenti` WHERE `username` = '".$username."'")) != 0)
		{
			$error = "Username je vec u upotrebi.";
		}
		
		if(empty($ime))
		{
			$error = "Niste uneli ime i prezime.";
		} else {
			$imepr = explode(" ", $ime);
			unset($ime);
			$ime = $imepr['0'];
			$prezime = $imepr['1'];		
		}
		
		if(empty($email))
		{
			$error = "Niste uneli e-mail.";
		}
		
		if(mysql_num_rows(mysql_query("SELECT `email` FROM `klijenti` WHERE `email` = '".$email."'")) != 0)
		{
			$error = "E-mail je vec u upotrebi.";
		}
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: index.php" );
			die();
		}		
		
		if(empty($sifra))
		{
			$sifra = randomSifra(8);
		} 
		
		$sifra2 = $sifra;
			
		$salt = hash('sha512', $username);
		$sifra = hash('sha512', $salt.$sifra);	

		$sigkod = rand("00000", "99999");

		mysql_query("INSERT INTO `klijenti` SET
			`username` = '".$username."',
			`sifra` = '".$sifra."',
			`ime` = '".$ime."',
			`prezime` = '".$prezime."',
			`email` = '".$email."',
			`status` = 'Aktivan',
			`lastlogin` = '0000-00-00 00:00:00',
			`lastactivity` = '0',
			`lastip` = '~',
			`lasthost` = '~',
			`kreiran` = '".date('Y-m-d')."',
			`token`= '',
			`avatar`= 'default.png',
			`sigkod` = '".$sigkod."',
			`zemlja`= '".$drzava."'		
		");
		
		$to = $email;
		$subject = "Registracija naloga";

		$message =
			"Pozdrav,  <b>".$ime." ".$prezime."</b><br /><br />
			Nedavno Vam je admin registrovao nalog na <b>GB Hoster</b>.<br />
			<br /><br />
			Username: ".$username."<br />
			Email Address: ".$email."<br />
			Password: ".$sifra2."<br />
			Sigurnosni kod: ".$sigkod." <b>Ovaj kod nemožete menjati pa vam preporučujemo da ga zapamtite jer vam je potreban.</b><br />
			Link sajta: <a href='http://gb-hoster.me'>http://gb-hoster.me</a><br /><br />
				
			---------<br />
			Ne odgovarajte na ovu poruku, ovo je samo informativna poruka!<br />
			Vas <b>GB Hoster!</b>";
				
				
		###
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: GB Hoster <podrska@'.$_SERVER['SERVER_NAME'].'>' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		#-----------------+
		$mail = mail($to, $subject, $message, $headers);
		#-----------------+
		if(!$mail)
		{
			echo 'Ne mogu poslati e-mail adresu.';
			die();
		}		
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Dodao klijenta <m>".$ime." ".$prezime."</m>" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Dodao klijenta <m>".$ime." ".$prezime."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Klijent je uspešno dodat.";
		$_SESSION['msg-type'] = "success";	
		
		header("Location: index.php");
		die();
	break;
	
	case 'pretraga':
		$email = mysql_real_escape_string($_POST['email']);
		//$email = htmlspecialchars($email);
		
		$id = mysql_query("SELECT `email`, `klijentid` FROM `klijenti` WHERE `email` = '".$email."'");
		
		if(mysql_num_rows($id) == 0)
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Klijent sa ovim e-mailom ne postoji u bazi.";
			$_SESSION['msg-type'] = "error";
			
			header("Location: index.php");
			die();
		}
		else
		{
			$inf = mysql_fetch_array($id);
			
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Klijent je pronadjen u bazi, trenutno gledate njegov profil.";
			$_SESSION['msg-type'] = "success";
			
			header("Location: klijent.php?id=".$inf['klijentid']);	
			die();
		}
		
	break;
	
	case 'pretragasrv':
		$srv = mysql_real_escape_string($_POST['srv']);
		
		$id = mysql_query("SELECT `id` FROM `serveri` WHERE `id` = '".$srv."'");
		
		if(mysql_num_rows($id) == 0)
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Server sa ovim ID-om ne postoji u bazi.";
			$_SESSION['msg-type'] = "error";
			
			header("Location: index.php");
			die();
		}
		else
		{
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Server je pronadjen u bazi, trenutno gledate njegov profil.";
			$_SESSION['msg-type'] = "success";
			
			header("Location: srv-pocetna.php?id=".$srv);	
			die();
		}
		
	break;
	
	case 'klijent_unban':
		samo_vlasnik($_SESSION['a_id']);
		$klijentid = mysqL_real_escape_string($_POST['id']);
		$banid = mysqL_real_escape_string($_POST['banid']);
		
		query_basic("DELETE FROM `banovi` WHERE `id` = '".$banid."'");
		query_basic("UPDATE `klijenti` SET `banovan` = '0' WHERE `klijentid` = '".$klijentid."'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Unbanovao klijenta <m>#{$klijentid}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Unbanovao klijenta <m>#{$klijentid}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Klijent je unbanovan.";
		$_SESSION['msg-type'] = "success";
			
		header("Location: klijenti.php?view=banovani");				
		die();
	break;
	
	case 'klijent_delete':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysqL_real_escape_string($_POST['id']);

		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'"));	

		$c_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$id."'"));			
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje klijenta" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Izbrisao klijenta <m>".$c_info['ime']." ".$c_info['prezime']."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
		
		$serveri = mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '".$id."'");

		require_once("./assets/libs/phpseclib/Crypt/AES.php");
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);		
		
		while($row = mysql_fetch_array($serveri))
		{
			$ip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$row['ip_id']."'");
			$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$row['box_id']."'");
			$password = $aes->decrypt($box['password']);
			server_izbrisi($row['id'], $id, $ip['ip'], $box['sshport'], $box['login'], $password);
		}
		
		query_basic("DELETE FROM `klijenti` WHERE `klijentid` = '".$id."'");
		query_basic("DELETE FROM `tiketi` WHERE `user_id` = '".$id."'");
		query_basic("DELETE FROM `tiketi_odgovori` WHERE `user_id` = '".$id."'");
		query_basic("DELETE FROM `klijenti_komentari` WHERE `klijentid` = '".$id."'");
		query_basic("DELETE FROM `reputacija` WHERE `klijentid` = '".$id."'");

		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Klijent je izbrisan.";
		$_SESSION['msg-type'] = "success";
			
		header("Location: klijenti.php?view=all");	
		die();
	break;
	
	case 'tiket_delete':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysqL_real_escape_string($_POST['id']);

		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE `id` = '".$_SESSION['a_id']."'"));	

		$c_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `tiketi` WHERE `id` = '".$id."'"));			
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Brisanje tiketa" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Izbrisao tiket <m>".$c_info['name']."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
		
		query_basic("DELETE FROM `tiketi` WHERE `id` = '".$id."'");

		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Tiket je izbrisan.";
		$_SESSION['msg-type'] = "success";
			
		header("Location: index.php");				
		die();
	break;	
	
	case 'ban_klijenta':
		$klijentid = $_POST['klijentid'];
		
		$datum = mysql_real_escape_string($_POST['datum']);
		$datum = htmlspecialchars($datum);
		
		$razlog = mysql_real_escape_string($_POST['razlog']);
		$razlog = htmlspecialchars($razlog);
		
		mysql_query("UPDATE `klijenti` SET `banovan` = '1' WHERE `klijentid` = '".$klijentid."'");
		
		mysql_query("INSERT INTO `banovi` SET
			`klijentid` = '".$klijentid."',
			`vreme` = '".strtotime(date("m/d/Y", time()))."',
			`razlog` = '".$razlog."',
			`trajanje` = '".strtotime($datum)."'");
			
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Banovao klijenta <m>#{$klijentid}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Banovao klijenta <m>#{$klijentid}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
			
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Klijent je banovan.";
		$_SESSION['msg-type'] = "success";
			
		header("Location: klijent.php?id=".$klijentid);			
		die();
	break;
	
	case 'emailbug':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		$odgovor = mysql_real_escape_string($_POST['odgovor']);
		$odgovor = nl2br(htmlspecialchars($odgovor));
		
		$klijentid = mysql_real_escape_string($_POST['klijentid']);
		$text = mysql_real_escape_string($_POST['text']);
		$naslov = mysql_real_escape_string($_POST['naslov']);
		
		$klijent = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$klijentid."'"));
		
		$to = $klijent['email'];
		$subject = "Odgovor: ".$naslov;
		$message = "Pozdrav <b>".$klijent['ime']." ".$klijent['prezime']."</b><br />
		Ovo je odgovor na vašu prijavu sa sajta koja glasi:<br />
		<span style='font-style: italic;'>".$text."</span><br /><br />
		<b>Odgovor administracije:</b><br />
		".$odgovor;
		###
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: GB Hoster <localhost@'.$_SERVER['SERVER_NAME'].'>' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		#-----------------+
		$mail = mail($to, $subject, $message, $headers);
		#-----------------+
		if(!$mail)
		{
		   exit("<h1><b>Error: message could not be sent.</b></h1>");
		}
		
		mysql_query("DELETE FROM `bug` WHERE `id` = '".$id."'");
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Odgovorili ste na ovu prijavu i prijava se odma izbrisala.";
		$_SESSION['msg-type'] = "success";
		
		header("Location: index.php");	
		die();
	break;
	
	case 'zakljucaj_tiket':
		$tiketid = mysql_real_escape_string($_POST['tiketid']);
		
		query_basic("UPDATE `tiketi` SET `status` = '3' WHERE `id` = '".$tiketid."'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Zakljucao tiket <m>#{$tiketid}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Zakljucao tiket <m>#{$tiketid}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());		
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Zakljucali ste tiket.";
		$_SESSION['msg-type'] = "success";
		
		$tiketic = query_fetch_assoc("SELECT * FROM `tiketi` WHERE `id` = '".$tiketid."'");
		
		$naslov = $tiketic['naslov'];
		
		if($naslov = "Team Speak 3") {
			query_basic("DELETE FROM `serveri_naruceni` WHERE `tiket_id` = '".$tiketid."'");
		}
		
		if($naslov = "Sinus Bot") {
			query_basic("DELETE FROM `serveri_naruceni` WHERE `tiket_id` = '".$tiketid."'");
		}
		header("Location: tiket.php?id=".$tiketid);	
		die();
	break;
	
	case 'odkljucaj_tiket':
		$tiketid = mysql_real_escape_string($_POST['tiketid']);
		
		query_basic("UPDATE `tiketi` SET `status` = '1' WHERE `id` = '".$tiketid."'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Otkljucao tiket <m>#{$tiketid}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Otkljucao tiket <m>#{$tiketid}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Zakljucali ste tiket.";
		$_SESSION['msg-type'] = "success";
		
		header("Location: tiket.php?id=".$tiketid);
		die();
	break;
	
	case 'prosl_tiket':
		$tiketid = mysql_real_escape_string($_POST['tiket']);
		$adminid = mysql_real_escape_string($_POST['admin']);
		query_basic("UPDATE `tiketi` SET `status` = '10', `admin` = '".$adminid."' WHERE `id` = '".$tiketid."'");	
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Prosledili ste tiket.";
		$_SESSION['msg-type'] = "success";
		
		header("Location: index.php");	
		die();
	break;
	
	case 'izbrisi-srv':
		samo_vlasnik($_SESSION['a_id']);
		
		require_once($_SERVER['DOCUMENT_ROOT']."/includes/func.server.inc.php");
		
		$serverid = mysql_real_escape_string($_POST['id']);
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		
		require_once("./assets/libs/phpseclib/Crypt/AES.php");
		$aes = new Crypt_AES();
		$aes->setKeyLength(256);
		$aes->setKey(CRYPT_KEY);	

		$password = $aes->decrypt($box['password']);

		$izbrisi = server_izbrisi($serverid, 'admin', $boxip['ip'], $box['sshport'], $box['login'], $password);
		
		if($izbrisi == "uspesno")
		{
			query_basic("DELETE FROM `tiketi` WHERE `server_id` = '".$serverid."'");
						
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Izbrisali ste server.";
			$_SESSION['msg-type'] = "success";
			header("Location: serveri.php?view=all");
			die();
		}
		else
		{
			$error = $izbrisi;
		}
		
		if(!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = "error";
			unset($error);
			header("Location: serveri.php?view=all");		
			die();
		}
	break;
	
	case 'masina_delete':
		samo_vlasnik($_SESSION['a_id']);
		$boxid = mysql_real_escape_string($_POST['id']);
		
		query_basic("DELETE FROM `box` WHERE `boxid` = '".$boxid."'");
		query_basic("DELETE FROM `serveri` WHERE `box_id` = '".$boxid."'");
		query_basic("DELETE FROM `boxip` WHERE `boxid` = '".$boxid."'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Izbrisao masinu <m>#{$boxid}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Izbrisao masinu <m>#{$boxid}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());		
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Izbrisali ste masinu i sve servere sa nje.";
		$_SESSION['msg-type'] = "success";
		
		header("Location: box_lista.php");	
		die();
	break;
	
	case 'gl_podes':
		samo_fs($_SESSION['a_id']);
		$iskljucen = mysql_real_escape_string($_POST['iskljucen']);
		$reg = mysql_real_escape_string($_POST['reg']);
		$log = mysql_real_escape_string($_POST['log']);
		$verzija = mysql_real_escape_string($_POST['verzija']);
		
		query_basic("UPDATE `config` SET `value` = '".$iskljucen."' WHERE `setting` = 'iskljucen'");
		query_basic("UPDATE `config` SET `value` = '".$reg."' WHERE `setting` = 'reg'");
		query_basic("UPDATE `config` SET `value` = '".$log."' WHERE `setting` = 'log'");
		query_basic("UPDATE `config` SET `value` = '".$verzija."' WHERE `setting` = 'verzija'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Promenio glavna podesavanja' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Promenio glavna podesavanja";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());		
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Glavna podešavanja su promenjena.";
		$_SESSION['msg-type'] = "success";
		
		header("Location: index.php");		
		die();
	break;
	
	case 'klijent_edit':
		samo_vlasnik($_SESSION['a_id']);
		$klijentid = mysql_real_escape_string($_POST['klijentid']);
		
		$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$klijentid."'");
		
		$username = mysql_real_escape_string($_POST['username']);
		$username = htmlspecialchars($username);
		
		$ime = mysql_real_escape_string($_POST['ime']);
		$ime = htmlspecialchars($ime);
		
		$email = mysql_real_escape_string($_POST['email']);
		$email = htmlspecialchars($email);
		
		$drzava = mysql_real_escape_string($_POST['zemlja']);
		$drzava = htmlspecialchars($drzava);
		
		$sifra = mysql_real_escape_string($_POST['password']);
		$sifra = htmlspecialchars($sifra);
		
		$sigkod = mysql_real_escape_string($_POST['sigkod']);
		$sigkod = htmlspecialchars($sigkod);
		
		$novac = $_POST['novac'];
		
		$novac = str_replace(",", ".", $novac);
		
		//$novac = $novac*0.01;

		if(empty($username))
		{
			$error = "Niste uneli korisnicko ime.";
		}
		
		if($username != $klijent['username'])
		{
			if(mysql_num_rows(mysql_query("SELECT `username` FROM `klijenti` WHERE `username` = '".$username."'")) != 0)
			{
				$error = "Username je vec u upotrebi.";
			}
		}
		
		if(empty($ime))
		{
			$error = "Niste uneli ime i prezime.";
		} else {
			$imepr = explode(" ", $ime);
			unset($ime);
			$ime = $imepr['0'];
			$prezime = $imepr['1'];		
		}
		
		if(empty($email))
		{
			$error = "Niste uneli e-mail.";
		}
		
		if($email != $klijent['email'])
		{		
			if(mysql_num_rows(mysql_query("SELECT `email` FROM `klijenti` WHERE `email` = '".$email."'")) != 0)
			{
				$error = "E-mail je vec u upotrebi.";
			}
		}
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: index.php" );
			die();
		}		

		if(empty($sifra))
		{
			mysql_query("UPDATE `klijenti` SET
				`username` = '".$username."',
				`ime` = '".$ime."',
				`prezime` = '".$prezime."',
				`email` = '".$email."',
				`status` = 'Aktivan',
				`sigkod` = '{$sigkod}',
				`novac` = '{$novac}',
				`zemlja`= '".$drzava."' WHERE `klijentid` = '".$klijentid."'	
			");
		}
		else
		{	
			$salt = hash('sha512', $username);
			$sifra = hash('sha512', $salt.$sifra);			
			mysql_query("UPDATE `klijenti` SET
				`username` = '".$username."',
				`sifra` = '".$sifra."',
				`ime` = '".$ime."',
				`prezime` = '".$prezime."',
				`email` = '".$email."',
				`status` = 'Aktivan',
				`sigkod` = '{$sigkod}',
				`novac` = '{$novac}',
				`zemlja`= '".$drzava."' WHERE `klijentid` = '".$klijentid."'	
			");		
		}
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Promenio klijenta <m>'.$ime.' '.$prezime.'</m>" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Promenio klijenta <m>".$ime." ".$prezime."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Klijent je uspešno promenjen.";
		$_SESSION['msg-type'] = "success";	
		
		header("Location: klijent.php?id=".$klijentid);
		die();
	break;
	
	case 'admin_edit':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		
		$admin = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$id."'");
		
		$username = mysql_real_escape_string($_POST['username']);
		$username = htmlspecialchars($username);
		
		$ime = mysql_real_escape_string($_POST['ime']);
		$ime = htmlspecialchars($ime);
		
		$email = mysql_real_escape_string($_POST['email']);
		$email = htmlspecialchars($email);
		
		$rank = mysql_real_escape_string($_POST['rank']);
		$rank = htmlspecialchars($rank);
		
		$sifra = mysql_real_escape_string($_POST['password']);
		$sifra = htmlspecialchars($sifra);
		
		if(empty($username))
		{
			$error = "Niste uneli korisnicko ime.";
		}
		
		if($username != $admin['username'])
		{
			if(query_numrows("SELECT * FROM `admin` WHERE `username` = '{$username}'") == 1)
			{
				$error = "To korisnicko ime vec postoji.";
			}
		}
		
		if(empty($ime))
		{
			$error = "Niste uneli ime i prezime.";
		} else {
			$imepr = explode(" ", $ime);
			unset($ime);
			$ime = $imepr['0'];
			$prezime = $imepr['1'];		
		}
		
		if(empty($email))
		{
			$error = "Niste uneli e-mail.";
		}
		
		if($email != $admin['email'])
		{		
			if(mysql_num_rows(mysql_query("SELECT `email` FROM `admin` WHERE `email` = '".$email."'")) != 0)
			{
				$error = "E-mail je vec u upotrebi.".$email."-".$admin['email'];
			}
		}
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: index.php" );
			die();
		}		

		if($rank == "admin") $boja = "red";
		else if($rank == "support") $boja = "#0088cc";		
		
		if(empty($sifra))
		{
			mysql_query("UPDATE `admin` SET
				`username` = '".$username."',
				`fname` = '".$ime."',
				`lname` = '".$prezime."',
				`email` = '".$email."',
				`status` = '".$rank."',
				`boja` = '".$boja."' WHERE `id` = '".$id."'
			");
		}
		else
		{		
			mysql_query("UPDATE `admin` SET
				`password` = '".sifra($sifra)."',
				`username` = '".$username."',
				`fname` = '".$ime."',
				`lname` = '".$prezime."',
				`email` = '".$email."',
				`status` = '".$rank."',
				`boja` = '".$boja."' WHERE `id` = '".$id."'
			");		
		}
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Promenio admina <m>'.$ime.' '.$prezime.'</m>" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Promenio admina <m>".$ime." ".$prezime."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Admin je uspešno promenjen.";
		$_SESSION['msg-type'] = "success";	
		
		header("Location: admin_pregled.php?id=".$id);
		die();
	break;	
	
	case 'admin_add':
		samo_vlasnik($_SESSION['a_id']);
		$username = mysql_real_escape_string($_POST['username']);
		$username = htmlspecialchars($username);
		
		$ime = mysql_real_escape_string($_POST['ime']);
		$ime = htmlspecialchars($ime);
		
		$email = mysql_real_escape_string($_POST['email']);
		$email = htmlspecialchars($email);

		$rank = mysql_real_escape_string($_POST['rank']);
		$rank = htmlspecialchars($rank);		
		
		$sifra = mysql_real_escape_string($_POST['password']);
		$sifra = htmlspecialchars($sifra);
		
		if(empty($username))
		{
			$error = "Niste uneli korisnicko ime.";
		}
		
		if(mysql_num_rows(mysql_query("SELECT `username` FROM `admin` WHERE `username` = '".$username."'")) != 0)
		{
			$error = "Username je vec u upotrebi.";
		}
		
		if(empty($ime))
		{
			$error = "Niste uneli ime i prezime.";
		} else {
			$imepr = explode(" ", $ime);
			unset($ime);
			$ime = $imepr['0'];
			$prezime = $imepr['1'];		
		}
		
		if(empty($email))
		{
			$error = "Niste uneli e-mail.";
		}
		
		if(mysql_num_rows(mysql_query("SELECT `email` FROM `admin` WHERE `email` = '".$email."'")) != 0)
		{
			$error = "E-mail je vec u upotrebi.";
		}
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			unset($error);
			header( "Location: index.php" );
			die();
		}		
		
		if(empty($sifra))
		{
			$sifra = randomSifra(8);
		} 
		
		$sifra2 = $sifra;
			
		$sifra = sifra($sifra);

		if($rank == "admin") $boja = "red";
		else if($rank == "support") $boja = "#0088cc";

		query_basic("INSERT INTO `admin` SET
			`username` = '{$username}',
			`password` = '{$sifra}',
			`fname` = '{$ime}',
			`lname` = '{$prezime}',
			`email` = '{$email}',
			`status` = '{$rank}',
			`lastactivity` = '0',
			`lastactivityname` = '~',
			`boja` = '{$boja}',
			`avatar`= 'default.png'	
		");
		
		$ida = mysql_insert_id();
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '{$_SESSION['a_id']}'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Dodao admina <m>'.$ime.' '.$prezime.'</m>" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Dodao admina <m>".$ime." ".$prezime."</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Admin je uspešno dodat.";
		$_SESSION['msg-type'] = "success";	
		
		header("Location: admin_pregled.php?id=".$ida);
		die();
	break;	

	case 'pluginadd':
		samo_vlasnik($_SESSION['a_id']);
		$ime = mysql_real_escape_string($_POST['ime']);
		$ime = htmlspecialchars($ime);
		
		$deskripcija = mysql_real_escape_string($_POST['deskripcija']);
		$deskripcija = htmlspecialchars($deskripcija);
		
		$prikaz = mysql_real_escape_string($_POST['skracenica']);
		$prikaz = htmlspecialchars($prikaz);
	
		$text = htmlspecialchars($_POST['text']);
		$text = mysql_real_escape_string($text);
		
		if(empty($ime)) $error = "Morate uneti ime plugina!";
		if(empty($deskripcija)) $error = "Morate uneti deskripciju!";
		if(empty($prikaz)) $error = "Morate uneti ime fajla!";
		if(empty($text)) $error = "Morate uneti text fajla!";
		
		$t = explode("-", $prikaz);
		if($t[0] != "plugins") $error = "Ime fajla mora poceti sa plugins-";
		unset($t);
		
		if(!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = "error";	
			header("Location: index.php");
			die();
		}

		query_basic("INSERT INTO `plugins` SET
			`ime` = '{$ime}',
			`deskripcija` = '{$deskripcija}',
			`prikaz` = '{$prikaz}',
			`text` = '{$text}'");

		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Dodao plugin <m>{$ime}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Dodao plugin <m>{$ime}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
			
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Plugin je uspešno dodat.";
		$_SESSION['msg-type'] = "success";	
		header("Location: pluginovi.php");
		die();			

	break;
	
	case 'pluginedit':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		
		$ime = mysql_real_escape_string($_POST['ime']);
		$ime = htmlspecialchars($ime);
		
		$deskripcija = mysql_real_escape_string($_POST['deskripcija']);
		$deskripcija = htmlspecialchars($deskripcija);
		
		$prikaz = mysql_real_escape_string($_POST['skracenica']);
		$prikaz = htmlspecialchars($prikaz);
	
		$text = htmlspecialchars($_POST['text']);
		$text = mysql_real_escape_string($text);
		
		if(empty($ime)) $error = "Morate uneti ime plugina!";
		if(empty($deskripcija)) $error = "Morate uneti deskripciju!";
		if(empty($prikaz)) $error = "Morate uneti ime fajla!";
		if(empty($text)) $error = "Morate uneti text fajla!";
		
		$t = explode("-", $prikaz);
		if($t[0] != "plugins") $error = "Ime fajla mora poceti sa plugins-";
		unset($t);
		
		if(!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = "error";	
			header("Location: index.php");
			die();
		}

		query_basic("UPDATE `plugins` SET
			`ime` = '{$ime}',
			`deskripcija` = '{$deskripcija}',
			`prikaz` = '{$prikaz}',
			`text` = '{$text}' WHERE `id` = '{$id}'");
			
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Promenio plugin <m>#{$id}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Promenio plugin <m>#{$id}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			

		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Plugin je uspešno promenjen.";
		$_SESSION['msg-type'] = "success";	
		header("Location: pluginovi.php");
		die();			

	break;
		
	case 'plugindel':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		
		query_basic("DELETE FROM `plugins` WHERE `id` = '{$id}'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Izbrisao plugin <m>#{$id}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Izbrisao plugin <m>#{$id}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());		
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Plugin je uspešno izbrisan.";
		$_SESSION['msg-type'] = "success";	
		header("Location: pluginovi.php");
		die();		
	break;
	
	case 'moddel':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		
		query_basic("DELETE FROM `modovi` WHERE `id` = '{$id}'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Izbrisao mod <m>#{$id}</m>' WHERE id='{$_SESSION["a_id"]}'");				
					
		$poruka = "Izbrisao mod <m>#{$id}</m>";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Mod je uspešno izbrisan.";
		$_SESSION['msg-type'] = "success";	
		header("Location: modovi.php");
		die();		
	break;	
	
	case 'folderadd':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		
		$folder = mysql_real_escape_string($_POST['folder']);
		$folder = htmlspecialchars($folder);

		$path = mysql_real_escape_string($_POST['lokacija']);

		if(strlen($folder) > 24) { $error = 'Duzina imena fajla ne sme biti veca od 24 slova.'; }
		if(strlen($folder) < 3) { $error = 'Duzina imena fajla mora biti veca od 3 slova.'; }
		
		if(!is_numeric($serverid)) { $error = 'Server ID mora biti u brojevnom formatu'; }
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$error = "Ne mogu se konektovati na FTP servera!";
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{		
	        ftp_pasv($ftp, true);
			if(!empty($path))
			{
				ftp_chdir($ftp, $path);	
			}
			
			if(ftp_mkdir($ftp, $folder))
			{
				$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Dodao folder <m>{$folder}</m> na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server[name]}</a>' WHERE id='{$_SESSION[a_id]}'");				
					
				$poruka = "Dodao folder <m>{$folder}</m> na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>";
				alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
			
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Dodali ste nov folder";
				header("Location: srv-webftp.php?id=".$serverid);
				die();
			}
			else
			{
				$error = 'Ne mogu napraviti folder.';
			}
		}
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			header("Location: srv-webftp.php?id=".$serverid);
			die();
		}
		ftp_close($ftp);
	break;
	
	case 'folderdel':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		
		$folder = mysql_real_escape_string($_POST['folder']);
		$folder = htmlspecialchars($folder);

		$path = mysql_real_escape_string($_POST['lokacija']);

		if(!is_numeric($serverid)) { $error = 'Server ID mora biti u brojevnom formatu'; }
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$error = "Ne mogu se konektovati na FTP servera!";
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{	
            ftp_pasv($ftp, true);	
			if(!empty($path))
			{
				ftp_chdir($ftp, $path);	
			}

			function ftp_delAll($conn_id,$dst_dir)
			{
				$ar_files = ftp_nlist($conn_id, $dst_dir);
				if (is_array($ar_files))
				{ 
					for ($i=0;$i<sizeof($ar_files);$i++)
					{ 
						$st_file = basename($ar_files[$i]);
						if($st_file == '.' || $st_file == '..') continue;
						if (ftp_size($conn_id, $dst_dir.'/'.$st_file) == -1) ftp_delAll($conn_id,  $dst_dir.'/'.$st_file); 
						else ftp_delete($conn_id,  $dst_dir.'/'.$st_file);
					}
					sleep(1);
					ob_flush() ;
				}
				if(ftp_rmdir($conn_id, $dst_dir)) return "true";
			}			
			
			function ftp_folderdel($conn_id,$dst_dir)
			{
				$ar_files = ftp_nlist($conn_id, $dst_dir);
				if (is_array($ar_files))
				{ 
					for ($i=0;$i<sizeof($ar_files);$i++)
					{ 
						$st_file = basename($ar_files[$i]);
						if($st_file == '.' || $st_file == '..') continue;
						if (ftp_size($conn_id, $dst_dir.'/'.$st_file) == -1)
						{ 
							ftp_delAll($conn_id,  $dst_dir.'/'.$st_file); 
						} 
						else 
						{
							ftp_delete($conn_id,  $dst_dir.'/'.$st_file);
						}
					}
					sleep(1);
					ob_flush() ;
				}
				if(ftp_rmdir($conn_id, $dst_dir)){
				return "true";
				}
			}			
			
			if(ftp_folderdel($ftp, $path.'/'.$folder))
			{
				ftp_close($ftp);
				
				$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Izbrisao folder <m>{$folder}</m> na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>' WHERE id='{$_SESSION["a_id"]}'");				
					
				$poruka = "Izbrisao folder <m>{$folder}</m> na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>";
				alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());					
				
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Izbrisali ste folder";
				header("Location: srv-webftp.php?id=".$serverid);
				die();
			}
			else
			{
				$error = 'Ne mogu izbrisati folder.';
			}
		}
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			header("Location: srv-webftp.php?id=".$serverid);
			die();
		}
	break;	
	
	case 'fajldel':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		
		$folder = mysql_real_escape_string($_POST['folder']);
		$folder = htmlspecialchars($folder);

		$path = mysql_real_escape_string($_POST['lokacija']);

		if(!is_numeric($serverid)) { $error = 'Server ID mora biti u brojevnom formatu'; }
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$error = "Ne mogu se konektovati na FTP servera!";
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{	
            ftp_pasv($ftp, true);	
			if(!empty($path))
			{
				ftp_chdir($ftp, $path);	
			}		
			
			if(ftp_delete($ftp, $path.'/'.$folder))
			{
				$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Izbrisao fajl na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>' WHERE id='{$_SESSION["a_id"]}'");				
					
				$poruka = "Izbrisao fajl na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>";
				alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
			
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Izbrisali ste fajl.";
				header("Location: srv-webftp.php?id=".$serverid);
				die();
			}
			else
			{
				$error = 'Ne mogu izbrisati fajl.';
			}
		}
		
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			header("Location: srv-webftp.php?id=".$serverid);
			die();
		}		
		ftp_close($ftp);
	break;
	
	case 'ftprename':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		
		$folder = mysql_real_escape_string($_POST['imeftp']);
		$folder = htmlspecialchars($folder);
		
		$ime = mysql_real_escape_string($_POST['imesf']);
		$ime = htmlspecialchars($ime);

		$path = mysql_real_escape_string($_POST['lokacija']);

		if(!is_numeric($serverid)) { $error = 'Server ID mora biti u brojevnom formatu'; }
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$error = "Ne mogu se konektovati na FTP servera!";
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{	
            ftp_pasv($ftp, true);	
			if(!empty($path))
			{
				ftp_chdir($ftp, $path);	
			}		
			
			if(ftp_rename($ftp, $folder, $ime))
			{
				$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Promenio ime fajla na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>' WHERE id='{$_SESSION["a_id"]}'");				
					
				$poruka = "Promenio ime fajla na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>";
				alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
			
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = "Promenili ste ime fajla/foldera.";
				header("Location: srv-webftp.php?id=".$serverid);
				die();
			}
			else
			{
				$error = 'To ime fajla/foldera vec postoji ili se dogodila neka greska.';
			}
		}
		if(!empty($error))
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			header("Location: srv-webftp.php?id=".$serverid);
			die();
		}		
		ftp_close($ftp);
	break;	
	
	case 'uploadfajla':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$path = mysql_real_escape_string($_POST['lokacija']);
		

		if(!is_numeric($serverid)) 
		{ 
			$_SESSION['msg-type'] = "success";
			$_SESSION['msg1'] = "Uspešno";		
			$_SESSION['msg2'] = 'Server ID mora biti u brojevnom formatu'; 
			header("Location: srv-webftp.php?id=".$serverid."&path=".$path); 
			die(); 
		}
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";		
			$_SESSION['msg2'] = "Ne mogu se konektovati na FTP servera!";
			header("Location: srv-webftp.php?id=".$serverid."&path=".$path);
			die();
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{		
	        ftp_pasv($ftp, true);
			if(!empty($path))
			{
				ftp_chdir($ftp, $path);	
			}		
			
			$fajl = $_FILES["file"]["tmp_name"];
			$ime_fajla = $_FILES["file"]["name"];
			$putanja_na_serveru = ''.$path.'/'.$ime_fajla.'';
			
			$temp = explode(".", $_FILES["file"]["name"]);
			if($temp[1] == "php") 
			{ 
				$_SESSION['msg-type'] = "error";
				$_SESSION['msg1'] = "Greška";			
				$_SESSION['msg2'] = 'Taj format nije dozvoljen.'; 
				header("Location: srv-webftp.php?id=".$serverid."&path=".$path);  
				die();
			}

			if($_FILES["file"]["size"] > 8388608) 
			{ 
				$_SESSION['msg-type'] = "error";
				$_SESSION['msg1'] = "Greška";			
				$_SESSION['msg2'] = 'Fajl moze biti najvise 8mb.'; 
				header("Location: srv-webftp.php?id=".$serverid."&path=".$path);  
				die();
			}
			
			if(!empty($path)) $putanja_na_serveru = $ime_fajla;
			else $putanja_na_serveru = $path.'/'.$ime_fajla;			

			if(ftp_put($ftp, $putanja_na_serveru, $fajl, FTP_BINARY))
			{
				$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Dodao fajl <m>{$ime_fajla}</m> na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>' WHERE id='{$_SESSION["a_id"]}'");				
					
				$poruka = "Dodao fajl <m>{$ime_fajla}</m> na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>";
				alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
			
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = 'Uspesno ste uploadovali fajl.';
				header("Location: srv-webftp.php?id=".$serverid."&path=".$path); 
				die();
			}
			else
			{
				header("Location: srv-webftp.php?id=".$serverid."&path=".$path);
				$_SESSION['msg-type'] = "error";
				$_SESSION['msg1'] = "Greška";				
				$_SESSION['msg2'] = 'Ne mogu uploadati fajl.';
				die();
			}
		}
		ftp_close($ftp);
	break;

	case 'spremanjefajla':
		$serverid = mysql_real_escape_string($_POST['serverid']);
		$path = mysql_real_escape_string($_POST['lokacija']);
		
		$tekst = $_POST['tekstf'];
		
		$fajl2 = mysql_real_escape_string($_POST['fajl2']);

		if(!is_numeric($serverid)) 
		{ 
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";			
			$_SESSION['msg2'] = 'Server ID mora biti u brojevnom formatu'; 
			header("Location: srv-webftp.php?id=".$serverid."&path=".$path);  
			die();
		}
		
		$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
		$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
		$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
		
		$ftp = ftp_connect($boxip['ip'], 21);
		if(!$ftp)
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";			
			$_SESSION['msg2'] = "Ne mogu se konektovati na FTP servera!";
			header("Location: srv-webftp.php?id=".$serverid."&path=".$path);
			die();
		}
			
		if (ftp_login($ftp, $server["username"], $server["password"]))
		{		
	        ftp_pasv($ftp, true);
			if(!empty($path))
			{
				ftp_chdir($ftp, $path);	
			}	

			$folder = 'cache_folder/panel_'.$server["username"].'_'.$fajl2;

			$fw = fopen(''.$folder.'', 'w+');
			$fb = fwrite($fw, stripslashes($tekst)) or die("Ne mogu spremiti fajl");
			$file = "$fajl2";
			$remote_file = ''.$path.'/'.$fajl2.'';
			if (ftp_put($ftp, $remote_file, $folder, FTP_BINARY)) 
			{	
				$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
					
				$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
				$lastactivename = mysql_query("UPDATE admin SET lastactivityname = 'Promenio fajl na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>' WHERE id='{$_SESSION["a_id"]}'");				
					
				$poruka = "Promenio fajl na serveru <a href=\"srv-pocetna.php?id={$serverid}\">{$server['name']}</a>";
				alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());					
			
				$_SESSION['msg-type'] = "success";
				$_SESSION['msg1'] = "Uspešno";
				$_SESSION['msg2'] = 'Uspesno ste promenili fajl.';
				header("Location: srv-webftp.php?id=".$serverid."&path=".$path); 
				die();
			} 
			else 
			{
				$_SESSION['msg-type'] = "error";
				$_SESSION['msg1'] = "Greška";			
				$_SESSION['msg2'] = "Ne mogu spremiti fajl.";
				header("Location: srv-webftp.php?id=".$serverid."&path=".$path);
				die();			
			}
			
			fclose($fw);

			unlink($folder);			
		}
		ftp_close($ftp);
	break;

	case 'modadd';
		samo_vlasnik($_SESSION['a_id']);
		$ime = sqli($_POST['ime']);
		$igra = sqli($_POST['igra']);
		$putanja = sqli($_POST['putanja']);
		$link = sqli($_POST['link']);
		$zipname = sqli($_POST['zipname']);
		$opis = sqli($_POST['opis']);
		$mapa = sqli($_POST['mapa']);
		$komanda = sqli($_POST['komanda']);
		$sakriven = sqli($_POST['sakriven']);
		$csrb = sqli($_POST['csrb']);
		$ccg = sqli($_POST['ccg']);
		$cbih = sqli($_POST['cbih']);
		$chr = sqli($_POST['chr']);
		$cmk = sqli($_POST['cmk']);
		$csrb_premium = sqli($_POST['csrb_premium']);
		$ccg_premium = sqli($_POST['ccg_premium']);
		$cbih_premium = sqli($_POST['cbih_premium']);
		$chr_premium = sqli($_POST['chr_premium']);
		$cmk_premium = sqli($_POST['cmk_premium']);
		
		$cena = $csrb.'|'.$ccg.'|'.$cmk.'|'.$chr.'|'.$cbih;
		$cena_premium = $csrb_premium.'|'.$ccg_premium.'|'.$cmk_premium.'|'.$chr_premium.'|'.$cbih_premium;
		
		query_basic("INSERT INTO `modovi` SET
			`ime` = '{$ime}',
			`igra` = '{$igra}',
			`opis` = '{$opis}',
			`putanja` = '{$putanja}',
			`link` = '{$link}',
			`zipname` = '{$zipname}',
			`mapa` = '{$mapa}',
			`sakriven` = '{$sakriven}',
			`cena` = '{$cena}',
			`cena_premium` = '{$cena_premium}',
			`komanda` = '{$komanda}'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Dodao mod #'.$id.'" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Dodao mod #{$id}";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());		
		
		$_SESSION['msg-type'] = "success";
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = 'Uspesno ste dodali mod.';
		header("Location: modovi.php"); 
		die();		
	break;
	
	case 'modedit':
		samo_vlasnik($_SESSION['a_id']);
		$id = mysql_real_escape_string($_POST['id']);
		$ime = sqli($_POST['ime']);
		$igra = sqli($_POST['igra']);
		$putanja = sqli($_POST['putanja']);
		$link = sqli($_POST['link']);
		$zipname = sqli($_POST['zipname']);
		$opis = sqli($_POST['opis']);
		$mapa = sqli($_POST['mapa']);
		$komanda = sqli($_POST['komanda']);
		$sakriven = sqli($_POST['sakriven']);
		$csrb = sqli($_POST['csrb']);
		$ccg = sqli($_POST['ccg']);
		$cbih = sqli($_POST['cbih']);
		$chr = sqli($_POST['chr']);
		$cmk = sqli($_POST['cmk']);
		$csrb_premium = sqli($_POST['csrb_premium']);
		$ccg_premium = sqli($_POST['ccg_premium']);
		$cbih_premium = sqli($_POST['cbih_premium']);
		$chr_premium = sqli($_POST['chr_premium']);
		$cmk_premium = sqli($_POST['cmk_premium']);
		$proslaigra = sqli($_POST['proslaigra']);
		
		$cena = $csrb.'|'.$ccg.'|'.$cmk.'|'.$chr.'|'.$cbih;
		$cena_premium = $csrb_premium.'|'.$ccg_premium.'|'.$cmk_premium.'|'.$chr_premium.'|'.$cbih_premium;
		
		query_basic("UPDATE `serveri` SET `igra` = '{$igra}' WHERE `igra` = '{$proslaigra}' AND `mod` = '{$id}'");
		
		query_basic("UPDATE `modovi` SET
			`ime` = '{$ime}',
			`igra` = '{$igra}',
			`opis` = '{$opis}',
			`putanja` = '{$putanja}',
			`link` = '{$link}',
			`zipname` = '{$zipname}',
			`mapa` = '{$mapa}',
			`sakriven` = '{$sakriven}',
			`cena` = '{$cena}',
			`cena_premium` = '{$cena_premium}',
			`komanda` = '{$komanda}' WHERE `id` = '{$id}'");
			
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Promenio mod #'.$id.'" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Promenio mod #{$id}";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());					
			
		$_SESSION['msg-type'] = "success";
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = 'Uspesno ste promenili mod.';
		header("Location: modovi.php"); 
		die();		
	break;
	
	case 'klijent-aktiviraj':
		$klijentid = sqli($_POST['klijentid']);
		
		$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '{$klijentid}'");
		
		if($klijent['status'] == "Aktivacija")
		{
			query_basic("UPDATE `klijenti` SET `status` = 'Aktivan' WHERE `klijentid` = '{$klijentid}'");
			
			$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
				
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Aktivirao klijenta #'.$klijentid.'" WHERE id="'.$_SESSION["a_id"].'"');				
				
			$poruka = "Aktivirao klijenta #{$klijentid}";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());					
			
			$_SESSION['msg-type'] = "success";
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Klijent je uspešno aktiviran";
			header("Location: klijent.php?id={$klijentid}");
			die();
		}
		else
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Klijent je već aktiviran";
			header("Location: klijent.php?id={$klijentid}");
			die();		
		}
	break;
	
		case 'sms-ok':
		$smsid = sqli($_POST['id']);
		
		$sms = query_fetch_assoc("SELECT * FROM `billing_sms` WHERE `id` = '{$smsid}'");
		
		if($sms['status'] == "pending" or "failed")
		{
			query_basic("UPDATE `billing_sms` SET `status` = 'OK' WHERE `id` = '{$smsid}'");
			
			$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
				
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Aktivirao sms #'.$smsid.'" WHERE id="'.$_SESSION["a_id"].'"');				
				
			$poruka = "Aktivirao sms #{$smsid}";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());					
			
			$_SESSION['msg-type'] = "success";
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "SMS je uspešno aktiviran";
			header("Location: index.php");
			die();
		}
		else
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "SMS je već aktiviran";
			header("Location: index.php");
			die();		
		}
	break;
	
	case 'sms-failed':
		$smsid = sqli($_POST['id']);
		
		$sms = query_fetch_assoc("SELECT * FROM `billing_sms` WHERE `id` = '{$smsid}'");
		
		if($sms['status'] == "pending" or "ok")
		{
			query_basic("UPDATE `billing_sms` SET `status` = 'Failed' WHERE `id` = '{$smsid}'");
			
			$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
				
			$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Odbio sms #'.$smsid.'" WHERE id="'.$_SESSION["a_id"].'"');				
				
			$poruka = "Odbio sms #{$smsid}";
			alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());					
			
			$_SESSION['msg-type'] = "success";
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "SMS je uspešno odbijen";
			header("Location: index.php");
			die();
		}
		else
		{
			$_SESSION['msg-type'] = "error";
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "SMS je već odbijen";
			header("Location: index.php");
			die();		
		}
	break;
	
	case 'ipadd':
		$boxid = sqli($_POST['boxid']);
		$ip = sqli($_POST['ip']);
		if (!validateIP($ip)) $error = "IP Mora biti u brojevnom formatu";
		
		if(!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = "error";
			unset($error);
			header("Location: box.php?id={$boxid}");		
			die();
		}
		
		query_basic("INSERT INTO `boxip` SET `boxid` = '{$boxid}', `ip` = '{$ip}'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Dodao ip adresu #'.$ip.'" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Dodao ip adresu #{$ip}";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());				
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Dodali ste ip adresu";
		$_SESSION['msg-type'] = "success";
		unset($error);
		header("Location: box.php?id={$boxid}");		
		die();
			
	break;
	
	case 'ipdel':
		$boxid = sqli($_POST['boxid']);
		$ip = sqli($_POST['ipid']);

		if(!empty($error))
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = "error";
			unset($error);
			header("Location: box.php?id={$boxid}");		
			die();
		}
		
		query_basic("DELETE FROM `boxip` WHERE `boxid` = '{$boxid}' AND `ipid` = '{$ip}'");
		
		$a_info = mysql_fetch_assoc(mysql_query("SELECT * FROM `admin` WHERE id = '".$_SESSION['a_id']."'"));		
			
		$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
		$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Izbrisao ip adresu #'.$ip.'" WHERE id="'.$_SESSION["a_id"].'"');				
			
		$poruka = "Izbrisao ip adresu #{$ip}";
		alog($a_info["id"], $poruka, $a_info['fname'].' '.$a_info['lname'], fuckcloudflare());			
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Izbrisali ste ip adresu";
		$_SESSION['msg-type'] = "success";
		unset($error);
		header("Location: box.php?id={$boxid}");		
		die();
			
	break;

	case 'mailtoall':
		samo_vlasnik($_SESSION['a_id']);
		$option = $_POST['option'];
		$subject = $_POST['subject'];
		$message = nl2br($_POST['message']);

		if(!is_numeric($option)) 
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Greška";
			$_SESSION['msg-type'] = "error";
			header("Location: index.php");		
			die();
		}

		$PosMin = time() - 1 * 800;

		if($option == 1) $sql = "SELECT * FROM `klijenti` WHERE `status` = 'Aktivan'";
		else if($option == 2) $sql = "SELECT * FROM `klijenti` WHERE `status` = 'Aktivan' AND `lastactivity` >= '{$PosMin}'";
		else die();

		$query = mysql_query($sql);

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: GB Hoster <mail@'.$_SERVER['SERVER_NAME'].'>' . "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		#-----------------+
		while($row = mysql_fetch_assoc($query))
		{
			$to = $row['email'];
			$mail = mail($to, $subject, $message, $headers);
		}
		#-----------------+
		if(!$mail)
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ne mogu poslati mail.";
			$_SESSION['msg-type'] = "error";
			header("Location: index.php");		
			die();
		}	
		else
		{
			$_SESSION['msg1'] = "Uspešno";
			$_SESSION['msg2'] = "Mail je poslat svima.";
			$_SESSION['msg-type'] = "success";
			header("Location: index.php");		
			die();			
		}
	break;
	
	case 'odobri_upatu':
		samo_vlasnik($_SESSION['a_id']);
		$id			=		mysqL_real_escape_string($_GET['id']);
		
		$uplate = mysql_query("SELECT * FROM `uplate` WHERE `id` = '$id'");
		
		if (!mysql_num_rows($uplate) > 0) {
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ova uplata ne postoji!";
			$_SESSION['msg-type'] = "error";
			header("Location: billings.php");
			die();
		}
		
		$uplate_i = mysql_fetch_array($uplate);
		
		if ($uplate_i['status'] == "2") {
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ne možete ponovo odobriti ovu uplatu!";
			$_SESSION['msg-type'] = "error";
			header("Location: billings.php");
			die();
		}
		
		query_basic("UPDATE `klijenti` SET `novac` = novac + '$uplate_i[novac]' WHERE `klijentid` = '$uplate_i[klijentid]'");
		query_basic("UPDATE `uplate` SET `status` = '2' WHERE `id` = '$uplate_i[id]'");
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Uspešno ste odobrili uplatu!";
		$_SESSION['msg-type'] = "success";
			
		header("Location: billings.php");				
		die();
	break;

	case 'odbij_upatu':
		samo_vlasnik($_SESSION['a_id']);
		$id			=		mysqL_real_escape_string($_GET['id']);
		
		$uplate = mysql_query("SELECT * FROM `uplate` WHERE `id` = '$id'");
		
		if (!mysql_num_rows($uplate) > 0) {
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ova uplata ne postoji!";
			$_SESSION['msg-type'] = "error";
			header("Location: billings.php");
			die();
		}
		
		$uplate_i = mysql_fetch_array($uplate);
		
		if ($uplate_i['status'] == "1") {
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ne možete ponovo odbiti ovu uplatu!";
			$_SESSION['msg-type'] = "error";
			header("Location: billings.php");
			die();
		}
		
		if ($uplate_i['status'] == "2") {
			query_basic("UPDATE `klijenti` SET `novac` = novac - '$uplate_i[novac]' WHERE `klijentid` = '$uplate_i[klijentid]'");
		}
		
		query_basic("UPDATE `uplate` SET `status` = '1' WHERE `id` = '$uplate_i[id]'");
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Uspešno ste odbili uplatu!";
		$_SESSION['msg-type'] = "success";
			
		header("Location: billings.php");				
		die();
	break;

	case 'obrisi_upatu':
		samo_vlasnik($_SESSION['a_id']);
		$id			=		mysqL_real_escape_string($_GET['id']);
		
		$uplate = mysql_query("SELECT * FROM `uplate` WHERE `id` = '$id'");
		
		if (!mysql_num_rows($uplate) > 0) {
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Ova uplata ne postoji!";
			$_SESSION['msg-type'] = "error";
			header("Location: billings.php");
			die();
		}
		
		$uplate_i = mysql_fetch_array($uplate);
		
		query_basic("DELETE FROM `uplate` WHERE `id` = '$uplate_i[id]'");
		
		$_SESSION['msg1'] = "Uspešno";
		$_SESSION['msg2'] = "Uspešno ste obrisali uplatu!";
		$_SESSION['msg-type'] = "success";
			
		header("Location: billings.php");				
		die();
	break;
}


?>
