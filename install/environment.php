<?php


	namespace Publications;


	class Environment {

		static private $instance;
		
		//these are the allowed @type for divs that chunks as a document
		public static $DOCTYPES = ['entry', 'doc'];


		//this is the installation location of the backend prep and management
		const APP_INSTALL_DIR = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/";

		//URL above domain to backend prep and management
		const APP_INSTALL_URL = "/publications/" . \MHS\Env::PROJECT_SHORTNAME . "/";

		//this is the installation location of the public delivery of the diary
		const LIVE_INSTALL_DIR = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/";

		//URL above domain for the mainc public app
		const LIVE_INSTALL_URL = "/publications/" . \MHS\Env::PROJECT_SHORTNAME . "/";

		//URL prefix (followed by doc ID) for static URL to document view
		const DOC_VIEW_URL_PREFIX = "/publications/" . \MHS\Env::PROJECT_SHORTNAME . "/document/";

		//URL prefix (followed by name id) for static URL to view name page
		const NAME_VIEW_URL_PREFIX = "/publications/" . \MHS\Env::PROJECT_SHORTNAME . "/";


		//relative folder to the metadata configuration "sets" XML
		const SETS_DEFINITION_SUBFOLDER ="sets/";

		//relative folder to where templates are kept
		const TEMPLATE_SUBFOLDER = "templates/";

		//relative folder to logs
		const LOGFILE_SUBFOLDER = "logs/";

		//ingest XSLT for source files uploaded
		const DISPLAY_XSLT_FILE = SERVER_WWW_ROOT . "html/publications/lib/xsl/tei-fragment-v2.xsl";

		//ingest XSLT for source files uploaded
		const PROOF_XSLT_FILE = SERVER_WWW_ROOT . "html/publications/lib/xsl/tei-proofread.xsl";

		//full path to the XML source
		const SOURCE_FOLDER = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/xml/";

// //This is the storage path, full path, to where metadata is kept.
// const STORAGE_PATH = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/metadata/";

// //PATH to the metadata folder
// const META_PATH = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/xml/metadata/";

		const INCLUDES_PATH = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/includes/";

		const SUPPORT_FILES_PATH = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/support-files/";


		const PAGE_IMAGES_PATH = SERVER_WWW_ROOT . "html/projects/" . \MHS\Env::PROJECT_SHORTNAME . "/page-images/";


		const FILENAME_REGEX = "/[^a-zA-Z0-9_\-\.]/";
		const FILENAME_SEQ_REGEX = "/[^a-zA-Z0-9_\-\.|]/"; //same as filename regex plus the separator used for string-based lists 

		const FILE_UPLOAD_EXT_WHITELIST =  [".xml", ".XML"];

		//Views
		const ERROR_VIEW = "error.php";

		const LOAD_PREV_NEXT_DOC_CONTEXT = true;

		//this tells which segment in the URL holds the doc Link id. the first segment after the router
		// is 1, so for example in this URL: project/index.php/view/VOL01d234 the ID is segment 2
		const DOC_LINK_URL_SEGMENT = 2;

		const JAVA_BIN = "java";

		//full path to the SAXON jar that supports xslt2.0
		const SAXON_JAR = SERVER_WWW_ROOT . "html/publications/lib/saxon8.jar";

		//full path with file name to the Jing jar, that handles schema validation (since we couldn't get PHP to work)
		const JING_FULL_PATH = SERVER_WWW_ROOT . "html/publications/jing/bin/jing.jar";

		//having the index here let's us override it in the project-specific environment file
		const SOLR_INDEX =  \MHS\Env::PROJECT_SHORTNAME;

		const SOLR_CORE = "publications";

		const DOC_MANAGER_SOLR_CONFIG_FILE = SERVER_WWW_ROOT . "html/docmanager/js/solr-config.json";

		const METADATA_SOLR_CONFIG_FILE = SERVER_WWW_ROOT . "html/publications/template/configs/solr-metadata.json";

		const MAIN_SEARCH_SOLR_CONFIG = SERVER_WWW_ROOT . "html/publications/template/configs/solr-search.json";

		const CONTEXT_SEARCH_RANGE = 240; //month before and after a document date to find prev/next documents to show


		//some private consts that are accessed via methods
		//access this with ::getSchemaFilename()
		private const SCHEMA_FILE = SERVER_WWW_ROOT . "html/publications/pub/schema/primarysourcecoop_rev2.2.rng";

		private const PROOF_VIEW_FILE = "/publications/template/views/document-proof.php";

		private const DOCUMENT_VIEW = "document";

		

		protected function __construct(){
		    $this->constructContinued();
		}


		/* override this for a project to use a different schema 
			provide the full path and filename
		*/
		public static function getSchemaFilename(){
			return self::SCHEMA_FILE;
		}

		/* provide the fullpath and filename above HTML */
		public static function getProofView(){
			return self::PROOF_VIEW_FILE;
		}	

		/* override this for a project-specific SOLR json config 
			provide the full path and filename
		*/
		public static function getSolrConfig(){
			return self::DOC_MANAGER_SOLR_CONFIG_FILE;
		}


		/* override this for a project-specific SOLR json config 
			provide the full path and filename
		*/
		public static function getMetadataSolrConfig(){
			return self::METADATA_SOLR_CONFIG_FILE;
		}

		/* override this for a project-specific SOLR json config 
			provide the full path and filename
		*/
		public static function getSolrSearchConfig(){
			return self::MAIN_SEARCH_SOLR_CONFIG;
		}

		/* override to change the context search range in months */
		public static function getContextSearchRange(){
			return self::CONTEXT_SEARCH_RANGE;
		}

		/* provide the full path and filename */
		public static function getDisplayXSLT(){
			return self::DISPLAY_XSLT_FILE;
		}

		/* provide the full path and filename */
		public static function getProofXSLT(){
			return self::PROOF_XSLT_FILE;
		}


		/* when overriding, provide path above html and filename */
		public static function getDocumentView(){
			return self::DOCUMENT_VIEW;
		}


		/* there are props you can override when extending */
		protected function constructContinued(){

			$this->configSOLR = [
				"POST_URL" => ":8983/solr/publications/update",
				"SOLRindex" => \MHS\Env::SOLR_INDEX,
				"indexXSLT" => SERVER_WWW_ROOT . "html/publications/lib/xsl/psc-to-solr.xsl",
				"XSLTparams" => [
					"resource_group_name" => \MHS\Env::PROJECT_FULLNAME,
					"resource_uri_start" => \MHS\Env::DOC_VIEW_URL_PREFIX
					],
				"DESTdir" => PSC_PROJECTS_PATH . \MHS\Env::PROJECT_SHORTNAME . "/solrtemp/",
				"SAXONjar" => \MHS\Env::SAXON_JAR,
				"deleteXSLT" => SERVER_WWW_ROOT . "html/publications/lib/xsl/solr-delete-psc.xsl"
			];
		}


		static public function getInstance(){
			if(self::$instance == NULL){

				$calledClass = get_called_class();

				self::$instance = new $calledClass();
			}

			return self::$instance;
		}




		/**
		 * Glean the project name from the URL path
		 */
		static public function getProjectEnv(){

			//remove leading slash
			if($_SERVER['REQUEST_URI'][0] == "/") $request = substr($_SERVER['REQUEST_URI'], 1);
			else $request = $_SERVER['REQUEST_URI'];
			
			//uri segments
			$segments = explode("/", $request);

			//popoff "publications"
			array_shift($segments);

			$projectName = array_shift($segments);

			//make sure the project name is a real name ...
			$check = preg_replace("/[^a-zA-Z0-9]/", "", $projectName);

			// ... other wise it's a site-wide page
			if($check != $projectName){
				$projectName = "coop";
			}


			//load settings
			$envFile = PSC_PROJECTS_PATH . $projectName . "/environment.php";
			// if(false == $envFile){
			// 	session_start();
			// 	die("Check that the project " . $projectName . " folder has been setup and is readable.");
			// }

			return ["envFile" => $envFile, "projectName" => $projectName];
		}

	}
