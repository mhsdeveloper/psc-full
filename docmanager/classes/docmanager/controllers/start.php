<?php


	namespace DocManager\Controllers;


//extends \Publications\Metadata\Responder
	class Start  {

		private $filePath = "";

		function __construct(){
			$this->AR = new \Publications\AjaxResponder();
		}



		function index(){
			$this->_mvc->render("start.php");
		}

		function steps(){
			$this->_mvc->render("steps.php");
		}


		function documents(){
			$doc = new \DocManager\Models\Document();
			
		}

	}