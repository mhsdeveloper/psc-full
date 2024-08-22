<?php


	namespace Publications;


	class Environment {

		static private $instance;
		
		//these are the allowed @type for divs that chunks as a document
		public static $DOCTYPES = ['entry', 'doc'];


		//this is the installation location of the backend prep and management
		const APP_INSTALL_DIR = SERVER_WWW_ROOT . "html/pXXX/XX" . \MHS\Env::PROJECT_SHORTNAME . "/";

		//URL above domain to backend prep and management
		const APP_INSTALL_URL = "/publications/" . \MHS\Env::PROJECT_SHORTNAME . "/";

		//this is the installation location of the public delivery of the diary
		const LIVE_INSTALL_DIR = SERVER_WWW_ROOT . "htmlXXX" . \MHS\Env::PROJECT_SHORTNAME . "/";

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
	 	const XSLT_FILE = SERVER_WWW_ROOT . "html/publications/lib/xsl/prep/psc-delivery-prep.xsl";

		//full path to the XML source
		const SOURCE_FOLDER = SERVER_WWW_ROOT . "hXXX" . \MHS\Env::PROJECT_SHORTNAME . "/xml/";

		//This is the storage path, full path, to where metadata is kept.
		const STORAGE_PATH = SERVER_WWW_ROOT . "html/puXX . \MHS\Env::PROJECT_SHORTNAME . "/metadata/";

		//PATH to the metadata folder
		const META_PATH = SERVER_WWW_ROOT . "html/puXXcts/" . \MHS\Env::PROJECT_SHORTNAME . "/xml/metadata/";

		const INCLUDES_PATH = SERVER_WWW_ROOT . "html/pubXXXects/" . \MHS\Env::PROJECT_SHORTNAME . "/includes/";

		const PAGE_IMAGES_PATH = SERVER_WWW_ROOT . "html/pubXXXXXs/" . \MHS\Env::PROJECT_SHORTNAME . "/page-images/";


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
		const JING_FULL_PATH = SERVER_WWW_ROOT . "jing/bin/jing.jar";

		//having the index here let's us override it in the project-specific environment file
		const SOLR_INDEX =  \MHS\Env::PROJECT_SHORTNAME;

		const DOC_MANAGER_SOLR_CONFIG_FILE = SERVER_WWW_ROOT . "html/docmanager/js/solr-config.json";



		//some private consts that are accessed via methods
		//access this with ::getSchemaFilename()
		private const SCHEMA_FILE = SERVER_WWW_ROOT . "html/publications/pub/schema/primarysourcecoop_rev2.1.rng";

		private const PROOF_VIEW_FILE = "/publications/template/views/document-proof.php";



		protected function __construct(){
		    $this->constructContinued();
		}


		/* override this for a project to use a different schema */
		public static function getSchemaFilename(){
			return self::SCHEMA_FILE;
		}

		public static function getProofView(){
			return self::PROOF_VIEW_FILE;
		}	

		/* override this for a project-specific SOLR json config */
		public static function getSolrConfig(){
			return self::DOC_MANAGER_SOLR_CONFIG_FILE;
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
				"DESTdir" => SERVER_WWW_ROOT . "html/XXts/". \MHS\Env::PROJECT_SHORTNAME . "/solrtemp/",
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

	}
