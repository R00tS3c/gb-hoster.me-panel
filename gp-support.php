<?php
include 'connect_db.php';

if (is_login() == false) {
	$_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $proveri_servere = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '$_SESSION[userid]'"));
	$proveri_billing = mysql_num_rows(mysql_query("SELECT * FROM `billing` WHERE `klijentid` = '$_SESSION[userid]' AND `game` = 'Team-Speak 3'"));
    if (!$proveri_servere) {
		if (!$proveri_billing) {
			$_SESSION['info'] = "Nemate kod nas servera.";
			header("Location: /home");
			die();
		}
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
                        <img src="/img/icon/gp/gp-supp.png" alt="" style="position:absolute; margin: 0% 2%;">
                        <h2 style="margin: 0% 10%;">Podrška</h2>
                        <h3 style="font-size: 12px; margin: 0% 10%;">
                            Dobrodosli u GB-Hoster.me Support panel
                            <br/>Ovde možete otvarati nove tikete ukoliko vam treba pomoć ili podrška oko servera.
                        </h3>
                        <div class="space clear" style="margin-top: 60px;"></div>

                        <div class="supportAkcija right">
                            <li style="list-style-type: none; margin: 0% -120%;">
                                <a href="gp-newtiket.php" class="btn">Novi tiket</a>
                            </li>
                            <li style="    list-style-type: none; margin: -21% -10%; margin-bottom: 35%;">
                                <a href="gp-support.php" class="btn">Arhiva</a>
                            </li>
                        </div>
                        
                        <div id="serveri">
                            <center><table class="darkTable">
                                <tbody style="">
                                    <tr style="background: #ba00006e;">
                                        <th>ID Tiketa</th>
                                        <th>Ime tiketa</th>
                                        <th>Datum</th>
                                        <th>Server</th>
                                        <th>Broj poruka</th>
                                        <th>Status</th>
                                    </tr>
                                    <?php  
                                        $gp_supp = mysql_query("SELECT * FROM `tiketi` WHERE `user_id` = '$_SESSION[userid]'");

                                        $broj_poruka = mysql_num_rows($gp_supp);

                                        while($row = mysql_fetch_array($gp_supp)) { 

                                            $srw_id = htmlspecialchars(mysql_real_escape_string(addslashes($row['server_id'])));
                                            $status = htmlspecialchars(mysql_real_escape_string(addslashes($row['status'])));
                                            $datum = htmlspecialchars(mysql_real_escape_string(addslashes($row['datum'])));
                                            $naslov = htmlspecialchars(mysql_real_escape_string(addslashes($row['naslov'])));

                                            if($status == 1){
                                                $status = 'Otvoren';
                                            }elseif($status == 4){
                                                $status = 'Pročitan';
                                            }elseif($status == 3){
                                                $status = 'Zaključan';
                                            }elseif($status == 2){
                                                $status = 'Odgovoren';
                                            }

                                            $ss_ip = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$srw_id'"));
                                            $server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$ss_ip[box_id]'"));
                                    ?>       
                                        <tr>
                                            <td><a href="gp-tiket.php?id=<?php echo $row['id']; ?>">#<?php echo $row['id']; ?></a></td>
                                            <td><a href="gp-tiket.php?id=<?php echo $row['id']; ?>"><?php echo $naslov; ?></a></td>
                                            <td><?php echo vreme($datum); ?></td>
                                            <td class="ip">
                                                <a href="gp-info.php?id=<?php echo $srw_id; ?>">
                                                    <?php echo $server_ip['ip'].':'.$ss_ip['port']; ?>    
                                                </a>
                                            </td>
                                            <td><?php echo $broj_poruka; ?></td>
                                            <td><?php echo $status; ?></td>
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

	<?php 
	include('style/footer.php');
	include('style/java.php');
	include('style/pin_provera.php');
?>

    


  

</body>
</html>