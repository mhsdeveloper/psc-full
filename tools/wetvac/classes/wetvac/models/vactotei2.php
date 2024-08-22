<?php


	namespace Wetvac\Models;



	class VacToTei2 extends \MHS\TxtProcessor\LineByLine {

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


		private $milestones = [
			"Transcription" => false,
			"persRef" => false,
			"Subjects" => false,
			"Annotation" => false
 		];



		public function setText($text = false){
			$this->text = $text;
		}


		public function getText(){
			return $this->text;
		}



		public function setIdRoot($id){
			$this->idRoot = $id;
			$this->docID = $id;
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



		public function separateDocParts(){
			$parts1 = explode("<body>\n", $this->text);
			$parts2 = explode("</body>\n", $parts1[1]);
			$this->teiHeader = $parts1[0] . "<body>\n";
			$this->teiDocMain =  $parts2[0];
			$this->teiDocEnding = "</body>\n" . $parts2[1];
		}


		public function rejoinParts(){
			$this->text = $this->teiHeader . $this->teiDocMain . $this->teiDocEnding;
		}


		public function formOutput(){
			$this->outputDOM = new \Wetvac\Models\DOMBuilder();
			$this->outputDOM->init($this->text);
			$this->outputDOM->getBodyElements();
/*			if(false == $this->outputDOM->placeMajorElements()){
				return false;
			}
*/
			$this->text = $this->outputDOM->getXML();
		}



		/* this also accepts a string input (coming from chunkByChunk), but
		 * when $text === false, it uses full teiDocMain as text
		 *
		 * so when NOT passing in $text, we operate on ->teiDocMain, and keep
		 * that updated.
		 *
		 * When passing in Text, we keep teiDocMain separate
		 */
		public function processMarkers($text = false){

			$noTextArg = false;
			$this->inSource = false;

			if($text === false) {
				$noTextArg = true;
				$text = $this->teiDocMain;
			}
			$this->setText($text);

			$this->preMarkerFixes();

			$tool = new \Wetvac\Models\MarkerTool();
			$tool->parseFullLineMarker("DATE", $this->text)->processContent("Wetvac\Models\VacToTei2::parseDate")->paragraphsToElements("date")->writeLine();
			$tool->parseFullLineMarker("EDITOR", $this->text)->paragraphsToElements("editor")->writeLine();
			$tool->parseFullLineMarker("EDITION", $this->text)->paragraphsToElements("edition")->writeLine();
			$tool->parseFullLineMarker("AUTHOR", $this->text)->paragraphsToElements("author")->writeLine();
			$tool->parseFullLineMarker("RECIPIENT", $this->text)->paragraphsToElements("recipient")->writeLine();
			$tool->parseFullLineMarker("HEAD", $this->text)->paragraphsToElements("head")->writeLine();
			$tool->parseFullLineMarker("SUBJECT", $this->text)->paragraphsToElements("subject")->writeLine();
			$tool->parseFullLineMarker("TRANSCRIBER", $this->text)->paragraphsToElements("transcriber")->writeLine();
			$tool->parseFullLineMarker("TRANSCRIPTION-DATE", $this->text)->paragraphsToElements("transcriptionDate")->writeLine();

			$this->hasNotes = false;

			//---- more detailed line-by-line processing
			$this->forEachLine(function($line){

				//allows singular or plural
				if($line->contains("{{MILESTONE")){
					$text = strtolower($line->getText());
					if(strpos($text, "transcriptio") !== false) $this->milestones['Transcription'] = "3";
					if(strpos($text, "persref") !== false) $this->milestones['persRef'] = "3";
					if(strpos($text, "subject") !== false) $this->milestones['Subjects'] = "3";
					if(strpos($text, "annotation") !== false) $this->milestones['Annotation'] = "4";
					return;
				}
				

				if($line->contains("{{PS}}")) {
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

				else if($line->contains("{{NOTE}}")) {
					$this->hasNotes = true;
					//close any sections
					$this->closeSection();
					$this->newSection("<note type=\"fn\">", "</note>");
				}

				//add the line
				$this->append($line);
			});

			//close any sections
			$this->closeSection();

/*			//if no notes, close source section
			if($this->hasNotes){
				$this->appendOutput("</note>\n");
			}
*/

			//rest of processes operate directly on text, so copy output back to text
			$this->updateText();

			//some paragraph replacements, single line or string, not sectional
			$this->findString("{{SALUTE}}")->inParagraphs()->rewrapWith("salute")->remove();
			$this->findString("{{CLOSE}}")->inParagraphs()->rewrapWith("farewell")->remove();

			$this->findString("{{DATELINE}}")->inParagraphs()->rewrapWith("dateline")->remove();
			$this->findString("{{SIGNED}}")->inParagraphs()->rewrapWith("signed")->remove();

			$this->findString("{{ILL}}")->replaceWith("<unclear/>");
			$this->findString("{{DAMAGE}}")->replaceWith("<gap/>");
			$this->findString("{{BLANK}}")->replaceWith("<space/>");
			$this->findString("<p>{{BLANK-BLOCK}}</p>")->replaceWith("<space type=\"block\"/>");

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

/*			//move insertions to correct location between docbody and docback
			preg_match_all('#<div type="insertion".*</div>#sU', $this->text, $matches);
			$insertions = implode("\n", $matches[0]);
			//remove $insertions
			foreach($matches as $ins){
				$this->text = str_replace($ins, "", $this->text);
			}
*/			
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

			$this->postMarkerFixes();

			//to finish, look at first arg: if we had passed in text, then pass back. otherwise write it back to teiDocMain
			if($noTextArg) {
				$this->teiDocMain = $this->text;
				return;
			}

			//we had passed in a text arg, so return new text
			return $this->text;
		}


		
		private function preMarkerFixes(){
			//no longer use {{SOURCE}}
			$this->text = preg_replace("#\s*<p>\s*\{\{\s*SOURCE\s*\}\}.*<\/p>\s*#sU", "", $this->text);

			$this->text = str_replace("\r\n", "\n", $this->text);
		}


		private function postMarkerFixes(){
			//remove empty paragraphs
			$this->text = preg_replace("/<p>\s*<\/p>/sU", "", $this->text);
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






		static function parseDate($text){

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



		private function parseComments(){
			//get comments out of the way
			$this->text = preg_replace("/(\{\{COMMENT\:)(.*)(\}\})/sU", "<!--$2-->", $this->text);
			$this->text = str_replace(["<p><!--", "--></p>"], ["<!--", "-->"], $this->text);
		}


		public function numberNotes(){
			//first number the ptrs 
			$noteNo = 0;
			$this->text = preg_replace_callback('/<ptr type="noteRef"/', function($match) use (&$noteNo) {
				$noteNo++;
				return '<ptr type="fn" n="' . $noteNo . '" target="'. $this->docID . '-fn' . $noteNo   .'"'; 
			}, $this->text);

			//now the notes
			$noteNo = 0;
			$this->text = preg_replace_callback('/<note type="fn"/', function($match) use (&$noteNo) {
				$noteNo++;
				return '<note type="fn" xml:id="'. $this->docID . '-fn' . $noteNo   .'"'; 
			}, $this->text);
		}


		public function getSetMilestones($queryParams = []){
			//these end up being the params send to the XSLT, hence named "params"

			$params = [
				"doneMileTranscription" => $this->milestones['Transcription'],
				"doneMilePersRef" => $this->milestones['persRef'],
				"doneMileSubjects" => $this->milestones['Subjects'],
				"doneMileAnnotation" => $this->milestones['Annotation']
			];

			//override
			if(isset($queryParams['transcriptionMilestone'])) $params['doneMileTranscription'] = $queryParams['transcriptionMilestone'];
			if(isset($queryParams['persrefsMilestone'])) $params['doneMilePersRef'] = $queryParams['persrefsMilestone'];
			if(isset($queryParams['subjectsMilestone'])) $params['doneMileSubjects'] = $queryParams['subjectsMilestone'];
			if(isset($queryParams['annotationsMilestone'])) $params['doneMileAnnotation'] = $queryParams['annotationsMilestone'];

			return $params;

		}

	}
