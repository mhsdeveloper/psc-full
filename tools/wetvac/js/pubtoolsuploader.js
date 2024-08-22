	/* usage:
		var PTU = new PubToolsUploader();
		PTU.responseHandler = function(resObj, event){
			results.innerHTML = resObj.data.filelistHTML;
		}
		PTU.init(document.getElementById("filedrag"));

		in DOM:
		<div id="filedrag" data-post-url="/publications/collegians/index.php/upload"
			data-max-file-size="2000000">Drag and drop files here</div>

		NOTE: the data-max-file-size should match the parallel setting in PHP receiving end.
		You can add a .hover style for the dragdrop element and it'll respond to that. yup.
		Other form elements get pulled in for a form with the class "dropUploaderForm"
		The entire form is sent as FormData.

		expected reply object from upload ajax event:

		Object {data: {filelistHTML: "" }, errors: Array(0), messages: Array(0), html: ""}
	 */

	function PubToolsUploader(){

		var me = this;

		//some properties
		this.fileFormName = "fileupload";
		this.responseHandler = null;
		this.filedragElement = null;


		// file selection
		this.fileSelectHandler = function(e) {

			// cancel event and hover styling
			this.fileDragHover(e);

			// fetch FileList object
			if(typeof e.dataTransfer != "undefined") var files = e.dataTransfer.files;
			else if(typeof e.target.files != "undefined") var files = e.target.files;
			else {
				console.log("Drop event has no files or dataTransfer");
				return;
			}
			// process all File objects
			for (var i = 0, f; f = files[i]; i++) {
				this.uploadFile(f);
			}

			this.filedragElement.value = "";
		}




		this.parseFile = function(file) {
			if (file.type.indexOf("text") == 0) {
				var reader = new FileReader();
				reader.onload = function(e) {
					var src = e.target.result;
				}
				reader.readAsText(file);
			}
		}



		// upload files
		this.uploadFile = function(file) {

			//look for other inputs
			var forms = document.getElementsByClassName("dropUploaderForm");

			if (forms.length > 0) {
				var data = new FormData(forms[0]);
			} else var data  = new FormData();

			data.append(this.fileFormName, file);


			var xhr = new XMLHttpRequest();
			if (file.size <= this.maxFileSize) {

				//set response
				document.body.className += " uploading";

				xhr.onreadystatechange = function(e){
					//success
					if(this.readyState == 4){
						if(this.status == 200){
							var obj = JSON.parse(this.responseText);
							me.ajaxStatus(obj, e);

						} else {
							me.ajaxStatus({errors: ["Sorry, unable to upload, the server encountered an error."], html: ""}, e);
						}

						document.body.className = document.body.className.replace(/ uploading/g, "");
					}
				}

				// start upload
				xhr.open("POST", this.url, true);
				xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				xhr.setRequestHeader("X_FILENAME", file.name);
				xhr.send(data);
			} else {
				this.responseHandler({errors: ["Sorry, your file is too large to upload."]}, e);
			}
		}



		// file drag hover
		this.fileDragHover = function(e) {
			if (e.type == "dragover"){
				if(e.target.className.indexOf(" hover") < 0) e.target.className += " hover";
			} else {
				e.target.className = e.target.className.replace(/ hover/g, "");
			}
		}



		this.ajaxStatus = function(obj){
			if (this.responseHandler !== null) {
				this.responseHandler(obj);
			} else {
				console.log("error with ajax response; object is null.");
			}
		}



		/* initialize
		 *
		 *	@filedrag: element onto which to drag files
		 *	@url: 		url to POST to, to receive the files
		 *
		 */
		this.init = function(filedrag) {

			this.filedragElement = filedrag;

			this.url = filedrag.getAttribute("data-post-url");
			if (this.url == null) {
				console.log("Please add @data-post-url to your filedrag element");
				return;
			}


			this.maxFileSize = filedrag.getAttribute("data-max-file-size");
			if (this.maxFileSize == null) {
				console.log("Please add @data-max-file-size to your filedrag element. Size is in bytes.");
				return;
			}

			// is XHR2 available?
			var xhr = new XMLHttpRequest();

			if (window.File && window.FileList && window.FileReader && xhr.upload) {

				// file drop
				filedrag.addEventListener("dragover", function(e){
					//e.preventDefault(); 
					//e.stopPropagation(); 
					me.fileDragHover(e);
				}, false);
				filedrag.addEventListener("dragleave", function(e){
					//e.preventDefault(); 
					//e.stopPropagation();  
					me.fileDragHover(e);
				}, false);
				filedrag.addEventListener("drop", function(e){
					//e.preventDefault(); 
					//e.stopPropagation(); 
					me.fileSelectHandler(e);
				}, false);
				filedrag.addEventListener("change", function(e){
					me.fileSelectHandler(e);
				});
				filedrag.style.display = "block";

				//prevent other dropping
				function stopDrag(e){
//					if(e.target == filedrag) return;
					e.preventDefault(); e.stopPropagation();
				}


				window.addEventListener("dragover", stopDrag);
				window.addEventListener("dragleave", stopDrag);
				window.addEventListener("drop", stopDrag);

			} else {
				this.responseHandler({errors: ["This browser is not compatible with the upload tool."]}, null);

				return;
			}
		}
	}
