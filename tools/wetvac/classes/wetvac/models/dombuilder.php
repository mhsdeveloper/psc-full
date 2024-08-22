<?php

	namespace Wetvac\Models;

	class DOMBuilder {

		private $TEI;

		private $elements = [];
		public $errorMsgs = [];

		private $biblElements = ["date", "author", "recipient", "editor", "edition", "transcriber", "transcriptionDate", "subject", "head"];
		private $docbodyElements = ["opener", "head", "dateline", "p", "closer", "postscript"];
		private $docbackElements = ["note"];
		private $sourceTypes = ["condition", "doctype", "repository", "address", "collection"];

		public function __construct(){
		}


		public function init($xml){
		    $this->TEI = new \DOMDocument();
		    $this->TEI->loadXML($xml);

            $this->xpath = new \DOMXPath($this->TEI);
            $this->xpath->registerNamespace("tei", "http://www.tei-c.org/ns/1.0");

		}


		public function getBodyElements(){
			$body = $this->TEI->getElementsByTagName("body");

			if($body->length == 0) {
				$this->errorMsgs[] = "No body element found.";
				return false;
			}

			$this->body = $body->item(0);

			foreach($this->body->childNodes as $node){
				if($node->nodeType == XML_ELEMENT_NODE){
					$this->elements[] = $node;
				}
			}
		}


/*
		public function placeMajorElements(){
			$el = $this->TEI->createElement("div");
			$el->setAttribute("type", "docbody");
		    $docbody = $this->body->insertBefore($el, $this->body->firstChild);

			$el = $this->TEI->createElement("bibl");
		    $bibl = $this->body->insertBefore($el, $this->body->firstChild);

			$el = $this->TEI->createElement("div");
			$el->setAttribute("type", "docback");
		    $docback = $this->body->appendChild($el);

			foreach($this->elements as $element){
				if(in_array($element->nodeName, $this->biblElements)){
					$bibl->appendChild($element);
				} else if(in_array($element->nodeName, $this->docbodyElements)){
					$docbody->appendChild($element);
				} else if(in_array($element->nodeName, $this->docbackElements)){
					$docback->appendChild($element);
				}
			}
			return true;
		}
*/


			public function getXML(){
			return $this->TEI->saveXML();
		}



		public function buildBibl(){
		    $author = new \DOMElement("author", "", "http://www.tei-c.org/ns/1.0");
		    $author->textContent = "JQA";
		    $bibl->appendChild($author);

		    $date = new \DOMElement("date", "", "http://www.tei-c.org/ns/1.0");
		    $date = $bibl->appendChild($date);
		    $date->setAttribute("type", "creation");
		    $date->setAttribute("when", $this->isoDate);

		    $editor = new \DOMElement("editor", "" , "http://www.tei-c.org/ns/1.0");
		    $editor = $bibl->appendChild($editor);
		    $editor->setAttribute("role", "transcription");
		    $editor->textContent = "Neal Millikan";
		}
	}