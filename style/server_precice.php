<div id="panelnav" style="
    margin-top: 11%;
    width: 605px;
    align-content: center;
    margin-left: 20%;">
<?php  
if ($server['igra'] == "1") { ?>
    <ul class="ServerInfoPrecice">
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs/" >Configs</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/plugins/" >Plugins</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/&fajl=server.cfg" >server.cfg</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs/&fajl=users.ini" >users.ini</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs/&fajl=plugins.ini" >plugins.ini</a>
        </li>  
    </ul>
<?php } elseif($server['igra'] == "2") { ?>
    <ul class="ServerInfoPrecice">
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/scriptfiles" style="font-size: 10px;">SCRIPTFILES</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/&fajl=server.cfg" style="font-size: 10px;">SERVER.CFG</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/&fajl=server_log.txt" style="font-size: 10px;">SERVER_LOG.TXT</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/gamemodes" style="font-size: 10px;">GAMEMODES</a>
        </li> 
    </ul>
<?php } elseif($server['igra'] == "3") { ?>
    <ul class="ServerInfoPrecice">
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>" >SERVER.PROPERTIES</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>" >PLUGINS</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>" >LOGS</a>
        </li>
    </ul>
<?php } elseif($server['igra'] == "4") { ?>
    <ul class="ServerInfoPrecice">
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs/" >Configs</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/plugins/" >Plugins</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike&file=server.cfg" >server.cfg</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs&file=users.ini" >users.ini</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs&file=plugins.ini" >plugins.ini</a>
        </li>  
    </ul>
<?php } elseif($server['igra'] == "5") { ?>
    <ul class="ServerInfoPrecice">
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs/" >Configs</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/plugins/" >Plugins</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike&file=server.cfg" >server.cfg</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs&file=users.ini" >users.ini</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/addons/amxmodx/configs&file=plugins.ini" >plugins.ini</a>
        </li>  
    </ul>
<?php } elseif($server['igra'] == "7") { ?>
    <ul class="ServerInfoPrecice">
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/" >Cstrike</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/models/" >Models</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/maps/" >Maps</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/sound/" >Sound</a>
        </li>
        <li>
            <a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/cstrike/sprites/" >Sprites</a>
        </li>
    </ul>
<?php } ?>
</div>