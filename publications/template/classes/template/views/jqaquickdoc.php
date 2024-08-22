<?php


	namespace JQADiaries\Views;


	class JQAQuickDoc extends \Publications\Metadata\Views\QuickDoc {

			/* expected properties for $this->build() are:

			$this->properties['doc'] : the document object from the metadata json, it will have at a minimum these properties:
											xmlid, dateFrom, dateTo, filename, head, teaser
			$this->properties['year']  : 4 digit year string
			$this->properties['month'] : 2 digit month
			$this->properties['idDate'] : a string component to represent the day of the month used for creating IDs; maybe the keywords "spans"
			$this->properties['day'] : 2 digit day of the month, or the string "spans"
			$this->properties['identifierField'] : this is a key to a property in the doc json metadata object
			$this->properties['docURLPrefix'] : this is the entire URL leading up to the unique last URL Segment that creates the URL to the document view

			*/


			static function buildRetrievalString($filename, $xmlid){
				/*
					we take the xml:id jqadiaries-v33-1825-04-01
					and the filename: JQADiaries-v33-1825-04-p351.xml
					and create a single unique string that has all this:
					Steps:
					 - we keep lowercase;
					 - remove extension and dot from filename
					 - grab last - separated token, the page number
					 - append to get: jqadiaries-v33-1825-04-01-p351
				*/
				$filename = str_replace(".xml", "", $filename);
				$tokens = explode("-", $filename);
				$page = array_pop($tokens);

				return $xmlid . "-" . $page;
			}


	}
