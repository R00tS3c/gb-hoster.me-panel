<?php

$srw_file = "1";

include 'connect_db.php';

if (is_login() == false) {
    $_SESSION['error'] = "Niste logirani!";
    header("Location: /home");
    die();
} else {
    $server_id = $_GET['id'];
    $proveri_server = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));

    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));
    $server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));

    if (!$proveri_server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemate ovlaščenje za isti.";
        header("Location: /gp-home.php");
        die();
    }
}
$new_ip = ipadresa($server['id']);

$server_onli = "<span style='color:#54ff00;'>Online</span>"; 
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

<?php include ("style/gpmenu.php"); ?>
                <div id="panelnav">
                <?php include('style/server_nav_precice.php'); ?>
                </div>
        <div id="server_info_infor">

            <div id="server_info_infor2">
                <!-- Server meni precice -->
                <div class="space"></div>

                <h5 style="margin: 1px 1px 22px 62px;font-size: 15px;"><?php echo $jezik['27']; ?></h5>
                <div class="server_infoInfo" style="max-width: 350px;border:1px solid;border-color: #ba0000;margin-left:6%;padding-left:3%;">

                    <div class="SrwInfo_Info">
					                        <?php  
                            if (is_pin() == false) {
                                $provera_pin = "#pin-auth"; 
                            } else {
                                $provera_pin = "#edit_name";
                            }
                        ?> 
                        <label style="color: #bbb;font-size: 15px;margin-top: 17px;"><?php echo $jezik['28']; ?><strong style="color: #ba0000;">
                                <?php echo $server['name']; ?>
                                <button style="background:none;border:none;color:#fff;" type="button" data-toggle="modal" data-target="<?php echo $provera_pin; ?>"><span class="fa fa-edit"></span></button>
                        </strong></label>
						
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['29']; ?><strong style="color: #ba0000;">
                                <?php echo $server['istice']; ?>
                                <a href="produzi.php?id=<?php echo $server['id']; ?>" style="background:none;border:none;color:#fff;"><span class="fa fa-edit"></span></a>
                        </strong></label><br/>
							
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['30']; ?> <strong style="color: #ba0000;"><?php echo gp_igra($server['igra']); ?></strong></label>
                        <br/>
                        <label style="color: #bbb;font-size: 15px;">FastDL Link:<strong style="color: #ba0000;"><?php echo $new_ip; ?></strong></label><br/>
                        
                        <?php
                            $serverStatus = $server['status'];  
                            if ($serverStatus == "Aktivan") {
                                $serverStatus = "<span style='color: #54ff00;'> Aktivan </span>";
                            } else if($serverStatus == "Suspendovan") {
                                $serverStatus = "<span style='color: #ffd800;'> Suspendovan </span>";
                            } else {
                                $serverStatus = "<span style='color: red;'> Neaktivan </span>";
                            }
                        ?> 
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['33']; ?><strong style="color: #ba0000;"><?php echo $serverStatus; ?></strong></label>
                     </div>

<br>
                </div>
                <h5 class="pc-icon" style="float: right;margin: -260px 325px 0px 0px;font-size: 15px;"><?php echo $jezik['34']; ?></h5>
                <div class="server_infoInfo2" style="max-width: 350px;border:1px solid;border-color: #ba0000; float: right;margin-top: -22.5%;margin-right: 7%;padding-left:2%;height:190px;width: 350px;">
                   
                    <div class="ServerInfoFTP" style="padding-top: 26px;padding-left:5px;">
                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['35']; ?> <strong style="color: #ba0000;font-size: 13px;"><?php echo $server_ip['ip']; ?></strong></label><br/>

                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['36']; ?> <strong style="color: #ba0000;font-size: 13px;">21</strong></label><br/>

                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['37']; ?> <strong style="color: #ba0000;font-size: 13px;"><?php echo $server['username']; ?></strong></label>
                        <br/>

                        <label style="color: #bbb;font-size: 15px;"><?php echo $jezik['38']; ?>                             <strong style="color: #ba0000;font-size: 13px;">
                                <?php if (is_pin() == false) { ?>
                                   <?php echo $jezik['39']; ?>
                                   <i class="fa fa-chevron-right" style="font-size: 15px;"></i>
                                                               <?php if (is_pin() == false) { ?>
                            <a style="cursor: pointer;" type="button" data-toggle="modal" data-target="#pin-auth"><?php echo $jezik['40']; ?></a>
                            <?php } ?>
                                <?php } else { echo $server['password'];  ?>
                                                            <a style="cursor: pointer;" type="button" data-toggle="modal" data-target="#ftp-pw">Promeni FTP sifru</a> <?php } ?>
                            </strong></label>
                    </div>
                </div>
				
                <!-- server ftp precice -->
                <?php include('style/server_precice.php'); ?>
                <div class="space" style="margin-top: 20px;"></div>
            </div>
        </div>
    </div>
    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>