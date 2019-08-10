<?php
include('./fnc/ostalo.php');

if (is_login() == true) {
    header("Location: /home");
    die();
} else {
    
}

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
<div id="wraper" style="background: rgba(0,0,0,0.7); box-sizing: border-box; max-width: 1002px; color: #fff !important; margin: 0px 0;">
                                <a href="/home"><img style="margin-top: 10%;margin-left:1%;" src="https://i.imgur.com/VdznoMT.png" alt=""></a>
        <div id="server_info_infor" style="
    float: right;
    /* margin-left: -23%; */
    margin-right: 40%;
">    
                <div class="space" style="margin-top: 20px;"></div>

                                        <div class="tiket_info_home">
                                            
                                            <div class="inputs" id="register" style="float:left;">
                                                <form action="client_process.php?task=register" method="POST" autocomplete="off">
                                                    
                                                    <input type="email" name="email" placeholder="email" required="">
													<br>
                                                    <input type="text" name="ime" placeholder="ime" required="">
													<br>
                                                    <input type="text" name="prezime" placeholder="prezime" required="">
<br>
                                                    <input type="text" name="username" placeholder="Korisnicko ime" required=""> <br />
                                                    <br>
													<input type="password" name="pass" placeholder="Password" required=""> <br />
                                                    <br>
													<input type="password" name="pass2" placeholder="Ponovite password" required=""> <br />
                                                    <br>
                                                    <?php
                                                        $sig_kod_ac = randomSifra(5);
                                                        $_SESSION['sig_kod_ac'] = $sig_kod_ac;
                                                    ?>
													<br>
                                                    <label for="sig_kod_c_ac">
                                                        <input disabled type="text" name="sig_kod_p" value="<?php echo $sig_kod_ac; ?>" required="">
                                                    </label>
                                                    <br>
                                                    <input id="sigkod" type="text" name="sig_kod_c_ac" style="width: 175px;" placeholder="Sig kod" required="">
                                                    
                                                    <br/>

                                                    <button style="margin-left: 8%;margin-top: 15%;">REGISTRUJ ME!</button>
                                                </form>
                                                  <div class="space" style="margin-top: 20px;"></div>

                                            </div>
                                        </div>
                                    </div>
                      
  </div>
   <div id="foter">
    <div id="copy">© 2019 by GB-Hoster.me
    </div>
    <div id="dev"><div style="font-size:11px;">KODIRAO</div><a href="https://github.com/CikerDeveloper" target="_blank" >RootSec</a>
    </div>
</div>    
   </div>

    
    <script>
        window.onload = function() {
            var sigkod = document.getElementById('sigkod');
            sigkod.onpaste = function(e) {
                e.preventDefault();
            }
        }
    </script>

</body>
</html>