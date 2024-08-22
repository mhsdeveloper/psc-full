<?php

/* DOCUMENTATION:
 *
 *	This system maps URIs such as myapp/catalog/view/63 to controller files/classes (catalog.php class Catalog) and methods ( view() )
 *  It assumes using views in a subfolder "views" and controllers in "controllers", only one level in. You can changes these folders, but
 *  it does not support subfolders, or other relative locations of them (they need to be a subfolder along side your index.php)
 *
 *	Controllers can render views, which 
 *
 *  This system does nothing else.

 

STEP 1) APACHE [optional]

		APACHE 2.2 CONFIG 
				 
				 
	   <Directory /var/www/html/application/url >
	   
		   Options -Indexes +FollowSymLinks
	   
		   RewriteEngine on
		   RewriteBase /application/url/
	   
		   RewriteCond %{REQUEST_FILENAME} !-f
		   RewriteCond %{REQUEST_FILENAME} !-d
	   
		   RewriteRule ^(.*)$ index.php [L,QSA]
	   
	   </Directory>



STEP 2) route file, that is, the master controller of your app is "my.url.com/install/path/index.php"

	require_once("mhsmvc2.php");
	$mvc = new MHSmvc();
	$mvc->run();

	In this basic setup, the first URI segment after 'index.php' will map to a controller file and class name,
	and the 2nd segment will map to methods.
	
	
	
STEP 3) controllers

	Traffic to "index.php/doc" will route to the file ./controllers/doc.php, include that file, and attempt to instatiate
	a class "doc" or "Doc". Without a class, it'll assume you ran procedural code in the include and execution ends.
	
    If it finds a class: 

	For the url "index.php/doc/view" it will try to run a method "view", If it's there.
 
    If it doesn't find a method, it tries to run index(), which would be free to use "view", grabbing it using $this->_mvc->segment(2).

    Failing that, it redirects to the 404 view you specific with $mvc->set404view(). Or dies!
	

	The controller is in the scope of the parent MHSmvc instantiation's function call, that is, $mvc includes
	your controller. If you write procedural code directly in the controller file (not in a function or class),
	you can reference MHSmvc functions (as of now there's only the render() function!) using $this.

    If there's a class, your class is given the property $this->_mvc which references this routing object, so you can call
    $this->_mvc->segment() and other functions.

	To change the 'controllers' subfolder, use $mvc->setControllersPath($relative_path);
    
    To specify the 404 view, use $mvc->set404view($view_file_name)
    
    In the absence of a matching route or rewrite or controller, the system tries to find an index.php with an index class, and index method.
    BEWARE: you need to define a __construct, because PHP will interpret the index method in an Index class as the constructor
    (method sharing name with class)
    
    
    
STEP 4) Custom Routes and remaps

	You can override the default routing behavior by calling $mvc->remap() before $mvc->run():
	
	$mvc->remap("/", "/views/start.php");
	
	Use this to route a specific php file in your app folder, such as for static views.
	
	
	You can also rewrite URIs using $mvc->rewrite(), to accommodate organizing controllers and methods more
	independently from the URI.

		$mvc->rewrite("/show", "/file_viewer/show");
	
	This simple changes what the URI_REQUEST is, using str_replace. So if the URI_REQUEST was
	"/show/file1234" the system would rewrite that as "/file_viewer/show/file1234" and then
	try to find a controller script and class named "file_viewer" and a method "show", which presumably
	would make use of segment 3 "file1234" to load some data.
	
	Rewrites are given priority in the order specified, so for example, given these rules:

		$mvc->rewrite("/fileviewer/missingDoc123", "/viewer/missing/missingDoc123");
		$mvc->rewrite("/fileviewer", "/viewer/file");
		
	the URI "/fileviewer/missingDoc123" will get sent to the class "Viewer" and the method "missing()" will be run,
	(so you could use the routing to grapple with problem data, for example)
	
	
	
MORE *)	Routing to classes with Namespaces

	This functionality assumes you have registered an autoloader that know how to interpret a full namespace and
	classname and find the file and class to instantiate.

	$mvc->route("/login/user", "myMHS\Database\User@load");
	
		will match any URI request that begins with "/login/user" relative to the app root index.php
		this will instantiate the class User in the ns myMHS\Database, give it the property _mvc for access to the
		app config,view methods etc., and call method "load". Methods can call $this->_mvc->segment(n) to get
		URI request segments as needed.
		Without a "@" indicating a method, the mvc will look for a method matching the next segment, or
		absent that, the method "index" is assumed.
	
		the mvc iterates thru the stored routes and askes if the URI_REQUEST contains the array key (first argument),
		and if so, stops checking and follows the match. So, given:
			
			$mvc->route("/login", "Database\Security\Login@start");
			$mvc->route("/login/user,"Database\Models\User@load");
			
		the second route will never run, because the URI "/login/user/tommy" contains the first route "/login".
		Instead, registered the routes in reverse order, starting with the most specific URI
	
			$mvc->route("/login/user,"Database\Models\User@load");
			$mvc->route("/login", "Database\Security\Login@start");
	
WHICH TAKE PRECEDENT?

	->remap 	URI matches are checked first, and matches routed.
	->route			to auto-loaded classes are checked next,
	->rewrite		and rewrites are next, which 
	
	
	
	
	VIEW:
		In the view scope, $this is the MVC object.
	
		Views can use $this->base_url() to get the path to the base controller, which includes "index.php"
		
		View URLs for css and js should use $this->base_dir(), which is without the "index.php" part

		The name of the view passed to render can be like "document" or "document.php" or "subfolder/document".
		This looks in $this->views_path and below.
		If the name of the view begins with a slash "/otherpath/view.php" then the path is changed to $_SERVER['DOCUMENT_ROOT'] . $view
	
*/
		
	
	class MHSmvc {
		
		private $remaps = array(); 
		private $routes = array(); 
		private $methodRoutes = ['DELETE' => [], "GET" => [], "OPTIONS" => [], "PATCH" => [], "POST" => [], "PUT" => []];
		private $rewriting = array();
		private $redirects = array();
		
		//default filename of a view for 404 errors
		private $view404 = "404.php"; 
		
		/* default relative path to controllers. The leading slash is correct! all urls in this system start this way
		 * so that users can set the app's home route with this syntax: $app->set_route( "/", "[path to include file]")
		 */
		private $controllers_path = "/controllers";
		
		// default view path
		private $views_path = "/views";
		
		//file to include after we've resolve routes and mappings
		private $file = '';
		
		/*
			$friendly_url 	: the url relative the the MVC application root path. These are not GET params, as the apache rewrite
									will maintain those and pass separately from the rewritten url.
									An example friendly_url:  /collections/sculpture
			$real_url		: the complete url relative to the MVC application that you want to go to. For example:
									/views/start.php
		*/
		public function remap($friendly_url, $real_url){
			//make sure we have a leading slashes
			if($friendly_url[0] != '/') $friendly_url = '/' . $friendly_url;
			if($real_url[0] != '/') $real_url = '/' . $real_url;
			$this->remaps[$friendly_url] = $real_url;
		}

		/*
			$urlMatch		: $url part to try to match
			$urlReplacement	:	replace with this string
			This function stores a url match/replacement pair as key->value in our array
		*/
		public function rewrite($urlMatch, $urlReplacement){
			$this->rewriting[$urlMatch] = $urlReplacement;
		}
		
		public function redirect($urlMatch, $absoluteURL){
			$this->redirects[$urlMatch] = $absoluteURL;
		}
		
		
		/* relate URI to ns/classes and methods
			$urlPattern:	 A URI path, with optional trailing *, to map to
			$fullClass
			
		*/
		public function route($urlPattern, $fullClassName){
			$this->routes[$urlPattern] = $fullClassName;
		}


		public Function delete($urlPattern, $fullClassName){
			$this->routeMethod("DELETE", $urlPattern, $fullClassName);
		}
		
		public Function get($urlPattern, $fullClassName){
			$this->routeMethod("GET", $urlPattern, $fullClassName);
		}
		
		public Function patch($urlPattern, $fullClassName){
			$this->routeMethod("PATCH", $urlPattern, $fullClassName);
		}
		
		public Function post($urlPattern, $fullClassName){
			$this->routeMethod("POST", $urlPattern, $fullClassName);
		}
		
		public Function put($urlPattern, $fullClassName){
			$this->routeMethod("PUT", $urlPattern, $fullClassName);
		}
		public Function options($urlPattern, $fullClassName){
			$this->routeMethod("OPTIONS", $urlPattern, $fullClassName);
		}
			
		public function routeMethod($method, $urlPattern, $fullClassName){
			if(!isset($this->methodRoutes[$method])){
				die("The method $method is not supported. Add it to mhsmvc2 class");
			}
			$this->methodRoutes[$method][$urlPattern] = $fullClassName;
		}

		
		/* run the system! */
		//wrappers
		public function go(){ $this->resolve_uri();}
		public function run(){ $this->resolve_uri();}
		public function resolve_uri(){
			
			
			/* all processing of thr URI_REQUEST for finding what file/controller to use
			   operates before any GET params, so let's isolate that
			*/
			
			$temp = explode('?', $_SERVER['REQUEST_URI']);
			$request = $temp[0];

			/*  we need to find the URI segments after the main index.php file
				depending on whether rewriting is enabled, this might mean removing
				the index.php file part of the script name.
				But we don't want to assume user is using index.php
				to setup everything, so we'll remove any PHP file from script path name, and them
				remove that from the Request URI
			*/
			
			//we clean our script name, leaving out the last segment (the name)
			$temp = explode("/", $_SERVER["SCRIPT_NAME"]);

			array_pop($temp);
			$cleanScript = implode("/", $temp);
		
			//now: remove script name from request
			// ... and also remove cleaned script name
			$request = str_replace(array($_SERVER["SCRIPT_NAME"], $cleanScript), "", $request);
			
			//fix leading slash: require one!
			if(!empty($request)) $request = ($request[0] == '/' ? $request : "/" . $request);
			else $request = '/';

			//fix trailing slash
			$request = $this->removeTrailingSlash($request);

			//Some basic security
			//remove attempts to navigate out one level
			$request = str_replace("../", "", $request);

			$path = $this->controllers_path;// default to the object's set path; logic for friendly_urls may override
			//check trailing slash
			$path = $this->addTrailingSlash($path);
			
			$file = '';
			if(empty($request)) {
				//no url string; top level of app so default to controller named "index"
				$request = "/index";	
			}
	
			// now process the rq
			$this->matchRedirects($request);

			if($this->matchRemaps($request)) {

				//found it: run and finish!
				if(is_readable($this->file)) {
					include($this->file);
					return;
				}
				//didn't find it, call our 404
				else {
					$this->call404($this->file);
					return;
				}
			}
			
			//next check for match in 
			else if($this->matchRoutes($request)) {
				//build our segments
				$this->segments = explode("/", $request);

				$obj = new $this->fullClassName();

				if(is_object($obj)){
					$obj->_mvc = $this;
					
					$this->controller = $obj;
					
					$method = $this->methodToCall;
					
					if(method_exists($obj, $method)) {
						$obj->$method();
						return;
					}
					//hope for index method
					else if(method_exists($obj, "index")) {
						$obj->index();
						return;
					}
					//no luck at all!
					else {
						$this->call404($request);
					}
					
					return;
				}
				
				return;
			}
			
			/* lastly, look for a controller file controller class, and method in our namespaceless,
			  Codeigniter-style convention
			*/
			else {
				//first rewrite URIs
				$request = $this->rewriteURI($request);
	
				//build our segments
				$this->segments = explode("/", $request);

				$seg1 = $this->segment(1); //not 0, because our request begins with a leading slash, so the first array part is 0

				$this->controller = $seg1;
				$this->file =  $path . $this->controller;
		
				//it'll have to be a .php file, so insure we have the extension
				if(strpos($this->file, ".php") === false) $this->file .= ".php";
	
				//need to make the file relative
				$this->file = '.' . $this->file;

				//try this file controller combo
				if($this->loadController()) return;
				
				//nope, so let's look for an index controller
				else {
					
					$this->file = '.' . $path . "index.php";
					$this->controller = "index";

					//yeah, found index 
					if($this->loadController()) return;
					else $this->call404($request);
					
				}
			}
		}
		
		
		/* try to match a redirect
		 */
		private function matchRedirects($request){

			if(isset($this->redirects[$request])) {
				header("Location: " . $this->redirects[$request]);
			}
			
			return true;
		}
		
		
		
		/* try to match requests with a specific route,
		 * return true on match, false on fail
		 */
		private function matchRemaps($request){

			if(isset($this->remaps[$request])) {

				$this->file = '.' . $this->remaps[$request];	
				
				return true;
			}
			else return false;
		}
		
		
		/* look for a URI pattern that matches our request
			store full ns\classname and method for parent to call
		 */
		
		private function matchRoutes($request){

			//first look for routes on specific methods
			foreach($this->methodRoutes as $method => $routes){
				if($_SERVER['REQUEST_METHOD'] === $method){
					krsort($routes);
					foreach($routes as $route => $class){
						if($this->matchRoute($request, $route, $class)) return true;
					}

					break;
				}
			}

			//next match general routes
			krsort($this->routes);
			foreach($this->routes as $route => $class){
				if($this->matchRoute($request, $route, $class)) return true;
			}
			
			return false;
		}
		

		private function matchRoute($request, $route, $class){
			/* logic:
				* The $request is the haystack, not the route:
				* the defined route has to appear at
				* the beginning of the request to match, so
				* that if the request also has URI segment parameters,
				* the match still happens. Thus the reverse sort,
				* to look to see if more specific defined routes
				* fit our URIrequest first
				*/
			if(strpos($request, $route) === 0) {
				//look for method after @
				if(strpos($class, '@')) {
					$temp = explode('@', $class);
					
					$this->fullClassName = $temp[0];
					$this->methodToCall = $temp[1];
				}
				
				//is our request - our route a method?
				else {
					$this->fullClassName = $class;

					//look for methods
					//find what's left of request after removing route
					$temp = str_replace($route, "", $request);
					
					//if there's anything, we'll try it later as a method
					if(strlen($temp) > 0) {
						//remove leading slash
						if($temp[0] == "/") $temp = substr($temp, 1);
						
						//but only take the part before any GET uri params
						if(strpos($temp, "?") !== false){
							$temp = explode("?", $temp);
							$temp = $temp[0];
						}
						
						$this->methodToCall = $temp;
						
					}
					//nope? then index()!
					else $this->methodToCall = "index";
				} 

				return true;
			}
			return false;
		}
		
		
		private function rewriteURI($request){

			foreach($this->rewriting as $find => $replace) {
				
				//do we have a match? replace, and break
				if(strpos($request, $find) !== false){
					
					//replace the 
					$request = str_replace($find, $replace, $request);
					
					break;
				}
			}
			
			return $request;
		}

		
		
		/* this function uses segment 1 as the name of a file and controller class,
		 * as tries to read the file.
		 * Then it tries to instantiate the class.
		 * Then, it tries to use segment 2 as a method, falling back to "index()".
		 *
		 * Returns true on success, or if it might have been procedural code that was run.
		 * returns false if class instantiated but no method found, or no readable file.
		 */

		private function loadController(){
			
			if(is_readable($this->file)) {
					
				include($this->file);
				//look to see if file defines a class, as per most mvc frameworks
				if (class_exists($this->controller, false)) {

					//name it and instantiate
					$classname = $this->controller;
					$instance = new $classname();

					//add reference to our router
					$instance->_mvc = $this;
					
					$this->controller = $instance;

					//what method do we call? 
					
					//first, see if we need to map to a method as segment 2
					$seg2 = $this->segment(2);

					if($seg2) {
						
						//look for a method matching that
						if(method_exists($instance, $seg2)) {
							$instance->$seg2();
							
							return true;
						}
						
						//or assume seg2 etc are used by 'index' method
						else if(method_exists($instance, "index")) {
							$instance->index();
							
							return true;
						}
						
						//or fail!
						else return false;
					}
					
					//No further segments, then look for index()
					else if(method_exists($instance, "index")) {
						$instance->index();
						return true;
					}
					
					//we have a class, but no method to call
					else return false;
				}
	
				//no class: when all else fails, we'll assume the included file is procedural and it was run.
				return true;
			
			} else return false; //not readable
	
		}

		
		/* this looks for and calls the view file*/
		public function render($view, $vars = array()){
			//if view has leading slash, interpet as full path from HTML ROOT
			if($view[0] == "/"){
				$file = $_SERVER['DOCUMENT_ROOT'] . $view;
			} else {
				//ERROR IF NO VIEW FOUND?
				$file = '.' . $this->views_path . "/" . $view;
			}
			if(strpos($view, ".php") === false) $file .= ".php";

			if(is_readable($file)) {
			
				//promote $vars array to local vars
				foreach($vars as $var => $val) $$var = $val;
		
				//INCLUDE VIEW
				include($file);
			} else {
				die("Sorry, unable to load the view " . $file);
			}
		}

		
		/* retrieve a URI segment
		 */
		
		public function segment($num){
			if(isset($this->segments[$num])) return $this->segments[$num];
			else return false;
		}


		/* get the base url, the url on the server to the app's root folder,
		 * so views etc can reference JS/CSS/Images assests properly in their href/src attributes
		 * BUT this returns with the index.php, so not good for CSS/JS, use base_dir
		 */
		public function base_url(){
		
			//save some processing by looking for stored version
			if(isset($this->baseurl)) return $this->baseurl;
			
			//tokenize SCRIPT_NAME and REQUEST_URI
			
			//step through and compare, keeping result until they diverge; this way,
			//we support both rewrites and the ../index.php/uri/request formats
			
			$scriptTokens = explode("/", $_SERVER['SCRIPT_NAME']);
			$uriTokens = explode("/", $_SERVER['REQUEST_URI']);
			
			$out = '';
			
			$count = count($scriptTokens);
			
			for($x=0; $x<$count; $x++){
				if($scriptTokens[$x] != $uriTokens[$x]) break;
				$out .= $scriptTokens[$x] . '/';
			}
			
			$this->baseurl = $this->removeTrailingSlash($out);
			return $this->baseurl;
		}
		
		
		
		/* returns the base directory, suitable for dynamic paths to JS/CSS
		 */
		public function base_dir(){
			
			//remove last segment of script name
			$parts = explode("/", $_SERVER['SCRIPT_NAME']);
			array_pop($parts);
			$path = implode("/", $parts);
			return $path;
		}
		

		
		/* set controller path */
		public function setControllersPath($path){
			if($path[0] != '/') $path = '/' . $path;
			$path = $this->removeTrailingSlash($path);
			$this->controllers_path = $path;
		}
		
		/* set view path */
		public function setViewsPath($path){
			if($path[0] != '/') $path = '/' . $path;
			$path = $this->removeTrailingSlash($path);
			$this->views_path = $path;
		}
		
		
		/* set a view to show when we can't resolve the URI_REQUEST to any controller */
		public function set404view($path){
			$this->view404 = $path;
		}
		
		
		public function call404($request){
			if(is_readable( '.' . $this->views_path . '/' . $this->view404)){
				
				$data['request'] = $request;
				$this->render($this->view404, $data);
				return;
			}
			else {
				header("HTTP/1.0 404 Not Found");
				die("Sorry, we could not negotiate the URI you provided.");
			}
		}
		
		
		/* retrieve last part of a path */
		private  function pathLastSegment($path){
			if(strpos($path, '/') !== false){
				$temp = explode('/', $path);
				$last = array_pop($temp);
				
				return $last;
			}
			
			else return $path;
		}
		
		
		/* remove a trailing '/' if there is one */
		private function removeTrailingSlash($str){
			$len = strlen($str);
			if($len > 1) if($str[$len - 1] == '/') $str = substr($str, 0, ($len -1));
			return $str;
		}
		
		private function addTrailingSlash($str){
			$len = strlen($str);
			if($len > 1) if($str[$len - 1] != '/') $str .= '/';
			return $str;			
		}

	}
	
	
