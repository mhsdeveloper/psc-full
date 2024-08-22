/* 

preliminaries to avoid undefined elements 
these should be overridden in customize-frontend/scripts.js

*/

	window.docManagerCustomDrawerTemplate = "";


	// override this in scripts.js with properties to add to your Vue data
	window.docManagerData = {}

	// override this in scripts.js to call App.addAppEventListener after app is mounted
	window.docManagerCustomizer = function(App){}