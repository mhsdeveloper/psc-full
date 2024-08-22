class Searcher {
	
	constructor(vueApp){
		this.vueApp = vueApp;
		this.start = 0;

		this.mode = "extra"; // "search" for when SOLR builds Doc list, "extra" when getting metadata to augment DB list of docs

		this.filenames = []; //simple set of filenames to send to server to retrieve corresponding DB workflow Data
		this.files2docs = {}; //use filenames as keys to hold array place to the docs in the results array
								// so that we can do things like vueapp.documents[this.file2docs['CMS1922-01-1.xml']];

		this.Solr = new Solr();

		/* Env has the solrSetup, but only those things that can change per install.
		   Here we wire in the callbacks
		*/
		Env.solrSetup.responseHandler = (resp) => {this.searchResponse(resp)};// (resp) => {	}

		Env.solrSetup.hashUpdateCallback = () => {
			this.vueApp.fields = this.Solr.fields;
		}

		Env.solrSetup.errorHandler = (resp) => {
			vueApp.warnSolrError = true;
		}

		this.Solr.setup(Env.solrSetup);
	}



	getDocuments(start = 0){
		//see if we need to search SOLR first
		let keys = Object.keys(this.vueApp.searchSolrFields);
		for(let field of keys){
			if(this.vueApp.searchSolrFields[field].length){
				//first switch over to SOLR-first, so reset start
				if(this.mode != "search"){
					this.vueApp.docPage = 1;
					start = 0;
					this.start = 0;
				}
				return this.search(start);
			}
		}

		this.getDBDocuments(start);
	}


	getDBDocuments(start = 0){

		this.start = start;

		this.dbDocs = [];
		let data = {
			filename: this.vueApp.doclistFields.filename,
			user: this.vueApp.doclistFields.user,
			order: this.vueApp.doclistOrder,
			dir: this.vueApp.order,
			start: start};

		Yodude.send(Env.baseURL + "documents", data).then((resp) => {
			//add place holder data
			let filenames = [];
			if(!resp.data.docs || resp.data.docs.length == 0) return;

			this.dbDocs = resp.data.docs;
			this.vueApp.docHits = parseInt(resp.data.total);
			let total = Math.ceil(resp.data.total / this.vueApp.docsPerPage);

			this.vueApp.docPageTotal = total;
			this.vueApp.docStart = parseInt(resp.data.start) + 1;

			for(let i=0;i<resp.data.docs.length;i++){
				resp.data.docs[i].date_when = "[Loading date...]";
				filenames.push(resp.data.docs[i].filename);
			}
			this.gatherExtraDocData(filenames);
		});
	}



	search(start = 0) {
		this.start = start;
		this.mode = "search";
		//remove filename limit
		this.vueApp.fields.filename = "*" + this.vueApp.doclistFields.filename + "*";
		this.Solr.setFields(this.vueApp.fields);

		this.Solr.addFields(this.vueApp.searchSolrFields, true, "*", "*");
		this.Solr.rows = 5000; //let's get a lot more rows incase we're dealing with xml that has multiple docs per file
		this.Solr.groupBy(null);
		this.Solr.search(start);
	}


	gatherExtraDocData(filenames){
		this.mode = "extra";
//		this.Solr.rows = this.vueApp.docsPerPage;
		this.Solr.setFields({filename: filenames.join(" ")});
		this.vueApp.documents = [];
		this.Solr.search();
	}


	async searchResponse(resp){
		if(resp.error){
			this.vueApp.warnSolrError = true;
		}
		
		if((!resp.response || !resp.response.docs) && !resp.grouped) return;

		if(this.mode == "search"){

			let docs = resp.response.docs;
			let filenames = [];

			//build filenames and query DB for DB fields for these files
			for(let doc of docs){
				//BUILD UNIQUE FILESNAMES (because diaries might have many results but fewer filenames)
				if(!filenames.includes(doc.filename)) filenames.push(doc.filename);
			}
			this.vueApp.docPageTotal = Math.ceil(filenames.length / this.vueApp.docsPerPage);

			//copy to page version of filenames
			let pageOfFilenames = [];
			for(let i=0;i<this.vueApp.docsPerPage; i++){
				if(typeof filenames[this.start + i] == 'undefined') break;
				pageOfFilenames.push(filenames[this.start + i]);
			}

			let data = {
				f: pageOfFilenames.join("|"),
				user: this.vueApp.doclistFields.user,
				order: this.vueApp.doclistOrder,
				dir: this.vueApp.order,
				start: 0
			};
	
			let dbresp =  await Yodude.send(Env.baseURL + "documents", data);

			this.dbDocs = dbresp.data.docs;

			this.mapSolrToDocs(docs);

			this.vueApp.documents = this.dbDocs;
			
		} else {
			//copy data to let SOLR obj keep it's own intact
			let docs = JSON.parse(JSON.stringify(resp.response.docs));
			this.mapSolrToDocs(docs);
			this.vueApp.documents = this.dbDocs;
		}
	}


	mapSolrToDocs(docs){
		//map solr info onto
		for(let i=0;i<this.dbDocs.length; i++){
			for(let j = docs.length -1; j > -1; j--){
				if(docs[j].filename == this.dbDocs[i].filename){
					//copy params
					let keys = Object.keys(docs[j]);
					for(let k=0;k<keys.length;k++){
						let key = keys[k];
						let outKey = key;
						//but don't map the id!
						if(key == "id") outKey = "xmlid";
						if(key == "filename") continue;

						let val = docs[j][key];

						this.dbDocs[i].linksOpen = 'closed';
						this.dbDocs[i].proofLinksOpen = 'closed';

						for(let i=0;i<this.vueApp.appEvents.parseReturnedSolrField.length;i++){
							let l = this.vueApp.appEvents.parseReturnedSolrField[i];
							val = l.func(key, val);
						}

						//make sure date_when is earliest, incase we have multiple docs per filename (diaries, for example)
						if(key == "date_when"){
							if(!this.dbDocs[i][outKey]) this.dbDocs[i][outKey] = val;
							else if(this.dbDocs[i][outKey] && val < this.dbDocs[i][outKey]) this.dbDocs[i][outKey] = val;
						}

						else if(key == "date_to") {
							if(!this.dbDocs[i]["date_to"]) this.dbDocs[i][outKey] = val;
							else if(this.dbDocs[i]["date_to"] && val > this.dbDocs[i]["date_to"]) this.dbDocs[i]["date_to"] = val;

						} else {
							if(!this.dbDocs[i][outKey]) this.dbDocs[i][outKey] = val;

							else {
								if(Array.isArray(val)){
									if(Array.isArray(this.dbDocs[i][outKey])){
										this.dbDocs[i][outKey] = this.dbDocs[i][outKey].concat(val);
									} else {
										val.push(this.dbDocs[i][outKey]);
										this.dbDocs[i][outKey] = val;
									}
								} else {
									if(Array.isArray(this.dbDocs[i][outKey])){
										this.dbDocs[i][outKey].unshift(val);
									} else {
										this.dbDocs[i][outKey] = [this.dbDocs[i][outKey], val];
									}
								}
							}

							//prune any duplicates if we have an array
							if(Array.isArray(this.dbDocs[i][outKey])){
								this.dbDocs[i][outKey] = [...new Set(this.dbDocs[i][outKey])];
							}
						}
					}
					docs.splice(j, 1); //remove found items;
					// break; don't break, because there will more matches for some doc types, like diary entries
				}
			}
		}
	}



	getWorkflowAfter(docs){
		//grab filenames
		for(let i=0;i<docs.length; i++){
			this.filenames.push(docs[i].filename); //ad to filename list for getting workflow from db
		}
		let filenames = this.filenames.join("|");

		Yodude.send(Env.baseURL + "documents?f=" + filenames).then((resp) => {
			this.updateSolrToVue(docs, resp.data.docs);
		});
	}


	XXXXXupdateSolrToVue(docs, workflow = []){
		let documents = [];
		for(let i=0;i<docs.length; i++){
			let doc = this.newDocListing();

			//map all solr fields to doc props
			let keys = Object.keys(docs[i]);
			for(let key of keys){
				//look for highlighting, which is special
				if(key == "highlighting"){
					if(docs[i].highlighting && docs[i].highlighting[0]) doc.context = docs[i].highlighting[0];
					else doc.context = "";
				} else if(key == "filename"){
					//need this list because need to copy primitive datatype to vue object props
					doc.filename = docs[i].filename;
					//also map filename to index
					this.files2docs[doc.filename] = i;
					this.filenames.push(doc.filename); //ad to filename list for getting workflow from db
				} else {
					doc[key] = docs[i][key];
				}

				//honor the customized config of eventlisteners, in customize-frontend/scripts.js
				for(let i=0;i<this.vueApp.appEvents.parseReturnedSolrField.length;i++){
					let l = this.vueApp.appEvents.parseReturnedSolrField[i];
					doc[key] = l.func(key, docs[i][key]);
				}
			}

			documents.push(doc);
		}

		//add status column
		for(let i=0;i<workflow.length; i++){
			let doc = workflow[i];
			let props = Object.keys(doc);

			//look for a search result with this filename
			//files2docs holds the search result file order (array indx) by filename, so ...
			let indx = this.files2docs[doc.filename];
			// ... we can see if there is an existing search result that matches the filenames
			// we just got from the workflow db
			if(typeof indx == "undefined") continue;
			// and double check that our array of results has that index
			if(typeof documents[indx] == "undefined") continue;
			
			//fix int that are strings in db
			doc['checked_out'] = parseInt(doc['checked_out']);
			//map mysql col props to our documents
			for(let prop of props){
				documents[indx][prop] = doc[prop];
			}
		}

		this.vueApp.documents = documents;
	}




	/* this returns an empty object with defaults that indicating loading etc.
		in this way, we can have SOLR load and pop-in the metadata, but MYSQL can grab
		workflow later.
	*/
	newDocListing(){
		return {
			published: -1,
			checked_out: -1,
			checked_outin_by: -1,
			checked_outin_date: -1,
			filename: -1,
			doc_beginning: -1,
			resource_uri_start: -1,
			person_keyword: -1
		}
	}
}
