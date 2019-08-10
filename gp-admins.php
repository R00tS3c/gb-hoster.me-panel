<?php
include 'connect_db.php';

if (is_login() == false) {
    $_SESSION['error'] = "Niste ulogovani.";
    header("Location: /home");
    die();
} else {
    $server_id = $_GET['id'];
    $proveri_server = mysql_num_rows(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));

    $server = mysql_fetch_array(mysql_query("SELECT * FROM `serveri` WHERE `id` = '$server_id' AND `user_id` = '$_SESSION[userid]'"));
    
    if (!$proveri_server) {
        $_SESSION['error'] = "Taj server ne postoji ili nemas ovlascenje za isti.";
        header("Location: /gp-home.php");
        die();
    }

    $info = mysql_fetch_array(mysql_query("SELECT * FROM `box` WHERE `boxid` = '$server[box_id]'"));
}

?>
<!DOCTYPE html>
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
<div id="wraper" style="/*background: rgba(0,0,0,0.7);*/ box-sizing: border-box; max-width: 1002px; color: #fff !important; /*margin: 0px 0;*/">
    <div id="ServerBox" style="border: 1px solid #ba0000; background: #000000b5;">
              <?php include("style/gpmenu.php"); ?>

				<div id="panelnav">
                <?php include('style/server_nav_precice.php'); ?>
                </div>
        <div id="server_info_infor">

            <div id="server_info_infor2">
                <!-- Server meni precice -->
                <div class="space" style="margin-top: 20px;"></div>

                <div id="ftp_container">
                    <div id="ftp_header" style="margin: 1px 1px 1px 35px;">
                        <div id="left_header">
                            <div>
                                <img src="/img/icon/gp/gp-admin.png" style="margin-left:10px;">
                            </div> 
						<h2 style="margin-left: 6%;margin-top: -5%;color: #ba0000;font-size: 20px;">Admini i slotovi</h2>
                        <h3 style="font-size: 12px;margin-top: -1%;margin-left: 6%;">Ovde mozete dodavati, brisati ili menjati trenutne admine i slotove na serveru.</h3>
                        <div class="space" style="margin-top: 60px;"></div>
                        </div>
                    </div>
                    <div class="space" style="margin-top: 60px;"></div>
                    <div class="supportAkcija" style="float: right;margin-top: -8%;margin-right: 4%;">
                            <a href="" class="btn" data-toggle="modal" data-target="#add-admin"><i class="fa fa-lock"></i> DODAJ ADMINA</a>
                    </div>              
                    <div id="plugin_body">
                        <?php  

                            $filename = "ftp://$server[username]:$server[password]@$info[ip]:21/cstrike/addons/amxmodx/configs/users.ini";
                            $contents = file_get_contents($filename);   

                            $fajla = explode("\n;", $contents);

                        ?>
                        <div id="serveri">
                            <center><table class="darkTable">
                                <tbody>
                                    <tr style="background: #ba000087;">
                                        <th>Nick/SteamID/IP</th>
                                        <th>Sifra (ako ima)</th>
                                        <th>Privilegije</th>
                                        <th>Vrsta</th>
                                        <th>Komentar</th>
                                    </tr>
                                    <?php 
                                        foreach($fajla as $sekcija) {
                                            $linije = explode("\n", $sekcija);
                                            array_shift($linije);
                                            
                                            foreach($linije as $linija) {
                                                $admin = explode('"',$linija);
                                                if(!empty($admin[1]) && !empty($admin[5])) { ?>
                                                    <tr>
                                                        <td><?php echo ispravi_text($admin[1]); ?></td>
                                                        <td><?php echo ispravi_text($admin[3]); ?></td>
                                                        <td><?php echo ispravi_text($admin[5]); ?></td>
                                                        <td><?php echo ispravi_text($admin[7]); ?></td>
                                                        <td><?php echo str_replace('//', '', ispravi_text($admin[8])); ?></td>
                                                    </tr>
                                                <?php }
                                            }
                                        }
                                    ?>                            
                                </tbody>
                            </table>
                        </center>
                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (is_login() == true) { ?>
        <!-- PIN (POPUP)-->
        <div class="modal fade" id="pin-auth" role="dialog">
            <div class="modal-dialog">
                <div id="popUP"> 
                    <div class="popUP">
                        <?php
                            $get_pin_toket = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                            $_SESSION['pin_token'] = $get_pin_toket;
                        ?>
                        <form action="process.php?task=un_lock_pin" method="post" class="ui-modal-form" id="modal-pin-auth">
                            <input type="hidden" name="pin_token" value="<?php echo $get_pin_toket; ?>">
                            <fieldset>
                                <h2>PIN Code zastita</h2>
                                <ul>
                                    <li>
                                        <p>Vas account je zasticen sa PIN kodom !</p>
                                        <p>Da biste pristupili ovoj opciji, potrebno je da ga unesete u box ispod.</p>
                                    </li>
                                    <li>
                                        <label>PIN KOD:</label>
                                        <input type="password" name="pin" value="" maxlength="5" class="short">
                                    </li>
                                    <li style="text-align:center;">
                                        <button> <span class="fa fa-check-square-o"></span> Otkljucaj</button>
                                        <button type="button" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </div>        
                </div>  
            </div>
        </div>
        <!-- KRAJ - PIN (POPUP) -->

        <!-- ADD ADMIN (POPUP)-->
        <div class="modal fade" id="add-admin" role="dialog">
            <div class="modal-dialog">
                <div id="popUP"> 
                    <div class="popUP">
                        <?php
                            $admin_token = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                            $_SESSION['admin_token'] = $admin_token;
                        ?>
                        <form action="process.php?task=add_admins" method="post" class="ui-modal-form" id="modal-pin-auth">
                            <input type="hidden" name="server_id" value="<?php echo $server['id']; ?>">
                            <input type="hidden" name="admin_token" value="<?php echo $admin_token; ?>">
                            <fieldset>
                                <h2>Dodavanje novog admina ili slota</h2>
                                        <label>Vrsta admina:</label>
                                        <select name="vrsta" id="vrsta" class="short" style="width: 175px;padding: 3px;">
                                            <option value="nick_admin">Nick+Sifra</option>
                                            <option value="steam_admin">SteamID+Sifra</option>
                                            <option value="ip_admin">IP adresa+Sifra</option>
                                        </select>
                                        <label>Nick/Steam/IP:</label>
                                        <input type="text" name="nick" class="short">
                                        <label>Sifra:</label>
                                        <input type="text" name="sifra" class="short">
                                        <label>Privilegije:</label>
                                        <select name="privilegije" id="privilegije" class="short" style="width: 175px;padding: 3px;">
                                            <option value="">Izaberi privilegiju</option>
                                            <option value="slot">Slot</option>
                                            <option value="slot_i">Slot+Imunitet</option>
                                            <option value="low_admin">Obican admin</option>
                                            <option value="ful_admin">Full admin</option>
                                            <option value="head">HEAD admin</option>
                                        </select>
                                        <br />
                                        <div id="panel" style="display: none;">
                                            *- "a" Imunity <br />
                                            *- "b" Slot <br />
                                            *- "c" amx_kick <br />
                                            *- "d" amx_ban i amx_unban <br />
                                            *- "e" amx_slay i amx_slap <br />
                                            *- "f" amx_map <br />
                                            *- "g" amx_cvar <br />
                                            *- "h" amx_cfg <br />
                                            *- "i" amx_chat i bela slova <br />
                                            *- "j" amx_vote i amx_votemap <br />
                                            *- "k" amx_cvar sv_password <br />
                                            *- "l" head admin <br />
                                            <label>Custom:</label>
                                            <input type="text" name="custom_flag" class="short" >
                                        </div>
                                        <label>Komentar:</label>
                                        <input type="text" name="komentar" class="short" >
                                        <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;"> 
                                            <span class="fa fa-check-square-o"></span> Dodaj admina
                                        </button>
                                        <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;" type="button" data-dismiss="modal" loginClose="close"> 
                                            <span class="fa fa-close"></span> Odustani 
                                        </button>
                            </fieldset>
                        </form>
                    </div>        
                </div>  
            </div>
        </div>
        <!-- KRAJ - ADD ADMIN (POPUP) -->
        
        <?php if (is_pin() == true) { ?>
            <!-- Generisi novu FTP sifru (POPUP)-->
            <div class="modal fade" id="ftp-pw" role="dialog">
                <div class="modal-dialog">
                    <div id="popUP"> 
                        <div class="popUP">
                            <?php
                                $get_pin_toket = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                                $_SESSION['pin_token'] = $get_pin_toket;
                            ?>
                            <form action="process.php?task=new_ftp_pw" method="post" class="ui-modal-form" id="modal-pin-auth">
                                <input type="hidden" name="pin_token" value="<?php echo $get_pin_toket; ?>">
                                <fieldset>
                                    <h2>Generisi novu FTP lozniku</h2>
                                    <ul>
                                        <li>
                                            <p>Dali ste sigurni da zelite da promenite FTP password?</p>
                                            <p>FTP password mozete menjat kad god hocete.</p>
                                        </li>
                                        <li>
                                            <label>NEW FTP PASS PO ZELJI: </label>
                                            <input type="text" name="ftp_pw_kor" class="short">
                                            <label>NEW AUTOMATCKI FTP PASS: </label>
                                            <input type="text" name="ftp_pw_gen" value="<?php echo randomSifra(10); ?>" class="short">
                                        </li>
                                        <li style="text-align:center;">
                                            <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;"> <span class="fa fa-check-square-o"></span> Promeni</button>
                                            <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;" type="button" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                                        </li>
                                    </ul>
                                </fieldset>
                            </form>
                        </div>        
                    </div>  
                </div>
            </div>
            <!-- KRAJ - Generisi novu FTP sifru (POPUP) -->
        <?php } ?>

    <?php } ?>

    <!-- FOOTER -->
    
    <?php include('style/footer.php'); ?>   

    <?php include('style/java.php'); ?>
    <script> 
        $(document).ready(function(){
            $("#custom").click(function(){
                $("#panel").slideToggle(100);
            });
        });
    </script>

</body>
</html>