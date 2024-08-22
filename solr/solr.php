<?php

	namespace Solr;
	

	/*
		Talk to SOLR via CURL and receive the reply

	*/

	class SOLR {

		protected $url;
		protected $response;
		protected $responseArray;
		protected $params;

		protected $groupingField = "";

		function __construct($core, $scheme = "", $server_name = ""){
			if(empty($server_name)) $server_name = $_SERVER['SERVER_NAME'];
			if(empty($scheme)) $scheme = $_SERVER['REQUEST_SCHEME'];
			$this->url =  $scheme . "://" . $server_name . ":8983" .  "/solr/" . $core . "/select";
		}


		function groupBy($field, $count = 3){
			$this->groupingField = $field;
			$this->groupingCount = $count;
		}


		function responder($req){
			$req = json_decode($req, true);
			$this->respondToArray($req);
		}


		function respondToArray($req){
			if(!isset($req['fields'])) $this->fail("No fields defined in SOLR config.");

			$this->params = new SearchParameters();

			$query = $this->configHandler($req);
			if(!empty($this->groupingField)) $query->setGroupingField($this->groupingField);
			if(!empty($this->groupingCount)) $query->setGroupingCount($this->groupingCount);
			$query->setRows("configRows");
			$query->setStart("configStart");
			$query->build();
			$this->callSOLR($query);
			$this->ajaxResponse();
		}



		// function respondToArray($reqArray){
		// 	$this->params = new \Publications\SOLR\SearchParameters();
		// 	if(!isset($reqArray['fields'])) $this->fail("No fields defined in SOLR config.");

		// 	$query = $this->configHandler($reqArray);
		// 	$query->setRows("rows");
		// 	$query->setStart("start");
		// 	$query->build();
		// 	$this->callSOLR($query->getQueryString());
		// 	$this->ajaxResponse();
		// }


		/*
			parse a json config request and build our query and fields

			{
				fields: [
					{
						type: "text",
						param: "[GET param name]",
						phraseLike: [true|false],
						name: "[name of field in SOLR]",
						required: [true|false] ,
						//optional:
						highlight: '[anything]', //if this is here at all, it will get SOLR highlights for this field
						value: [string here for fixed value]
					},
					{
						type: "intDateRange",
						startParam: "[GET param name for start]",
						endParam: "[GET param name for end]",
						startName: "[name of field in SOLR]",
						endName: "[name of field in SOLR]",
						required: [true|false]
						//optional:
						startValue: [string here fixed starting date],
						endValue: [string here for fixed ending date],
						reject: [term/value; docs with the term are excluded from results]
					}
				],
				displayFields: ["name", "name", etc],
				facetFields: ["name", "name"],
				sortFields: [
					{
						name: "[solr field name]",
						sort: "[asc | desc]"
					},
					.. another field?
				]
			}

		*/
		function configHandler($config){
			$query = new Query();

			if(!isset($config['fields'])) $this->fail("no fields in config");

			//first setup expected params
			foreach($config['fields'] as $field){
				//skip if fixed value set
				if($field['type'] == "text" && !isset($field['value'])){
					$param = $this->params->add($field['param']);
					if(isset($field['phraseLike']) && $field['phraseLike']) $param->isPhraseLike();

				} else if($field['type'] == "intDateRange" && !isset($field['startValue'])){
					$this->params->add($field['startParam'], "00000000");
					$this->params->add($field['endParam'], "99999999");
				}
			}

			//parse URL
			$this->params->fromURLString();

			//run fields again and map params actual values to fields
			foreach($config['fields'] as $field){
				if($field['type'] == "text"){
					if(isset($field['value'])){
						$value = $field['value'];
					} else {
						$value = $this->params->get($field['param']);
					}
					$temp = $query->addSearchField($field['name']);

					if(isset($field['wholeFieldRequired']) && $field['wholeFieldRequired'] == false) $temp->wholeFieldRequired = false;

					if(isset($field['reject'])) $temp->addRejectTerms($value);
					else if(isset($field['required']) && $field['required']) $temp->addRequiredTerms($value);
					else $temp->addTerms($value);
					if(isset($field['highlight']) && $field['highlight']) $temp->highlight();

				} else if($field['type'] == "intDateRange"){
					if(isset($field['startValue'])){
						$startValue = $field['startValue'];
					} else {
						$startValue = $this->params->get($field['startParam']);
					}
					if(isset($field['endValue'])){
						$endValue = $field['endValue'];
					} else {
						$endValue = $this->params->get($field['endParam']);
					}

					//end of doc ("date_to") needs to be on or after range start
					$query->addIntegerDateRange($field['startName'], $startValue, "99999999");
					//beginning of doc ("date_when") needs to be before or on end of rnage
					$query->addIntegerDateRange($field['endName'], 00000000, $endValue);
				}
			}

			foreach($config['displayFields'] as $field){
				$query->addReturnField($field);
			}
			//this is needed to match highlighting
			$query->addReturnField("id");

			foreach($config['facetFields'] as $field){
				$query->addFacetField($field);
			}

			foreach($config['sortFields'] as $field){
				$query->setSortField($field['name'], $field['sort']);	
			}

			return $query;
		}



		function callSOLR($query){
			$queryString = $query->getQueryString();
			$url = $this->url . "?" . $queryString;

			//		$header[] = "Content-type: text/xml";
			//		$header[] = "charset=utf-8";
			// create a new curl resource
			$ch = curl_init();
		
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPGET, true);

			$this->response = curl_exec($ch);

			if(!$this->response) $this->fail("SOLR IS NOT RUNNING");

			$this->responseArray = json_decode($this->response, true);

			//add empties for all missing return fields
			// for grouped results
			if(isset($this->responseArray['grouped'])){
				foreach($this->responseArray["grouped"] as $groupName => $groups){
					foreach($groups['groups'] as $gindex => $group){
						foreach($group['doclist']['docs'] as $dindex => $doc){
							foreach($query->fieldList as $field){
								if(!isset($doc[$field])){
									$this->responseArray['grouped'][$groupName]['groups'][$gindex]['doclist']['docs'][$dindex][$field] = "";
								}
							}
						}
					}
				}
			}
			else foreach($this->responseArray['response']["docs"] as $key => $doc){
				foreach($query->fieldList as $field){
					if(!isset($doc[$field])) $this->responseArray['response']['docs'][$key][$field] = "";
				}
			}
			$this->response = json_encode($this->responseArray);

			// close curl resource, and free up system resources
			curl_close($ch);
		}





		function callSOLRWithQueryString($query){
			$url = $this->url . "?" . $queryString;

			$ch = curl_init();
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPGET, true);

			$this->response = curl_exec($ch);

			if(!$this->response) $this->fail("SOLR IS NOT RUNNING");

			$this->responseArray = json_decode($this->response, true);
			// close curl resource, and free up system resources
			curl_close($ch);

			return $this->response;
		}


		

		static function postAddXMLFile($url, $filePath){
			try {
				$body = file_get_contents($filePath);
				if(false === $body){
					throw new \Exception("File {$filePath} not readable");
				}
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS,  $body ); 
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type:application/xml']);
				$result = curl_exec($ch);

				if(curl_errno($ch)){
					throw new \Exception(curl_error($ch));
				}
			} catch(\Exception $e){
				print "<b>Failed</b>: \n";
				print_r($e->getMessage());
			}
			//Print out the response from the page
			return self::formatPostResult($result);
		}


		
		static function postAddXML($url, $xml){
			try {
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS,  $xml ); 
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type:application/xml']);
				$result = curl_exec($ch);

				if(curl_errno($ch)){
					throw new \Exception(curl_error($ch));
				}
			} catch(\Exception $e){
				print "<b>Failed</b>: \n";
				print_r($e->getMessage());
			}
			//Print out the response from the page
			return self::formatPostResult($result);
		}


		static function postJSON($url, $json){
			try {
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS,  $json ); 
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type:application/json']);
				$result = curl_exec($ch);
				if(curl_errno($ch)){
					throw new \Exception(curl_error($ch));
				}
			} catch(\Exception $e){
				print "<b>Failed</b>: \n";
				print_r($e->getMessage());
			}
			//Print out the response from the page
			return self::formatPostResult($result);
		}



		static function commitPost($url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			//Tell cURL to return the output as a string.
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '<commit/>');
			curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type:application/xml']);
			$result = curl_exec($ch);

			if(curl_errno($ch)){
				throw new \Exception(curl_error($ch));
			}

			//Print out the response from the page
			return self::formatPostResult($result);
		}


		static function formatPostResult($result){
			$result = trim($result);
			$result = preg_replace("/\s\s+/", " ", $result);
			$parts = explode(" ", $result);
			$success = (intval($parts[0]) == 0 ? true : false);
			$ms = isset($parts[1]) ? $parts[1] : "-1";

			return ["success" => $success, "duration" => $ms];
		}


		function fail($msg){
			$data["error"] = true;
			$data['message'] = $msg;
			header('Content-Type: application/json');
			print json_encode($data);
			die();
		}

		
		function ajaxResponse(){
			header('Content-Type: application/json');
			print $this->response;
		}
		
	}