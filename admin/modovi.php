<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled modova";
$fajl = "modovi";

include("assets/header.php");

$obavestenja = mysql_query("SELECT * FROM `modovi` ORDER BY `igra`, `ime`");
?>
	<div class="widget stacked widget-table action-table">
					
		<div class="widget-header">
			<i class="icon-th-list"></i>
			<h3>Lista modova</h3>
		</div> <!-- /widget-header -->
				
		<div class="widget-content">
					
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="45px" class="tip" title="test">ID</th>
						<th>Ime</th>
						<th>Opis</th>
						<th>Mapa</th>
						<th>Igra</th>
						<th style="width: 60px;">Akcija</th>
					</tr>
				</thead>
				<tbody>
			<?php	
					if(mysql_num_rows($obavestenja) == 0) {
						echo'<tr><td colspan="5"><m>Trenutno nema plugina za instalaciju.</m></td></tr>';
					}
					while($row = mysql_fetch_array($obavestenja)) 
					{
						if($row['igra'] == "1") $igraslika = "game-cs.png";
						else if($row['igra'] == "2") $igraslika = "game-samp.png";
						else if($row['igra'] == "3") $igraslika = "game-minecraft.png";						
						else if($row['igra'] == "4") $igraslika = "game-cod4.png";
						else if($row['igra'] == "5") $igraslika = "game-mta.png";
						else if($row['igra'] == "6") $igraslika = "game-teamspeak.png";
						else if($row['igra'] == "7") $igraslika = "game-fdl.png";
						else if($row['igra'] == "8") $igraslika = "game-sinusbot.png";
						else if($row['igra'] == "9") $igraslika = "game-fivem.png";

						
			?>
					<tr>
						<td>#<?php echo $row['id']; ?></td>
						<td><?php echo $row['ime']; ?></td>
						<td width="370px"><m><?php echo $row['opis']; ?></m></td>
						<td><?php echo $row['mapa']; ?></td>
						<td><img src="./assets/img/<?php echo $igraslika; ?>" /> <?php echo igra($row['igra']); ?></td>
						<td class="td-actions">
							<form action="process.php" method="POST">
								<input type="hidden" name="task" value="moddel" />
								<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
								<button type="submit" class="btn btn-small btn-warning">
									<i class="btn-icon-only icon-remove"></i>										
								</button>
							</form>
							
							<button data-toggle="modal" href="#modedit" type="submit" class="btn btn-small btn-warning" style="float: left; margin-left: 10px;" onclick="mod('<?php echo $row['id']; ?>', '<?php echo mysql_real_escape_string($row['ime']); ?>', '<?php echo mysql_real_escape_string($row['opis']); ?>', '<?php echo mysql_real_escape_string($row['putanja']); ?>', '<?php echo mysql_real_escape_string($row['igra']); ?>', '<?php echo mysql_real_escape_string($row['cena']); ?>', '<?php echo mysql_real_escape_string($row['mapa']); ?>', '<?php echo mysql_real_escape_string($row['sakriven']); ?>', '<?php echo mysql_real_escape_string($row['komanda']); ?>', '<?php echo mysql_real_escape_string($row['link']); ?>', '<?php echo mysql_real_escape_string($row['zipname']); ?>', '<?php echo mysql_real_escape_string($row['cena_premium']); ?>')">
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
include("assets/footer.php");
?>
