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

if (isset($_POST['sgid']) && isset($_POST['perm_name']) && isset($_POST['edit_true'])) {
	$Perm_ID 	= txt($_POST['sgid']);
	$Perm_Name 	= txt($_POST['perm_name']);
	
	$poke_msg_ok = $tsAdmin->serverGroupRename($Perm_ID, $Perm_Name);
	
	if (!$poke_msg_ok) {
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

	                                            <th>Action</th>

	                                        </tr>

		                    	

						                    	<?php

												#get serverGroupList

												$get_group_list = $tsAdmin->serverGroupList();

												

												//print_r($get_group_list);



												#print perm to you

												foreach($get_group_list['data'] as $perm_) {

													$Perm_ID 		= txt($perm_['sgid']);

													$Perm_Name 		= txt($perm_['name']);

													$Perm_Icon 		= $tsAdmin->serverGroupGetIconBySGID($Perm_ID);



													//print_r($Perm_Icon);

												?>

												<tr>

													<td>

														<img src="data:image/png;base64,<?php echo $Perm_Icon['data']; ?>" /> 

														<?php echo $Perm_Name; ?>

													</td>

													<td style="width:100px;">

														<li class="left" style="padding:0px 5px;">

															<i class="fa fa-edit"></i>

															<a href="#" data-toggle="modal" data-target="#izmeni-auth_id_<?php echo $Perm_ID; ?>">Izmeni</a>

														</li>

													</td>

												</tr>



													<!-- POKE POPUP -->

<div id="izmeni-auth_id_<?php echo $Perm_ID; ?>" class="modal fade" role="dialog">

	<div class="modal-dialog">

	    <div id="popUP"> 

	        <div class="popUP">

	            <form action="/gp-perm.php?id=<?php echo $Server_ID; ?>" method="POST" autocomplete="off" id="modal-izmeni-auth">

	                <fieldset>

	                    <h2 style="font-size:15px;">Izmeni <?php echo $Perm_Name; ?> permisiju</h2>

	                    <ul>

	                        <li>

	                            <label>Name:</label>

	                            <input type="hidden" name="sgid" value="<?php echo $Perm_ID; ?>">

	                            <input type="hidden" name="edit_true" value="true">

	                            <input type="text" name="perm_name" value="<?php echo $Perm_Name; ?>" class="short">

	                        </li>

	                        <div class="space clear"></div>

	                        <li style="text-align:center;border:none;">

	                        	<button style="color:#333!important;"> <span class="fa fa-check-square-o"></span> Save</button>

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