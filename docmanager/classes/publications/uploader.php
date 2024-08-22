<?php



	namespace Publications;



	class Uploader extends \MHS\Uploader {

		public function processFile($fullpath){
			$xml = file_get_contents($fullpath);
			if(false === $xml) {
				$this->setError("Error: unable to read file " . $fullpath);
				return false;
			}

			$xml = preg_replace("/<\?xml\-model.*\?>/sU", "", $xml);
			$success = file_put_contents($fullpath, $xml);
			if(false === $success) {
				$this->setError("Error saving processed XML to $fullpath");
			}

			return $success;
		}
	}
