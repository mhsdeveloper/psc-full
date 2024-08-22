<?php

	namespace SubjectsManager\Models;


	class DataModel {

		const DATABASE = "psccore";
		const USER = MYSQL_USER;
		const PASSWORD = MYSQL_USER_PASSWORD;
		
		protected $DB;

		protected function __construct(){
			
			$this->DB = new \MHS\Needle(self::DATABASE, self::USER, self::PASSWORD);	
		
		}

		protected function runQuery($query, $arrParams){
			
			try{

				$this->DB->prepQuery($query);

				foreach ($arrParams as &$item) {

					if (strlen($item->value) > 0) $this->DB->setParam($item->variable, $item->value);
					$this->DB->runQuery();

				}

			} catch(\Exception $e){

				$this->errors["status"] = 400;
				$this->errors["message"] = $e->getMessage();
				
				if(! empty($errors)){

					\MHS\Sniff::print_r($errors);

				}

				return false;
			}

		}

	}