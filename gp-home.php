<?php
header('Content-Type: text/html; charset=utf-8');

include 'connect_db.php';

if (is_login() == false) {
	$_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $proveri_servere = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '$_SESSION[userid]'"));
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
    <div id="ServerBox" style="border: 1px solid #ba0000; background: #000000b5;">
<?php include("style/gpmenu.php"); ?>
        <div id="server_info_infor">    
            <div id="server_info_infor">
                <div id="server_info_infor2">
                    <div class="space" style="margin-top: 20px;"></div>
                    <div class="home">
                        
                        <div class="home-right">
                            <img src="/img/icon/gp/gp-user.png" alt="">
                            <h2 style="margin-left: 6%;margin-top: -5%;">Dobrodošao u Gpanel</h2>
                            <?php  
                                $nadji = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$_SESSION[userid]'"));
                            ?>
                            <h3 style="margin-top: -1%;margin-left: 6%;"><?php echo $nadji['username']; ?></h3>
                            
                            <h2 style="margin: -2% 41%; font-weight: 100;"><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;&nbsp;Novosti!</h2>

                            <div class="gp-logo">
                                <a href="/home"><img style="margin-top: 5%;margin-left: 7%;width: 20%;" src="//i.imgur.com/VdznoMT.png" alt=""></a>
                            </div>
                        </div>


                        <div class="gp-home" style="margin-left: 40%;margin-top: -14%;">
                            <div class="div-gp">
                                <?php  
                                    $gp_obv = mysql_query("SELECT * FROM `obavestenja` WHERE `vrsta` = '1' ORDER BY `id` DESC LIMIT 3");

                                    while($row = mysql_fetch_array($gp_obv)) { 

                                        $naslov = htmlspecialchars(mysql_real_escape_string(addslashes($row['naslov'])));
                                        $poruka = $row['poruka'];
                                        $datum = htmlspecialchars(mysql_real_escape_string(addslashes($row['datum'])));

                                    ?>
                                    <div class="gp-obv-ispis" style="max-width: 550px;border:1px solid;border-color: #ba0000;margin-top: 5px;">
                                        <p class="gp-obv-naslov">
                                            <i class="glyphicon glyphicon-chevron-right" style="font-size: 10px;"></i> 
                                            <?php echo htmlspecialchars(mysql_real_escape_string(addslashes($naslov))); ?>
                                        </p>
                                        <p class="gp-obv-text"><?php echo $poruka; ?></p>
                                    </div>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="space" style="margin-bottom: 40px;"></div>
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