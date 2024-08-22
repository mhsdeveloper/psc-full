<?php


	namespace API\Controllers;

	class ExtName extends Controller {

		function index(){
			if(!isset($_GET['husc'])){
				$this->fail("Error: no HUSC provided.");
			}

			$husc = preg_replace("/[^a-zA-Z0-9\-]/U", "", $_GET['husc']);

			$name = new \API\Models\NameModel();
			$result = $name->get($husc);

			if(!$result){
				$this->fail("Unable to find name for " . $husc);
			}

			$nameData = $result[0];

			//now get descriptions
			$descriptions = $name->getProjectMetadata($nameData['id']);
			$nameData['descriptions'] = $descriptions;

			$links = $name->getLinks($nameData['id']);
			$nameData['links'] = $links;

			$this->JSONRespond($nameData);
		}
	}