<?php


	namespace DocManager\Controllers;


	/* class for retrieving
	*/

	class DocumentFiles extends AjaxController {


		public function index(){

			$path = \MHS\Env::SOURCE_FOLDER;

			$files = scandir($path);


			$this->AR->statusOk();
			$this->AR->data("files", $files);
			$this->AR->respond();
		}


	}