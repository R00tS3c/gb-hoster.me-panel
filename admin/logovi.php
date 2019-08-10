<?php
session_start();

include("konfiguracija.php");
include("includes.php");

samo_vlasnik($_SESSION['a_id']);

$naslov = "Pregled logova";
$fajl = "logovi";

	$adjacents = 3;

	$query = "SELECT COUNT(*) as num FROM logovi WHERE `clientid` IS NULL";
	$total_stranas = mysql_fetch_array(mysql_query($query));
	$total_stranas = $total_stranas[num];
	
	$targetstrana = "logovi"; 	
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
	

	$sql = "SELECT * FROM `logovi` WHERE `clientid` IS NULL ORDER by `id` DESC LIMIT $start, $limit";
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
			$pagination.= "<li><a href=\"$targetstrana?strana=$prev\">«</a></li>";
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
					$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
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
						$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
				}
				$pagination.= "<li><a>...</a></li>";
				$pagination.= "<li><a href=\"$targetstrana?strana=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetstrana?strana=$laststrana\">$laststrana</a></li>";		
			}
			elseif($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2))
			{
				$pagination.= "<li><a href=\"$targetstrana?strana=1\">1</a></li>";
				$pagination.= "<li><a href=\"$targetstrana?strana=2\">2</a></li>";
				$pagination.= "<li><a>...</a></li>";
				for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++)
				{
					if ($counter == $strana)
						$pagination.= "<li><a class=\"active\">$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
				}
				$pagination.= "<li><a>...</a></li>";
				$pagination.= "<li><a href=\"$targetstrana?strana=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetstrana?strana=$laststrana\">$laststrana</a></li>";		
			}
			else
			{
				$pagination.= "<li><a href=\"$targetstrana?strana=1\">1</a></li>";
				$pagination.= "<li><a href=\"$targetstrana?strana=2\">2</a></li>";
				$pagination.= "<li><a>...</a></li>";
				for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++)
				{
					if ($counter == $strana)
						$pagination.= "<li><a class=\"active\">$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
				}
			}
		}
		
		//next button
		if ($strana < $counter - 1)
			$pagination.= "<li><a href=\"$targetstrana?strana=$next\">»</a></li>";
		else
			$pagination.= "<li class=\"disabled\"><a>»</a></li>";
		$pagination.= "</ul></div>\n";	
		
	}


include("assets/header.php");
?>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Logovi</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Poruka</th>
								<th>Admin</th>
								<th>IP</th>
								<th>Vreme</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($result) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema logova.</m></td></tr>';
							}
							while($row = mysql_fetch_array($result)) {	?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo $row['message']; ?></td>
								<td><m><?php echo log_ime($row['adminid']); ?></m></td>
								<td><?php echo $row['ip']; ?></td>
								<td><?php echo vreme($row['vreme']); ?></m></td>
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>
						
						<form action="process.php" method="POST">
							<input type="hidden" name="task" value="izbrisi_logove" />
							<button class="btn btn-primary" type="submit" style="float: right; margin: 15px;">Izbriši sve logove</button>		
						</form>				
						<?php echo $pagination; ?>
					
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->		
<?php
include("assets/footer.php");
?>
