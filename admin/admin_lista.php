<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Pregled admin liste";
$fajl = "admin_lista";

$admin_lista = mysql_query("SELECT * FROM `admin` ORDER BY status, id ASC") or die(mysql_error());

include("assets/header.php");
?>
			<div class="widget stacked widget-table action-table">
					
				<div class="widget-header">
					<i class="icon-th-list"></i>
					<h3>Lista admina</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="45px" class="tip" title="test">ID</th>
								<th>Ime i prezime</th>
								<th>Username</th>
								<th>E-mail</th>
								<th>Zadnja akcija</th>
								<th>Status</th>
								<?php if(vlasnik($_SESSION['a_id'])) { ?>
								<th class="td-actions">Akcije</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
					<?php	
							if(mysql_num_rows($admin_lista) == 0) {
								echo'<tr><td colspan="5"><m>asd.</m></td></tr>';
							}
							while($row = mysql_fetch_array($admin_lista)) {	
							?>
							<tr>
								<td>#<?php echo $row['id']; ?></td>
								<td><?php echo admin_ime_p_l($row['id']); ?></td>
								<td><m><?php echo $row['username'] ?></m></td>
								<td><?php echo $row['email']; ?></td>
								<td><m><?php echo $row['lastactivityname']; ?> - pre <?php echo time_elapsed_A($nowtime-$row['lastactivity']); ?></m>
								<td><?php echo admin_status($row['id']); ?></m></td>
								<td class="td-actions">
									<?php if(vlasnik($_SESSION['a_id'])) { ?>
									<a href="admin_pregled.php?id=<?php echo $row['id']; ?>">
									<button type="submit" class="btn btn-small btn-warning">
										<i class="btn-icon-only icon-edit"></i>									
									</button>
									</a>
									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="izbrisi_admina" />
										<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-remove"></i>										
										</button>
									</form>

									<form action="process.php" method="POST">
										<input type="hidden" name="task" value="promeni_profil" />
										<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
										<input type="hidden" name="username" value="<?php echo $row['username']; ?>" />
										<button type="submit" class="btn btn-small btn-warning">
											<i class="btn-icon-only icon-arrow-right"></i>										
										</button>
									</form>
									<?php } ?>
									</a>
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
