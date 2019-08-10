<?php
include('fnc/ostalo.php');

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $server_id = ispravi_text($_GET['id']);
    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));
    
    if (!$server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemas ovlascenje za isti.";
        header("Location: /gp-home.php");
        die();
    }

    $info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));
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
                <div class="space" style="margin-top: 0px;"></div>

                <div id="ftp_container">
                    <div id="ftp_header" style="margin: 0px 0px 0px 30px;">
						<div id="left_header">
                            <div>
                                <img src="/img/icon/gp/gp-konzola.png" style="margin-left:10px;">
                            </div> 
						<h2 style="margin-left: 7%;margin-top: -4%;">Konzola</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 7%;">Ovde mozete slati komande koje ce biti izvrsene na serveru.</h3>
                        <div class="space" style="margin-top: 30px;"></div>
                        </div>
                    </div>              
                    <div id="console_body">
                        <div id="konzolaajax" serverid="<?php echo $server_id; ?>">
                            <div style="margin-top: 20px;"></div>
<pre id="scroll" style="width: 89%;
    height: 465px;
    background: none;
    color: #fff;
    overflow-y: scroll;
    color: #ba0000;
    border: 1px solid #fff;
    margin: 1px 1px 1px 32px;
    padding: 20px;overflow-x: hidden;">
    <?php
  
        if(!($con = ssh2_connect($info['ip'], $info['sshport']))) { 
            return "error";
        } else {
            if(!ssh2_auth_password($con, $server['username'], $server['password'])) {
                return "error";
            } else {
                $stream = ssh2_exec($con,'tail -n 1000 screenlog.0'); 
                stream_set_blocking($stream, true );

                while ($line=fgets($stream)){ 
                    if (!preg_match("/rm log.log/", $line) || !preg_match("/Creating bot.../", $line)){
                        $resp .= $line; 
                    }
                } 

                if(empty($resp)){ 
                    $result_info = "Could not load console log";
                } else { 
                    $result_info = $resp;
                }
            }
        }

        $result_info = str_replace("/home", "", $result_info);
        $result_info = str_replace("/home", "", $result_info);  
        $result_info = str_replace(">", "", $result_info);

        if($server['igra'] == "2") {
			$filename = "ftp://$server[username]:$server[password]@$info[ip]:21/server_log.txt";
			$text .= file_get_contents($filename);
			echo $text;
        } 
	else

	 {
			$text .= htmlspecialchars($result_info);
			echo $text;
        }
    ?>
</pre>
                            
                            <?php if ($server['igra'] == "1") {
                                $rcon_provera = cscfg('rcon_password', $server['id']);
                                if(!$rcon_provera == "") { ?>
                                    <form action="process.php?task=console_rcon_com" method="POST" style="padding: 15px 0px 0px 30px;">
                                        <input hidden="" type="text" name="server_id" value="<?php echo $server['id']; ?>" required="">
                                        <input type="text" name="komanda" placeholder="amx_map <mapname>" required="" style="background: none;border: 1px solid #ccc;padding: 5px 10px;border-radius: 2px;color: #fff;width: 250px;">
                                        <button style="background: none;padding: 5px 10px;border: 1px solid #ccc;border-radius: 2px;color: #fff;">></button>
                                    </form>
                                    <p style="color:#ccc;"><span style="color:red;">(napomena)</span> koristite input bez zagrada, navodnika i html znakova jer u suprotnom skripta nece raditi kako treba!</p>
                                <?php }
                            } ?>
                            <?php if ($server['igra'] == "3") { ?>
                                    <form action="process.php?task=console_rcon_com_mc" method="POST">
                                        <input hidden="" type="text" name="server_id" value="<?php echo $server['id']; ?>" required="">
                                        <input type="text" name="komanda" placeholder="amx_map <mapname>" required="" style="background: none;border: 1px solid #ccc;padding: 5px 10px;border-radius: 2px;color: #fff;width: 250px;">
                                        <button style="background: none;padding: 5px 10px;border: 1px solid #ccc;border-radius: 2px;color: #fff;">></button>
                                    </form>
                                    <p style="color:#ccc;"><span style="color:red;padding: 5px 0px 20px 25px;">(napomena)</span> koristite input bez zagrada, navodnika i html znakova jer u suprotnom skripta nece raditi kako treba!</p>
                                <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function konzola_refresh(id){
	$('#konzolaajax').load('gp-console_log.php?id='+id);	
	$("#scroll").animate({ scrollTop: $("#scroll")[0].scrollHeight});
    }
    setInterval('konzola_refresh(<?php echo $server['id']; ?>)', 8000);
</script>  
    <!-- Php script :) -->

    <?php include('style/footer.php'); ?>

    <?php include('style/pin_provera.php'); ?>

    <?php include('style/java.php'); ?>

</body>
</html>