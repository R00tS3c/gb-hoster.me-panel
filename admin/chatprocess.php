<?php
session_start();

//if($_SESSION['a_id'] == "2")
   //exit();



include "konfiguracija.php";
require('includes.php');

if(isset($_GET['chat_send'])) {
	$chat_text = mysql_real_escape_string(htmlspecialchars($_POST['chat_text']));
	
	if(strlen($chat_text) > 340) {
		exit();
	}

	if(strlen($chat_text) <= 1) {
		exit();
	}	
	
	$chat_autor = admin_ime($_SESSION['a_id']);
	
	$chat_text = htmlspecialchars($chat_text);
	
	// Zastita za Javascript, Html i ostalo.
	$zamene = array(
		':D' => '<img src="./assets/smajli/002.png" />',
		':P' => '<img src="./assets/smajli/104.png" />',
		'o.o' => '<img src="./assets/smajli/012.png" />',
		':)' => '<img src="./assets/smajli/001.png" />',
		':m' => '<img src="./assets/smajli/006.png" />',
		';)' => '<img src="./assets/smajli/003.gif" />',
		':O' => '<img src="./assets/smajli/004.png" />',
		'3:)' => '<img src="./assets/smajli/007.png" />',
		':$' => '<img src="./assets/smajli/008.png" />',
		':S' => '<img src="./assets/smajli/009.png" />',
		':(' => '<img src="./assets/smajli/010.png" />',
		';(' => '<img src="./assets/smajli/011.gif" />',
		'<3' => '<img src="./assets/smajli/015.png" />',
		'</3' => '<img src="./assets/smajli/016.png" />',
		'-.-' => '<img src="./assets/smajli/083.png" />',
		':ninja' => '<img src="./assets/smajli/086.png" />',
		':P' => '<img src="./assets/smajli/104.png" />',
		':T' => '<img src="./assets/smajli/tuga.gif" />',
		'picka' => '**cka',
		'kurac' => '**rac',
		'svinja' => '**inja',
		'stoka' => '**oka',
		'materina' => '***erina',
		'xD' => '<img src="./assets/smajli/xD.png" />');
		
	$chat_text = makeClickableLinks($chat_text);
		
	$chat_text = str_replace(array_keys($zamene), array_values($zamene), $chat_text);	
	
	
	
	//$chat_text = preg_replace('/\[url=(.+?)\](.+?)\[\/url\]/', '<a target="_blank" href="\1">\2</a>', $chat_text);
	
	$date = getdate(date("U"));
	$datum = "$date[mday] $date[month] $date[year], $date[hours]:$date[minutes]:$date[seconds]";
	
	$zamenee = array
	(
		'January' => 'Jan',
		'February' => 'Feb',
		'March' => 'Mar',
		'April' => 'Apr',
		'May' => 'Maj',
		'June' => 'Jun',
		'July' => 'Jul',
		'August' => 'Aug',
		'September' => 'Sep',
		'October' => 'Oct',
		'November' => 'Nov',	
		'December' => 'Dec'
	);	
	$datum = str_replace(array_keys($zamenee), array_values($zamenee), $datum);
	
	$lastactive = mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
	$lastactivename = mysql_query('UPDATE admin SET lastactivityname = "Chat" WHERE id="'.$_SESSION["a_id"].'"');
	
	// Zabranjuje prazan text da se ispise
	if(empty($chat_text)){}
	else{
		if(empty($chat_autor)){
		}else{
			if($chat_text == "undefined"){
			}else{
				mysql_query("INSERT INTO chat_messages VALUES('". $chat_text ."', '". $chat_autor ."', '". $datum ."', '', '" . $_SESSION['a_id'] . "')");
				
			}
		}	
	}	
}

if(isset($_GET['chat_refresh'])){
	$query = mysql_query("SELECT * FROM chat_messages ORDER BY ID DESC LIMIT 40");
	if (mysql_num_rows($query) == 0){
		echo '<li style="height: 35px;" id="cno"><m>Na chatu trenutno nema poruka!</m></li>';
	}
	while($row = mysql_fetch_assoc($query)){
		
		if(strpos($row['admin_id'], "klijent_") !== false)
		{
			$kid = explode("_", $row['admin_id']);
			$kid = $kid[1];
?>
		<li>
		<div id="maxchat">
			<img src="<?php echo user_avatar($kid); ?>" id="cavatar" />
			<span id="cautor" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo $row['Datum']; ?>">				
			<?php echo user_imesl($kid); ?>
			kaze: <?php echo $row['Text']; ?>
			</span>
			<div id="copcije">
			<a onclick="Chat_Izbrisi(<?php echo $row['ID']; ?>)">
				<i class="btn-icon-only icon-remove"></i>
			</a>
			</div>	
		</div>
		</li>
<?php
		}
		else
		{
?>
		<li>
		<div id="maxchat">
			<img src="<?php echo admin_avatar($row['admin_id']); ?>" id="cavatar" />
			<span id="cautor" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo $row['Datum']; ?>">				
			<?php echo admin_ime_l($row['admin_id']); ?>
			kaze: <?php echo $row['Text']; ?>
			</span>
			<div id="copcije">
			<a onclick="chat('<?php echo "@[reply]".admin_ime_c($row['admin_id'])."[/reply] "; ?>')">
				<i class="btn-icon-only icon-edit"></i>
			</a>
			<a onclick="Chat_Izbrisi(<?php echo $row['ID']; ?>)">
				<i class="btn-icon-only icon-remove"></i>
			</a>
			</div>	
		</div>
		</li>
<?php
		}
	}
}

if(isset($_GET['online_refresh'])){
	$PosMin = time() - 1 * 800;
	$query = mysql_query("SELECT * FROM admin WHERE `lastactivity` >= '".$PosMin."'");
	if (mysql_num_rows($query) == 0){
		echo '<li style="height: 35px;" id="cno"><m>Trenutno niko nije online!</m></li>';
	}
	while($row = mysql_fetch_assoc($query)){	
?>
		<li>
			<img src="<?php echo admin_avatar($row['id']); ?>" id="cavatar" />
			<span id="cautor">				
			<?php echo admin_ime_l($row['id']); ?>
			- <?php echo get_status($row['lastactivity']); ?>
			</span>
		</li>
<?php		
	}	
}

if(isset($_GET['clanovi_refresh'])){
	$PosMin = time() - 1 * 800;
	$query = mysql_query("SELECT * FROM klijenti WHERE `lastactivity` >= '".$PosMin."'");
	if (mysql_num_rows($query) == 0){
		echo '<li style="height: 35px;" id="cno"><m>Trenutno niko nije online!</m></li>';
	}
	while($row = mysql_fetch_assoc($query)){	
		echo '
		<li>
			<img src="' . user_avatar($row['klijentid']) . '" id="cavatar" />
			<span id="cautor">							
			' . user_imes($row['klijentid']) . '
			- ' . get_status($row['lastactivity']) . '
			</span>
		</li>';
		
		
	}	
}

if(isset($_GET['chat_delete_all'])){
	samo_vlasnik($_SESSION['a_id']);
	mysql_query("DELETE FROM `chat_messages`");
}

if(isset($_GET['chat_delete'])){
	samo_vlasnik($_SESSION['a_id']);
	$chatid = $_GET['chat_delete'];
	mysql_query('UPDATE admin SET lastactivity = "'.$_SERVER['REQUEST_TIME'].'" WHERE id="'.$_SESSION["a_id"].'"');
	mysql_query("DELETE FROM `chat_messages` WHERE ID = '".$chatid."'");
}

?>