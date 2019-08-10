<?php
session_start();

include("konfiguracija.php");
include("includes.php");

if (isset($_POST['task']))
{
	$task = mysql_real_escape_string($_POST['task']);
}

else if (isset($_GET['task']))
{
	$task = mysql_real_escape_string($_GET['task']);
}

switch (@$task)
{
	case 'profil':
		$username = sqli($_POST['username']);
		$password = sqli($_POST['password']);
		$ime = sqli($_POST['ime']);
		$prezime = sqli($_POST['prezime']);
		$email = sqli($_POST['email']);
		$email = strtolower($email);
		
		if(!empty($_POST['signature']))
		{
			$signature = mysql_real_escape_string(nl2br(htmlspecialchars($_POST['signature'])));
			$zamene = array(
				'&lt;br /&gt;' => '<br />',
				'<br />' => '<br />',
				'\n' => '<br />',
				'[b]' => '<b>',
				'[i]' => '<i>',
				'[/i]' => '</i>',
				'[u]' => '<u>',
				'[/u]' => '</u>',
				'[/b]' => '</b>');
			$signature = str_replace(array_keys($zamene), array_values($zamene), $signature);			
		}
		else $signature = "";
		
		if(empty($username)) {
			$error .= "Korisnicko ime nije uneto.";
		}
		
		if($username != $_SESSION['a_username'])
		{
			if(query_numrows("SELECT * FROM `admin` WHERE `username` = '{$username}'") == 1)
			{
				$error = "To korisnicko ime vec postoji.";
			}
		}
		
		if(strlen($username) > 16)
		{
			$error = "Username moze imati najvise 16 karaktera";
		}
		
		if(strlen($username) < 3)
		{
			$error = "Username moze imati najmanje 3 karaktera";
		}
		
		if(strlen($ime) > 10)
		{
			$error = "Username moze imati najvise 10 karaktera";
		}
		
		if(strlen($ime) < 2)
		{
			$error = "Username moze imati najmanje 2 karaktera";
		}
		
		if(strlen($prezime) > 15)
		{
			$error = "Username moze imati najvise 15 karaktera";
		}
		
		if(strlen($prezime) < 2)
		{
			$error = "Username moze imati najmanje 2 karaktera";
		}
		
		if(empty($ime)) {
			$error .= "Ime nije uneto.";
		}
		
		if(empty($prezime)) {
			$error .= "Prezime nije uneto.";
		}
		
		if(empty($email)) {
			$error .= "E-mail nije unet.";
		}

		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greska";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			//unset($error);
			header( "Location: profil" );
			die();
		}			
		
		$velicina = 1024*2000; // 2MB
		$ekstenzije = array('jpeg', 'jpg', 'png', 'gif');

		$sizes = array(1000 => 1000, 1500 => 1500);
	
		$enable_max_size = 1; // 0 - Off || 1 - On
		$max_size = 2048; // Max image size

		$enable_max_width = 1; // 0 - Off || 1 - On
		$max_width = 1500; // Max image width

		$enable_max_height = 1;  // 0 - Off || 1 - On
		$max_height= 1500; // Max image width

		$enable_black_list = 0; // 0 - Off || 1 - On
		$black_list = array(".php",".html",".css",".sql",".js",".asp",".aspx",".phtml",".php4",".shtml",".pl",".py",".txt","xml",".cgi",".php3",".jsp",".sh",".c"); // Forbidden extensions

		$enable_white_list = 1; // 0 - Off || 1 - On
		$white_list = array(".jpg",".jpeg",".png",".gif"); // Allowed extensions

		$enable_mime = 1; // 0 - Off || 1 - On
		$mime_content_types = array("image/jpg","image/png","image/gif","image/jpeg"); // Allowed mime content types

		$site_link = "";
		$upload_directory = "./avatari/"; // For current dir leave empty
		$enable_direct_link = 1; // 0 - Off || 1 - On
		$enable_forum_code = 1; // 0 - Off || 1 - On
		$enable_html_code = 1; // 0 - Off || 1 - On

		$klijentid = $_SESSION['a_id'];

		if($_FILES['avatar']) 
		{
			if(!getimagesize($_FILES['avatar']['tmp_name'])) $error = "Invalid image File! Please chose a valid image!";
			else 
			{
				$getimagesize = getimagesize($_FILES['avatar']['tmp_name']);
				$image_name = $_FILES['avatar']['name'];
				$image_size = $_FILES['avatar']['size'] / 1024;
				$image_type = $_FILES['avatar']['type'];
				$image_temp = $_FILES['avatar']['tmp_name'];
				$image_width = $getimagesize[0];
				$image_height = $getimagesize[1];
				$image_extension = str_replace("%00", "", strtolower(strrchr($image_name, ".")));
				$image_uname = $klijentid.''.$image_extension;		

				if($image_size > $max_size) $error = "Image is to big! Max image size in kilobytes is $max_size";
				if($image_width > $max_width) $error = "Image width is to big! Max width is $max_width";
				if($image_height > $max_height) $error = "Image height is to big! Max height is $max_width";

				$found = 0;
				$echo_blist = "";
				foreach($black_list as $blist) {
					if(strstr($image_extension, $blist)) {
						$found = 1;
						break;
					}
				}
				if($found == 1) {
					$error = "Chosen image containts invalid extension. Invalid extensions: ";
					foreach($black_list as $blist) {
						$echo_blist .= "<b>".$blist."</b>&nbsp;";
					}
					$error = substr($echo_blist, 0, -1);
				}

				$found_white = 0;
				$echo_wlist = "";
				foreach($white_list as $wlist) {
					if(strstr($image_extension, $wlist)) {
						$found_white = 1;
						break;
					} 
					else $found_white = 0;
				}
				if($found_white == 0) {
					$error = "Chosen image doesen't containts valid extension. Valid extensions: ";
					foreach($white_list as $wlist) {
						$echo_wlist .= "<b>".$wlist."</b>&nbsp;";
					}
					$error = substr($echo_wlist, 0, -1);
				}

				$found_mime = 0;
				$echo_mime = "";
				foreach($mime_content_types as $mime) {
					if(strstr($image_type, $mime)) {
						$found_mime = 1;
						break;
					} 
					else $found_mime = 0;
				}
				if($found_mime == 0) {
					$error = "Chosen image doesen't containts valid mime content type. Valid mimces: ";
					foreach($mime_content_types as $mime) {
						$echo_mime .= "<b>".$mime."</b>&nbsp;";
					}
					$error = substr($echo_mime, 0, -1);
				}

				//--------------------------// Upload image //--------------------------//

				if($upload_directory != "") {
					if(!file_exists($upload_directory)) {
						mkdir($upload_directory);
					}
				}

				if (!empty($error))
				{
					$_SESSION['msg1'] = "Greska";
					$_SESSION['msg2'] = $error;
					$_SESSION['msg-type'] = 'error';
					unset($error);
					header( "Location: profil" );
					die();
				}	

				if($upload_directory == "" || file_exists($upload_directory)) {
					resize($image_extension);

					query_basic("UPDATE admin SET avatar = '{$image_extension}' WHERE id = '{$klijentid}'");

					mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
					mysql_query('UPDATE admin SET lastactivityname = "Promena svog profila" WHERE id="'.$_SESSION["a_id"].'"');		

					$_SESSION['msg1'] = "Uspesnos";
					$_SESSION['msg2'] = "Uspesno ste promenili vas profil!";
					$_SESSION['msg-type'] = 'success';				
					header( "Location: profil.php" );
					die();
				}
			}
		}
		
		if(empty($password)) 
		{
			mysql_query( "UPDATE admin SET 
				`username` = '".$username."', 
				`fname` = '".$ime."',
				`lname` = '".$prezime."',
				`signature` = '".$signature."',
				`email` = '".$email."' WHERE `id` = '".$_SESSION['a_id']."'" ) or die(mysql_error());	
								
			mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			mysql_query('UPDATE admin SET lastactivityname = "Promena svog profila" WHERE id="'.$_SESSION["a_id"].'"');	

			$poruka = "Promena svog profila";
			alog($_SESSION['a_id'], $poruka, $ime.' '.$prezime, fuckcloudflare());				
							
			$_SESSION['msg1'] = "Uspesno";
			$_SESSION['msg2'] = "Uspesno ste promenili vas profil!";
			$_SESSION['msg-type'] = 'success';				
			header( "Location: profil.php" );
			die();
		} else {

			mysql_query( "UPDATE admin SET
				`username` = '".$username."',
				`password` = '".sifra($password)."',
				`fname` = '".$ime."',
				`lname` = '".$prezime."',
				`signature` = '".$signature."',
				`email` = '".$email."' WHERE `id` = '".$_SESSION['a_id']."'" ) or die(mysql_error());		

			mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
			mysql_query('UPDATE admin SET lastactivityname = "Promena svog profila" WHERE id="'.$_SESSION["a_id"].'"');		

			$poruka = "Promena svog profila";
			alog($_SESSION['a_id'], $poruka, $ime.' '.$prezime, fuckcloudflare());	

			$_SESSION['msg1'] = "Uspesno";
			$_SESSION['msg2'] = "Uspesno ste promenili vas profil!";
			$_SESSION['msg-type'] = 'success';				
			header( "Location: profil.php" );
			die();
		}		
		
		if (!empty($error))
		{
			$_SESSION['msg1'] = "Greska";
			$_SESSION['msg2'] = $error;
			$_SESSION['msg-type'] = 'error';
			//unset($error);
			header( "Location: profil" );
			die();
		}	
	break;
}

exit('<h1><b>403 Forbidden</b></h1>'); //If the task is incorrect or unspecified, we drop the user.
?>