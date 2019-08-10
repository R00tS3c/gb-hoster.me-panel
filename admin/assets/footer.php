
	</div> <!-- /container -->
</div> <!-- /main -->
<div class="push"></div>
</div>

<div id="footera">

</div>
<div class="extra">
		<div class="extra-inner">
			<div class="container">
				<div class="row">
					<div class="span12">
						<center>
							<img src="/admin/assets/img/glogo.png" alt="gb-hoster.me LOGO!">
						</center>
					</div>
				</div>
			</div>
		</div>
	</div>
	
<div id="footerc">

	<div class="extra">

		<div class="container">
		Stranica učitana za: <m><?php
	$mtime = explode(' ', microtime());
	$totaltime = $mtime[0] + $mtime[1] - $starttime;
	printf('%.3f sekundi.', $totaltime);

	?></m>

		</div> <!-- /container -->

	</div> <!-- /extra -->


		
		
	<div class="footer">
			
		<div class="container">
			
			<div class="row">
				
				<div id="footer-copyright" class="span6">
					&copy; <m>GB HOSTER</m> SOLUTION All rights reserved.
				</div> <!-- /span6 -->
				
				<div id="footer-terms" class="span6">
					<i class="icon-html5 icon-2x"></i><span style="margin-left: 10px;">Design by <a href="http://www.facebook.com/Jasarevic" target="_blank">Semir Jašarević</a></span>
				</div> <!-- /.span6 -->
				
			</div> <!-- /row -->
			
		</div> <!-- /container -->
		
	</div> <!-- /footer -->

</div> <!-- #footerc end -->

<!-- Modal -->



<div id="ban_klijenta" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Ban klijenta sa tikete</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="ban_klijenta" />
<?php	
			if(!empty($tiket_id) AND $fajl == "tiket") { 
				$id = query_fetch_assoc("SELECT * FROM `tiketi` WHERE `id` = '{$tiket_id}'");
				$id = $id['user_id'];
?>
			<input type="hidden" name="klijentid" value="<?php echo $id; ?>" />
<?php
				unset($id);
			} 
			
?>
			<div class="control-group">
				<label for="pernament">Vrsta bana</label>
				<select class="span5" name="pernament" id="trajni_ban">
					<option value="0" disabled selected="selected">- Izaberite -</option>
					<option value="1">Vremenski ban</option>
					<option value="2">Trajni ban</option>
				</select>
			</div>	

			<div class="control-group">
				<label for="datum">Datum</label>
				<div class="controls">
					<input disabled name="datum" class="span5" type="text" id="datum_bana" placeholder="Trajanje bana">
				</div>
			</div>	

			<div class="control-group">
				<label for="datum">Razlog</label>
				<div class="controls">
					<textarea rows="3" name="razlog" class="span5" type="text" placeholder="Razlog bana..."></textarea>
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="ban_klijenta_gp" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Ban klijenta</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="ban_klijenta" />
			<input type="hidden" name="klijentid" value="<?php echo $id; ?>" />
			<div class="control-group">
				<label for="pernament">Vrsta bana</label>
				<select class="span5" name="pernament" id="trajni_ban_gp">
					<option value="0" disabled selected="selected">- Izaberite -</option>
					<option value="1">Vremenski ban</option>
					<option value="2">Trajni ban</option>
				</select>
			</div>	

			<div class="control-group">
				<label for="datum">Datum</label>
				<div class="controls">
					<input disabled name="datum" class="span5" type="text" id="datum_bana_gp" placeholder="Trajanje bana">
				</div>
			</div>	

			<div class="control-group">
				<label for="datum">Razlog</label>
				<div class="controls">
					<textarea rows="3" name="razlog" class="span5" type="text" placeholder="Razlog bana..."></textarea>
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="prosl_tiket" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Prosledite tiket</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="prosl_tiket" />
		<?php if(isset($_GET['id'])) { $tid = $_GET['id']; ?>
			<input type="hidden" name="tiket" value="<?php echo $tid; ?>" />
		<?php } ?>

			<div class="control-group">
				<label for="pernament">Admin</label>
				<select class="span5" name="admin">
				<?php 
					$admini = mysql_query("SELECT * FROM `admin`");
					while($row = mysql_fetch_assoc($admini)) { ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></option>
				<?php } 
					unset($admini);
				?>
				</select>
			</div>					
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Prosledi</button>
			</div>
		</form>
</div>

<div id="modal-folderadd" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj novi folder</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post">
			<input type="hidden" name="task" value="folderadd" />
<?php
			if(isset($_GET['id'])) {
?>
			<input type="hidden" name="serverid" value="<?php echo $_GET['id']; ?>" />
<?php
			}
			if(isset($_GET['path'])) {
?>
			<input type="hidden" name="lokacija" value="<?php echo $_GET['path']; ?>" />
<?php
			}
?>		
			<div class="control-group">
				<label for="iskljucen">Ime foldera</label>
				<div class="controls">
					<input name="folder" class="span5" type="text" placeholder="Ime foldera" />
				</div>
			</div>						
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="modal-ftprename" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Promeni ime foldera/fajla</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post">
			<input type="hidden" name="task" value="ftprename" />
