<?php


    namespace Publications;

    
    /* IMPORTANT ASSUMPTIONS:!!!!!
     *
     * 1) all documents are at the same level, all siblings, can't do anything
     * like those APDE year/month structures!!! All docs are the first level within body
     *
     * 2) <pb> elements must be within <div type="docbody"> elements
     *
     * 3) actualy XML files contain complete pages; never parts of pages of a larger doc
     * 
     * CAVEAT: 
     * we could use some attributes like mhs:leadingPageBeginsIn="[filename]" and
     * mhs:trailingPageEndsIn="[filename]"
     *
     */
    
    
    
    
    class PageMaker {
      
        /* The format should be unindexed array like:
         *  [
         *      0 => [
         *          "id" => "[the HTML id of the element where the image should be aligned at top]",
         *          "n" => "[the @n from the <pb>]",
         *          "filename" => "[filename]",
         *          "precedes" => true|false
         *      ],
         *
         *      1 => ...
         *
         *  ];
         *
         *  if "id" is blank or missing, the system should attach the image to the master
         *  document container.
         
         * page image filenames are built thus:
         * [filename without ext]-p[@n].jpg
         *
         * in the case of the first page, if the same element that has @mhs:startingPage
         * also has @mhs:previousDoc, it will use the latter as the filename for building
         * the first page image filename.
         
         */
        protected $imageList = [];
        
        public $leadingPB = false;
        public $trailingPB = false;
        
        protected $xpath; //this $xpath is for the full xml document
        protected $doc; //the xml file's DOMDocument
        protected $docnode; //the node version of our document fragment
        
        protected $fragment; //separate self-contained DOMDocument version of our document
        protected $namespace = "http://www.tei-c.org/ns/1.0";
        
        protected $pageStrings = [];
        
        protected $docNodeWithPrecedingPB;
        protected $docNodeWithFollowingPB;
        
        
        const MISSING_PAGE_HTML = '
        <div type="doc">
            <div type="docbody">
                <p>[Note: the text for this area is found in another document.]</p>
            </div>
        </div>
        ';

        
        public function __construct(\Publications\DocumentLoader $DocumentLoader) {
            $this->DocLoader = $DocumentLoader;
            $this->doc = $this->DocLoader->doc->getFullDoc();
            $this->xpath= new \DOMXPath($this->doc);
            $this->xpath= \Publications\XmlProcessor::registerNamespaces($this->xpath);
            $this->docnode = $this->DocLoader->doc->getNode();
            
            
            $this->fragment = $this->DocLoader->doc->getFragment();
        }

        
        
        public function getImages(){
            return $this->imageList;
        }
        
        
        
        public function removeLeadingTrailing(){            
            $xpath = new \DOMXPath($this->fragment);
            $xpath = \Publications\XmlProcessor::registerNamespaces($xpath);
            $nodes = $xpath->query(".//tei:pb");
            if($nodes->length){
                
                if($this->leadingPB) {
                    $pb = $nodes->item(0);
                    $pb->parentNode->removeChild($pb);
                }
                if($this->trailingPB) {
                    $pb = $nodes->item($nodes->length -1);
                    $pb->parentNode->removeChild($pb);
                }
            }
        }
            
        
        /* split the xml into separate divs for each page, and rebuild
         * the hierarchy surrounding the <pb> so that the xml remains well-formed.
         */
        
        public function getDocXMLAsPages(){
            
            $this->findImagesFromPbs();
            
            $xml = $this->fragment->saveXML();
            $this->pageStrings = explode("<pb", $xml);
  
            //if no pagebreaks, then just wrap in single page wrapper and leave 
            if(count($this->pageStrings) == 1){
                return $this->wrapXMLInPage($xml);
            }
            
            $xpath = new \DOMXPath($this->fragment);
            $xpath = \Publications\XmlProcessor::registerNamespaces($xpath);
            
            $pbs = $xpath->query(".//tei:pb", $this->fragment);
            
            $output = "";
            $elements = "";
            $pbXpath = [];
            
            $count = count($this->pageStrings);

            /* add xpath arrays to a master array */
            foreach($pbs as $pb){
                $pbXpathSets[] = $this->buildXpath($pb);
            }
            
            //first page has the original opening tags, so just add the xml string
            $pageXML = $this->pageStrings[0];

            /*first page closes it structure based on how the coming
                pb is nested, thus the [1], which is the xpath structure
                for the coming pb
                  */
            $pbXpath = $pbXpathSets[0];
            $pageXML .= $this->closingPageTags($pbXpath);
            $output .= $this->wrapXMLInPage($pageXML);
            
            for($i=1; $i< $count; $i++) {            
                //reset
                $pageXML = "";
                $element = "";
                //build elements string for opening tags to nest fragment
                foreach($pbXpath as $part) {
                    //reverse order, since we're working out way back from the inner element
                    $elements = "<" . $part['nodeName'] . " type=\"" . $part['type'] .  "\">\n" . $elements;
                }
                $pageXML .= $elements;     
                //need to repair the <pb> broken by the explode()
                $pageXML .= "<pb";
                
                $pageXML .= $this->pageStrings[$i];
                
                //close the structure based no NEXT PB LEVEL that we'll be at
                $pbXpath = $pbXpathSets[$i];
                $pageXML .= $this->closingPageTags($pbXpath);
                
                $output .= $this->wrapXMLInPage($pageXML);
            }
            
            return $output;
        }
        
        
        
        public function buildPagesXML(){
            //first evaluate our doc's own edge cases as far as it's own pbs
            $this->findLeadingTrailingPB();
            
            //xapths of the pbs used to form the empty hierarchy around them
            $pbXpaths = [];
            $outputXML = "";
            $returnNode = true;

           // if we start right on a pb, we just need that pb's info, and...
            if($this->leadingPB){
                $docPbs = $this->xpath->query(".//tei:pb", $this->docnode);
                $pb1 = $docPbs->item(0);
                $pbXpaths[$pb1->getAttribute("n")] = $this->buildXpath($pb1);
                //... remove it so it doesn't mess with our parsing later
                $pb1->parentNode->removeChild($pb1);
            }
            /* if we don't start right on a pb, then our page images
             * will have text before our doc; 
             */
            else {
                //find the pb before, and also the doc it's in
                $pb = $this->findPrecedingPB($returnNode);
 
                //Deal with found first page of xml doc later
                if(null == $this->docNodeWithPrecedingPB) {
                    $outputXML = $this::MISSING_PAGE_HTML;
                    //add to the xpath array so we can build images; 
                    //this first item won't actually build tags, so just the @n
                    $pbXpaths[$pb] = ["dummy"];
                    
                    //yet there might be intervening docs, so
                    $doc = $this->xpath->query("/tei:TEI/tei:text/tei:body/tei:div[1]")->item(0);
                    while($doc and $doc->isSameNode($this->docnode) === false){
                        if($doc->nodeType == XML_ELEMENT_NODE) {
                            //if it's a document, grab it!
                            $type = $doc->getAttribute("type");
                            if(in_array($type, \MHS\Env::$DOCTYPES)) {
                                $outputXML .= $this->doc->saveXML($doc);
                            }
                        }
                        $doc = $doc->nextSibling;
                    }
                } else {                
                /* now grab the doc xml that holds the preceding page, and
                 * any intervening docs' xmls
                 * IF there's a node. If not, it's likely at the start of the XML doc
                 */
                    //but first let's track the xpath of the pb, to later recreate the wellformedness
                    $pbXpath = $this->buildXpath($pb);
                    
                    //but first make sure it's last page has signif text
                    $pbflags = $this->findLeadingTrailingPBIn($this->docNodeWithPrecedingPB);
                    
                    //we only need this doc's text if this doc doesn't have a trailing pb
                    if($pbflags['trailing'] === false) {
                        //we only need the xml text starting with the preceding pb
                        //let's add a marking to the text before ...
                        $text = $this->doc->createTextNode("###MHSPLACEHOLDER###");
                        $pb->parentNode->insertBefore($text, $pb);
                        
                        //... so we can split the string there
                        //and remove the actually pb, don't want to split on this later 
                        $pb->parentNode->removeChild($pb);
                        
                        //copy just text after pb
                        $temp = $this->doc->saveXML($this->docNodeWithPrecedingPB);
                        $parts = explode("###MHSPLACEHOLDER###", $temp);
                        $outputXML .= $this->openingPageTags($pbXpath);
                        $outputXML .= $parts[1];
                        
                        //track xpath of pb
                        $pbXpaths[$pb->getAttribute("n")] = $pbXpath;
                    }
                    
                    /* there might be other docs in between, so just because
                     * that doc has a trailing pb and we don't it's text, let be sure 
                     * there aren't others
                     */        
                    $next = $this->docNodeWithPrecedingPB->nextSibling;
                    
                    while($next){
                        //when we find an actual element
                        if($next->nodeType == XML_ELEMENT_NODE) {
                            //check that it's not our doc
                            if($next->isSameNode($this->docnode)) break;
                            
                            //if it's a document, grab it!
                            $type = $next->getAttribute("type");
                            if(in_array($type, \MHS\Env::$DOCTYPES)) {
                                $outputXML .= $this->doc->saveXML($next);
                            }
                        }           
                        $next = $next->nextSibling;
                    }
                }
            }
            
            //mark our document so it can be styled, and add our doc to output
            $this->docnode->setAttribute("class", "selected");
            $outputXML .= $this->doc->saveXML($this->docnode);


            //GRAB ALL OUR xpaths for doc's pbs
            $docPbs = $this->xpath->query(".//tei:pb", $this->docnode);
            foreach($docPbs as $node) {
                $tempx = $this->buildXpath($node);
                //And add keys for these to mark as in our doc
                $tempx['mainDoc'] = true;
                $n = $node->getAttribute("n");
                $pbXpaths[$n] = $tempx;
            }
            
            $postDocXML = "";

            //our own doc closes with a pb, so ...
            if($this->trailingPB){
                //...just remove it since it represents text we don't see
                $pb1 = $docPbs->item($docPbs->length -1);
                $pb1->parentNode->removeChild($pb1);
                
            //now look for next pb if we don't end at a pb
            } else {
                $fpb = $this->findFollowingPB($returnNode);
               
                //this is the docnode of the doc holding the following pb
                if($this->docNodeWithFollowingPB !== null){
                    
                    //but first make sure it's last page has signif text
                    $pbflags = $this->findLeadingTrailingPBIn($this->docNodeWithFollowingPB);
                    
                    //we only need this doc's text if this doc doesn't have a leading pb (thus signif text before pb)
                    if($pbflags['leading'] === false) {
                        //we only need the xml text before the following pb
                        //let's add a marking to the text before ...
                        $text = $this->doc->createTextNode("###MHSPLACEHOLDER###");
                        $fpb->parentNode->insertBefore($text, $fpb);
                        
                        //... so we can split the string there
                        //but first let's track the xpath of the pb, to later recreate the wellformedness
                        $pbXpath = $this->buildXpath($fpb);

                        //copy just text before pb
                        $temp = $this->doc->saveXML($this->docNodeWithFollowingPB);
                        $parts = explode("###MHSPLACEHOLDER###", $temp);
                        $postDocXML = $parts[0];        
                        $postDocXML .= $this->closingPageTags($pbXpath);
                        
                        //don't add to our tracking of xpaths because we've already
                        //closed it, and we don't need the page image
                    }
                    
                    /* there might be other docs in between, so just because
                     * that doc has a  leading pb and we don't it's text, let be sure
                     * there aren't others
                     */
                    $prev = $this->docNodeWithFollowingPB->previousSibling;
                    
                    while($prev){
                        //when we find an actual element
                        if($prev->nodeType == XML_ELEMENT_NODE) {
                            //check that it's not our doc
                            if($prev->isSameNode($this->docnode)) break;
                            
                            //if it's a document, grab it!
                            $type = $prev->getAttribute("type");
                            if(in_array($type, \MHS\Env::$DOCTYPES)) {
                                $postDocXML = $this->doc->saveXML($prev) . $postDocXML;
                            }
                        }
                        $prev = $prev->previousSibling;
                    }
                } else {
                    //there are no more page breaks, but maybe other docs before the end, so..
                    $next = $this->docnode->nextSibling;

                    while($next){
                        //when we find an actual element
                        if($next->nodeType == XML_ELEMENT_NODE) {
                            //if it's a document, grab it!
                            $type = $next->getAttribute("type");
                            if(in_array($type, \MHS\Env::$DOCTYPES)) {
                                $postDocXML .= $this->doc->saveXML($next);
                            }
                        }
                        $next = $next->nextSibling;
                    }
                }
            }
            $outputXML .= $postDocXML;


            // split on pbs and rebuild xml structure            
            $pagestrings = explode("<pb", $outputXML);
            //add first part
            $outputXML = "";
            $pageXML = array_shift($pagestrings);
            
            /* at this point  we should only have pbs where we need them, no leading or trailing pbs,
             * BUT our array of paths should have the leading pb so we can use it to determine the first
             * page image to use.
             * Thus for parsing into wellformedness, we need to skip that first array, thus...
             */
            $i=0;
            
            foreach($pbXpaths as $pbXpath){
                $i++;
                // .. we skip the first pb
                if($i == 1) continue;

                //look for flag that this page should be tagged as of our doc
                if(isset($pbXpath['mainDoc'])){
                    //remove that flag since it'll break the hierarchy
                    array_pop($pbXpath);
                    $cssClass = "selected";
                } else $cssClass = "";
                
                //close off the first page chunk
                $pageXML .= $this->closingPageTags($pbXpath);
                //and wrap in a div
                $outputXML .= $this->wrapXMLInPage($pageXML);
                
                //start new chunk
                $pageXML = ""; 
                //build it's structure
                $elements = $this->openingPageTags($pbXpath, $cssClass);
                
                $pageXML .= $elements;
                
                //fix the broken <pb from our split
                $pageXML .= "<pb";
                //get the chunk
                $pageXML .= array_shift($pagestrings);
                
            }

            //finish off the last one
            //and wrap in a div
            $outputXML .= $this->wrapXMLInPage($pageXML);
            
            //add images for each page
            foreach($pbXpaths as $n => $pageData){
                $this->addToImagelist($n);
            }
 
            return $outputXML;
        }
        

        
        protected function findImagesFromPbs(){
            
            //find out if we have any significant leading and trailing pagebreaks
            $this->findLeadingTrailingPB();
            
            /* if our first page (page before first pb) is NOT empty,
             * Then we need to find the page before, so we can show image
             * for text upto first pb
             */
            if(false === $this->leadingPB) {
                $pre = $this->findPrecedingPB();
                if($pre !== false) $this->addToImagelist($pre, "front");
            }
            
            //find all pbs in our fragment
            $pagelist = $this->xpath->evaluate(".//tei:div[@type='docbody']//tei:pb", $this->docnode);
            
            $count = count($pagelist);
            //don't need last page if no significant text
            if($this->trailingPB) $count--;
            
            for($i=0; $i<$count; $i++) {
                $this->addToImagelist($pagelist[$i]->getAttribute("n"));
            }
        }
        
        
        protected function findLeadingTrailingPB(){
            $parts = $this::findLeadingTrailingPBIn($this->docnode);
            
            $this->leadingPB = $parts['leading'];
            $this->trailingPB = $parts['trailing'];
        }
        
        protected function findLeadingTrailingPBIn($node){
            $nodes = $this->xpath->query(".//tei:div[@type='docbody']", $node);
            $xml = $this->doc->saveXML($nodes->item(0));
            $xml = str_replace("<pb", "$$$$<pb", $xml);
            $xml = strip_tags($xml);
            $xml = preg_replace("/\s/", "", $xml);
            //now all that's left are $$$$ for the pbs and significant characters
            //so we can see if there's anything useful before first and after last pb
            $pages = explode("$$$$", $xml);
            
            $leading = $trailing = false;
            
            if(isset($pages[0]) and strlen($pages[0]) == 0) {
                $leading = true;
            }
            if(strlen($pages[count($pages) -1]) == 0){
                $trailing = true;
            }
            
            return ["leading" => $leading, "trailing" => $trailing];
        }
        
        
        /* finds the preceding pb and returns it's @n,
         * and tracks the doc that was in by setting $this->docNodeWithPrecedingPB
         * returns the pb's @n, or the node itself if 1st arg true
         * OR if no preceding pb, only mhs:startingPage, returns that attribute
         */
        protected function findPrecedingPB($returnPBNode = false){
            //find preceding pbs
            $pagelist = $this->xpath->evaluate("./preceding::tei:pb", $this->docnode);
            
            //there are preceding pbs, so grab nearest
            if($pagelist->length){
                $prevPage = $pagelist->item($pagelist->length -1)->getAttribute("n");
                
                //find parent doc node
                $p = $pagelist->item($pagelist->length -1)->parentNode;
                while($p){
                    $type = $p->getAttribute("type");
                    if(in_array($type, \MHS\Env::$DOCTYPES)){
                        $this->docNodeWithPrecedingPB = $p;
                        break;
                    }
                    $p = $p->parentNode;
                }
                
                if($returnPBNode) return $pagelist->item($pagelist->length -1);
                else return $prevPage;
 
            } else {
                //look for startingPage from parent node
                //we don't need to set $this->docNodeWIthPreceding in this case
                //since there won't be any significant text before our doc
                $p = $this->docnode->parentNode;
                $startingP = '';
                while($p){
                    $startingP = $p->getAttribute("mhs:startingPage");
                    if(!empty($startingP)){
                        //remove leading zeroes
                        while(isset($startingP[0]) and $startingP[0] == "0") $startingP = substr($startingP, 1);
                        return $startingP;
                    }
                    $p = $p->parentNode;
                }
            }
            
            return false;
        }
        
        
        /* finds the following pb and returns it's @n,
         * and tracks the doc that was in by setting $this->docNodeWithPrecedingPB
         */
        protected function findFollowingPB($returnPBNode = false){
            //find preceding pbs
            $pagelist = $this->xpath->evaluate("./following::tei:pb", $this->docnode);

            //there are following pbs, so grab nearest
            if($pagelist->length){
                $nextPage = $pagelist->item(0)->getAttribute("n");

                //find parent doc node
                $p = $pagelist->item(0)->parentNode;
                
                while($p){
                    $type = $p->getAttribute("type");
                    if(in_array($type, \MHS\Env::$DOCTYPES)){
                        $this->docNodeWithFollowingPB = $p;
                        break;
                    }
                    $p = $p->parentNode;
                }
                if($returnPBNode) return $pagelist->item(0);
                else return $nextPage;
                
            } 
            return false;
        }
        
        
        protected function openingPageTags($pbXpath, $cssClass = ""){
            //close off the elements
            $elements = "";
            foreach($pbXpath as $part) {
                //build the xpath in order
                $elements .= "<" . $part['nodeName'] . " type=\"" . $part['type'] .  "\"";
                //put the css class only on the doc level
                if(in_array($part['type'], \MHS\Env::$DOCTYPES) and !empty($cssClass)) $elements .= " class=\"" . $cssClass . "\" ";
                $elements .= ">\n";
            }
            return $elements;
        }
        
        
        protected function closingPageTags($pbXpath){
            //close off the elements
            $elements = "";
            foreach($pbXpath as $part){
                //reverse order, since we're working out way back from the inner element
                $elements = "</" . $part['nodeName'] . ">\n" . $elements;
            }
            return $elements;
        }
        
       
        
        protected function buildXpath(\DOMNode $node){
            //walk up ancestors until find parent doc, and track the xpath as we go
            $par = $node->parentNode;
            $pathElements = [];
            while($par){
                array_unshift($pathElements, ["nodeName" => $par->nodeName, "type" => $par->getAttribute("type")]);
                //when we hit a document level, stop
                if(in_array($par->getAttribute("type"), \MHS\Env::$DOCTYPES)) break;
                $par = $par->parentNode;
            }
            
            //last element is the #document (parent node of first element), so drop
            //array_pop($pathElements);
            return $pathElements;
        }
        
        
        protected function wrapXMLInPage($xml){
            return "<div class=\"page\">" . $xml . "</div>\n";
        }
        
        
        
        protected function addToImagelist($n, $where = "end"){
            $newElement = [
                "n" => $n,
                "id" => $this->htmlElementIdFromN($n),
                "filename" => $this->imageFilenameFrom($this->DocLoader->filename, $n)
            ];
            
            if($where == "end") array_push($this->imageList, $newElement);
            else {
                array_unshift($this->imageList, $newElement);
            }
        }
        
        
        
        /* returns the expected HTML element ID based on the image n,
         * which is used
         */
        protected function htmlElementIdFromN($n){
            return "pageBreak" . $n;
        }
        
        
        protected function imageFilenameFrom($filename, $n){
            $pathparts = pathinfo($filename);
            
            return $pathparts['filename'] . "-p" . $n . ".jpg";
        }
        
        
    } //class