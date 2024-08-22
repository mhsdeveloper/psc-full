/*

	This is the MACI, the Metadata API Command Interpreter,



	it looks for commands in the html and interprets them into calls 
	to the metadata api.


	

	use these <code>s to call for data and place the results:

	<code>documents-by-name:banyer-maria date title teaser 25</code>

		results in this api call: /mhs-api/ext/metadata?person_keyword=banyer-maria&configRows=25
		and tells JS to display each document's date, title, teaser in that order

		this also loads facets for subject and person_keyword, that other code's can use

	<code>project-names:</code>
	<code>project-subjects:</code>
	<code>subjects-by-name:banyer-maria</code>
	<code>names-by-name:banyer-maria</code>
	<code>name-card:banyer-maria</code>


	Here is the data structure that build by parsing the <codes>, in order to schedule just
	one ajax call per HUSC, and then get the data out to the proper viewBlocks

	this.calls = {
		banyerDASHmaria: 
		{
			type: "person_keyword",
			husc: "banyer-maria",
			count: 10, [10 is default]
			start: 0,
			data: null,
			viewBlocks: [
			]
		}
	];


	MORE INFO:

	viewBlock's originate from the text code in WP and are parsed in init() into viewBlocks,
	with this structure:
	{
		type: "[name|project_names|project_subjects|name_documents|name_subjects",
		fields: [field names in array],
		element: [DOM element where output will be rendered],
		teaserLength: [int max length from doc_beginning field in SOLR]

	}


	a "code" in the API is the set of instructions needed to add data to a <code>-based request
	a "call" in the API is a data object that makes one Ajax call to gather metadata and stores it,
	then calls it's viewBlocks to help them complete their display 


	USING WITHOUT THE WP <code> elements:

		call methods directly with a viewBlock object:

		let M = new MetaCI(MyMetaDISInstance);
		let husc = "sedgwick-catharine";
		let tokens = "";
		let viewBlock = {
			element: someDomDivContainer,
			callback: ()=> {} // only for calling M.addNameCard()
		}
		M.documentsByName(husc, tokens, viewBlock);
		M.run();



	CALLBACKS

	this.finalCallback  is called when all metadata is done being retrieves. a results object is passed to the callback.
*/

class MetaCI {
    
	constructor(renderer = null, autorun = true){
		this.renderer = renderer;
		this.codes = [];
		this.calls = {};
		this.callKeys = [];

		this.names = {}; //track all the various HUSCS that need fleshing out
		this.namesKeys = [];

		//these are elements that need names completion before they can be rendered
		this.callsWithNames = [];

		this.finishWithNames = true;
		this.currentCall = null;


		//track data returned to pass to calling view/app
		this.results = {docs: [], subjects: [], person_keywords: []}
		this.finalCallback = null;


		this.url = "/mhs-api/ext/";

		///names?huscs=adams-john;adams-abigail&fields=family_name;given_name;maiden_name;middle_name
		this.namesUrl = "/mhs-api/ext/names?";

		this.tokensFieldsMapping = {
			"date": "date_when",
			"title": "title",
			"teaser": "doc_beginning"
		}

		this.huscCommands = ["documents-by-name", "subjects-by-name", "name-card", "names-by-name"];
		this.defaultDocumentFields = ["date_when", "title", "doc_beginning"];

		this.errorHandler = null;
		if(autorun) document.addEventListener("DOMContentLoaded", ()=>{this.init();});
	}




	/* general prep; gather all markers
	*/
	init(){
		this.codes = document.getElementsByTagName("code");
		//sort our codes
		for(let i=0;i<this.codes.length;i++){
			//skip the code for the searchbox
			if(this.codes[i].textContent.includes("searchbox")) continue;

			let viewBlock = {};
		
			let par = this.codes[i].parentNode;
			par.classList.add("hasCode");

			//create container
			let div = document.createElement("div");
			div.className = "metadataSet";
			this.codes[i].after(div);

			viewBlock.element = div;
			let raw = this.codes[i].textContent;
			//clean out
			raw = raw.toLowerCase();
			raw = raw.replace(/[^a-z0-9\:\-]/g, " "); //fix other chars
			raw = raw.replace(/\s\s+/g, " "); //consolidate spaces
			raw = raw.replace(": ", ":");//fix space between husc and command
			raw = raw.trim();
			//skip if 
			if(raw.indexOf(":") == -1) continue;

			//get command and person
			let tokens = raw.split(" ");
			let lead = tokens[0].split(":");

			if(this.huscCommands.includes(lead[0]) && typeof lead[1] == "undefined") {
				console.log("Command does not specific an input HUSC after the command: " + raw);
				return;
			}

			switch(lead[0]){
				case "project-names": this.projectNames(tokens, viewBlock); break;
				case "project-subjects": this.projectSubjects(tokens, viewBlock); break;
				case "documents-by-name": this.documentsByName(lead[1], tokens, viewBlock); break;
				case "subjects-by-name": this.subjectsByName(lead[1], tokens, viewBlock); break;
				case "name-card": this.addNameCard(lead[1], tokens, viewBlock); break;
				case "names-by-name": break;
			}
			
		}

		this.run();		
	}




