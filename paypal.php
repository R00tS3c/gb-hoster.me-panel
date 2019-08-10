<?php
include('./fnc/ostalo.php');

if (is_login() == false) {
	$_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
}

if(isset($_GET['tip'])) {
	if($_GET['tip'] != "banka") {
		header( "Location: redirect.php?url=https://keepme.live/x/u/gb-hoster" );
		die();
	}
}

$klijent = mysql_fetch_assoc(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$_SESSION['userid']."'"));

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
					<div class="space" style="margin-top: 80px;"></div>
					<div class="gp-home">
						<img src="/img/icon/gp/gp-server.png" alt="" style="position:absolute;margin-left:20px;">
						<h2 style="margin-left: 6%;margin-top: -5%;">Dodavanje PayPal uplate</h2>
						<h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Ovde mozete dodati uplatu putem PayPala!</h3>
						<div class="space" style="margin-top: 30px;"></div>

							<form class="paypal" action="/PayPal/payments.php" method="post" id="paypal_form" style="margin-left:3%;">
								<input type="hidden" name="cmd" value="_xclick" />
								<input type="hidden" name="no_note" value="1" />
								<input type="hidden" name="lc" value="EUR" />
								<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />
								<input type="hidden" name="item_number" value="123456" / >

								<div class="add_server_by_client">
									<label for="klijent" style="font-size:15px;">Ime: </label>
									<br>
									<input name="first_name" type="text" placeholder="Ime" required="">
								</div><br>

								<div class="add_server_by_client">
									<label for="klijent" style="font-size:15px;">Prezime: </label>
									<br>
									<input name="last_name" type="text" placeholder="Prezime" required="">
								</div><br>

								<div class="add_server_by_client">
									<label for="klijent" style="font-size:15px;">Mail: </label>
									<br>
									<input name="payer_email" type="text" placeholder="Mail" required="">
								</div><br>

								<div class="add_server_by_client">
									<label for="klijent" style="font-size:15px;">Iznos koji želite uplatiti: </label>
									<br>
									<input name="amount" type="text" placeholder="Iznos upisite u evrima" required="">
								</div><br>

								<button class="right add_server_by_client_btn" type="submit" style="margin-right: 2%;"> 
									<i class="fa fa-cart-plus"></i> Dodaj uplatu
								</button>

							</form></br></br></br>
					</div>
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
    <?php include('style/footer.php'); ?>   

    <?php include('style/java.php'); ?>
</body>
</html>