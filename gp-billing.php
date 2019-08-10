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
                        <h2 style="margin-left: 6%;margin-top: -5%;">Uplate</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Lista svih vasih uplata</h3>
                        <div class="space" style="margin-top: 60px;"></div>
							<div class="supportAkcija right" style="margin-top: -5%;margin-right: 5%;">
									<a href="/gp-addpayments.php" class="lock-btn btn">
										<i class="fa fa-shopping-cart"></i> Dodajte Uplatu
									</a>
							</div>
                        <div id="serveri">
                            <table class="darkTable" style="margin-left:2%;">
                                <tbody>
                                    <tr style="background: #ba000052;">
                                        <th>ID</center></th>
                                        <th>Iznos Novca</th>
                                        <th>Link Uplatnice</th>
                                        <th>Vreme Uplate</th>
                                        <th>Status Uplate</th>
                                    </tr>
                                    <?php  
										$uplate = mysql_query("SELECT * FROM `uplate` WHERE `klijentid` = '$_SESSION[userid]' ORDER by id DESC");
										
										while($row = mysql_fetch_array($uplate)) { 
											$id				=	htmlspecialchars(mysql_real_escape_string(addslashes($row['id'])));
											$iznos			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['novac'])));
											$link			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['link'])));
											$vreme			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['vreme'])));
											$status			=	htmlspecialchars(mysql_real_escape_string(addslashes($row['status'])));
											
											if($status == "1") {
												$status = "<span style='color: red;'>Odbijeno!</span>";
											} else if($status == "2") {
												$status = "<span style='color: #54ff00;'>Prihvaćeno</span>";
											} else {
												$status = "<span style='color: #ffd800;'>Na čekanju!</span>";
											}
                                        ?>       
										<tr>
                                            <td><center>#<?php echo $id; ?></center></td>
                                            <td><center><?php echo $iznos; ?> &euro;</center></td>
											<td><center><?php if($link != "SMS UPLATA") { ?><a href="<?php echo $link; ?>"><?php echo $link; ?></a><?php } else echo $link; ?></center></td>
                                            <td><center><?php echo $vreme; ?></center></td>
                                            <td><center><div class="aktivan"><?php echo $status; ?></div></center></td>
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
        </div>
        <!-- KRAJ - PIN (POPUP) -->

    <?php } ?>

    <!-- FOOTER -->
    	<?php 
	include('style/footer.php');
	include('style/java.php');
	?>
    </div>

</body>
</html>