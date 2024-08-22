<?php


	namespace Wetvac\Models;

	
		
		
	class WetChecker {
	
		//string holding the raw TEI xml from our initial Word to TEI xslt
		private $tei;

		private $errors = [];
		
		//these are only allowed before $docbackStartingBlock
		//order in the array doesn't matter 
		private $blockTags = [
			"{{XMLID}}",
			"{{TRANSCRIBER}}",
			"{{TRANSCRIPTION-DATE}}",
			"{{EDITOR}}",
		    "{{EDITION}}",
		    "{{DATE}}",
			"{{AUTHOR}}",
			"{{RECIPIENT}}",
			"{{SUBJECT}}",
			"{{HEAD}}",
			"{{DATELINE}}",
			"{{SALUTE}}",
			"{{CLOSE}}",
			"{{SIGNED}}",
			"{{PS}}",
			"{{INSERTION}}",
			"{{SOURCE}}",
			"{{MILESTONES}}",
			"{{MILESTONE}}"
		];

		// these must only appear after $docbackStartingBlock, and no others can
		private $docbackBlocks = [
			"{{NOTE}}",
			"{{DOCTYPE}}",
			"{{REPOSITORY}}",
			"{{COLLECTION}}",
			"{{CONDITION}}",
		    "{{ADDRESS}}",
		    "{{ENDORSEMENT}}",
		    "{{NOTATION}}",
		];
		
		private $inlineBlocks = [
		    "{{ILL}}",
		    "{{PB}}",
		    "{{BLANK}}",
		    "{{INS}}",
		    "{{DAMAGE}}",
			"{{N}}",
			"{{ENDP}}",
			"{{PEND}}"
		];
		
		//these should also appear in one of the above arrays
		private $requiredBlocks = [
			"{{XMLID}}",
			"{{DATE}}",
			"{{SOURCE}}",
			"{{DOCTYPE}}",
			"{{REPOSITORY}}",
			"{{COLLECTION}}",
		];
		
		private $docbackStartingBlock = "{{SOURCE}}";
		
		private $hasErrors = false;
		
		private $details = ["badBlocks" => "", "docbackBlocks" => "", "curlyCount"  => "", "squareCount" => "",
		    "docTextBlocks" => "", "requiredBlocks" => "", "blocksAtP" => "", "noteCount" => ""];
	


		function msgFromErrorType($type){
			$msg = "";
			
			switch($type){
				case "docbackBlocks": $msg = "The following markers are not allowed after " . $this->docbackStartingBlock . ": "; break;

				case "curlyCount": $msg = "The number of {{ doesn't match the number of }}, so you have broken markers."; break;

				case "squareCount": $msg = "The number of [ doesn't match the number of ]."; break;
					
				case "docTextBlocks": $msg = "The following markers are not allowed before " . $this->docbackStartingBlock . ": "; break;
				
				case "requiredBlocks": $msg = "You are missing a required marker: "; break;

				case "blocksAtP": $msg = "The following block-level markers need to be placed right at the beginning of a new paragraph with no intervening whitespace: "; break;

				case "badBlocks": $msg = "The following markers are not part of the WETVAC system and are not allowed anywhere: "; break;
				
				case "noteCount": $msg = "You must have an equal number of {{N}} and {{NOTE}} markers, one {{N}} referencing each {{NOTE}}."; break;

				case "persRef": $msg = "Your persRef {{P:}}'s need to have matching {{ENDP}} markers, one for each."; break;

				case "carrots": $msg = "There are not an even number of ^s in your document."; break;
			}
			
			if(isset($this->details[$type])) $msg .= " " . $this->details[$type];
			
			return $msg;
		}



	
		function loadRawTEI($tei){
			$this->tei = $tei;
		}
	
	
		function checkMatchingCurly(){
		    preg_match_all("/\{\{/", $this->tei, $matches);
		    if(!isset($matches[0])) return true;
		    $openingCount = count($matches[0]);
		    
		    preg_match_all("/\}\}/", $this->tei, $matches);
		    if(!isset($matches[0])) return true;
		    $closingCount = count($matches[0]);
		    
		    if($openingCount != $closingCount) return false;
		    
		    return true;
		}
		
		
		function checkMatchingSquare(){
		    preg_match_all("/\[/", $this->tei, $matches);
		    if(!isset($matches[0])) return true;
		    $openingCount = count($matches[0]);
		    
		    preg_match_all("/\]/", $this->tei, $matches);
		    if(!isset($matches[0])) return true;
		    $closingCount = count($matches[0]);
		    
			if($openingCount != $closingCount) {
				if($openingCount > $closingCount){
					$this->details['squareCount'] = "Your are missing an closing ].";
				} else {
					$this->details['squareCount'] = "Your are missing an opening [.";
				}
				return false;
			}
		    
		    return true;
		}
		
		
		function checkBlocksAtP(){
		    $blockLevelTags = array_merge($this->blockTags, $this->docbackBlocks);
		    
		    $errorFree = true;
		    
		    foreach($blockLevelTags as $marker){
		        //does the marker even appear?
		        if(strpos($this->tei, $marker) !== false) {
		            //does the marker appear with a leading <p>?
		            $str = "<p>" . $marker;
		            if(strpos($this->tei, $str) === false) {
		                $this->details['blocksAtP'] .= $marker . " ";
		                $errorFree = false;
		            }
		        }
		    }
		    
		    return $errorFree;
		}
	
	
		function checkRequiredBlocks(){								
		    foreach($this->requiredBlocks as $marker){
		        if(strpos($this->tei, $marker) === false) {
		            $this->details['requiredBlocks'] = $marker;
		            return false;
		        }
		    }
		    
		    return true;
		}
		
		
		function checkDocTextBlocks(){
		    //no Source tag so all set
		    if(strpos($this->tei, $this->docbackStartingBlock) === false) return true;
		  
		    $halves = explode($this->docbackStartingBlock, $this->tei);  
		    //right at end, no further content, so fine
		    if(!isset($halves[0])) return true;
		    
		    preg_match_all("/\{\{[A-Z\-]+\}\}/", $halves[0], $matches);
		    
		    //nothing found
		    if(!isset($matches[0])) return true;
		    
		    $markers = $matches[0];
		    
		    //make sure no front tags in there
		    foreach($markers as $marker){
		        if(in_array($marker, $this->docbackBlocks)) {
		            $this->details[ "docTextBlocks" ] = $marker;
		            
		            return false;
		        }
		    }
		    
		    return true;
		}
		
		
		
		function checkDocBackBlocks(){
			//no Source tag so all set
			if(strpos($this->tei, $this->docbackStartingBlock) === false) return true;

			$halves = explode($this->docbackStartingBlock, $this->tei);
			//right at end, no further content, so fine
			if(!isset($halves[1])) return true;
			
			preg_match_all("/\{\{[A-Z\-]+\}\}/", $halves[1], $matches);

			//nothing found
			if(!isset($matches[0])) return true;
			
            $markers = $matches[0];
            //make sure no front tags in there
            foreach($markers as $marker){
                if(in_array($marker, $this->blockTags)) {
                    $this->details[ "docbackBlocks" ] = $marker;
                    
                    return false;
                }
            }
                
			return true;	
		}
		
		
		function checkBadBlocks(){
		    $allBlocks = array_merge($this->blockTags, $this->docbackBlocks, $this->inlineBlocks);
		    preg_match_all("/\{\{[A-Z\-]+\}\}/", $this->tei, $matches);
		    
		    $errorFree = true;
		    
		    foreach($matches[0] as $marker){
		        if(!in_array($marker, $allBlocks)){
		          $this->details['badBlocks'] .= "<br/>\n" . $marker;
		          $errorFree = false;
		        }
		    }
		    
		    return $errorFree;
		}
		
		
		function checkNoteCount(){
		    preg_match_all("/\{\{N\}\}/", $this->tei, $matches);
		    if(!isset($matches[0])) $nbCount = 0;
    		else $nbCount = count($matches[0]);

    		preg_match_all("/\{\{NOTE\}\}/", $this->tei, $matches);
    		if(!isset($matches[0])) $nCount = 0;
    		else $nCount = count($matches[0]);
    		
    		if($nbCount + $nCount > 0 and $nbCount != $nCount){
				if($nbCount > $nCount){
					$this->details['noteCount'] = "Your are missing a {{NOTE}}.";
				} else {
					$this->details['noteCount'] = "Your are missing a {{N}}.";
				}

				return false;
			}
    		else return true;
		}


		
		function matchingPersRef(){
		    preg_match_all("/\{\{P:(.*)\}\}/U", $this->tei, $matches);
		    if(!isset($matches[0])) $nbCount = 0;
    		else $nbCount = count($matches[0]);

    		preg_match_all("/\{\{ENDP\}\}/U", $this->tei, $matches);
    		if(!isset($matches[0])) $nCount = 0;
    		else $nCount = count($matches[0]);
    		
    		if($nbCount + $nCount > 0 and $nbCount != $nCount){
				if($nbCount > $nCount){
					$this->details['persRef'] = "You are missing a closing {{ENDP}}.";
				} else {
					$this->details['persRef'] = "You are missing an opening {{P:}}.";
				}
				return false;
			}
    		else return true;
		}
		

		function checkCarrots(){
			preg_match_all("/\^/", $this->tei, $matches);
		    if(!isset($matches[0])) $count = 0;
    		else $count = count($matches[0]);

			if($count % 2) return false;
			return true;
		}

		
		
		function fullCheck(){
			
			//check matching {{
		    if($this->checkMatchingCurly() === false) $this->error("curlyCount");
			
			//check matching [ ]
		    if($this->checkMatchingSquare() === false) $this->error("squareCount");
			
			//check all blocks at start of <p>	
			if($this->checkBlocksAtP() === false) $this->error("blocksAtP");
			
			//check required blocks
			if($this->checkRequiredBlocks() == false) $this->error("requiredBlocks");
			
			//check wrongly-named blocks
			if($this->checkBadBlocks() == false) $this->error("badBlocks");
			
		    //check blocks not allowed before source
		    if($this->checkDocTextBlocks()=== false) $this->error("docTextBlocks");
		    
			//check blocks after SOURCE
			if($this->checkDocBackBlocks() === false) $this->error("docbackBlocks");
			
			//check matching ^
			if($this->checkCarrots() === false) $this->error("carrots");
						
			//check match count of {{N}} and {{NOTE}}
            if($this->checkNoteCount() === false) $this->error("noteCount");
			
			//check ++ no spaces around
			
			//check == no spaces around

			//check persRef matches
			if($this->matchingPersRef() === false) $this->error("persRef");
			
			if($this->hasErrors) return false;
			
			return true;			
		}
	
	
	
	

		protected function error($typeObj){
			$this->hasErrors = true;

			if(is_string($typeObj)){
				$type = $typeObj;
				$msg = $this->msgFromErrorType($type);
			}
			else {
				if(isset($typeObj['type'])) $type = $typeObj['type'];
				$msg = $this->msgFromErrorType($type);
				if(isset($typeObj['message'])){
					$msg .= $typeObj['message'];
				}
			}

			$this->errors[] = $msg;
	    
			return false;
		}


		public function getErrors(){
			return $this->errors;
		}

	
	} //endclass
