<?php

function samo_vlasnik($id)
{
	$admin = query_fetch_assoc("SELECT * FROM `admin` WHERE `id` = '".$id."'");
	
	if($admin['status'] != "admin")
	{
		$_SESSION['msg1'] = "Greška";
		$_SESSION['msg2'] = "Nemate pristup ovome!";
		$_SESSION['msg-type'] = "error";
		header("Location: index.php");
		die();
	}
	return 1;
}

function makeClickableLinks($text)
{

        $text = html_entity_decode($text);
        $text = " ".$text;
        $text = preg_replace("/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/",
                '<a href="\\1" target=_blank>\\1</a>', $text);
        $text = preg_replace("/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/",
                '<a href="\\1" target=_blank>\\1</a>', $text);
        $text = preg_replace("/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/",
        '\\1<a href="http://\\2" target=_blank>\\2</a>', $text);
        $text = preg_replace('/([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4})/',
        '<a href="mailto:\\1" target=_blank>\\1</a>', $text);
        return $text;
}

function randomSifra($duzina)
{
	$karakteri = "abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWXYZ";
	$string = str_shuffle($karakteri);
	$sifra = substr($string, 0, $duzina);
	return $sifra;
}

function proveraEmaila($email)
{
	if (preg_match('|^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$|i', $email))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

function proveraIme($ime)
{
	if(preg_match('/^[A-Z][a-zA-Z -]+$/', $ime))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

function potvrdiKlijenta()
{
	session_regenerate_id();
	###
	$token = session_id();
	###
	mysql_query( "UPDATE `klijenti` SET `token` = '".$token."' WHERE `klijentid` = '".$_SESSION['klijentid']."'" );
}

function klijentUlogovan()
{
	if (!empty($_SESSION['klijentid']) && is_numeric($_SESSION['klijentid']))
	{
		$verifikacija = mysql_query( "SELECT `username` FROM `klijenti` WHERE `klijentid` = '".$_SESSION['klijentid']."' && `status` = 'Aktivan'" );
		if (mysql_num_rows($verifikacija) == 1)
		{
			return TRUE;
		}
		unset($verifikacija);
	}
	return FALSE;
}

function logout()
{
	unset($_SESSION['captchamd5']);
	unset($_SESSION['captcha']);
	unset($_SESSION['klijentulogovan']);
	unset($_SESSION['klijentid']);
	unset($_SESSION['klijentusername']);
	unset($_SESSION['klijentime']);
	unset($_SESSION['klijentprezime']);
	unset($_SESSION['sigkod']);
	unset($_SESSION['loginpokusaja']);

	setcookie('l0g1nC', "", time() - 3600);
	setcookie('klijentUsername', "", time() - 3600);

}

function time_elapsed_A($secs){
    $bit = array(
        'y' => $secs / 31556926 % 12,
        'w' => $secs / 604800 % 52,
        'd' => $secs / 86400 % 7,
        'h' => $secs / 3600 % 24,
        'm' => $secs / 60 % 60,
        's' => $secs % 60
        );
		
	if(($secs % 60) <= 0) {
		$ret[] = 'Upravo sada';
	}else{		
		$ret[] = "pre&nbsp;"; 
	}
    foreach($bit as $k => $v)
	{
        if($v > 0)
		{
			$ret[] = $v . $k;
		}
	}
        
	$pre = "pre ";
    return join(' ', $ret);
}

function vreme($data) {
	$vreme = date("d.m.Y, H:i", $data);
	$time = explode(",", $vreme);
	
	$time[1] = "<z>" . $time[1] . "</z>";
	
	$datum = $time[0] . ',' . $time[1];
	
	return $datum;
}

function avatar($id, $width = 0, $height = 0, $margin = false) 
{
	if($width == '0' || $height == '0'){
		$avatar = "<img src='/avatar.php?mode=c&id={$id}' />";
		return $avatar;
		exit;
	}
	else
	{
		if($margin) $avatar = "<img src='/avatar.php?mode=c&id={$id}' style='margin: ".$margin."px; width: ".$width."px; height: ".$height."px;' />";
		else $avatar = "<img src='/avatar.php?mode=c&id={$id}' style='width: ".$width."px; height: ".$height."px;' />";
		return $avatar;
		exit;
	}
}

function avatar_t($id, $name, $width = 0, $height = 0, $margin = false) 
{
	if($width == '0' || $height == '0'){
		$avatar = "<img rel='tips' title='".$name."' src='/avatar.php?mode=c&id={$id}' />";
		return $avatar;
		exit;
	}
	else
	{
		if($margin) $avatar = "<img rel='tips' title='".$name."' src='/avatar.php?mode=c&id={$id}' style='margin: ".$margin."px; width: ".$width."px; height: ".$height."px;' />";
		else $avatar = "<img rel='tips' title='".$name."' src='/avatar.php?mode=c&id={$id}' style='width: ".$width."px; height: ".$height."px;' />";
		return $avatar;
		exit;
	}
}

function a_avatar($id, $width, $height) 
{
	if($width == '0' || $height == '0'){
		$avatar = "<img src='/avatar.php?mode=a&id={$id}' />";
		return $avatar;
		exit;
	}
	else
	{
		$avatar = "<img src='/avatar.php?mode=a&id={$id}' style='width: ".$width."px; height: ".$height."px;' />";
		return $avatar;
		exit;
	}
}

function novacmysql($novac, $drzava)
{
	if($drzava=="srb"){
		$novac = $novac*123.31;
		$novacc = $novac;
		return $novacc;	
	}else if($drzava=="hr"){
		$novac = $novac*7.67;	
		$novacc = $novac;
		return $novacc;		
	}else if($drzava=="bih"){
		$novac = $novac*1.96;		
		$novacc = $novac;
		return $novacc;		
	}else if($drzava=="cg" || $drzava == "other"){
		$novacc = $novac;
		return $novacc;		
	}else if($drzava=="mk"){
		$novac = $novac*61.67;	
		$novacc = $novac;
		return $novacc;		
	}
	return FALSE;
}

function novac($novac, $drzava)
{
	if($drzava=="srb"){
		$novac = $novac*123.31;	
		$novacc = number_format(floatval($novac), 2).' din';
		return $novacc;	
	}else if($drzava=="hr"){
		$novac = $novac*7.67;	
		$novacc = number_format(floatval($novac), 2).' kn';
		return $novacc;		
	}else if($drzava=="bih"){
		$novac = $novac*1.96;		
		$novacc = number_format(floatval($novac), 2).' km';
		return $novacc;		
	}else if($drzava == "cg" || $drzava == "other"){	
		$novacc = number_format(floatval($novac), 2).' eur';
		return $novacc;		
	}else if($drzava=="mk"){
		$novac = $novac*61.67;	
		$novacc = number_format(floatval($novac), 2).' den';
		return $novacc;		
	}
	return FALSE;
}



function novacval($novac, $drzava)
{
	if($drzava=="srb"){
		$novacc = number_format(floatval($novac), 2).' din';
		return $novacc;	
	}else if($drzava=="hr"){
		$novacc = number_format(floatval($novac), 2).' kn';
		return $novacc;		
	}else if($drzava=="bih"){	
		$novacc = number_format(floatval($novac), 2).' km';
		return $novacc;		
	}else if($drzava=="cg" || $drzava == "other"){	
		$novacc = number_format(floatval($novac), 2).' eur';
		return $novacc;		
	}else if($drzava=="mk"){	
		$novacc = number_format(floatval($novac), 2).' den';
		return $novacc;		
	}
	return FALSE;
}

function novac_srb($novac, $drzava)
{
	if($drzava=="srb"){
		$novacc = $novac*0.0081;
		return $novacc;	
	}else if($drzava=="hr"){
		$novacc = $novac*0.13;	
		return $novacc;		
	}else if($drzava=="bih"){
		$novacc = $novac*0.51;		
		return $novacc;		
	}else if($drzava=="cg" || $drzava == "other"){
		$novacc = $novac;	
		return $novacc;		
	}else if($drzava=="mk"){
		$novacc = $novac*0.016;	
		return $novacc;		
	}
	return FALSE;
}

function tiket_prioritet($id)
{
	global $jezik;
	if($id == "1")
	{
		$prioritet = $jezik['text237'];
	}
	else if($id == "2")
	{
		$prioritet = $jezik['text238'];
	}
	else if($id == "3")
	{
		$prioritet = $jezik['text239'];
	}
	return $prioritet;
}

function billing_status($status)
{
	global $jezik;
	if($status == "Leglo") return '<span style="color: #4ED000;">'.$jezik['text271'].'</span>';
	else if($status == "Ceka proveru") return '<span style="color: #4ED000;">'.$jezik['text272'].'</span>';
	else if($status == "Nije leglo") return '<span style="color: red;">'.$jezik['text273'].'</span>';
	else if($status == "Na cekanju") return '<span style="color: #FF6E00;">'.$jezik['text274'].'</span>';
}
function sms_status($status)
{
	global $jezik;
	if($status == "OK") return '<span style="color: #4ED000;">OK</span>';
	else if($status == "pending") return '<span style="color: #FF6E00;">Pending</span>';
	else if($status == "Failed") return '<span style="color: red;">Failed</span>';
}

function tiket_status($status)
{
	global $jezik;	
	if($status=="1")
	{
		$st = '<div class="tstatus" style="color: #4ED000;">'.$jezik['text275'].'</div>';
		return $st;
	}
	else if($status=="4")
	{
		$st = '<div class="tstatus" style="color: #00C5FF;">'.$jezik['text276'].'</div>';
		return $st;
	}
	else if($status=="10")
	{
		$st = '<div class="tstatus" style="color: #00C5FF;">'.$jezik['text277'].'</div>';
		return $st;
	}
	else if($status=="2")
	{
		$st = '<div class="tstatus" style="color: #FF6E00;">'.$jezik['text278'].'</div>';
		return $st;
	}
	else if($status=="3")
	{
		$st = '<div class="tstatus" style="color: #939393;">'.$jezik['text279'].'</div>';
		return $st;
	}
	else if($status=="5")
	{
		$st = '<div class="tstatus" style="color: orange;">'.$jezik['text278'].'</div>';
		return $st;
	}
	else if($status=="8")
	{
		$st = '<div class="tstatus" style="color: #4ED000;">'.$jezik['text275'].'</div>';
		return $st;
	}	
	return FALSE;
}

function tikett_status($status)
{
	global $jezik;	
	if($status=="1")
	{
		$st = '<span style="color: #4ED000;">'.$jezik['text275'].'</span>';
		return $st;
	}
	else if($status=="4")
	{
		$st = '<span style="color: #00C5FF;">'.$jezik['text276'].'</span>';
		return $st;
	}
	else if($status=="2")
	{
		$st = '<span style="color: #FF6E00;">'.$jezik['text278'].'</span>';
		return $st;
	}
	else if($status=="3")
	{
		$st = '<span style="color: #939393;">'.$jezik['text279'].'</span>';
		return $st;
	}
	else if($status=="5")
	{
		$st = '<span style="color: orange;">'.$jezik['text278'].'</span>';
		return $st;
	}
	else if($status=="8")
	{
		$st = '<span style="color: #4ED000;">'.$jezik['text275'].'</span>';
		return $st;
	}
	else if($status=="10")
	{
		$st = '<span style="color: #00C5FF;">'.$jezik['text277'].'</span>';
		return $st;
	}
	return FALSE;
}

function klijent_log($cid, $poruka, $ime, $ip, $vreme)
{
	query_basic("INSERT INTO `logovi` SET
		`clientid` = '".$cid."',
		`message` = '".$poruka."',
		`name` = '".$ime."',
		`ip` = '".$ip."',
		`vreme` = '".$vreme."'");
	return TRUE;
}

function klijent_activity($id)
{
	query_basic("UPDATE `klijenti` SET `lastactivity` = '".time()."' WHERE `klijentid` = '".$id."'");
}

function get_status($last, $all = false)
{
	global $jezik;
	$times = array(
		'online'	=> 0,
		'idle'		=> 300,
		'offline'	=> 700,
	);

	if($all) $status = '<font color="cbff65">ONLINE</font>';
	else $status = '<span style="color: #cbff65;">Online</span>';
	if ($last < (time() - $times['idle']))
	{
		$status = '<m>'.$jezik['text280'].'</m>';
	}
	if ($last < (time() - $times['offline']))
	{
		if($all) $status = '<font color="red">OFFLINE</font>';
		else $status = '<span style="color: red;">Offline</span>';
	}		
	return $status;
}

function drzava($d)
{
	if($d == "srb") $drzava = "Srbija";
	else if($d == "hr") $drzava = "Hrvatska";
	else if($d == "bih") $drzava = "Bosna i Hercegovina";
	else if($d == "mk") $drzava = "Makedonija";
	else if($d == "cg") $drzava = "Crna gora";
	else if($d == "other") $drzava = "No Balkan";
	return $drzava;
}

function drzavaimg($d)
{
	if($d == "srb") $drzava = "<img src='./assets/blue/img/din.png' /> Srbija";
	else if($d == "hr") $drzava = "<img src='./assets/blue/img/kn.png' /> Hrvatska";
	else if($d == "bih") $drzava = "<img src='./assets/blue/img/km.png' /> Bosna i Hercegovina";
	else if($d == "mk") $drzava = "<img src='./assets/blue/img/den.png' /> Makedonija";
	else if($d == "cg") $drzava = "<img src='./assets/blue/img/e.png' /> Crna gora";
	else if($d == "other") $drzava = "<img src='./assets/blue/img/e.png' /> No Balkan";
	return $drzava;
}


function drzava_valuta($d)
{
	if($d == "srb") $drzava = "din";
	else if($d == "hr") $drzava = "kn";
	else if($d == "bih") $drzava = "km";
	else if($d == "mk") $drzava = "den";
	else if($d == "cg" || $d == "other") $drzava = "€";
	return $drzava;
}

function igra($i)
{
	if($i == "1") $igra = "<img width='16' height='16' src='./assets/img/game-cs.png' /> Counter-Strike 1.6";
	else if($i == "2") $igra = "<img width='16' height='16' src='./assets/img/game-samp.png' /> San Andreas Multiplayer";
	else if($i == "3") $igra = "<img width='16' height='16' src='./assets/img/game-minecraft.png' /> Minecraft";
	else if($i == "4") $igra = "<img width='16' height='16' src='./assets/img/game-cs.png' /> Call of Duty 4";
	else if($i == "5") $igra = "<img width='16' height='16' src='./assets/img/game-cs.png' /> Multi Theft Auto";
	return $igra;
}

function lokacija_ded($i)
{
	if($i == "1") $lok = "Premium - Srbija";
	else if($i == "2") $lok = "Lite - Nemaèka";
	return $lok;
}

function getStatus($ip, $port)
{
	if($socket = @fsockopen($ip, $port, $errno, $errstr, 1))
	{
		fclose($socket);
		return 'Online';
	}
	else
	{
		return 'Offline';
	}
}

function adminRank($rank)
{
	if($rank == "admin")
	{
		$rankb = "Administrator";
	}
	else if($rank == "support")
	{
		$rankb = "Radnik";
	}
	else
	{
		$rankb = "";
	}
	return $rankb;
}

function ipadresa($srvid)
{
	$port = query_fetch_assoc("SELECT `port`, `ip_id` FROM `serveri` WHERE `id` = '".$srvid."'");
	$ip = query_fetch_assoc("SELECT `ip` FROM `boxip` WHERE `ipid` = '".$port['ip_id']."'");	
	
        $ip2 = $ip['ip'];

        $ip = $ip2.":".$port['port'];

        

        if($ip2 == "192.168.168.2"){

           $ip = "37.187.190.59".":".$port['port'];
        }
		else if($ip2 == "192.168.150.2"){

           $ip = "37.59.144.83".":".$port['port'];
        }
		else if($ip2 == "192.168.10.2"){

           $ip = "51.254.49.222".":".$port['port'];
        }
		else if($ip2 == "192.168.21.2"){

           $ip = "37.59.144.81".":".$port['port'];
        }
        else if($ip2 == "192.168.31.2"){

           $ip = "92.222.234.250".":".$port['port'];
        }

	return $ip;
}

function srv_status($status)
{
	global $jezik;
	if($status=="Aktivan")
	{
		$st = '<span style="color: #4ED000;">'.$jezik['text281'].'</span>';
		return $st;
	}
	else if($status=="Suspendovan")
	{
		$st = '<span style="color: #FF0000;">'.$jezik['text282'].'</span>';
		return $st;
	}
	else if($status=="Istekao")
	{
		$st = '<span style="color: yellow;">'.$jezik['text283'].'</span>';
		return $st;
	}
	return FALSE;
}

function srv_istekao($srvid)
{
	global $jezik;

	$da = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$srvid."'");
	$datum = $da['istice'];
	$sdatum = date("Y-m-d", time());
	
	$d = strtotime($datum);
	$s = strtotime($sdatum);
	if($s > $d)
	{
		$d = $d-$s;
		$dana = floor($d/(60*60*24));
		
		$dana = explode("-", $dana);
		
		$istekao = "<span style='color: #FF0000'>".$datum."</span> (".$jezik['text284']." <span style='color: rgba(255,255,255,0.6)'>".$dana[1]."</span> ".$jezik['text285'].")";
		$istekao .= "<form action='process.php' method='POST' id='produzisrv'>
						<input type='hidden' name='task' value='produzi-server'>
						<input type='hidden' name='srvid' value='".$srvid."'>
						<input type='hidden' name='klijentid' value='".$_SESSION['klijentid']."'>
						<button type='submit'>[ ".$jezik['text286']." ]</button>
					</form>";
	} 
	else
	{
		$d = $d-$s;
		$dana = floor($d/(60*60*24));
		
		if($dana == "0")
		{
			$istekao = "<span style='color: #FF0000'>".$datum."</span> (".$jezik['text603'].")";
			$istekao .= "<form action='process.php' method='POST' id='produzisrv'>
							<input type='hidden' name='task' value='produzi-server'>
							<input type='hidden' name='srvid' value='".$srvid."'>
							<input type='hidden' name='klijentid' value='".$_SESSION['klijentid']."'>
							<button type='submit'>[ ".$jezik['text286']." ]</button>
						</form>";		
		}
		else
		{
			$istekao = "<span style='color: #4ED000'>".$datum."</span> (".$jezik['text15123']." ".$jezik['text602']." <span style='color: rgba(255,255,255,0.6)'>".$dana."</span> ".$jezik['text285'].")";
		}
	}
	return $istekao;
}

function srv_mod($mod)
{
	$mod = query_fetch_assoc("SELECT * FROM `modovi` WHERE `id` = '".$mod."'");
	$mod = $mod['ime'];
	return $mod;
}

function reputacija($id)
{
	$rep = query_fetch_assoc("SELECT SUM(rep) as repu FROM `reputacija` WHERE `adminid` = '".$id."'");
	$rep = $rep['repu'];
	if($rep == 0)
	{
		$rep = "<span style='color: #4ED000;'>0</span>";
	}
	else if($rep > 0)
	{
		$rep = "<span style='color: #4ED000;'>+".$rep."</span>";
	}
	else if($rep < 0)
	{
		$rep = "<span style='color: #FF0000;'>".$rep."</span>";
	}
	return $rep;
}

function klijentServeri($klijentid)
{
	$brsrv = query_numrows("SELECT * FROM `serveri` WHERE `user_id` = '".$klijentid."'");
	return $brsrv;
}	

function cscfg($find, $serverid)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
	
	$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/cstrike/server.cfg";
				
	$contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";

	if(preg_match_all($pattern, $contents, $matches)){
	   $text = implode("\n", $matches[0]);
	   $g = explode('"', $text);
	   return $g[1];
	}
}

function sampcfg($find, $serverid)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
	
	$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/server.cfg";
				
	$contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";

	if(preg_match_all($pattern, $contents, $matches)){
		$text = implode("\n", $matches[0]);
		$g = explode(' ', $text);
		return $g[1];
	}
}

