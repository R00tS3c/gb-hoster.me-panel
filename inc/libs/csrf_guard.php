<?
	/*****************************************************************
	 * Safelight CSRF Guard for PHP
	 * This program contains a single CSRFGuard class which is used
	 * by PHP sites to implement the Sychronizer Token Pattern as 
	 * a defense against Cross-Site Request Forgery (CSRF). For more
	 * information on CSRF and the Synchronizer Token Pattern see
	 * the OWASP Foundation site:
	 * https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)
	 *
	 * Copyright (C) 2012 Safelight Security Advisors
     * Authors: John Carmichael - jcarmichael@safelightsecurity.com
     *			Mike Cooper - mcooper@safelightsecurity.com
	 *
	 * This program is free software: you can redistribute it and/or 
	 * modify it under the terms of the Attribution-ShareAlike License
	 * as published by Creative Commons, either version 3 of the 
	 * License, or s(at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, 
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	 *
	 * To view the terms of the Attribution-ShareAlike License see
	 * http://creativecommons.org/licenses/by-sa/3.0/nz/legalcode
	 ****************************************************************/
	 
	 class CSRFGuard {

		/*********************************************************
		 * Class constructor
		 *
		 * During intialization the class defaults to a session array
		 * named "CSRFGuard" that is used to store all data. It is possible
		 * to override this name during initialization to specify a custom
		 * name, and this is required in order to protect multiple forms
		 * on the same page, as they each require a separate set of variables.
		 *
		 * While initializing this class it is possible to specify
		 * the $timeout parameter to override the default of 300
		 * seconds (5 mins).
		 *
		 * If the request is made outside of this time frame, it
		 * will be considered invalid. This paraemeter defaults to
		 * 300 seconds (5 mins) but can be overridden in the
		 * constructor method call.
		 ********************************************************/
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

		/*********************************************************
		 * Generates the hidden form element containing the token
		 *
		 * Creates a new CSRFGuard Token in the current session and
		 * returns a hidden form field element that contains the
		 * value for submitting with the protected form.
		 *
		 * Specifying the session object name is optional and defaults to
		 * "CSRFGuard", however it must match the value used during intialization
		 *
		 * Specifying the field name is optional and defaults to
		 * "CSRFGuardToken", however the call to checkToken() must match this value
		 *
		 * @visibility public
		 * @return string.
		 ********************************************************/
		 public function generateHiddenField($objectName='CSRFGuard', $fieldName='CSRFGuardToken') {
			$this->generateToken($objectName);
			$field = "<input type=\"hidden\" name=\"".$fieldName."\" value=\"";
			$field .= $_SESSION[$objectName]['Token'] . "\" />";
			
			return $field;
		 }
		 
		 
		/*********************************************************
		 * Checks the CSRFGuard token to authenticate the request
		 *
		 * This check returns true if and only if the submitted
		 * token matches the guard values in the active session.
		 * If the token is not in the request, there is missing
		 * Guard data or the request is outside the timeframe 
		 * specified in the constructor this method will return 
		 * false.  If no active session is found an exception is
		 * thrown.
		 *
		 * Specifying the session object name is optional and defaults to
		 * "CSRFGuard", however it must match the value used during intialization
		 *
		 * Specifying the field name is optional and defaults to
		 * "CSRFGuardToken", however it must match the call to generateHiddenField()
		 *
		 * @visibility public
		 * @return boolean
		 ********************************************************/
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
		
		/*********************************************************
		 * Generates the appropriate CSRFGuard values
		 *
		 * This utility function creates a new set of CSRF Guard 
		 * data in the current session overwriting any existing 
		 * old data if necessary.
		 *
		 * @visibility private
		 ********************************************************/
		 private function generateToken($objectName='CSRFGuard') {
			// Store creation time for checking timeout later
			$_SESSION[$objectName]['Time'] = time();
			
			// Concatenate the salt, time and session id to hash for token
			$tokenString = $this->randomString() . time();
			$tokenString .= session_id();
			
			$_SESSION[$objectName]['Token'] = hash("crc32", $tokenString);
		 }

		/*********************************************************
		 * Checks if the request is within the appropriate
		 * timeframe
		 *
		 * This utility function checks to see if the current
		 * protected request is within the timeframe specified
		 * in the constructor.
		 *
		 * @visibility private
		 * @return booleans
		 ********************************************************/
		 private function checkTimeout($objectName='CSRFGuard') {
			return ($_SERVER['REQUEST_TIME'] - $_SESSION[$objectName]['Time']) < $_SESSION[$objectName]['Timeout'];
		 }

		/*********************************************************
		 * Random string generator
		 *
		 * This utility function for creating cryptographically 
		 * random strings for us as the basis of the token.
		 *
		 * @visibility private
		 * @return string: A 64 character hexidecimal string
		 ********************************************************/
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