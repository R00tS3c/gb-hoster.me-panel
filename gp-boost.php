<?php
include 'connect_db.php';
require("fnc/pagination.class.php");

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $server_id = $_GET['id'];
    $proveri_server = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));

    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));
    $server_ip = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));
	
	$ss_ip = $server_ip['ip'];
	$ss_port = $server['port'];
	$fullip = $ss_ip.":".$ss_port;
	$boostovaniserveri = $mdb->query("SELECT * FROM `t2` WHERE `ipport`= '$fullip'");
	
    if (!$proveri_server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemas ovlascenje za isti.";
        header("Location: /gp-home.php");
        die();
    }

    $info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));

    if ($server['slotovi'] <= 25) {
        $_SESSION['info'] = "Samo serveri sa 26 ili vise slotova mogu da koriste ovu opciju.";
        header("Location: gp-info.php?id=".$server['id']);
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
    <div id="ServerBox" style="border: 1px solid #ba0000; background: #000000b5;">
                
<div id="gamenav">
            <ul>
                <li><a href="gp-home.php">News</a></li>
                <li><a href="gp-servers.php">Servers</a></li>
                <li><a href="gp-voiceservers.php">Voice Server</a></li>
                <li><a href="gp-settings.php">Settings</a></li>
                <li><a href="gp-iplog.php">IP Log</a></li>
                <li><a href="client_process.php?task=logout">Logout</a></li> 
            </ul>
</div>
				<div id="panelnav">
                <?php include('style/server_nav_precice.php'); ?>
                </div>

        <div id="server_info_infor">

            <div id="server_info_infor2">
                <!-- Server meni precice -->
                <div class="space" style="margin-top: 20px;"></div>

                <div id="ftp_container">
                    <div id="ftp_header" style="margin: 0px 0px 0px 30px;">
						  <div id="ftp_header">
											<div id="left_header">
                            <div>
                                <img src="/img/icon/gp/gp-plugins.png" style="margin-left:10px;">
                            </div> 
						<h2 style="margin-left: 7%;margin-top: -4%;">Boost</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 7%;">Ovde mozete bostovati vas server svakih 2 dana free!</h3>
                        <div class="space" style="margin-top: 60px;"></div>
                        </div>
                        
                    </div>              
                    </div>              
                    <div id="plugin_body">
                        <form id="form" action="process.php?task=boost_server" method="POST">
                            <input type="hidden" name="server_id" value="<?php echo $server_id; ?>" />
                                                        <a href="javascript:{}" onclick="document.getElementById('form').submit();"><div class="divbutton" style="margin-top:3%;float: right; margin-top: -60px;margin-right: 20px;">BOOST</div></a>
                        </form>
                    </div>
                </div>
				
				<div id="serveri">
                            <center><table class="darkTable">
                                <tbody>
                                    <tr style="background: #ba0000ab;">
                                        <th>IP SERVERA</th>
                                        <th>VREME BOOSTA</th>
										<th>ISTICE BOOST</th>
                                    </tr>
                                    <?php
                                        error_reporting(0);  
										$queryelem = $mdb->query("SELECT * FROM `t2` WHERE `ipport`='$fullip'");
                                        $numberOfElements = mysqli_num_rows($queryelem);
                                        $currentPage = $_GET['page'];

                                        $elementsPerPage = 15;
                                        $paginationWidth = 9;
                                        $data = Pagination::load($numberOfElements, $currentPage, $elementsPerPage, $paginationWidth);

                                        $start = ($data['currentPage']-1) * intval($elementsPerPage);
                                        $limit = intval($elementsPerPage);
                                        $data_query = $mdb->query("SELECT * FROM `t2` WHERE `ipport`= '$fullip' LIMIT {$start}, {$limit}");
                                    
                                        while($row = mysqli_fetch_array($data_query)) { 

                                            $ipsrv = htmlspecialchars(mysqli_real_escape_string($mdb, addslashes($row['ipport'])));
											$vremeboosta = htmlspecialchars(mysqli_real_escape_string($mdb, addslashes($row['vreme'])));
											$vrmbst = new DateTime($vremeboosta);
											$istice = $vrmbst->modify('+2 day')->format('Y-m-d H:i:s');
                                        ?>  
                                            <tr>
                                                <td><?php echo $ipsrv; ?></td>
                                                <td><?php echo $vremeboosta; ?></td>
												<td><?php echo $istice; ?></td>
                                            </tr>
                                    <?php } ?>                               
                                </tbody>
                            </table>
                        </center>
                            <br />
                            <div class="pagg">
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

    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>