function mccfg($find, $serverid)
{
	$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
	$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
	$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
	
	$fajl = "ftp://$server[username]:$server[password]@$boxip[ip]:21/server.properties";
				
	$contents = file_get_contents($fajl);
	
	$pattern = preg_quote($find, '/');

	$pattern = "/^.*$pattern.*\$/m";

	if(preg_match_all($pattern, $contents, $matches)){
		$text = implode("\n", $matches[0]);
		$g = explode('=', $text);
		return $g[1];
	}
}

function sqli($text)
{
	$text = mysql_real_escape_string($text);
	$text = htmlspecialchars($text);
	
	return $text;
}


function resize($cover = false, $format2, $width = 150, $height = 150){
	if($cover) list($w, $h) = getimagesize($_FILES['cover']['tmp_name']);
	else list($w, $h) = getimagesize($_FILES['avatar']['tmp_name']);

	$ratio = max($width/$w, $height/$h);
	$h = ceil($height / $ratio);
	$x = ($w - $width / $ratio) / 2;
	$w = ceil($width / $ratio);
	
	if($cover)
	{
		$format = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

		$path = "./avatari/covers/{$_SESSION['klijentid']}{$format2}";

		$imgString = file_get_contents($_FILES['cover']['tmp_name']);

		$image = imagecreatefromstring($imgString);
		$tmp = imagecreatetruecolor($width, $height);
		imagecopyresampled($tmp, $image,
	  	0, 0,
	  	$x, 0,
	  	$width, $height,
	  	$w, $h);

		switch ($_FILES['cover']['type']) {
			case 'image/jpeg':
				imagejpeg($tmp, $path, 100);
				break;
			case 'image/png':
				imagepng($tmp, $path, 0);
				break;
			case 'image/gif':
				imagegif($tmp, $path);
				break;
			default:
				exit;
				break;
		}
	}
	else
	{
		$format = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));

		$path = "./avatari/{$_SESSION['klijentid']}{$format2}";

		$imgString = file_get_contents($_FILES['avatar']['tmp_name']);

		$image = imagecreatefromstring($imgString);
		$tmp = imagecreatetruecolor($width, $height);
		imagecopyresampled($tmp, $image,
	  	0, 0,
	  	$x, 0,
	  	$width, $height,
	  	$w, $h);

		switch ($_FILES['avatar']['type']) {
			case 'image/jpeg':
				imagejpeg($tmp, $path, 100);
				break;
			case 'image/png':
				imagepng($tmp, $path, 0);
				break;
			case 'image/gif':
				imagegif($tmp, $path);
				break;
			default:
				exit;
				break;
		}
	}
	return $path;

	imagedestroy($image);
	imagedestroy($tmp);
}

