<?php
session_start();

include("konfiguracija.php");
include("includes.php");

if(empty($_GET['id']) or !is_numeric($_GET['id'])) {
	header("Location: index.php");
}

$id = mysql_real_escape_string($_GET['id']);

if(query_numrows("SELECT * FROM `klijenti` WHERE `klijentid` = '{$id}'") == 0)
{
	$_SESSION['msg-type'] = "error";
	$_SESSION['msg1'] = "Greška";
	$_SESSION['msg2'] = "Taj klijent ne postoji u bazi.";
	header("Location: klijenti.php");
	die();
}

$naslov = "Pregled klijent logova";
$fajl = "klijenti";

	$adjacents = 3;

	$query = "SELECT COUNT(*) as num FROM `logovi` WHERE `clientid` = '{$id}'";
	$total_stranas = mysql_fetch_array(mysql_query($query));
	$total_stranas = $total_stranas[num];
	
	$targetstrana = "klogovi.php?id=".$id; 	
	$limit = 30; 
	
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
	

	$sql = "SELECT * FROM `logovi` WHERE `clientid` = '{$id}' ORDER by `id` DESC LIMIT $start, $limit";
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


include("assets/header.php");
?>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Logovi - <a href="klijent.php?id=<?php echo $id; ?>">Nazad na klijenta</a></h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Poruka</th>
								<th>IP</th>
								<th>Vreme</th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($result) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema logova.</m></td></tr>';
							}
							while($row = mysql_fetch_array($result)) 
							{	
								$row['message'] = str_replace("gp-server.php", "srv-pocetna.php", $row['message']);
								
					?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo $row['message']; ?></td>
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