<?php
			if(isset($_GET['id'])) {
?>
			<input type="hidden" name="serverid" value="<?php echo $_GET['id']; ?>" />
<?php
			}
			if(isset($_GET['path'])) {
?>
			<input type="hidden" name="lokacija" value="<?php echo $_GET['path']; ?>" />
<?php
			}
?>
			<input type="hidden" name="imeftp" id="imeftps" value="" />
			<div class="control-group">
				<label for="iskljucen">Novo ime foldera/fajla</label>
				<div class="controls">
					<input name="imesf" class="span5 sah" type="text" value="" />
				</div>
			</div>						
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Promeni</button>
			</div>
		</form>
</div>

<div id="modal-fajldel" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Brisanje fajla</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post">
			<input type="hidden" name="task" value="fajldel" />
<?php
			if(isset($_GET['id'])) {
?>
			<input type="hidden" name="serverid" value="<?php echo $_GET['id']; ?>" />
<?php
			}
			if(isset($_GET['path'])) {
?>
			<input type="hidden" name="lokacija" value="<?php echo $_GET['path']; ?>" />
<?php
			}
?>
			<input type="hidden" name="folder" id="ime_fajla" value="" />
			<div class="control-group">
				<label for="iskljucen">Da li ste sigurni da želite izbrisati fajl?</label>
			</div>						
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Ne</button>
				<button class="btn btn-primary">Da</button>
			</div>
		</form>
</div>

<div id="modal-folderdel" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Brisanje foldera</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post">
			<input type="hidden" name="task" value="folderdel" />
<?php
			if(isset($_GET['id'])) {
?>
			<input type="hidden" name="serverid" value="<?php echo $_GET['id']; ?>" />
<?php
			}
			if(isset($_GET['path'])) {
?>
			<input type="hidden" name="lokacija" value="<?php echo $_GET['path']; ?>" />
<?php
			}
?>
			<input type="hidden" name="folder" id="ime_foldera" value="" />
			<div class="control-group">
				<label for="iskljucen">Da li ste sigurni da želite izbrisati folder?</label>
			</div>						
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Ne</button>
				<button class="btn btn-primary">Da</button>
			</div>
		</form>
</div>

<div id="glavnapodesavanja" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Glavna podesavanja</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
<?php
			$iskljucen = query_fetch_assoc("SELECT `value` FROM `config` WHERE `setting` = 'iskljucen'");
			$verzija = query_fetch_assoc("SELECT `value` FROM `config` WHERE `setting` = 'verzija'");
			$reg = query_fetch_assoc("SELECT `value` FROM `config` WHERE `setting` = 'reg'");
			$log = query_fetch_assoc("SELECT `value` FROM `config` WHERE `setting` = 'log'");
?>
			<input type="hidden" name="task" value="gl_podes" />
			<div class="control-group">
				<label for="iskljucen">Iskljucen klijent panel</label>
				<select class="span5" name="iskljucen">
					<option value="0"<?php if($iskljucen['value'] == "0") echo ' selected="selected"'; ?>>Ne</option>
					<option value="1"<?php if($iskljucen['value'] == "1") echo ' selected="selected"'; ?>>Da</option>
				</select>
			</div>	
			<div class="control-group">
				<label for="reg">Iskljuci registraciju</label>
				<select class="span5" name="reg">
					<option value="0"<?php if($reg['value'] == "0") echo ' selected="selected"'; ?>>Ne</option>
					<option value="1"<?php if($reg['value'] == "1") echo ' selected="selected"'; ?>>Da</option>
				</select>
			</div>	
			<div class="control-group">
				<label for="log">Iskljuci login</label>
				<select class="span5" name="log">
					<option value="0"<?php if($log['value'] == "0") echo ' selected="selected"'; ?>>Ne</option>
					<option value="1"<?php if($log['value'] == "1") echo ' selected="selected"'; ?>>Da</option>
				</select>
			</div>				
			<div class="control-group">
				<label for="iskljucen">Verzija</label>
				<div class="controls">
					<input name="verzija" class="span5" type="text" value="<?php echo $verzija['value']; ?>" />
				</div>
			</div>			
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="klijentedit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Promeni klijenta</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" id="klijentedit-validate" enctype="multipart/form-data">
			<input type="hidden" name="task" value="klijent_edit" />

