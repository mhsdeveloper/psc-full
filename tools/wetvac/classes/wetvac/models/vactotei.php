<?php


	namespace Wetvac\Models;



	class VacToTei extends \MHS\TxtProcessor\LineByLine {

		public $teiHeader = "";

		private $teiDocMain = "";

		private $teiDocEnding = "";

		//array of individual docs with the XML
		private $chunks = [];


		private $idRoot = "NOID";

		private $docID = "NOID";
		
		private $docCount = 0;

		private $metadata = [
			"document-id" => "",
			"transcriber" => "",
			"transcription-date" => "",
			"editor" => "",
			"edition" => "",
			"authors" => "",
			"recipient" => "",
			"date" => "",
			"head" => "",
			"subject" => []
		];




		public function text($text = false){
			if(false == $text) return $this->text;
			else $this->text = $text;
		}



		public function setIdRoot($id){
			$this->idRoot = $id;
		}




		public function separateDocParts(){
			$parts1 = explode("<body>\n", $this->text);

			$parts2 = explode("</body>\n", $parts1[1]);

			$this->teiHeader = $parts1[0] . "<body>\n";

			$this->teiDocMain =  $parts2[0];

			$this->teiDocEnding = "</body>\n" . $parts2[1];
		}


		public function chunkByChunk(){

			//first expand {{DOC}} placeholder
			$this->teiDocMain = str_replace("{{DOC}}", "{{DOC}}**DOCLINE**", $this->teiDocMain);

			//KEEP IN MIND there's a paragraph surrounding the {{DOC}} and the following ID
			$this->chunks = explode("<p>{{DOC}}", $this->teiDocMain);

			//zero our text holder so we can add chunks as they are done
			$this->teiDocMain = "";

			foreach($this->chunks as $chunk){

				//skip leading or trailing empty doc
				if(strlen($chunk) < 10) continue;

				$chunk = $this->processDocument($chunk);

				$this->teiDocMain .= $chunk;
			}
		}





		public function rejoinParts(){
			$this->text = $this->teiHeader . $this->teiDocMain . $this->teiDocEnding;

			return $this->text;
		}





		public function gleanMetadata(){

			//which line of actual content are we at?
			$this->contentLineCount = 0;

			$this->foundDate = false;
			$this->foundHeader = false;


			$this->forEachLine(function($line){
/*				//llook for doc id
				if($line->contains("**DOCLINE**")) {
					$this->metadata['document-id'] = $line->trimLeading()->trimTrailingP()->getText();
				}
*/
				//lines beginning with {{  are our header metadata (transcriber, etc)
				if($line->contains("<p>{{")){
					if($line->contains("{{TRANSCRIBER}}")) $this->metadata['transcriber'] = $line->trimLeading()->trimTrailingP()->getText();
					else if($line->contains("{{TRANSCRIPTION-DATE}}")) {
						$tempdate = $line->trimLeading()->trimTrailingP()->getText();
						$this->metadata['transcription-date'] = $this->parseDate($tempdate);
					}
					else if($line->contains("{{TRANSCRIPTION DATE}}")) {
						$tempdate = $line->trimLeading()->trimTrailingP()->getText();
						$this->metadata['transcription-date'] = $this->parseDate($tempdate);
					}
					else if($line->contains("{{EDITOR}}")) $this->metadata['editor'] = $line->trimLeading()->trimTrailingP()->getText();
					else if($line->contains("{{EDITION}}")) $this->metadata['edition'] = $line->trimLeading()->trimTrailingP()->getText();
					else if($line->contains("{{AUTHOR}}")) $this->metadata['authors'] = $line->trimLeading()->trimTrailingP()->getText();
					else if($line->contains("{{RECIPIENT}}")) $this->metadata['recipients'] = $line->trimLeading()->trimTrailingP()->getText();
					else if($line->contains("{{HEAD}}")) $this->metadata['head'] = $line->trimLeading()->trimTrailingP()->getText();
					else if($line->contains("{{SUBJECT}}")) $this->metadata['subject'][] = $line->trimLeading()->trimTrailingP()->getText();
					else if($line->contains("{{DATE}}")) {
						$tempdate = $line->trimLeading()->trimTrailingP()->getText();
						$this->metadata['date'] = $this->parseDate($tempdate);
					}

				}
			});

			//reconcile what we've found for the head
			$this->parseHead();

			//reset doc ID and add individual doc incrementor
			$this->docCount++;
			
			$docIDinc = "" . $this->docCount;
			while(strlen($docIDinc) < 3) $docIDinc = "0" . $docIDinc;
			
			$this->docID = $this->idRoot . "-" . $docIDinc;
		}




		/* this also accepts a string input (coming from chunkByChunk), but
		 * when $text === false, it uses full teiDocMain as text
		 *
		 * so when NOT passing in $text, we operate on ->teiDocMain, and keep
		 * that updated.
		 *
		 * When passing in Text, we keep teiDocMain separate
		 */
		public function processDocument($text = false){

			$noTextArg = false;
			$this->inSource = false;

			if($text === false) {
				$noTextArg = true;
				$text = $this->teiDocMain;
			}

			$this->setText($text);

			//get comments out of the way
			$this->text = preg_replace("/(\{\{COMMENT\:)(.*)(\}\})/sU", "<!--$2-->", $this->text);
			$this->text = str_replace(["<p><!--", "--></p>"], ["<!--", "-->"], $this->text);


			$this->gleanMetadata();

			//add document beginning with placeholders to pop in metadata once we find it
/*			if(!empty($this->metadata['document-id'])) {
				$this->docID = $this->metadata['document-id'];
			}
*/			
			
			$this->appendOutput("<div type=\"doc\" xml:id=\"" . $this->docID . "\">\n{{BIBL}}\n");
			$this->newSection("<div type=\"docbody\">",""); //no closing tag because we'll need to nest other sections
//			$this->appendOutput("\n{{HEAD}}\n");

			$this->noNotes = true;

			//---- more detailed line-by-line processing
			$this->forEachLine(function($line){

				if($line->contains("{{CLOSE}}")) {
					$this->newSection("<closer>", "</closer>");
					$this->appendOutput($line->trimLeading()->trimTrailingP()->getText());
					return;
				}
				else if($line->contains("{{PS}}")) {
					$this->newSection("<postscript>", "</postscript>");
				}
				else if($line->contains("{{ADDRESS}}")) {
					$this->newSection("<note type=\"address\">", "</note>", "p");
				}
				else if($line->contains("{{INSERTION}}")) {
					$this->newSection("<div type=\"insertion\">", "</div>");
				}
				else if($line->contains("{{ENDORSEMENT}}")) {
					$this->newSection("<note type=\"endorsement\">", "</note>");
				}
				else if($line->contains("{{REPOSITORY}}")) {
					$this->newSection("<note type=\"repository\">", "</note>");
				}
				else if($line->contains("{{DOCTYPE}}")) {
					$this->newSection("<note type=\"doctype\">", "</note>");
				}
				else if($line->contains("{{COLLECTION}}")) {
					$this->newSection("<note type=\"collection\">", "</note>");
				}
				else if($line->contains("{{CONDITION}}")) {
					$this->newSection("<note type=\"condition\">", "</note>");
				}
				else if($line->contains("{{ENDORSEMENT}}")) {
					$this->newSection("<note type=\"endorsement\">", "</note>");
				}
				else if($line->contains("{{NOTATION}}")) {
					$this->newSection("<note type=\"notation\">", "</note>");
				}


				else if($line->contains("{{SOURCE}}")) {
					//close any sections
					$this->closeSection();
					//close docbody
					$this->appendOutput("</div>\n");
					//here we sneak in the docback div
					$this->newSection("<div type=\"docback\">\n", ""); //again, no closing tag since we need to next other sections
					$this->newSection("<note type=\"source\">", "");
					$this->inSource = true;
				}

				else if($line->contains("{{NOTE}}")) {
					$this->noNotes = false;
					//close any sections
					$this->closeSection();
					//close source
					if($this->inSource){
						$this->appendOutput("</note>\n");
						$this->inSource = false;
					}
					$this->newSection("<note type=\"fn\">", "</note>");
				}


				//add the line
				$this->append($line);
			});

			//close any sections
			$this->closeSection();

			//if no notes, close source section
			if($this->noNotes){
				$this->appendOutput("</note>\n");
			}

			//close document; closes the docback and the doc
			$this->appendOutput("\n</div>\n</div><!-- //document -->\n");
			//rest of processes operate directly on text, so copy output back to text
			$this->updateText();

			//some paragraph replacements, single line or string, not sectional
			$this->findString("{{SALUTE}}")->inParagraphs()->rewrapWith("salute")->remove();

			$this->findString("{{DATELINE}}")->inParagraphs()->rewrapWith("dateline")->remove();
			$this->findString("{{SIGNED}}")->inParagraphs()->rewrapWith("signed")->remove();

			$this->findString("{{ILL}}")->replaceWith("<unclear/>");
			$this->findString("{{DAMAGE}}")->replaceWith("<gap/>");
			$this->findString("{{BLANK}}")->replaceWith("<space/>");
			$this->findString("<p>{{BLANK-BLOCK}}</p>")->replaceWith("<space type=\"block\"/>");
/*
			//wrap the dateline/salute elements in <opener>
			//dateline will be first, so if we have it, wrap <opener>
			if($this->hasString("<dateline>")) {
				$this->findString("<dateline>")->replaceWith("<opener>\n<dateline>");
				//might have <salute, but not necessarily 
				if($this->hasString("</salute>")) $this->findString("</salute>")->replaceWith("</salute>\n</opener>");
				else $this->findString("</dateline>")->replaceWith("</dateline>\n</opener>");
			}
			else if($this->hasString("<salute>")){
				$this->findString("<salute>")->replaceWith("<opener>\n<salute>");
				$this->findString("</salute>")->replaceWith("</salute>\n</opener>");
			}
*/
			//interate over repeatables
			$this->replaceEach("{{PB}}", function($i){ return '<pb n="' . ($i + 2) . '"/>';	});
			$this->replaceEach("{{N}}", function($i){ return '<ptr type="noteRef"/>';});
			$this->replaceEach("{{INS}}", function($i){ return '<ptr type="insRef" n="' . ($i + 1) . '" target="' . $this->docID . "-ins" . ($i + 1) .  '"/>';	});
			$this->replaceEach("<div type=\"insertion\">", function($i){ return '<div type="insertion" xml:id="' . $this->docID . "-ins" . ($i + 1) .  '">';	});

			//supplied first, because we can block any with the ?
			$this->text = preg_replace("/\[([^\?]*)\]/sU", "<unclear>$1$2</unclear>", $this->text);

			//unclear
			$this->text = preg_replace("/\[([^\?]*)\?\]/sU", "<unclear cert=\"low\">$1</unclear>", $this->text);

			//abbreviations
			$this->text = preg_replace("/(\W)([\w'`~\?\-]+)\=\=([\w]+)(\W)/sU", "$1<choice><abbr>$2</abbr> <expan>$3</expan></choice>$4", $this->text);
			//remove any spaces

			//orig to reg
			$this->text = preg_replace("/(\W)([\w'`~\?\-]+)\+\+([\w]+)(\W)/sU", "$1<choice><orig>$2</orig> <reg>$3</reg></choice>$4", $this->text);

			$this->placeBibl();

			//move insertions to correct location between docbody and docback
			preg_match_all('#<div type="insertion".*</div>#sU', $this->text, $matches);
			$insertions = implode("\n", $matches[0]);
			//remove $insertions
			foreach($matches as $ins){
				$this->text = str_replace($ins, "", $this->text);
			}
			
			//place insertions
			$this->text = str_replace('<div type="docback"', $insertions . '<div type="docback"', $this->text);

			//deal with pseudo persrefs
			$this->text = preg_replace_callback('/(\{\{P\:)(.*)(\}\})/sU', 
				function($matches){
					if(count($matches) < 4) return $matches[0];
					return "<persRef ref=\"" . trim($matches[2]) . "\">";
				}
			, $this->text);

			$this->text = str_replace(["{{ENDP}}", "{{PEND}}"], "</persRef>", $this->text);

			$this->processedCarrots();


			//final clean up
			//PB with numbering
			$this->text = preg_replace('/(\{\{PB)\s+([0-9]+)(\}\})/', '<pb n="$2"/>', $this->text);

			//any remaining {{ becomes comments
			$this->text = str_replace(["{{", "}}"], ["<!--", "-->"], $this->text);

			//remove the paragraphs with the metadata
//			$this->findRegex('</bibl>.*<opener>', 's')->replaceWith("</bibl>\n<div type=\"docbody\"><opener>");

/*
			//fix closer wrapping; do this here, at end, to avoid confusing <salute> tags
			if($this->hasString("<closer>")){
				$this->findString("<closer>")->replaceWith("<closer>\n<salute>");

				//where do we placed the clsing </salute> ??
				if($this->hasString("<signed>")){
					$this->findString("<signed>")->replaceWith("</salute>\n<signed>");
				} else {
					$this->findString("</closer>")->replaceWith("</salute>\n</closer>");
				}

			} else if($this->hasString("<signed>")){
				$this->findString("<signed>")->replaceWith("<closer>\n<signed>");
				$this->findString("</signed>")->replaceWith("</signed>\n</closer>");
			}
*/
			//to finish, look at first arg: if we had passed in text, then pass back. otherwise write it back to teiDocMain
			if($noTextArg) {
				$this->teiDocMain = $this->text;
				return;
			}

			//we had passed in a text arg, so return new text
			return $this->text;
		}




		private function processedCarrots(){
			$place = 0;
			$callback = function($matches) use(&$place){
				$place++;
				if($place % 2) return "<add>";
				else return "</add>";
			};

			$this->text = preg_replace_callback("/\^/U", $callback, $this->text);
		}






		private function parseDate($text){

            $text = trim($text);

			//if we have dashes, split on those
			if(strpos($text, "-") !== false) $del = "-";
			else {
			     $del = " ";
			     //regularize spaces
			     $text = preg_replace("/\s\s+/", " ", $text);
			}

			$parts = explode($del, $text);
			foreach($parts as $i => $part){
    			$parts[$i] = trim($part);
			}

			$year = "0000";
			$month = $day = "00";

			//YEAR
			if(isset($parts[0]) and strlen($parts[0]) == 4 ) $year = $parts[0];

			//month
			if(isset($parts[1])){

				$rawmo = $parts[1];

				if(strlen($rawmo) == 2){
					$month = $rawmo;
				}

				else if(strlen($rawmo) > 2){
					//match month
					if(stripos($rawmo, "jan") === 0) $month = "01";
					else if(stripos($rawmo, "feb") === 0) $month = "02";
					else if(stripos($rawmo, "mar") === 0) $month = "03";
					else if(stripos($rawmo, "apr") === 0) $month = "04";
					else if(stripos($rawmo, "may") === 0) $month = "05";
					else if(stripos($rawmo, "jun") === 0) $month = "06";
					else if(stripos($rawmo, "jul") === 0) $month = "07";
					else if(stripos($rawmo, "aug") === 0) $month = "08";
					else if(stripos($rawmo, "sep") === 0) $month = "09";
					else if(stripos($rawmo, "oct") === 0) $month = "10";
					else if(stripos($rawmo, "nov") === 0) $month = "11";
					else if(stripos($rawmo, "dec") === 0) $month = "12";
				}

				else if(strlen($rawmo) == 1){
					$month = "0" . $rawmo;
				}
			}

			//DAY
			if(isset($parts[2])){
				if(strlen($parts[2]) < 3 and is_numeric($parts[2])) {
					$day = $parts[2];
					//fix single digitas
					if(strlen($day) == 1) $day = "0" . $day;
				}
			}


			return $year . "-" . $month . "-" . $day;
		}





		private function parseHead(){

			//authors specified, so we can just use the head as is
			if(!empty($this->metadata['authors'])) return;

			//if authors not specified in WET, parse head instead

			//discover any " to " grammar
			if(stripos($this->metadata['head'], "to ") === 0) $separator = "to ";
			else if(stripos($this->metadata['head'], " to ") !== false) $separator = " to ";
			else return;

			if(strpos($this->metadata['head'], $separator) !== false) {
				$parts = explode($separator, $this->metadata['head']);
				$this->metadata['authors'] = trim($parts[0]);
				$this->metadata['recipient'] = trim($parts[1]);
			}

		}




		private function placeHead(){
			$text = "<head>" . $this->metadata['head'] . "</head>";

			$this->findString("{{HEAD}}")->replaceWith($text);
		}



		private function placeBibl(){

			$text = "<bibl>\n";
			$text .= "\t<date type=\"creation\" when=\"" . $this->metadata['date'] . "\"/>\n";

			if(!empty($this->metadata['authors'])) {
				$aset = explode(";", $this->metadata['authors']);
				foreach($aset as $author){
					$text .= "\t<author>" . trim($author) . "</author>\n";
				}
			}

			if(!empty($this->metadata['recipients'])) {
				$aset = explode(";", $this->metadata['recipients']);
				foreach($aset as $r){
					$text .= "\t<recipient>" . trim($r) . "</recipient>\n";
				}
			}
			$text .= "\t<head>" . $this->metadata['head'] . "</head>\n";

			$text .= "\t<editor>" . $this->metadata['editor'] . "</editor>\n";
			$text .= "\t<edition>" . $this->metadata['edition'] . "</edition>\n";

			$text .= "\t<name type=\"transcriber\">" . $this->metadata['transcriber'] . "</name>\n";
			$text .= "\t<date type=\"transcription\" when=\"" . $this->metadata['transcription-date'] . "\"/>\n";

			foreach($this->metadata['subject'] as $subject){
				$text .= "\t<subject>". $subject . "</subject>\n";
			}

			$text .= "</bibl>";

			$this->findString("{{BIBL}}")->replaceWith($text);
		}


		public function numberNotes(){
			//first number the ptrs 
			$noteNo = 0;
			$this->text = preg_replace_callback('/<ptr type="noteRef"/', function($match) use (&$noteNo) {
				$noteNo++;
				return '<ptr target="'. $this->docID . '-fn' . $noteNo   .'"'; 
			}, $this->text);

			//now the notes
			$noteNo = 0;
			$this->text = preg_replace_callback('/<note type="fn"/', function($match) use (&$noteNo) {
				$noteNo++;
				return '<note type="fn" xml:id="'. $this->docID . '-fn' . $noteNo   .'"'; 
			}, $this->text);
		}

	}
