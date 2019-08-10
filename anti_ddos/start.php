<?php 
	 
	if(!isset($_SESSION)){
		session_start();
	}
	if(isset($_SESSION['standby'])){
		/**
		 * AntiDDOS System
		 * FILE: index.php
		 * By Sanix Darker
		 */

		// There is all your configuration

		$_SESSION['standby'] = $_SESSION['standby']+1;

		 $ad_ddos_query = 5;// ​​number of requests per second to detect DDOS attacks
		 $ad_check_file = 'check.txt';// file to write the current state during the monitoring
		 $ad_all_file = 'all_ip.txt';// temporary file
		 $ad_black_file = 'black_ip.txt';// will be entered into a zombie machine ip
		 $ad_white_file = 'white_ip.txt';// ip logged visitors
		 $ad_temp_file = 'ad_temp_file.txt';// ip logged visitors
		 $ad_dir = 'anti_ddos/files';// directory with scripts
		 $ad_num_query = 0;// ​​current number of requests per second from a file $check_file
		 $ad_sec_query = 0;// ​​second from a file $check_file
		 $ad_end_defense = 0;// ​​end while protecting the file $check_file
		 $ad_sec = date ("s");// current second
		 $ad_date = date ("is");// current time
		 $ad_defense_time = 100;// ddos ​​attack detection time in seconds at which stops monitoring
		 

		$config_status = "";
		function Create_File($the_path){

			$handle = fopen($the_path, 'w') or die('Cannot open file:  '.$the_path);
			return "Creating ".$the_path." .... done";
		}


		 // Checking if all files exist before launching the cheking
		$config_status .= (!file_exists("{$ad_dir}/{$ad_check_file}")) ? Create_File("{$ad_dir}/{$ad_check_file}") : "ERROR: Creating "."{$ad_dir}/{$ad_check_file}<br>";
		$config_status .= (!file_exists("{$ad_dir}/{$ad_temp_file}")) ? Create_File("{$ad_dir}/{$ad_temp_file}") : "ERROR: Creating "."{$ad_dir}/{$ad_temp_file}<br>";
		$config_status .= (!file_exists("{$ad_dir}/{$ad_black_file}")) ? Create_File("{$ad_dir}/{$ad_black_file}") : "ERROR: Creating "."{$ad_dir}/{$ad_black_file}<br>";
		$config_status .= (!file_exists("{$ad_dir}/{$ad_white_file}")) ? Create_File("{$ad_dir}/{$ad_white_file}") : "ERROR: Creating "."{$ad_dir}/{$ad_white_file}<br>";
		$config_status .= (!file_exists("{$ad_dir}/{$ad_all_file}")) ? Create_File("{$ad_dir}/{$ad_all_file}") : "ERROR: Creating "."{$ad_dir}/{$ad_all_file}<br>";

		if(!file_exists ("{$ad_dir}/../anti_ddos.php")){
			$config_status .= "anti_ddos.php does'nt exist!";
		}

		if (!file_exists("{$ad_dir}/{$ad_check_file}") or 
		 		!file_exists("{$ad_dir}/{$ad_temp_file}") or 
		 			!file_exists("{$ad_dir}/{$ad_black_file}") or 
		 				!file_exists("{$ad_dir}/{$ad_white_file}") or 
		 					!file_exists("{$ad_dir}/{$ad_all_file}") or 
		 						!file_exists ("{$ad_dir}/../anti_ddos.php")) {

			 						$config_status .= "Some files does'nt exist!";
			 						die($config_status);
		}


		// TO verify the session start or not
		require ("{$ad_dir}/{$ad_check_file}");

		if ($ad_end_defense and $ad_end_defense> $ad_date) {
			require ("{$ad_dir}/../anti_ddos.php");
		} else {

			$ad_num_query = ($ad_sec == $ad_sec_query) ? $ad_num_query++ : '1 ';
			$ad_file = fopen ("{$ad_dir}/{$ad_check_file}", "w");

			$ad_string = ($ad_num_query >= $ad_ddos_query) ? '<?php $ad_end_defense ='.($ad_date + $ad_defense_time).'; ?>' : '<?php $ad_num_query ='. $ad_num_query. '; $ad_sec_query ='. $ad_sec. '; ?>';

			fputs ($ad_file, $ad_string);
			fclose ($ad_file);
		}
	}else{

			$_SESSION['standby'] = 1;
			
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			header("refresh:8,".$actual_link);
		?>
<style>
@media (max-width:1000px){.title,.title-sub{font-weight:300;text-transform:uppercase;text-align:center}.hide{display:none}.wrap{width:95%;margin:0 auto}.firewall img{max-width:290px}.loadline{height:35px;position:relative;margin:20px auto 0;padding:2px;border:2px solid #17687C;width:90%}.title{font-size:22.5px;text-shadow:0 0 30px #219BBA}.title-sub{font-size:8.5px;text-shadow:0 0 20px #219BBA}}@media (min-width:1000px){.title,.title-sub{font-weight:300;text-transform:uppercase;text-align:center}.wrap{width:80%;margin:0 auto}.firewall img{max-width:400px}.loadline{height:35px;position:relative;margin:20px auto 0;padding:2px;border:2px solid #17687C;width:770px}.title{font-size:64.5px;text-shadow:0 0 30px #219BBA}.title-sub{font-size:24.2px;text-shadow:0 0 20px #219BBA}}*{margin:0}body{background:url(http://i.imgur.com/a8YPROl.png);color:#fff;font-weight:400;font-family:'Open Sans',sans-serif}.loadline>span{display:block;height:100%;background-color:#17687C;background-image:-webkit-gradient(linear,left bottom,left top,color-stop(0,rgba(23,104,124,.4)),color-stop(1,rgba(23,104,124,.4)));background-image:-moz-linear-gradient(center bottom,#2bc253 37%,#54f054 69%);position:relative;overflow:hidden}.animate>span>span,.loadline>span:after{content:"";position:absolute;top:0;left:0;bottom:0;right:0;background-image:-webkit-gradient(linear,0 0,100% 100%,color-stop(.25,rgba(255,255,255,.2)),color-stop(.25,transparent),color-stop(.5,transparent),color-stop(.5,rgba(255,255,255,.2)),color-stop(.75,rgba(255,255,255,.2)),color-stop(.75,transparent),to(transparent));background-image:-moz-linear-gradient(-45deg,rgba(255,255,255,.2) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.2) 50%,rgba(255,255,255,.2) 75%,transparent 75%,transparent);z-index:1;-webkit-background-size:50px 50px;-moz-background-size:50px 50px;-webkit-animation:move 2s linear infinite;overflow:hidden}.animate>span:after{display:none}@-webkit-keyframes move{0%{background-position:0 0}100%{background-position:50px 50px}}.loader>span{background-color:rgba(23,104,124,.4)}.nostripes>span:after,.nostripes>span>span{-webkit-animation:none;background-image:none}.animated{-webkit-animation-duration:1s;animation-duration:1s;-webkit-animation-fill-mode:both;animation-fill-mode:both}@-webkit-keyframes zoomIn{from{opacity:0;-webkit-transform:scale3d(.3,.3,.3);transform:scale3d(.3,.3,.3)}50%{opacity:1}}@keyframes zoomIn{from{opacity:0;-webkit-transform:scale3d(.3,.3,.3);transform:scale3d(.3,.3,.3)}50%{opacity:1}}.zoomIn{-webkit-animation-name:zoomIn;animation-name:zoomIn}@-webkit-keyframes flash{100%,50%,from{opacity:1}25%,75%{opacity:0}}@keyframes flash{100%,50%,from{opacity:1}25%,75%{opacity:0}}.flash{-webkit-animation-name:flash;animation-name:flash}.animated.infinite{-webkit-animation-iteration-count:infinite;animation-iteration-count:infinite}.firewall{text-align:center;margin:20px 0 -40px}#flash{-webkit-animation-duration:4s;-ms-animation-delay:5s}</style>
<!DOCTYPE html>
<title>GB-HOSTER ANTI-DDOS</title>
<body>
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
    <div class="wrap">
        <div class="firewall animated flash infinite" id="flash"><img src="https://i.imgur.com/xYlPkfQ.png" /></div>
        <div class="title animated zoomIn">Zaustavio vas je naš firewall</div>
        <div class="title-sub animated zoomIn">Bićete prebačeni na sajt za nekoliko sekundi</div>
        <div class="loadline loader animated zoomIn">
            <span style="width: 100%"></span>
        </div>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


</body>

	<?php exit(); }

?>