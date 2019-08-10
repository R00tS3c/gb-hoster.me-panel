<?php
include('./fnc/ostalo.php');

if (is_login() == false) {
	$_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
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
						<h2 style="margin-left: 6%;margin-top: -5%;">Dodavanje uplate</h2>
						<h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Ovde mozete dodati uplatu!</h3>
						<div class="space" style="margin-top: 30px;"></div>
						
						<?php if((isset($_GET['tip']) && $_GET['tip'] == "banka")) { ?>
							<div class="supportAkcija right" data-toggle="modal" data-target="#modalUplatnice" style="margin-right: 2%;">
									<a href="javascript:{}" class="btn"><i class="fa fa-refresh"></i> Pogledaj Uplatnice</a>
							</div>
							
							<form action="/process.php?task=billing_add_banka" style="margin-left:3%;" method="POST" autocomplete="off">
								
								<div class="add_server_by_client">
									<label for="klijent" style="font-size:15px;">Ime i prezime: </label>
									<br>
									<input name="ime" type="text" placeholder="Ime i prezime" required="">
								</div><br>
								
								<div class="add_server_by_client">
									<label for="klijent" style="font-size:15px;">Iznos koji ste uplatili: </label>
									<br>
									<input name="novac" type="text" placeholder="Iznos upisite u evrima" required="">
								</div><br>
								
								<div class="add_server_by_client">
									<label for="klijent" style="font-size:15px;">Link uplatnice: </label>
									<br>
									<input name="link" type="text" placeholder="https://prnt.sc/..." required="">
								</div><br>
								
								<div class="add_server_by_client">
									<label for="drzava" style="font-size:15px;">Drzava: </label>
									<br>
									<select name="drzava" id="drzava" style="width: 175px;">
										<option value="" disabled selected="selected">Izaberi</option>
										<option value="SRB">Srbija</option>
										<option value="BiH">Bosna i Hercegovina</option>
										<!--<option disabled value="HRV">Hrvatska</option>-->
										<!--<option disabled value="CG">Crna Gora</option>-->
										<!--<option disabled value="MK">Makedonija</option>-->
										<!--<option disabled value="Other">Ostale drzave</option>-->
									</select>
									</div>
									<div class="space" style="margin-top: 10px;"></div>
								
								<button class="right add_server_by_client_btn" type="submit" style="margin-right: 2%;"> 
									<i class="fa fa-cart-plus"></i> Dodaj uplatu
								</button>					
							</form></br></br></br>

								
						<?php } else { ?>
						
							<div class="tiket-content">
								<div class="tiket_info_home">


									<div id="panelnav" style="width: 605px;align-content: center;margin-left: 30%;">
										<ul class="ServerInfoPrecice">
										<li>
											<a href="paypal.php">
												 PayPal
											</a>
										</li>
										<li><a href="gp-addpayments.php?tip=banka"><span class="fa fa-bank"></span> Banka/Posta</a></li>                                                                                                
										<li><a href="sms.php"><span class="fa fa-commenting-o"></span> SMS</a></li>
									</ul>
									</div></br>

								</div>
							</div>
						
						<?php } ?>

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
	<!-- Modal -->
		<div class="modal fade" id="modalUplatnice" role="dialog" style="width:800px;">
			<div class="modal-dialog">

					<!-- Modal Body -->
						<p class="statusMsg"></p>
						<form role="form">
							<div class="form-group">
								<label for="inputName" style="font-size:14px;">Drzava</label>
								<br>
								<select id="drzava-uplatnica" name="drzava" rel="drzava">
									<option value="0" selected="selected" >Izaberi drzavu</option>
									<option value="1" >Srbija</option>
									<option value="3" >Bosna i Hercegovina</option>
									<!--<option value="2" >Crna gora</option>-->
									<!--<option value="4" >Hrvatska</option>-->
									<!--<option value="5" >Makedonija</option>-->
									<!--<option value="6" >Ostale zemlje</option>-->
								</select>
							</div>
							<br>
							<div class="form-group">
								<div id="srbija" style="display: none;">
									<a href="uplatnice/srbija.jpg" target="_blank">
										<img class="img-responsive" src="uplatnice/srbija.jpg" />
									</a>
								</div>
								<!--<div id="crnagora" style="display: none;">
									<a href="uplatnica.php?drzava=cg" target="_blank">
										<img class="img-responsive" src="uplatnica.php?drzava=cg" class="img-fluid" />
									</a>
								</div>-->
								<div id="bosna" style="display: none;">
									<a href="uplatnice/bosna.jpg" target="_blank">
										<img class="img-responsive" src="uplatnice/bosna.jpg" />
									</a>
								</div>
								<!--<div id="hrvatska" style="display: none;">
									<a href="uplatnica.php?drzava=hr" target="_blank">
										<img class="img-responsive" src="uplatnica.php?drzava=hr" />
									</a>
								</div>-->
								<!--<div id="makedonija" style="display: none;">
									<a href="uplatnica.php?drzava=mk" target="_blank">
										<img class="img-responsive" src="uplatnica.php?drzava=mk" />
									</a>
								</div>-->
								<!--<div id="ostalezemlje" style="display: none;">
									<a href="uplatnica.php?drzava=other" target="_blank">
										<img class="img-responsive" src="uplatnica.php?drzava=other" />
									</a>
								</div>-->
							</div>
						</form>

				<!-- Modal Footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Zatvori prozor sa uplatnicama</button>
				</div>
				<br>
		</div>
	</div>
    <?php } ?>
    <?php include('style/footer.php'); ?>   

    <?php include('style/java.php'); ?>
</body>
</html>