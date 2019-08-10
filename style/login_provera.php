	<?php if (is_login() == false) { ?>
	<div id="panel">
                       <div id="inputs">
						<a style="position:absolute; margin:-27px 0 0 200px; " href="/register">Registrujte se</a>
						<a style="position:absolute;margin: -27px 0 0 35px;" href="/demo">DEMO
                         </a>
                        <form class="logform register min" method="post" action="/process.php?task=login">
                            <input type="text" size="10" name="username" placeholder="Username"/>
                            <input type="password" name="pass" placeholder="Password"/>
                            <input type="submit" class="btn right" value=""/>
                        </form>
                    </div>
					</div>
	<?php } else { ?>
	<div id="panel" style="width:325px;margin: 55px 0 0 200px;">
					<div class="av" style="margin-left:15%;margin-top:8%">
						<img src="<?php echo userAvatar($_SESSION['userid']); ?>" style="width:70px;height:70px;">
					</div>

					<div style="margin-left:50%;margin-top:-18%;">
						<li style="display:block;">
							<span class="glyphicon glyphicon-user" style="color:#bbb;"></span> 
							<span style="color: #fff;"><?php echo userIme($_SESSION['userid']); ?></span>
						</li>
						<li style="display:block;">
							<span class="glyphicon-envelope" style="color:#bbb;"></span> 
							<span style="color: #fff;"><?php echo userEmail($_SESSION['userid']); ?></span>
						</li>
						<li style="display:block;">
							<span class="glyphicon glyphicon-hdd" style="color:#bbb;"></span> 
							<span style="color: #fff;"><?php echo get_client_ip(); ?></span>
						</li>
						<li style="display:block;">
							<span class="glyphicon glyphicon-euro" style="color:#bbb;"></span> 
							<span style="color: #fff;"><?php echo userMoney($_SESSION['userid']); ?></span>
						</li>
						<div id="cgo" style="margin-left: -35px;display: inline-flex;margin-top: 5%;">
							<a href="/gp-settings.php"><div class="divbutton" style="margin-left: -5%;">EDIT</div></a>
							<a href="/client_process.php?task=logout"><div class="divbutton">LOGOUT</div></a>
						</div>	
						</div>
						</div>
	<?php } ?>
