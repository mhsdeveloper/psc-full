<?php


	namespace DocManager\Controllers;


	class Zip {


		function index(){
			$path = \MHS\Env::SOURCE_FOLDER;
			chdir($path);

			$this->deleteZips();

			$outfile = \MHS\Env::PROJECT_SHORTNAME . "-selected-xml-files.zip";

			//when just a specific list of files
			if(isset($_GET['files'])){
				$files = explode(";", $_GET['files']);

				self::compressFiles($files, $outfile);
				self::download($outfile);
				return;
			}

			//get all files
			$outfile = \MHS\Env::PROJECT_SHORTNAME . "-ALL-xml-files.zip";
			$files = scandir($path);
			self::compressFiles($files, $outfile);
			self::download($outfile);
		}


		function deleteZips(){
			$dels = glob("*.zip");
			foreach($dels as $file){
				unlink($file);
			}
		}



		static function compressFiles($files, $zipname){
			$zip = new \ZipArchive;
			$zip->open($zipname, \ZipArchive::CREATE);
			foreach ($files as $file) {
				if($file == ".." || $file == ".") continue;
				if(!stripos($file, ".xml")) continue;
			  $zip->addFile($file);
			}
			$zip->close();

			return $zip;
		}



		static function download($zipname){
			///Then download the zipped file.
			header('Content-Type: application/zip');
			header('Content-disposition: attachment; filename='.$zipname);
			header('Content-Length: ' . filesize($zipname));
			readfile($zipname);
		}

	}
















/* $zipArchive = new ZipArchive();
$zipFile = "./example-zip-file.zip";
if ($zipArchive->open($zipFile, ZipArchive::CREATE) !== TRUE) {
    exit("Unable to open file.");
}
$folder = 'example-folder/';
createZip($zipArchive, $folder);
$zipArchive->close();

*/