<?php
			if(isset($_GET['id']))
			{
				$id = mysql_real_escape_string($_GET['id']);
				$klijentf = mysql_fetch_array(mysql_query("SELECT * FROM `klijenti` WHERE `klijentid` = '".$id."'"));
?>				
			
			<input type="hidden" name="klijentid" value="<?php echo $id; ?>" />
			<div class="control-group">
				<label for="username">Username</label>
				<div class="controls">
					<input name="username" class="span5" type="text" value="<?php echo $klijentf['username']; ?>">
				</div>
			</div>	
			
			<div class="control-group">
				<label for="ime">Ime i prezime</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" value="<?php echo $klijentf['ime'].' '.$klijentf['prezime']; ?>">
				</div>
			</div>	

			<div class="control-group">
				<label for="email">E-mail</label>
				<div class="controls">
					<input name="email" class="span5" id="email" type="text" value="<?php echo $klijentf['email']; ?>">
				</div>
			</div>	

			<div class="control-group">
				<label for="zemlja">Država</label>
				<select class="span5" name="zemlja" id="trajni_ban">
					<option value="srb" <?php if($klijentf['zemlja'] == "srb") echo'selected="selected"'; ?>>Srbija</option>
					<option value="cg" <?php if($klijentf['zemlja'] == "cg") echo'selected="selected"'; ?>>Crna gora</option>
					<option value="bih" <?php if($klijentf['zemlja'] == "bih") echo'selected="selected"'; ?>>Bosna i Hercegovina</option>
					<option value="hr" <?php if($klijentf['zemlja'] == "hr") echo'selected="selected"'; ?>>Hrvatska</option>
					<option value="mk" <?php if($klijentf['zemlja'] == "mk") echo'selected="selected"'; ?>>Makedonija</option>
				</select>
			</div>

			<div class="control-group">
				<label for="password">Šifra</label>
				<div class="controls">
					<input name="password" class="span5" type="password" placeholder="Ostavi prazno polje za preskakanje ovog koraka...">
				</div>
			</div>

			<div class="control-group">
				<label for="password">Novac - Upisuj u EUR</label>
				<div class="controls">
					<input name="novac" class="span5" type="text" value="<?php echo $klijentf['novac']; ?>">
				</div>
			</div>			
			
			<?php if(vlasnik($_SESSION['a_id'])) { ?>
			<div class="control-group">
				<label for="password">Sigurnosni kod</label>
				<div class="controls">
					<input type="text" name="sigkod" class="span5" value="<?php echo $klijentf['sigkod']; ?>">
				</div>
			</div>
<?php
			}
			}
?>	
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Promeni</button>
			</div>
		</form>
</div>


<div id="adminedit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Promeni admina</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" id="adminedit-validate" enctype="multipart/form-data">
			<input type="hidden" name="task" value="admin_edit" />

<?php
			if(isset($_GET['id']))
			{
				$id = mysql_real_escape_string($_GET['id']);
				$klijentf = mysql_fetch_array(mysql_query("SELECT * FROM `admin` WHERE `id` = '".$id."'"));
?>				
			
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<div class="control-group">
				<label for="username">Username</label>
				<div class="controls">
					<input name="username" class="span5" type="text" value="<?php echo $klijentf['username']; ?>">
				</div>
			</div>	
			
			<div class="control-group">
				<label for="ime">Ime i prezime</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" value="<?php echo $klijentf['fname'].' '.$klijentf['lname']; ?>">
				</div>
			</div>	

			<div class="control-group">
				<label for="email">E-mail</label>
				<div class="controls">
					<input name="email" class="span5" id="email" type="text" value="<?php echo $klijentf['email']; ?>">
				</div>
			</div>	

			<div class="control-group">
				<label for="zemlja">Rank</label>
				<select class="span5" name="rank">
					<option value="admin" <?php if($klijentf['status'] == "admin") echo'selected="selected"'; ?>>Admin</option>
					<option value="support" <?php if($klijentf['status'] == "support") echo'selected="selected"'; ?>>Radnik</option>
				</select>
			</div>

			<div class="control-group">
				<label for="password">Šifra</label>
				<div class="controls">
					<input name="password" class="span5" type="password" placeholder="Ostavi prazno polje za preskakanje ovog koraka...">
				</div>
			</div>	
<?php
			}
			else if(isset($_SESSION['a_id']))
			{
				$id = $_SESSION['a_id'];
				$klijentf = mysql_fetch_array(mysql_query("SELECT * FROM `admin` WHERE `id` = '".$id."'"));
?>				
			
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<div class="control-group">
				<label for="username">Username</label>
				<div class="controls">
					<input name="username" class="span5" type="text" value="<?php echo $klijentf['username']; ?>">
				</div>
			</div>	
			
			<div class="control-group">
				<label for="ime">Ime i prezime</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" value="<?php echo $klijentf['fname'].' '.$klijentf['lname']; ?>">
				</div>
			</div>	

			<div class="control-group">
				<label for="email">E-mail</label>
				<div class="controls">
					<input name="email" class="span5" id="email" type="text" value="<?php echo $klijentf['email']; ?>">
				</div>
			</div>	

			<div class="control-group">
				<label for="zemlja">Rank</label>
				<select class="span5" name="rank">
					<option value="admin" <?php if($klijentf['status'] == "admin") echo'selected="selected"'; ?>>Admin</option>
					<option value="support" <?php if($klijentf['status'] == "support") echo'selected="selected"'; ?>>Radnik</option>
				</select>
			</div>

			<div class="control-group">
				<label for="password">Šifra</label>
				<div class="controls">
					<input name="password" class="span5" type="password" placeholder="Ostavi prazno polje za preskakanje ovog koraka...">
				</div>
			</div>
<?php
			}			
?>			
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Promeni</button>
			</div>
		</form>
</div>

