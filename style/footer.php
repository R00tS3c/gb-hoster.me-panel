            <div id="news">
                <div id="title">NOVOSTI</div>
                                    <div id="news1">
                        <div id="title">TS3 SERVERI SU U PONUDI!</div>
                        <a id="readmore" href="#"></a>
                    </div>
                                    <div id="news1">
                        <div id="title">Dođite na naš TS3 svaki klan dobija sobu g4all.ml!</div>
                        <a id="readmore" href="#"></a>
                    </div>
                                    <div id="news1">
                        <div id="title">Plaćanja PayPal, Uplatnica (BiH, Srb), SMS, PaySafeCard</div>
                        <a id="readmore" href="#"></a>
                    </div>
                            </div>
            <div id="stats">
                <div id="title"><?php echo $jezik['5']; ?></a></div>
                <div id="wrap">
                                <div id="st">
                                    <div id="left"><?php echo $jezik['6']; ?></div>
                                    <div id="right">
                                        <div id="match"><?php echo mysql_num_rows($stats_masine); ?></div>
                                        <div id="bgmatch">
                                            <div style="width:<?php echo mysql_num_rows($stats_masine) * 3; ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
								<?php
								$percent = mysql_num_rows($stats_servera_aktivnih)/mysql_num_rows($stats_server);
								$width = number_format( $percent * 100, 2 ) . '%';
								?>
                                <div id="st">
                                    <div id="left"><?php echo $jezik['7']; ?></div>
                                    <div id="right">
                                        <div id="match"><?php echo mysql_num_rows($stats_servera_aktivnih); ?> / <?php echo mysql_num_rows($stats_server); ?></div>
                                        <div id="bgmatch">
                                            <div style="width:<?php echo $width;?>;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="st">
                                    <div id="left"><?php echo $jezik['8']; ?></div>
                                    <div id="right">
                                        <div id="match"><?php echo mysql_num_rows($stats_klijenti); ?></div>
                                        <div id="bgmatch">
                                            <div style="width:<?php echo mysql_num_rows($stats_klijenti) * 3; ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                    <!---<a href="#"></a> --->
                </div>
            </div>
        </div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div id="foter">
    <div id="copy">© 2019 by GB-Hoster.me
    </div>
    <div id="dev"><div style="font-size:11px;">DEVELOPER</div><a href="https://github.com/R00tS3c" target="_blank" >RootSec</a>
    </div>
</div>