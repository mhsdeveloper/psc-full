<?php

	namespace Publications;

	require_once("mhs_global_env.php");

	class IndexXML {

		function __construct($Config){
			$this->Config = $Config;
			$this->postURL = SOLR_IP . $this->Config->configSOLR['POST_URL']; 
		}



		function processFile($filename){
			//check destination
			if(!is_readable($this->Config->configSOLR['DESTdir'])) {
				if(!mkdir($this->Config->configSOLR['DESTdir'])){
					error_log("Unable to create dest dir: " . $this->Config->configSOLR['DESTdir']);

					return false;
				}
			}

			$XSLTRunner = new \MHS\XSLT2Runner($this->Config);

			foreach($this->Config->configSOLR["XSLTparams"] as $param => $value){
				$XSLTRunner->addParameter($param, $value);
			}
			$XSLTRunner->addParameter("index", $this->Config->configSOLR["SOLRindex"]);
			$XSLTRunner->addParameter("filename", $filename);

			$XSLTRunner->setFilename($filename);
			$XSLTRunner->setOutputFilename($filename, $this->Config->configSOLR['DESTdir']);

			return $XSLTRunner->runShellCMD($this->Config->configSOLR['SAXONjar'], $this->Config->configSOLR['indexXSLT']);
		}




		function unindexFile($filename){
			$XSLTRunner = new \MHS\XSLT2Runner($this->Config);

			foreach($this->Config->configSOLR["XSLTparams"] as $param => $value){
				$XSLTRunner->addParameter($param, $value);
			}
			$XSLTRunner->addParameter("index", $this->Config->configSOLR["SOLRindex"]);

			$XSLTRunner->setFilename($filename);
			$XSLTRunner->setOutputFilename($filename, $this->Config->configSOLR['DESTdir']);

			$XSLTRunner->runShellCMD($this->Config->configSOLR['SAXONjar'], $this->Config->configSOLR['deleteXSLT']);
			return true;
		}



		function removeSolrDoc($filename){
			$deleteXMLQueryFile = $this->Config->configSOLR['DESTdir'] . $filename;
			$xml = "<delete><query>filename:" . $filename . "</query></delete>";
			file_put_contents($deleteXMLQueryFile, $xml);
		}

		

		/* file name is the name only of the file that  contains a SOLR xml command (<add> <commit>)
			in the folder where temp SOLR files are kept
		*/
		function callSolr($filename){

			$fullpath = $this->Config->configSOLR['DESTdir'] . $filename;
			$statusArray = \Solr\SOLR::postAddXMLFile($this->postURL, $fullpath);

			if(!$statusArray['success']) {
				$error = print_r($statusArray, true);
				error_log("Solr error during posting XML in indexxml.php: " . $error);
				return false;
			}

			$statusArray = \Solr\SOLR::postAddXML($this->postURL, '<commit/>');
			if(!$statusArray['success']) {
				$error = print_r($statusArray, true);
				error_log("Solr error during posting COMMIT in indexxml.php: " . $error);
				return false;
			} 

			return true;
		}



		function feedSolrXML($xml, $urlSuffix = ""){
			$url = $this->postURL . $urlSuffix;
			$statusArray = \Solr\SOLR::postAddXML($url, $xml);
			if(!$statusArray['success']) {
				$error = print_r($statusArray, true);
				error_log("Solr error during posting xml in feedSolrXML() in indexxml.php: " . $error);
				return false;
			} 

			$statusArray = \Solr\SOLR::postAddXML($this->postURL, '<commit/>');
			if(!$statusArray['success']) {
				$error = print_r($statusArray, true);
				error_log("Solr error during posting COMMIT in indexxml.php: " . $error);
				return false;
			} 

			return true;
		}


		/*
			something like:

			{"id": "jqadiaries-v24-1795-03-p001--entry18", "author": {"add": ["adams-john10"]}}
		*/
		function feedSolrJSON($json, $urlSuffix = ""){
			$url = $this->postURL . $urlSuffix;
			$statusArray = \Solr\SOLR::postJSON($url, $json);
			if(!$statusArray['success']) {
				$error = print_r($statusArray, true);
				error_log("Solr error during posting json in feedSolrJSON() in indexxml.php: " . $error);
				return false;
			} 

			$statusArray = \Solr\SOLR::postAddXML($this->postURL, '<commit/>');
			if(!$statusArray['success']) {
				$error = print_r($statusArray, true);
				error_log("Solr error during posting COMMIT in indexxml.php: " . $error);
				return false;
			} 

			return true;
		}

	}
