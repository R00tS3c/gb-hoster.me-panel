<?php
$fajl = "login";
include("konfiguracija.php");
include("includes.php");

if(isset($_GET['mode']))
{
	if($_GET['mode'] == "klijenti")
	{
		$s = mysql_real_escape_string($_REQUEST["s"]);
		$output = "";
		$s = str_replace(" ", "%", $s);

		$query = "SELECT * FROM `klijenti` WHERE email LIKE '%" . $s . "%' or concat(' ',ime,' ',prezime,' ',username,' ') LIKE '%" . $s . "%' LIMIT 5";

		$squery = mysql_query($query);
		
		if((mysql_num_rows($squery) != 0) && ($s != ""))
		{
			while($sLookup = mysql_fetch_array($squery))
			{
				$displayName = $sLookup["email"];
				$ime = $sLookup["ime"];
				$prezime = $sLookup["prezime"];
				$output .= '<li style="overflow: hidden;" style="cursor: pointer;" onclick="sendToSearch(\'' . $displayName . '\')"><img style="width: 30px; height: 30px; float: left;" src="' . user_avatar($sLookup['klijentid']) . '" /><div style="margin-left: 45px; display: block; float: left; margin-left: 3px;"> ' . $ime . ' ' . $prezime . ' 
				<br /><span style="font-size: 11px;">' . $displayName . '</span></div></li>';
			}
		}	

		echo $output;
	}
	else if($_GET['mode'] == "serveri")
	{
		$s = mysql_real_escape_string($_REQUEST["s"]);
		$output = "";

		$ips = mysql_query("SELECT * FROM `boxip`");
		while($row = mysql_fetch_array($ips))
		{
			$s = str_replace($row['ip'], $row['ipid'], $s);
		}
		
		$s = str_replace(" ", "%", $s);

		$query = "SELECT * FROM `serveri` WHERE name LIKE '%" . $s . "%' or concat(ip_id,':',port) LIKE '%" . $s . "%' LIMIT 5";
		$squery = mysql_query($query);
		
		if((mysql_num_rows($squery) != 0) && ($s != ""))
		{
			while($row = mysql_fetch_array($squery))
			{		
				$ip = query_fetch_assoc("SELECT `ip` FROM `boxip` WHERE `ipid` = '{$row['ip_id']}'");
				$imeservera = $row["name"]; $ip = $ip['ip']; $port = $row["port"];
				$output .= '<li style="overflow: hidden;" style="cursor: pointer;" onclick="sendToSearch2(\'' . $row['id'] . '\')"><img style="width: 30px; height: 30px; float: left;" src="' . user_avatar($row['user_id']) . '" /><div style="margin-left: 45px; display: block; float: left; margin-left: 3px;"> ' . $ip . ':' . $port . ' 
				<br /><span style="font-size: 11px;">' . $imeservera . '</span></div></li>';
			}
		}	

		echo $output;
	}
}
?>