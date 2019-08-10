<header>
	<div id="top_bar">
		<div class="top_bar_vesti">
			<li><a href="">VESTI</a></li>
		</div>
		
		<div class="top_bar_info">

		
			<p>
			<marquee behavior="scroll" direction="right">
			                                <?php  
                                    $gp_obv = mysql_query("SELECT * FROM `vesti` ORDER BY `id`");

                                    while($row = mysql_fetch_array($gp_obv)) { 

                                        $naslov = htmlspecialchars(mysql_real_escape_string(addslashes($row['naslov'])));
                                        $porukaa = $row['poruka'];
										$poruka = strip_tags($porukaa);
                                        $datum = htmlspecialchars(mysql_real_escape_string(addslashes($row['datum'])));

                                    ?>
									
			<?php echo $poruka; ?><span> </span>
									<?php }?>
			</marquee>
			</p>
		</div>

		<div class="top_bar_flag" style="float: right;">
			<li><a href="/home?jezik=rs"><img src="/img/icon/flag/RS.png" alt=""></a></li>
			<li><a href="/home?jezik=de"><img src="/img/icon/flag/DE.png" alt=""></a></li>
			<li><a href="/home?jezik=en"><img src="/img/icon/flag/US.png" alt=""></a></li>
		</div>
	</div>
</header>
