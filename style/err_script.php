<?php if(isset($_SESSION['ok'])) { echo "<div id='msg_bar_ok'><p>$_SESSION[ok]</p></div>"; ?>
	<script>
		setTimeout(function(){
		  	if ($('#msg_bar_ok').length > 0) {
		   		$('#msg_bar_ok').remove();
		  	}
		}, 3000);
	</script>
<?php unset($_SESSION['ok']); } elseif(isset($_SESSION['error'])) { echo "<div id='msg_bar_error'><p>$_SESSION[error]</p></div>"; ?>
	<script>
		setTimeout(function(){
	  		if ($('#msg_bar_error').length > 0) {
	   			$('#msg_bar_error').remove();
	  		}
		}, 3000);
	</script>
<?php unset($_SESSION['error']); } elseif(isset($_SESSION['info'])) { echo "<div id='msg_bar_info'><p>$_SESSION[info]</p></div>"; ?>
	<script>
		setTimeout(function(){
	  		if ($('#msg_bar_info').length > 0) {
	   			$('#msg_bar_info').remove();
	  		}
		}, 6000);
	</script>
<?php unset($_SESSION['info']); } ?>