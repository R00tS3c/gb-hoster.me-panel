<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Lista tiketa";
$fajl = "tiket_lista";

$targetstrana = "tiket_lista.php?vrsta=".$_GET['vrsta']; 	
if($_GET['vrsta'] == "admin") 
   $limit = "100";
else $limit = 100; 	
	
if(empty($_GET['strana'])) {
	$start = 0;	
	$strana = 1;
}elseif(!isset($_GET['strana'])) {
	$start = 0; 	
	$strana = 0;
} else{
	$start = ($_GET['strana'] - 1) * $limit; 
	$strana = $_GET['strana'];
}

if(!empty($_GET['vrsta'])) {
	if($_GET['vrsta'] == "novi") 
	{
		$sql = "SELECT * FROM `tiketi` WHERE `status` = '1' OR `status` = '4' OR `status` = '5' ORDER BY `status`, `id` DESC LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `tiketi` WHERE `status` = '1'";
		$ikona = "icon-comment";
		$itikete = "nove ";
	} 
	elseif($_GET['vrsta'] == "odgovoreni") 
	{
		$sql = "SELECT * FROM `tiketi` WHERE `status` = '2' ORDER BY `id` DESC LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `tiketi` WHERE `status` = '2'";
		$ikona = "icon-comment-alt";
		$itikete = "odgovorene ";
	} 
	elseif($_GET['vrsta'] == "zakljucani") 
	{
		$sql = "SELECT * FROM `tiketi` WHERE `status` = '3' ORDER BY `id` DESC LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `tiketi` WHERE `status` = '3'";
		$ikona = "icon-lock";
		$itikete = "zakljucane ";
	} 
	elseif($_GET['vrsta'] == "uplate") 
	{
		$sql = "SELECT * FROM `tiketi` WHERE `status` = '8' ORDER BY `id` DESC LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `tiketi` WHERE `status` = '8'";
		$ikona = "icon-credit-card";
		$itikete = "billing ";
	} 
	elseif($_GET['vrsta'] == "prosledjeni") 
	{
		$sql = "SELECT * FROM `tiketi` WHERE `status` = '10' AND `admin` = '".$_SESSION['a_id']."' ORDER BY `id` DESC LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `tiketi` WHERE `status` = '10' AND `admin` = '".$_SESSION['a_id']."'";
		$ikona = "icon-credit-card";
		$itikete = "odgovorene ";
	} 
	elseif($_GET['vrsta'] == "sve_uplate") 
	{
		samo_vlasnik($_SESSION['a_id']);
		$sql = "SELECT * FROM `tiketi` WHERE `naslov` LIKE 'Billing: Nova uplata %' ORDER BY `id` DESC LIMIT $start, $limit";
		$query = "SELECT * FROM `tiketi` WHERE `naslov` LIKE 'Billing: Nova uplata %'";
		$ikona = "icon-credit-card";
		$itikete = "billing ";
	} 
	elseif($_GET['vrsta'] == "ceka_proveru") 
	{
		samo_vlasnik($_SESSION['a_id']);
		$sql = "SELECT * FROM `tiketi` WHERE `naslov` = 'Billing: Nova uplata - Ceka proveru' ORDER BY `id` DESC LIMIT $start, $limit";
		$query = "SELECT * FROM `tiketi` WHERE `naslov` = 'Billing: Nova uplata - Ceka proveru'";
		$ikona = "icon-credit-card";
		$itikete = "billing ";
	} 
	elseif($_GET['vrsta'] == "admin") 
	{
		if(!empty($_GET['id']))
		{
			$aid = sqli($_GET['id']);
		
			$sql = "SELECT * FROM `tiketi` ORDER BY `id` DESC";
			$query = "SELECT * FROM `tiketi`";
			$ikona = "icon-credit-card";
			$itikete = "adminove ";
		}
	} 
	else 
	{
		header("Location: index.php");
	}
	
} else {
	header("Location: index.php");
}

$adjacents = 3;

$total_stranas = mysql_fetch_array(mysql_query($query));
$total_stranas = $total_stranas[num];
	
$result = mysql_query($sql) or die($sql);
	

if ($strana == 0) $strana = 1;					
$prev = $strana - 1;							
$next = $strana + 1;							
$laststrana = ceil($total_stranas/$limit);		
$lpm1 = $laststrana - 1;						
	
