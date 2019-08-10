<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled Narucenih Servera";
$fajl = "billings";

$adjacents = 3;

$query = "SELECT COUNT(*) as num FROM `billing`";
$total_stranas = mysql_fetch_array(mysql_query($query));
$total_stranas = $total_stranas[num];

$targetstrana = "billings.php";
$limit = 15; 	

if(empty($_GET['strana'])) {
	$start = 0;	
	$strana = 1;
	} else if(!isset($_GET['strana'])) {
	$start = 0; 	
	$strana = 0;
} else{
	$start = ($_GET['strana'] - 1) * $limit; 
	$strana = $_GET['strana'];
}

$sql = "SELECT * FROM `billing` ORDER by `id` ASC LIMIT $start, $limit";
$result = mysql_query($sql);

if ($strana == 0) $strana = 1;					
$prev = $strana - 1;							
$next = $strana + 1;							
$laststrana = ceil($total_stranas/$limit);		
$lpm1 = $laststrana - 1;						

$pagination = "";
if($laststrana > 1) {	
	$pagination .= "<div class=\"pagination\" style=\"margin-left: 10px;\"><ul>";
	if ($strana > 1) 
		$pagination.= "<li><a href=\"$targetstrana?strana=$prev\">«</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>«</a></li>";
	
	if ($laststrana < 7 + ($adjacents * 2))	{	
		for ($counter = 1; $counter <= $laststrana; $counter++)	{
			if ($counter == $strana)
				$pagination.= "<li><a class=\"active\">$counter</a></li>";
			else
				$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
		}
	} else if($laststrana > 5 + ($adjacents * 2)) {
		if($strana < 1 + ($adjacents * 2)) {
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana?strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana?strana=$laststrana\">$laststrana</a></li>";		
		} else if($laststrana - ($adjacents * 2) > $strana && $strana > ($adjacents * 2)) {
			$pagination.= "<li><a href=\"$targetstrana?strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana?strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $strana - $adjacents; $counter <= $strana + $adjacents; $counter++) {
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
			}
			$pagination.= "<li><a>...</a></li>";
			$pagination.= "<li><a href=\"$targetstrana?strana=$lpm1\">$lpm1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana?strana=$laststrana\">$laststrana</a></li>";		
		} else {
			$pagination.= "<li><a href=\"$targetstrana?strana=1\">1</a></li>";
			$pagination.= "<li><a href=\"$targetstrana?strana=2\">2</a></li>";
			$pagination.= "<li><a>...</a></li>";
			for ($counter = $laststrana - (2 + ($adjacents * 2)); $counter <= $laststrana; $counter++) {
				if ($counter == $strana)
					$pagination.= "<li><a class=\"active\">$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetstrana?strana=$counter\">$counter</a></li>";					
			}
		}
	}
	
	if ($strana < $counter - 1)
		$pagination.= "<li><a href=\"$targetstrana?strana=$next\">»</a></li>";
	else
		$pagination.= "<li class=\"disabled\"><a>»</a></li>";
	$pagination.= "</ul></div>\n";	
}

include("assets/header.php");
?>
	<div class="main">
		<div class="main-inner">
			<div class="container">
				<div class="row">

					<div class="span12">
						<h1>
							<span class="icon-user"></span> Lista Naručenih Servera
						</h1>
						<hr>
					</div>

					<div class="span12">
						<div class="widget widget-table action-table">
							<div class="widget-header"> <i class="icon-th-list"></i>
								<h3>Lista Naručenih Servera</h3>
							</div>

							<div class="widget-content">
								<table class="table table-striped table-bordered tabela-asd">
									<thead>
										<tr>
											<th>ID</th>
											<th>Naručio</th>
											<th>Igra</th>
											<th>Lokacija</th>
											<th>Cena</th>
											<th>Način Plaćanja</th>
											<th>Status</th>
											<th>Akcija</th>
										</tr>
									</thead>
									<tbody>
										<?php	
										if(mysql_num_rows($result) == 0) {
											echo'<tr><td colspan="5"><m>Tabela je prazna.</m></td></tr>';
										}
										while($row = mysql_fetch_array($result)) {
											$lokacija = $row['lokacija'];
											if($lokacija == 1) {
												$lokacija = "Lite - Njemacka";
											} else if($lokacija == 2) {
												$lokacija = "Lite - Poljska";
											} else if($lokacija == 3) {
												$lokacija = "Lite - Francuska";
											} else if($lokacija == 4) {
												$lokacija = "Premium - Srbija";
											} else if($lokacija == 5) {
												$lokacija = "Premium - BiH";
											} else {
												$lokacija = "Lite - Njemacka";
											}
											
                                            $pay_status = $row['BillingStatus'];
											
                                            if ($pay_status == "0") {
                                                $pay_status = "Na čekanju!";
											} else if ($pay_status == "1") {
                                                $pay_status = "Uplaćeno!";
                                            } else if ($pay_status == "2") {
                                                $pay_status = "Uplaćeno!";
                                            }
											$klijent = query_fetch_assoc("SELECT * FROM `klijenti` WHERE `klijentid` = '$row[klijentid]'");
											?>
											<tr>
												<td><a href="klijent.php?id=<?php echo $row['klijentid']; ?>"><m>#<?php echo $row['klijentid']; ?></m></a></td>
												<td><a href="klijent.php?id=<?php echo $row['klijentid']; ?>"><m><?php echo $klijent['ime'].' '.$klijent['prezime']; ?></m></a></td>
												<td><?php echo $row['game']; ?></td>
												<td><?php echo $lokacija; ?></td>
												<td><?php echo $row['iznos']; ?> €</td>
												<td><?php echo $row['paytype']; ?></td>
												<td><?php echo $pay_status; ?></td>
												<td>SOON!</td>
											</tr>	
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<?php echo $pagination; ?>
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->		
<?php
include("assets/footer.php");
?>