<div id="klijentadd" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj klijenta</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" id="klijentadd-validate" enctype="multipart/form-data">
			<input type="hidden" name="task" value="klijent_add" />
			<div class="control-group">
				<label for="username">Username</label>
				<div class="controls">
					<input name="username" class="span5" type="text" placeholder="Username...">
				</div>
			</div>	
			
			<div class="control-group">
				<label for="ime">Ime i prezime</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" placeholder="Ime i prezime...">
				</div>
			</div>	

			<div class="control-group">
				<label for="email">E-mail</label>
				<div class="controls">
					<input name="email" class="span5" id="email" type="text" placeholder="E-mail adresa...">
				</div>
			</div>	

			<div class="control-group">
				<label for="zemlja">Država</label>
				<select class="span5" name="zemlja" id="trajni_ban">
					<option value="srb">Srbija</option>
					<option value="cg">Crna gora</option>
					<option value="bih">Bosna i Hercegovina</option>
					<option value="hr">Hrvatska</option>
					<option value="mk">Makedonija</option>
				</select>
			</div>

			<div class="control-group">
				<label for="password">Šifra</label>
				<div class="controls">
					<input name="password" class="span5" type="password" placeholder="Šifra...">
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="adminadd" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj admina</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" id="adminadd-validate" enctype="multipart/form-data">
			<input type="hidden" name="task" value="admin_add" />
			<div class="control-group">
				<label for="username">Username</label>
				<div class="controls">
					<input name="username" class="span5" type="text" placeholder="Username...">
				</div>
			</div>	
			
			<div class="control-group">
				<label for="ime">Ime i prezime</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" placeholder="Ime i prezime...">
				</div>
			</div>	

			<div class="control-group">
				<label for="email">E-mail</label>
				<div class="controls">
					<input name="email" class="span5" id="email" type="text" placeholder="E-mail adresa...">
				</div>
			</div>	

			<div class="control-group">
				<label for="password">Šifra</label>
				<div class="controls">
					<input name="password" class="span5" type="password" placeholder="Šifra...">
				</div>
			</div>	

			<div class="control-group">
				<label for="rank">Rank</label>
				<select class="span5" name="rank">
					<option value="admin">Admin</option>
					<option value="support">Radnik</option>
				</select>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="dodajMasinu" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj masinu</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" id="validation-form" enctype="multipart/form-data">
			<input type="hidden" name="task" value="addmasina" />
			<div class="control-group">
				<label for="ipmas">Ip masine</label>
				<div class="controls">
					<input name="ipmas" class="span5" type="text" placeholder="192.168.1.1">
				</div>
			</div>
			
			<div class="control-group">
				<label for="lok">Lokacija</label>
				<select class="span5" name="lok">
					<option value="Lite">Lite - Nemačka</option>
					<option value="Premium">Premium - Srbija</option>
				</select>
			</div>
			
			<div class="control-group">
				<label for="datacentar">Datacentar</label>
				<div class="controls">
					<input name="datacentar" class="span5" type="text" placeholder="Server4you">
				</div>
			</div>	

			<div class="control-group">
				<label for="ssh2port">SSH2 Port</label>
				<div class="controls">
					<input name="ssh2port" class="span5" type="text" placeholder="22">

				</div>
			</div>	

			<div class="control-group">
				<label for="root">Root login</label>
				<div class="controls">
					<input name="root" class="span5" type="text" placeholder="root">
				</div>
			</div>	

			<div class="control-group">
				<label for="pw">Sifra</label>
				<div class="controls">
					<input name="pw" class="span5" type="password" placeholder="password">
				</div>
			</div>
			
			<div class="control-group">
				<label for="fdl">FastDL Status</label>
				<select class="span5" name="fdl">
					<option value="0">Offline</option>
					<option value="1">Online</option>
				</select>
			</div>
			
			<div class="control-group">
				<label for="fdl_link">FastDL Link (Potrebno ukoliko je FastDL Status = Online)</label>
				<div class="controls">
					<input name="fdl_link" class="span5" type="text" placeholder="FastDL Link">
				</div>
			</div>
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>


