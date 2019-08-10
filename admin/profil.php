<?php
session_start();

include("konfiguracija.php");
include("includes.php");

$naslov = "Promena profila";
$fajl = "profil";

// PROFIL INFO
$id = $_SESSION['a_id'];
$sql = "SELECT * FROM admin WHERE id = '$id'";
$res = mysql_query($sql) or die(mysql_error());
	
$podatke = mysql_fetch_assoc($res);

$signature = str_replace("<br />", "\n", $podatke['signature']);
$signature = str_replace("<b>", "[b]", $signature);
$signature = str_replace("</b>", "[/b]", $signature);
$signature = str_replace("<i>", "[i]", $signature);
$signature = str_replace("</i>", "[/i]", $signature);
$signature = str_replace("<u>", "[u]", $signature);
$signature = str_replace("</u>", "[/u]", $signature);
$signature = str_replace("&lt;br /&gt;", "\n", $signature);
$signature = str_replace("&lt;b&gt;", "[b]", $signature);
$signature = str_replace("&lt;/b&gt;", "[/b]", $signature);
$signature = str_replace("&lt;i&gt;", "[i]", $signature);
$signature = str_replace("&lt;/i&gt;", "[/i]", $signature);
$signature = str_replace("&lt;u&gt;", "[u]", $signature);
$signature = str_replace("&lt;/u&gt;", "[/u]", $signature);

include("assets/header.php");
?>
      <div class="row">
      	
      	<div class="span12">
      		
      		<div class="widget stacked">
					
				<div class="widget-header">
					<i class="icon-check"></i>
					<h3>Promena profila</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">
					
					<br />
					
					<form action="profil_process" method="post" id="validation-form"enctype="multipart/form-data">
						<input type="hidden" name="task" value="profil" />
						<fieldset>
						    <div class="control-group">
						      <label class="control-label" for="name">Korisnicko ime</label>
						      <div class="controls">
						        <input type="text" class="span10" name="username" id="username" value="<?php echo $podatke['username'] ?>">
						      </div>
						    </div>						
						    <div class="control-group">
						      <label class="control-label" for="name">Ime</label>
						      <div class="controls">
						        <input type="text" class="span10" name="ime" id="ime" value="<?php echo $podatke['fname'] ?>">
						      </div>
						    </div>
						    <div class="control-group">
						      <label class="control-label" for="name">Prezime</label>
						      <div class="controls">
						        <input type="text" class="span10" name="prezime" id="prezime" value="<?php echo $podatke['lname'] ?>">
						      </div>
						    </div>							
						    <div class="control-group">
						      <label class="control-label" for="email">Email Address</label>
						      <div class="controls">
						        <input type="text" class="span10" name="email" id="email" value="<?php echo $podatke['email'] ?>">
						      </div>
						    </div>
						    <div class="control-group">
						      <label class="control-label" for="signature">Signature - Mozete koristiti [b]TEXT[/b], [i]TEXT[/i], [u]TEXT[/u]</label>
						      <div class="controls">
								<textarea name="signature" id="signature" class="span10" rows="3"><?php echo $signature; ?></textarea>
						      </div>
						    </div>							
						    <div class="control-group">
						      <label class="control-label" for="email">Lozinka - Ostavi prazno polje ako zelis da ti ostane ista sifra</label>
						      <div class="controls">
						        <input type="text" class="span10" name="password" id="password">
						      </div>
						    </div>
							<div class="control-group">
							   <label class="control-label" for="avatar">Avatar</label>
							   <div class="controls">
								 <input type="file" name="avatar" size="1" class="span10" id="avatar">
							   </div>
							</div>
				          
						    <div class="form-actions">
						      <button type="submit" class="btn btn-danger btn">Promeni</button>&nbsp;&nbsp;
						      <button type="reset" class="btn">Otkazi</button>
						    </div>
							
						  </fieldset>
						</form>
					
				</div> <!-- /widget-content -->
					
			</div> <!-- /widget -->					
			
	    </div> <!-- /span12 -->     
      	
      </div> <!-- /row -->
<?php
include("assets/footer.php");
?>