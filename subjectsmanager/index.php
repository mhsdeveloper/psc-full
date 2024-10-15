	<?php
	/*
		This is the master router index for our app

	*/
	use SubjectsManager\Helpers\CVStoDB;

	session_start();	

	//use our autoloader
	require "autoloader.php";
	require_once("mhs_global_env.php");

	require_once(SERVER_WWW_ROOT . "environment.php");

	ini_set('error_log', "errors.txt");
	define("SNIFF_OUT_FILE", "sniff_out.txt");

	$classLoader = new SplClassLoader('MHS', SERVER_WWW_ROOT . 'html/publications/mhs/classes');
	$classLoader->register();

	$pubsLoader = new SplClassLoader('Publications', SERVER_WWW_ROOT . 'html/publications/lib/classes');
	$pubsLoader->register();

	$appLoader = new SplClassLoader('SubjectsManager', SERVER_WWW_ROOT . 'html/subjectsmanager/classes');
	$appLoader->register();



	require "mhsmvc2.php";
	$mvc = new MHSmvc();
	
	// $mvc->route("/api", "\SubjectsManager\Controllers\subjectsmanagercontroller@handleRequests");

	//public api routes first
//	$mvc->get("/ext/gettopics", "\SubjectsManager\Controllers\ProjectTopicDataController@getTopics");
	$mvc->get("/ext/findtopic", "\SubjectsManager\Controllers\SearchController");
	$mvc->get("/ext/getcompletetopic", "\SubjectsManager\Controllers\SearchController@getCompleteTopic");
	$mvc->get("/ext/search", "\SubjectsManager\Controllers\SearchController@fullSearch");
	$mvc->get("/ext/getallinsolr", "\SubjectsManager\Controllers\SearchController@getAllInSOLR");
	$mvc->get("/ext/getinsolr", "\SubjectsManager\Controllers\SearchController@getInSOLR");
	$mvc->get("/ext/getallfromdb", "\SubjectsManager\Controllers\SearchController@getAllFromDB");
	$mvc->get("/ext/getalltopicrelationships", "\SubjectsManager\Controllers\ProjectTopicDataController@getAllTopicRelationships");
	//	//	$mvc->get("/ext/getrelationships", "\SubjectsManager\Controllers\SearchController@getRelationships");

	//routes used in both public and backend
	$mvc->get("/getumbrellaterms", "\SubjectsManager\Controllers\TopicsController@getAllUmbrellas");
	$mvc->get("/getsubtopics", "\SubjectsManager\Controllers\TopicsController@getSubTopics");



	//NOTE: change this variable to enable or disable security
	$enableSecurity = true;

	//backend routes
	if(!$enableSecurity || \Publications\StaffUser::isLoggedin()){

		//load settings
		if(\Publications\StaffUser::isLoggedin()){
			$envFile = \Publications\StaffUser::getProjectEnvFile();
			if(false == $envFile){
				die("Check that the project " . $_SESSION['PSC_SITE'] . " folder has been setup and is readable. Are you an Admin? Be sure to go to a specific project's dashboard first.");
			}
			require($envFile);

			$_SESSION['PROJECT_ID'] = \MHS\Env::PROJECT_ID;

			$mvc->get("/topicsapp", "\SubjectsManager\Controllers\TopicsAppController");
		
		}
		

		//these routes for backend
		
		$mvc->get("/getproject", "\SubjectsManager\Controllers\TopicsController@getProject");
		$mvc->get("/alltopicdata", "\SubjectsManager\Controllers\TopicsController@getAllTopicData");
		$mvc->get("/gettopicid", "\SubjectsManager\Controllers\TopicsController@getId");
		$mvc->get("/gettopicnamebyid", "\SubjectsManager\Controllers\TopicsController@getName");
		$mvc->get("/getsubtopics", "\SubjectsManager\Controllers\TopicsController@getSubTopics");
		$mvc->get("/getbroadertopics", "\SubjectsManager\Controllers\TopicsController@getBroaderTopics");
		$mvc->get("/searchtopic", "\SubjectsManager\Controllers\TopicsController@searchTopics");
		$mvc->post("/createtopic", "\SubjectsManager\Controllers\TopicsController@postTopic");

		$mvc->post("/updateumbrella", "\SubjectsManager\Controllers\TopicsController@postUmbrella");
		$mvc->put("/updatetopic", "\SubjectsManager\Controllers\TopicsController@putTopic");
		$mvc->delete("/deletetopic", "\SubjectsManager\Controllers\TopicsController@delTopic");

		$mvc->get("/gettopicrelationships", "\SubjectsManager\Controllers\TopicRelationshipsController@getTopicRelationshipsByScope");
		$mvc->get("/getseealso", "\SubjectsManager\Controllers\TopicRelationshipsController@getTopicSeeAlso");
		$mvc->post("/createtopicrelationship", "\SubjectsManager\Controllers\TopicRelationshipsController@postTopicRelationship");
		$mvc->delete("/deletetopicrelationship", "\SubjectsManager\Controllers\TopicRelationshipsController@delTopicRelationship");
		
		$mvc->get("/gettopics", "\SubjectsManager\Controllers\ProjectTopicDataController@getTopics");
		$mvc->get("/gettopicsbyname", "\SubjectsManager\Controllers\TopicsController@getTopicsByName");
		$mvc->get("/gettopicnames", "\SubjectsManager\Controllers\ProjectTopicDataController@getTopicNames");
		$mvc->get("/getprojects", "\SubjectsManager\Controllers\ProjectTopicDataController@getProjects");
		$mvc->post("/createprojecttopicrelationship", "\SubjectsManager\Controllers\ProjectTopicDataController@postProjectTopicRelationship");
		$mvc->get("/getprojecttopicrelationship", "\SubjectsManager\Controllers\ProjectTopicDataController@getProjectTopicRelationship");
		$mvc->get("/getrecentlyeditedtopics", "\SubjectsManager\Controllers\ProjectTopicDataController@getRecentlyEditedTopics");
		$mvc->post("/updateprojecttopicrelationship", "\SubjectsManager\Controllers\ProjectTopicDataController@putProjectTopicRelationship");
		$mvc->delete("/deleteprojecttopicrelationship", "\SubjectsManager\Controllers\ProjectTopicDataController@delTopicRelationship");

	}

	$mvc->remap("/", "views/noaccess.php");
	$mvc->run();
