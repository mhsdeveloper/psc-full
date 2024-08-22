<?php


	/* Generic class for spliting a file into chunks based on a string marker
	
		originally developed for Colonial Collegians Word/txt to TEI conversion
		
		You must define a $this->processChunk() that operates on $this->chunk
	*/
	
	
	
	
	namespace MHS\TxtProcessor;
	
	
	
	class Chunker {
		
		
		
		private $chuckSeparator = "";
		
		
		private $errors = [];
		
		
		private $search_array = [];
		private $replace_array = [];
		

		protected $content = "";
		


		public function loadFile($file){
			if(!is_readable($file)) $this->fatalError("Unable to read {$file}");
			$this->content = file_get_contents($file);
			return true;
		}
		
		
		
		public function setChunkSeparator($chunkSeparator){
			$this->chunkSeparator = $chunkSeparator;
		}
		


		/*
		 */
		public function each(){
			
			if(empty($this->chunkSeparator)) {
				$this->fatalError("No chunk separator specified");
				return false;
			}
			
			if(false === method_exists($this, "processChunk")) {
				$this->fatalError("Wrapper class did not define processChunk() method");
				return false;
			}
			
			$chunks = explode($this->chunkSeparator, $this->content);
			
			foreach($chunks as $chunk){
				
				$this->chunk = $chunk;
				//fail on any chunck fail
				if(false === $this->processChunk()) return false;
			}
		
			return true;	
		}
		
	
	
	
	
		/* function to convert an array that represents string mappings
		 * (key is search for and replaced with value) to
		 *  two arrays, a search and replace, sorted so that smaller
		 *  strings won't erroneously replace parts of longer strings
		 */
		public function buildSearchReplaceArrays($mapping_array){
			
			krsort($mapping_array);
			
			$search = [];
			$replace = [];
			
			foreach($mapping_array as $key => $value) {
				$search[] = $key;
				$replace[] = $value;
			}
			
			
			$this->search_array  = $search;
			$this->replace_array = $replace;
		}
	

	
	
		public function mapStrings(){
			$this->chunk = str_replace($this->search_array, $this->replace_array, $this->chunk);
		}


		private function error($message){
			$this->errors[] = $message;
		}
		
		
		private function fatalError($message){
			$this->error($message);
			die(implode("<br/>\n", $this->errors));
		}
	
	
		public function printErrors($separatorHTML){
			print implode($separatorHTML, $this->errors);
		}
	}