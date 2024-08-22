<?php



	namespace SupportFiles\Models;




	class files {

		private $errors = [];

		public $uploadedFilename = "";

		//only these files are allowed to be seen or changed/deleted
		private $whiteList = [
			"timeperiods.xml",
			"footer.html"
		];

		function setPath($path){
			$this->fullPath = $path;

			if(!is_readable($this->fullPath)){
				if(!mkdir($this->fullPath)){
					$this->logError("Unable to create dir " . $this->fullPath);
					return false;
				}
			}

			return true;
		}


		function dir(){
			$files = scandir($this->fullPath);
			$list = [];
			foreach($files as $file){
				if($file == "." || $file == "..") continue;
				if(!in_array($file, $this->whiteList)) continue;

				$list[] = ["name" => $file];
			}

			return $list;
		}


		function getFileContent($file){

			$text = file_get_contents($this->fullPath . $file);

			if(false === $text){
				$this->logError("file_get_contents couldn't get the file " . $file);
				return false;
			}

			return $text;
		}



		function saveFileContent($filename, $text){
			if(!in_array($filename, $this->whiteList)){
				$this->logError("Not allowed to edit/save " . $file);
				return false;
			}
			if(!file_put_contents($this->fullPath . $filename, $text)){
				$this->logError("Error saving file: " . $file);
				return false;
			}

			return true;
		}



		function deleteFile($file){
			if(!in_array($file, $this->whiteList)){
				$this->logError("Can't delete $file; not in whiltelist.");
				return false;
			}

			if(!unlink($this->fullPath . $file)){
				$this->logError("Unable to delete $file.");
				return false;
			}
			return true;
		}



		function physicallyUploadFile(){
			$this->uploader = new Uploader();
			$this->uploader->setDestFolder($this->fullPath);

			$this->uploader->findFileFromRequest();

			$filename = $this->uploader->getFilename();

			if(!in_array($filename, $this->whiteList)){
				$this->logError("That file is not allowed to be uploaded.");
				return false;
			}

			
			//get file from the request stream
			//check return from upload
			if(false === $this->uploader->upload()) {
				$this->logError("Unable to upload the file: \n" . $this->uploader->getError());
				return false;
			}
			$this->uploadedFilename = $filename;
			
			return true;
		}


		public function getErrors(){
			return $this->errors;
		}


		private function logError($msg){
			$this->errors[] = $msg;
		}
	}

