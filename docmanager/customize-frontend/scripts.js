	/******************************************************************
	 
		Here we add properties to our vue app, that get folded into
		the app's initialization with data: function(){return {etc}}

		the global AppData will get returned to Vue: App.data = function() { return AppData}

	 ******************************************************************/

	AppData.currentHusc = false;
	AppData.currentName = null;
	AppData.showNameLookup = false;
	AppData.showStatus = false;
	AppData.auditFilename = "";
	AppData.auditTitle = "";
	AppData.auditText = "";

	//these should coordinate with the html inputs in customize-frontend/search-inputs.php
	AppData.searchSolrFields = {
		husc: "",
		subject: ""
	}


	/******************************************************************
		Add any vue methods here to App.methods; for example:

		App.methods.myMethod = function(){
			do something
		}

		here we use function(), not () => {}, because these functions
		will run in the scope of the vue app; "this" inside function()
		will be the vue app.

	 ******************************************************************/





	App.methods.fileNoExt = function(document){
		let f = document.filename.split(".xml")[0];
		return f;
	}
	
	
	App.methods.fileDate = function(document){
		if(!document.date_when || !document.date_to) return "[not found in SOLR, reindex?]";
		let d = document.date_when;
		let t = document.date_to;
		if(t != d) return d + " to " + t;
		return d;
	}


	App.methods.buildDocTextPreview = function(document, length = 400){
		if(Array.isArray(document.doc_beginning)) return document.doc_beginning[0].substring(0, length);
		if(document.doc_beginning) return document.doc_beginning.substring(0, length);
		return "";
	}
	
	
	App.methods.checkedOutInDate = function(document){
		let d = document.checked_outin_date;
		return d;
	}



	App.methods.nameLookup = function(name){
		this.currentName = null;
		this.currentHusc = name;
		this.showNameLookup = true;
		Yodude.send(Env.apiURL + "name/" + name)
		.then((resp) => {
			if(typeof resp.errors != "undefined" && resp.errors.length){
				return;
			}

			this.currentName = resp.data[0];
		});
	}

	//this is the method when docs are uploaded or checked in
	App.methods.auditHuscs = async function(filename, callbackF){
		Yodude.send(Env.baseURL + "document/audithuscs?f=" + filename).then((resp) => {
			if(resp.errors && resp.errors.length){
				this.logError(resp.errors);
				callbackF(true);
				return;
			}

			if(resp.messages && resp.messages.length){
				this.log(resp.messages.join("<br/>\n"));
			}

			let msg = resp.data.join(", ");
			this.log("Please note: the file " + filename + " contains the following HUSCS which are not in the database: " + msg, "notice");

			Yodude.send(Env.baseURL + "document/makenamespublic?f=" + filename).then((resp) => {
				if(resp.errors && resp.errors.length){
					this.logError(resp.errors);
					callbackF(true);
					return;
				}
	
				if(resp.messages && resp.messages.length){
					this.log(resp.messages.join("<br/>\n"));
				}
	
				let msg = "";
				if(Array.isArray(resp.data.messages)) msg = resp.data.messages.join(", ");
				else if(resp.data.messages) msg = resp.data.messages;
				else if(Array.isArray(resp.data)) msg = resp.data.join(", ");

				this.log("Making names from the document publicly searchable: " + msg, "notice");
				callbackF(true);
			});
		});
	}


	//this is the method clicking the audit button for a doc in the db
	App.methods.checkHuscs = function(filename){	
		this.auditTitle = "Auditing HUSCs for";
		this.auditText = "";
		this.clearLog();
		this.showStatus = true;
		this.auditFilename = filename;
		Yodude.send(Env.baseURL + "document/checkhuscs?f=" + filename).then((resp) => {
			if(resp.errors && resp.errors.length){
				this.logError(resp.errors);
				return;
			}

			if(resp.messages && resp.messages.length){
				this.log(resp.messages.join("<br/>\n"));
			}
			if(resp.data.length){
				this.auditText = "Please note: the file " + filename + " contains the following HUSCS which are not in the database:<br/><b>";
				let msg = resp.data.join("</b><br/><b>");
				this.auditText += msg + "</b>";

			} else {
				this.auditText = "All of the HUSCs mentioned in this file are in the names database.";
			}

		});
	}



	App.methods.checkRevDesc = function(filename){
		this.auditTitle = "Checking Revision Descriptions for";
		this.clearLog();
		this.auditText = "";
		this.showStatus = true;
		this.auditFilename = filename;
		Yodude.send(Env.baseURL + "document/checkrevdesc?f=" + filename).then((resp) => {
			if(resp.errors && resp.errors.length){
				this.logError(resp.errors);
				return;
			}

			if(resp.messages && resp.messages.length){
				this.log(resp.messages.join("<br/>\n"));
			}

			if(resp.data){
				let keys = Object.keys(resp.data);

				for(let key of keys){
					this.auditText += "<div><b>" + key + "</b><br/>";
					if(Array.isArray(resp.data[key])){	
						this.auditText += resp.data[key].join("<br/>");
					} else this.auditText += resp.data[key];
					this.auditText += "<br/></div>";
				}
			} else {
				this.auditText = "No Revision Description information found";
			}

		});
	}



		
	App.methods.auditSubjects = async function(filename, callbackF = null){
		this.auditTitle = "Auditing Topics for";
		this.clearLog();
		this.auditText = "";

		this.showStatus = true;
		this.auditFilename = filename;

		//first get subjects in actual XML
		///document/getsubjects

		Yodude.send(Env.baseURL + "document/getsubjects?f=" + filename).then((resp) => {

			if(resp.errors && resp.errors.length){
				this.logError(resp.errors);
				return false;
			}

			if(resp.messages && resp.messages.length){
				this.log(resp.messages.join("<br/>\n"));
			}

			if(resp.data){
				//these are the topics in the actual XML
				let foundSubjects = resp.data;

				//create lookup for all subjectsi n XML
				let lookup = {}
				for(let s of foundSubjects){
					lookup[s] = {inDB: false, assigned: false};
				}

				//find what are in db and what are assigned to project
				let subjstr = foundSubjects.join(";");
				Yodude.send("/subjectsmanager/gettopicsbyname?t=" + subjstr + "&project=" + Env.projectShortname).then((resp) => {
					if(resp.errors && resp.errors.length){
						this.logError(resp.errors);
						return false;
					}

					if(!resp.topics || !resp.assigned){
						this.logError("Unable to access topics DB");
						return false;
					}

					for(let sub of foundSubjects){
						for(let topic of resp.topics){
							if(topic.topic_name == sub){
								lookup[sub].inDB = true;
								break;
							}
						}

						let keys = Object.keys(resp.assigned);
						for(let key of keys){
							let topic = resp.assigned[key];
							if(topic.topic_name == sub){
								lookup[sub].assigned = true;
								break;
							}
						}
					}

					//resort so that problems listed first
					let outKeyOrder = []
					let keys = Object.keys(lookup);
					for(let key of keys){
						if(!lookup[key].inDB || !lookup[key].assigned){
							outKeyOrder.unshift(key);
						} else outKeyOrder.push(key);
					}

					//format display
					this.auditText += "<div class='report'>";
					let statusText = "";
					let notInDBCount = 0;
					let notAssignedCount = 0;
					for(let key of outKeyOrder){
						let cls = "";
						statusText += "<p><b>" + key + ":</b> ";

						if(lookup[key].inDB) statusText += `<span class='ok'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
						<path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
					  </svg>in DB</span> `;
						else{
							notInDBCount++;
							statusText += `<span class='bad'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
							<path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
						  </svg>NOT in DB</span> `;
						}

						if(lookup[key].assigned) statusText += `<span class='ok'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
						<path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
					  </svg>assigned</span>`;
						else {
							notAssignedCount++;
							statusText += `<span class='bad'><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
							<path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
						  </svg>topic not assign to edition</span>`;
						}
						statusText += "</p>";
					}

					// if(notInDBCount){
					// 	this.auditText += "<h3 class='notice'>" + notInDBCount + " topics are not in the DB</h3>";
					// }
					// if(notAssignedCount){
					// 	this.auditText += "<h3 class='notice'>" + notInDBCount + " topics are not assigned to the edition</h3>";
					// }

					this.auditText += statusText;
					this.auditText += "</div>";

					if(callbackF) callbackF(notInDBCount, notAssignedCount);

					if(notInDBCount || notAssignedCount) return false;

					return true;
				});

			} else {
				this.log("No subjects found in the document.");
			}

		});
	}


	App.methods.proofread = (id)=>{
		window.open(Env.viewURL + id + "?proof=1");
	}



	App.methods.addImageAttrs = function(doc) {
		this.auditTitle = "Adding pb attributes for images for " + doc.filename;
		this.auditText = "";
		this.clearLog();

		if(doc.checked_out == "1"){
			this.auditText = "Can't edit elements for a document that is checked out.";
			this.showStatus = true;
			return;
		}

		Yodude.send(Env.baseURL + "document/addimageattrs?f=" + doc.filename).then((resp) => {
			if(resp.errors && resp.errors.length){
				this.logError(resp.errors);
				return false;
			}

			this.auditText = resp.data.messages;
			this.showStatus = true;
		});
	}



	
	/******************************************************************
		This function get's call by the vue app at the mounted 
		lifecycle	 

	 ******************************************************************/
	window.docManagerCustomizer = function(App){

		function PSCFormatDate(d){
			d = "" + d;//make a string
			let out = "";
			//already has dashes
			if(d.indexOf("-") > -1){
				out = d.substring(0, 10);
			} else {
				out = d.substring(0, 4) + "-" + d.substring(4, 6) + "-" + d.substring(6, 8);
			}
			return out;
		}


		let processSolrField = function(name, value){
			if(name =="date_when"){
				return PSCFormatDate(value);
			}
			if(name =="date_to"){
				return PSCFormatDate(value);
			}

			return value;
		}




	/******************************************************************
	
		Within docManagerCustomizer you can add functions in it's 
		scope (so, let x = function()) and then use 
		App.addAppEventListener to tie those functions to certain 
		moments in the apps action cycle:

			App.addAppEventListener("postUpload", ()=>{

			});

		Use the ()=>{} syntax for the 2nd argument, which is the listener.
		
		For more, see "how-to-customize.txt"

		******************************************************************/

		let interrupt = true;

		App.addAppEventListener("postUpload", (resp, continueCallback)=> {
			let filename = resp.data.filename;
			App.auditHuscs(filename, continueCallback);
		}, interrupt);

		App.addAppEventListener("parseReturnedSolrField", processSolrField);

		App.addAppEventListener("editDocument", (doc)=>{console.log(doc)});
		App.addAppEventListener("releaseDocument", (doc)=>{console.log(doc)});

		App.prePublishCheck = (doc) =>{
			App.auditSubjects(doc.filename, (notInDBCount, notAssignedCount) => {
				if(notAssignedCount || notInDBCount){
					return false;
				}

				App.showStatus = false;
				App.publishFinish(doc);
			});

		}

		

	} // end of docManagerCustomizer