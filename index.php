<?php
include_once("connect_db.php");
header('Content-Type: charset=UTF-8');

?>



<html xmlns="http://www.w3.org/1999/xhtml">
<?php include ("assets/php/head.php"); ?>
<?php include('style/err_script.php'); ?>
<body>
		    <div id="content">
        <div id="TOP">
            <div id="header">
                <a id="logo" href="/"></a>
<?php include ("style/login_provera.php"); ?>
	            </div>
<?php include ("style/navigacija.php"); ?>
        </div>
        <div id="rotacalc">
            <div id="rotator">
              <div id="bgrot" class="stepcarousel">
                <div class="belt" style="width: 587px; left: 0px;">
                     <div class="panel">
                        <div id="rot-1">
                            <div id="title">
                                <div id="left">
                                <h3>Counter Strike 1.6 2019</h3>
Skinite Counter Strike 1.6 2019 zaštićen od svih slowhackova
                                </div>
                                <a id="right" href="http://cs.gb-hoster.me/"></a>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div id="rot-2">
                            <div id="title">
                                <div id="left">
                                <h3>Counter Strike Global Offenssive</h3>
                                Uskoro u ponudi Counter Strike Global Offenssive
                                </div>
                                <a id="right" href="https://www.facebook.com/gbhosterme/"></a>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
			<div id="calc">
				<div id="left"><?php echo $jezik['2']; ?></div>
				<select id="cgame" class="gameList">
				</select>
				<div id="left" style="display:none;"><?php echo $jezik['3']; ?></div>
				<select style="display:none;" id="cgame" class="locList">
				</select>
				<div id="left"><?php echo $jezik['3']; ?></div>
				<select id="cgame" class="typeList">
				</select>
				<div id="left"><?php echo $jezik['4']; ?></div>
				<div id="cgo">
				<select id="cslots" class="slotList">
				</select>
				<a href="#"></a>
				</div>
			</div>

                    </div>
                    <div id="wraper" style="background: rgba(0,0,0,0.7); box-sizing: border-box; max-width: 1002px; color: #fff !important; margin: 15px 0;">
            <div id="game">
                <div id="title"><i class="fa fa-gamepad" aria-hidden="true"></i>&nbsp;&nbsp;COUNTER STRIKE 1.6</div>
                      
                <div id="game-bg" class="cs">
                    <a href="naruci.php?game=1"></a>
                </div>
            </div>
            <div id="game">
                <div id="title"><i class="fa fa-gamepad" aria-hidden="true"></i>&nbsp;&nbsp;COUNTER STRIKE: GLOBAL OFFENSIVE</div>
                <div id="game-bg" class="csgo">
                    <a href="naruci.php?game=7"></a>
                </div>
            </div>
            <div id="game">
                <div id="title"><i class="fa fa-gamepad" aria-hidden="true"></i>&nbsp;&nbsp;GRAND THEFT AUTO: SAN ANDREAS</div>
                <div id="game-bg" class="samp">
                    <a href="naruci.php?game=2"></a>
                </div>
            </div>
            <div id="game">
                <div id="title"><i class="fa fa-gamepad" aria-hidden="true"></i>&nbsp;&nbsp;MINECRAFT</div>
                <div id="game-bg" class="mc">
                    <a href="naruci.php?game=3"></a>
                </div>
            </div>
            <div id="game">
                <div id="title"><i class="fa fa-microphone" aria-hidden="true"></i>&nbsp;&nbsp;TEAMSPEAK 3</div>
                <div id="game-bg" class="ts3">
                    <a href="naruci.php?game=6"></a>
                </div>
            </div>
            <div id="game">
                <div id="title"><i class="fa fa-music" aria-hidden="true"></i>&nbsp;&nbsp;SINUSBOT</div>
                <div id="game-bg" class="bot">
                    <a href="naruci.php?game=10"></a>
                </div>
            </div>
            <div id="game">
                <div id="title"><i class="fa fa-download" aria-hidden="true"></i>&nbsp;&nbsp;FAST DOWNLOAD</div>
                <div id="game-bg" class="fdl">
                    <a href="naruci.php?game=11"></a>
                </div>
            </div>
            <div id="game">
                <div id="title"><i class="fa fa-server" aria-hidden="true"></i>&nbsp;&nbsp;VIRTUAL PRIVATE SERVER</div>
                <div id="game-bg" class="vps">
                    <a href="https://www.facebook.com/gbhosterme/"></a>
                </div>
            </div>
        <?php include ("style/footer.php"); ?>

        </div>
</div>
<script>
$('.stepcarousel').carousel({
  interval: 5000
})
</script>
</body></html>
