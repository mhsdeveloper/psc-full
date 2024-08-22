function NextPrev(props) {

	return {
		navMode: "chronological", // or "searchResults"
		mode: localStorage.getItem("COOPSEARCH-results-mode"), // "coopSearch", "search", or "read"
		$template: '#nextPrev',
		today: "",
		thisDoc: {}, // the present doc's metadata, as return from SOLR context search
		todayDocs: [],
		prevDocs: [],
		nextDocs: [],
		docTitle: "",

		returnFields: props.fields,
		showBackToSearch: false,
		searchHash: "",
		showOfResults: false, //flag to show "showing 1 of 22 results" style banner
		showTodayDocs: false,

		arrangeTitle(){
			//move title out of metadata
			let t = "[untitled]";
			let tpar = document.getElementsByTagName("titlestmt");
			if(tpar.length) t = tpar[0].getElementsByTagName("title")[0].innerHTML;
			else {
				tpar = document.getElementById("document").getElementsByTagName("header");
				if(tpar.length) t = tpar[0].innerHTML;
			}
			this.docTitle = t;
		},
	

		getTodayFromDoc(){
			//BTW this.config belongs to the parent Obj, DocumentEnhancements,
			//because of the way petite vue just add components sub objects.
			//Subjects all share a common "this", which is the parent obj

			let numericDate = CoopHelpers.getDocumentDate(this.config.xpaths.date);
			//the context sidebar date display
			this.today = CoopHelpers.formatDate(numericDate);

			//handle notBefore notAfter
			if(numericDate.includes("~")){
				numericDate = numericDate.replace(/[ ~]/g, "");
				numericDate = numericDate.substring(0, 8);
			}
			return numericDate;
		},

		getDateContext(numericDate){
			let searchDate = "" + numericDate;
			while(searchDate.length < 8) searchDate += "9"; //9 so that it sorts at end of date ranges
			//hold the docs here when sorted by chrono
			let tempDocs = {today: [], next: [], prev: []};
			let url = "/publications/" + Env.projectShortname + "/context?configRows=3000&date=" + searchDate + "&project=" + Env.projectShortname;

			Yodude.send(url).then((resp) => {
				if(resp.errors && resp.errors.length){
					this.logError(resp.errors);
					return;
				}
				if(!resp.response) return;

				if(resp.response.docs.length == 0){
					//no nearby docs?
					return;
				}

				//sort all docs into new object with dates as keys
				let docs = {}
				for(let doc of resp.response.docs){
					if(!docs[doc.date_when]) docs[doc.date_when] = [];
					//skip the current doc
					if(doc.id == this.docid){
						this.thisDoc = doc;
						this.thisDoc.displayDate = CoopHelpers.formatDate(doc.date_when);
						continue;
					}

					//format date
					doc.displayDate = CoopHelpers.formatDate(doc.date_when);
					docs[doc.date_when].push(doc);
				}

				//found doc with a date key that is our doc's date so keep reference to it
				if(docs[numericDate]) tempDocs.today = docs[numericDate];
				//or, find closest key
				else {
					let dateKeys = Object.keys(docs);
					for(let i=0;i<dateKeys.length;i++){
						if(dateKeys[i].indexOf(numericDate) === 0){
							break;
						}
					}
				}

				//find next and prev date sets
				let keys = Object.keys(docs);
				for(let i=0;i<keys.length; i++){
					if(keys[i] == searchDate){
						if(i>0){
							tempDocs.prev = docs[keys[i-1]];
						}

						if((i + 1)< keys.length){
							tempDocs.next = docs[keys[i+1]];
						}
					}
				}
				//now sort each list
				this.todayDocs = this.sortDocs(tempDocs.today);
				this.prevDocs = this.sortDocs(tempDocs.prev);
				this.nextDocs = this.sortDocs(tempDocs.next);
			});
		},

		sortDocs(docs){
			let sorted = [];

			//first look for any doc that is marked as list_order = 0
			for(let i=0;i<docs.length; i++){
				if(docs[i].list_order == "0"){
					sorted.push(docs.splice(i,1)[0]);
					break;
				}
			}

			//finish the rest
			let id = "ZZZ99999"; //this is last possible doc id type, regardless of project
			while(docs.length){
				let j = 0;

				for(let i=0;i<docs.length; i++){
					//is this doc's id lower than our current tracking id?
					if(docs[i].id < id){
						id = docs[i].id;
						j = i;
					}
				}
				sorted.push(docs.splice(j,1)[0]);
			}

			return sorted;
		},


		backToResults(){
			let hash = localStorage.getItem("COOPSEARCH-" + "lastSearch");
			let href = "/publications/" + Env.projectShortname + "/search#" + hash;
			location.href = href;
		},


		showSearchResultsCxt(){
			let doci = 0;
			let start = 0;
			let rows = 3;

			//labelling for sequential nv
			//label
			doci = parseInt(this.params.get("doci"));

			start = doci > 0 ? doci - 1 : 0;
			this.startLabel = doci + 1;

			//for our first doc, there's no previous, so current is result 0 and next is result 1
			// but after that, result 0 is prev, result 1 is current, and result 2 is next
			let nextIndex = doci == 0 ? 1 : 2;

			let Searcher = new SolrDirect();
			var solrSetup =	{
				localStoragePrefix: "COOPSEARCH-",
				url: Env.searchAPIURL,
				trackHash: false,
				responseHandler: (resp) => {
					if(resp.response.docs && resp.response.docs.length){
						this.searchTotal = resp.response.numFound;

						this.hasSearchCxt = true;
						if(doci > 0 && resp.response.docs[0]){
							let doc = resp.response.docs[0];
							doc.displayDate = CoopHelpers.formatDate(resp.response.docs[0].date_when);
							doc.index = doci - 1; //start is already decremented
							this.prevDocs.push(doc);
						}
						if(resp.response.docs[nextIndex]){
							let doc = resp.response.docs[nextIndex];
							doc.displayDate = CoopHelpers.formatDate(resp.response.docs[nextIndex].date_when);
							doc.index = doci + 1; //start is decremented, so double inc
							this.nextDocs.push(doc);
						}
					}
				}
			}
			Searcher.setup(solrSetup);

			let hash = localStorage.getItem("COOPSEARCH-" + "lastSearch");
			Searcher.readHash(hash);
			Searcher.addReturnFields(this.returnFields);
			Searcher.start = start;
			Searcher.rows = rows;
			Searcher.search();
		},


		buildLink(doc){
			let id = doc.id;
			let index = doc.index;
			return id + '?doci=' + index;
		},


		getNavMode(){

			let navmode = this.params.get("navmode");
			if(navmode){
				if(navmode != "searchresults"){
					navmode = "chronological";
				}
			}

			if(!navmode){
				navmode = localStorage.getItem("COOPNAVMODE");
				if(navmode == "searchresults" && this.params.get("doci"));
				else navmode = "chronological";
			}
			this.navMode = navmode;
			localStorage.setItem("COOPNAVMODE", this.navMode);
		},


		toChronoNav(){
			this.navMode = "chronological";
			localStorage.setItem("COOPNAVMODE", this.navMode);
			let numericDate = this.getTodayFromDoc();
			this.getDateContext(numericDate);
		},


		mounted() {
 			this.params = new URLSearchParams(location.search);

// console.log("USE SEARCHAPARAMS BUILT IN JS PARSING!!!!");
// console.log(this.params);
			//get id of this current url's doc
			let tempurl = location.href;
			if(tempurl.includes("#")) tempurl = tempurl.split("#")[0];
			if(tempurl.includes("?")) tempurl = tempurl.split("?")[0];
			let temp = tempurl.split("/");
			
			this.docid = temp[temp.length -1];

			//get mode from GET params or localStorage
			this.getNavMode();

			
			let hash = localStorage.getItem("COOPSEARCH-" + "lastSearch");
			if(hash) this.showBackToSearch = true;

			if(this.navMode == 'searchresults'){
				this.showSearchResultsCxt();
				this.getTodayFromDoc();
			} else {
				let numericDate = this.getTodayFromDoc();
				this.getDateContext(numericDate);
			}


			this.arrangeTitle();
		}
	}
}


export { NextPrev }