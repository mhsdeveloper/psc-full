<?php
	
	/* in addition to the requirements of the interface, note these global constants
	 * need to be defined. The are global because they are shared among classes.
	 
	 
		\MHS\Env::DOC_LINK_PREFIX		: which is the whole href string before a document's ID
		\MHS\Env::SHORT_TITLE_LINK_PREFIX: same, but for the URL to display the short title definition
		
		$this::TEMPLATE_OVERRIDE_PATH
			set this to the full path of an xsl fill to include that file, which could contain
			higher priority templates for customizing the TEI-fragment display

	*/



	namespace Publications;


	interface DocInterface {
		
		
		/* return an array of those letters, case sensitive,
		 * that are allowed in the document ID scheme of the system.
		 *
		 * These will be used to filter and sanitize input.
		 *
		 * for example, return this:
					
					return array("R", "n", "p");
					
		 */
		
		function getAllowedIdChars();
		
		
		
		
		/* returns an array of those chars that should be removed
		 * from a URL's vol parameter to check for sanity, i.e.
		 * to check if what remains is numeric
		 */
		
		function getAllowedVolChars();
		


		
		/* returns an array of those chars that should be removed
		 * from a URL's page parameter to check for sanity, i.e.
		 * to check if what remains is numeric
		 */
		
		function getAllowedPageChars();
		
		
		
		
		
		/* returns a full path of the Volume or other XML that contains a doc
		 * when the document's ID is passed to it.
		 * In other words, the function expresses the relationship between doc ID
		 * and volume file path.
		 */
		
		static function find($id);
		
		
		
		/* returns the volume from the ID without a leading zero
		 */
		
		static function volumeFromID($id);
		
		
	}//interface