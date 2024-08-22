<?php


	namespace Template\Controllers;


	class Person {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = \MHS\Env::SOLR_CORE;


		function __construct(){
			$this->Config = \MHS\Env::getInstance();
		}


		/* just render the html, JS will handle the queries
		*/
		function index(){

			$data = [];
			$data['husc'] = $this->_mvc->segment(3);

			$this->_mvc->render("person", $data);
		}

	} //class
