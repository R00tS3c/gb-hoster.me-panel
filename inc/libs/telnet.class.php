<?php
class TS3 {
    private $fp, $connected = false;

    /**
     * Konekcija
     */
    function telnetConnect($ip, $port, $timeout=5) {
        $this->fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);


        if (!$this->fp) {
            $this->connected = false;
            return "Konekcija failovala";
        }
        $this->connected = true;

        /*
        fgets($this->fp,4096);
        fgets($this->fp,4096);
        $data = fgets($this->fp,4096);
        echo $data;
        if (strpos($data,"error id=3329") === true) die("BANNED!");
         */
    }

    /*
     *  izvrsi komandu, a zatim vrati output
     */
    function telnetExec($string) {
        if (!$this->connected) return false;

        fputs($this->fp,"$string\r\n");
        do {
            $data.=fgets($this->fp,4096);
        } while(strpos($data, 'msg=') === false or strpos($data, 'error id=') === false);
        return $data;
    }

    function useServer($sid, $port="") {
        if (!$this->connected) return false;

        //if ($sid>0) $this->telnetExec("");
    }
    /*
     * Metoda salje login podatke u shell
     */
    function loginQuery($user, $pass) {
        if (!$this->connected) return false;

        $out = $this->telnetExec("login {$user} {$pass}");
        $lines = explode("\n",$out);
        $data = trim($lines[count($lines)-2]);

        if ( $data == "error id=0 msg=ok" ) return true;
        else return false;
    }

    function getServerGroups() {
        if (!$this->connected) return false;

        $out = $this->telnetExec("servergrouplist");
        $lines = explode("|",$out);
        $groups = array();

        foreach ($lines as $line) {
            if ( $this->getData("type", $line) != 1) continue;

            $groups[] = array(
                'sgid' => $this->getData("sgid", $line),
                'name' => $this->unEscapeText( $this->getData("name", $line) )
            );
        }

        return $groups;
    }
    /**
     * Metoda uzima servere, parsira i vraca u nizu
     */
    function getServers() {
        if (!$this->connected) return false;

        $out = $this->telnetExec("serverlist");
        $lines = explode("|",$out);
        $servers = array();
        foreach ($lines as $line) {
            //echo $line."<br />";
            $servers[] = array(
                'sid'   =>  $this->getData("virtualserver_id", $line),
                'name'  =>  $this->unEscapeText($this->getData("virtualserver_name", $line)),
                'slots' => $this->getData("virtualserver_maxclients", $line),
                'clientsonline' => $this->getData("virtualserver_clientsonline", $line),
                'port' => $this->getData("virtualserver_port", $line),
                'status'    =>  $this->getData("virtualserver_status", $line)
            );
        }

        return $servers;
    }

    /*
     * Uzima informacije u formatu polje=vrednost do razmaka
     * vraca samo vrednost ili false ako polje nije nadjeno
     */
    function getData($data, $string) {
        $string = preg_replace("/\n/", " ", $string);
        $string = preg_replace("/\r/", " ", $string);
        $temp = @strpos($string, $data);
        if ( $temp === false ) return false;
        $start = $temp + strlen($data);    // trazi prvu poziciju $data u $string + doda duzinu $data (taman dodje do =)

        //$temp = @strpos($string, " ", $start-1);    // trazi prvu poziciju razmaka posle $data
        $temp = $this->strpos_arr($string, array(" ","\n","\r"), $start-1);    // trazi prvu poziciju razmaka posle $data
        if ( $temp === false ) $end = strlen($string)-$start;
        else $end = $temp - $start;
        $temp = explode("=",substr($string, $start, $end)); // exploduje po = to sto smo dobili (field=value)

        return $temp[1];
    }

    function strpos_arr($haystack, $needle, $start) {
        if(!is_array($needle)) $needle = array($needle);
        foreach($needle as $what) {
            if(($pos = strpos($haystack, $what, $start))!==false) return $pos;
        }
        return false;
    }

    /**
     *  metoda za debug svrhe i ispis :)
     */
    function formatOutput($string) {
        $string = str_replace("\n", "<br />", $string);
        $string = str_replace("|", "<br />", $string);

        return $string;
    }

    /*
     * metoda salje quit
     */
    function quitQuery() {
        if (!$this->connected) return false;

        fputs ($this->fp, "quit\r\n");
    }

    function __destruct() {
        $this->quitQuery();
    }

    /**
     * unEscapeText turns escaped chars to normals
     *
     * @author     Par0noid Solutions
     * @access     private
     * @param      string  $text   text which should be escaped
     * @return     string  text
     */
    function unEscapeText($text) {
        $escapedChars = array("\t", "\v", "\r", "\n", "\f", "\s", "\p", "\/");
        $unEscapedChars = array('', '', '', '', '', ' ', '|', '/');
        $text = str_replace($escapedChars, $unEscapedChars, $text);
        return $text;
    }

    /**
      * escapeText escapes chars that we can use it in the query
      *
      * @author     Par0noid Solutions
      * @access     private
      * @param      string  $text   text which should be escaped
      * @return     string  text
      */
       function escapeText($text) {
            $text = str_replace("\t", '\t', $text);
            $text = str_replace("\v", '\v', $text);
            $text = str_replace("\r", '\r', $text);
            $text = str_replace("\n", '\n', $text);
            $text = str_replace("\f", '\f', $text);
            $text = str_replace(' ', '\s', $text);
            $text = str_replace('|', '\p', $text);
            $text = str_replace('/', '\/', $text);
            return $text;
        }

}

$ts3 = new TS3();
?>