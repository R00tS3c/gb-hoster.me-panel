<?php

include_once ("connect_db.php");

?>
<!DOCTYPE html>
<html>
<?php include('style/head.php'); ?>
<body>
    <!-- Error script -->
    <?php include('style/err_script.php'); ?>
    
    <!-- HEADER BOX -->

    <?php include('style/header.php'); ?>

    <!-- LOGIN BOX --> 

    <?php include('style/login_provera.php'); ?>

    <div id="space" style="margin-top: 200px;"></div>

    <!-- NAV BOX -->

    <?php include('style/navigacija.php'); ?>

	<!-- SLAJDER I STATISTIKA -->

	<!-- BANER -->
	
	<?php include('style/banner.php'); ?>

	<!-- OSTALOOO -->
	
	<article>
		<?php

			define("access", 1);

			error_reporting(0);

			if($_GET['page'] == "naruci") {
			   include("naruci.php");
			}
			if($_GET['page'] == "info") {
			   include("info.php");
			}

		?>
	</article>

	<!-- izbrisi_email (POPUP)-->
    <div class="modal fade" id="edit-bilten" role="dialog">
        <div class="modal-dialog">
            <div id="popUP"> 
                <div class="popUP">
                    <?php
                        $get_pin_toket = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                        $_SESSION['token'] = $get_pin_toket;
                    ?>
                    <form action="process.php?task=edit_bilten" method="post" class="ui-modal-form" id="modal-pin-auth">
                        <input type="hidden" name="token" value="<?php echo $get_pin_toket; ?>">
                        <fieldset>
                            <h2>Promena biltena</h2>
                            <ul>
                                <li>
                                    <p>Ovim cete onemoguciti dolazenje nasih obavestenja na vas email.</p>
                                    <p>Promenu vazda mozete promeniti!</p>
                                </li>
                                <li>
                                    <label>Email:</label>
                                    <input type="email" name="email" placeholder="Molimo unesite vas email." class="short">
                                </li>
                                <li style="text-align:center;">
                                    <button> <span class="fa fa-check-square-o"></span> Sacuvaj</button>
                                    <button type="button" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                                </li>
                            </ul>
                        </fieldset>
                    </form>
                </div>        
            </div>  
        </div>
    </div>
    <!-- KRAJ - izbrisi_email (POPUP) -->
		
	<div class="space" style="margin-top: 30px;"></div>

    <!-- FOOTER -->
    
    <?php include('style/footer.php'); ?>   

    <?php include('style/java.php'); ?>

</body>
</html>