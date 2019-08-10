<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled klijenta";
$fajl = "klijenti";

if(isset($_GET['view']))
{
	$view = mysql_real_escape_string($_GET['view']);
	if($view == "all")
	{
		
		$adjacents = 3;

		$query = "SELECT COUNT(*) as num FROM `klijenti`";
		$total_stranas = mysql_fetch_array(mysql_query($query));
		$total_stranas = $total_stranas[num];
		
		$targetstrana = "klijenti.php?view=all"; 	
		$limit = 15; 	
		
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
		

		$sql = "SELECT * FROM `klijenti` ORDER by `klijentid` ASC LIMIT $start, $limit";
		$result = mysql_query($sql);
		

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
				$pagination.= "<li><a href=\"$targetstrana&strana=$prev\">«</a></li>";
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
						$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
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
							$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
					}
					$pagination.= "<li><a>...</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$lpm1\">$lpm1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$laststrana\">$laststrana</a></li>";		
				}
				elseif($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2))
				{
					$pagination.= "<li><a href=\"$targetstrana&strana=1\">1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=2\">2</a></li>";
					$pagination.= "<li><a>...</a></li>";
					for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++)
					{
						if ($counter == $strana)
							$pagination.= "<li><a class=\"active\">$counter</a></li>";
						else
							$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
					}
					$pagination.= "<li><a>...</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$lpm1\">$lpm1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$laststrana\">$laststrana</a></li>";		
				}
				else
				{
					$pagination.= "<li><a href=\"$targetstrana&strana=1\">1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=2\">2</a></li>";
					$pagination.= "<li><a>...</a></li>";
					for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++)
					{
						if ($counter == $strana)
							$pagination.= "<li><a class=\"active\">$counter</a></li>";
						else
							$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
					}
				}
			}
			
			//next button
			if ($strana < $counter - 1)
				$pagination.= "<li><a href=\"$targetstrana&strana=$next\">»</a></li>";
			else
				$pagination.= "<li class=\"disabled\"><a>»</a></li>";
			$pagination.= "</ul></div>\n";	
			
		}
	}
	else if($view == "banovani")
	{
		
		$adjacents = 3;

		$query = "SELECT COUNT(*) as num FROM `klijenti` WHERE `banovan` = '1'";
		$total_stranas = mysql_fetch_array(mysql_query($query));
		$total_stranas = $total_stranas[num];
		
		$targetstrana = "klijenti.php?view=banovani"; 	
		$limit = 15; 	
		
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
		

		$sql = "SELECT * FROM `klijenti` WHERE `banovan` = '1' ORDER by `klijentid` DESC LIMIT $start, $limit";
		$result = mysql_query($sql);
		

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
				$pagination.= "<li><a href=\"$targetstrana&strana=$prev\">«</a></li>";
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
						$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
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
							$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
					}
					$pagination.= "<li><a>...</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$lpm1\">$lpm1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$laststrana\">$laststrana</a></li>";		
				}
				elseif($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2))
				{
					$pagination.= "<li><a href=\"$targetstrana&strana=1\">1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=2\">2</a></li>";
					$pagination.= "<li><a>...</a></li>";
					for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++)
					{
						if ($counter == $strana)
							$pagination.= "<li><a class=\"active\">$counter</a></li>";
						else
							$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
					}
					$pagination.= "<li><a>...</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$lpm1\">$lpm1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=$laststrana\">$laststrana</a></li>";		
				}
				else
				{
					$pagination.= "<li><a href=\"$targetstrana&strana=1\">1</a></li>";
					$pagination.= "<li><a href=\"$targetstrana&strana=2\">2</a></li>";
					$pagination.= "<li><a>...</a></li>";
					for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++)
					{
						if ($counter == $strana)
							$pagination.= "<li><a class=\"active\">$counter</a></li>";
						else
							$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
					}
				}
			}
			
			//next button
			if ($strana < $counter - 1)
				$pagination.= "<li><a href=\"$targetstrana&strana=$next\">»</a></li>";
			else
				$pagination.= "<li class=\"disabled\"><a>»</a></li>";
			$pagination.= "</ul></div>\n";	
			
		}	
	}
	else if($view == "aktivacija")
	{
		if(vlasnik($_SESSION['a_id'])) 
		{
			$adjacents = 3;

			$query = "SELECT COUNT(*) as num FROM `klijenti` WHERE `status` = 'Aktivacija'";
			$total_stranas = mysql_fetch_array(mysql_query($query));
			$total_stranas = $total_stranas[num];
			
			$targetstrana = "klijenti.php?view=aktivacija"; 	
			$limit = 15; 	
			
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
			

			$sql = "SELECT * FROM `klijenti` WHERE `status` = 'Aktivacija' ORDER by `klijentid` DESC LIMIT $start, $limit";
			$result = mysql_query($sql);
			

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
					$pagination.= "<li><a href=\"$targetstrana&strana=$prev\">«</a></li>";
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
							$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
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
								$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
						}
						$pagination.= "<li><a>...</a></li>";
						$pagination.= "<li><a href=\"$targetstrana&strana=$lpm1\">$lpm1</a></li>";
						$pagination.= "<li><a href=\"$targetstrana&strana=$laststrana\">$laststrana</a></li>";		
					}
					elseif($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2))
					{
						$pagination.= "<li><a href=\"$targetstrana&strana=1\">1</a></li>";
						$pagination.= "<li><a href=\"$targetstrana&strana=2\">2</a></li>";
						$pagination.= "<li><a>...</a></li>";
						for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++)
						{
							if ($counter == $strana)
								$pagination.= "<li><a class=\"active\">$counter</a></li>";
							else
								$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
						}
						$pagination.= "<li><a>...</a></li>";
						$pagination.= "<li><a href=\"$targetstrana&strana=$lpm1\">$lpm1</a></li>";
						$pagination.= "<li><a href=\"$targetstrana&strana=$laststrana\">$laststrana</a></li>";		
					}
					else
					{
						$pagination.= "<li><a href=\"$targetstrana&strana=1\">1</a></li>";
						$pagination.= "<li><a href=\"$targetstrana&strana=2\">2</a></li>";
						$pagination.= "<li><a>...</a></li>";
						for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++)
						{
							if ($counter == $strana)
								$pagination.= "<li><a class=\"active\">$counter</a></li>";
							else
								$pagination.= "<li><a href=\"$targetstrana&strana=$counter\">$counter</a></li>";					
						}
					}
				}
				
				//next button
				if ($strana < $counter - 1)
					$pagination.= "<li><a href=\"$targetstrana&strana=$next\">»</a></li>";
				else
					$pagination.= "<li class=\"disabled\"><a>»</a></li>";
				$pagination.= "</ul></div>\n";	
				
			}	
		}
		else
		{
			$_SESSION['msg1'] = "Greška";
			$_SESSION['msg2'] = "Support ne može aktivirati klijente!";
			$_SESSION['msg-type'] = "error";
			header("Location: index.php");
			die();
		}
	}
}


