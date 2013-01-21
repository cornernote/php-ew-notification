<?php
/**
 * Database Layer
 *
 * Provides an easier to use interface for MySQL
 * @package Eternal-WoW! Site
 * @author Eternal-WoW!
 * @version 1.0
 */
 
/**
 * Database Layer with caching for website
 */
class SqlDB {
  // variables
	private $handles;
	private $configs;
	private $status;
	private $counters;
	private $results = Array();
	private $errors;
	// Caching stuff
	private $cache = null;
	private $cache_status = 0;
	private $cache_config;
 
	/**
	 *
	 * @param $key A unique identifier for the connection
	 * @param $host Hostname of the SQL Database
	 * @param $user Username to connect to the SQL Database
	 * @param $pass Password to connect to the SQL Database
	 * @param $dbname Database Name. If false, no database is selected
	 * @param $persistant Use Persistant MySQL connections
	 * @param @caching Enable Caching?
	 * @param $autoconnect If true, will establish connection right away and fail if it cannot. If false, will establish connection when a query is first executed.
	 */
	public function NewConnection($key, $host, $user, $pass, $dbname = false, $persistant = true, $caching = false, $autoconnect = false) {
 
		// Raise an error if a key is not defined, or the key has already been used.
		if(strlen($key) < 1 || isset($this->configs[$key])) {
						$this->errors['global'][] = "Key(".$key.") has already been used. Cannot re-establish database connection over existing key.";
						return false;
		}
 
		// Create the configuration for the key
		$this->configs[$key] = Array(
			'hostname' => $host,
			'username' => $user,
			'password' => $pass,
			'database' => $dbname,
			'persistant' => $persistant,
			'caching' => $caching
		);
 
		// Setup everything else
		$this->handles[$key] = false;
		$this->status[$key] = 0;
		$this->counters[$key] = 0;
		$this->results[$key] = Array();
		$this->errors[$key] = Array();
 
		// Was autoconnect specified?
		if($autoconnect)
			return $this->__create_connection($key);
 
		return true;
	}
	public function ConfigureCaching($host, $port, $timeout = 1) {
		$this->cache_config = Array(
			'hostname' => $host,
			'port' => $port,
			'timeout' => $timeout
		);
		return true;
	}
	public function isConnectable($keys) {
		if(!is_array($keys)) {
			$keys = Array($keys);
		}
		foreach($keys as $key) {
			switch($this->status[$key]) {
				case 1:
					return true;
				case 0:
					// Try and establish the connection
					if(!$this->__create_connection($key)) {
									return false;
					}
					break;
				case -1:
					// Already failed, give up already
					return false;
					break;
				default:
					die("FATAL ERROR: UNKNOWN CONNECTION STATUS!");
			} 
		}
		return true; 
	}
	private function __create_connection($key) {
 
		// Is there even a configuration with this key?
		if(!isset($this->configs[$key])) return false;
 
		// Has the connection already been attempted?
		if($this->status[$key] < 0) return false;
 
		// Attempt the connection
		if($this->configs[$key]['persistant'])
			$handle = mysql_pconnect($this->configs[$key]['hostname'], $this->configs[$key]['username'], $this->configs[$key]['password']);
		else
			$handle = mysql_connect($this->configs[$key]['hostname'], $this->configs[$key]['username'], $this->configs[$key]['password']);
 
		// Check for a valid handle?
		if(!$handle) {
			$this->status[$key] = -1;
			$this->errors[$key][] = mysql_error($handle);
			return false;
		}
		// Now try and select a db where necessary
		if($this->configs[$key]['database'] !== false) {
			if(!mysql_select_db($this->configs[$key]['database'], $handle)) {
				$this->status[$key] = -1;
				$this->errors[$key][] = mysql_error($handle);
				return false;                      
			}
		}
		// Successful Connection
		$this->status[$key] = 1;
		// Assign the handle for future use
		$this->handles[$key] = $handle;
		// Empty the errors
		$this->errors[$key] = Array();
 
		// Make sure we have a cache configuration before we bother...
		if(is_array($this->cache_config)) {
			// If caching is enabled see if we need to create a cache connection
			// Don't need to check for a cache, we are using xcache
			if($this->configs[$key]['caching'] && $this->cache_status == 0) {
	$this->cache_status = 1;
			}
		}
		return true;
	}
/**
 * Start database query
 * @param string  $key        Key for database query
 * @param string  $query      Query to run on database
 * @param integer $cache_time Time to cache result in seconds
 */
	public function Query($key, $query, $cache_time = 60) {
		// Deal with arguments
		// Must have at least a key passed
		if(strlen($key) < 1) {
			$this->errors['global'][] = "Query did not have a key specified.";
			return false;
		}
		// Does the key exist?
		if(!isset($this->configs[$key])) {
			$this->errors[$key][] = "Connection Configuration for ".$this->status[$key]." does not exit.";
			return false;
		}
		// Handle current connection status
		switch($this->status[$key]) {
			case 1:
				// Don't have to do anything, it's already active...
				break;
			case 0:
				// Try and establish the connection
				if(!$this->__create_connection($key)) {
					return false;
				}
				break;
			case -1:
				// Already failed, give up already
				return false;
				break;
			default:
				die("FATAL ERROR: UNKNOWN CONNECTION STATUS!");
		}
		// Check for session locks on table row ~ Somone77 11/28/12
		global $locker; // I don't know what I'm going to do about multiple lock classes. My guess is create the classes into an array, but we'll see.
		if(isset($locker))
		{
			if(!$locker->waitUntilUnlocked())
			{
				$this->errors[$key][] = "Expired while waiting 15 seconds for table ".$lockCheck[0]." to unlock.";
				return false;
			}
		}
		// Second argument MUST be a type we support
		switch(trim(substr($query, 0, (strpos($query, " ")+1)))) {
			case "SELECT":       
				$result = $this->__query_select($key, $query, $cache_time);
				break;
			default:
				$result = $this->__query_write($key, $query);
				break;
		}
 
		return $result;
	}
	private function __query_write(&$key, &$query) {
		$startTime = microtime(true);
 
	$result = mysql_query($query, $this->handles[$key]);
 
	$totalTime = microtime(true) - $startTime;
	$this->debugSQL($query, $totalTime);
 
	return $result;
	}
	private function __query_select(&$key, &$query, &$cache_time) {
		//Is caching enabled?
		if($this->configs[$key]['caching'] && $cache_time > 0) {
						// Calculate MD5 of Query
						$hash = md5($query);
						// Check to see if the query is still cached
						if(xcache_isset("wow_".$key."_".$query)) {
							$data = xcache_get("wow_".$key."_".$query);
								return $this->__add_result($key, $data);
			}
		}
 
		// Now do the SQL select query
		$startTime = microtime(true);
	
	$result = mysql_query($query, $this->handles[$key]);
	
	$totalTime = microtime(true) - $startTime;
	$this->debugSQL($query, $totalTime);
 
		if(!$result) {
						echo mysql_error($this->handles[$key]);
						return false;
		}
 
 
		// Now cache the results
		if($this->configs[$key]['caching'] && $cache_time > 0) {
			// If caching is enabled, cache the results and return a pointer to the results
			$data = Array();
			while($row = mysql_fetch_assoc($result)) {
							$data[] = $row;
			}
			// Cache it
			xcache_set("wow_".$key."_".$query, $data, $cache_time);
			
			if(!is_array($data)) {
				$data = Array();
			}
			return $this->__add_result($key, $data);
		}
 
		// Otherwise, just return a pointer to the live SQL data
		return $result;
	}
	private function __add_result($key, $data) {
		$this->counters[$key]++;
		$this->results[$key][$this->counters[$key]]['data'] = $data;
		$this->results[$key][$this->counters[$key]]['ptr'] = 0;
		return $this->counters[$key];
	}
 
