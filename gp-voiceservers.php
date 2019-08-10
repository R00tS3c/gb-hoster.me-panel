<?php
include 'connect_db.php';

if (is_login() == false) {
	$_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $proveri_servere = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '$_SESSION[userid]' AND `igra` = '6'"));
    if (!$proveri_servere) {
        $_SESSION['info'] = "Nemate kod nas servera.";
        header("Location: /home");
        die();
    }
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<?php include ("assets/php/head.php"); ?>
<?php include('style/err_script.php'); ?>
<body>
		    <div id="content">
        <div id="TOP">
            <div id="header">
                <a id="logo" href="/"></a>
<?php include ("style/login_provera.php"); ?>
	            </div>
<?php include ("style/navigacija.php"); ?>
        </div>
<div id="wraper" style="/*background: rgba(0,0,0,0.7);*/ box-sizing: border-box; max-width: 1002px; color: #fff !important; /*margin: 0px 0;*/">
    <div id="ServerBox" style="background: #000000ad; border: 1px solid #ba0000;">
<?php include("style/gpmenu.php"); ?>

        <div id="server_info_infor">    
            <div id="server_info_infor">
                <div id="server_info_infor2">
                    <div class="space" style="margin-top: 20px;"></div>
                    <div class="gp-home">
                        <img src="/img/icon/gp/gp-server.png" alt="" style="margin-left:20px;">
                        <h2 style="margin-left: 6%;margin-top: -5%;">Serveri</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Lista svih vasih servera</h3>
                        <div class="space" style="margin-top: 60px;"></div>
                        
                        <div id="serveri">
                            <center><table class="darkTable">
                                <tbody>
                                    <tr style="background: #ba000052;">
                                        <th>Ime servera</th>
                                        <th>Vazi do</th>
                                        <th>Cena</th>
                                        <th>IP adresa</th>
                                        <th>Slotovi</th>
                                        <th>Status</th>
                                    </tr>
                                    <?php  
                                        $gp_obv = mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '$_SESSION[userid]' AND `igra` = '6'");

                                        while($row = mysql_fetch_array($gp_obv)) { 

                                            $srw_id = htmlspecialchars(mysql_real_escape_string(addslashes($row['id'])));
                                            $naziv_servera = htmlspecialchars(mysql_real_escape_string(addslashes($row['name'])));
                                            $istice = htmlspecialchars(mysql_real_escape_string(addslashes($row['istice'])));
                                            $box_id = htmlspecialchars(mysql_real_escape_string(addslashes($row['box_id'])));
                                            $port = htmlspecialchars(mysql_real_escape_string(addslashes($row['port'])));
                                            $slotovi = htmlspecialchars(mysql_real_escape_string(addslashes($row['slotovi'])));
                                            $cena = htmlspecialchars(mysql_real_escape_string(addslashes($row['cena'])));
                                            $status = htmlspecialchars(mysql_real_escape_string(addslashes($row['status'])));
                                            $igra = htmlspecialchars(mysql_real_escape_string(addslashes($row['igra'])));

                                            $serverStatus = $status;  
                                            if ($serverStatus == "Aktivan") {
                                                $serverStatus = "<span style='color: green;'> Aktivan </span>";
                                            } else if($serverStatus == "Suspendovan") {
                                                $serverStatus = "<span style='color: red;'> Suspendovan </span>";
                                            } else {
                                                $serverStatus = "<span style='color: red;'> Neaktivan </span>";
                                            }
											
											$igra = "img/icon/gp/game/ts3.ico";
											
											$server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$box_id'"));
                                        ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo $igra; ?>" style="width: 15px;">
                                                <a href="gp-voiceinfo.php?id=<?php echo $srw_id; ?>"><?php echo $naziv_servera ?></a>
                                            </td>
                                            <td><?php echo $istice; ?></td>
                                            <td><?php echo $cena; ?> &euro;</td>
                                            <td class="ip"><?php echo $server_ip['ip'].':'.$port; ?></td>
                                            <td><?php echo $slotovi; ?></td>
                                            <td><div class="aktivan"><?php echo $serverStatus; ?></div></td>
                                        </tr>
                                    <?php } ?>                               
                                </tbody>
                            </table>
						</center>
                        </div>
                    </div>
                    <div class="space" style="margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>