<?php



	namespace MHS;
	
	
	
	
	class ShellCmd {
		

		/* set the full path to the executable, no arguments or options
		 */
		public function setCmd($cmd){
			$this->cmd = $cmd;
		}
		


		public function runCmd($fullCmd){
			$this->cmd = "";
			return $this->run($fullCmd);
		}
		

		public function run($stdin){	
		
			$descriptorspec = array(
				0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
				1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
				2 => array("pipe", "w") // stderr is a file to write to
			);
			 
			 $process = proc_open($this->cmd . $stdin, $descriptorspec, $pipes, dirname(__FILE__), null);
			 
			 if (is_resource($process)) {
				 // $pipes now looks like this:
				 // 0 => writeable handle connected to child stdin
				 // 1 => readable handle connected to child stdout
				 // Any error output will be appended to /tmp/error-output.txt
		 
				$this->stdout = stream_get_contents($pipes[1]);
				fclose($pipes[1]);

				$this->stderr = stream_get_contents($pipes[2]);
				fclose($pipes[2]);

				// It is important that you close any pipes before calling
				 // proc_close in order to avoid a deadlock
				 $return_value = proc_close($process);
				 
				 return $return_value;
				
			} else {
				return false;
			}
		}
		
		
		
		public function getStdout(){
			return $this->stdout;
		}
		
		
		
		public function getStderr(){
			return $this->stderr;
		}
	}
	