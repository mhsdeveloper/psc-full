<?php

	/* class to handle caching data to the filesystem, and checking if cached files exist.
		This is meant to be used by a controller, to save processing and db calls from a model.
		
		usage:
		
		$cache = new DataCache(array("storage_path" => "/var/my/cache/folder", "expire_hours" => 2));
		
		if($cache->cached()){
			
			$data = $cache->getData();
			
			print_r($data);
			
			$greeting = $cache->get("Greeting");
			
			print "<h1>{$onePiece}</h1>";
			
		} else {
			$cache->add("Greeting, "Hello");
			$cache->add("Table", $phpObject);
			
			$cache->store();
		}
		
		
		
		NOTE: don't store boolean data, because functions that return data return false on failure.
		So maybe store integers?
		
	*/

	
	
	namespace MHS;




	class DataCache {
	
	
		//data to be cerealized... mmm... cereal
		private $data = [];
		
	
	
		/* pass your config options:
		
			$config = array(
							"storage_path" => "/full/path/in/filesystem",
							"expire_hours" => 2,
						);
		*/
		public function __construct($config){
			
			if(!isset($config['storage_path']) or !is_readable($config['storage_path'])) {
				throw new \Exception("Unspecified or unreadable ['storage_path']");
			} else {
				$this->storage_path = $config['storage_path'];
			}
			
			if(!isset($config['expire_hours']) or !is_numeric($config['expire_hours'])) {
				throw new \Exception("Unspecified or incorrect expire_hours");
			} else {
				$this->expire_hours = $config['expire_hours'];
			}
		}
	
	
	
		/* checks if there's cached data for the uniqueID.
		 * use makeUniqueID, or let DataCache use the REQUEST_URI
		 * 
		 * Returns true if it finds data AND data is not expired
		 * else returns false.
		 */
		public function cached(){
			
			if(!isset($this->uniqueID)) $this->makeUniqueID();
			$cachefile = $this->storage_path . '/' . $this->uniqueID . ".cache";
			
			//readable?
			if(!@is_readable($cachefile)) return false;
			
			$created = @filemtime($cachefile);

			@clearstatcache();

		   //is still valid?
		   $cacheduration = $this->expire_hours * 3600; //convert hours to seconds
		   if(($created + $cacheduration) > time()) return true;
		   else return false;
		}
		
		
		/* returns the unserialized data from the cached file
		 */
		public function getData(){

			if(!isset($this->uniqueID)) $this->makeUniqueID();
			$cachefile = $this->storage_path . '/' . $this->uniqueID . ".cache";
			
			//reable? Load it
			if(is_readable($cachefile)) {

				$handle = fopen($cachefile, "rb");
				$serialized = fread($handle, filesize($cachefile));
				fclose($handle);
				
			} else {
				return false;
			}
			
			//unserialied to property
			$this->data = unserialize($serialized);
			
			return $this->data;
		}
		
		
		
		/* retrieve a single element from the data array
		 */
		public function get($key){
			
			if(isset($this->data[$key])) {
				
				return $data[$key];
				
			} else return false;
		}
	
	
	
	
		//add single array element to our data array
		public function add($key, $value){
			$this->data[$key] = $value;
		}
		
		
		
		/* make user-supplied array our data array.
		 * REPLACES exhisting data!
		 */
		public function addData($data){
			$this->data = $data;
		}
	
	
	
	
		/* store() will generate a filename based it's uniqueID property,
		 * which it will autogenerate from the REQUEST_URI if you haven't
		 * yet created or specified via ->makeUniqueID
		 * 
		 */
		public function store(){
			
			if(!isset($this->uniqueID)) $this->makeUniqueID();
			
			$serialData = $this->cerealize();
			
			$filename = $this->storage_path . "/" . $this->uniqueID . ".cache";
			
			$filehandle = fopen($filename, "wb"); //b makes binary safe for our serial data
			
			if(false === $filehandle) return false;
			
			if(false === fwrite($filehandle, $serialData)) return false;
			if(false === fclose($filehandle)) return false;
			
			return true;
		}
		
		
		


		/* pass a string, or let the function use the REQUEST_URI
		 */
		public function makeUniqueID($str = ""){
			if(empty($str)) $str = $_SERVER['REQUEST_URI'];
		
			$this->uniqueID = md5($str);
		}
		
		
	
	


		//serialize it
		private function cerealize(){
			return serialize($this->data);
		}
		
	
	}