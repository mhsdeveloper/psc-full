<?php




    namespace Publications;
    
    
    
    class Filenames {
        
        public $pattern = "/[^a-zA-Z0-9\-]/";
        
        private $errors = [];
        
        function check($filename){
            
            //remove the extension;
            $filenameNoExt = pathinfo($filename)['filename'];
            
            $hasErrors = false;
            
            //must start with letter
            $matches = [];
            preg_match("/[a-zA-Z]/", $filenameNoExt[0], $matches);
            if(count($matches) === 0) {
                $this->errors[] = "Filenames must start with a letter.";
                $hasErrors = true;
            }
            
            //remove all but allowed characters and compare
            $new = preg_replace($this->pattern, "", $filenameNoExt);
            
            if($new !== $filenameNoExt) {
                $this->errors[] = "Filenames may contain only letters, numbers, and dash.";
                $hasErrors = true;
            }
            
            if($hasErrors) return false;
            
            return true;
        }
        
        
        function getErrors(){
            return $this->errors;
        }
        
    }