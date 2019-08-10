<?php
$config['target_dir'] = $_SERVER['DOCUMENT_ROOT'];
$config['output_file'] = "scan/".date("Y-m-d").".txt";
$config['false_positives_file'] = "false_positives.txt";
//$config['email'] = "csmodovi@gmail.com";

$suspicious_strings = array(
    'c99shell', 'phpspypass', 'Owned',
    'hacker', 'h4x0r', '/etc/passwd',
    'uname -a', 'eval(base64_decode(',
    '(0xf7001E)?0x8b:(0xaE17A)',
    'd06f46103183ce08bbef999d3dcc426a',
    'rss_f541b3abd05e7962fcab37737f40fad8',
    'r57shell',
    'Locus7s',
    'milw0rm.com',
    '$IIIIIIIIIIIl',
    'SubhashDasyam.com',
    '31337',
	"nerds"
);

$suspicious_files = array();

if(file_exists($config['false_positives_file'])) {
    $contents = file_get_contents($config['false_positives_file']);
    $false_positives = explode("\n", $contents);
} else {
    $false_positives = false;
}

function is_php_file($filename) {
    return substr($filename, -4) == ".php" || 
        substr($filename, -5) == ".php4" || 
        substr($filename, -5) == ".php5";
}

$dir_count = 0;
function backdoor_scan($path) {
    global $suspicious_strings;
    global $suspicious_files;
    global $config;
    global $false_positives;
    global $dir_count;
    
    $dir_count++;
    
    $d = @dir($path);
    if($d == false) {
        echo "\nNe mogu otvoriti folder ".$path.", preskacem";
        return;
    }
    while(false !== ($filename = $d->read())) {
        if($filename != "." && $filename != "..") {
            $full_filename = $d->path."/".$filename;
            
            $false = false;
            if($false_positives) {
                if(in_array($full_filename, $false_positives))
                    $false = true;
            }
            if(!$false) {
                if(is_dir($full_filename)) {
                    backdoor_scan($full_filename);
                } else {
                    if(is_php_file($filename)) {
                        $contents = file_get_contents($full_filename);
                        $suspicious = false;
						if($filename != "scanner.php") {
                        	foreach($suspicious_strings as $string) {
                           		if(strpos(strtolower($contents), strtolower($string)) != false) {
									echo "<br />\n$string";
									$suspicious = true;
								}
							}
                        }
						
                        if($suspicious) {
                            echo "<br />\nFajl je pronadjen : ".$full_filename;
                            $of = fopen($config['output_file'], "a");
                            fwrite($of, $full_filename."\n");
                            fclose($of);
							
                            $suspicious_files[] = $full_filename;
                        }
                    }
                }
            }
        }
    }
}

$of = fopen($config['output_file'], "w");
fclose($of);

if(substr($config['target_dir'], -1) == "/")
    $config['target_dir'] = substr($config['target_dir'], 0, strlen($config['target_dir'])-1);

backdoor_scan($config['target_dir']);
/*
if(sizeof($suspicious_files) > 0) {
    if(!empty($config['email'])) {
        $body = '';
        foreach($suspicious_files as $filename) {
            $body .= $filename."\r\n";
        }
        mail($config['email'], "Pronadjeno ".sizeof($suspicious_files)." fajlova, datuma ".date("Y-m-d"),
            $body, "From: ".$config['email']."\r\nReply-To: ".$config['email']."\r\n");
    }
}*/

echo "\n\n";
if(sizeof($suspicious_files > 0)) {
    echo "<br />\nSkeniranje zavrseno. Lista fajlova se nalazi u : ".$config['output_file']."\n";
} else {
    echo "<br />\nSkeniranje zavrseno. Nije pronadjen ni jedan fajl!";
}
echo "\n";

?>
