<?php



	namespace MHS;

	class Env extends \Publications\Environment {

		
		const VERSION = "0.2b";
		const PROJECT_SHORTNAME = "wetvac";
		const PROJECT_FULLNAME = "WETVAC";
		const PROJECT_HTML_TITLE = "WETVAC";
		const CSS_FILENAME = "nix";
		const GA_ACCOUNT = "nix";
		const GA_DOMAIN = "www.masshist.org";


		const DEBUGGING_MODE_ON = false;

		//this is the installation location of the backend prep and management
		const APP_INSTALL_DIR = SERVER_WWW_ROOT . "html/tools/wetvac/";

		//URL above domain to backend prep and management
		const APP_INSTALL_URL = "/tools/wetvac/";


		//relative URL for uploading files for converting from Word to TEI
		const CONVERT_UPLOAD_URL = "index.php/convert/upload";

		//relative URL to process docx
		const CONVERT_PROCESS_URL = "index.php/convert/process";

		// full path to upload dir
		const CONVERT_UPLOAD_DIR = SERVER_WWW_ROOT . "html/tools/wetvac/uploads/";

		//relative URL for the ajax call to post a file to upload
		const SOURCE_UPLOAD_URL = "index.php/upload-source";



		//XSLT to convert Word's interior document.xml to TEI
		const WORD_TO_TEI_XSLT = SERVER_WWW_ROOT . "html" . self::APP_INSTALL_URL ."xsl/word-document-to-tei.xsl";

		//XSLT for OX->TEI processing post OX pre WET
		const PRE_WET_XSLT = SERVER_WWW_ROOT . "html". self::APP_INSTALL_URL . "xsl/pre_wet.xsl";

		//for after WET
		const POST_WET_XSLT = SERVER_WWW_ROOT  . "html". self::APP_INSTALL_URL . "xsl/post_wet.xsl";





		protected function __construct(){


			/* remap the groupID for names to new sortKey and displayString
			 * the key should match and existing key, who's data get's replaced.
			 * In the data, the sortKey should use "-" in place of spaces, and "[+]" in place of dashes
			 * The sortKey will become the output array's key, but the key will become groupID to still
			 * match the TEI source
			*/
			$this->remapNames = array(
			);

			/* names to add to create "See [name]", for maiden names etc. This will automatically be
			 * added, and will not create live links
			 * this array simple gets merged with existing names/groups array before processing/sorting.
			 * foreach array member, the key is the sortKey (the groupID, such as Smith,-Abigail) and
			 * the value is the display such as "Smith, Abigail: see Adams, Abigail"

				e.g.: "Smith,-Abigail" => "Smith, Abigail: see Adams, Abigail"

			 */
			$this->seeNames = array(
			);



			$this->constructContinued();
		}


	}
