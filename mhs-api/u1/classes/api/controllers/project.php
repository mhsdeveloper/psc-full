<?php


	namespace API\Controllers;



	class Project extends Controller {
		
		function addNames(){

			if(!isset($_GET['ids'])){
				$this->fail("No name ids provided.");
			}

			$ids = explode(",", $_GET['ids']);

			$project = new \API\Models\ProjectModel();

			if(!$project->addNames($ids)) {
				$this->fail("Unable to add names to project");	
			}
			
			$this->JSONRespond();
		}


		function removeNames(){

			if(!isset($_GET['ids'])){
				$this->fail("No name ids provided.");
			}

			$ids = explode(",", $_GET['ids']);

			$project = new \API\Models\ProjectModel();

			if(!$project->removeNames($ids)) {
				$this->fail("Unable to add names to project");	
			}
			
			$this->JSONRespond();
		}

		function addMissingHuscs(){
			if(!isset($_GET['h'])){
				$this->fail("No huscs provided.");
			}

			$huscs = explode(";", $_GET['h']);

			$project = new \API\Models\ProjectModel();

			if(!$project->auditHuscsInProject($huscs)){
				$this->fail("Unable to add HUSCs to project");	
			}
					
			$newNames = $project->getAddedHuscs();

			$this->JSONRespond($newNames);
		}

	}