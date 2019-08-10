<?php
session_start();

include("konfiguracija.php");
include("includes.php");
require_once '../inc/libs/GameQ.php';

$naslov = "Server - WebFtp";
$fajl = "srv-webftp";
$srv = "1";
$a='a';

if(logged_in()) {
	
} else {
	header("Location: ./login");
	die();
}

if(empty($_GET['id']) or !is_numeric($_GET['id'])) 
{
	header("Location: index.php");
	die();
}

$serverid = mysql_real_escape_string($_GET['id']);

if(query_numrows("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'") == 0)
{
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Taj server ne postoji.";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

if(isset($_GET['path']))
{
	$lokacija = sqli($_GET['path']);
}

$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$serverid."'");
$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `ipid` = '".$server['ip_id']."'");
$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$server['box_id']."'");
$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$server['user_id']."'");

if(CheckBoxStatus($serverid) == "Offline") {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Mašina je OFFLINE!";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}

if($server['igra'] == "6") { header("Location:ts-pocetna.php?id=$serverid"); }
$ip = $boxip['ip'];

if(isset($_GET["path"]))
{
	$path = $_GET["path"];
	$back_link = dirname( $path );

	$ftp_path = substr($path, 1);
	$breadcrumbs = preg_split('/[\/]+/', $ftp_path, 9);	
	$breadcrumbs = str_replace("/", "", $breadcrumbs);

	$ftp_pth = '';
	if(($bsize = sizeof($breadcrumbs)) > 0) 
	{
		$sofar = '';
		for($bi=0;$bi<$bsize;$bi++) 
		{
			if($breadcrumbs[$bi])
			{
				$sofar = $sofar . $breadcrumbs[$bi] . '/';

				$ftp_pth .= '  <i class="icon-angle-right"></i>  <a style="color: #000;" href="srv-webftp.php?id='.$serverid.'&path=/'.$sofar.'"><i class="icon-folder-open"></i> '.$breadcrumbs[$bi].'</a>';
			}
		}
	}
}
else
{
	header("Location: srv-webftp.php?id=".$serverid."&path=/");
	die();
}

$ftp = ftp_connect($ip, $box['ftpport']);

if(!$ftp)
{
	$_SESSION['msg-type'] = "error";
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Ne mogu se konektovati na FTP servera!";
	header("Location: srv-pocetna.php?id=".$serverid);
	die();
}


if (@ftp_login($ftp, $server["username"], $server["password"]))
{
	ftp_pasv($ftp, true);
	if(!isset($_GET['fajl']))
	{
                ftp_pasv($ftp_id, "True");
		$ftp_chdir2 = ftp_chdir($ftp, $path);
		$ftp_contents = ftp_rawlist($ftp, $path);
		$i = "0";

                foreach ($ftp_contents as $folder)
		{
			$broj = $i++;	
			$current = preg_split("/[\s]+/",$folder,9);

			$isdir = ftp_size($ftp, $current[8]);
			if ( substr( $current[0][0], 0 - 1 ) == "l" )
			{
				$ext = explode(".", $current[8]);
				
				$xa = explode("->", $current[8]);
				
				$current[8] = $xa[0];
				
				$current[0] = "link";
				
				$current[4] = "link fajla";
				
				$ftp_fajl[]=$current;
			}
			else
			{
				if ( substr( $current[0][0], 0 - 1 ) == "d" ) $ftp_dir[]=$current;
				else 
				{
					$text = array( "txt", "cfg", "sma", "SMA", "CFG", "inf", "log", "rc", "ini", "yml", "json", "properties" );
					$ext = explode(".", $current[8]);
					if($ext[2] == "conf") $current[9] = $ext[1];
					else if(!empty($ext[1])) if (in_array( $ext[1], $text )) $current[9] = $ext[1];
					
					$ftp_fajl[]=$current;
				}
			}	
		}
    }
	else
	{
		$filename = "ftp://$server[username]:$server[password]@$ip:21".$lokacija."/$_GET[fajl]";
		$contents = file_get_contents($filename);
	}  
	if(isset($_GET["path"])) {
		$old_path = ''.$_GET["path"].'/';
		$old_path = str_replace('//', '/', $old_path);
	}	
}
else 
{
	$_SESSION['msg-type'] = "error";
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Pogrešne FTP podatke!".$server["username"].' - '.$server["password"];
	header("Location: srv-pocetna.php?id=".$serverid);
	die();
}

ftp_close($ftp);

include("assets/header.php");
?>

<div class="row">
	<div class="span12">
		<div class="widget stacked widget-table action-table">
					
			<div class="widget-header">
				<i class="icon-th-list"></i>
				<h3>WebFTP</h3>
			</div>
				
			<div class="widget-content">		
<?php
				if(isset($_GET["path"])) {
?>
				<div id="infox">
					<i class="icon-comment"></i>
					<p id="h5">WebFTP</p><br />
					<p>Pristup vašim fajlovima bez odlaska na FTP.</p><br />
					<p style="margin-top: -3px;">Menjajte fajlove, brišite i dodajte nove.</p>
				</div>

				<a class="btn btn-small btn-warning" data-toggle="modal" href="#modal-folderadd" style="color: #FFF; float: right; margin-top: -43px; margin-right: 5px;" type="button"><i class="icon-credit-card"></i> Novi folder</a>
				<form action="process.php" method="post" enctype="multipart/form-data" style="float: right; margin-top: -5px; margin-right: 5px;">
					<input type="hidden" name="task" value="uploadfajla" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<input type="hidden" name="lokacija" value="<?php echo $lokacija; ?>" />
					
					<input type="file" name="file" id="file" style="background: rgba(0,0,0,0.5); border: 1px solid rgb(97, 33, 48);">
					<button type="submit" class="btn btn-small btn-danger">Upload</button>
				</form>
				<br />
				<div id="paginacija">
					<a style="color: #000;" href="srv-webftp.php?id=<?php echo $serverid; ?>"><i class="icon-home"></i> root</a>
					<?php echo $ftp_pth; if(isset($_GET['fajl'])) { ?>  <i class="icon-angle-right"></i>  <i class="icon-file"></i> <?php echo sqli($_GET['fajl']); } ?>
				</div>
<?php
				} else {
?>
				<div id="paginacija">
					<a style="color: #000;" href="srv-webftp.php?id=<?php echo $serverid; ?>"><i class="icon-home"></i> root</a>
					<?php if(isset($_GET['fajl'])) { ?>  <i class="icon-angle-right"></i>  <i class="icon-file"></i> <?php echo sqli($_GET['fajl']); } ?>
				</div>
<?php
				} if(!isset($_GET['fajl'])) {
?>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Ime fajla/foldera</th>
							<th>Velicina</th>
							<th>User</th>
							<th>Grupa</th>
							<th>Permisije</th>
							<th>Modifikovan</th>
							<th>Akcija</th>
						</tr>
					</thead>
					<tbody>
<?php
					$back_link = str_replace("\\", '/', $back_link);
					if($path != "/")
					{
?>		
						<tr>
							<td colspan="7" style="cursor: pointer;" onClick="window.location='?id=<?php echo $serverid; ?><?php if ($back_link != "/") { ?>&path=<?php echo $back_link; } ?>'">
							<z><i class="icon-arrow-left"></i></z>  ...
							</td>
						</tr>
<?php
					}
					if(!empty($ftp_dir))
					{
						foreach($ftp_dir as $x)
						{
?>		
							<tr style="vertical-align: top;">
								<td>
									<a style="color: #000;" href="srv-webftp.php?id=<?php echo $serverid; ?>&path=<?php echo $old_path."".$x[8]; ?>">
										<i class='icon-folder-open' style="color: rgb(255, 153, 0);"></i>
						<?php
										echo $x[8];
						?>
									</a>
								</td>	
								<td>-</td>
								<td>
								<?php echo $x[2]; ?>
								</td>
								<td>
								<?php echo $x[3]; ?>
								</td>
								<td>
								<?php echo $x[0]; ?>
								</td>
								<td>
								<?php echo $x[5].' '.$x[6].' '.$x[7]; ?>
								</td>		
								<td style="padding: 0;">
									<form method="POST" action="process.php" id="izbrisi-folder" style="float: right;">
										<a data-toggle="modal" href="#modal-folderdel" onclick='imefoldera("<?php echo $x[8]; ?>");'>
											<button id="iconweb"><i class="icon-remove"></i></button>
										</a>
									</form>
									<form method="POST" action="serverprocess.php" id="izbrisi-fajl" style="float: right;">
										<a data-toggle="modal" href="#modal-ftprename" onclick='imeftpf("<?php echo $x[8]; ?>");'>
											<button id="iconweb"><i class="icon-edit"></i></button>
										</a>
									</form>			
								</td>
							</tr>	
<?php
						}
					}
					if(!empty($ftp_fajl))
					{
						foreach($ftp_fajl as $x)
						{
				?>
						<tr>
							<td>
				<?php
							if(isset($x[9]))
							{
				?>
							<a href="srv-webftp.php?id=<?php echo $serverid; ?>&path=<?php echo $old_path; ?>&fajl=<?php echo $x[8]; ?>">
								<i class='icon-file-text'></i>
				<?php
								echo $x[8];
				?>
							</a>
				<?php
							}
							else
							{
				?>
								<i class='icon-file'></i>
				<?php
								echo $x[8];
				?>
				<?php		
							}
				?>
							</td>
							<td>
				<?php

							if($x[4] == "link fajla") echo $x[4];
							else {			
								if($x[4] < 1024) echo $x[4]." byte";
								else if($x[4] < 1048576) echo round(($x[4]/1024), 0)." KB";
								else echo round(($x[4]/1024/1024), 0)." MB";
							}
				?>
							</td>
							<td>
							<?php echo $x[2]; ?>
							</td>
							<td>
							<?php echo $x[3]; ?>
							</td>
							<td>
							<?php echo $x[0]; ?>
							</td>
							<td>
							<?php echo $x[5].' '.$x[6].' '.$x[7]; ?>
							</td>
							<td style="padding: 0;">
								<form method="POST" action="process.php" id="izbrisi-fajl" style="float: right;">
									<a data-toggle="modal" href="#modal-fajldel" onclick='imefajla("<?php echo $x[8]; ?>");'>
										<button id="iconweb"><i class="icon-remove"></i></button>
									</a>
								</form>
								<form method="POST" action="serverprocess.php" id="izbrisi-fajl" style="float: right;">
									<a data-toggle="modal" href="#modal-ftprename" onclick='imeftpf("<?php echo $x[8]; ?>");'>
										<button id="iconweb"><i class="icon-edit"></i></button>
									</a>
								</form>			
							</td>
						</tr>
				<?php
						}
					}	
				?>
				</table>
<?php
				}
				else
				{
?>
				<div id="bsve">
					<span style="font-size:20px;">
						<i class="icon-file"></i> <?php echo sqli($_GET['fajl']); ?>
					</span>
					<form action="process.php" id="spremanje_fajla" method="POST">
						<input type="hidden" name="task" value="spremanjefajla" />
						<input type="hidden" name="fajl2" value="<?php echo sqli($_GET['fajl']); ?>" />
						<input type="hidden" name="lokacija" value="<?php echo $lokacija; ?>" />
						<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
						<textarea rows="7" id="fajledit" name="tekstf" height="auto"><?php echo htmlspecialchars($contents); ?></textarea><br />
						<button type="submit" class="btn btn-primary"><i class="icon-arrow-right"></i> Sačuvaj</button>
					</form>		
				</div>
<?php
				}
?>	
					
			</div>
			
		</div>
	</div>	
</div>

<?php
include("assets/footer.php");
?>