<?php 
if(isset($fajlx))
{
if($fajlx == "box") {
$box = query_fetch_assoc("SELECT * FROM `box` WHERE `boxid` = '".$_GET['id']."'");
$boxip = query_fetch_assoc("SELECT * FROM `boxip` WHERE `boxid` = '".$_GET['id']."'");
?>
<div id="editMasinu" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Edit masinu</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" id="validation-form" enctype="multipart/form-data">
			<input type="hidden" name="task" value="editmasina" />
			<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
			<div class="control-group">
				<label for="ipmas">Ip masine</label>
				<div class="controls">
					<input name="ipmas" class="span5" type="text" value="<?php echo $box['ip']; ?>">
				</div>
			</div>

			<div class="control-group">
				<label for="datacentar">Datacentar</label>
				<div class="controls">
					<input name="datacentar" class="span5" type="text" value="<?php echo $box['name']; ?>">
				</div>
			</div>	

			<div class="control-group">
				<label for="ssh2port">SSH2 Port</label>
				<div class="controls">
					<input name="ssh2port" class="span5" type="text" value="Na ti kurac!">

				</div>
			</div>	

			<div class="control-group">
				<label for="root">Root login</label>
				<div class="controls">
					<input name="root" class="span5" type="text" value="<?php echo $box['login']; ?>">
				</div>
			</div>
			
			<div class="control-group">
				<label for="root">Maksimalno servera</label>
				<div class="controls">
					<input name="maxsrv" class="span5" type="text" maxsize="2" value="<?php echo $box['maxsrv']; ?>">
				</div>
			</div>				

			<div class="control-group">
				<label for="pw">Sifra</label>
				<div class="controls">
					<input name="pw" class="span5" type="password" placeholder="Ostavi prazno polje da ostane ista sifra" />
				</div>
			</div>
			<div class="control-group">
				<label for="fdl">FastDL Status</label>
				<select class="span5" name="fdl">
					<?php
					if($box['fdl'] == "1") {
						echo "<option value='1'>Online</option>
						<option value='0'>Offline</option>";
					} else {
						echo "<option value='0'>Offline</option>
						<option value='1'>Online</option>";
					}
					?>
				</select>
			</div>
			
			<div class="control-group">
				<label for="fdl_link">FastDL Link (Potrebno ukoliko je FastDL Status = Online)</label>
				<div class="controls">
					<input name="fdl_link" class="span5" type="text" value="<?php echo $box['fdl_link']; ?>" >
				</div>
			</div>
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Sačuvaj</button>
			</div>
		</form>
</div>
<?php
}
} ?>

<div id="dodajobavestenje" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj obavestenje</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="addobavestenje" />
			<div class="control-group">
				<label for="naslov">Naslov</label>
				<div class="controls">
					<input name="naslov" class="span5" type="text" placeholder="Naslov obavestenja">
				</div>
			</div>
			
			<div class="control-group">
				<label for="vrsta">Vrsta</label>
				<select class="span5" name="vrsta">
					<option value="1">Za klijente (Panel)</option>
					<option value="2" disabled="disabled">Za sve (Sajt)</option>
				</select>
			</div>	

			<div class="control-group">
				<label for="tekst">Obavestenje</label>
				<div class="controls">
					<textarea rows="5" name="tekst" class="span5" type="text" placeholder="Obavestenje tekst"></textarea>
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="dodaj-slajd" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj slajd</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="dodaj_slajd" />
			<div class="control-group">
				<label for="naslov">Naslov</label>
				<div class="controls">
					<input name="naslov" class="span5" type="text" placeholder="Naslov">
				</div>
			</div>
			
			<div class="control-group">
				<label for="tekst">Text</label>
				<div class="controls">
					<textarea rows="5" name="tekst" class="span5" type="text" placeholder="Text\nText"></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label for="slika">Slika</label>
				<div class="controls">
					<input name="slika" class="span5" type="text" placeholder="/assets/img/slajd/x.png">
				</div>
			</div>	
				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="edit_obavestenje" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Edit obavestenje</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="editobavestenje" />
			<input type="hidden" class="id_ob" name="id" value="" />
			
			<div class="control-group">
				<label for="naslov">Naslov</label>
				<div class="controls">
					<input id="naslovo" name="naslov" class="span5" type="text" value="">
				</div>
			</div>
			
			<div class="control-group">
				<label for="vrsta">Vrsta</label>
				<select class="span5" name="vrsta">
					<option value="1">Za klijente (Panel)</option>
					<option value="2" disabled="disabled">Za sve (Sajt)</option>
				</select>
			</div>	

			<div class="control-group">
				<label for="tekst">Obavestenje</label>
				<div class="controls">
					<textarea id="texto" rows="5" name="tekst" class="span5" type="text" value=""></textarea>
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Sačuvaj</button>
			</div>
		</form>
</div>

<div id="edit_slajd" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Edit slajd</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="edit_slajd" />
			<input type="hidden" class="id_sl" name="id" value="" />
			
			<div class="control-group">
				<label for="naslov">Naslov</label>
				<div class="controls">
					<input id="naslovs" name="naslov" class="span5" type="text" value="">
				</div>
			</div>
			
			<div class="control-group">
				<label for="tekst">Text</label>
				<div class="controls">
					<textarea id="texts" rows="5" name="tekst" class="span5" type="text" value=""></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label for="naslov">Slika</label>
				<div class="controls">
					<input id="slikas" name="slika" class="span5" type="text" value="">
				</div>
			</div>
			
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Sačuvaj</button>
			</div>
		</form>
</div>


<div id="editobavestenje" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
<?php
	$row = mysql_fetch_array(mysql_query("SELECT * FROM `obavestenja` WHERE `vrsta` = '2'"));
?>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Edituj obavestenje</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="editobavestenje" />
			<div class="control-group">
				<label for="naslov">Naslov</label>
				<div class="controls">
					<input name="naslov" class="span5" type="text" value="<?php echo $row['naslov']; ?>">
				</div>
			</div>

			<div class="control-group">
				<label for="tekst">Obavestenje</label>
				<div class="controls">
					<textarea rows="5" name="tekst" class="span5" type="text"><?php echo $row['poruka']; ?></textarea>
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="srvrcon" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Rcon komanda</h3>
	</div>
	<div class="modal-body">
		<form action="serverprocess.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="rcon" />
