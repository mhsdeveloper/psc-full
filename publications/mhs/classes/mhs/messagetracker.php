<?php

	/* singleton class for tracking errors and messages centrally across
	 * all objects for a single service request/response.
	 */


	/* must pass a config array with these members:
	 *
	 * "debugging_mode_on" true or false: true, errors will print to output
	 * "app_install_dir" is the full path to the routing index.php
	 * "logfile_subfolder" the the sub folder where logs should be saved.
	 */

	 
	 

	namespace MHS;
	
	
	
	
	class MessageTracker {
		
		
		private $errors = [];
		
		private $userErrors = [];
		
		private $messages = [];
		
		
		
		
		function __construct($config){
			$this->config = $config;
		}
		
		
		
		function message($message){
			$this->messages[] = $message;
		}
		
		
		function getMessages(){
			return $this->messages;
		}
		
		
		
		function userError($message){
			$this->userErrors[] = $message;
		}
		
		
		function getUserErrors(){
			return $this->userErrors;
		}
		
		function purgeUserErrors(){
			$this->userErrors = [];
		}
		
	
		function error($exception){
		
			//build message
			$message = $exception->getMessage();
			$code = $exception->getCode();
			$file = $exception->getFile();
			$line = $exception->getLine();

			$this->errorFromParts($message, $code, $file, $line);
		}



		function errorFromParts($message, $code, $file, $line){
			//add to tracking
			$this->errors[] = [
				"message" => $message,
				"file" => $file,
				"line" => $line,
				"code" => $code
			];
		
		
			//print to output or log
			if($this->config['debugging_mode_on']) {
				print "\n<br/><b>{$message}</b> in file {$file} on line: {$line}<br/>\n";
				print "Code:<br/>\n";
				print "<pre>{$code}</pre>\n";
			}
			
			else {
				$logstr = date("Y-m-d G:i:s") . "-- {$message} in file {$file} on line: {$line}\n\n";

				error_log($logstr);
				
				// $logfilename = $this->config['app_install_dir'] . $this->config['logfile_subfolder'] . "errors.log";
				
				// //cleanup if too big
				// if(is_readable($logfilename)) {
					
				// 	$stats = stat($logfilename);
					
				// 	//set aside if too big
				// 	if($stats['size'] > 500000){
				// 		$newfilename = $this->config['app_install_dir'] . $this->config['logfile_subfolder'] . "errors-" . date("Y-m-d") . ".log";
				// 		@rename($logfilename, $newfilename);
				// 	}
				// }
				
				// @file_put_contents($logfilename, $logstr , FILE_APPEND);
			}
		}
	
	
		function getErrors(){
			return $this->errors;
		}
		
		function getErrorsAsString(){
			return implode("<br/>\n", $this->userErrors);
		}
	}