	projectNames(tokens, viewBlock){
		if(!this.calls.projectFacets){
			this.calls.projectFacets = {type: "project_facets", data: null, count: 50, start: 0, viewBlocks: []}
		}
		viewBlock.type = "project_names";
		//add this <code> itself as a viewBlock of the data entry
		this.calls.projectFacets.viewBlocks.push(viewBlock);
	}



	projectSubjects(tokens, viewBlock){
		if(!this.calls.projectFacets){
			this.calls.projectFacets = {type: "project_facets", data: null, count: 1, start: 0, viewBlocks: []}
		}
		viewBlock.type = "project_subjects";
		this.calls.projectFacets.viewBlocks.push(viewBlock);
	}



	documentsByName(HUSC, tokens, viewBlock){
		this.addNameHusc(HUSC);

		//first make sure we have a data entry for this one
		let key = this.formDataEntryKey(HUSC);

		//setup viewBlock
		viewBlock.type = "name_documents";

		//go through tokens to store order of fields
		viewBlock.fields = [];
		for(let t of tokens){
			if(this.tokensFieldsMapping[t]){
				viewBlock.fields.push(this.tokensFieldsMapping[t]);
			} else if(t.indexOf("teaser") == 0){
				viewBlock.fields.push(this.tokensFieldsMapping["teaser"]);
				//try to find int of chars after "teaser"
				let str = t.replace(/[^0-9]/g, "");
				let i = parseInt(str);
				if(Number.isInteger(i)) viewBlock.teaserLength = i;
				else viewBlock.teaserLength = -1;
			}
		}
		//set defaults if none
		if(viewBlock.fields.length == 0){
			viewBlock.fields = this.defaultDocumentFields.splice(0);
		}

		this.addMetadataCall(key, HUSC, tokens, viewBlock);
	}




	subjectsByName(HUSC, tokens, viewBlock){
		this.addNameHusc(HUSC);

		//first make sure we have a data entry for this one
		let key = this.formDataEntryKey(HUSC);
		//setup viewBlock
		viewBlock.type = "name_subjects";

		this.addMetadataCall(key, HUSC, tokens, viewBlock);
	}



	addNameCard(HUSC, tokens, viewBlock){
		this.addNameHusc(HUSC);
		let key = "nameDASH" + this.formDataEntryKey(HUSC);
		//setup viewBlock
		viewBlock.type = "name";
		this.calls[key] = {type: "name", husc: HUSC, data: null, count: 1, start: 0, viewBlocks: [viewBlock]};
	}


	addNameHusc(HUSC){
		if(!this.names[HUSC]) this.names[HUSC] = {};
	}



	//both subjectsByName and documentsByName depend on the same metadata call,
	//which is to facet on an initial search that limitss to person_keyword = [some HUSC]
	//thus both those functions call this one
	addMetadataCall(key, HUSC, tokens, viewBlock){
		if(!this.calls[key]){
			this.calls[key] = {type: "person_keyword", husc: HUSC, data: null, count: 10, start: 0, viewBlocks: []}
			//tokens are the words in the <code> html command
			this.calls[key].count = this.findTokenCount(tokens);
			this.findFlags(this.calls[key], tokens);
		}

		//add this code itself as a viewBlock of the data entry
		this.calls[key].viewBlocks.push(viewBlock);
	}




	formDataEntryKey(raw){
		let key = raw.replace(/\-/g, "DASH");
		return key;
	}




	findTokenCount(tokens){
		for(let token of tokens){
			let i = parseInt(token);
			if(Number.isInteger(i)) return i;
		}
		return 10;
	}




	findFlags(call, tokens){
		let flags = {};
		for(let i=0;i<tokens.length; i++){
			if(tokens[i] == "allprojects" || tokens[i] == "allProjects"){
				call.allProjects = true;
			}
		}

		return flags;
	}




	/* run through calls and get data
	*/
	run(){
		if(this.renderer) this.renderer.learnNames(this.names);
		this.startMetadataCalls();
	}




