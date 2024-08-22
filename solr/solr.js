/*

	see /project/api/js folder for example config json files


	//example setup to pass to constructor:

			var solrSetup =	{
				url: "/projects/api/index.php/solrsearch",
				configURL: "/projects/api/js/solr-config.json",

				highlightingFields: ["text_merge"],

				responseHandler: (resp) => {
					console.log(resp);
					let html = Searcher.createPagination();
				}
			}

	//URL hash format:
		http://www.someplace.com/searchpage#start/num/field/value/field/value etc.

*/

class Solr {
	
	constructor() {
	}
	
	setup(config = {}){
		this.config = config;
		this.start = 0;
		this.responseObj = {};
		this.status = {
			ok: "OK",
			failed: "FAILED"
		}
		this.fields = {}

		this.groupField = "";
		this.grouping = false;

		this.prevSearchString = "";


		//let's define our default configuration
		let defaults = {
			url: "/projects/api/index.php/solrsearch",
			configURL: "/projects/api/js/solr-config.json",
			highlightingFields: ["text"],
			prevPageLabel: "prev",
			nextPageLabel: "next",
			resultsElement: null,
			rows: 10,
			paginationElement: null,
			responseHandler: (resp) => {
				console.log(resp.response);
			},
			hashUpdateCallback: null,
			errorHandler: (resp) => {
				console.log(resp);
			},
			httpErrorHandler: (resp) => {
				console.log(resp);
			},
			trackHash: true
		}
		let keys = Object.keys(defaults);
		for(let key of keys){
			if(typeof this.config[key] == "undefined"){
				this.config[key] = defaults[key];
			}
		}

		this.rows = this.config.rows;

		//prep highlight mapping
		this.hikeys = [];
		for(let i=0;i<this.config.highlightingFields.length; i++){
			this.hikeys.push(this.config.highlightingFields[i]);
		}

		return this.readHash();
	}


	createPagination(){
		let numFound = this.responseObj.response.numFound;
		let pages = Math.ceil(numFound / this.rows);
		let page = Math.floor(this.start / this.rows);

		let wrapper = document.createElement("div");
		wrapper.className = "pagination";

		//prev button
		if(this.start > 0){
			let div = this.createButton(page - 1, this.config.prevPageLabel);
			div.classList.add("prev");
			wrapper.appendChild(div);
		}

		//always show page 1
		let div = this.createButton(0);
		wrapper.appendChild(div);

		//simply list all pages
		if(pages < 14){
			for(let i=1;i<pages;i++){
				let div = this.createButton(i);
				wrapper.appendChild(div);
			}
		}

		//something more complex...
		else {
			let maxButtons = 10;
			let pageButtons = 0;
				
			if(numFound > this.rows){

				//if further along in list, provide context of pages before
				if(page > 5){
					let a = document.createElement("a");
					a.classList.add("page");
					a.innerHTML = "...";
					wrapper.appendChild(a);

					// let div = this.createButton(page);
					// wrapper.appendChild(div);

					for(let i= page - 1;i<pages;i++){
						let div = this.createButton(i);
						wrapper.appendChild(div);
						pageButtons++;
						if(pageButtons == maxButtons) break;
					}
				}

				//simply the first ten pages
				else {
					for(let i=1;i<pages;i++){
						let div = this.createButton(i);
						wrapper.appendChild(div);
						pageButtons++;
						if(pageButtons == maxButtons) break;
					}
				}

			}

		}

		//next button
		if(page + 1 < pages){
			div = this.createButton(page + 1, this.config.nextPageLabel);
			div.classList.add("next");
			wrapper.appendChild(div);
		}

		wrapper.addEventListener('click', (e) => {this.paginationClick(e);});

		if(this.config.paginationElement){
			this.config.paginationElement.innerHTML = "";
			this.config.paginationElement.appendChild(wrapper);
		}

		return wrapper;
	}


	createButton(page, label = ""){
		let a = document.createElement("a");
		a.classList.add("page");
		let start = page * this.rows;
		a.title = start;
		if(start != this.start) a.href = "#";
		else {
			a.classList.add("currentPage");
		}
		if(label.length) a.innerHTML = label;
		else a.innerHTML = page  + 1;
		a.setAttribute("data-solr-start", start);
		return a;
	}


	groupBy(field = null, count = 3){
		if(!field || field.length === 0){
			this.grouping = false;
			return;
		}

		this.groupField = field;
		this.groupCount = count;
		this.grouping = true;
	}


	createURL(){
		let fields = Object.keys(this.fields);
		let outFields = {};
		for(let field of fields){
			if(this.fields[field] == "") continue;
			outFields[field] = this.fields[field];
		}
		let data = JSON.parse(JSON.stringify(outFields));
		if(this.config.configURL.length) data.configURL = this.config.configURL;
		data.configStart = this.start;
		data.configRows = this.rows;
		let parts = this.prepGet(data);
		let url = this.config.url + parts;
		if(this.grouping){
			url += "&groupField=" + this.groupField + "&groupCount=" + this.groupCount;
		}
		return url;
	}