<?php		if(isset($_GET['id'])) { ?>
			<input type="hidden" name="serverid" value="<?php echo mysql_real_escape_string($_GET['id']); ?>" />
<?php		}	?>
			<div class="control-group">
				<label for="naslov">Rcon komanda</label>
				<div class="controls">
					<input name="rcon" class="span5" type="text" placeholder="amx_map de_dust2">
				</div>
			</div>		
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Posalji</button>
			</div>
		</form>
</div>

<div id="srvmove" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Prebaci server</h3>
	</div>
	<div class="modal-body">
		<form action="serverprocess.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="prebacisrv" />
<?php		if(isset($_GET['id'])) { ?>
			<input type="hidden" name="serverid" value="<?php echo mysql_real_escape_string($_GET['id']); ?>" />
<?php		}	?>
			<div class="control-group">
				<label for="naslov">E-mail novog korisnika</label>
				<div class="controls">
					<input name="email" class="span5" type="text" placeholder="email@email.com">
				</div>
			</div>		
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Posalji</button>
			</div>
		</form>
</div>

<div id="server_add" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true" style="width: 620px;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj server</h3>
	</div>
	<div class="modal-body">
		<form action="serverprocess.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="server_add" />
			<table>
				<tr>
					<th style="width: 50%;"></th>
					<th style="width: 50%;"></th>
				</tr>
				<tr>
					<td>
						<div class="control-group">
							<label for="klijent">Klijent</label>
							<div class="controls">
								<select name="klijent" class="span4">
<?php
								$klijenti = mysql_query("SELECT * FROM `klijenti` ORDER BY `ime`");
								while($row = mysql_fetch_array($klijenti)) {
?>
									<option value="<?php echo $row['klijentid']; ?>"><?php echo $row['ime'].' '.$row['prezime'].' - '.$row['email']; ?></option>
<?php
								}
?>
								</select>
							</div>
						</div>						
					</td>
					<td>
						<div class="control-group">
							<label for="klijent">Klijent</label>
							<div class="controls">
								<select name="klijent" class="span4">
<?php
								$klijenti = mysql_query("SELECT * FROM `klijenti` ORDER BY `ime`");
								while($row = mysql_fetch_array($klijenti)) {
?>
									<option value="<?php echo $row['klijentid']; ?>"><?php echo $row['ime'].' '.$row['prezime'].' - '.$row['email']; ?></option>
<?php
								}
?>
								</select>
							</div>
						</div>						
					</td>					
				</tr>
				<tr>
					<td>
						<div class="control-group">
							<label for="klijent">Klijent</label>
							<div class="controls">
								<select name="klijent" class="span4">
<?php
								$klijenti = mysql_query("SELECT * FROM `klijenti` ORDER BY `ime`");
								while($row = mysql_fetch_array($klijenti)) {
?>
									<option value="<?php echo $row['klijentid']; ?>"><?php echo $row['ime'].' '.$row['prezime'].' - '.$row['email']; ?></option>
<?php
								}
?>
								</select>
							</div>
						</div>						
					</td>
					<td>
						<div class="control-group">
							<label for="klijent">Klijent</label>
							<div class="controls">
								<select name="klijent" class="span4">
<?php
								$klijenti = mysql_query("SELECT * FROM `klijenti` ORDER BY `ime`");
								while($row = mysql_fetch_array($klijenti)) {
?>
									<option value="<?php echo $row['klijentid']; ?>"><?php echo $row['ime'].' '.$row['prezime'].' - '.$row['email']; ?></option>
<?php
								}
?>
								</select>
							</div>
						</div>						
					</td>					
				</tr>				
			</table>	
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Posalji</button>
			</div>
		</form>
</div>

<div id="pluginadd" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj plugin</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="pluginadd" />
			<div class="control-group">
				<label for="ime">Ime plugina</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" placeholder="Ime plugina">
				</div>
			</div>
			
			<div class="control-group">
				<label for="deskripcija">Deskripcija</label>
				<div class="controls">
					<textarea rows="2" name="deskripcija" class="span5" type="text" placeholder="Ovaj plugin sluzi tome i tome."></textarea>
				</div>
			</div>		

			<div class="control-group">
				<label for="skracenica">Prikaz</label>
				<div class="controls">
					<input name="skracenica" class="span5" type="text" placeholder="plugins-ime.ini">
				</div>
			</div>			

			<div class="control-group">
				<label for="text">Text</label>
				<div class="controls">
					<textarea rows="2" name="text" class="span5" type="text" placeholder="plugin.amxx
					
					plugin2.amxx"></textarea>
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="pluginedit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Edituj plugin</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="pluginedit" />
			<input type="hidden" class="id_pl" name="id" value="" />
			<div class="control-group">
				<label for="ime">Ime plugina</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" id="plime">
				</div>
			</div>
			
			<div class="control-group">
				<label for="deskripcija">Deskripcija</label>
				<div class="controls">
					<textarea rows="2" name="deskripcija" class="span5" type="text" id="pldesk"></textarea>
				</div>
			</div>		

			<div class="control-group">
				<label for="skracenica">Prikaz</label>
				<div class="controls">
					<input name="skracenica" class="span5" type="text" id="plprikaz">
				</div>
			</div>			

			<div class="control-group">
				<label for="text">Text</label>
				<div class="controls">
					<textarea rows="2" name="text" class="span5" type="text" id="pltext"></textarea>
				</div>
			</div>				
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Sacuvaj</button>
			</div>
		</form>
