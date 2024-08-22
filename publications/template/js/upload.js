

	/* usage:
	 *
	 *
		window.addEventListener("DOMContentLoaded", function(){
			var dragdrop = new dropUploader();

			# this function should do something with a responseObj.html property,
			# such as display the .html in a div
			dragdrop.responseHandler = function(responseObj){
				// do stuff here based on the Ajax response
			}

			dragdrop.Init(document.getElementById("filedrag"));
		}, false);

		in DOM:

		<div id="filedrag" data-post-url="/publications/collegians/index.php/upload"
			data-max-file-size="2000000">Drag and drop files here</div>

		NOTE: the data-max-file-size should match the parallel setting in PHP receiving end.

		You can add a .hover style for the dragdrop element and it'll respond to that. yup.

	 *
	 *
	 */



	function dropUploader() {

		//some properties
		this.fileFormName = "fileupload";

		this.responseHandler = null;


		// file selection
		this.FileSelectHandler = function(e) {

			// cancel event and hover styling
			this.FileDragHover(e);

			// fetch FileList object
			var files = e.target.files || e.dataTransfer.files;

			// process all File objects
			for (var i = 0, f; f = files[i]; i++) {
				this.UploadFile(f);
			}
		}



		this.ParseFile = function(file) {
			if (file.type.indexOf("text") == 0) {
				var reader = new FileReader();
				reader.onload = function(e) {
					var src = e.target.result;
				}
				reader.readAsText(file);
			}
		}



		// upload files
		this.UploadFile = function(file) {

			var data  = new FormData();
			data.append(this.fileFormName, file);

			var xhr = new XMLHttpRequest();
			if (file.size <= this.maxFileSize) {

				//set response
				var me = this;

				xhr.onreadystatechange = function(){
					//success
					if(this.readyState == 4){
						if(this.status == 200){
							var obj = JSON.parse(this.responseText);
							me.ajaxStatus(obj);

						} else {
							me.ajaxStatus({html: "Sorry, unable to upload, the server encountered an error."});
						}
					}
				}

				// start upload
				xhr.open("POST", this.url, true);
				xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				xhr.setRequestHeader("X_FILENAME", file.name);
				xhr.send(data);
			} else {
				var msg = "Sorry, your file is too large to upload.";
				if (this.responseHandler !== null) {
					this.responseHandler({html: msg});
				} else {
					console.log(msg);
				}
			}
		}


		// file drag hover
		this.FileDragHover = function(e) {
			e.stopPropagation();
			e.preventDefault();
			if(e.type == "dragover"){
				e.target.className += " hover";
			} else {
				e.target.className = e.target.className.replace(/ hover/g, "");
			}
		}



		this.ajaxStatus = function(obj){
			if (this.responseHandler !== null) {
				this.responseHandler(obj);
			} else {

			}
		}




		/* initialize
		 *
		 *	@filedrag: element onto which to drag files
		 *	@url: 		url to POST to, to receive the files
		 *
		 */
		this.Init = function(filedrag) {

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

				var me = this;

				// file drop
				filedrag.addEventListener("dragover", function(e){ me.FileDragHover(e)}, false);
				filedrag.addEventListener("dragleave", function(e){ me.FileDragHover(e)}, false);
				filedrag.addEventListener("drop", function(e){ me.FileSelectHandler(e)}, false);
				filedrag.style.display = "block";

			} else {
				var msg = "This browser is not compatible with the upload tool.";
				if (this.responseHandler !== null) {
					this.responseHandler({html: msg});
				} else console.log(msg);

				return;
			}
		}
	}
