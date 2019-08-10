<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled servera";
$fajl = "server";
$srv = "1";

if(logged_in()) {
	
} else {
	header("Location: ./login");
}

if(empty($_GET['id']) or !is_numeric($_GET['id'])) 
{
	header("Location: index.php");
}

$serverid = mysql_real_escape_string($_GET['id']);

if(query_numrows("SELECT * FROM `box` WHERE `boxid` = '".$serverid."'") == 0)
{
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Taj server ne postoji.";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
}

$sql = "SELECT * FROM `box` WHERE `boxid` = '$serverid'";

if(query_numrows($sql) == 0) die("Taj server ne postoji u bazi!");

unset($sql);

/*---------------------------------------------------------------------------------*/
// Vreme grafika
$grafik_vreme[1] = date('H')%24; $grafik_vreme[2] = (date('H')+24-2)%24; 
$grafik_vreme[3] = (date('H')+24-4)%24; $grafik_vreme[4] = (date('H')+24-6)%24;
$grafik_vreme[5] = (date('H')+24-8)%24; $grafik_vreme[6] = (date('H')+24-10)%24; 
$grafik_vreme[7] = (date('H')+24-12)%24; $grafik_vreme[8] = (date('H')+24-14)%24;
$grafik_vreme[9] = (date('H')+24-16)%24; $grafik_vreme[10] = (date('H')+24-18)%24; 
$grafik_vreme[11] = (date('H')+24-20)%24; $grafik_vreme[12] = (date('H')+24-22)%24; 
$grafik_vreme[13] = (date('H')+24-24)%24;
/*---------------------------------------------------------------------------------*/
// Cache
$cache_path = "grafik/box:$serverid.png";
$cache_path = str_replace(":", "_", $cache_path);
/*---------------------------------------------------------------------------------*/

if ($stat = @stat($cache_path))
	$mtime = time() - $stat['mtime'];

