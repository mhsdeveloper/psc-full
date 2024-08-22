<?php


	namespace DocManager;


	class Hooks {

		static private $instance = NULL;

		public $preUploadHooks = [];
		public $postUploadHooks = [];
		public $filenameChangesHooks = [];
		public $publishHooks = [];
		public $unPublishHooks = [];
		public $solrIndexHooks = [];

		static public function getInstance(){
			if (self::$instance == null){
				self::$instance = new Hooks();
			}
			return self::$instance;
		}
			

		function add($type, $class, $method){

			$hook = [
				"class" => $class,
				"method" => $method
			];

			switch($type){

				//disabled; you really need to fully upload in order to be able to do anything, so this doesn't quite make sense
				// case "preupload":
				// case "preUpload":
				// 	$this->preUploadHooks[] = $hook; break;

				case "postupload":
				case "postUpload":
					$this->postUploadHooks[] = $hook; break;

				case "filenameChange":
				case "filenamechange":
				case "fileNameChange":
					$this->filenameChangesHooks[] = $hook; break;


				/* ABOUT PUBLISH HOOKS

					if any publish hook returns false, the main app will not publish the doc in the documents table

				*/
				case "publish":
				case "published":
					$this->publishHooks[] = $hook; break;

				/* ABOUT UNPUBLISH HOOKS

					if any unpublish hook returns false, the main app will not unpublish the doc in the documents table

				*/
				case "unpublish":
				case "unpublished":
				case "unPublish":
				case "unPublished":
					$this->unPublishHooks[] = $hook; break;
		

				case "solrIndex":
					$this->solrIndexHooks[] = $hook; break;
			}
		}
	}