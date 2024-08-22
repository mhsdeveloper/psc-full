<?php

	namespace Publications;


	trait TeiXslt {

		protected $xslt;

		public function getXslt(){ return $this->xslt;}

		public function loadXSLT($fullpath){
			$this->xslt = new \DOMDocument;

			if($this->xslt->load($fullpath)) return true;
			return false;
		}


		/* transform()
		 *
		 * $xsltfile	:	string:	fullpath to xslt transform
		 *
		 * $params		:	array: key is parameter name, value is value is value ...
		 *
		 *	returns true on success, false on any number of things that could go wrong!
		 */
		public function transformFragment($params = false){
			$this->transformer = new \XSLTProcessor;
			$this->transformer->importStyleSheet($this->xslt);

			if(is_array($params)){
				foreach($params as $param => $value) $this->transformer->setParameter('', $param, $value);
			}

			//if(!isset($this->fragment)) $this->output = $this->transformer->transformToXML($this->xmlsource);
			$this->output = $this->transformer->transformToXML($this->fragment);
//error handling needed
			if($this->output === false) return false;
			else return true;
		}




		/* version of above that takes the doc as input, so we can transform any doc via our object's xslt
		 *
		 * returns the transformed code as string
		 */

		public function transformAny($domdoc, $params = false){
			$this->transformer = new \XSLTProcessor;
			$this->transformer->importStyleSheet($this->xslt);
			if(is_array($params)){
				foreach($params as $param => $value) $this->transformer->setParameter('', $param, $value);
			}

			$output = $this->transformer->transformToXML($domdoc);

			if($output === false) return false;
			else return $output;
		}


		//Wrapper shortcut to transform the entire DOMDoc, that is, the file, not an fragment or other externail file
		public function transformDoc($params = false){
			return $this->transformAny($this->fullDoc, $params);
		}



		/* getXSLToutput()
		 *
		 * returns $this->output, from the XSLT transform
		 */
		public function getXSLToutput(){
			return $this->output;
		}



	}