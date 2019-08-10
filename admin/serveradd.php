<?php
session_start();

include("konfiguracija.php");

include("includes.php");

samo_vlasnik($_SESSION['a_id']);


$naslov = "Dodavanje servera";
$fajl = "serveradd";

if(isset($_GET['masina'])) $masina = mysql_real_escape_string($_GET['masina']);
if(isset($_GET['ip'])) $ip = mysql_real_escape_string($_GET['ip']);
if(isset($_GET['klijent'])) $klijentid = mysql_real_escape_string($_GET['klijent']);


if(isset($_GET['masina']) AND isset($_GET['ip']) AND isset($_GET['klijent']))
{

	$boxip = query_fetch_assoc("SELECT `ip` FROM `boxip` WHERE `boxid` = '".$masina."'");

	$provera_username = query_numrows("SELECT `id` FROM `serveri` WHERE `user_id` = '".$klijentid."'");  

	$server_br = $sifra = randomBroj(5);;
	
	$username_proveren = 'srv_'.$klijentid.'_'.$server_br.'';
	
	while(query_numrows("SELECT * FROM `serveri` WHERE `username` = '{$username_proveren}'") != 0) {
		$username_proveren = 'srv_'.$klijentid.'_'.($server_br + 1).'';  
	}

	$sifra = randomSifra(8);

	require("../inc/libs/lgsl/lgsl_class.php");	
	
	for($port = 27015; $port <= 29999; $port++)
	{
		if(query_numrows("SELECT * FROM `serveri` WHERE `ip_id` = '".$ip."' AND `port` = '".$port."' LIMIT 1") == 0)
		{
			$serverl = lgsl_query_live('halflife', $boxip['ip'], NULL, $port, NULL, 's');
			
			if(@$serverl['b']['status'] == '1') $srvonline = "Da";
			else $srvonline = "Ne";		
			
			if($srvonline == "Ne")
			{
				$portcs = $port;
				break;
			}
		}
	}

	for($port = 7777; $port <= 9999; $port++)
	{
		if(query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '".$masina."' AND `port` = '".$port."' LIMIT 1") == 0)
		{
			$serverl = lgsl_query_live('samp', $boxip['ip'], NULL, $port, NULL, 's');
			
			if(@$serverl['b']['status'] == '1') $srvonline = "Da";
			else $srvonline = "Ne";	
			
			if($srvonline == "Ne")
			{
				$portsamp = $port;
				break;
			}
		}
	}

	for($port = 25565; $port <= 25999; $port++)
	{
		if(query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '".$masina."' AND `port` = '".$port."' LIMIT 1") == 0)
		{
			$serverl = lgsl_query_live('minecraft', $boxip['ip'], NULL, $port, NULL, 's');
			
			if(@$serverl['b']['status'] == '1') $srvonline = "Da";
			else $srvonline = "Ne";	
			
			if($srvonline == "Ne")
			{
				$portmc = $port;
				break;
			}
		}
	}
	for($port = 30110; $port <= 32110; $port++)
	{
		if(query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '".$masina."' AND `port` = '".$port."' LIMIT 1") == 0)
		{
				$portfivem = $port;
				break;
		}
	}
	for($port = 9987; $port <= 11000; $port++)
	{
		if(query_numrows("SELECT * FROM `serveri` WHERE `box_id` = '".$masina."' AND `port` = '".$port."' LIMIT 1") == 0)
		{
				$portts = $port;
				break;
		}
	}

}

include("assets/header.php");
?>
      <div class="row">
      	
      	<div class="span12">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Dodaj server</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					<table width="100%">
						<tr>
							<th style="width: 50%"></th>
							<th style="width: 50%"></th>
						</tr>
						<tr>		
							<td>							
								<form action="serveradd.php" method="get">
							<?php	if(isset($_GET['klijent'])) { ?>
									<input type="hidden" name="klijent" value="<?php echo $_GET['klijent']; ?>" />
							<?php	}	?>
									<div class="control-group">
										<label for="klijent">Masina</label>
										<div class="controls">
											<select name="masina" class="span6" onchange="this.form.submit()">
												<option value="0" disabled selected="selected">Izaberite masinu</option>
			<?php
											$box = mysql_query("SELECT * FROM `box` ORDER BY `boxid`");
											while($row = mysql_fetch_array($box)) {
			?>
												<option <?php if($masina == $row['boxid']) echo ' selected="selected"'; ?> value="<?php echo $row['boxid']; ?>">#<?php echo $row['boxid'].' '.$row['name'].' - '.$row['ip']; ?></option>
			<?php
											}
			?>
											</select>
										</div>
									</div>	
								</form>						
							</td>
<?php						if(isset($_GET['masina'])) { ?>
							<form action="serveradd.php" method="get">
						<?php	if(isset($_GET['klijent'])) { ?>
								<input type="hidden" name="klijent" value="<?php echo $_GET['klijent']; ?>" />
						<?php	}	?>							
								<input type="hidden" name="masina" value="<?php echo $masina; ?>" />
								<td>
									<div class="control-group">
										<label for="klijent">Ip adresa</label>
										<div class="controls">
											<select name="ip" class="span6" onchange="this.form.submit()">
												<option value="0" disabled selected="selected">Izaberite IP</option>
			<?php
											$ipp = mysql_query("SELECT * FROM `boxip` WHERE `boxid` = '{$masina}'");
											while($row = mysql_fetch_array($ipp)) {
			?>
												<option <?php if(!empty($ip)) if($ip == $row['ipid']) echo ' selected="selected"'; ?> value="<?php echo $row['ipid']; ?>">#<?php echo $row['ipid'].' '.$row['ip']; ?></option>
			<?php
											}
			?>
											</select>
										</div>
									</div>	
								</td>
							</form>	
<?php					}	?>							
						</tr>			
						<tr>
<?php					if(isset($masina) AND isset($_GET['ip'])) { ?>								
							<form action="serveradd.php" method="get">
						<?php	if(isset($_GET['klijent'])) { ?>
								<input type="hidden" name="klijent" value="<?php echo $_GET['klijent']; ?>" />
						<?php	}	?>							
								<input type="hidden" name="masina" value="<?php echo $masina; ?>" />
								<input type="hidden" name="ip" value="<?php echo $ip; ?>" />
								<td>
									<div class="control-group">
										<label for="klijent">Klijent</label>
										<div class="controls">
											<select name="klijent" class="span6" onchange="this.form.submit()">
												<option value="0" disabled selected="selected">Izaberite klijenta</option>
			<?php
											$klijent = mysql_query("SELECT * FROM `klijenti` ORDER BY `ime`");
											while($row = mysql_fetch_array($klijent)) {
			?>
												<option <?php if(isset($_GET['klijent'])) if($klijentid == $row['klijentid']) echo ' selected="selected"'; ?> value="<?php echo $row['klijentid']; ?>"><?php echo $row['ime'].' '.$row['prezime'].' - '.$row['email']; ?></option>
			<?php
											}
			?>
											</select>
										</div>
									</div>	
								</td>
							</form>	
<?php						} 
							if(isset($masina) AND isset($_GET['ip']) AND isset($_GET['klijent'])) {
?>
						<form action="serverprocess.php" method="post">
							<input type="hidden" name="task" value="server-add" />
							<input type="hidden" name="klijentid" value="<?php echo $klijentid; ?>" />
							<input type="hidden" name="ipid" value="<?php echo $ip; ?>" />
							<input type="hidden" name="boxid" value="<?php echo $masina; ?>" />
							<td>
								<div class="control-group">
									<label for="klijent">Igra</label>
									<div class="controls">
										<select name="igra" class="span6" id="serveraddigra">
											<option value="0" disabled selected="selected">Izaberi</option>
											<option value="1">Counter-Strike 1.6</option>
											<option value="2">San Andreas Multiplayer</option>
											<option value="3">Minecraft</option>											
											<option value="4" disabled>COD</option>
											<option value="5" disabled>Multi Theft Auto</option>
											<option value="6">Team Speak 3</option>
											<option value="7">Fast DL</option>
											<option value="9">FiveM</option>


										</select>
									</div>
								</div>								
							</td>
						</tr>
						<tr>
							<td>
								<div class="control-group">
									<label for="klijent">Slotovi (Nije potrebno za FastDL Server)</label>
									<div class="controls">
										<input class="span5" type="text" name="slotovi" placeholder="25" />
									</div>
								</div>								
							</td>						
							<td>
								<div class="control-group">
									<label for="klijent">Ime servera</label>
									<div class="controls">
										<input name="ime" class="span5" type="text" placeholder="Ime servera">
									</div>
								</div>								
							</td>	
						</tr>
						<tr>
							<td>
								<div class="control-group">
									<label for="klijent">Port (Nije potrebno za FastDL Server)</label>
									<div class="controls">
										<input name="port" class="span5" type="text" placeholder="Izaberite igru..." id="defad">
										<input name="portcs" class="span5" type="text" value="<?php echo $portcs; ?>" id="csad" style="display: none">
										<input name="portsamp" class="span5" type="text" value="<?php echo $portsamp; ?>" id="sampad" style="display: none">
										<input name="portmc" class="span5" type="text" value="<?php echo $portmc; ?>" id="mcad" style="display: none">
										<input name="portfivem" class="span5" type="text" value="<?php echo $portfivem; ?>" id="fivemad" style="display: none">										<input name="portts" class="span5" type="text" value="<?php echo $portts; ?>" id="tsad" style="display: none">
									</div>
								</div>								
							</td>							
							<td>
								<div class="control-group">
									<label for="klijent">Username</label>
									<div class="controls">
										<input name="username" class="span5" type="text" readonly="readonly" value="<?php echo $username_proveren; ?>">
									</div>
								</div>								
							</td>	
						</tr>
						<tr>
							<td>
								<div class="control-group">
									<label for="klijent">Password</label>
									<div class="controls">
										<input name="password" class="span5" type="text" readonly="readonly" value="<?php echo $sifra; ?>">
									</div>
								</div>								
							</td>	
							<td>
								<div class="control-group">
									<label for="klijent">Istice</label>
									<div class="controls">
										<input name="istice" class="span5" type="text" id="datum" value="<?php echo date("m/d/Y", time()); ?>">
									</div>
								</div>								
							</td>	
						</tr>
						<tr>
							<td>
								<div class="control-group">
									<label for="klijent">Mod (Ne treba za TS3 i FDL)</label>
									<div class="controls">
										<select name="mod" class="span6" id="csdef">
											<option value="0" disabled selected="selected">Izaberite mod</option>	
										</select>
										<select name="mod" class="span6" id="csmod" style="display: none;">
											<option value="0" disabled selected="selected">Izaberite mod</option>
			<?php
											$mod = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '1'");
											while($row = mysql_fetch_array($mod)) {
			?>
												<option value="<?php echo $row['id']; ?>">#<?php echo $row['id'].' '.$row['ime']; ?></option>
			<?php
											}
			?>
										</select>
										<select name="mod" class="span6" id="mcmod" style="display: none;">
											<option value="0" disabled selected="selected">Izaberite mod</option>
			<?php
											$mod = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '3'");
											while($row = mysql_fetch_array($mod)) {
			?>
												<option value="<?php echo $row['id']; ?>">#<?php echo $row['id'].' '.$row['ime']; ?></option>
			<?php
											}
			?>
										</select>
										<select name="mod" class="span6" id="sampmod" style="display: none;">
											<option value="0" disabled selected="selected">Izaberite mod</option>
			<?php
											$mod = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '2'");
											while($row = mysql_fetch_array($mod)) {
			?>
												<option value="<?php echo $row['id']; ?>">#<?php echo $row['id'].' '.$row['ime']; ?></option>
			<?php
											}
			?>
										</select>
										<select name="mod" class="span6" id="fivemmod" style="display: none;">
											<option value="0" disabled selected="selected">Izaberite mod</option>
			<?php
											$mod = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '9'");
											while($row = mysql_fetch_array($mod)) {
			?>
												<option value="<?php echo $row['id']; ?>">#<?php echo $row['id'].' '.$row['ime']; ?></option>
			<?php
											}
			?>
										</select>	
																				<select name="mod" class="span6" id="tsmod" style="display: none;">
											<option value="0" disabled selected="selected">Izaberite mod</option>
			<?php
											$mod = mysql_query("SELECT * FROM `modovi` WHERE `igra` = '6'");
											while($row = mysql_fetch_array($mod)) {
			?>
												<option value="<?php echo $row['id']; ?>">#<?php echo $row['id'].' '.$row['ime']; ?></option>
			<?php
											}
			?>
										</select>	
									</div>
								</div>								
							</td>	
							<td>
								<div class="control-group">
									<button class="btn btn-primary" type="submit">Napravi</button>
								</div>								
							</td>	
							</form>
						</tr>						
<?php					} 	?>
					</table>
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->					
			
	    </div> <!-- /span12 -->     
      	
      </div> <!-- /row -->
<?php
include("assets/footer.php");
?>