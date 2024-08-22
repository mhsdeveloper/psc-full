<?php

/* 
 */

	namespace Wetvac\Models;



	
	class DocxUnpacker {


		public function unzip($filename){
			$zip = zip_open($filename);

	        if (!$zip || is_numeric($zip)) return false;

			$content = '';

	        while ($zip_entry = zip_read($zip)) {

	            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

	            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

	            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

	            zip_entry_close($zip_entry);
	        }// end while

	        zip_close($zip);

	        return $content;		
		}
		
	}
