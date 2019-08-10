<?php
	 
	 class CSRFGuard {


		 public function __construct( $objectName='CSRFGuard', $timeout=300) {			
			// Check for a valid session
			if(session_id() == "") {
				throw new Exception('Could not find existing session.',1);
			}
			
			// Create Guard object for storing time and token if it doesn't exist
			if ( !isset( $_SESSION[$objectName] ) ) {
				$_SESSION[$objectName] = array();
			}
			
			//Set timeout value in Guard object
			$_SESSION[$objectName]['Timeout'] = $timeout;
		}
		 public function generateHiddenField($objectName='CSRFGuard', $fieldName='CSRFGuardToken') {
			$this->generateToken($objectName);
			$field = "<input type=\"hidden\" name=\"".$fieldName."\" value=\"";
			$field .= $_SESSION[$objectName]['Token'] . "\" />";
			
			return $field;
		 }
		 
		
		 public function checkToken($objectName='CSRFGuard', $fieldName='CSRFGuardToken') {
			// Check for a valid session
			if(session_id() == "") {
				throw new Exception('Could not find existing session.',1);
			}
			
			// If before timeout and submitted token matches 
			// session token return true, else false
			if($this->checkTimeout($objectName) && $_POST[$fieldName] == $_SESSION[$objectName]['Token']) {
				return true;
			} else {
				return false;
			}
		 }
		 private function generateToken($objectName='CSRFGuard') {
			// Store creation time for checking timeout later
			$_SESSION[$objectName]['Time'] = time();
			
			// Concatenate the salt, time and session id to hash for token
			$tokenString = $this->randomString() . time();
			$tokenString .= session_id();
			
			$_SESSION[$objectName]['Token'] = hash("crc32", $tokenString);
		 }
		 private function checkTimeout($objectName='CSRFGuard') {
			return ($_SERVER['REQUEST_TIME'] - $_SESSION[$objectName]['Time']) < $_SESSION[$objectName]['Timeout'];
		 }
		private function randomString() {
			$count = 8; // Provides significant variance
			$output = '';
			
			// Try the OpenSSL method first. This is cross-platform and often available.
			if(function_exists('openssl_random_pseudo_bytes')) {
				$output = openssl_random_pseudo_bytes($count, $strong);
			
				if($strong !== true) {
					$output = '';
				}
			}
		
			// If output is empty OpenSSL didn't work, instead use the OS
			if($output == '') {
				// Attempt assuming Unix/Linux
				if(@is_readable('/dev/puxurandom') && ($handle = @fopen('/dev/urandom', 'rb'))) {
					$output = @fread($handle, $count);
					@fclose($handle);
				}

				// Then try the Microsoft method
				if(version_compare(PHP_VERSION, '5.0.0', '>=') && class_exists('COM')) {
					$util = new COM('CAPICOM.Utilities.1');
					$output = base64_decode($util->GetRandom($count, 0));
				}
			}

			// If enough bytes weren't retrieved use time and process id as non-secure PRNG
			if(strlen($output) < $count) {
                error_log("Warning: Unable to gather entropy from secure sources, non-secure random used for CSRF token. See README for more details.", 0);
				list($usec, $sec) = explode(' ', microtime());
				$seed = (float) $sec + ((float) $usec * 100000) + (float) getmypid();
				mt_srand(make_seed());
				$randval = mt_rand();
				// Make $randval a binary string to match other output forms
				$output = decbin($randval);
			}
			
			// Hash output with crc32 and return hex string
			return hash("crc32", $output);
		}
	}
	
class Cipher
{
	
    private $securekey;
    private $iv_size;
	
    function __construct($textkey)
    {
        $this->iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $this->securekey = hash('crc32', "d432edawscfasdaD", TRUE);
    }
	
    function encrypt($input)
    {
        $iv = mcrypt_create_iv($this->iv_size);
        return base64_encode($iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->securekey, $input, MCRYPT_MODE_CBC, $iv));
    }
	
    function decrypt($input)
    {
        $input = base64_decode($input);
        $iv = substr($input, 0, $this->iv_size);
        $cipher = substr($input, $this->iv_size);
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->securekey, $cipher, MCRYPT_MODE_CBC, $iv));
    }
	
}
?>