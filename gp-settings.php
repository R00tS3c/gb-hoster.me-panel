<?php
include 'connect_db.php';

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    /*$proveri_servere = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '$_SESSION[userid]'"));
    if (!$proveri_servere) {
        $_SESSION['info'] = "Nemate kod nas servera.";
        header("Location: /home");
        die();
    }*/

    $proveri_usera = mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$_SESSION[userid]'");
    if (mysql_num_rows($proveri_usera) == 0) {
        $_SESSION['info'] = "Ovaj korisnik ne postoji...";
        header("Location: /home");
        die();
    }

    $uzmi_usera = mysql_fetch_array($proveri_usera);
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
                    <div class="gp-home" style="margin-left: 20px;">
                        <h2>Licni podaci</h2>
                        <h3 style="font-size: 12px;">Ovde mozete promeniti licne podatke!</h3>                       
                        
                        <div class="podForm" style="">
                            <?php if (is_pin() == false) { ?>
                                <strong style="color: #fff;">
                                    Kako bi pristupili opciji za editovanje vaših informacija potrebno je da ispravno unesete vaš pin kod!
                                </strong> 
                                <br /> <br />
                                <span style="padding: 5px 10px;color: #fff;cursor: pointer;font-size: 12px;font-weight: bold;border: 1px solid #bbb;" data-toggle="modal" data-target="#pin-auth">OTKLJUČAJ
                                </span>
                            <?php } else { ?>
                                <form action="process.php?task=edit_profile" method="POST" autocomplete="off">
                                    <label for="ime">IME </label>
                                    <input type="text" name="ime" value="<?php echo $uzmi_usera['ime']; ?>" style="margin-left: 81px;">
                                    <br />

                                    <label for="prezime">PREZIME </label>
                                    <input type="text" name="prezime" value="<?php echo $uzmi_usera['prezime']; ?>" style="margin-left: 81px;;"> <br />

                                    <label for="email">EMAIL </label>
                                    <input disabled name="email" value="<?php echo $uzmi_usera['email']; ?>" style="margin-left: 81px;">
                                    <?php if (is_pin() == true) { ?>
                                        <!-- <span style="margin-left:10px;color:#bbb;cursor:pointer;" data-toggle="modal" data-target="#email-auth"> Zahtijev za promenu email adrese</span> -->
                                    <?php } ?>
                                    <br />

                                    <label for="password">PASSWORD </label>
                                    <input type="password" name="password" placeholder="Ako ne zelite menjat ostavite prazno polje" style="margin-left: 81px;"> <br />
				    
                                    <label for="token">TOKEN </label>
                                    <?php if (is_pin() == false) { ?>
                                        <input disabled name="token" style="margin-left: 81px;" value="SAKRIVEN -(ovo ne mozete da menjate)">
                                    <?php } else { ?>
                                        <input disabled name="token" style="margin-left: 81px;" value="<?php echo $uzmi_usera['token']; ?>">
                                        <span style="margin-left:10px;color:#bbb;cursor:pointer;" data-toggle="modal" data-target="#token-auth"> Prikazi key token</span><br />
                                    <?php } ?>
				    <label for="avatar">AVATAR LINK </label>
                                    <input type="text" name="avatar" value="<?php echo $uzmi_usera['avatar']; ?>" style="margin-left: 81px;"> <br />
				    </br>
</br>
                                    <button style="padding: 5px 10px;background: #ba0000;color: #fff;cursor: pointer;font-size: 12px;font-weight: bold;border: 1px solid #fff;">SACUVAJ</button>
                                </form>
                            <?php } ?>
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
                                        <p>Vas account je zasticen sa PIN kodom !</p>
                                        <p>Da biste pristupili ovoj opciji, potrebno je da ga unesete u box ispod.</p>
                                        <label>PIN KOD:</label>
                                        <input type="password" name="pin" value="" maxlength="5" class="short">
                                        <button> <span class="fa fa-check-square-o"></span> Otkljucaj</button>
                                        <button type="button" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                            </fieldset>
                        </form>
                    </div>        
                </div>  
            </div>
        </div>
        <!-- KRAJ - PIN (POPUP) -->

        <?php if (is_pin() == true) { ?>
            
            <!-- TOKEN (POPUP)-->
            <div class="modal fade" id="token-auth" role="dialog">
                <div class="modal-dialog">
                    <div id="popUP"> 
                        <div class="popUP">
                            <form action="process.php?task=client_new_token" method="POST" class="ui-modal-form" id="modal-token-auth">
                                <?php
                                    $new_token = randomSifra(30).'_'.$_SESSION['userid'];
                                    $_SESSION['new_token'] = $new_token;
                                ?>
                                <fieldset>
                                    <h2>PHP API Token</h2>
                                    <ul>
                                        <li>
                                            <p>
                                                Token sluzi za dodeljivanje privilegija vasih servera nekoj eksternoj aplikaciji. <br />
                                                Ako ne znate cemu ovo sluzi, onda vam verovatno nece ni trebati :) <br />
                                                Korisne PHP API TOKEN SKRIPTE: <a href="/api.php?token">KLIK!</a> <br />
                                            </p>
                                        </li>

                                        <li>
                                            <label for="token">Trenutni <br /> TOKEN</label>
                                            <input hidden type="text" name="stari_token" value="<?php echo $uzmi_usera['token']; ?>">
                                            <input disabled type="text" value="<?php echo $uzmi_usera['token']; ?>" style="width: 85%;">
                                        </li>

                                        <br />

                                        <p>
                                            Ovde mozete generisati novi PHP API token! <br />
                                            Ako ga promenite, sve aplikacije koje ga trenutno koriste gube pristup i moracete im ponovo upisati novi token! <br />
                                            Ukolio ocete da ostavite stari kliknite na 'dugme' "ODUSTANI" .
                                        </p>
                                        
                                        <li>
                                            <label for="token">Novi <br /> TOKEN</label>
                                            <input hidden type="text" name="new_token" value="<?php echo $new_token; ?>">
                                            <input disabled type="text" value="<?php echo $new_token; ?>" style="width: 85%;">
                                        </li>

                                        <li style="text-align:center;">
                                            <button> <span class="fa fa-check-square-o"></span> SACUVAJ</button>
                                            <button type="button" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                                        </li>
                                    </ul>
                                </fieldset>
                            </form>
                        </div>        
                    </div>  
                </div>
            </div>
            <!-- KRAJ - TOKEN (POPUP) -->

        <?php } ?>

    <?php } ?>

    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>