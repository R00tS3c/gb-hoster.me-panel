<?php

$srw_file = "1";
$ts = "TeamSpeak3";

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

$ts_port = "10011";

require_once($_SERVER['DOCUMENT_ROOT'].'/core/inc/libs/ts/lib/ts3admin.class.php');

$ip = $server_ip['ip'];

if($server['igra'] != "6")
	header("Location:gp-info.php?id=$server_id");

$tsAdmin = new ts3admin($ip, $ts_port);

if($tsAdmin->getElement('success', $tsAdmin->connect())) {
	$tsAdmin->login($server['username'], $server['password']);
	$tsAdmin->selectServer($server['port']);
} else {
	$_SESSION['error'] = "Doslo je do greske.";
	header("Location: gp-voiceservers.php");
	die();
}

$ts_s_info 		= $tsAdmin->serverInfo();

#poke client

if (isset($_POST['ban_id']) && isset($_POST['unban_true'])) {
	$Ban_ID 	= $_POST['ban_id'];
	
	$unban_fnc_ok = $tsAdmin->banDelete($Ban_ID);
	
	if (!$unban_fnc_ok) {
		$_SESSION['error'] = "Doslo je do greske.";
		header("Location: gp-perm.php?id=$server_id");
		die();
	} else {
		$_SESSION['info'] = "Uspesno ste izvrsili komandu.";
		header("Location: gp-perm.php?id=$server_id");
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

<?php include ("style/gamenav.php"); ?>
                <div id="panelnav">
                <?php include('style/server_nav_precice.php'); ?>
                </div>
        <div id="server_info_infor">

            <div id="server_info_infor2">
                <!-- Server meni precice -->
                <div class="space" style="margin-top: 20px;"></div>
				<center>
                <div id="ftp_container">
                    <div id="ftp_header">
					    <div id="ftp_header">
						<div id="left_header">

		                            <div>

		                                <img src="/img/icon/gp/gp-plugins.png">

		                            </div> 

		                            <div style="margin-top:15px;color: #fff;">

		                                <strong>TS3 Permisije</strong>

		                                <p>Ovde mozete dodavati, brisati, editovati i gledati permisije.</p>

		                            </div>

		                        </div>
                    </div>              
                    <div id="plugin_body">
		
		<div id="webftp">

	                                <table class="darkTable">

	                                    <tbody>

	                                        <tr>

	                                            <th>Name</th>

	                                            <th>IP</th>

	                                            <th>Razlog</th>

	                                            <th>Admin</th>

	                                            <th>Action</th>

	                                        </tr>

		                    	

						                    	<?php

												#get serverGroupList

												$get_banned_client = $tsAdmin->banList();

												

												//print_r($get_banned_client);



												#print perm to you

												foreach($get_banned_client['data'] as $ban_list) {

													$Ban_ID 		= $ban_list['banid'];

													$Ban_IP 		= $ban_list['ip'];

													$Ban_Name 		= $ban_list['name'];

													$Ban_Razlog 	= $ban_list['reason'];



													$Ban_Admin 		= $ban_list['invokername'];

													

													//print_r($Perm_Icon);

												?>

												<tr>

													<td><?php echo $Ban_Name; ?></td>

													<td><?php echo $Ban_IP; ?></td>

													<td><?php echo $Ban_Razlog; ?></td>

													<td><?php echo $Ban_Admin; ?></td>

													<td style="width:115px;">

														<li class="right" style="padding:0px 5px;">

															<i class="fa fa-remove"></i>

															<a href="#" data-toggle="modal" data-target="#unban-auth_id_<?php echo $Ban_ID; ?>">Obrisi ban</a>

														</li>

													</td>

												</tr>



													<!-- POKE POPUP -->

<div id="unban-auth_id_<?php echo $Ban_ID; ?>" class="modal fade" role="dialog">

	<div class="modal-dialog">

	    <div id="popUP"> 

	        <div class="popUP">

	            <form action="/gp-bans.php?id=<?php echo $server_id; ?>" method="POST" autocomplete="off" id="modal-unban-auth">

	                <fieldset>

	                    <h2 style="font-size:15px;">Unban <b><?php echo $Ban_Name; ?></b></h2>

	                    <ul>

	                    	<li>

	                    		<p><strong><i>Dali zelite da unbanujete <b><?php echo $Ban_Name; ?></b>?</i></strong></p>

	                    	</li>

	                        <li style="background:none;border:none;">

	                            <input type="hidden" name="ban_id" value="<?php echo $Ban_ID; ?>">

	                            <input type="hidden" name="unban_true" value="true">

	                        </li>

	                        <div class="space clear"></div>

	                        <li style="text-align:center;background:none;border:none;" class="right">

	                        	<button style="color:#333!important;"> <span class="fa fa-check-square-o"></span> Unban</button>

	                        </li>

	                    </ul>

	                </fieldset>

	            </form>

	        </div>        

	    </div>  

	</div>

</div>

<!-- KRAJ - POKE (POPUP) -->

												<?php } ?>

										</tbody>

									</table>

								</div>
				
                </div>
            </div>
        </div>
		</center>
    </div>
        </div>
    </div>
    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>