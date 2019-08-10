<?php

session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Lista billing tiketa";
$fajl = "tiket_lista";

$targetstrana = "billing_tiket_lista.php?vrsta=".$_GET['vrsta'];

if($_GET['vrsta'] == "admin")
   $limit = "100";
else $limit = 100;

if(empty($_GET['strana'])) {
	$start = 0;
	$strana = 1;
} else if(!isset($_GET['strana'])) {
	$start = 0;
	$strana = 0;
} else {
	$start = ($_GET['strana'] - 1) * $limit;
	$strana = $_GET['strana'];
}

if(!empty($_GET['vrsta'])) {
	if($_GET['vrsta'] == "all") {
		$sql = "SELECT * FROM `billing_tiketi` WHERE `status` = '1' OR `status` = '4' OR `status` = '5' ORDER BY `status`, `id` DESC LIMIT $start, $limit";
		$query = "SELECT COUNT(*) as num FROM `billing_tiketi` WHERE `status` = '1'";
		$ikona = "icon-comment";
		$itikete = "nove ";
	} else {
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
	
	if ($strana > 1) 
		$pagination.= "<li><a href=\"$targetstrana&amp;strana=$prev\">«</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>«</a></li>";	
	
	if ($laststrana < 7 + ($adjacents * 2)) {
		for ($counter = 1; $counter <= $laststrana; $counter++)
		{
			if ($counter == $strana)
				$pagination.= "<li><a class=\"active\">$counter</a></li>";
			else
				$pagination.= "<li><a href=\"$targetstrana&amp;strana=$counter\">$counter</a></li>";					
		}
	}
	elseif($laststrana > 5 + ($adjacents * 2)) {
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
								if($_GET['vrsta'] != "admin") {
								$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '".$row['user_id']."'");
								
								$drz = query_fetch_assoc("SELECT * FROM `billing_tiketi_odgovori` WHERE `tiket_id` = '".$row['id']."'");
								$drz = explode("Drzava: <m>", $drz['odgovor']);
								
								$drz = explode("</m>", $drz[1]);
					?>
							<tr>
								<td>#<?php echo $row['id']; ?>/<?php echo $count; ?></td>
								<td><a href="billing_tiket.php?id=<?php echo $row['id']; ?>"><m><?php echo $row['naslov']; ?></m></a></td>
								<td><?php echo $klijent[zemlja]; ?></td>
								<td><m><?php echo $klijent['ime']." ".$klijent['prezime']; ?></m></td>
								<td><?php echo status_tiketa($row['id']); ?></td>
								<?php if($itikete == "billing ") echo '<td>'.$iznosuplate.'</td>'; ?>
								<td><?php echo vreme($row['datum']); ?></m></td>
								<td class="td-actions" style="width: 92px;">								
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="billing_tiket_delete" />
										<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-remove"></i>										
										</button>
									</form>
									
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="billing_zakljucaj_tiket" />
										<input type="hidden" name="tiketid" value="<?php echo $row['id']; ?>" />
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-lock"></i>										
										</button>
									</form>
									
									
									<a href="billing_tiket.php?id=<?php echo $row['id']; ?>">
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