	getNames(){
		this.namesKeys = Object.keys(this.names);
		if(this.namesKeys.length == 0){
			this.startRenderer();
			return;
		}

		let huscs = this.namesKeys.join(";");
		let url = this.namesUrl + "huscs=" + huscs + "&fields=family_name;given_name;maiden_name;middle_name;date_of_birth;date_of_death";

		//first gather HUSC completions
		this.send(url).then((resp) => {
			if(resp == null){
				resp = {}
				resp.errors = ["Unable to connect to names database. Please contact the web developer."];
				if(this.errorHandler) this.errorHandler(resp);
			} else if(typeof resp.errors != "undefined" && resp.errors.length){
				if(this.errorHandler) this.errorHandler(resp);
			} else {
				this.completeNames(resp);
			}
		});
	}




	completeNames(resp){
		let names = Object.keys(resp);
		if(names.length == 0) return;
		for(let husc of names){
			if(this.names[husc]){
				this.deepCopyName(resp[husc], this.names[husc]);
			}
		}

		this.startRenderer();
	}




	deepCopyName(src, dest){
		let parts = Object.keys(src);
		for(let i=0;i<parts.length; i++){
			dest[parts[i]] = src[parts[i]];
		}
	}



	startRenderer(){
		if(this.renderer) for(let key of this.callKeys){
			this.renderer.renderViewBlocks(this.calls[key].data, this.calls[key].viewBlocks);
		}
		if(this.finalCallback) this.finalCallback(this.results);
	}




	startMetadataCalls(){
		this.callKeys = Object.keys(this.calls);
		this.callIndex = -1;
		this.makeNextCall();
	}




	makeNextCall(){
		this.callIndex++;
		if(this.callIndex >= this.callKeys.length){
			this.getNames();
			return;
		}

		this.currentCall = this.calls[this.callKeys[this.callIndex]];

		//build url based on type
		let url = this.url;
		switch(this.currentCall.type){
			case "project_facets":
				url += "metadata?"
				break;

			case "person_keyword":
				url += "metadata?person_keyword=" + this.currentCall.husc;
				break;
			
			case "name":
				url += "name?husc=" + this.currentCall.husc;
				break;

		}

		url += "&configStart=" + this.currentCall.start;
		url += "&configRows=" + this.currentCall.count;

		if(!this.currentCall.allProjects){
			url += "&project=" + Env.projectShortname;
		}
		this.callSolr(url);
	}




	callSolr(url){
console.log("METACI solr call");
console.log(url);
		// we keep the actual asynchronous part in another function so that
		// we can to a call()then.() type syntax
		this.send(url).then((resp) => {
			if(resp == null){
				resp = {}
				resp.errors = ["Unable to connect to SOLR search engine. Please contact the web developer."];
				if(this.errorHandler) this.errorHandler(resp);
			} else if(typeof resp.errors != "undefined" && resp.errors.length){
				this.responseObj = resp;
				if(this.errorHandler) this.errorHandler(resp);
			} else {
				this.responseObj = resp;
				this.parseResponse(resp);
			}
		});
	}




	parseResponse(resp){
		this.currentCall.data = resp;

		let viewBlocks = this.currentCall.viewBlocks;
		let data = this.currentCall.data;

		//populate totals tracking
		if(data.response && data.response.docs){
			for(let d of data.response.docs){
				this.results.docs.push(d);
			}
		}
		if(data.facet_counts && data.facet_counts.facet_fields && data.facet_counts.facet_fields.person_keyword){
			for(let p of data.facet_counts.facet_fields.person_keyword) this.results.person_keywords.push(p);
		}
		if(data.facet_counts && data.facet_counts.facet_fields && data.facet_counts.facet_fields.subject){
			for(let p of data.facet_counts.facet_fields.subject) this.results.subjects.push(p);
		}

		this.addNamesFromData(data, this.currentCall.count);
		this.makeNextCall();
	}




	addNamesFromData(data, count){
		if(!data || !data.facet_counts || !data.facet_counts.facet_fields || !data.facet_counts.facet_fields.person_keyword){
			return;
		}

		//iterate by twos, because SOLR alters the persons with the 
		for(let i=0; i < count * 2; i += 2){
			let person = data.facet_counts.facet_fields.person_keyword[i];
			if(!person) break;
			this.addNameHusc(person);
		}
	}




	async send(url){
		let pack = {
			method: "GET", mode: 'cors', cache: 'no-cache',	credentials: 'same-origin',
			headers: {  'Content-Type': 'application/json'	}
		}

		try {
			let response = await fetch(url, pack);		
			if (!response.ok) {
				let msg = `HTTP error! status: ${response.status}`;
				console.log(msg);
				return false;
			} else {
				return await response.json();
			}
		} catch(err){
			console.log(err);
			return {};
		}
	}

}