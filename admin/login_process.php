<?php
session_start();
$fajl = "login";
//------------------------------------

include('../konfiguracija.php');
include('includes.php');

if (isset($_POST['task']))
{
	$task = mysql_real_escape_string($_POST['task']);
}
else if (isset($_GET['task']))
{
	$task = mysql_real_escape_string($_GET['task']);
}

//error_reporting(E_ALL);
switch(@$task)
{
	case 'logout':
		mysql_query("UPDATE `admin` SET `lastactivityname` = 'Logout' `lastactivity` = '".time() - (0 * 0 * 11 * 10) ."' WHERE id='".$_SESSION['a_id']."'");
		logout();
	break;
		
		case 'login':
			$username = mysql_real_escape_string($_POST['username']);
			$sifra = mysql_real_escape_string($_POST['sifra']);
			
			//mysql_query('UPDATE admin SET password = "7e6f8cf49d93fc087255c9ef4c5a17f775b2e14e" WHERE username="'.$username.'"');
			
			if(!empty($username) && !empty($sifra))
			{
				$sifra = sifra($_POST['sifra']);
				$_SESSION['pw'] = $sifra; // Koristi se <?php echo $_SESSION['pw']; na login stranici, ako se zaboravi pw.
				
				$broj = query_numrows("SELECT * FROM `admin` WHERE `username` = '{$username}' AND `password` = '{$sifra}'");

				if($broj == 1)
				{
					$rows = query_fetch_assoc("SELECT `id`, `username`, `fname`, `lname` FROM `admin` WHERE `username` = '{$username}' AND `password` = '{$sifra}'");
					
					$vremesada = time()-60;
					
					if(query_numrows("SELECT * FROM `admin` WHERE `lastactivity` > '{$vremesada}' AND `username` = '{$username}'") == 1)
					{
						$_SESSION['msg'] = "Neko je već ulogovan na ovaj nalog";
						if (!empty($return))
						{
							header( "Location: ".urldecode($return));
							die();
						}
						else
						{
							header( "Location: index.php" );
							die();
						}						
					}	

					$_SESSION['a_ulogovan'] = true;
					$_SESSION['a_username'] = $username;
					$_SESSION['a_ime'] = $rows['fname'];
					$_SESSION['a_prezime'] = $rows['lname'];
					$_SESSION['a_id'] = $rows['id'];					
					
					$poruka = "Uspešan login.";
					alog($rows['id'], $poruka, $rows['fname'].' '.$rows['lname'], fuckcloudflare());
		
					$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
					$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Login" WHERE id="'.$_SESSION["a_id"].'"');

					$cookie = "{$rows['id']}|{$sifra}";
					$cookie = $rows['id']."-".hash('sha512', $cookie);
					
					if (isset($_POST['rememberMe']))
					{
						setcookie('m1ol0g1n', htmlentities($cookie, ENT_QUOTES), time() + (86400 * 7 * 2));
						setcookie('adminUsername', htmlentities($username, ENT_QUOTES), time() + (86400 * 7 * 2));
					}
					else if (isset($_COOKIE['m1ol0g1n']))
					{
						setcookie('m1ol0g1n', htmlentities($cookie, ENT_QUOTES), time() - 3600);
						setcookie('adminUsername', htmlentities($username, ENT_QUOTES), time() - 3600);
					}

					##
					if (!empty($_SESSION['loginattempt']))
					{
						unset($_SESSION['loginattempt']);
					}
					else if (!empty($_SESSION['lockout']))
					{
						unset($_SESSION['lockout']);
					}

					if (!empty($return))
					{
						$_SESSION['msg-type'] = "success";
						$_SESSION['msg1'] = "Uspešno";
						$_SESSION['msg2'] = "Ulogovali ste se.";
						header( "Location: ".urldecode($return));
						die();
					}
					else
					{
						$_SESSION['msg-type'] = "success";
						$_SESSION['msg1'] = "Uspešno";
						$_SESSION['msg2'] = "Ulogovali ste se.";
						header( "Location: index.php" );
						die();
					}			
				}
			}
			
			$sifra = htmlspecialchars(mysql_real_escape_string($_POST['sifra']));
			$poruka = "Neuspešan login. ( <m>Korisnicko ime:</m> ".$username." | <m>Lozinka</m>: ".$sifra." )";
			$poruka = mysql_real_escape_string($poruka);
			alog(0, $poruka, 'Nije ulogovan', fuckcloudflare());				
			
			$_SESSION['loginerror'] = TRUE;
			@$_SESSION['loginattempt']++;
			if (4 < $_SESSION['loginattempt'])
			{
				$_SESSION['lockout'] = time();
				$_SESSION['loginattempt'] = 0;
				$poruka = "Banovan na 1 sat zbog 5 pogrešnih logina.";
				alog(0, $poruka, 'Nije ulogovan', fuckcloudflare());
			}
			header( "Location: login.php" );
			die();
		break;
	}
?>