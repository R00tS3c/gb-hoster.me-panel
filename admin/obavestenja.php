<?php
session_start();

include("konfiguracija.php");
include("includes.php");

samo_vlasnik($_SESSION['a_id']);

$naslov = "Pregled obavestenja";
$fajl = "obavestenja";

if(isset($_GET['view']))
{
	include("assets/header.php");
	
	if($_GET['view'] == "klijenti")
	{
		$obavestenja = mysql_query("SELECT * FROM `obavestenja` WHERE `vrsta` = '1'");
?>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista obavestenja</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Naslov</th>
								<th>Datum</th>
								<th class="td-actions"></th>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($obavestenja) == 0) {
								echo'<tr><td colspan="5"><m>Trenutno nema obavestenja.</m></td></tr>';
							}
							while($row = mysql_fetch_array($obavestenja)) 
							{	
							?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo $row['naslov']; ?></td>
								<td><?php echo vreme($row['datum']); ?></td>
								<td class="td-actions">
									<button onclick="izbrisi_obavestenje('<?php echo $row['id']; ?>')" type="submit" class="btn btn-small btn-warning">
										<i class="btn-icon-only icon-remove"></i>										
									</button>	
									<button href="#edit_obavestenje" data-toggle="modal" onclick="edit_obavestenje('<?php echo $row['id']; ?>', '<?php echo $row['naslov']; ?>', '<?php echo mysql_real_escape_string(htmlspecialchars($row['poruka'])); ?>')" class="btn btn-small btn-warning">
										<i class="btn-icon-only icon-edit"></i>										
									</button>	
								</td>								
							</tr>	
					<?php	}

					?>
							</tbody>
						</table>						
								
				</div> <!-- /widget-content -->
			
			</div> <!-- /widget -->		
<?php
	}
	else if($_GET['view'] == "all")
	{
	
	}
}
else
{
	$_SESSION['msg1'] == "Greska";
	$_SESSION['msg1'] == "Morate izabrati odredjenu kategoriju.";
	$_SESSION['msg-type'] == "error";
}
include("assets/footer.php");
?>
