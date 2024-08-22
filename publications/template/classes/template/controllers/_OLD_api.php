<?php


	namespace JQADiaries\Controllers;




	class API {

		function __construct(){
			$this->Config = \MHS\Env::getInstance();

			$this->AR = new \Publications\AjaxResponder();
		}


		function index(){

			if(!isset($_GET['query'])){
				$this->AR->errors("query not specified");
				$this->AR->respond();
				exit();
			}

			switch($_GET['query']){
				case "docexists":  $this->docExists(); break;
			}
		}



		function docExists(){

			if(!isset($_GET['id'])){
				$this->AR->errors("id not specified");
				$this->AR->respond();
				exit();
			}

			$YearViewer = new \Publications\Metadata\YearReader($this->Config);

			//determine year from id
			$parts = explode("-", $_GET['id']);
			$count = count($parts);

			for($x=0; $x<$count; $x++){
				if(strlen($parts[$x]) == 4){
					$year = $parts[$x];
					break;
				}
			}
			//error if not year
			if(!isset($year)){
				$this->AR->errors("year not determined from id");
				$this->AR->respond();
				exit();
			}


			//this object just knows how to find and retrieve the json for a year
			$yearArray = $YearViewer->getYearArray($year);

			//this object has methods to traverse the array to find our key
			$MD = new \Foghorn\Models\MetadataArrayKeeper($this->Config, "years");

			$MD->import($yearArray);

			$results = $MD->findKeys($_GET['id']);
			if(count($results)) {
				$this->AR->data("docexists", true);
				$this->AR->data("filename", $MD->getDeepValue($results[0] . "/filename"));
			}


			$this->AR->data("id", $_GET['id']);
			$this->AR->respond();
			exit();
		}

	}
