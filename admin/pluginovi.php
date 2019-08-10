<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled plugina";
$fajl = "plugini";

include("assets/header.php");

$obavestenja = mysql_query("SELECT * FROM `plugins` ORDER BY `ime`");
?>
	<div class="widget stacked widget-table action-table">
					
		<div class="widget-header">
			<i class="icon-th-list"></i>
			<h3>Lista plugina</h3>
		</div> <!-- /widget-header -->
				
		<div class="widget-content">
					
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="45px" class="tip" title="test">ID</th>
						<th>Ime</th>
						<th>Deskripcija</th>
						<th>Skracenica</th>
						<th style="width: 60px;">Text</th>
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
			?>
					<tr>
						<td>#<?php echo $row['id']; ?></td>
						<td><?php echo $row['ime']; ?></td>
						<td width="370px"><m><?php echo $row['deskripcija']; ?></m></td>
						<td><?php echo $row['prikaz']; ?></td>
						<td><?php echo $row['text']; ?></td>
						<td class="td-actions">
							<form action="process.php" method="POST">
								<input type="hidden" name="task" value="plugindel" />
								<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
								<button type="submit" class="btn btn-small btn-warning">
									<i class="btn-icon-only icon-remove"></i>										
								</button>
							</form>
							
							<button data-toggle="modal" href="#pluginedit" type="submit" class="btn btn-small btn-warning" style="float: left; margin-left: 10px;" onclick="plugin('<?php echo $row['id']; ?>', '<?php echo mysql_real_escape_string($row['ime']); ?>', '<?php echo mysql_real_escape_string($row['deskripcija']); ?>', '<?php echo mysql_real_escape_string($row['prikaz']); ?>', '<?php echo mysql_real_escape_string($row['text']); ?>')">
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
