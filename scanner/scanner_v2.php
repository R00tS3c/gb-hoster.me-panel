<?php
$config['target_dir'] = $_SERVER['DOCUMENT_ROOT'];
$config['output_file'] = "scan/".date("Y-m-d").".txt";
$config['false_positives_file'] = "false_positives.txt";

$suspicious_strings = array(
    /*'c99shell', 'phpspypass', 'Owned',
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
    '31337',*/
	'clanovi',
	'onlinea'
);

$suspicious_files = array();

if(file_exists($config['false_positives_file'])) {
    $contents = file_get_contents($config['false_positives_file']);
    $false_positives = explode("\n", $contents);
} else {
    $false_positives = false;
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
                        $contents = file_get_contents($full_filename);
                        $suspicious = false;
						if($filename != "scanner_v2.php" && $filename != "scanner.php") {
                        	foreach($suspicious_strings as $string) {
                           		if(strpos(strtolower($contents), strtolower($string)) != false) {
									if(sizeof($suspicious_files) == 0) {
										echo "U fajlu : <b>".$full_filename."</b> je pronadjeno : <b>$string</b>";
									} else {
										echo "<br>U fajlu : <b>".$full_filename."</b> je pronadjeno : <b>$string</b>";
									}
									
									$suspicious = true;
								}
							}
                        }
						
                        if($suspicious) {
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

$of = fopen($config['output_file'], "w");
fclose($of);

if(substr($config['target_dir'], -1) == "/")
    $config['target_dir'] = substr($config['target_dir'], 0, strlen($config['target_dir'])-1);

backdoor_scan($config['target_dir']);

if(sizeof($suspicious_files) > 0) {
	echo "<br><br>Skeniranje je zavrseno.<br>Lista fajlova se nalazi u : <b>".$config['output_file']."</b>";
} else {
	echo "<br>Skeniranje je zavrseno.<br>Nije pronadjen ni jedan fajl!";
}

?>
