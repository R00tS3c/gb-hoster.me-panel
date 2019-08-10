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
                            <h2>PIN Code zaštita</h2>
                                    <p>Vas account je zasticen sa PIN kodom !</p>
                                    <p>Da biste pristupili ovoj opciji, potrebno je da ga unesete u box ispod.</p>
                                    <label>PIN KOD:</label>
                                    <input type="password" name="pin" value="" maxlength="5" class="short">
                        <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;"> 
                            <span class="fa fa-check-square-o"></span> Otkljucaj</button>
                        <button  class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;" type="button1" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                        </fieldset>
                    </form>
                </div>        
            </div>  
        </div>
    </div>
    <!-- KRAJ - PIN (POPUP) -->
    
    <?php if (is_pin() == true) { ?>
        <!-- Generisi novu FTP sifru (POPUP)-->
        <div class="modal fade" id="ftp-pw" role="dialog">
            <div class="modal-dialog">
                <div id="popUP"> 
                    <div class="popUP">
                        <?php
                            $get_pin_toket = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                            $_SESSION['pin_token'] = $get_pin_toket;

                            $get_new_pw = randomSifra(8);
                            $_SESSION['get_new_pw'] = $get_new_pw;
                        ?>
                        <form action="process.php?task=new_ftp_pw" method="post" class="ui-modal-form" id="modal-pin-auth">
                            <input hidden type="text" name="pin_token" value="<?php echo $get_pin_toket; ?>">
                            <input hidden type="text" name="server_id" value="<?php echo $server['id']; ?>">
                            <fieldset>
                                <h2>Generisi novu FTP lozniku</h2>
                                <ul>
                                    <li>
                                        <p>Dali ste sigurni da zelite da promenite FTP password?</p>
                                        <p>FTP password mozete menjat kad god hocete.</p>
                                    </li>
                                    <li>
                                        <label>FTP PASS PO ZELJI: </label>
                                        <input type="text" name="ftp_pw_kor" maxlength="8" class="short" placeholder="Za auto pw ostavite prazno" style="width: 200px;"> (max 8 karaktera) <br />
                                        <label>AUTO FTP PW: </label>
                                        <input disabled type="text" name="ftp_pw_gen" value="<?php echo $_SESSION['get_new_pw']; ?>" class="short">
                                    </li>
                                    <li style="text-align:center;">
                                        <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;"><span class="fa fa-check-square-o"></span> Promeni</button>
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

        <!-- Promeni ime servera (POPUP)-->
        <div class="modal fade" id="edit_name" role="dialog">
            <div class="modal-dialog">
                <div id="popUP"> 
                    <div class="popUP">
                        <?php
                            $get_pin_toket = $_SERVER['REMOTE_ADDR'].'_p_'.randomSifra(100);
                            $_SESSION['pin_token'] = $get_pin_toket;
                        ?>
                        <form action="process.php?task=edit_name_p" method="post" class="ui-modal-form" id="modal-pin-auth">
                            <input type="hidden" name="pin_token" value="<?php echo $get_pin_toket; ?>">
                            <input type="hidden" name="server_id" value="<?php echo $server['id']; ?>">
                            <fieldset>
                                <h2>Promena imena</h2>
                                <ul>
                                    <li>
                                        <p>Ovo ce promeniti ime samo u panelu!</p>
                                        <p>Promena nece biti aktivna na serveru!</p>
                                    </li>
                                    <li>
                                        <label>Ime:</label>
                                        <input type="text" name="ime_servera" class="short">
                                    </li>
                                    <li style="text-align:center;">
                                        <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;"> <span class="fa fa-check-square-o"></span> Sacuvaj</button>
                                        <button class="modalbtn" style="padding: 7px 15px;text-align: center;cursor: pointer;margin: 6px 1px 5px 1px;" type="button" data-dismiss="modal" loginClose="close"> <span class="fa fa-close"></span> Odustani </button>
                                    </li>
                                </ul>
                            </fieldset>
                        </form>
                    </div>        
                </div>  
            </div>
        </div>
        <!-- KRAJ - Promeni ime servera (POPUP) -->
    <?php } ?>

<?php } ?>