if ((file_exists($cache_path)) && ($mtime < 300)) { mysql_close(); header("Content-type: image/png"); readfile($cache_path); }
else 
{
	$sql = "SELECT * FROM `box` WHERE `boxid` = '$serverid'";
	
	if(query_numrows($sql) == 0) die("Taj server ne postoji u bazi.");

	$server2 = query_fetch_assoc($sql);
	$podaci = unserialize(gzuncompress($server2['cache']));
	$tete =  $server2['box_load'];
	$playersmax = $podaci["{$column['boxid']}"]['loadavg']['loadavg'];

	$temp=explode(":",$server2["box_load"]);
	
	if(!empty($temp[0])){
	$temp0=explode(":",$temp[0]);
	$r0 = $temp0[0];} else $r0 = "";
	if(!empty($temp[1])){
	$temp1=explode(":",$temp[1]);
	$r1 = $temp1[0];} else $r1 = "";
	if(!empty($temp[2])){
	$temp2=explode(":",$temp[2]);
	$r2 = $temp2[0];} else $r2 = "";
	if(!empty($temp[3])){
	$temp3=explode(":",$temp[3]);
	$r3 = $temp3[0];} else $r3 = "";
	if(!empty($temp[4])){
	$temp4=explode(":",$temp[4]);
	$r4 = $temp4[0];} else $r4 = "";
	if(!empty($temp[5])){
	$temp5=explode(":",$temp[5]);
	$r5 = $temp5[0];} else $r5 = "";
	if(!empty($temp[6])){
	$temp6=explode(":",$temp[6]);
	$r6 = $temp6[0];} else $r6 = "";
	if(!empty($temp[7])){
	$temp7=explode(":",$temp[7]);
	$r7 = $temp7[0];} else $r7 = "";
	if(!empty($temp[8])){
	$temp8=explode(":",$temp[8]);
	$r8 = $temp8[0];} else $r8 = "";
	if(!empty($temp[9])){
	$temp9=explode(":",$temp[9]);
	$r9 = $temp9[0];} else $r9 = "";
	if(!empty($temp[10])){
	$temp10=explode(":",$temp[10]);
	$r10 = $temp10[0];} else $r10 = "";
	if(!empty($temp[11])){
	$temp11=explode(":",$temp[11]);
	$r11 = $temp11[0];} else $r11 = "";
	if(!empty($temp[12])){
	$temp12=explode(":",$temp[12]);
	$r12 = $temp12[0];} else $r12 = "";
	if(!empty($temp[13])){
	$temp13=explode(":",$temp[13]);
	$r13 = $temp13[0];} else $r13 = "";
	if(!empty($temp[14])){
	$temp14=explode(":",$temp[14]);
	$r14 = $temp14[0];} else $r14 = "";
	if(!empty($temp[15])){
	$temp15=explode(":",$temp[15]);
	$r15 = $temp15[0];} else $r15 = "";
	if(!empty($temp[16])){
	$temp16=explode(":",$temp[16]);
	$r16 = $temp16[0];} else $r16 = "";
	if(!empty($temp[17])){
	$temp17=explode(":",$temp[17]);
	$r17 = $temp17[0];} else $r17 = "";
	if(!empty($temp[18])){
	$temp18=explode(":",$temp[18]);
	$r18 = $temp18[0];} else $r18 = "";
	if(!empty($temp[19])){
	$temp19=explode(":",$temp[19]);
	$r19 = $temp19[0];} else $r19 = "";
	if(!empty($temp[20])){
	$temp20=explode(":",$temp[20]);
	$r20 = $temp20[0];} else $r20 = "";
	if(!empty($temp[21])){
	$temp21=explode(":",$temp[21]);
	$r21 = $temp21[0];} else $r21 = "";
	if(!empty($temp[22])){
	$temp22=explode(":",$temp[22]);
	$r22 = $temp22[0];} else $r22 = "";
	if(!empty($temp[23])){
	$temp23=explode(":",$temp[23]);
	$r23 = $temp23[0];} else $r23 = "";
	if(!empty($temp[24])){
	$temp24=explode(":",$temp[24]);
	$r24 = $temp24[0];} else $r24 = "";
	if(!empty($temp[25])){
	$temp25=explode(":",$temp[25]);
	$r25 = $temp25[0];} else $r25 = "";
	if(!empty($temp[26])){
	$temp26=explode(":",$temp[26]);
	$r26 = $temp26[0];} else $r26 = "";
	if(!empty($temp[27])){
	$temp27=explode(":",$temp[27]);
	$r27 = $temp27[0];} else $r27 = "";
	if(!empty($temp[28])){
	$temp28=explode(":",$temp[28]);
	$r28 = $temp28[0];} else $r28 = "";
	if(!empty($temp[29])){
	$temp29=explode(":",$temp[29]);
	$r29 = $temp29[0];} else $r29 = "";
	if(!empty($temp[30])){
	$temp30=explode(":",$temp[30]);
	$r30 = $temp30[0];} else $r30 = "";
	if(!empty($temp[31])){
	$temp31=explode(":",$temp[31]);
	$r31 = $temp31[0];} else $r31 = "";
	if(!empty($temp[32])){
	$temp32=explode(":",$temp[32]);
	$r32 = $temp32[0];} else $r32 = "";
	if(!empty($temp[33])){
	$temp33=explode(":",$temp[33]);
	$r33 = $temp33[0];} else $r33 = "";
	if(!empty($temp[34])){
	$temp34=explode(":",$temp[34]);
	$r34 = $temp34[0];} else $r34 = "";
	if(!empty($temp[35])){
	$temp35=explode(":",$temp[35]);
	$r35 = $temp35[0];} else $r35 = "";
	if(!empty($temp[36])){
	$temp36=explode(":",$temp[36]);
	$r36 = $temp36[0];} else $r36 = "";
	if(!empty($temp[37])){
	$temp37=explode(":",$temp[37]);
	$r37 = $temp37[0];} else $r37 = "";
	if(!empty($temp[38])){
	$temp38=explode(":",$temp[38]);
	$r38 = $temp38[0];} else $r38 = "";
	if(!empty($temp[39])){
	$temp39=explode(":",$temp[39]);
	$r39 = $temp39[0];} else $r39 = "";
	if(!empty($temp[40])){
	$temp40=explode(":",$temp[40]);
	$r40 = $temp40[0];} else $r40 = "";
	if(!empty($temp[41])){
	$temp41=explode(":",$temp[41]);
	$r41 = $temp41[0];} else $r41 = "";
	if(!empty($temp[42])){
	$temp42=explode(":",$temp[42]);
	$r42 = $temp42[0];} else $r42 = "";
	if(!empty($temp[43])){
	$temp43=explode(":",$temp[43]);
	$r43= $temp43[0];} else $r43 = "";
	if(!empty($temp[44])){
	$temp44=explode(":",$temp[44]);
	$r44 = $temp44[0];} else $r44 = "";
	if(!empty($temp[45])){
	$temp45=explode(":",$temp[45]);
	$r45 = $temp45[0];} else $r45 = "";
	if(!empty($temp[46])){
	$temp46=explode(":",$temp[46]);
	$r46 = $temp46[0];} else $r46 = "";
	if(!empty($temp[47])){
	$temp47=explode(":",$temp[47]);
	$r47 = $temp47[0];} else $r47 = "";
	if(!empty($temp[48])){
	$temp48=explode(":",$temp[48]);
	$r48 = $temp48[0];} else $r48 = "";

	$data= array($r48,$r47,$r46,$r45,$r44,$r43,$r42,$r41,$r40,$r39,$r38,$r37,$r36,$r35,$r34,$r33,$r32,$r31,$r30,$r29,$r28,$r27,$r26,$r25,$r24,$r23,$r22,$r21,$r20,$r19,$r18,$r17,$r16,$r15,$r14,$r13,$r12,$r11,$r10,$r9,$r8,$r7,$r6,$r5,$r4,$r3,$r2,$r1,$r0);

	$tot_width = 330;
	$tot_height = 170;

	$count = count($data);
	
	if ($playersmax < 5) $max = 10;
	else $max = $playersmax;

	$ymax = $max;
	$ymin = $ymax-($tot_height/80);

	$margins = 20;
	$hlin = 6;

	$graph_width = $tot_width - $margins * 2+13;
	$graph_height = $tot_height - $margins * 2;


	$image = imagecreatetruecolor(330, 168);

	$bjela = imagecolorallocate($image,40, 40, 40);
	$zuta = imagecolorallocate($image,255, 170, 0);

	$ratio = $tot_height/($ymax-$ymin);
	$ratio2 = $graph_height/$max;
	$hinc = $graph_width/$count;
	$vinc = $graph_height/$hlin;

	imagefilledrectangle($image,1,1,$tot_width-2,$tot_height-2,0);

	for($i=0;$i<=$hlin;$i++)
	{
        $y=$tot_height - $margins - $vinc * $i ;
		imageline($image, $margins-1, $y, $tot_width - $margins+6, $y, $bjela);	
		$ylabel = $ymax-2 - intval($vinc * $i / $ratio2);
		$v = intval($vinc * $i / $ratio2);
		imagestring($image,1,5,$y-2,$v,$bjela);	
	}

	imageline($image, 18, 20, 18, 153, $bjela);
	imageline($image, 317, 20, 317, 150, $bjela);
	imageline($image, $margins-5, 150, $tot_width - $margins+6, 150, $bjela);


	$offsetX = 335;
	$offsetY = 154;
	for ($i = 1; $i < 13; $i++)
	{
		if ($grafik_vreme[$i] <= 9)
		{
			$data2 = $grafik_vreme[$i];
			$grafik_vreme[$i]= "0".$data2;
		}
		imageline($image,$offsetX+7-($i*25),$offsetY-1,$offsetX+7-($i*25),$offsetY-3,$bjela);
		imageline($image,$offsetX+1-($i*25),$offsetY-3,$offsetX+1-($i*25),$offsetY-4,$bjela);
		imagestring($image, 1, $offsetX-($i*25), $offsetY+3, $grafik_vreme[$i], $bjela );
	}
		
	for ($i = 1; $i < 12; $i++) 
	{
		imageline($image,$offsetX-5-($i*25),$offsetY-2,$offsetX-5-($i*25),$offsetY-4,$bjela);
		imageline($image,$offsetX-12-($i*25),$offsetY-3,$offsetX-12-($i*25),$offsetY-4,$bjela);
	}
	
	imageline($image,$offsetX-8-($i*25),$offsetY-3,$offsetX-8-($i*25),$offsetY-4,$bjela);

    for ($i=1; $i<$count; $i++)
	{
		$x1 = ($i-2)*$hinc+$margins;
		$x2 = $x1 + $hinc;
		$y1 = $margins + $graph_height -  $data[$i-1]*$ratio2;
		$y2 = $tot_height - $margins - $data[$i]*$ratio2;

		imagesmoothline($image, $x1+6,$y1,$x2+6,$y2, $zuta);
	}

	header("Content-type: image/png");

	imagecolortransparent($image, 0);
	imagepng($image);
	imagepng($image, $cache_path);
	imagedestroy($image);

}

