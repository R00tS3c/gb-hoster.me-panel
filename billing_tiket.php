<?php
include('./fnc/ostalo.php');

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
	if(isset($_POST['billing_id'])) {
		$tiket_id = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['billing_id'])));
	} else if(isset($_GET['id'])) {
		$tiket_id = htmlspecialchars(mysql_real_escape_string(addslashes($_GET['id'])));
	}

    if ($tiket_id == "") {
        $_SESSION['error'] = "Ovaj tiket ne postoji.";
        header("Location: gp-billing.php");
        die();
    }

    $tiket_info = mysql_fetch_array(mysql_query("SELECT * FROM `billing_tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'"));
    if (!$tiket_info) {
        $_SESSION['error'] = "Ovaj tiket ne postoji ili nemas ovlascenje za isti.";
        header("Location: gp-billing.php");
        die();
    }
}

function vreme($data) {
	$vreme = date("d.m.Y, H:i", $data);
	$time = explode(",", $vreme);
	
	$time[1] = "" . $time[1] . "";
	
	$datum = $time[0] . ',' . $time[1];
	
	return $datum;
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
                        <img src="/img/icon/gp/gp-supp.png" alt="" style="position:absolute;">
                        <h2>Billing Podrška</h2>
                        <h3 style="font-size: 12px;">
                            Dobrodosli u e-Game.me Billing Panel
                            <br/>Ovde možete vidjeti vase Billing tikete.
                        </h3>
                        <div class="space" style="margin-top: 60px;"></div>
						
                        <div id="tiket_body">   
                            <div class="tiket_info">
                                
                                <div class="gleda">    
                                    Pregledava: <span class="autor" style="color: red">
                                        <a style="color: red">Admin</a>
                                    </span>            
                                </div>

                                <div class="tiket_info_ab">
                                            
                                    <div class="tiket-button">
                                        <div class="tiket-button_a">
                                            <?php  
                                                $tiket_status = $tiket_info['status'];
                                                if ($tiket_status == "3") {
                                                    $tiket_status = "<span style='color: red;'><i>ZAKLJUCAN</i></span>";
                                                } else if ($tiket_status == "4") {
                                                    $tiket_status = "<span style='color: blue;'><i>PROČITAN</i></span>";
                                                } else if ($tiket_status == "2") {
                                                    $tiket_status = "<span style='color: green;'><i>ODGOVOREN</i></span>";
                                                } else {
                                                    $tiket_status = "<span style='color: #54ff00;'><i>OTVOREN</i></span>";
                                                }
                                            ?>
                                            <button>Status: <?php echo $tiket_status; ?></button>
                                            <?php  
                                                $tiket_opcije = $tiket_info['status'];
                                                if ($tiket_opcije == "3") {
                                                    $tiket_opcije = "<span style='color: #54ff00;'><i>ODKLJUCAJ TIKET</i></span>";
                                                } else {
                                                    $tiket_opcije = "<span style='color: #fff;'><i>ZAKLJUCAJ TIKET</i></span>";
                                                }
                                            ?>
                                            <?php if ($tiket_info['status'] == "3") { ?>
                                                <form action="process.php?task=billing_tiket_unlock" method="POST">
                                                    <input hidden type="text" name="tiket_id" value="<?php echo $tiket_info['id']; ?>">
                                                    <button class="btn btn-large btn-success btn-support-ask">
                                                        <?php echo $tiket_opcije; ?>
                                                    </button>
                                                </form>
                                            <?php } else { ?>
                                                <form action="process.php?task=billing_tiket_lock" method="POST">
                                                    <input hidden type="text" name="tiket_id" value="<?php echo $tiket_info['id']; ?>">
                                                    <button class="btn btn-large btn-danger btn-support-ask">
                                                    <?php echo $tiket_opcije; ?>
                                                    </button>
                                                </form>
                                            <?php } ?>
                                            <?php if ($tiket_info['status'] == "3") {} else { ?>
                                                <form action="process.php?task=billing_send_view" method="POST">
                                                    <input hidden type="text" name="tiket_id"  value="<?php echo $tiket_info['id']; ?>">
                                                    <button class="btn btn-large btn-info btn-support-ask" style="padding:10px;border-bottom:2px solid#0e89d2;">
                                                        POSALJI PONOVO NA PREGLED
                                                    </button>
                                                </form>
                                            <?php } ?>
                                        </div>

                                    </div>

                                </div>

                                <div class="tiket_info_b">   
                                    <div class="tiket-header">
                                        <h3>
                                            <span class="fa fa-info-circle" style="color:#076ba6;font-size:19px;"></span>
                                            <?php echo ispravi_text($tiket_info['naslov']); ?>
                                            <span style="float:right;margin-right:10px;">
                                                <?php echo ispravi_text($tiket_info['datum']); ?>
                                            </span>
                                        </h3>
                                    </div>
                                    
                                    <div class="tiket-content">
                                        
                                        <?php  

                                           $tiket_odg = mysql_query("SELECT * FROM `billing_tiketi_odgovori` WHERE `tiket_id` = '$tiket_info[id]' ORDER BY `id` ASC");

                                            while($row = mysql_fetch_array($tiket_odg)) {

                                                //PROVERA - ADMIN, USER
                                                $User_Odgovor_id    = $row['user_id'];
                                                $Admin_Odgovor_id   = $row['admin_id'];
                                                //ODGOVOR
                                                $Odgovor            = $row['odgovor'];

                                                $klijent = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '$User_Odgovor_id'"));

                                                $admin = mysql_fetch_array(mysql_query("SELECT * FROM `admin` WHERE `id` = '$Admin_Odgovor_id'"));

                                                if ($Admin_Odgovor_id == "") {
                                                    $boja           = "#fff";
                                                    $avatar         = $klijent['avatar'];
                                                    $ime_prezime    = $klijent['ime'].' '.$klijent['prezime'];
                                                } else {
                                                    $boja           = $admin['boja'];
                                                    $avatar         = $admin['avatar'];
                                                    $ime_prezime    = $admin['fname'].' '.$admin['lname'];
                                                }
                                        ?>
                                        
                                            <div class="tiket_info_odg">
                                                
                                                <div class="tiket_info_home_a">
                                                    <li><img src="/img/a/<?php echo $avatar; ?>" alt=""></li>
                                                    <li>
                                                        <p style="color:<?php echo $boja; ?>">
                                                            <strong><?php echo $ime_prezime; ?></strong>
                                                        </p>
                                                    </li>
                                                    <li style="float: right;">
                                                        <p><strong><?php echo ispravi_text(vreme($row['vreme_odgovora'])); ?></strong></p>
                                                    </li>
                                                </div>
                                                
                                                <div class="tiket_info_home_p">
                                                    <p>
                                                        <strong><?php echo $Odgovor; ?></strong>
                                                    </p>
                                                </div>

                                            </div>

                                            <hr>

                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <?php if ($tiket_info['status'] == "0") { ?>
                                    <div class="tiket_info_c">
                                        <div class="tiket-header">
                                            <h3>
                                                <span class="fa fa-info-circle" style="color:red;font-size:19px;"></span>
                                                TIKET JE ZAKLJUCAN !
                                            </h3>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="tiket_info_c">
                                        <div class="tiket-header">
                                            <h3>
                                                <span class="fa fa-info-circle" style="color:#076ba6;font-size:19px;"></span>
                                                Dodajte vas odgovor
                                            </h3>
                                        </div>
                                        
                                        <div class="tiket-content">
                                            <div class="tiket_info_home">
                                                
                                                <div class="tiket_info_home_a">
                                                    <li><img src="<?php echo user_avatar($_SESSION['userid']); ?>" alt=""></li>
                                                    <li><p><strong><?php echo ime_prezime($_SESSION['userid']); ?></strong></p></li>
                                                </div>
                                                
                                                <div class="tiket_info_home_c_o">
                                                    <form action="process.php?task=billing_add_odgovor" method="POST" autocomplete="off">
                                                        <input hidden type="text" name="tiket_id" value="<?php echo $tiket_info['id']; ?>">
                                                        <textarea name="add_odgovor" class="odgovor" placeholder="Dodajte vas odgovor..."></textarea>
                                                        <button>ODGOVORI</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
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