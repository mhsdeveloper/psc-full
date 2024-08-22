<?php 


	namespace DocManager\Models;


	class BaseUser {

		static function level(){
			if($_SESSION['PSC_ROLE'] == ROLE_READ_ONLY) return 1;
			if($_SESSION['PSC_ROLE'] == ROLE_XML_EDITOR) return 2;
			if($_SESSION['PSC_ROLE'] == ROLE_EDITOR) return 3;
			if($_SESSION['PSC_ROLE'] == ROLE_ADMIN) return 4;

		}

	}