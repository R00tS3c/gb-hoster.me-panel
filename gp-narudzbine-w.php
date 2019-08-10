<?php
include('./fnc/ostalo.php');

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $bill_id = htmlspecialchars(mysql_real_escape_string(addslashes($_GET['id'])));

    if ($bill_id == "") {
        $_SESSION['error'] = "Ova narudzba ne postoji ili nemas ovlascenje za istu.";
        header("Location: gp-narudzbine.php");
        die();
    }

    $billing_info = mysql_fetch_array(mysql_query("SELECT * FROM `billing` WHERE `id` = '$bill_id' AND `klijentid` = '$_SESSION[userid]'"));
    if (!$billing_info) {
        $_SESSION['error'] = "Ova narudzba ne postoji ili nemas ovlascenje za istu.";
        header("Location: gp-narudzbine.php");
        die();
    }

    if ($billing_info['srw_name'] == "") {
        $billing_info['srw_name'] = "Narudzba!";
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
                        <img src="/img/icon/gp/gp-server.png" alt="" style="position:absolute;margin-left:20px;">
                        <h2 style="margin-left: 60px;color: #ba0000;font-size: 30px;">Narudzba</h2>
                        <h3 style="font-size: 12px;margin-left: 60px;margin-top: -5px;">Ovde mozete pogledati vase narudzbe i ukoliko su odobrene, mozete ih aktivirati!</h3>
                        <div class="space" style="margin-top: 40px;"></div>

                        <div class="supportAkcija right" style="margin-top: -70px;margin-right: 25px;">
                            <li style="list-style-type: none;">
                                <a href="/naruci.php?naruci" class="btn"><i class="fa fa-refresh"></i> Nova narudzba</a>
                            </li>
                        </div>
                        <div id="tiket_body">   
                            <div class="tiket_info">
                               
                                    <div class="tiket_info_ab" style="width: 35%; height: auto; float: right; background: rgba(0,0,0,0.5); border: 1px solid #333; margin: 5px 10px; padding: 20px;">
								    
                                    <div class="tiket-button">
                                        <div class="tiket-button_a">
                                            <?php  
                                            $pay_status = $billing_info['BillingStatus'];

                                            if ($pay_status == "0") { ?> 
                                                <button class="btn-large btn-info btn-support-ask">
                                                    Status: Na čekanju!
                                                </button>
                                            <?php } else if ($pay_status == "1") { ?>
                                                <button class="btn-large btn-warning btn-support-ask">
                                                    Status: Uplaćeno!
                                                </button>
                                            <?php } else if ($pay_status == "2") { ?>
                                                <button class="btn-large btn-success btn-support-ask">
                                                    Status: Uplaćeno!
                                                </button>
                                            <?php } ?>
                                        </div>

                                    </div>
									
									<div class="tiket-button">
                                        <div class="tiket-button_a">

                          <style> 
                           .tiket-button_a button {
                               width: 100%;
                               background: none;
                               border: 1px solid #fff;
                               padding: 10px;
                               margin-left: 10px;
                               color: #fff;
                               margin: 5px 0;
                               position: relative;
                               border-radius: 5px;
                             }
                          </style>

                                           <?php  
                                            $billing_status = $billing_info['BillingStatus'];
											$billing_game = $billing_info['game'];
											
                                            if ($billing_status == "0") { ?> 
                                                <form action="process.php?task=billing_srv_uplata" method="POST">
                                                    <input hidden type="text" name="billing_id" value="<?php echo $billing_info['id']; ?>">
                                                    <button class="btn-large btn-success btn-support-ask">
                                                        <span style='color: #54ff00;'><i>UPLATI SERVER</i></span>
                                                    </button>
                                                </form>
												<form action="process.php?task=billing_del" method="POST">
                                                    <input hidden type="text" name="billing_id" value="<?php echo $billing_info['id']; ?>">
                                                    <button class="btn-large btn-success btn-support-ask">
                                                        <span style='color: red;'><i>OBRISI NARUDZBINU</i></span>
                                                    </button>
                                                </form>
                                            <?php } else if ($billing_status == "1") { ?>
												<form action="naruci_process.php?task=billing_srv_install" method="POST">
													<input hidden type="text" name="billing_id" value="<?php echo $billing_info['id']; ?>">
													<button class="btn-large btn-success btn-support-ask">
														<span style='color: #54ff00;'><i>INSTALIRAJ SERVER</i></span>
													</button>
                                                </form>
												<form action="process.php?task=billing_srv_refund" method="POST">
                                                    <input hidden type="text" name="billing_id" value="<?php echo $billing_info['id']; ?>">
                                                    <button class="btn btn-large btn-success btn-support-ask">
                                                        <span style='color: blue;'><i>POVRATI NOVAC</i></span>
                                                    </button>
                                                </form>
												<?php } else if ($billing_status == "2") { ?>
												<form action="billing_tiket.php" method="POST">
													<input hidden type="text" name="billing_id" value="<?php echo $billing_info['id']; ?>">
													<button class="btn-large btn-success btn-support-ask">
														<span style='color: #54ff00;'><i>POGLEDAJ BILLING TIKET</i></span>
													</button>
                                                </form>
                                            <?php } ?>
                                        </div>

                                    </div>
									
                                </div>

                                <div class="tiket_info_b" style="width: 60%; height: auto;">   
                                <div class="tiket-header" style="width: 92%; height: 50px; padding: 0px 15px; background: rgba(0,0,0,0.5); border: 1px solid #333;margin: 5px 9px;">
                                        <h3>
                                            <span class="fa fa-info-circle" style="color:#ba0000;font-size:19px;"></span>
                                            <?php echo ispravi_text($billing_info['srw_name']); ?>
                                            <span style="float:right;margin-right:10px;">
                                                <?php echo ispravi_text($billing_info['vreme'].', '.$billing_info['datum']); ?>
                                            </span>
                                        </h3>
                                    </div>
                                    
                              <div class="tiket-content" style="width: 97%; background: rgba(0,0,0,0.5); padding: 0px; color: #fff; border: 1px solid #333; 
                              margin: 7px 9px; margin-bottom: 20px;">
                                        <div class="tiket_info_home">
                                            <div class="tiket_info_home_a">
                                                <li style="list-style-type: none;"><img src="<?php echo userAvatar($_SESSION['userid']); ?>" alt=""></li>
                                                <li style="list-style-type: none;"><p><strong><?php echo userIme($_SESSION['userid']); ?></strong></p></li>
                                            </div>
                                            
                                            <?php if ($pay_status == "0") { ?>
                                                <div class="tiket_info_home_p">
                                                    <p>
                                                        <strong><?php echo $billing_info['description']; ?></strong>
                                                    </p>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="bill_pay_">
                                                    <label for="billing_pay">UPLATI PREKO : </label>
                                                    <li style="list-style-type: none;">
                                                        <a href="/gp-addpayments.php?tip=paypal">
                                                            <img src="./img/icon/pp_i.png" style="width:25px;height:25px;"> PayPal
                                                        </a>
                                                    </li>
                                                    <li style="list-style-type: none;"><a href="/gp-addpayments.php?tip=banka"><span class="fa fa-bank"></span> Banka/Posta</a></li>                                                                                                
                                                <li style="list-style-type: none;"><a href="/gp-addpayments.php?tip=sms"><span class="fa fa-commenting-o"></span> SMS</a></li>
                                                </div>
                                            <?php } elseif ($pay_status == "1") { ?>
                                                <div class="tiket_info_home_p">
                                                    <p>
                                                        <strong><?php echo $billing_info['description']; ?> &euro;</strong>
                                                    </p>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="bill_pay_">
                                                    <label for="billing_dokaz">DOKAZ : </label>
                                                    <li><a href="">SLIKA</a></li>
                                                    <li><a href="">NESTO DRUGO</a></li>

                                                    <hr>

                                                    <label for="billing_pay">UPLATI PREKO : </label>
                                                    <li style="list-style-type: none;">
                                                        <a href="/gp-addpayments.php?tip=paypal">
                                                            <img src="./img/icon/pp_i.png" style="width:25px;height:25px;"> PayPal
                                                        </a>
                                                    </li>
                                                    <li style="list-style-type: none;"><a href="/gp-addpayments.php?tip=banka"><span class="fa fa-bank"></span> Banka/Posta</a></li>                                                                                                
                                                <li style="list-style-type: none;"><a href="/gp-addpayments.php?tip=sms"><span class="fa fa-commenting-o"></span> SMS</a></li>
                                                </div>
                                            <?php } elseif ($pay_status == "2") { ?>
                                                <div class="tiket_info_home_p">
                                                    <p>
                                                        <strong><?php echo $billing_info['description']; ?> &euro;</strong>
                                                    </p>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="bill_pay_">
                                                    <label for="billing_pay">
                                                        <h2 style="margin: 5px 20px; color: #54ff00;">Narudzba je aktivna!</h2>
                                                    </label>
                                                </div>
                                            <?php } else { ?>
                                                <div class="tiket_info_home_p">
                                                    <p>
                                                        <strong><?php echo $billing_info['description']; ?> &euro;</strong>
                                                    </p>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="bill_pay_">
                                                    <label for="billing_pay">UPLATI PREKO : </label>
                                                    <li style="list-style-type: none;">
                                                        <a href="/gp-addpayments.php?tip=paypal">
                                                            <img src="./img/icon/pp_i.png" style="width:25px;height:25px;"> PayPal
                                                        </a>
                                                    </li>
                                                    <li style="list-style-type: none;"><a href="/gp-addpayments.php?tip=banka"><span class="fa fa-bank"></span> Banka/Posta</a></li>                                                                                                
                                                <li style="list-style-type: none;"><a href="/gp-addpayments.php?tip=sms"><span class="fa fa-commenting-o"></span> SMS</a></li>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

    <!-- FOOTER -->
    
    <?php include('style/footer.php'); ?>   

    <?php include('style/java.php'); ?>

</body>
</html>