	public function FetchArray($key, $result) {
		if(is_int($result)) {
			// Check for a valid key
			if($this->results[$key][$result]['ptr'] < count($this->results[$key][$result]['data'])) {
				$row = $this->results[$key][$result]['data'][$this->results[$key][$result]['ptr']];
				$this->results[$key][$result]['ptr']++;
				return $row;
			} else {
				return false;
			}
		} else{
			return mysql_fetch_assoc($result);
		}
	}
	public function NumRows($key, $result) {
		if(is_int($result)) {
			return count($this->results[$key][$result]['data']);
		} else {
			return mysql_num_rows($result);
		}
	}
	public function NumAffected($key) {
					return mysql_affected_rows($this->handles[$key]);
	}
	public function Result($key, $result, $row, $field) {
		if(is_int($result)) {
			return $this->results[$key][$result]['data'][$row][$field];
		} else {
			return mysql_result($result, $row, $field); 
		}
	}
	public function InsertID($key) {
		return mysql_insert_id($this->handles[$key]);  
	}
	public function Error($key) {
		// Determine last error
		if(count($this->errors[$key])) {
						return $this->errors[$key][(count($this->errors[$key]) - 1)];
		}
		if(count($this->errors['global'])) {
						return $this->errors['global'][(count($this->errors['global'])-1)];
		}
		return false;
	}
	public function Escape($key, $text) {
	return mysql_real_escape_string($text, $this->handles[$key]);
	}
	public function Free($key, $result) {
		if(is_int($result)) {
			// Free the result from memory...
			unset($this->results[$key][$result]);
			return true;
		} else {
			// Free the result from mysql...
			return mysql_free_result($result);
		}
	}
	private function debugSQL($text, $time = -1, $file="debugsql.txt") {
			$enabled = true;
			$detailedTrace = false;
 
			if (!$enabled) { return false; }
			
			$impact = 0;
 
			$trace = debug_backtrace(false);
 
 
			foreach ($trace as $key => $value) {
				unset($trace[$key]['object']);
				if ((strpos($value['file'], "mysql.php") !== false) || (strpos($value['file'], "global.php") !== false)) {
					unset($trace[$key]);
				}
			}
			
			//var_dump($trace); // For Testing
 
			if (!$detailedTrace) {
				$traceString = "";
				foreach ($trace as $key => $value) {
					if ($traceString != "") {
						$traceString .= " => ";
					}
					$traceString .= $value['file'] . ":" . $value['function'] . "@" . $value['line'];
				}
				
				$trace = $traceString;
			} else {
				$trace = serialize($trace);
			}
			
			if (strlen($trace) > 2000) {
				$trace = substr($trace,0,2000) . "...";
			}
 
			if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "") {
				$referer = "<none>";
			} else {
				$referer = $_SERVER['HTTP_REFERER'];
			}
 
			//$header = $_ENV['userdata']->data['user_id'] . " - " . $_SERVER['REMOTE_ADDR'] . " - [" . date("Y-m-d H:i:s") . "] " . $_SERVER['REQUEST_METHOD'] . ": " . $_SERVER['REQUEST_URI'] . " ref " . $referer;
			$header = $_SERVER['REMOTE_ADDR'] . " - [" . date("Y-m-d H:i:s") . "] " . $_SERVER['REQUEST_METHOD'] . ": " . $_SERVER['REQUEST_URI'] . " ref " . $referer;
			
			//$separator = "__________";
 
			if ($time != -1) {
				$time = round($time * 1000, 3);
				$impact = round($time / .3);
				
				if ($impact > 0) {
					$impactText = "[";
					for ($i = 0; $i < $impact; $i++) {
						$impactText .= ".";
					}
					$impactText .= "]";
				}
 
				$text = "[" . $time . "ms] " . (($impact > 0) ? " $impactText " : "") . $text;
			}
 
			fwrite(fopen($file, "a+"), $header . " | " . $text . " | " . $trace . "\n");
	}
}
?>