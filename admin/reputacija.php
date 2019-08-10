<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Lista reputacija";
$fajl = "reputacija";

$sql = "SELECT * FROM `reputacija` WHERE `adminid` = '".$_SESSION['a_id']."' ORDER BY `id` DESC";
$query = "SELECT COUNT(*) as num FROM `reputacija` WHERE `adminid` = '".$_SESSION['a_id']."'";
$ikona = "icon-credit-card";
$itikete = "billing ";

$adjacents = 3;

$total_stranas = mysql_fetch_array(mysql_query($query));
$total_stranas = $total_stranas[num];
	
$targetstrana = "reputacija.php"; 	
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
					<h3>Vase reputacije</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Reputacija</th>
								<th>Klijent</th>
								<th>Tiket</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($result) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nemate reputacije.</m></td></tr>';
							}
							while($row = mysql_fetch_array($result)) {	
								$tiket = query_fetch_assoc("SELECT * FROM `tiketi` WHERE `id` = '".$row['tiketid']."'");
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo reputacijab($row['rep']); ?></td>
								<td><?php echo user_imep($row['klijentid']); ?></td>
								<td><a href="tiket.php?id=<?php echo $row['tiketid']; ?>"><m><?php echo $tiket['naslov']; ?></m></a></td>								
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>			
						<?=$pagination?>
					
					
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->	
<?php
include("assets/footer.php");
?>