	paginationClick(e){
		let start = e.target.getAttribute("data-solr-start");
		if(!start) return;
		e.preventDefault();
		e.stopPropagation();
		this.search(start);
	}

	
	search(start = 0){
		this.start = start;
		this.updateHash();
		this.callSolr();
	}

	
	async send(url){
		if(!url) url = this.createURL();
		this.sentURL = url;

		let pack = {
			method: "GET", mode: 'cors', cache: 'no-cache',	credentials: 'same-origin',
			headers: {  'Content-Type': 'application/json'	}
		}

		try {
			let response = await fetch(url, pack);		
			if (!response.ok) {
				let msg = `HTTP error! status: ${response.status}`;
				if(this.config.httpErrorHandler) this.config.httpErrorHandler(response);
				return false;
			} else {
				return await response.json();
			}
		} catch(err){
			console.log(err);
			return {};
		}
	}


	callSolr(){
		this.send().then((resp) => {
			//store url this app sent
			resp.solrJSurl = this.sentURL;

			if(resp == null){
				resp = {}
				resp.errors = ["Unable to connect to SOLR search engine. Please contact the web developer."];
				if(this.config.errorHandler) this.config.errorHandler(resp);
			} else if(typeof resp.errors != "undefined" && resp.errors.length){
				this.responseObj = resp;
				if(this.config.errorHandler) this.config.errorHandler(resp);
			} else {
				this.responseObj = resp;
				this.parseResponse(resp);
//why was this here???!!!		if(this.config.responseHandler) this.config.responseHandler(resp);
			}
		});
	}

	parseResponse(resp){
		//add highlighting to each document so display can simply iterate over docs
		if(resp.highlighting){
			let hiids = Object.keys(resp.highlighting);

			if(resp.grouped){
				let groupNames = Object.keys(resp.grouped);
				for(let groupName of groupNames){
					for(let group of resp.grouped[groupName].groups){
						for(let doc of group.doclist.docs){
							this.moveHighlightToDoc(hiids, resp.highlighting, doc);
						}
					}
				}
			}

			else {
				//add text to docs
				for(let i=0;i< resp.response.docs.length;i++){
					if(resp.response.docs[i].highlighting) continue; //this avoids double parsing
					this.moveHighlightToDoc(hiids, resp.highlighting, resp.response.docs[i]);
				}
			}
		}
		if(this.config.responseHandler) this.config.responseHandler(resp);
	}

	moveHighlightToDoc(hiids, highlighting, doc){
		doc.highlighting = [];
		let indx = hiids.indexOf(doc.id);
		if(indx > -1){
			for(let h=0;h<this.hikeys.length; h++){
				let hirow = highlighting[hiids[indx]][this.hikeys[h]];
				if(!hirow) {
//					console.log("No highlighting for field: " + this.hikeys[h]);
				} else {
					doc.highlighting.push(highlighting[hiids[indx]][this.hikeys[h]].join(" "));
				}
			}
		} else {
			console.log("didn't find hilight with id: " + doc.id);
		}
	}


	//better grammar: you're not adding fields, you're setting all of them
	setFields(fields){
		this.addFields(fields);
	}
	
	addFields(fields, keepExisting = false, leadingWildcard = "", trailingWildcard = ""){
		if(!keepExisting){
			this.fields = JSON.parse(JSON.stringify(fields));
		} else {
			let keys = Object.keys(fields);
			for(let field of keys){
				this.fields[field] = leadingWildcard + fields[field] + trailingWildcard;
			}
		}
	}


	prepGet(data){
		//encode data into url for get requests
		let str="?";
		
		let keys = Object.keys(data);
		for(let i=0;i<keys.length; i++){
			if(i>0) str += "&";
			str += encodeURIComponent(keys[i]);
			str += "=";
			str += encodeURIComponent(data[keys[i]]);
		}

		return str;
	}


	readHash(force = false){
		if(!force && this.config.trackHash == false){
			return false;			
		}
		//no hash
		if(location.href.indexOf("#") == -1) return false;
		let raw = location.href.split("#")[1];

		let set = this.fieldsFromHashString(raw);
		this.start = set.start;
		this.addFields(set.fields);
		this.rows = set.rows;
		this.callSolr();
		return true;
	}


	fieldsFromHashString(str){
		let parts = str.split("/");
		//must have start [0], num [1], and a field an value
		if(parts.length < 4) return false;
		let start = parts.shift();
		if(start == "null") start = 0;
		let rows = parts.shift();
		let fields = {};
		while(parts.length){
			let field = parts.shift();
			let val =decodeURIComponent( parts.shift());
			val = val.replace(/\+/, " ");
			fields[field] = val;
		}
		return {fields: fields, start: start, rows: rows};
	}


	updateHash(){
			
		let url = location.href.split("#")[0];
		url += "#" + this.start + "/" + this.rows;
		let keys = Object.keys(this.fields);
		for(let i=0;i<keys.length; i++){
			url += "/" + keys[i] + "/" + this.fields[keys[i]];
		}

		location.href = url;
		if(this.config.hashUpdateCallback) this.config.hashUpdateCallback(this);
	}
}




