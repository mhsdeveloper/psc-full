
var App = {

	el: "#app",

	mounted: function() {
		this.getFiles();
	}
}



let AppData = {
	Env: Env,
	username: Env.username,
	userRole: Env.role,
	userLevel: Env.level,

	files: [],

	uploadFiles: [],
	uploadTotal: 0,
	uploadProgress: 0,
	uploading: false,
	log: [],
	logShowing: false,

	editingFilename: "",
	editor: null,
	editorShowing: false,
	deleteFilename: "",

	confirmText: "",
	confirmShowing: false
};

App.watch = {
	uploadFiles(e){
		this.upload(e);
	}
}


App.computed = {}

App.methods = {

	async getFiles(){
		let url = Env.baseURL + "dir";
		const response = await fetch(url);
		if (!response.ok) {
		  throw new Error("Unable to access support files server.");
		} else {
			const resp = await response.json();
			this.files = resp.data;
		}
	},
	
	upload(fileObj){
		this.log = [];
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

		data.append("file", file, file.name);

		Yodude.send(Env.baseURL + "upload", data, "POST", "raw").then((resp) => {
			if(resp === false || (resp.errors && resp.errors.length)){
				this.logError(resp.errors);
				return this.finishedUpload();
			} 
			this.logMsg("Uploaded " + file.name);
		
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
		this.getFiles();
	},


	download(filename){
		Yodude.send(Env.baseURL + "download", {filename: filename}, "GET")
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
			a.setAttribute("download", filename);

			// Trigger the download by simulating click
			a.click();

			// Cleanup
			window.URL.revokeObjectURL(a.href);
			document.body.removeChild(a);
		});

	},

	deleteFileCheck(filename){
		this.confirmText = "Are you sure you want to delete " + filename + "?";
		this.confirmShowing = true;
		this.deleteFilename = filename;
	},


	async deleteFile(){
		const response = await fetch(Env.baseURL + "deleteFile?name=" + this.deleteFilename);
		if (!response.ok) {
		  throw new Error("Unable to access support files server.");
		} else {
			const resp = await response.json();
			this.deleteFilename = "";
			//update list
			this.getFiles();
		}
	},

	cancelDelete(){
		this.deleteFilename = "";
	},


	showLog(title = "File Uploads", mode = "tall"){
		this.logShowing = true;
		this.statusTitle = title;
		this.statusDialogMode = mode;
	},


	clearLog(){
		this.statusLog = [];
	},

	logMsg(msg, type = "msg"){
		if(Array.isArray(msg)) msg = msg.join("<br/>\n");
		let log = {type: type, text: msg}
		if(type == "error") log.type = "error";
		this.log.push(log);
	},
	 
	logError(msg){
		this.logMsg(msg, "error");
	},

	/* for now this is just html files */
	async edit(filename){
		if(this.editingFilename.length){
			alert("Already editing a file. Please finish before editing another.");
			return;
		}

		//first, get the file
		const response = await fetch(Env.baseURL + "getFile?name=" + filename);
		if (!response.ok) {
		  throw new Error("Unable to access support files server.");
		} else {
			const resp = await response.json();
			this.editingFilename = filename;
			let el = document.getElementById("editor");
			this.editorShowing = true;
			el.innerHTML = resp.data.text;
			this.editor = new Quill('#editor', {
				modules: {
					toolbar: [
						['bold', 'italic', 'underline'],        // toggled buttons
						[{"header": [1,2,3,4,5,6,false]}],
					]
				},
				theme: 'snow', 
			});
		}
	},


	saveHTML(){
		let el = document.getElementById("editor");
		let inner = el.firstChild.innerHTML;


		let form = new FormData();
		form.append("text", inner);
		form.append("filename", this.editingFilename);

		Yodude.send(Env.baseURL + "saveFile", form, "POST", "raw").then((resp) => {
			if(resp.errors && resp.errors.length){
				for(let e of errors){
					this.logError("Sorry, the server encountered an error: <br/>" + e);
				}
				this.showLog("Error occured");
			} else {
				//save to close
				this.cancelEdit();
				this.showLog("File saved.");
			}
		});
	},

	cancelEdit(){
		this.editingFilename = "";
		let el = document.getElementById("editor");
		this.editorShowing = false;
		el.innerHTML = "";

		//remove quill toolbar
		let toolbar = document.getElementsByClassName("ql-toolbar");
		toolbar[0].parentNode.removeChild(toolbar[0]);
	}

}