function imagesmoothline ( $image , $x1 , $y1 , $x2 , $y2 , $color )
{
	$colors = imagecolorsforindex ( $image , $color );
	if ( $x1 == $x2 )
	{
		imageline ( $image , $x1 , $y1 , $x2 , $y2 , $color ); // Vertical line
	}
	else
	{
		$m = ( $y2 - $y1 ) / ( $x2 - $x1 );
		$b = $y1 - $m * $x1;
		if ( abs ( $m ) <= 1 )
		{
			$x = min ( $x1 , $x2 );
			$endx = max ( $x1 , $x2 );
			while ( $x <= $endx )
			{
				$y = $m * $x + $b;
				$y == floor ( $y ) ? $ya = 1 : $ya = $y - floor ( $y );
				$yb = ceil ( $y ) - $y;
				$tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , $x , floor ( $y ) ) );
				$tempcolors['red'] = $tempcolors['red'] * $ya + $colors['red'] * $yb;
				$tempcolors['green'] = $tempcolors['green'] * $ya + $colors['green'] * $yb;
				$tempcolors['blue'] = $tempcolors['blue'] * $ya + $colors['blue'] * $yb;
				if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
				imagesetpixel ( $image , $x , floor ( $y ) , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
				$tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , $x , ceil ( $y ) ) );
				$tempcolors['red'] = $tempcolors['red'] * $yb + $colors['red'] * $ya;
				$tempcolors['green'] = $tempcolors['green'] * $yb + $colors['green'] * $ya;
				$tempcolors['blue'] = $tempcolors['blue'] * $yb + $colors['blue'] * $ya;
				if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
				imagesetpixel ( $image , $x , ceil ( $y ) , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
				$x ++;
			}
		}
		else
		{
			$y = min ( $y1 , $y2 );
			$endy = max ( $y1 , $y2 );
			while ( $y <= $endy )
			{
				$x = ( $y - $b ) / $m;
				$x == floor ( $x ) ? $xa = 1 : $xa = $x - floor ( $x );
				$xb = ceil ( $x ) - $x;
				$tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , floor ( $x ) , $y ) );
				$tempcolors['red'] = $tempcolors['red'] * $xa + $colors['red'] * $xb;
				$tempcolors['green'] = $tempcolors['green'] * $xa + $colors['green'] * $xb;
				$tempcolors['blue'] = $tempcolors['blue'] * $xa + $colors['blue'] * $xb;
				if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
				imagesetpixel ( $image , floor ( $x ) , $y , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
				$tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , ceil ( $x ) , $y ) );
				$tempcolors['red'] = $tempcolors['red'] * $xb + $colors['red'] * $xa;
				$tempcolors['green'] = $tempcolors['green'] * $xb + $colors['green'] * $xa;
				$tempcolors['blue'] = $tempcolors['blue'] * $xb + $colors['blue'] * $xa;
				if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
				imagesetpixel ( $image , ceil ( $x ) , $y , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
				$y ++;
			}
		}
	}
}

mysql_close();
?>
