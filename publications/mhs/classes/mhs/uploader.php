<?php

	namespace MHS;



	/* USAGE

		intended for uploading one file at a time only, expecting that AJAX will be used
		to query the server and this class multiple times for a set of files.
	 *
	 * extend this class with new methods:
	 *
	 * 		if there's a $this->processFile()
	 * 			on upload each file's full destination path will be passed to the function.
	 * 			processFiles should return true on success, or false on failure;
	 *
	 */




	class Uploader {

		protected $destFolder = "";

		protected $rerouteURL = "";

		protected $inputName = "fileupload";

		protected $uploadedFilename = "";

		protected $error = "";

		protected $activeFile = false;

		public $whiteList = [".jpg", ".gif", ".png", ".pdf", ".xml", ".rng"];


		public function setInputName($name){
			$this->inputName = $name;
		}


		public function setDestFolder($folder){
			$this->destFolder = $folder;
		}


		public function getFilename() {
			return $this->uploadedFilename;
		}

		public function getFullFilePath() {
			return $this->destFolder . $this->uploadedFilename;
		}


		public function addAllowedFileType($extension){
			$ext = strtolower($extension);
			//make sure we have a leading dot
			if($ext[0] != ".") $ext = "." . $ext;

			$this->whiteList[] = $ext;
		}

		public function removeAllowedFileType($extension){
			$ext = strtolower($extension);
			//make sure we have a leading dot
			if($ext[0] != ".") $ext = "." . $ext;
			if(!in_array($ext, $this->whiteList)) return;

			$i = array_search($ext, $this->whiteList);
			array_splice($this->whiteList, $i, 1);
		}


		public function findFileFromRequest(){
			// form submit
			if(isset($_FILES[$this->inputName])){
				$this->activeFile = $_FILES[$this->inputName];
			} else {
				$this->activeFile = array_pop($_FILES);
			}
		}


		function upload($move = true){
			//check for correct usage
			if(!is_readable($this->destFolder)) {
				$this->setError("The destination folder is not set.");
				return false;
			}

			if(false === $this->activeFile) $this->findFileFromRequest();

			// else 
			// 	$this->setError("form field \"" . $this->inputName . "\" not set");
			// 	return false;
			// }

			$pathparts = pathinfo($this->activeFile['name']);
			$ext = "." . strtolower($pathparts['extension']);

			if(!in_array($ext, $this->whiteList)){
				$exts = implode(" ", $this->whiteList);
				$this->setError("Sorry, that type of file is not allowed. Please upload a file with one of these extensions: " . $exts);
				return false;
			}


			if ($this->activeFile['error'] == UPLOAD_ERR_OK) {
				$fn = $this->activeFile['name'];

				if($move) {
					if($this->moveToDest() === false) return false;
				}
			}

			else {
				switch($this->activeFile['error']){
					case 1: $msg = "File is larger that allowed on the server side."; break;
					case 2: $msg = "File larger that the form's UPLOAD_MAX_FILESIZE specifies."; break;
					case 3: $msg = "File was only partially uploaded"; break;
					case 4: $msg = "No file was uploaded. hm...."; break;
				}

				$this->setError($msg);
				return false;
			}
			$this->uploadedFilename = $fn;

			if($move && method_exists($this, "processFile")){
				$success = $this->processFile($this->destFolder . $fn);
				return $success;
			}
			else {
				return true;
			}
		}

		function moveToDest(){
			if(false === move_uploaded_file($this->activeFile['tmp_name'], $this->destFolder . $this->activeFile['name'])) {
				$this->setError("Sorry, the file was received but move_uploaded_file() returned an error.");
				return false;
			}

			return true;
		}


		protected function setError($msg){
			$this->error = $msg;
		}


		public function getError(){
			return $this->error;
		}
	}
