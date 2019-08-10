<div id="bilten">
	<img src="/img/icon/support-icon.png">
	<div id="text_container">
		<h2><span style="color:#a60000">GB-Hoster.me</span> Bilten</h2>
		<p>prijavite se na nas bilten i dobijate najnovije informacije vezane za sam hosting...</p>
		<p>Poruke od naseg Biltena <a href="#" data-toggle="modal" data-target="#edit-bilten" style="text-decoration: none;color:#a60000">možete onemogućiti</a> u bilo kojem trenutku!</p>
	</div>
	<div id="input_container">
		<?php
            $get_pin_toket = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
            $_SESSION['token_email'] = $get_pin_toket;
        ?>
		<form action="process.php?task=bilten_email" method="POST">
			<input type="hidden" name="token_email" value="<?php echo $get_pin_toket; ?>">
			<button>PRIJAVI</button>
			<input type="email" name="email" placeholder="Unesi e-mail">
		</form>					
		<button style="background:none;border:none;color:#fff;" type="button" data-toggle="modal" data-target="#edit-bilten">
			<span class="fa fa-edit"></span> Izbrisi moj email.
		</button>
	</div>
</div>