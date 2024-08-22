<?php


	/* general purpose csv spreadsheet iterator/processor/lookover thingy
	 *
	 * Usage:
	 *
	 * 	$C = new \MHS\CsvProcessor();
	 * 	$C->loadFile("file.csv");
	 * 	$C->cleanLineEndings();
	 * 	$C->each(','); //comma separated cells
	 *
	 * This will just printout the contents of the csv. To do anything more,
	 * you'll need to extend the class with your own. You need to override:
	 *
	 * ->processRow();
	 *
	 * This functon should access the property array ->row whose array members
	 * are the columns of that row.
	 *
  	 */
	
	
	
	
	namespace MHS;
	
	
	
	class CsvProcessor {
	
	
		/* our string of data, once loaded from the CSV
		 */
		private $content;
	
	
	
		/* our line separator
		 */
		private $lineSeparator = "\n";
	
	
	
		/* flag to skip the first row, which is often labels
		 */
		private $skipFirstLine = false;
	
	
	
		/* track number of cells per row:
		 * if this changes from one row to the next, we can guess there was a parsing error
		 */
		private $columnCount = 0;
	
	
		
		public function loadFile($file){
			if(!is_readable($file)) $this->fatalError("Unable to read {$file}");
			$this->content = file_get_contents($file);
			return true;
		}
		
		
		/* remove windows \r and extra line breaks
		 */
		public function cleanLineEndings(){
			$this->content = str_replace(array("\r", "\n\n"), array("\n", "\n"), $this->content);
		}
		
		
		
		public function setLineSeparator($lineSeparator){
			$this->lineSeparator = $lineSeparator;
		}
		
		
		public function skipFirstLine(){
			$this->skipFirstLine = true;
		}
		
		
		
		/*
		 */
		public function each($cellSeparator){
			
			$lines = explode($this->lineSeparator, $this->content);
			
			$this->rowCount = 0;
			
			foreach($lines as $line){
				
				$this->rowCount++;

				if($this->rowCount == 1 && $this->skipFirstLine) continue;
				
				///separate columns
				$this->row = explode($cellSeparator, $line);
				
				//look if we have differing cell counts
				$count = count($this->row);
				
				//skip blank lines
				if(empty($line)) continue;
				
				// except for the first row when it's zero, or a blank line
				if($count != $this->columnCount && $this->columnCount > 0 && $count != 0) {
					$this->error("Bad column count at row " . $rowCount);
				}
				
				$this->columnCount = $count;
				
				$this->processRow();
			}
			
		}
		
		
		
		public function processRow(){
			foreach($this->row as $column) {
				print $column . " | ";
			}
			
			print "<br/>\n";
		}
	
	
	
	
		private function error($message){
			
			print '
				<div style="background: red; color: white">' .
				$message .
				"</div>\n\n";
		}
		
		
		private function fatalError($message){
			$this->error($message);
			exit();
		}
	
	
	} //class
	