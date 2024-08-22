<?php


	namespace Template\Controllers;


	class Context {

		//need to provide the index to limit results to just this set
		const SOLR_INDEX =  \MHS\Env::SOLR_INDEX;
		const SOLR_CORE = \MHS\Env::SOLR_CORE;


		function __construct(){
		}

		function index(){
			$this->monthRange = \MHS\Env::getContextSearchRange();

			$this->nearbyChronology();
		}


		function years(){
			$url = "http://" . SOLR_IP .":8983/solr/" . self::SOLR_CORE ."/select?facet.field=date_year&facet.mincount=1&facet=true&fl=id%2C%20index&indent=true&q.op=OR&q=%2Bindex%3A" . self::SOLR_INDEX . "&rows=1";

			$resp = $this->curl($url);

			if(!isset($resp["facet_counts"]) 
				|| !isset($resp["facet_counts"]["facet_fields"])
				|| !isset($resp["facet_counts"]["facet_fields"]['date_year'])){
					$this->fail("SOLR did not return any date facets.");
			}
			$facets = $this->collateFacets($resp["facet_counts"]["facet_fields"]['date_year']);
			ksort($facets);
			$this->respond($facets);
		}


		function months(){
			$year = preg_replace("/[^0-9]/", "", $_GET['year']);
			$url = "http://" . SOLR_IP .":8983/solr/" . self::SOLR_CORE ."/select?facet.field=date_month&facet.mincount=1&facet=true&fl=id%2C%20index&indent=true&q.op=OR&q=%2Bindex%3A" . self::SOLR_INDEX . "%20%2Bdate_year%3A" . $year . "&rows=1";

			$resp = $this->curl($url);

			if(!isset($resp["facet_counts"]) 
				|| !isset($resp["facet_counts"]["facet_fields"])
				|| !isset($resp["facet_counts"]["facet_fields"]['date_month'])){
					$this->fail("SOLR did not return any date facets.");
			}
			$facets = $this->collateFacets($resp["facet_counts"]["facet_fields"]['date_month']);
			ksort($facets);
			$this->respond($facets);
		}


		function monthDocs(){

			$year = preg_replace("/[^0-9]/", "", $_GET['year']);
			$month = preg_replace("/[^0-9]/", "", $_GET['month']);

			$url = "http://" . SOLR_IP .":8983/solr/" . self::SOLR_CORE ."/select?fl=id%2C%20index%2Ctitle%2Cdate_when%2Cdoc_beginning&indent=true&q.op=OR&q=%2Bindex%3A". self::SOLR_INDEX . "%20%2Bdate_year%3A" . $year . "%20%2Bdate_month%3A" . $month . "&rows=3000&sort=date_when%20asc,%20person_keyword%20desc";

			$resp = $this->curl($url);

			$this->respond($resp);
		}




		function findDocByDate(){
			$date = preg_replace("/[^0-9]/", "", $_GET['date']);
			$vol = preg_replace("/[^0-9]/", "", $_GET['volume']);

			$url = "http://" . SOLR_IP .":8983/solr/" . self::SOLR_CORE ."/select?fl=id%2C%20index%2Ctitle%2Cdate_when%2Cdoc_beginning&indent=true&q.op=OR&q=%2Bindex%3A" . self::SOLR_INDEX;
			$url .= "%20%2Bdate_when%3A" . $date;
			
			if(!empty($vol)){
				$url .= "%20%2Bvolume%3A" . $vol;			
			}
			
			$url .= "&rows=10&sort=date_when%20asc,%20person_keyword%20desc";

			$resp = $this->curl($url);

			$this->respond($resp);
		}




		function nearbyChronology(){
			$date = preg_replace("/[^0-9]/", "", $_GET['date']);

			$startDate = $this->subtractMonths($date, $this->monthRange);
			$endDate = $this->addMonths($date, $this->monthRange);

			//load some config
			$conf = [
				"fields" => [
					[
						"type" => "intDateRange",
						"startName" => "date_when",
						"endName" => "date_to",
						"startValue" => $startDate,
						"endValue" => $endDate,
					]
					,
					[
						"name" => "status",
						"type" => "text",
						"value" => "published",
						"required" => true
					]
				],
				"facetFields" => [],
				"displayFields" => ["id", "filename", "date_when", "title", "doc_beginning", "resource_uri", "volume", "list_order", "position"],
				"sortFields" => [
					["name" => "date_when",	"sort" => "asc" ]
				]
			];

			if(isset($_GET['project'])){
				$conf['fields'][] = [
					"name" => "index",
					"param" => "project",
					"type"=> "text",
					"required" => true
				];
			}


			$this->SOLR = new \Solr\SOLR(self::SOLR_CORE, "http", SOLR_IP);
			$this->SOLR->respondtoArray($conf);
		}
		


		function subtractMonths($date, $range){
			$year = substr($date, 0, 4);
			$month = substr($date, 4, 2);
			$day = substr($date, 6, 2);

			$yearSpan = floor($range / 12);
			$monthSpan = $range % 12;

			$startYear = intval($year) - $yearSpan;
			$startMonth = intval($month) - $monthSpan;
			if($startMonth < 0){
				$startYear -= 1;
				$startMonth += 12;
			}
			$startMonth .= ""; //to a string
			if(strlen($startMonth) == 1) $startMonth = "0" . $startMonth;

			return  $startYear . $startMonth . $day;
		}



		function addMonths($date, $range){
			$year = substr($date, 0, 4);
			$month = substr($date, 4, 2);
			$day = substr($date, 6, 2);

			$yearSpan = floor($range / 12);
			$monthSpan = $range % 12;

			$startYear = intval($year) + $yearSpan;
			$startMonth = intval($month) + $monthSpan;
			if($startMonth > 12){
				$startYear += 1;
				$startMonth -= 12;
			}
			$startMonth .= ""; //to a string
			if(strlen($startMonth) == 1) $startMonth = "0" . $startMonth;

			return  $startYear . $startMonth . $day;
		}


		function curl($url){
			$ch = curl_init();
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPGET, true);

			$response = curl_exec($ch);
			if(!$response) return $this->fail("SOLR IS NOT RUNNING");
			return json_decode($response, true);
		}

		function collateFacets($facets){
			$out = [];
			$len = count($facets);
			for($i=0; $i<$len; $i += 2){
				$out[$facets[$i]] = $facets[$i + 1];
			}

			return $out;
		}


		public function respond($response){
			$response = json_encode($response);
			header('Content-Type: application/json');
			header("Access-Control-Allow-Origin: *");
			header('Access-Control-Allow-Credentials: true');
			print $response;
			die();
		}


	} //class
