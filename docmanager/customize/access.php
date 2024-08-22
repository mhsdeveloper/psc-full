<?php
	namespace Customize;

	//no autoloader needed, we just have this one file
	include(SERVER_WWW_ROOT . "html/publications/lib/classes/publications/staffuser.php");
	

	//MAINTENANCE CHECK
	if(MAINTENANCE_DOCMANAGER !== false){
		print "This app is currently under maintenance. ";
		if(MAINTENANCE_DOCMANAGER !== true){
				print MAINTENANCE_DOCMANAGER;
		}

		die();
	}
	

	if(\Publications\StaffUser::isLoggedin() == false) die("Please login.");


	/* this is what docmanager requires: that we extend BaseUser and provide
		these methods
	*/

	class User extends \DocManager\Models\BaseUser {

		static function role(){
			return \Publications\StaffUser::role();
		}


		static function isAtLeastXMLEditor(){
			return \Publications\StaffUser::isAtLeastXMLEditor();
		}

		static function username(){
			return \Publications\StaffUser::username();
		}

	}
