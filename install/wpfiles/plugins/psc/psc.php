<?php
/*
Plugin Name: Primary Source Cooperative Tools
Author: WRBECK
Version: 0.1
*/


class PSCoopPluginMomma {

	static function Init(){
		add_action("init", "PSCoopPluginMomma::LoadProjectSettings");

		/* Create Staff Member User Role */
		add_role(
			'names_editor', //  System name of the role.
			__( 'Names DB Editor'  ), // Display name of the role.
			array(
				'read'  => true,
				'delete_posts'  => false,
				'delete_published_posts' => false,
				'edit_posts'   => false,
				'publish_posts' => false,
				'upload_files'  => false,
				'edit_pages'  => false,
				'edit_published_pages'  =>  false,
				'publish_pages'  => false,
				'delete_published_pages' => false, // This user will NOT be able to  delete published pages.
			)
		);

		/* Create Staff Member User Role */
		add_role(
			'xml_editor', //  System name of the role.
			__( 'XML Editor'  ), // Display name of the role.
			array(
				'read'  => true,
				'delete_posts'  => false,
				'delete_published_posts' => false,
				'edit_posts'   => false,
				'publish_posts' => false,
				'upload_files'  => false,
				'edit_pages'  => false,
				'edit_published_pages'  =>  false,
				'publish_pages'  => false,
				'delete_published_pages' => false, // This user will NOT be able to  delete published pages.
			)
		);
		
//		remove_role("xml_editor");
	

		/* add our dashboard */
		add_action('wp_dashboard_setup', 'add_coop_dashboard');
		
		function add_coop_dashboard() {
			//global $wp_meta_boxes;
			wp_add_dashboard_widget('coop_dashboard_widget', 'Coop Tools', 'coop_dashboard');
		}
		
		function coop_dashboard() {
			$name = get_bloginfo("name");
			
			print '<h1 id="coop_dash_title" style="font-size: 36px; top: -3rem">' . $name . '</h1>';

			print '
				<a class="button" href="/subjectsmanager/topicsapp" target="_blank"><b style="font-size: 150%">Subjects Manager</b></a><br/><br/>
				<a class="button" href="/tools/namesmanager/index.php" target="_blank"><b style="font-size: 150%">Names Manager</b></a><br/><br/>
				<a class="button" href="/docmanager/index.php" target="_blank"><b style="font-size: 150%">Documents Manager</b></a><br/><br/>
				<a class="button" href="/supportfiles/index.php" target="_blank"><b style="font-size: 150%">Support Files Manager</b></a><br/><br/>
				<a class="button" href="/tools/tools/index.php" target="_blank" XXonclick="javascript: window.open(`/tools/tools/index.php`,`_blank`, `popup,right=0,top=100,width=750,height=700`)"><b style="font-size: 150%">Processes</b></a>
				<br/><br/>
				<a class="button" onclick="javascript: window.open(`/publications/template/views/link-tools.php`,`_blank`, `popup,right=0,top=100,width=450,height=700`)"><b style="font-size: 150%">Link Tools</b></a>
				<br/><br/>
				<a href="/publications/template/documentation/pages-in-wordpress.html" target="_blank">Guide to editing pages in Word Press</a>
				<script>
					let sash = document.getElementById("coop_dashboard_widget");
					sash.parentNode.insertBefore(document.getElementById("coop_dash_title"), sash);
				</script>
			';
		}

//roots of a caching system
		// add_action('save_post', 'customCacher', 10, 2);

		// function customCacher($post_id, $post){
		// 	ob_start();
		// 	print_r($post);
		// 	$str = ob_get_clean();
		// 	file_put_contents("POST.txt", $str);
		// }
	}


	static function LoadProjectSettings(){

		//with no autoloader, include the env file's dependencies first
		require(SERVER_WWW_ROOT . "/environment.php");

		$url = get_site_url();

		preg_match("/https?\:\/\/.+\/(.*)$/U", $url, $matches);

		if(isset($matches[1])) $url = $matches[1];

		if(strpos($url, "/")) $url = explode("/", $url)[0];

		$projectSite = $url;

		if(empty($projectSite)){
			$projectSite = "coop";
		}

		$env = PSC_PROJECTS_PATH . $projectSite . "/environment.php";

		if(!is_readable($env)){
//			error_log("Error in wp psc plugin: can determine project env file, none at: " . $env);
			return false;
		}

		include_once($env);
		PSCoopPluginMomma::GrabLogin($projectSite);
	}

	static function GrabLogin($projectSite) {

		$current_user = wp_get_current_user();
		$roles = ( array ) $current_user->roles;

		session_start();
		$_SESSION['PSC_USER'] = $current_user->data->user_login;
		$_SESSION['PSC_NAME'] = $current_user->data->display_name;
		$_SESSION['PSC_ROLE'] = $roles[0];
		$_SESSION['PSC_SITE'] = $projectSite;

		$_SESSION['PSC_PROJECT_ID'] = \MHS\Env::PROJECT_ID;
	}

}

PSCoopPluginMomma::Init();
