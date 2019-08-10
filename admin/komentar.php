<?php
session_start();
include("konfiguracija.php");

if(isset($_SESSION['a_id']))
{

	$commentsnew = mysql_query("SELECT novo FROM `komentari` WHERE profilid = '".$_SESSION['a_id']."' and novo = '1'");
	$commentsnew = mysql_num_rows($commentsnew);
?>	
<a href="./mojprofil">
	<i class="icon-comment"></i>
	Komentari 
	<?php 	
	if($commentsnew > 0) {	
	?>
	<span class="label label-warning">
		<?php echo $commentsnew; ?>
	</span>
	<?php	
	}	
	?>
</a>	
<?php
}
?>
					