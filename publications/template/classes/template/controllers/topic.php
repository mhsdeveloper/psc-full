<?php


	namespace Template\Controllers;


	class Topic {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = \MHS\Env::SOLR_CORE;


		function __construct(){
			$this->Config = \MHS\Env::getInstance();
		}


		/* just render the html, JS will handle the queries
		*/
		function index(){
			$this->_mvc->render("topic");
		}

	} //class
