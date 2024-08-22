<?php

	namespace Solr;
	

	/* we accept the q= part of the actual SOLR query directly from the client
		but we interpret no of rows so that server-side we can limit this.
		also, we save the more detailed highlighting params for here, since
		those can also affect performance.

		So sent the SOLR q= as query= with the "q=..." stuff all urlencoded:
		  e.g. query=q%3D%2B{field%3Avalue....


		send other SOLR params just as in SOLR, except facets; solr allows repeat
		"facet.field=..." but of course PHP will set $_GET['facet_field'] and only get the last one.
		so send as &ff=facet;facet2;facet3


		*/


	class SolrDirect {

		static function parseQ($q = ""){

			//fix empty queries
			$q = trim(preg_replace("/\s\s+/", " ", $q));
			if(empty($q)) $q = "id:*";

			//replace | with &
			$q = str_replace("|", "&", $q);
		
			//remove SOLR style rows in q
			$q = preg_replace("/rows=[0-9]+/", "", $q);
			$q = preg_replace("/start=[0-9]+/", "", $q);

			return $q;
		}

		/* this is separate from ParseQ() so that controllers can add intervening steps,
			maybe new fields to the q=, etc.

		NOTE this swaps SOLRs query syntax () for {}. we receive {} so that actual query terms can
		contain (), such as for topic names: "Family Relations (Adams)"

			*/
		static function cleanupQ($q, $swapCurly = true){
			//cleanup any double &&
			$q = str_replace(["&"], [""], $q);
			if($swapCurly) $q = str_replace(["{", "}"], ["(", ")"], $q);
			$q = substr($q, 2); //remove q=
			$q = rawurlencode($q);

			//add back q=
			return "q=".$q;
		}


		static function simpleEncode($in){
			return str_replace(" ", "%20", $in);
		}



		//add other query params
		// notice that PHP must remap xxx.yyy GET params to xxx_yyy for some reason
		// see print_r($_GET);

		static function addOtherSolrGetParams($queryString, $maxRowCount = 100,  $hlMaxAnalyzedChars = "50000", $hlFragSize = "100"){
			if(isset($_GET['rows'])){
				$rowCount = intval($_GET['rows']);
				if($rowCount > $maxRowCount) $rowCount = $maxRowCount;
			}
			$queryString .= "&rows=" . $rowCount;

			if(isset($_GET['start'])){
				$start = intval($_GET['start']);
			}
			$queryString .= "&start=" . $start;

			if(isset($_GET['group'])) {
				$queryString .= "&group=true";

				if(isset($_GET['group_limit'])){
					$queryString .= "&group.limit=" . intval($_GET['group_limit']);
				}

				if(isset($_GET['group_field'])){
					$field = preg_replace("/[^a-zA-Z0-9\-\_]/", "", $_GET['group_field']);
					$queryString .= "&group.field=" . $field;
				}
			}

			if(isset($_GET['hl'])){
				$field = preg_replace("/[^a-zA-Z0-9\-\_]/", "", $_GET['hl_fl']);
				$queryString .= "&hl=true&hl.fl=" . $field;
				$queryString .= "&hl.maxAnalyzedChars=" . $hlMaxAnalyzedChars;
				$queryString .= "&hl.fragsize=" . $hlFragSize;
				$queryString .= "&hl.requireFieldMatch=true";
			}

			if(isset($_GET['sort'])){
				$param = preg_replace("/[^a-zA-Z0-9\-\_ ]/", "", $_GET['sort']);
				$queryString .= "&sort=" . self::simpleEncode($param);
			}

			if(isset($_GET['ff'])){
				$queryString .= "&facet=true&facet.mincount=1";
				$facets = explode(";", $_GET['ff']);
				foreach($facets as $facet) $queryString .= "&facet.field=" . $facet;
			}

			if(isset($_GET['fl'])){
				$field = preg_replace("/[^a-zA-Z0-9\-\_ ]/", "", $_GET['fl']);
				$queryString .= "&fl=" . self::simpleEncode($field);
			}

			return $queryString;
		}



		static function buildURL($core, $server_name = "", $port = "8983"){
			if(empty($server_name)) $server_name = $_SERVER['SERVER_NAME'];
			return "http://" . $server_name . ":" . $port .  "/solr/" . $core . "/select";
		}



		static function call($url){

			$ch = curl_init();
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_setopt($ch, CURLOPT_HTTPGET, true);

			$response = curl_exec($ch);

			return $response;
		}
	}


	