include("assets/header.php");
?>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3><?php if($_GET['view'] == "all") echo 'Lista klijenta'; else if($_GET['view'] == "banovani") echo 'Lista banovanih klijenta'; ?></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Username</th>
								<th>E-mail</th>
								<th>Ime</th>
								<th>Zadnji login</th>
								<th>Zemlja</th>
								<th>Zadnji ip</th>
								<th>Online status</th>
<?php
								if($_GET['view'] == "banovani")
								{
?>
								<th>Banovan do</th>
<?php
								}
?>								
								<th>Status</th>
								<th class="td-actions" style="width: 112px;"><?php if($_GET['view'] == "all") echo 'Delete/Profil'; else if($_GET['view'] == "banovani") echo 'Unban/Profil'; ?></th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($result) == 0) {
								echo'<tr><td colspan="5"><m>Tabela je prazna.</m></td></tr>';
							}
							while($row = mysql_fetch_array($result)) {	
								$banovan = query_fetch_assoc("SELECT * FROM `banovi` WHERE `klijentid` = '".$row['klijentid']."'");
					?>
							<tr>
								<td><a href="klijent.php?id=<?php echo $row['klijentid']; ?>"><m>#<?php echo $row['klijentid']; ?></m></a></td>
								<td><?php echo $row['username']; ?></td>
								<td><a href="klijent.php?id=<?php echo $row['klijentid']; ?>"><m><?php echo $row['email']; ?></m></a></td>
								<td><?php echo $row['ime'].' '.$row['prezime']; ?></td>
								<td><?php echo $row['lastlogin']; ?></td>
								<td><?php echo $row['zemlja']; ?></td>
								<td><?php echo $row['lastip']; ?></td>
								<td><?php echo get_status($row['lastactivity']); ?></td>
<?php
								if($_GET['view'] == "banovani")
								{
?>
								<td><?php echo vreme($banovan['trajanje']); ?></td>
<?php
								}
?>
								<td><?php echo $row['status']; ?></td>
								<td class="td-actions" style="width: 92px;">								
<?php
								if($_GET['view'] == "banovani")
								{
?>
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="klijent_unban" />
										<input type="hidden" name="id" value="<?php echo $row['klijentid']; ?>" />
										<input type="hidden" name="banid" value="<?php echo $banovan['id']; ?>" />
										
										<button title="Unbanuj klijenta" type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-remove"></i>										
										</button>	
									</form>
<?php
								}
								else if($_GET['view'] == "aktivacija")
								{	
?>
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="klijent-aktiviraj" />
										<input type="hidden" name="klijentid" value="<?php echo $row['klijentid'] ?>" />
										
										<button title="Aktiviraj klijenta" type="submit" class="btn btn-small btn-success">
											<i class="btn-icon-only icon-ok"></i>										
										</button>
									</form>
									
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="klijent_delete" />
										<input type="hidden" name="id" value="<?php echo $row['klijentid']; ?>" />
										
										<button title="Izbrisi klijenta" type="submit" class="btn btn-small btn-danger">
											<i class="btn-icon-only icon-remove"></i>										
										</button>
									</form>
<?php							
								}
								else
								{
?>									
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="klijent_delete" />
										<input type="hidden" name="id" value="<?php echo $row['klijentid']; ?>" />
										
										<button title="Izbrisi klijenta" type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-remove"></i>										
										</button>
									</form>
<?php
								}
?>
										

									<a href="klijent.php?id=<?php echo $row['klijentid']; ?>">
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-arrow-right"></i>										
										</button>
									</a>
								</td>	
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>			
						<?php echo $pagination; ?>
					
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->		
<?php
include("assets/footer.php");
?>