#Encoding to Provided Page Images

If a document has page images that you want to appear in the document display, add an attribute "facs" to the page breaks of your document: 

	<pb n="[page no of the page that  follows this element]" facs="['yes' or image file name and extension, no path ]"/>

If @facs is "yes", then the system will look for a filename that begins with the document id, followed by "-p" followed by the page number, and then ".jpg". For example if the document RBT00115.xml has a 

	<pb n="2" facs="yes"/>
then the system will look for an image named RBT00115-p2.jpg
Otherwise, if @facs is a filename, the system will look for that file where files are uploaded by the docmanager.
If @facs is empty, then no image will be displayed.
Note that filenames are case-sensitive.

If a document begins mid-way through a page, then the first child of the div type="docbody" element should be a page break as above, but with the addition attribute  subtype="continuation"

##DEVELOPER NOTES

All tools should store images in the "[project abbr]/page-images" folder.