</div>

<div id="modadd" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Dodaj mod</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="modadd" />
			<div class="control-group">
				<label for="ime">Ime moda</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" placeholder="Ime plugina">
				</div>
			</div>
			
			<div class="control-group">
				<label for="igra">Igra</label>
				<div class="controls">
					<select name="igra">
						<option value="1">Counter Strike 1.6</option>
						<option value="2">San Andreas Multiplayer</option>
						<option value="3">Minecraft</option>						
						<option value="4" disabled>Call Of Duty 4</option>
						<option value="5" disabled>Multi Theft Auto</option>
						<option value="6">Team Speak 3</option>
						<option value="7">FastDL</option>
						<option value="8">Sinus Bot</option>
						<option value="9">FiveM</option>
					</select>	
				</div>
			</div>
			<!--
			<div class="control-group">
				<label for="putanja">Putanja (Vise nije potrebno!)</label>
				<div class="controls">
					<input name="putanja" class="span5" type="text" placeholder="/home/gamefiles/putanja">
				</div>
			</div>
			-->
			<div class="control-group">
				<label for="link">Mod Link (Bez /)</label>
				<div class="controls">
					<input name="link" class="span5" type="text" placeholder="mods.gb-hoster.me/Games/CounterStrike">
				</div>
			</div>
			
			<div class="control-group">
				<label for="zipname">Mod ZIP Name</label>
				<div class="controls">
					<input name="zipname" class="span5" type="text" placeholder="Public.zip">
				</div>
			</div>
			
			<div class="control-group">
				<label for="text">Opis moda</label>
				<div class="controls">
					<textarea rows="2" name="opis" class="span5" type="text" placeholder="Detaljan opis moda"></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label for="mapa">Default mapa</label>
				<div class="controls">
					<input name="mapa" class="span5" type="text" placeholder="de_mapa">
				</div>
			</div>				

			<div class="control-group">
				<label for="text">Komanda</label>
				<div class="controls">
					<textarea rows="2" name="komanda" class="span5" type="text" placeholder="Komanda"></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label for="sakriven">Sakriven</label>
				<div class="controls">
					<select name="sakriven">
						<option value="1">Da</option>
						<option value="0">Ne</option>
					</select>	
				</div>
			</div>				
			
			<div class="control-group">
				<label for="text">Lite cena po slotu</label>
				<div class="controls">
					<input name="csrb" type="text" class="span1" placeholder="SRB" /> <input name="ccg" type="text" class="span1" placeholder="CG" />
					<input name="cbih" type="text" class="span1" placeholder="BiH" /> <input name="chr" type="text" class="span1" placeholder="HR" />
					<input name="cmk" type="text" class="span1" placeholder="MK" /> 
				</div>
			</div>
			
			<div class="control-group">
				<label for="text">Premium cena po slotu</label>
				<div class="controls">
					<input name="csrb_premium" type="text" class="span1" placeholder="SRB" /> <input name="ccg_premium" type="text" class="span1" placeholder="CG" />
					<input name="cbih_premium" type="text" class="span1" placeholder="BiH" /> <input name="chr_premium" type="text" class="span1" placeholder="HR" />
					<input name="cmk_premium" type="text" class="span1" placeholder="MK" /> 
				</div>
			</div>
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>

<div id="mailtoall" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Pošalji mail</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="mailtoall" />
			<div class="control-group">
				<label for="subject">Naslov</label>
				<div class="controls">
					<input name="subject" class="span5" type="text" placeholder="Naslov maila">
				</div>
			</div>	
			<div class="control-group">
				<label for="option">Kome?</label>
				<div class="controls">
					<select name="option">
						<option value="1">Svim klijentima koji su aktivirani</option>
						<option value="2">Klijentima koji su trenutno online</option>
					</select>	
				</div>
			</div>		

			<div class="control-group">
				<label for="message">Message</label>
				<div class="controls">
					<textarea rows="3" name="message" class="span5" type="text" placeholder="Message, možete koristiti <b>Text</b>"></textarea>
				</div>
			</div>
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Pošalji</button>
			</div>
		</form>
</div>


