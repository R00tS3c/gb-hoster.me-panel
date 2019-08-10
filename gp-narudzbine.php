<?php
include 'connect_db.php';

if (is_login() == false) {
	$_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
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
                        <h2 style="margin-left: 6%;margin-top: -5%;">Narudzbine</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Lista svih vasih narudzba</h3>
                        <div class="space" style="margin-top: 60px;"></div>
                        
                        <div id="serveri" style="margin-left:2%;">
                            <table class="darkTable">
                                <tbody>
                                    <tr style="background: #ba000052;">
                                        <th>ID</th>
                                        <th>Ime servera</th>
                                        <th>Igra</th>
                                        <th>Cena</th>
                                        <th>Lokacija</th>
                                        <!--<th>Vrsta Placanja</th>-->
                                        <th>Datum Narudzbe</th>
                                        <th>Status</th>
                                    </tr>
                                    <?php  
                                        $gp_obv = mysql_query("SELECT * FROM `billing` WHERE `klijentid` = '$_SESSION[userid]' ORDER by id DESC");

                                        while($row = mysql_fetch_array($gp_obv)) { 
                                            $b_id			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['id'])));
                                            $srw_name		=	htmlspecialchars(mysql_real_escape_string(addslashes($row['srw_name'])));
                                            $iznos			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['iznos'])));
                                            $datum			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['datum'])));
                                            $vreme			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['vreme'])));
                                            //$paytype		=	htmlspecialchars(mysql_real_escape_string(addslashes($row['paytype'])));
                                            $status			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['status'])));
                                            $lokacija		=	htmlspecialchars(mysql_real_escape_string(addslashes($row['lokacija'])));
                                            $game			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['game'])));

                                            if ($status == "0") {
                                                $status = "<span style='color: red;'>Na čekanju!</span>";
                                            } elseif ($status == "1") {
                                                $status = "<span style='color: #54ff00;'>Uplaćeno!</span>";
                                            } elseif ($status == "2") {
                                                $status = "<span style='color: #54ff00;'>Uplaćeno!</span>";
                                            }

                                            if ($srw_name == "") {
                                                $srw_name = "Narudzba!";
                                            }
											
											if ($lokacija == "4") {
												$lokacija = "<img src='/img/icon/country/RS.png'>";
											} else {
												$lokacija = "<img src='/img/icon/country/DE.png'>";
											}
											
											if ($game == "Counter-Strike 1.6") {
												$game = "<img src='/img/icon/game/cs.png' alt='Counter-Strike 1.6' style='width:16px;height:16px;'> Counter-Strike 1.6";
											} else if ($game == "GTA San Andreas") {
												$game = "<img src='/img/icon/game/gta.png' alt='GTA San Andreas' style='width:16px;height:16px;'> GTA San Andreas";
											} else if ($game == "SinusBot") {
												$game = "<img src='/img/icon/game/sinusbot.png' alt='Sinus Bot' style='width:16px;height:16px;'> Sinus Bot";
											} else if ($game == "Team-Speak 3") {
												$game = "<img src='/img/icon/game/ts3.png' alt='Team Speak' style='width:16px;height:16px;'> Team Speak";
											} else if ($game == "FastDL") {
												$game = "<img src='/img/icon/game/fdl.png' alt='FastDL' style='width:16px;height:16px;'> FastDL";
											}
                                        ?>       
                                        <tr>
                                            <td><a href="gp-narudzbine-w.php?id=<?php echo $b_id; ?>">#<?php echo $b_id; ?></a></td>
                                            <td class="ip"><a href="gp-narudzbine-w.php?id=<?php echo $b_id; ?>"><?php echo $srw_name; ?></a></td>
                                            <td><?php echo $game; ?></td>
											<td><?php echo $iznos; ?> &euro;</td>
                                            <td><?php echo $lokacija; ?></td>
											<!--<td><?php echo $paytype; ?></td>-->
                                            <td><?php echo $vreme.', '.$datum; ?></td>
                                            <td><div class="aktivan"><?php echo $status; ?></div></td>
                                        </tr>
                                    <?php } ?>                               
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="space" style="margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>
    </div>

    <?php if (is_login() == true) { ?>
        <!-- PIN (POPUP)-->
        <div class="modal fade" id="pin-auth" role="dialog">
            <div class="modal-dialog">
                <div id="popUP"> 
                    <div class="popUP">
                        <?php
                            $get_pin_toket = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                            $_SESSION['pin_token'] = $get_pin_toket;
                        ?>
                        <form action="process.php?task=un_lock_pin" method="post" class="ui-modal-form" id="modal-pin-auth">
                            <input type="hidden" name="pin_token" value="<?php echo $get_pin_toket; ?>">
                            <fieldset>
                                <h2>PIN Code zastita</h2>
                                <ul>
                                    <li>
                                        <p>Vas account je zasticen sa PIN kodom !</p>
                                        <p>Da biste pristupili ovoj opciji, potrebno je da ga unesete u box ispod.</p>
                                    </li>
                                    <li>
                                        <label>PIN KOD:</label>
                                        <input type="password" name="pin" value="" maxlength="5" class="short">
                                    </li>
                                    <li style="text-align:center;">
                                        <button> <span class="fa fa-check-square-o"></span> Otkljucaj</button>
                                        <button type="button" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </div>        
                </div>  
            </div>

        <!-- KRAJ - PIN (POPUP) -->

    <?php } ?>

    <!-- FOOTER -->
    
    </div>
	<?php 
	include('style/footer.php');
	include('style/java.php');
	?>
        </div>
</body>
</html>