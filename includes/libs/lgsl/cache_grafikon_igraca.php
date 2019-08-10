<?php
//if(!isset($_GET['sifra'])) die("Morate uneti sifru!");

//$sifra = $_GET['sifra'];

//if($sifra != "krontab123xasd") die("Sifra nije tacna!");

$fajl = "login";

include("includess.php");

/* RANKOVANJE
			SERVERA */

$servers = mysql_query("SELECT * FROM lgsl ORDER BY ABS(`rank_bodovi`) DESC");

$rank = 1;
while($row = mysql_fetch_assoc($servers))
{
	$poriktaj_rang = "UPDATE `serveri` SET `rank` = '{$rank}' WHERE `id` = '{$row[id]}'";
	mysql_query($poriktaj_rang);
 
	$rank++;
}

function calculate_median($arr) 
{
	sort($arr);
	$count = count($arr);
	$middleval = floor(($count-1)/2);
	if($count % 2) $median = $arr[$middleval];
	else {
		$low = $arr[$middleval];
		$high = $arr[$middleval+1];
		$median = (($low+$high)/2);
	}
	return $high;
}

function calculate_average($arr) 
{
	$count = count($arr);
	foreach ($arr as $value) $total = $total + $value;
	$average = ($total/$count);
	return round($average, 1);
}

$sql = "SELECT * FROM `lgsl` ORDER BY id ASC";
$q = mysql_query($sql);

while ($kolona = mysql_fetch_array($q)) 
{
	$temp=explode(":",$kolona["igraci_5min"]);

	$temp1=explode(":",$temp[0]);
	$a1 = $temp1[0];
	$temp2=explode(":",$temp[1]);
	$a2 = $temp2[0];
	$temp3=explode(":",$temp[2]);
	$a3 = $temp3[0];
	$temp4=explode(":",$temp[3]);
	$a4 = $temp4[0];
	$temp5=explode(":",$temp[4]);
	$a5 = $temp5[0];
	$temp6=explode(":",$temp[5]);
	$a6 = $temp6[0];
	$temp7=explode(":",$temp[6]);
	$a7 = $temp7[0];
	$temp8=explode(":",$temp[7]);
	$a8 = $temp8[0];
	$temp9=explode(":",$temp[8]);
	$a9 = $temp9[0];
	$temp10=explode(":",$temp[9]);
	$a10 = $temp10[0];
	$temp11=explode(":",$temp[10]);
	$a11 = $temp11[0];
	$temp12=explode(":",$temp[11]);
	$a12 = $temp12[0];
	$temp13=explode(":",$temp[12]);
	$a13 = $temp13[0];
	$temp14=explode(":",$temp[13]);
	$a14 = $temp14[0];
	$temp15=explode(":",$temp[14]);
	$a15 = $temp15[0];
	$temp16=explode(":",$temp[15]);
	$a16 = $temp16[0];
	$temp17=explode(":",$temp[16]);
	$a17 = $temp17[0];
	$temp18=explode(":",$temp[17]);
	$a18 = $temp18[0];
	$temp19=explode(":",$temp[18]);
	$a19 = $temp19[0];
	$temp20=explode(":",$temp[19]);
	$a20 = $temp20[0];
	$temp21=explode(":",$temp[20]);
	$a21 = $temp21[0];
	$temp22=explode(":",$temp[21]);
	$a22 = $temp22[0];
	$temp23=explode(":",$temp[22]);
	$a23 = $temp23[0];
	$temp24=explode(":",$temp[23]);
	$a24 = $temp24[0];

	$temp25=explode(":",$temp[24]);
	$a25 = $temp25[0];
	$temp26=explode(":",$temp[25]);
	$a26 = $temp26[0];
	$temp27=explode(":",$temp[26]);
	$a27 = $temp27[0];
	$temp28=explode(":",$temp[27]);
	$a28 = $temp28[0];
	$temp29=explode(":",$temp[28]);
	$a29 = $temp29[0];
	$temp30=explode(":",$temp[29]);
	$a30 = $temp30[0];
	$temp31=explode(":",$temp[30]);
	$a31 = $temp31[0];
	$temp32=explode(":",$temp[31]);
	$a32 = $temp32[0];
	$temp33=explode(":",$temp[32]);
	$a33 = $temp33[0];
	$temp34=explode(":",$temp[33]);
	$a34 = $temp34[0];
	$temp35=explode(":",$temp[34]);
	$a35 = $temp35[0];
	$temp36=explode(":",$temp[35]);
	$a36 = $temp36[0];

	$temp37=explode(":",$temp[36]);
	$a37 = $temp37[0];
	$temp38=explode(":",$temp[37]);
	$a38 = $temp38[0];
	$temp39=explode(":",$temp[38]);
	$a39 = $temp39[0];
	$temp40=explode(":",$temp[39]);
	$a40 = $temp40[0];
	$temp41=explode(":",$temp[40]);
	$a41 = $temp41[0];
	$temp42=explode(":",$temp[41]);
	$a42 = $temp42[0];
	$temp43=explode(":",$temp[42]);
	$a43 = $temp43[0];
	$temp44=explode(":",$temp[43]);
	$a44 = $temp44[0];
	$temp45=explode(":",$temp[44]);
	$a45 = $temp45[0];
	$temp46=explode(":",$temp[45]);
	$a46 = $temp46[0];
	$temp47=explode(":",$temp[46]);
	$a47 = $temp47[0];
	$temp48=explode(":",$temp[47]);
	$a48 = $temp48[0];

	$home_values_array = array($a1,$a2,$a3,$a4,$a5,$a6);

	$average_home_value = calculate_median($home_values_array);

	$igrachi = explode(":",$kolona["igraci"]);

	$te2=explode(":",$igrachi[1]);
	$t2 = $te2[0];
		
	for ($i=48;$i>0;$i--) $igrachi[$i] = $igrachi[$i-1];
		
	$igrachi[0] = $average_home_value;
		
	$test = implode(":",$igrachi);
		
	$sql = "UPDATE lgsl SET igraci='$test' WHERE id=$kolona[id]";
	mysql_query($sql);
}

