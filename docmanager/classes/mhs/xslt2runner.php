<?php

	namespace MHS;


	class XSLT2Runner {

		private $parameters = [];

		/* for the Config, it expects to get:
		 *
		 * 		- an instance of \MHS\MessageTracker as Config->Messenger
		 * 		- ->source_folder, the full path with trailing slash where the input XML files lives
		 *		- $this->Config->java_bin
		 *		- $this->Config->saxon_jar
		 *		- $this->Config->xslt_file
		 */
		function __construct($Config){
			$this->Config = $Config;
		}



		public function addParameter($parameterName, $value){
			//prep value
			$value = str_replace('"', "“", $value);

			$this->parameters[$parameterName] = $value;
		}



		/* use this function to set one filename for in and output
		 */
		public function setFilename($filename){
			$filename = \MHS\Env::SOURCE_FOLDER . $filename;

			if(!is_readable($filename)){
				error_log("Unable to read i/o xml file for xslt: " . $filename);

				$this->inputFilename = $this->outputFilename = "";
				return false;
			}

			$this->inputFilename = $filename;
			$this->outputFilename = $filename;

			return true;
		}

		/* this overrides using same filename for in and output
		 */
		public function setOutputFilename($filename, $dirpath = false){
			if($dirpath == false) $dirpath = \MHS\Env::SOURCE_FOLDER;

			$this->outputFilename = $dirpath . $filename;
		}



		public function runShellCMD($saxon_jar = false, $xslt_file = false){
			//prep params
			$params = [];
			foreach($this->parameters as $parameter => $value){
				$params[] = $parameter . '="' . $value . '"';
			}
			$params = implode(" ", $params);

			if($saxon_jar == false) $saxon_jar = \MHS\Env::SAXON_JAR;
			if($xslt_file == false) $xslt_file = \MHS\Env::XSLT_FILE;

			$cmd = \MHS\Env::JAVA_BIN ;// . " -jar " . $this->Config->saxon_jar . " " . $this->filename . " " . $this->Config->xslt_file . " > " . $tempfile . " " . $params . " 2>&1";
			$stdin = " -jar " . $saxon_jar . " " . $this->inputFilename . " " . $xslt_file . " " . $params;

			$Shell = new \MHS\ShellCmd();
			$Shell->setCmd($cmd);
			$result = $Shell->run($stdin);
			$stdout = $Shell->getStdout();
			$stderr = $Shell->getStderr();



			if(!empty($stderr)) {
				error_log("XSLT error: " . $stderr);
				return false;
			}

			if(false === file_put_contents($this->outputFilename, $stdout)) {
				error_log("Unable to save the XML as file: " . $this->outputFilename);
				return false;
			}

			return true;
		}
	}
