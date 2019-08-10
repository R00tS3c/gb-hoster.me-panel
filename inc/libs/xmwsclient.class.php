<?php

class xmwsclient {

    public $module = null;
    public $method = null;
    public $username = null;
    public $password = null;
    public $serverkey = null;
    public $wsurl = null;
    public $data = null;

    /**
     * This is a quick way to configure the class variables instead of specifying per line.
     * @return void
     */
    function InitRequest($wsurl, $mod, $met, $key, $user="", $pass="") {
        $this->module = $mod;
        $this->method = $met;
        $this->username = $user;
        $this->password = $pass;
        $this->serverkey = $key;
        $this->wsurl = $wsurl;
        return;
    }

    function SetRequestData($string) {
        $this->data = $string;
    }

    /**
     * Automatically prepares and formats the XMWS XML request message based on your preset variables.
     * @return string The formatted XML message ready to post.
     */
    function BuildRequest() {
        $request_template = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                "<xmws>" .
                "\t<apikey>" . $this->serverkey . "</apikey>\n" .
                "\t<request>" . $this->method . "</request>\n" .
                "\t<authuser>" . $this->username . "</authuser>\n" .
                "\t<authpass>" . $this->password . "</authpass>\n" .
                "\t<content>" . $this->data . "</content>" .
                "</xmws>";
        return $request_template;
    }

    /**
     * The main Request class that initiates the connection and request to the web service.
     * @param type $post_xml
     * @return type 
     */
    function Request($post_xml) {
        $full_wsurl = $this->wsurl . "/api/" . $this->module;
        return $this->PostRequest($full_wsurl, $post_xml);
    }

    /**
     * This takes a RAW XMWS XML repsonse and converts it to a usable PHP array.
     * @param type $xml
     * @return type 
     */
    function ResponseToArray($xml) {
        //var_dump($xml);
        return array('response' => $this->GetXMLTagValue($xml, 'response'), 'data' => $this->GetXMLTagValue($xml, 'content'));
    }

    /**
     * Returns the value between a given XML tag.
     * @param string $xml
     * @param type $tag
     * @return type 
     */
    function GetXMLTagValue($xml, $tag) {
        $xml = " " . $xml;
        $ini = strpos($xml, '<' . $tag . '>');
        if ($ini == 0)
            return "";
        $ini += strlen('<' . $tag . '>');
        $len = strpos($xml, '</' . $tag . '>', $ini) - $ini;
        return substr($xml, $ini, $len);
    }

    /**
     * A simple POST class that attempts to POST data simply.
     * @param string $url URL to the XMWS web service controller.
     * @param string $data The data to post.
     * @param string $optional_headers Optional if you need to send additonal headers.
     * @return string The XML repsonse. 
     */
    function PostRequest($url, $data, $optional_headers = null) {
        $log_dir = 'logs';
        $log_file = 'api_error_'.date('Y-m-d_Hi').'.txt';
        //does log directory exists?
        if(!is_dir($log_dir)){
            @mkdir($log_dir, 0777);
        }
        
        $params = array('http' => array(
                'method' => 'POST',
                'content' => $data
                ));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            //log error
            $err_str = var_dump(error_get_last());
            $err_fp = fopen($log_dir.'/'.$log_file, 'a');
            fwrite($err_fp, $err_str."\n\n");
            fclose($err_fp);
            die("Problem reading data from " . $url . "");
        }
        $response = @stream_get_contents($fp);
        //var_dump($response);
        if ($response == false) {
            //log error
            $err_str = var_dump(error_get_last());
            $err_fp = fopen($log_dir.'/'.$log_file, 'a');
            fwrite($err_fp, $err_str."\n\n");
            fclose($err_fp);
                    
            die("Problem reading data from " . $url . "");
        }
        return $response;
    }

    /**
     * Simply outputs the contents of the response as a PHP array (using print_r())
     * @param string $xml 
     */
    function ShowXMLAsArrayData($xml) {
        echo "<pre>";
        print_r($this->ResponseToArray($xml));
        echo "</pre>";
    }
    
    /**
    * A simple way to build an XML section for the <content> tag, perfect for multiple data lines etc.
    * @param string $name The name of the section <tag>.
    * @param array $tags An associated array of the tag names and values to be added.
    * @return string A formatted XML section block which can then be used in the <content> tag if required.
    */
    function NewXMLContentSection($name, $tags){
    $xml = "\t<" . $name . ">\n";
    foreach ($tags as $tagname => $tagval) {
        $xml .="\t\t<" . $tagname . ">" . $tagval . "</" . $tagname . ">\n";
    }
    $xml .= "\t</" . $name . ">\n";
    return $xml;
    }
    
    
    /**
    * Takes an XML string and converts it into a usable PHP array.
    * @param $contents string The XML content to convert to a PHP array.
    * @param $get_arrtibutes bool Retieve the tag attrubtes too?
    * @param $priotiry string
    * @return array
    */
    static function XMLDataToArray($contents, $get_attributes = 1, $priority = 'tag') {
        //print_r(func_get_args());
        //echo('<br><br>');
        if (!function_exists('xml_parser_create')) {
            return array();
        }
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values)
            return; //Hmm...
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();
        $current = & $xml_array;
        $repeated_tag_index = array();
        foreach ($xml_values as $data) {
            unset($attributes, $value);
            extract($data);
            $result = array();
            $attributes_data = array();
            if (isset($value)) {
                if ($priority == 'tag') {
                    $result = $value;
                } else {
                    $result['value'] = $value;
                }
            }
            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag') {
                        $attributes_data[$attr] = $val;
                    } else {
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                    }
                }
            }
            if ($type == "open") {
                $parent[$level - 1] = & $current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    $current = & $current[$tag];
                }else {
                    if (isset($current[$tag][0])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                        $repeated_tag_index[$tag . '_' . $level] = 2;
                        if (isset($current[$tag . '_attr'])) {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = & $current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") {
                if (!isset($current[$tag])) {
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                }else {
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $current[$tag] = array(
                            $current[$tag],
                            $result
                        );
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) {
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }
                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            } elseif ($type == 'close') {
                $current = & $parent[$level - 1];
            }
        }
        return ($xml_array);
    }

}

?>
