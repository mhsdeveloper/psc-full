<?php


	namespace Tools\Controllers;



	class Start {

		private $filePath = "";

		const XSLT_OUTPUT_PATH = SERVER_WWW_ROOT . "/html/publications/lib/xsl/psc-umbrellas.xsl";

		const XSL_START = '<?xml version="1.0" encoding="UTF-8"?>
		<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema" exclude-result-prefixes="xs" version="2.0">
			<xsl:variable name="umbrellas">';

		const XSL_END = '</xsl:variable></xsl:stylesheet>';


		function __construct(){
			$this->AR = new \Publications\AjaxResponder2();
		}



		function index(){
			$this->_mvc->render("start.php");
		}




		function storeMenu(){
			if(!isset($_POST['menu'])){
				$this->fail("No menu provided.");
			}

			if(!file_put_contents(\MHS\Env::INCLUDES_PATH . "header.html", $_POST['menu'])){
				$this->fail("unable to save header to includes folder.");
			}		

			$this->AR->addData("status", "OK");
			$this->AR->setStatusOK();
			$this->AR->respond();
			exit();
		}




		function generateUmbrellaList(){
			$appLoader = new \SplClassLoader('SubjectsManager', SERVER_WWW_ROOT . 'html/subjectsmanager/classes');
			$appLoader->register();
		
			$db = new \MHS\Needle(\SubjectsManager\Models\DataModel::DATABASE, \SubjectsManager\Models\DataModel::USER, \SubjectsManager\Models\DataModel::PASSWORD);

			//get all subjects
			$query = "SELECT topic_name, id, is_umbrella FROM topics ORDER BY topic_name";

			try {
				$db->prepQuery($query);
				$db->runQuery();
				$rows = $db->getAllRows();

				//get all umbrella ids, and build out id-based lookup
				$umbrellas = [];
				$topicsById = [];

				foreach($rows as $topic){
					if($topic['is_umbrella']){
						$umbrellas[$topic['id']] = $topic['topic_name'];
					}

					$topicsById[$topic['id']] = ["topic_name" => $topic['topic_name'], "umbrellas" => []];
				}

				//get all relationships WHERE related_topic_id IN 
				$umbrellasInClause = array_keys($umbrellas);
				$inClause = implode(",", $umbrellasInClause);

				$query = "SELECT related_topic_id, topic_id FROM topic_relationships WHERE related_topic_id IN(" . $inClause . ") AND relationship = 'broadensTo' ORDER BY related_topic_id";
				$db->prepQuery($query);
				$db->runQuery();
				$rows = $db->getAllRows();

				//add umbrella names to each topic
				foreach($rows as $relationship){
					$umbrella = $umbrellas[$relationship['related_topic_id']];
					$topicsById[$relationship['topic_id']]['umbrellas'][] = $umbrella; 
				}

				//build xml
				$xml = self::XSL_START;

				foreach($topicsById as $topic){
					$xml .= '<topic name="' . $topic['topic_name'] . '">';
					foreach($topic['umbrellas'] as $umb){
						$xml .= "<u>" . $umb . "</u>";
					}
					$xml .= '</topic>' . "\n";
				}

				$xml .= self::XSL_END;

				if(!file_put_contents(self::XSLT_OUTPUT_PATH, $xml)){
					$this->fail("Unable to write changes to the xslt file: " . self::XSLT_OUTPUT_PATH);
					exit();
				}

				$this->AR->addData("status", "OK");
				$this->AR->setStatusOK();
				$this->AR->respond();
				exit();
				

			} catch(Exception $e){
				$errors = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				$this->fail($errors);
				exit();
			}
		}




		function storeFooter(){
			if(!isset($_POST['data'])){
				$this->fail("No menu provided.");
			}

			if(!file_put_contents(\MHS\Env::INCLUDES_PATH . "footer.html", $_POST['data'])){
				$this->fail("unable to save footer to includes folder.");
			}		

			$this->AR->addData("status", "OK");
			$this->AR->setStatusOK();
			$this->AR->respond();
			exit();
		}




		function storeCss(){
			if(!isset($_POST['css'])){
				$this->fail("No css provided.");
			}

			if(!file_put_contents(\MHS\Env::INCLUDES_PATH . "csshead.html", $_POST['css'])){
				$this->fail("unable to save css to includes folder.");
			}		

			$this->AR->addData("status", "OK");
			$this->AR->setStatusOK();
			$this->AR->respond();
			exit();
		}

		protected function fail($msg){
			$this->AR->setStatusFailed();
			$this->AR->errors($msg);
			$this->AR->respond();
			exit();
		}

	}