<div id="modedit" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="label">Edituj mod</h3>
	</div>
	<div class="modal-body">
		<form action="process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="task" value="modedit" />
			<input type="hidden" class="mod_id" name="id" value="" />
			<input type="hidden" class="igra_id" name="proslaigra" value="" />
			<div class="control-group">
				<label for="ime">Ime moda</label>
				<div class="controls">
					<input name="ime" class="span5" type="text" id="modime">
				</div>
			</div>
			
			<div class="control-group">
				<label for="igra">Igra</label>
				<div class="controls">
					<select name="igra" id="modigra">
						<option value="1">Counter Strike 1.6</option>
						<option value="2">San Andreas Multiplayer</option>
						<option value="3">Minecraft</option>
						<option value="4" disabled>Call Of Duty 4</option>						
						<option value="5" disabled>Multi Theft Auto</option>
						<option value="6">Team Speak 3</option>
						<option value="7">FastDL</option>
						<option value="8">Sinus Bot</option>
						<option value="9">FiveM</option>
					</select>	
				</div>
			</div>
			<!--
			<div class="control-group">
				<label for="putanja">Putanja (Vise nije potrebno!)</label>
				<div class="controls">
					<input name="putanja" class="span5" type="text" id="modputanja">
				</div>
			</div>
			-->
			<div class="control-group">
				<label for="link">Mod Link (Bez /)</label>
				<div class="controls">
					<input name="link" class="span5" type="text" id="modlink">
				</div>
			</div>
			
			<div class="control-group">
				<label for="zipname">Mod ZIP Name</label>
				<div class="controls">
					<input name="zipname" class="span5" type="text" id="modzipname">
				</div>
			</div>
			
			<div class="control-group">
				<label for="text">Opis moda</label>
				<div class="controls">
					<textarea rows="2" name="opis" class="span5" type="text" id="modopis"></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label for="mapa">Default mapa</label>
				<div class="controls">
					<input name="mapa" class="span5" type="text" id="modmapa">
				</div>
			</div>				

			<div class="control-group">
				<label for="text">Komanda</label>
				<div class="controls">
					<textarea rows="2" name="komanda" class="span5" type="text" id="modkomanda"></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label for="sakriven">Sakriven</label>
				<div class="controls">
					<select name="sakriven" id="modsakriven">
						<option value="1">Da</option>
						<option value="0">Ne</option>
					</select>	
				</div>
			</div>
			
			<div class="control-group">
				<label for="text">Lite cena po slotu</label>
				<div class="controls">
					<input name="csrb" type="text" class="span1" id="modsrb" /> <input name="ccg" type="text" class="span1" id="modcg" />
					<input name="cbih" type="text" class="span1" id="modbih" /> <input name="chr" type="text" class="span1" id="modhr" />
					<input name="cmk" type="text" class="span1" id="modmk" /> 
				</div>
			</div>
			
			<div class="control-group">
				<label for="text">Premium cena po slotu</label>
				<div class="controls">
					<input name="csrb_premium" type="text" class="span1" id="modsrb_premium" /> <input name="ccg_premium" type="text" class="span1" id="modcg_premium" />
					<input name="cbih_premium" type="text" class="span1" id="modbih_premium" /> <input name="chr_premium" type="text" class="span1" id="modhr_premium" />
					<input name="cmk_premium" type="text" class="span1" id="modmk_premium" /> 
				</div>
			</div>
	</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Otkazi</button>
				<button class="btn btn-primary">Dodaj</button>
			</div>
		</form>
</div>


	<!-- Scriptfiles
	================================================== -->
	<!-- Ovde je radi brzeg ucitavanja stranice 
	<script src="assets/js/libs/jquery-1.8.3.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
	<!--<script src="assets/js/ajax.js"></script>
	<script src="assets/js/libs/jquery-ui.js"></script>-->
	
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->
	<script src="assets/js/libs/jquery-1.8.3.min.js"></script>
	<script>window.jQuery || document.write("<script src='assets/js/libs/jquery-1.8.3.min.js'>\x3C/script>")</script>
	
	<!--<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
	<script src="assets/js/libs/jquery-ui.js"></script>
	<script>window.jQuery || document.write("<script src='assets/js/libs/jquery-ui.js'>\x3C/script>")</script>
	<!--<script src="assets/js/libs/jquery-ui-1.10.0.custom.min.js"></script>-->
	
	<script src="assets/js/libs/bootstrap.js"></script>

	<script src="assets/js/blinkpagetitle.js"></script>
	
	<script src="assets/js/autosize.js"></script>
	
	<script type="text/javascript" src="http://tablesorter.com/__jquery.tablesorter.min.js"></script> 	
	
	<script src="assets/js/plugins/validate/jquery.validate.js"></script>

	<script src="assets/js/demo/validation.js"></script>

	<script src="assets/js/plugins/faq/faq.js"></script>

	<script src="assets/js/demo/faq.js"></script>

	<script src="assets/js/tipsy.js"></script>

	<script src="assets/js/livesearch.js"></script>
	
	<script src="assets/js/form.js"></script>
	
	<script src="assets/js/gbh_panel.js?v5"></script>

	<script src="assets/js/Application.js"></script>

</body>

</html>
<style>
.extra {
    border-top: 1px solid #3b4f6b;
    border-bottom: 1px solid #3b4f6b;
}
* {
    margin: 0;
    padding: 0;
    border: 0;
}
user agent stylesheet
div {
    display: block;
}


</style>