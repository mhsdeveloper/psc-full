<?php
	/*


	*/


	namespace Template\Models;


	class Feedback extends DataModel {

		function feedme($note, $url){

			//basic sanity? really necessary if using prepared statements?
			$note = preg_replace("/[\(\)\{\}\;\:]/", " ", $note);
			$url = preg_replace("/[\(\)\{\}\;\:]/", " ", $url);

			try {
				$query = "INSERT INTO feedback set url = :url, note = :note, created_on = :create_on";

				$DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
				$DB->setParam(":note", $note);
				$DB->setParam(":url", $url);
				$DB->setParam(":created_on", date("Y-m-d H:i:s"));
				$DB->prepQuery($query);
				$DB->runQuery();

				return true;

			} catch(\Exception $e){
				$errors[] = $e->getMessage();
				if(count($errors)){
					\MHS\Sniff::print_r($errors);
				}
				return false;
			}
		}
		
	}