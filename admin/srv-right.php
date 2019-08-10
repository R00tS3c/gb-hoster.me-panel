<?php
if(CheckBoxStatus($serverid) == "Offline") {
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Mašina je OFFLINE!";
	$_SESSION['msg-type'] = 'error';
	header("Location: index.php");
	die();
}
?>
		<div class="span4">
      		<div class="widget stacked">
				<a style="width: 86%;" href="javascript:;" class="btn btn-large btn-support-ask">Status: <?php if($server['igra'] == "7") {
					echo formatStatus(getStatus($boxip['ip'], $box['sshport']));
				} else if($server['igra'] == "9") {
					include("./libs/fivem.php");
					if(fivemstatus($boxip['ip'], $server['port'])==true)
					{
						echo '<span style="color: green;">Online</span>';
						$srvonline = "Da";
					}
					else {
						echo '<span style="color: red;">Offline</span>';
					}
				} else {
					echo $online;
				} ?></a><br /><br />
				<a style="width: 86%;" href="#srvmove" data-toggle="modal" class="btn btn-large btn-info btn-support-ask"><i class="icon-forward"></i> Prebaci server</a><br /><br />
<?php			if($srvonline == "Da" AND $server['igra'] == "1") echo'
				<a style="width: 86%;" href="#srvrcon" data-toggle="modal" class="btn btn-large btn-inverse btn-support-ask">Rcon komanda</a><br /><br />';

			if($server['startovan'] == "0" && $server['igra'] != "7") {
?>
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-start" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-success btn-support-ask">Startuj server</button>
				</form>	
<?php
			} else if($server['startovan'] == "1" && $server['igra'] != "7") {
?>
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-stop" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-danger btn-support-ask">Stopiraj server</button>
				</form>
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-restart" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-warning btn-support-ask">Restartuj server</button>
				</form>
<?php
			}
			if($server['igra'] != "7") {
?>
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-kill" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-danger btn-support-ask">Kill server</button>
				</form>
				<?php
			}
			?>
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-reinstall" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-info btn-support-ask">Reinstaliraj server</button>
				</form>
				

				
<?php
				if($server['status'] == "Suspendovan")
				{
?>
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-unsuspend" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-warning btn-support-ask">Unsuspenduj server</button>
				</form>	
<?php
				}
				else
				{
?>
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-suspend" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-danger btn-support-ask">Suspenduj server</button>
				</form>	
<?php
				}
?>
				
				<form action="serverprocess.php" method="POST">
					<input type="hidden" name="task" value="server-delete" />
					<input type="hidden" name="serverid" value="<?php echo $serverid; ?>" />
					<button style="width: 100%;" type="submit" name="status" class="btn btn-large btn-danger btn-support-ask">Izbriši server</button>
				</form>				
			</div>
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-pushpin"></i>
					<h3>Server net info <a style="margin-left: 90px;float: right;" href="javascript:;" onclick="refresht(<?php echo $serverid; ?>)"><i style="float: right;" class="icon-refresh"></i></a></h3>
				</div>
				
				<div class="widget-content" id="asd123x">
<?php
				if($srvonline == "Da"  && $server['igra'] != "7" && $server['igra'] != "9") {
					if($server['igra'] == "1") $mapa = "http://banners.gametracker.rs/map/cs/".$srvmapa;
					if($server['igra'] == "2") $mapa = "http://banners.gametracker.rs/map/samp/".$srvmapa;
					if($server['igra'] == "3") $mapa = "http://banners.gametracker.rs/map/cs/".$srvmapa;
					if($server['igra'] == "4") $mapa = "http://banners.gametracker.rs/map/cs/".$srvmapa;
					if($server['igra'] == "5") $mapa = "http://banners.gametracker.rs/map/cs/".$srvmapa;					
?>
					<p id="h2"><i class="icon-th-large"></i>  Online: <z><?php echo $srvonline; ?></p>
					<p id="h2"><i class="icon-edit-sign"></i>  Ime servera: <z><?php echo htmlspecialchars($srvime); ?></z></p>
<?php
					if($server['igra'] == "2") { 
?>
					<p id="h2"><i class="icon-edit-sign"></i>  Gamemode: <z><?php echo $gt; ?></z></p>
<?php
					}
?>
					<p id="h2"><i class="icon-flag"></i>  Mapa: <z><?php echo htmlspecialchars($srvmapa); ?></z></p>
					<p id="h2"><i class="icon-th"></i>  Igraci: <z><?php echo $srvigraci; ?></z></p>
					<div id="srvmapa">
						<img width="110px" height="90px" src="<?php echo $mapa; ?>.jpg" />
					</div>					
<?php
				} else {
?>
					<p id="h2"><i class="icon-th-large"></i>  Online: <z><?php echo $srvonline; ?></p>
<?php
					if($server['startovan'] == "1"  && $server['igra'] != "7" && $server['igra'] != "9")
					{
?>
					<p id="h2"><i class="icon-asterisk"></i>  Moguće rešenje i greška: <z>Server je startovan ali nije online. Proverite da li je default mapa ispravna i da li postoji. Ako je ispravna onda izbrišite zadnji plugin koji ste dodali.</z></p>
<?php
					} else if($server['igra'] != "7" && $server['igra'] != "9") {
?>
					<p id="h2"><i class="icon-asterisk"></i> Rešenje: <z>Server je ugašen, da bi ga pokrenuli morate ga startovati klikom na dugme start ispred.</z></p>
<?php					
					}
				}
?>
				</div>
			</div>			
		</div>