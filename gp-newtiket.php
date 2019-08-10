<?php
include('./fnc/ostalo.php');

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    /*$tiket_id = htmlspecialchars(mysql_real_escape_string(addslashes($_GET['id'])));

    if ($tiket_id == "") {
        $_SESSION['error'] = "Ovaj tiket ne postoji.";
        header("Location: gp-support.php");
        die();
    }

    $tiket_info = mysql_fetch_array(mysql_query("SELECT * FROM `tiketi` WHERE `id` = '$tiket_id' AND `user_id` = '$_SESSION[userid]'"));
    if (!$tiket_info) {
        $_SESSION['error'] = "Ovaj tiket ne postoji ili nemas ovlascenje za isti.";
        header("Location: gp-support.php");
        die();
    }*/
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
                    <div class="space clear" style="margin-top: 20px;"></div>
                    <div class="gp-home">
                        <img src="/img/icon/gp/gp-supp.png" alt="" style="position:absolute; margin: 0% 2%;">
                        <h2 style="margin: 0% 10%;">Podrška</h2>
                        <h3 style="font-size: 12px; margin: 0% 10%;">
                            Dobrodosli u GB-Hoster.me Support panel
                            <br/>Ovde možete otvarati nove tikete ukoliko vam treba pomoć ili podrška oko servera.
                        </h3>
                        <div class="space clear" style="margin-top: 60px;"></div>

                        <div class="supportAkcija right">
                            <li style="list-style-type: none; margin: 10% -40%;">
                                <a href="gp-newtiket.php" class="btn" ><i class="fa fa-refresh"></i> Novi tiket</a>
                            </li>
                        </div>
                        <div id="tiket_body">   
                            <div class="tiket_info" style="margin: 2% 3%;">
                                <div class="tiket_info_c">
                                    <div class="tiket-header">
                                        <h3>
                                            <span class="fa fa-info-circle" style="color:#fff;font-size:19px;"></span>
                                            Potrebna vam je pomoć? -Otvorite novi tiket!
                                        </h3>
                                    </div>
                                    
                                    <div class="tiket-content" style="list-style-type: none;">
                                        <div class="tiket_info_home">
                                            
                                            <div class="tiket_info_home_a">
                                                <li><img src="<?php echo user_avatar($_SESSION['userid']); ?>" alt=""></li>
                                                <li><p><strong><?php echo ime_prezime($_SESSION['userid']); ?></strong></p></li>
                                            </div>
                                            
                                            <div class="tiket_info_home_c_o">
                                                <form action="process.php?task=add_tiket" method="POST" autocomplete="off" style="width: 50%;">
                                                    
                                                    <select name="server_id" id="server_id">
                                                    <?php  
                                                        $server_id_p = mysql_query("SELECT * FROM `serveri` WHERE `user_id` = '$_SESSION[userid]'");
                                                        while($row = mysql_fetch_array($server_id_p)) {
                                                            $server_name_p = ispravi_text_sql($row['name']);
                                                    ?>
                                                        <option value="<?php echo $row['id'] ?>"><?php echo $server_name_p; ?></option>
                                                    <?php } ?>
                                                    </select>
                                                    <br>
                                                    <br>

                                                    <input type="text" name="tiket_naslov" placeholder="Naslov" required="" style="width: 99%;">
                                                  <br>
                                                  <br>
                                                    <textarea name="tiket_text" class="odgovor" placeholder="Napisite vas problem..."></textarea>
                                                    <br>
                                                    <br>
                                                    <button class="btn">Pošalji</button>
                                                </form>
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
    </div>

    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>