function translateMCColors($text) {
    $dictionary = array(
        '[30;22m' => '</span><span style="color: #000000;">', // §0 - Black
        '[34;22m' => '</span><span style="color: #0000AA;">', // §1 - Dark_Blue
        '[32;22m' => '</span><span style="color: #00AA00;">', // §2 - Dark_Green
        '[36;22m' => '</span><span style="color: #00AAAA;">', // §3 - Dark_Aqua
        '[31;22m' => '</span><span style="color: #AA0000;">', // §4 - Dark_Red
        '[35;22m' => '</span><span style="color: #AA00AA;">', // §5 - Purple
        '[33;22m' => '</span><span style="color: #FFAA00;">', // §6 - Gold
        '[37;22m' => '</span><span style="color: #AAAAAA;">', // §7 - Gray
        '[30;1m' => '</span><span style="color: #555555;">', // §8 - Dakr_Gray
        '[34;1m' => '</span><span style="color: #5555FF;">', // §9 - Blue
        '[32;1m' => '</span><span style="color: #55FF55;">', // §a - Green
        '[36;1m' => '</span><span style="color: #55FFFF;">', // §b - Aqua
        '[31;1m' => '</span><span style="color: #FF5555;">', // §c - Red
        '[35;1m' => '</span><span style="color: #FF55FF;">', // §d - Light_Purple
        '[33;1m' => '</span><span style="color: #FFFF55;">', // §e - Yellow
        '[37;1m' => '</span><span style="color: #FFFFFF;">', // §f - White
       
        '[0;30;22m' => '</span><span style="color: #000000;">', // §0 - Black
        '[0;34;22m' => '</span><span style="color: #0000AA;">', // §1 - Dark_Blue
        '[0;32;22m' => '</span><span style="color: #00AA00;">', // §2 - Dark_Green
        '[0;36;22m' => '</span><span style="color: #00AAAA;">', // §3 - Dark_Aqua
        '[0;31;22m' => '</span><span style="color: #AA0000;">', // §4 - Dark_Red
        '[0;35;22m' => '</span><span style="color: #AA00AA;">', // §5 - Purple
        '[0;33;22m' => '</span><span style="color: #FFAA00;">', // §6 - Gold
        '[0;37;22m' => '</span><span style="color: #AAAAAA;">', // §7 - Gray
        '[0;30;1m' => '</span><span style="color: #555555;">', // §8 - Dakr_Gray
        '[0;34;1m' => '</span><span style="color: #5555FF;">', // §9 - Blue
        '[0;32;1m' => '</span><span style="color: #55FF55;">', // §a - Green
        '[0;36;1m' => '</span><span style="color: #55FFFF;">', // §b - Aqua
        '[0;31;1m' => '</span><span style="color: #FF5555;">', // §c - Red
        '[0;35;1m' => '</span><span style="color: #FF55FF;">', // §d - Light_Purple
        '[0;33;1m' => '</span><span style="color: #FFFF55;">', // §e - Yellow
        '[0;37;1m' => '</span><span style="color: #FFFFFF;">', // §f - White
       
        '[5m' => '', // Obfuscated
        '[21m' => '<b>', // Bold
        '[9m' => '<s>', // Strikethrough
        '[4m' => '<u>', // Underline
        '[3m' => '<i>', // Italic
       
        '[0;39m' => '</b></s></u></i></span>', // Reset
        '[0m' => '</b></s></u></i></span>', // Reset
        '[m' => '</b></s></u></i></span>', // End
    );
  
	$text = str_replace("<", htmlentities("<"), $text);
	$text = str_replace(">", htmlentities(">"), $text);
    $text = str_replace(array_keys($dictionary), $dictionary, $text);
   
    return '<span style="color: #FFFFFF;">'.$text;
}

?>