$pagination = "";
if($laststrana > 1)
{	
	$pagination .= "<div class=\"pagination\" style=\"margin-left: 10px;\"><ul>";
	//previous button
	if ($strana > 1) 
		$pagination.= "<li><a href=\"$targetstrana&amp;strana=$prev\">«</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>«</a></li>";	
	
	//strana	
	if ($laststrana < 7 + ($adjacents * 2))	//not enough stranas to bother breaking it up
	{	
		for ($counter = 1; $counter <= $laststrana; $counter++)
		{
			if ($counter == $strana)
				$pagination.= "<li><a class=\"active\">$counter</a></li>";
			else
				$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
		}
	}
	elseif($laststrana > 5 + ($adjacents * 2))	//enough stranas to hide some
	{
		if($strana < 1 + ($adjacents * 2))		
		{
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$laststrana\">$laststrana</a></li>";		
		}
		elseif($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2))
		{
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++)
			{
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=$laststrana\">$laststrana</a></li>";		
		}
		else
		{
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana&amp;strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++)
			{
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
			}
		}
	}
		
	//next button
	if ($strana < $counter - 1)
		$pagination.= "<li><a href=\"$targetstrana&amp;strana=$next\">»</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>»</a></li>";
	$pagination.= "</ul></div>\n";	
		
}

include("assets/header.php");

?>

			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="<?php echo $ikona; ?>"></i>
					<h3><?php echo ucwords($_GET['vrsta']); ?> tiketi</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Naslov tiketa</th>
								<th>Drzava</th>
								<th>Server</th>
								<th>Klijent</th>
								<th>Status</th>
								<?php if($itikete == "billing ") echo '<th>Iznos uplate</th>'; ?>
								<th>Vreme</th>
								<th class="td-actions" style="width: 112px;"></th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($result) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema tiketa.</m></td></tr>';
							}
							$count = 0;
							while($row = mysql_fetch_array($result)) {	
							$count++;
								if($itikete == "billing ") {
									$info2 = mysql_query("SELECT `iznos` FROM `billing` WHERE `id` = '".$row['billing']."'") or die(mysql_error());
									$info2 = mysql_fetch_array($info2);

									$iznosuplate = novac($info2['iznos'], 'srb');
								}
								if($_GET['vrsta'] == "admin") 
								{
									if(!empty($_GET['id']))
									{
										if(query_numrows("SELECT * FROM `tiketi_odgovori` WHERE `tiket_id` = '{$row[id]}' AND `admin_id` = '{$aid}'") == "1")
										{	
											$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$row['user_id']."'");
											$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$row['server_id']."'");
											
											$drz = query_fetch_assoc("SELECT * FROM `tiketi_odgovori` WHERE `tiket_id` = '".$row['id']."'");
											$drz = explode("Drzava: <m>", $drz['odgovor']);
											
											
											
											if($server['igra'] == "1") $igra = 'game-cs.png';
											else if($server['igra'] == "2") $igra = 'game-samp.png';
											else if($server['igra'] == "4") $igra = 'game-cod4.png';
											else if($server['igra'] == "3") $igra = 'game-minecraft.png';
											else if($server['igra'] == "6") $igra = 'game-ts3.png';
											else if($server['igra'] == "8") $igra = 'game-sinusbot.png';
											else if($server['igra'] == "9") $igra = 'game-fivem.png';
								?>
										<tr>
											<td>#<?php echo $row['id']; ?>/<?php echo $count; ?></td>
											<td><a href="tiket.php?id=<?php echo $row['id']; ?>"><m><?php echo $row['naslov']; ?></m></a></td>
											<td><?php echo $drz[1]; ?></td>
			<?php
											if($row['naslov'] == "Billing: <z>Nova uplata</z>" or $row['naslov'] == "Billing: Nova uplata - Leglo" or $row['naslov'] == "Billing: Nova uplata - Nije leglo"){
												echo '<td><m>Nema servera</m></td>';
											}
											else
											{
			?>
											<td class="tipg"><a href="srv-pocetna.php?id=<?php echo $row['server_id']; ?>" data-toggle="tooltip" data-placement="top" title="Counter-Strike 1.6"><img src="./assets/img/<?php echo $igra; ?>" style="width: 16px; height: 16px; margin-top: -3px;" /> <m><?php echo $server['name']; ?></m></a></td>
			<?php
											}
			?>
											<td><m><?php echo $klijent['ime']." ".$klijent['prezime']; ?></m></td>
											<td><?php echo status_tiketa($row['id']); ?></td>
											<?php if($itikete == "billing ") echo '<td>'.$iznosuplate.'</td>'; ?>
											<td><?php echo vreme($row['datum']); ?></m></td>
											<td class="td-actions" style="width: 92px;">								
												<form action="process.php" method="POST">
													<input type="hidden" name="task" value="tiket_delete" />
													<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
													<button type="submit" class="btn btn-small btn-warning">
														<i class="btn-icon-only icon-remove"></i>										
													</button>
												</form>
												
												<form action="process.php" method="POST">
													<input type="hidden" name="task" value="zakljucaj_tiket" />
													<input type="hidden" name="tiketid" value="<?php echo $row['id']; ?>" />
													<button type="submit" class="btn btn-small btn-warning">
														<i class="btn-icon-only icon-lock"></i>										
													</button>
												</form>
												
												
												<a href="tiket.php?id=<?php echo $row['id']; ?>">
													<button type="submit" class="btn btn-small btn-warning">
														<i class="btn-icon-only icon-arrow-right"></i>										
													</button>
												</a>
											</td>									
										</tr>	<?php
										}
									}
								}
								else
								{
								$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$row['user_id']."'");
								$server = query_fetch_assoc("SELECT * FROM `serveri` WHERE `id` = '".$row['server_id']."'");
								
								$drz = query_fetch_assoc("SELECT * FROM `tiketi_odgovori` WHERE `tiket_id` = '".$row['id']."'");
								$drz = explode("Drzava: <m>", $drz['odgovor']);
								
								$drz = explode("</m>", $drz[1]);
								
								if($server['igra'] == "1") $igra = 'game-cs.png';
								else if($server['igra'] == "2") $igra = 'game-samp.png';
								else if($server['igra'] == "4") $igra = 'game-cod4.png';
								else if($server['igra'] == "3") $igra = 'game-minecraft.png';
								else if($server['igra'] == "6") $igra = 'game-ts3.png';
								else if($server['igra'] == "7") $igra = 'game-fdl.png';
								else if($server['igra'] == "8") $igra = 'game-sinusbot.png';
								else if($server['igra'] == "9") $igra = 'game-fivem.png';
					?>
							<tr>
								<td>#<?php echo $row['id']; ?>/<?php echo $count; ?></td>
								<td><a href="tiket.php?id=<?php echo $row['id']; ?>"><m><?php echo $row['naslov']; ?></m></a></td>
								<td><?php echo $klijent[zemlja]; ?></td>
