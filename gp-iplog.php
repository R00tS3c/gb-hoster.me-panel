<?php
include 'connect_db.php';
require("fnc/pagination.class.php");

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
    <div id="ServerBox" style="background: #000000ad; border: 1px solid #ba0000;">
<?php include("style/gpmenu.php"); ?>
    <div id="ServerBox">
        <div id="server_info_infor">    
            <div id="server_info_infor">
                <div id="server_info_infor2">
                    <div class="space" style="margin-top: 20px;"></div>
                    <div class="gp-home">
                        <img src="/img/icon/gp/gp-server.png" alt="" style="margin-left:20px;">
                        <h2 style="margin-left: 6%;margin-top: -5%;">Logovi</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Lista svih logova ovog naloga</h3>
                        <div class="space" style="margin-top: 60px;"></div>
                        
                        <div id="serveri">
                           <center> <table class="darkTable">
                                <tbody>
                                    <tr>
                                        <th>ID</th>
                                        <th>Poruka</th>
                                        <th>Ip adresa</th>
                                        <th>D&V</th>
                                    </tr>
                                    <?php
                                        error_reporting(0);  
                                        $numberOfElements = mysql_num_rows(mysql_query("SELECT * FROM `logovi` WHERE `clientid`='{$_SESSION['userid']}' ORDER BY id DESC"));
                                        $currentPage = $_GET['page'];

                                        $elementsPerPage = 15;
                                        $paginationWidth = 9;
                                        $data = Pagination::load($numberOfElements, $currentPage, $elementsPerPage, $paginationWidth);

                                        $start = ($data['currentPage']-1) * intval($elementsPerPage);
                                        $limit = intval($elementsPerPage);
                                        $data_query = mysql_query("SELECT * FROM `logovi` WHERE `clientid`='{$_SESSION['userid']}' ORDER BY id DESC LIMIT {$start}, {$limit}");
                                    
                                        while($row = mysql_fetch_array($data_query)) { 

                                            $log_id = htmlspecialchars(mysql_real_escape_string(addslashes($row['id'])));
                                            $message = mysql_real_escape_string(addslashes($row['message']));
                                            $log_ip = htmlspecialchars(mysql_real_escape_string(addslashes($row['ip'])));
                                            $vreme = htmlspecialchars(mysql_real_escape_string(addslashes($row['vreme'])));
                                            $clientid = htmlspecialchars(mysql_real_escape_string(addslashes($row['clientid'])));
                                            $adminid = htmlspecialchars(mysql_real_escape_string(addslashes($row['adminid'])));
                                        ?>  
                                        <?php if ($adminid == ""||$adminid == "NULL") { ?>
                                            <tr>
                                                <td>#<?php echo $log_id; ?></td>
                                                <td><?php echo $message; ?></td>
                                                <td class="ip">
                                                    <?php if (is_demo() == true) {
                                                        echo $log_ip;
                                                    } else {
                                                        $iseci = explode(".", $log_ip);
                                                        echo $iseci[0].'.'.$iseci[1].'.'.'**'.'.'.'**';
                                                        //echo "IP nije vidljiv demo nalogu.";
                                                    } ?>
                                                </td>
                                                <td><?php echo vreme($vreme); ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>                               
                                </tbody>
                            </table>
                        </center>
                            <br />
                            <div class="pagg" style="float: right; margin: -5px 30px 0px 0px;">
                                <?php  
                                    if($data['previousEnabled']) {
                                        echo '<a class="prev_page" href="?page=' . ($currentPage-1) . '">«</a>';
                                    } else { 
                                        echo '<span class="prev disabled">«</span>';
                                    }

                                    foreach ($data['numbers'] as $number) {
                                        echo '<a class="pages active" href="?page=' . $number . '">' .  $number . '</a>';
                                        echo '&nbsp;&nbsp;';
                                    }

                                    if ($data['nextEnabled']) {
                                        echo '<a class="next_page" href="?page=' . ($currentPage+1) . '">»</a>';
                                    } else {
                                        echo '<span class="next disabled">»</span>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="space" style="margin-bottom: 20px;"></div>
                </div>
            </div>
        </div>
     </div>
<style>
    #stats
    {
            width: 286px!important;
    }
</style>
    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>