<?php
require ("connect_db.php");
require ("inc/libs/telnet.class.php");


if (($_GET['task']=="start") || ($_GET['task']=="stop") || ($_GET['task']=="restart")) {

    $serverid = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
    $clientid = $_SESSION['userid'];

    $server = $gpanel->getServer($serverid, $clientid);
    if ($server['serverid']=="") $gpanel->izbaciGresku(_ERROR_SERVER_NO_ACCESS);
    $voice = mysql_fetch_array($mysql->query("SELECT * FROM `voiceservers` WHERE `voiceid`='{$server['voiceid']}'"));
    if ($voice['voiceid']=="") $gpanel->izbaciGresku("Nepostojeci master server! Prijavite ovaj problem podrsci!");

    if ($server['status'] != "Active") $gpanel->serverGreska($server['serverid'], _ERROR_SERVER_SUSPENDED_NO_ACCESS,"gp-voiceserver.php");

    $sid = (int)$server['cfg1'];
    $ok = true;
    if (!$sid) {
        $ok = false;
        $notification = "Greska! TS3 server mora da ima server id u cfg1 !";
    } else if ($_GET['task'] == "start") {
        $sql = "UPDATE server SET online='Started' WHERE serverid='{$server['serverid']}'";
        $log_sql = "Server Started: <a href=\"serversummary.php?id={$server['serverid']}\">{$server['name']}</a> (Client)";
        $akcija = "started";

        $komanda = "serverstart sid={$sid}";
        $autostart = "serveredit virtualserver_autostart=1";
    } else if ($_GET['task'] == "stop") {
        $sql = "UPDATE server SET online='Stopped' WHERE serverid='{$server['serverid']}'";
        $log_sql = "Server Stopped: <a href=\"serversummary.php?id={$server['serverid']}\">{$server['name']}</a> (Admin)";
        $akcija = "stopped";

        $komanda = "serverstop sid={$sid}";
        $autostart = "serveredit virtualserver_autostart=0";
    } else if ($_GET['task'] == "restart") {
        $sql = "";
        $log_sql = "Server Restarted: <a href=\"serversummary.php?id={$server['serverid']}\">{$server['name']}</a> (Admin)";
        $akcija = "restarted";
        $ok = false;
        $notification = "Restart nije implementiran ! Mora stop pa start !";
        $autostart = "";
    }


    if (isset($_GET['return'])) $return = $_GET['return'];
    else $return = "gp-voiceserver.php";

    if ($ok) {
        $ts3->telnetConnect($voice['ip'], $voice['port']);
        if ( !$ts3->loginQuery($voice['user'], $voice['pass']) ) $gpanel->izbaciGresku("Master server login fail! Prijavite ovaj problem podrsci!");

        if ($akcija == "stopped") {             // ako stopiramo server, onda mu izvrsimo autostart pre stopiranja
            $ts3->telnetExec("use sid={$sid}");
            $ts3->telnetExec($autostart);
        }

        $ret = $ts3->getData("msg", $ts3->telnetExec( $komanda ) );

        if ($akcija == "started") {         // ako startujemo server, onda mu izvrsimo autostart posle startovanja
            $ts3->telnetExec("use sid={$sid}");
            $ts3->telnetExec($autostart);
        }
        if ($ret == "ok") {
            if ( !empty($sql) ) $mysql->query($sql);

            $akcija_big = ucwords($akcija);
            $gpanel->loguj($server['clientid'],$server['serverid'],$server['boxid'], "Server $akcija_big: <a href=\"serversummary.php?id=$server[serverid]\">$server[name]</a> (Client)", $_SESSION['clientname']);

            $_SESSION['notification'] = "Server {$akcija}";
            header("Location:{$return}?id={$serverid}");
            die();
        } else {
            $_SESSION['notification'] = $ret;
            header("Location:{$return}?id={$serverid}");
            die();
        }
    } else {
        $_SESSION['notification'] = $notification;

        header("Location:{$return}?id={$serverid}");

        die();
    }
} else if ($_GET['task'] == "permreset") {
    $serverid = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
    $clientid = $_SESSION['userid'];

    $server = $gpanel->getServer($serverid,$clientid);
    if ($server['serverid']=="") $gpanel->izbaciGresku(_ERROR_SERVER_NO_ACCESS);
    $voice = mysql_fetch_array(mysql_query("SELECT * FROM `voiceservers` WHERE `voiceid`='{$server['voiceid']}'"));
    if ( empty($voice['voiceid']) ) $gpanel->izbaciGresku("Nepostojeci master server! Prijavite ovaj problem podrsci!");

    $ts3->telnetConnect($voice['ip'], $voice['port']);
    if ( !$ts3->loginQuery($voice['user'], $voice['pass']) ) die("Master server login fail !");

    $ts3->telnetExec("use sid={$server['cfg1']}");
    $ret = $ts3->telnetExec("permreset");

    $errorid = $ts3->getData("error id", $ret);
    $msg = $ts3->getData("msg", $ret);
    $token = $ts3->getData("token", $ret);


    if (!$errorid) {
        $notification = "Restartovane permisije!<br />Novi Server admin token: ".urlencode($token);
    } else {
        $notification = $msg;
    }

    $_SESSION['notification'] = $notification;
    header("Location:gp-voiceserver.php?id={$serverid}");
    die();
} else if ($_POST['task'] == "servergrouplist") {
    $serverid = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
    $clientid = $_SESSION['userid'];
    $server = $gpanel->getServer($serverid,$clientid);
    if ($server['serverid']=="") $gpanel->izbaciGresku(_ERROR_SERVER_NO_ACCESS);
    $voice = mysql_fetch_array(mysql_query("SELECT * FROM `voiceservers` WHERE `voiceid`='{$server['voiceid']}'"));
    if ( empty($voice['voiceid']) ) $gpanel->izbaciGresku("Nepostojeci master server! Prijavite ovaj problem podrsci!");

    $ts3->telnetConnect($voice['ip'], $voice['port']);
    if ( !$ts3->loginQuery($voice['user'], $voice['pass']) ) die("Master server login fail !");



    $ts3->telnetExec("use sid={$server['cfg1']}");
    $groups = $ts3->getServerGroups();
    $return = array();
    foreach ($groups as $group) {
        $return[$group['sgid']] = $group['name'];
    }

    die(json_encode($return));
} else if ($_GET['task'] == "tokenadd" ) {
    $serverid = htmlspecialchars(mysql_real_escape_string(addslashes($_POST['server_id'])));
    $clientid = $_SESSION['userid'];

    $server = $gpanel->getServer($serverid,$clientid);
    if ($server['serverid']=="") $gpanel->izbaciGresku(_ERROR_SERVER_NO_ACCESS);
    $voice = mysql_fetch_array($mysql->query("SELECT * FROM `voiceservers` WHERE `voiceid`='{$server['voiceid']}'"));
    if ( empty($voice['voiceid']) ) $gpanel->izbaciGresku("Nepostojeci master server! Prijavite ovaj problem podrsci!");

    $ts3->telnetConnect($voice['ip'], $voice['port']);
    if ( !$ts3->loginQuery($voice['user'], $voice['pass']) ) die("Master server login fail !");

    $comment = $ts3->escapeText($_POST['comment']);
    $sgid = (int)$_POST['gid'];

    $ts3->telnetExec("use sid={$server['cfg1']}");
    $ret = $ts3->telnetExec("tokenadd tokentype=0 tokenid1={$sgid} tokenid2=0 tokendescription={$comment}");

    $errorid = $ts3->getData("error id", $ret);
    $msg = $ts3->getData("msg", $ret);
    $token = $ts3->getData("token", $ret);

    unset($ret);

    if (!$errorid) {
        $ret['notification'] = "Uspesno kreiran novi token:<br />".urlencode($token);
        $ret['status'] = "ok";
        $ret['token'] = $token;
        $ret['comment'] = $comment;
    } else {
        $ret['notification'] = $ts3->unEscapeText($msg);
        $ret['status'] = "fail";
    }

    die(json_encode($ret));
}
?>