/* BOX LOAD */
$sql = "SELECT * FROM `box` ORDER BY boxid ASC";
$boxdata = mysql_query($sql);

while ($kolona = mysql_fetch_array($boxdata)) 
{
	$temp=explode(":",$kolona["box_load_5min"]);

	$temp1=explode(":",$temp[0]);
	$a1 = $temp1[0];
	$temp2=explode(":",$temp[1]);
	$a2 = $temp2[0];
	$temp3=explode(":",$temp[2]);
	$a3 = $temp3[0];
	$temp4=explode(":",$temp[3]);
	$a4 = $temp4[0];
	$temp5=explode(":",$temp[4]);
	$a5 = $temp5[0];
	$temp6=explode(":",$temp[5]);
	$a6 = $temp6[0];
	$temp7=explode(":",$temp[6]);
	$a7 = $temp7[0];
	$temp8=explode(":",$temp[7]);
	$a8 = $temp8[0];
	$temp9=explode(":",$temp[8]);
	$a9 = $temp9[0];
	$temp10=explode(":",$temp[9]);
	$a10 = $temp10[0];
	$temp11=explode(":",$temp[10]);
	$a11 = $temp11[0];
	$temp12=explode(":",$temp[11]);
	$a12 = $temp12[0];
	$temp13=explode(":",$temp[12]);
	$a13 = $temp13[0];
	$temp14=explode(":",$temp[13]);
	$a14 = $temp14[0];
	$temp15=explode(":",$temp[14]);
	$a15 = $temp15[0];
	$temp16=explode(":",$temp[15]);
	$a16 = $temp16[0];
	$temp17=explode(":",$temp[16]);
	$a17 = $temp17[0];
	$temp18=explode(":",$temp[17]);
	$a18 = $temp18[0];
	$temp19=explode(":",$temp[18]);
	$a19 = $temp19[0];
	$temp20=explode(":",$temp[19]);
	$a20 = $temp20[0];
	$temp21=explode(":",$temp[20]);
	$a21 = $temp21[0];
	$temp22=explode(":",$temp[21]);
	$a22 = $temp22[0];
	$temp23=explode(":",$temp[22]);
	$a23 = $temp23[0];
	$temp24=explode(":",$temp[23]);
	$a24 = $temp24[0];

	$temp25=explode(":",$temp[24]);
	$a25 = $temp25[0];
	$temp26=explode(":",$temp[25]);
	$a26 = $temp26[0];
	$temp27=explode(":",$temp[26]);
	$a27 = $temp27[0];
	$temp28=explode(":",$temp[27]);
	$a28 = $temp28[0];
	$temp29=explode(":",$temp[28]);
	$a29 = $temp29[0];
	$temp30=explode(":",$temp[29]);
	$a30 = $temp30[0];
	$temp31=explode(":",$temp[30]);
	$a31 = $temp31[0];
	$temp32=explode(":",$temp[31]);
	$a32 = $temp32[0];
	$temp33=explode(":",$temp[32]);
	$a33 = $temp33[0];
	$temp34=explode(":",$temp[33]);
	$a34 = $temp34[0];
	$temp35=explode(":",$temp[34]);
	$a35 = $temp35[0];
	$temp36=explode(":",$temp[35]);
	$a36 = $temp36[0];

	$temp37=explode(":",$temp[36]);
	$a37 = $temp37[0];
	$temp38=explode(":",$temp[37]);
	$a38 = $temp38[0];
	$temp39=explode(":",$temp[38]);
	$a39 = $temp39[0];
	$temp40=explode(":",$temp[39]);
	$a40 = $temp40[0];
	$temp41=explode(":",$temp[40]);
	$a41 = $temp41[0];
	$temp42=explode(":",$temp[41]);
	$a42 = $temp42[0];
	$temp43=explode(":",$temp[42]);
	$a43 = $temp43[0];
	$temp44=explode(":",$temp[43]);
	$a44 = $temp44[0];
	$temp45=explode(":",$temp[44]);
	$a45 = $temp45[0];
	$temp46=explode(":",$temp[45]);
	$a46 = $temp46[0];
	$temp47=explode(":",$temp[46]);
	$a47 = $temp47[0];
	$temp48=explode(":",$temp[47]);
	$a48 = $temp48[0];

	$home_values_array = array($a1,$a2,$a3,$a4,$a5,$a6);

	$average_home_value = calculate_median($home_values_array);

	$igrachi = explode(":",$kolona["box_load"]);

	$te2=explode(":",$igrachi[1]);
	$t2 = $te2[0];
		
	for ($i=48;$i>0;$i--) $igrachi[$i] = $igrachi[$i-1];
		
	$igrachi[0] = $average_home_value;
		
	$test = implode(":",$igrachi);
		
	$sql = "UPDATE `box` SET `box_load` = '$test' WHERE `boxid` = $kolona[boxid]";
	mysql_query($sql);
}

mysql_query( "UPDATE `config` SET `value` = '".date('Y-m-d H:i:s')."' WHERE `setting` = 'cache_grafik'" );

?>
