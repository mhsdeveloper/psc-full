

App.methods = {
	selectFileTab(){
		this.showFileTab = true;
		this.showSearchTab = false;
	},

	selectSearchTab(){
		this.showFileTab = false;
		this.showSearchTab = true;
	},


	search(){ 
		this.retrieveMode = "search";
		this.searcher.search(); 
	},


	getDocs(){
		this.documents = [];
		this.retrieveMode = "workflow";
		this.searcher.getDocuments();
	},

	
	upload(fileObj){
		this.statusLog = [];
		this.showLog();
		this.uploading = true;
		this.uploadProgress = 0;
		this.uploadFiles = fileObj;
		this.uploadTotal = this.uploadFiles.length;
		this.uploadOne();
	},


	uploadOne(){
		this.uploadProgress = ((this.uploadTotal - this.uploadFiles.length) / this.uploadTotal) + .001;
		if(this.uploadFiles.length == 0){
			this.finishedUpload();
			return;
		}

		let file = this.uploadFiles.shift();

		let data = new FormData();
		if(Env.validateSchemaOnUpload) data.append("checkSchema", "1");
		else data.append("checkSchema", 0);

		data.append("file", file, file.name);

		Yodude.send(Env.baseURL + "upload", data, "POST", "raw").then((resp) => {
			if(resp === false || (resp.errors && resp.errors.length)){
				this.logError(resp.errors);
				return this.finishedUpload();
			} 
			this.log("Uploaded " + file.name);

			for(let i=0;i<this.appEvents.postUpload.length;i++){
				let l = this.appEvents.postUpload[i];
				if(l.interrupt){
					l.func(resp, this.uploadContinue);
					return; //this is the interrupt part, we stop!
				} else {
					l.func(resp);
				}
			}
			
			this.uploadOne();
		});
	},

	uploadContinue(success){
		if(!success){
			alert("The uploader encountered an error.");
			return;
		}
		this.uploadOne();
	},


	finishedUpload(){
		this.uploading = false;
		this.showNewFileDialog = false;
		this.showCheckinDialog = false;
		this.showStatusDialog = true;
		this.updateDocList();
	},

	
	updateDocList(){
		if(this.retrieveMode == "search") this.search();
		else this.getDocs();
	},



	checkin(doc){
		this.uploadProgress = 0;
		if(doc.checked_outin_by != Env.username){
			this.logError(Env.labeling.checkedoutTo + doc.checked_outin_by);
			return;
		}
		this.checkinDoc = doc;
		this.showCheckinDialog = true;
	},


	checkinFileSelected(){
	},


	checkinContinue(){
		this.uploadProgress = .5;
		this.statusLog = [];

		let data = new FormData();
		if(Env.validateSchemaOnUpload) data.append("checkSchema", "1");
		else data.append("checkSchema", 0);
		data.append("document_id", this.checkinDoc.id);
		data.append("fileupload", this.checkinFile);//on PHP end, our controller expects the file to have the array key "fileupload"
		Yodude.send(Env.baseURL + "checkin", data, "POST", "raw").then((resp) => {
			this.uploadProgress = 1;
			if(typeof resp.errors != "undefined" && resp.errors.length){
				this.logError(resp.errors);
			} else {
				this.updateDocList();
			}
			this.log("Checkedin " + this.checkinFile.name);

			for(let i=0;i<this.appEvents.postUpload.length;i++){
				let l = this.appEvents.postUpload[i];
				if(l.interrupt){
					l.func(resp, ()=>{
						this.showCheckinDialog = false;
						this.showStatusDialog = true;
					});
					return; //this is the interrupt part, we stop!
				} else {
					l.func(resp);
				}
			}
		});
	},


	checkout(doc){
		Yodude.send(Env.baseURL + "checkout/" + doc.id)
		.then((resp) => {
			if(typeof resp.errors != "undefined" && resp.errors.length){
				this.showErrors(resp.errors);
				return;
			}
			doc.checked_out = 1;
			this.downloadXML(doc);
		});
	},


	editDocument(doc){
		Yodude.send(Env.baseURL + "checkout/" + doc.id)
		.then((resp) => {
			if(typeof resp.errors != "undefined" && resp.errors.length){
				this.showErrors(resp.errors);
				return;
			}
			doc.checked_out = 1;

			for(let i=0;i<this.appEvents.editDocument.length;i++){
				let l = this.appEvents.editDocument[i];
				val = l.func(doc);
			}
		});
	},

	releaseDocument(doc){
		Yodude.send(Env.baseURL + "undo-checkout/" + doc.id)
		.then((resp) => {
			if(typeof resp.errors != "undefined" && resp.errors.length){
				this.showErrors(resp.errors);
				return;
			}
			doc.checked_out = 0;

			for(let i=0;i<this.appEvents.releaseDocument.length;i++){
				let l = this.appEvents.releaseDocument[i];
				val = l.func(doc);
			}
		});
	},


	uncheckout(){
		Yodude.send(Env.baseURL + "undo-checkout/" + this.checkinDoc.id)
		.then((resp) => {
			if(typeof resp.errors != "undefined" && resp.errors.length){
				this.showErrors(resp.errors);
				return;
			}
			this.checkinDoc.checked_out = 0;
		});
	},


	view(e){
		let ids = this.getDocIds(e.target);
		let doc = this.getDocFromID(ids.docid);
		let xmlid = doc.filename.replace(".xml", "");
		let url = "/publications/" + Env.projectShortname + "/documents/" + xmlid;
		window.open(url, "_blank");
	},


	viewXML(e) {
		let ids = this.getDocIds(e.target);
		let doc = this.getDocFromID(ids.docid);
		this.downloadXML(doc);
	},


	downloadXML(doc){
		Yodude.send(Env.baseURL + "downloadXML", {filename: doc.filename}, "GET")
		.then((resp) => {
			// Create an invisible A element
			const a = document.createElement("a");
			a.style.display = "none";
			document.body.appendChild(a);

			// Set the HREF to a Blob representation of the data to be downloaded
			a.href = window.URL.createObjectURL(
				new Blob([resp.data.fileContent], {type: "text/plain" })
			);

			// Use download attribute to set set desired file name
			a.setAttribute("download", doc.filename);

			// Trigger the download by simulating click
			a.click();

			// Cleanup
			window.URL.revokeObjectURL(a.href);
			document.body.removeChild(a);
		});
	},


	updateDoc(data){
	},


	deleteFile(doc){
		this.deleteID = doc.id;
		this.deleteFilename = doc.filename;
		this.deleteDialog = true;
	},

	deleteContinue(){
		Yodude.send(Env.baseURL + "delete/" + this.deleteFilename)
		.then((resp) => {
			if(resp.errors && resp.errors.length) this.showErrors(resp.errors);
			this.updateDocList();
		});
	},

	async publish(doc){
		if(this.prePublishCheck){
			this.prePublishCheck(doc);
		}
		else {			
			this.publishFinish(doc);
		}
	},

	publishFinish(doc){
		this.showPleaseWait = true;
		let ids = "";
		if(typeof doc.xmlid == "string"){
			ids = doc.xmlid;
		} else if(Array.isArray(doc.xmlid)){
			ids = doc.xmlid.join(";");
		}

		Yodude.send(Env.baseURL + "publish?filename=" + doc.filename + "&ids=" + ids)
		.then((resp) => {
			if(resp.errors && resp.errors.length){
				this.clearLog();
				this.logError(resp.errors);
				this.statusTitle = "Unable to Publish";
				this.showStatusDialog = true;
			} else {
				doc.published = 1;
			}

			this.showPleaseWait = false;
		});
	},


	unPublish(doc){
		this.showPleaseWait = true;

		let ids = "";
		if(typeof doc.xmlid == "string"){
			ids = doc.xmlid;
		} else if(Array.isArray(doc.xmlid)){
			ids = doc.xmlid.join(";");
		}
		Yodude.send(Env.baseURL + "unpublish?filename=" + doc.filename + "&ids=" + ids)
		.then((resp) => {
			if(resp.errors && resp.errors.length) this.showErrors(resp.errors);
			//this.updateDocList();
			else {
				doc.published = 0;
			}
			this.showPleaseWait = false;

		});
	},


	showErrors(errArray){
console.log(errArray);
		if(!errArray) {
			this.errorMsgs = [];
		} else {
			this.errorMsgs = errArray;
		}
	},

	closeError(e){
		this.errorMsgs = [];
	},


	reindex(){
		this.clearLog();
		this.reindexCount = 0;
		this.showLog("Reindexing XML Files");
		this.reindexFiles = [];
		for(let f of this.documents){
			this.reindexFiles.push(f.filename);
		}
		this.reindexTotal = this.reindexFiles.length;
		this.reindexNext();
	},

	reindexOne(filename){
		this.clearLog();
		this.reindexCount = 0;
		this.showLog("Reindexing XML Files");
		this.reindexFiles = [];
		this.reindexFiles.push(filename);
		this.reindexTotal = this.reindexFiles.length;
		this.updateLSReindexQueue();
		this.reindexNext();
	},

	reindexAll(){
		let queue = this.checkLSReindexQueue();
		if(false !== queue){
			this.showContReindex = true;
			return;
		}


		Yodude.send(Env.baseURL + "documents/reindexall")
		.then((resp) => {
			if(resp.errors && resp.errors.length){
				console.log(resp.errors);
			}

			if(resp.data && resp.data.length){
				this.startReindexFromFiles(resp.data);
			} else {
				console.log("No resp.data.docs found.");
			}
		});	
	},

	startReindexFromFiles(files){
		this.reindexFiles = files;
		this.updateLSReindexQueue();
		
		this.clearLog();
		this.showLog("Reindexing ALL XML Files", "progress");
		this.reindexTotal = this.reindexFiles.length;
		this.reindexCount = 0;
		this.reindexNext();
	},

	continueLSReindex(){
		let queue = this.checkLSReindexQueue();
		this.startReindexFromFiles(queue);
		this.showContReindex = false;
	},

	cancelLSReindex(){
		this.reindexFiles = [];
		this.updateLSReindexQueue();
		this.showContReindex = false;
	},

	reindexNext(){
		this.reindexCount++;
		let perc = Math.min(1, (this.reindexCount + 1)/this.reindexTotal);
		this.reindexProgress = perc;
		if(this.reindexFiles.length == 0){
			this.log("Reindexing complete", "end");
			return;
		}

		let filename = this.reindexFiles[0];//don't shift, wait until it truly completes
		Yodude.send(Env.baseURL + "documents/reindex?f=" + filename)
		.then((resp) => {
			if(resp.error) this.logError(resp.error);
			else if(resp.errors && resp.errors.length) this.logError(resp.errors);
			else {
				this.reindexFiles.shift();
				this.log("Reindexed " + filename);
				this.updateLSReindexQueue();
				this.reindexNext();
			}
		});
	},

	updateLSReindexQueue(){
		let queue = this.reindexFiles.join("|");
		localStorage.setItem("docManReindexQueue", queue);
	},


	checkLSReindexQueue(){
		let queue = localStorage.getItem("docManReindexQueue");
		if(!queue || queue.length === 0) return false;
		let set = queue.split("|");
		if(set.length) return set;
		return false;
	},

	/* set interrupt to true if you want the normal flow of the app to
	 wait for some outcome from your listener. In that case, the app will
	 call the listener with an additional argument, which is the f() with which
	  to continue
	 */
	addAppEventListener(eventname, listener, interrupt = false){
		if(!this.appEvents[eventname]){
			console.log("There is no app event named " + eventname);
			return;
		}
		let l = {func: listener, interrupt: interrupt}
		this.appEvents[eventname].push(l);
	},


	showLog(title = "File Uploads", mode = "tall"){
		this.showStatusDialog = true;
		this.statusTitle = title;
		this.statusDialogMode = mode;
	},


	clearLog(){
		this.statusLog = [];
	},

	log(msg, type = "msg"){
		if(Array.isArray(msg)) msg = msg.join("<br/>\n");
		let log = {type: type, text: msg}
		if(type == "error") log.type = "error";
		this.statusLog.push(log);
	},
	 
	logError(msg){
		this.log(msg, "error");
	},

	toggleDrawer: function(e){
		let div = e.target.parentNode.parentNode;
		if(div.classList.contains("open")){
			div.classList.remove("open");
		} else {
			div.classList.add("open");
		}
	},
	toggleStepStatus(step){
		if(this.userlevel < 3) return;
		let status = step.status == 1 ? 0 : 1;
		Yodude.send(Env.baseURL +  "update-document-step?document_step_id=" + step.document_step_id + "&status=" + status).then((resp) => {
			if(typeof resp.errors != "undefined" && resp.errors.length){
				this.$emit("error", resp.errors);
			} else {
				step.status = status;
			}
		});
	},



	zipListed(){
		let files = [];
		for(let d of this.documents){
			files.push(d.filename);
		}
		let filenames = files.join(";");
		window.open(Env.baseURL +  "zip?files=" + filenames);
	},


	zipAll(){
		window.open(Env.baseURL +  "zip");
	},



	resyncConfirm(){
		this.showResync = true;
	},

	resyncAll(){
		Yodude.send(Env.baseURL + "documents/resync")
		.then((resp) => {
			if(resp.errors && resp.errors.length) this.showErrors(resp.errors);
			else {
				location.href = "";
			}
		});
	},



	temp(doc){
		console.log(doc);
	}

}

