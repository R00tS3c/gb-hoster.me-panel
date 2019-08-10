<ul>
<?php if ($server['igra'] == "1") { ?>
    <li><a href="gp-info.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-admins.php?id=<?php echo $server['id']; ?>">Admini i slotovi</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>">WebFTP</a></li>
    <li><a href="gp-plugins.php?id=<?php echo $server['id']; ?>">Plugini</a></li>
    <li><a href="gp-mods.php?id=<?php echo $server['id']; ?>">Modovi</a></li>
    <li><a href="gp-console.php?id=<?php echo $server['id']; ?>">Konzola</a></li>
    <li><a href="gp-boost.php?id=<?php echo $server['id']; ?>">Boost</a></li>
    <li><a href="gp-autorestart.php?id=<?php echo $server['id']; ?>">Autorestart</a></li>
<?php } else if ($server['igra'] == "2") { ?>
    <li><a href="gp-info.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>&path=/&fajl=server.cfg">Podesavanje</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>">WebFTP</a></li>
    <li><a href="gp-mods.php?id=<?php echo $server['id']; ?>">Modovi</a></li>
    <li><a href="gp-console.php?id=<?php echo $server['id']; ?>">Konzola</a></li>
    <li><a href="gp-autorestart.php?id=<?php echo $server['id']; ?>">Autorestart</a></li>
<?php } else if ($server['igra'] == "3") { ?>
    <li><a href="gp-info.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-admins.php?id=<?php echo $server['id']; ?>">Admini i slotovi</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>">WebFTP</a></li>
    <li><a href="gp-plugins.php?id=<?php echo $server['id']; ?>">Plugini</a></li>
    <li><a href="gp-mods.php?id=<?php echo $server['id']; ?>">Modovi</a></li>
    <li><a href="gp-console.php?id=<?php echo $server['id']; ?>">Konzola</a></li>
    <li><a href="gp-boost.php?id=<?php echo $server['id']; ?>">Boost</a></li>
    <li><a href="gp-autorestart.php?id=<?php echo $server['id']; ?>">Autorestart</a></li>
<?php } else if ($server['igra'] == "4") { ?>
    <li><a href="gp-info.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-admins.php?id=<?php echo $server['id']; ?>">Admini i slotovi</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>">WebFTP</a></li>
    <li><a href="gp-plugins.php?id=<?php echo $server['id']; ?>">Plugini</a></li>
    <li><a href="gp-mods.php?id=<?php echo $server['id']; ?>">Modovi</a></li>
    <li><a href="gp-console.php?id=<?php echo $server['id']; ?>">Konzola</a></li>
    <li><a href="gp-boost.php?id=<?php echo $server['id']; ?>">Boost</a></li>
    <li><a href="gp-autorestart.php?id=<?php echo $server['id']; ?>">Autorestart</a></li>
<?php } else if ($server['igra'] == "5") { ?>
    <li><a href="gp-info.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-admins.php?id=<?php echo $server['id']; ?>">Admini i slotovi</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>">WebFTP</a></li>
    <li><a href="gp-plugins.php?id=<?php echo $server['id']; ?>">Plugini</a></li>
    <li><a href="gp-mods.php?id=<?php echo $server['id']; ?>">Modovi</a></li>
    <li><a href="gp-console.php?id=<?php echo $server['id']; ?>">Konzola</a></li>
    <li><a href="gp-boost.php?id=<?php echo $server['id']; ?>">Boost</a></li>
    <li><a href="gp-autorestart.php?id=<?php echo $server['id']; ?>">Autorestart</a></li>
<?php } else if ($server['igra'] == "7") { ?>
    <li><a href="gp-fdlinfo.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-config.php?id=<?php echo $server['id']; ?>">Podesavanje</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>">WebFTP</a></li>
<?php } else if ($server['igra'] == "6") { ?>
    <li><a href="gp-voiceinfo.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-perm.php?id=<?php echo $server['id']; ?>">Permissions</a></li>
    <li><a href="gp-bans.php?id=<?php echo $server['id']; ?>">Bans</a></li>
	<?php include('style/s_s_r_r_k_ts.php'); ?>
<?php  } else if($server['igra'] == "9") { ?>
    <li><a href="gp-info.php?id=<?php echo $server['id']; ?>">Server</a></li>
    <li><a href="gp-webftp.php?id=<?php echo $server['id']; ?>">WebFTP</a></li>
    <li><a href="gp-mods.php?id=<?php echo $server['id']; ?>">Modovi</a></li>
    <li><a href="gp-console.php?id=<?php echo $server['id']; ?>">Konzola</a></li>
    <li><a href="gp-autorestart.php?id=<?php echo $server['id']; ?>">Autorestart</a></li>
<?php } ?>
<?php if ($server['igra'] != "7" && $server['igra'] != "6") include('style/s_s_r_r_k.php'); ?>
</ul>