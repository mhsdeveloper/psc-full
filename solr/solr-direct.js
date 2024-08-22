/*


   




*/

class SolrDirect {
	
	constructor() {
		this.queryObjects = [];
		this.responseObj = {};
		this.status = {
			ok: "OK",
			failed: "FAILED"
		}

		this.prevSearchString = "";

		//settings that are not in config because UI might want to change them more easily via method calls
		this.groupingField = ""; //empty => no grouping
		this.groupLimit = 10;
		this.highlightField = ""; // empty => no highlighting
		this.start = 0;
		this.rows = 20;
		this.sortField = "";
		this.sortDir = "asc";
		this.facetFields = [];
		this.returnFields = [];
	}
	
	setup(config = {}){
		this.config = config;
		//let's define our default configuration
		let defaults = {
			url: "",
			localStoragePrefix: "SOLRSEARCH-",

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

		if(this.config.url.length === 0) {
			console.log("YO! you need to pass solr-direct.js an obj with a url to ajax to.");
		}

		return this.readHash();
	}





	/*
		query objects are the individual parts of the q= sent to SOLR:
		always a field:value or field:(value value ...)
	*/

	newQueryObject(){
		let obj = {
			andOr: "and", // this along with order and context implies ...
			field: "text_merge",
			terms: "", //a.k.a the value of the field searching on
			display: "", //an alternative for when the UI wants to show value different from actual query value
			rangeStart: "",
			rangeEnd: "",
			requireAllTerms: false,
			isPhrase: false, // make sure to handle quotes within the value itself
		}

		return obj;
	}




	groupBy(field, limit = 10){
		this.groupingField = field;
		this.groupLimit = limit;
	}

	setHighlightField(field = 'text_merge'){
		this.highlightField = field;
	}

	addFacetFields(fields){
		if(!Array.isArray(fields)) fields = [fields];

		for(let f of fields){
			if(!this.facetFields.includes(f)) this.facetFields.push(f);
		}
	}

	addReturnFields(fields){
		if(!Array.isArray(fields)) fields = [fields];

		for(let f of fields){
			if(!this.returnFields.includes(f)) this.returnFields.push(f);
		}
	}

	setSort(field, dir = "asc"){
		this.sortField = field;
		this.sortDir = dir;
	}


	createQueryString(){
		let inOrGroup = false;
		let str = "q=";

		let objCount = this.queryObjects.length;

		for(let i=0; i< objCount; i++){
			let obj = this.queryObjects[i];

			if(obj.terms.length == 0 && obj.rangeStart == 0 && obj.rangeEnd == 0) continue;

			//next obj
			let next = this.queryObjects[i + 1];

			//look ahead: if not in an OR group and following is an OR, start group
			if(!inOrGroup && next && next.andOr == "or"){
				inOrGroup = true;
				//something in the or group will be required, so +
				str += "+{";
			}

			else if(inOrGroup) ;//already in orGroup, so no +

			//not in orGroup, so an AND, i.e. required
			else if(!inOrGroup) str += "+";

			//build field:value pair
			str += obj.field + ":";

			//ranges first
			if(obj.rangeStart.length || obj.rangeEnd.length){
				str += "[" + obj.rangeStart + " TO " + obj.rangeEnd + "]";
			}

			else if(obj.isPhrase){
				str += '"' + obj.terms + '"';
			} 
			else if(obj.terms.includes(" ")){
				//require each word
				//fix extra spaces
				obj.terms = obj.terms.replace(/\s\s+/g, " ");
				//obj.terms = obj.terms.replace(/ /g, " +");
				str += "{" + obj.terms + "}";
			} 
			else str += obj.terms;

			//this is last
			if(!next){
				if(inOrGroup) str += "}";
			} else {
				//close a current or group
				if(inOrGroup && next.andOr == "and"){
					str += "}";
					inOrGroup = false;
				}

				//space between each group
				str += " ";
			}
		}

		return str;
	}

	
	search(){
		let str = this.createQueryString();
		str = "query=" + encodeURIComponent(str);

		str += "&rows=" + this.rows;
		str += "&start=" + this.start;

		if(this.groupingField.length){
			str += "&group=true&group.field=" + this.groupingField + "&group.limit=" + this.groupLimit;
		}

		if(this.highlightField.length){
			str += "&hl=true&hl.fl=" + this.highlightField;
		}

		if(this.sortField.length) str += "&sort=" + this.sortField + "%20" + this.sortDir;

		if(this.facetFields.length){
			str += "&ff=" + this.facetFields.join(";");
		}

		if(this.returnFields.length){
			str += "&fl=" + this.returnFields.join("%20");
		}

		this.callSolr(this.config.url, str);
	}


	callSolr(url = null, queryString = null){
		if(!url) url = this.config.url;
		if(!queryString) queryString = this.createQueryString();

		url += "?" + queryString;
		this.sentURL = url;

		this.send(url).then((resp) => {
			//store url this app sent
			resp.solrJSurl = this.sentURL;
			if(this.config.trackHash) this.updateHash(this.sentURL);

			if(resp == null){
				resp = {}
				resp.errors = ["Unable to connect to SOLR search engine. Please contact the web developer."];
				if(this.config.errorHandler) this.config.errorHandler(resp);
			} else if(typeof resp.errors != "undefined" && resp.errors.length){
				this.responseObj = resp;
				if(this.config.errorHandler) this.config.errorHandler(resp);
			} else {
				this.responseObj = resp;
				if(this.config.responseHandler) this.config.responseHandler(resp);
			}
		});
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




	/* OUTBOUND DATA: preparing the query String

	*/



	/* the hash is what the client-side uses to store the search state:

		@param q is the same as for SOLR exactly EXCEPT & is replaced by |
				server-side should adjust it to	add require fields or check for users messing around
		
				q=+index:rbt +(subject:"Presidential Election" subject:"Elections")|hl.fields=text_merge

	*/

	readHash(hash = ""){
		//no hash
		if(hash.length === 0){
			if(location.href.indexOf("#") == -1) return false;
			hash = location.href.split("#")[1];
		}

		hash = decodeURIComponent(hash);

		let paramStrings = hash.split("|");

		this.queryObjects = [];

		let currentQueryObject = null;

		let nextIsOr = false;

		let completeObject = (obj) => {
			//look for +s in terms
			if(obj.terms.includes("+")){
				obj.terms = obj.terms.replace(/\+/g, "");
				obj.requireAllTerms = true;
			}
			
			if(obj.terms.includes("{") || obj.terms.includes("}")){
				obj.terms = obj.terms.replace(/[\{\}]/g, "");
			}

			if(obj.terms.includes('"')){
				obj.terms = obj.terms.replace(/"/g, '');
				obj.isPhrase = true;
			}

			//parse any ranges
			if(obj.terms.includes("[")){
				let temp = obj.terms.replace(/[\[\]]/g, "");
				let parts = temp.split(" TO ");
				obj.rangeStart = parts[0];
				obj.rangeEnd = parts[1];
				obj.terms = "";
			}

			obj.display = obj.terms;
			this.queryObjects.push(obj);
			obj = null;
		}

		for(let str of paramStrings){
			//parse the main query sets
			if(str.indexOf("q=") === 0){

				let parts = str.split("q=")[1].split(" ");

				for(let part of parts){

					if(part.length === 0) continue;

					//new field/value pair
					if(part.includes(":")){

						//first add existing element
						if(currentQueryObject !== null) completeObject(currentQueryObject);

						currentQueryObject = this.newQueryObject();

						if(part[0] === "+"){
							currentQueryObject.andOr = "and";
							part = part.substring(1);
						} else {
							currentQueryObject.andOr = "or";
						}

						//but if starts with (, then following group is AND with what came before
						if(part[0] === "{"){
							currentQueryObject.andOr = "and";
							part = part.substring(1);
							nextIsOr = true;
						}

						else if(nextIsOr){
							nextIsOr = false;
							currentQueryObject.andOr = "or";
						}

						let pairs = part.split(":");
						currentQueryObject.field = pairs[0];
						currentQueryObject.terms = pairs[1];
						currentQueryObject.display = pairs[1];
					}

					//existing one with spaces separating terms
					else {
						//look to see if ends with )
						if(part[part.length -1] === "}"){
							part = part.substring(0, part.length);
						}
						currentQueryObject.terms += " " + part;
					}
				}

				if(currentQueryObject !== null) completeObject(currentQueryObject);

			} else if(str.indexOf("start=") === 0) {
				this.start = parseInt(str.split("=")[1]);

			} else if(str.indexOf("rows=") === 0) {
				this.rows = parseInt(str.split("=")[1]);
			} else if(str.indexOf("hl.fl=") === 0) {
				this.setHighlightField(str.split("=")[1]);
			} else if(str.indexOf("sort=") === 0) {
				let sortParts = str.split("=")[1].split(" ");
				this.setSort(sortParts[0], sortParts[1]);
			} else if(str.indexOf("hl=") === 0) {
				//can skil hl=true/false; depends on hl.fl
			} else if(str.indexOf("ff=") === 0){
				let ffparts= str.split("=")[1].split(";");
				this.addFacetFields(ffparts);
			} else if(str.indexOf("fl=") === 0){
				let ffparts= str.split("=")[1].split(" ");
				this.addReturnFields(ffparts);
			}  else {
				console.log("NOT PARSING THIS PART OF HASH: " + str);
			}
		}

		return true;
	}



	/* this is to update the hash based entirely on our objects props
	*/
	updateHash(url){
		//replace & with | so we can keep in the hash and not worry about mistake as params
		url = url.replaceAll('&', '|');

		url = url.split("query=")[1];

		let rootUrl = location.href.split("#")[0];

		location.href = rootUrl + "#" + url;

		localStorage.setItem(this.config.localStoragePrefix + "lastSearch", url);


	}
}




