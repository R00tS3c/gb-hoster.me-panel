<?php

die();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Allowed country SMS by Fortumo</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="keywords" content="gamehosting,gameserver,cs1.6,cs,gta,mc">
    <meta name="author" content="Muhamed Skoko (Kevia)">

    <link rel="shortcut icon" href="https://i.imgur.com/VdznoMT.png"> <!-- LOGO, ICON -->
   
    <!-- CSS BOOTSTRAP -->
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		
	<style>
		input:focus,textarea:focus,button:focus,select:focus,option:focus,a:focus {
		    outline: none;
		}

		select { color: #ccc!important; } 
		option { color: #000; }

		#popUP {
		    margin-top: 100px;
		    background: #fff;
		    border: 2px solid #000;
		    border-radius: 10px;
		    padding: 10px 15px;
		}

		.popUP li {
		    display: block;
		}

		.popUP button {
		    background: none;
		    border: 1px solid #043654;
		    margin: 15px 5px;
		    display: inline-block;
		    padding: 3px 10px;
		    float: left;
		}
		
		.jezik li {
			display: inline-block;
			padding: 10px;
		}
		
	</style>

</head>
<body>

	<?php
		header('Cache-control: private');

		include_once $_SERVER['DOCUMENT_ROOT']."/connect_db.php";

		if(isset($_SESSION['userid']))
			$UserName = userName($_SESSION['userid']);
		else
			$UserName = "Demo";
		
		if($UserName == "Ne mogu pronaci ime.")
			$UserName = "Demo";

		if (isset($_GET['jezik'])) {
			$lang = $_GET['jezik'];

			$_SESSION['jezik'] = $lang;

			setcookie("jezik", $lang, time() + (3600 * 24 * 30));
		} else if(isset($_SESSION['jezik'])) {
			$lang = $_SESSION['jezik'];
		} else if(isset($_COOKIE['jezik'])) {
			$lang = $_COOKIE['jezik'];
		} else {
			$lang = 'en';
		}

		switch ($lang) {
			case 'rs':
			$lang_file = 'lang.sr.php';
			break;

			case 'en':
			$lang_file = 'lang.en.php';
			break;

			case 'de':
			$lang_file = 'lang.de.php';
			break;

			default:
			$lang_file = 'lang.en.php';

		}

		include_once '../lang/'.$lang_file;

		if (!isset($_SESSION['jezik'])) { ?>
			
			<div id="jezik" style="padding: 0 20px;">
				<div class="jezik">

					<center>
						
						<h2>Molimo izaberite jezik</h2>

						<li><a href="index.php?jezik=rs"><img src="/img/icon/flag/RS.png" alt=""></a></li>
						<li><a href="index.php?jezik=de"><img src="/img/icon/flag/DE.png" alt=""></a></li>
						<li><a href="index.php?jezik=en"><img src="/img/icon/flag/US.png" alt=""></a></li>
					</center>
					
				</div>
			</div>

		<?php } else { ?>

			<div id="boost" style="padding: 0 20px;">
				<div class="boost">

					<h3><?php echo $jezik['uplata']; ?></h3>
					<li><?php echo $jezik['obv1']; ?></li>
					<li><?php echo $jezik['obv2']; ?></li>
					<li><?php echo $jezik['obv3']; ?></li>
					<li><?php echo $jezik['obv4']; ?></li>

					<hr />

					<strong><?php echo $jezik['drzave']; ?></strong>

					<br />

					<?php
			            $xml = simplexml_load_string( file_get_contents( "https://api.fortumo.com/api/services/2/7e2af7f35be7e034c98f258d06c33986.7f676737532ad9b60c5da63f4741cd97.xml" ) );
					?>

					<?php foreach( $xml->service->countries->country as $country ) { ?>
						<a data-toggle="modal" href="#<?php echo strtoupper ( $country[ 'code' ] ); ?>" style="text-decoration: none;">
							<img data-toggle="tooltip" data-placement="top" title="<?php echo $country[ 'name' ]; ?>" src="/img/icon/country/<?php echo strtoupper ( $country[ 'code' ] ); ?>.png">
						</a>
						
				        <div class="modal fade" id="<?php echo strtoupper ( $country[ 'code' ] ); ?>" role="dialog">
				            <div class="modal-dialog">
				                <div id="popUP"> 
				                    <div class="popUP">
				                    	<li style="text-align:center;float: right;">
		                                    <button type="button" data-dismiss="modal" loginClose="close"> x </button>
		                                </li>
				                        <fieldset>
			                                <h2><?php echo $jezik['upustvo']; ?></h2>
			                                <ul>
			                                	<h3><?php echo $jezik['primer']; ?> </h3>
			                                	<li>
			                                		<p>
			                                			<i><?php echo $jezik['primer']; ?>: </i>
			                                			<strong style="text-decoration: underline;">
			                                				<?php
			                                					echo $country->prices->price->message_profile[ "keyword" ].' '.$UserName.' '." | send to ".$country->prices->price->message_profile[ "shortcode" ];
			                                				?>
			                                			</strong>
			                                		</p>
			                                	</li>

			                                	<hr>

			                                	<h3><?php echo $jezik['info']; ?> </h3>

			                                    <li>
			                                        <p><?php echo $jezik['drzava'] ?>
			                                        	<strong>
			                                        		<?php echo $country[ "name" ]; ?> 
			                                        		<img src="/img/icon/country/<?php echo strtoupper($country['code']); ?>.png">
			                                        	</strong>
			                                        </p>
			                                        <p><?php echo $jezik['pare']; ?> 
			                                        	<strong>
			                                        		<?php echo $country->prices->price[ "amount" ]; ?> 
			                                        		<?php echo $country->prices->price[ "currency" ]; ?> &euro;
			                                        	</strong>
			                                        </p>
			                                        <p><?php echo $jezik['kod']; ?> 
			                                        	<strong><?php echo $country->prices->price->message_profile[ "keyword" ]; ?></strong>
			                                        </p>
			                                        <p><?php echo $jezik['send'] ?> 
			                                        	<strong>
			                                        		<?php echo $country->prices->price->message_profile[ "shortcode" ]; ?>
			                                        	</strong>
			                                        </p>
			                                        <p><?php echo $jezik['supp'] ?> <strong><?php echo $country->promotional_text->local; ?></strong></p>
			                                    </li>
			                                </ul>
			                            </fieldset>
				                    </div>        
				                </div>  
				            </div>
				        </div>
					<?php } ?>
				</div>
			</div>

		<?php } ?>

	<!-- JAVA :) -->
	<script>
		$(document).ready(function(){
		    $('[data-toggle="modal"]').tooltip();
		});
		$(document).ready(function(){
		    $('[data-toggle="tooltip"]').tooltip();
		});
	</script>
	
</body>
</html>