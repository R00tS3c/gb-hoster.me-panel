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
				<a style="width: 86%;" href="javascript:;" class="btn btn-large btn-support-ask">Status: <?php echo $Server_Online;?></a><br /><br />
				<a style="width: 86%;" href="#srvmove" data-toggle="modal" class="btn btn-large btn-info btn-support-ask"><i class="icon-forward"></i> Prebaci server</a><br /><br />
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
				<p id="h2"><i class="icon-th-large"></i>  Online: <z><?php echo $Server_Online; ?></p>
				<p id="h2"><i class="icon-edit-sign"></i>  Ime servera: <z><?php echo htmlspecialchars($Server_Name); ?></z></p>
				<p id="h2"><i class="icon-th"></i>  Igraci: <z><?php echo $Server_Players; ?></z></p>
				<p id="h2"><i class="icon-lock"></i>  Password: <z><?php echo $ts_s_pass; ?></z></p>
				<p id="h2"><i class="icon-time"></i>  UpTime: <z><?php echo $ts_s_uptime; ?></z></p>
				</div>
			</div>			
		</div>