<?php
								if($row['naslov'] == "Billing: <z>Nova uplata</z>" or $row['naslov'] == "Billing: Nova uplata - Leglo" or $row['naslov'] == "Billing: Nova uplata - Nije leglo"){
									echo '<td><m>Nema servera</m></td>';
								}
								else
								{
?>
								<td class="tipg"><a href="srv-pocetna.php?id=<?php echo $row['server_id']; ?>" data-toggle="tooltip" data-placement="top" title="Counter-Strike 1.6"><img src="./assets/img/<?php echo $igra; ?>" style="width: 16px; height: 16px; margin-top: -3px;" /> <m><?php echo $server['name']; ?></m></a></td>
<?php
								}
?>
								<td><m><?php echo $klijent['ime']." ".$klijent['prezime']; ?></m></td>
								<td><?php echo status_tiketa($row['id']); ?></td>
								<?php if($itikete == "billing ") echo '<td>'.$iznosuplate.'</td>'; ?>
								<td><?php echo vreme($row['datum']); ?></m></td>
								<td class="td-actions" style="width: 92px;">								
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="tiket_delete" />
										<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-remove"></i>										
										</button>
									</form>
									
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="zakljucaj_tiket" />
										<input type="hidden" name="tiketid" value="<?php echo $row['id']; ?>" />
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-lock"></i>										
										</button>
									</form>
									
									
									<a href="tiket.php?id=<?php echo $row['id']; ?>">
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-arrow-right"></i>										
										</button>
									</a>
								</td>									
							</tr>	
					<?php		}
							}

					?>
							</tbody>
						</table>
			
						<?php echo $pagination; ?>
					
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->	
<?php
include("assets/footer.php");
?>
