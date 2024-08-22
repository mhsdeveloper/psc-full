//faceter.js simply sorts and formats the SOLR response json into a more usable structure

// !!!!!!!!!!!!!!!!!!!!!! This is now just used for the read pages
// search-app.js is for the main search pages


import { Faceter as faceter } from "./faceter.js";
import { ref } from "/lib/vue3.2.41-dev.js";
import { TopicsLookup } from "./topics-lookup.js?bcbv=1001";

export default {
	
	buildData(config, props) {
		this.data = function(){

			let dates = ref(null);

			let data = {
				mode: "search", // or "read"
				openSearchCategories: false,

				start: 0,

				//this is the fields as strings that are passed to searcher
				params: {
					terms: "",
					sd: "",
					ed: ""
				},

				//objects used to display params in UI, in order user selects them.
				//these get parsed into this.params (above)
				selectedParams: [

				],

				// //these arrays are the data rep for the selec
				// subject: [],
				// //this is the field in solr that store huscs

				showFacets: false,
				//obj that parses SOLR facets
				faceter: faceter,

				//obj that represents the facet UI structure
				//this is available facets, NOT selected
				facets: {
					
				},

				facetFieldNames: [],

				showNames: false,

				useKeywords: false,
				useNames: false,
				useDates: false,
				pendingNameRole: "",
				pendingNameHusc: "",
				pendingNameObj: null,

				showTopics: false,
				useTopics: false,
	
				huscCache: {},

				dateYearRange: {
					min: 1600,
					max: 1940
				},

				startDate: {
					year: 1800,
					month: "00",
					day: "00"
				},

				endDate: {
					year: 1900,
					month: "99",
					day: "99"
				},

				//the facet object for displaying selected facets
				selectedFacets:[],
				//just the actual names (the SOLR tokens)
				selectedFacetNames: [],
				facetShowAllFlags: {},

				hitCount: 0,
				recentSearchParams: "",

				documents: [],
				errorMessage: "",
				showLoading: false,

				searched: false,
				contextHidden: false,
				infoBox: null,
				showInfoBox: false,
				needToClickSearch: false,

				test: "",

				dates
			}

			//build facets from global
			for(let f of config.facetFields){
				data.facets[f.field] = [];
				data.params[f.param] = "";
				data.facetFieldNames.push(f.field);
				data.facetShowAllFlags[f.param] = false;
			}

			//keep config for self
			data.config = config;

			if(data.config.hideNames) data.showNames = false;
			if(data.config.hideTopics) data.showTopics = false;
			if(data.config.hideDates) data.useDates = false;
			
			if(props){
				//add other props 
				let pkeys = Object.keys(props);
				for(let k of pkeys){
					data[k] = props[k];
				}
			}

			return data;
		}
	},


	components: {
		TopicsLookup
	},



	methods: {

		findTopic(){

		},

		updateTerms(e){
			if(e && this.params.terms.length) e.target.parentNode.classList.add("inuse");
		},

		addSearchedName(){
			let displayName = CoopHelpers.nameMetadataFormat(this.pendingNameObj);
			let value = this.pendingNameObj.name_key;
			if(this.pendingNameRole.length) value += ":" + this.pendingNameRole;
			this.addSearchParam("p", value, displayName);
			this.searchPending();
			this.showNames = false;
			this.namesInput.value = "";
			this.pendingNameRole = "";
		},


		addSearchedTopic(topic){
			this.addSearchParam("s", topic, topic);
			this.searchPending();
	//		this.showTopics = false;
		},



		refine(param, obj){
			if(param == "p"){
				let displayName = CoopHelpers.nameMetadataFormat(obj);
				this.addSearchParam("p", obj.name_key, displayName, false);
			} else if(param == "s"){
				this.addSearchParam("s", obj.name, obj.name, false);
			}

			//if(this.mode == "read") this.search();
			this.search();
		},

		/* use this to process any use selected search parameter, whether from
			HASH or clicked
			Here we just add to our array of search params. We only  convert to 
			SOLR specific params when we use this.search()
		*/
		addSearchParam(param, value, displayName){
			//avoid repeat params
			if(param == "date"){
				this.useDates = true;
				//only allow adding date once
				for(let p of this.selectedParams){
					if(p.param == "date") return;
				}
			}

			for(let p of this.selectedParams){
				if(p.value == value) return;
			}

			this.selectedParams.push({
				param,
				value,
				displayName
			});

			this.searchPending();
		},

		removeParam(indx){
			let param = this.selectedParams.splice(indx, 1);
			if(param[0].param == "date"){
				this.useDates = false;
			}
			if(this.mode == "read") this.search();
			else this.searchPending();
		},

		toggleTips() {
			this.openSearchCategories = !this.openSearchCategories;
		},


		placeInfoBox(e){
			let boxname = e.target.getAttribute("data-name");
			let div = document.getElementById(boxname);
			if(!div){
				console.log("no info box div #" + boxname);
				return;
			}
			this.infoBox.innerHTML = div.innerHTML;
			this.infoBox.style.display = "block";
			let rect = e.target.getBoundingClientRect();
			let rectTools = this.tools.getBoundingClientRect();
			this.infoBox.style.top = (rect.top - rectTools.top ) + "px";
			this.infoBox.style.left =  40 + "px"; //rect.left
			this.showInfoBox = true;
		},

		hideInfoBox(){
			if(this.infoBox.classList.contains("show")){
				this.showInfoBox = false;
				this.infoBox.classList.remove("show");
			}
		},
	

		resetFacets(){
			this.facets.person_keyword = [];
			this.facets.subject = [];
		},

		searchPending(){
			this.needToClickSearch = true;
		},

		//this is NOT called by SOLR on page load, only when user clicks (or read page loads first time w/o GET params)
		search(start = 0){
			this.start = start;
			this.resetFacets();
			this.showTopics = false;
			this.showNames = false;
			this.showLoading = true;
			this.params.p = "";
			this.params.s = "";

			if(this.params.terms.length === 0){
				this.useKeywords = false;
			}

			if(this.useDates){
				this.params.sd = this.datePartsToDate(this.startDate);
				this.params.ed = this.datePartsToDate(this.endDate);
			} else {
				this.params.sd = "";
				this.params.ed = "";
			}

			//parse rest
			for(let p of this.selectedParams){
				if(p.param == "p"){
					this.params.p += " " + p.value;
				}
				else if(p.param == "s"){
					this.params.s += ' "' + p.value + '"';
				}
			}

			this.params.p = this.params.p.trim();
			this.params.s = this.params.s.replace(/\s\s+/g, " ");
			this.params.s = this.params.s.trim();

			this.Searcher.addFields(this.params);
			this.Searcher.search(this.start);
		},


		datePartsToDate(partsObj){
			if(partsObj.year.length == 0) return "";
			return partsObj.year + partsObj.month + partsObj.day;
		},


		dateToParts(date, partsObj){
			partsObj.year = date.substring(0, 4);
			if(date.length > 5) partsObj.month = date.substring(4,6);
			if(date.length > 7) partsObj.day = date.substring(6,8);
		},


		resetAll(){
			let keys = Object.keys(this.params);
			for(let key of keys){
				this.params[key] = "";
			}
			this.search();
		},

	
		searchEnterKey(e){
			if(e.key == "Enter"){
				this.search();
			}
		},
	

		parseResults(obj){
			this.needToClickSearch = false;
			window.scrollTo(0, 0);
			this.showLoading = false;
			this.searched = true;
			this.contextHidden = true;
	
			if(obj.error && obj.error.length > 0){
				this.errorMessage = obj.error;
				return;
			}
	
			if(obj.response){
				//good results, so store this search
				let searchParams = location.href.split("#")[1];
				localStorage.setItem(Env.projectShortname + "_RECENT_SEARCH", searchParams);
				this.recentSearchParams = searchParams;

				for(let doc of obj.response.docs){
					this.parseHighlighting(doc);
				}
				this.documents = obj.response.docs;
				this.hitCount = parseInt(obj.response.numFound);
				this.showResults = true;
				this.Searcher.createPagination();
				let raw = this.faceter.loadSolrResponse(obj);
				this.correlateFacetsFields(raw);
			}
		},



		//raw comes from the faceter.js object
		correlateFacetsFields(raw){
			if(raw.subject){
				this.facets.subject = [];

				for(let s of raw.subject.facets) {
					//avoid repeating the facet
					//only show if will reduce the count
					if(s.count < this.hitCount){
						this.facets.subject.push(s);
					}
				}
			}
			if(raw.person_keyword) this.completeNames(raw.person_keyword.facets);
		},



		completeNames: function(huscs){
			let person_keyword = [];
			let names = [];
			for(let n of huscs){
				if(n.count == 0) continue;
				names.push(n.name.trim());
			}

			this.fillHuscCache(names, (data) =>{
				//go thru raw facet and see if we got a names from the db matching the husc ...
				for(let person of huscs){
					//change resp.data to this.huscCache
					if(data[person.name] && person.count < this.hitCount){
						//... we did, so add count ..
						data[person.name].count = person.count;
						// ... and then push to our live vue list
						person_keyword.push(JSON.parse(JSON.stringify(data[person.name])));
					}
				}
				this.facets.person_keyword = person_keyword;
			});
		},



		fillHuscCache(huscs, callback){
			let sendHuscs = [];
			
			//new version with only those names not yet retrieved
			for(let n of huscs){
				if(this.huscCache[n]) continue;
				sendHuscs.push(n.trim());
			}
			if(sendHuscs.length === 0){
				return callback(this.huscCache);
			}
			let list = sendHuscs.join(";");
			Yodude.send(Env.apiExtURL + "names?huscs=" + list).then((resp) => {
				if(resp.errors && resp.errors.length){
					this.logError(resp.errors);
					return;
				}

				//store response huscs in husc cache
				for(let husc of sendHuscs){
					if(resp.data[husc]){
						this.huscCache[husc] = JSON.parse(JSON.stringify(resp.data[husc]));
					} else {
						this.huscCache[husc] = {name_key: husc, family_name: husc, given_name: "", middle_name: "", maiden_name: "", suffix: "", title: ""}
					}
				}
				
				return callback(this.huscCache);
			});
		},


		parseHighlighting: function(doc){
			if(!doc.highlighting.length){
				doc.highlighting.push(doc.doc_beginning);
				return;
			}
			//try to avoid when highlighting just repeats title
			let hitext = doc.highlighting[0].replace(/<.*?>/g, "").replace(/\s\s+/g, " ");
			let title = doc.title[0].replace(/<.*?>/g, "").replace(/\s\s+/g, " ");
			if(title == hitext) doc.highlighting[0] = "";
			else doc.highlighting[0] = "... " + doc.highlighting[0] + " ...";
		},

		personFacetName(f){
			let out = CoopHelpers.nameMetadataFormat(f);
			return out;
		},

		facetName(f){
			return f;
		},


		formRefUrl(url){
			if(url.indexOf("http") === 0) return url;

			if(url.includes("/")){
				return "/database/" + url;
			}

			return "/database/images/" + url;
		},


		formatResourceGroupname(name){
			return name;
		},


		linkFromResourceGroup(name){
			return "";
		},

		formatBody(body){
			let trimmed = body.substring(0, 200);
			return trimmed + "...";
		},


		buildLink(doc, index){
			let url = doc.resource_uri_start  + doc.id;
			//just plain read, so create links that just navigation chronologically

			if(this.selectedParams.length === 0 && this.params.terms.length === 0){
				return url;
			}

			let highliteSearchString = "";
			if(doc.highlighting && doc.highlighting[0].length){
				let text = doc.highlighting[0];
				//remove successive markers
				text = text.replace(/<\/em>\s+<em>/g, " ");
				let matches = [...text.matchAll(/(<em>)([^<>]*)(<\/em>)/gi)];
				for(let m of matches){
					// m[0] is full match, m[1] is <em>, m[2] is inside
					if(m[2].length > highliteSearchString.length) highliteSearchString = m[2];
				}

				highliteSearchString = "&ss=" + encodeURIComponent(highliteSearchString);
			}
			index = parseInt(this.Searcher.start) + parseInt(index);

			return url + '?sp=' + this.recentSearchParams + '&doci=' + index + '&from=' + this.mode + highliteSearchString;
		},

		reset(){
			location.href = "/publications/" + Env.projectShortname + "/search";
		}
	},


	/**
	 * How this app should communicate with SOLR
	 * we want the app to control syncing the UI and the Hash always,
	 * and then call SOLR. SOLR's response then informs the refine facets,
	 * but the selected facets and fields are always created from this App. 
	 * 
			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			// App add selected params, adjusts Hash, and calls SOLR
			// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	 */

	mounted(){
		//namelookup is designed to work outside of Vue, so we follow it's setup here
		buildNameLookup();

		this.env = Env;
		this.coopHelpers = CoopHelpers;
		this.projects = Projects;

		window.addNameToSearch = (husc, obj, input) => {
			this.pendingNameHusc = husc;
			this.pendingNameObj = obj;
			this.namesInput = input;
		}
		
		this.CoopHelpers = CoopHelpers;
		localStorage.setItem(Env.projectShortname + "_results_mode", this.mode);
		this.faceter.setFacetFields(this.facetFieldNames);
		this.faceter.setFacetsWhitelist(this.config.whitelists);

		let solrSetup = {
			url: Env.searchAPIURL,
			configURL: "solr-search.json",
			highlightingFields: ["text_merge"],
			prevPageLabel: "Previous",
			nextPageLabel: "Next",
			trackHash: false,
			rows: 20,
			//this will tell solr.js to automatically update the pagination html
			//do this in vue app
			paginationElement: null,

			//this is called when solr.js receives a reply from the server
			responseHandler: (resp) => {
				this.parseResults(resp);
				let pg = document.getElementById("pagination");
				pg.innerHTML = "";
				pg.appendChild(this.Searcher.createPagination());
			}
		}


		this.Searcher = new Solr();
		//returns true if performed an initial search
		this.Searcher.setup(solrSetup);
	
		//if set to show all (from "read" page), make sure ! already loading due to url hash params
		if(this.mode == "read" && !location.href.includes("#")){
			this.search();
		} else if(location.href.includes("#")){
			let fields = this.Searcher.fieldsFromHashString(location.href.split("#")[1]);
			let fieldNames = Object.keys(fields.fields);
			let namesQueue = [];
			for(let p of fieldNames){
				let value = fields.fields[p];
				if(value.length === 0) continue;

				if(p == "terms"){
					this.useKeywords = true;
					this.params.terms = value;
					//this.updateTerms();
				}

				if(p == "s"){
					//look for multiple subjects
					if(value.includes('" "')){
						let values = value.split('" "');
						for(let v of values){
							let val = v.replace(/"/g, "");
							this.addSearchParam("s", val, val);
						}
					} else {
						let val = value.replace(/"/g, "");
						this.addSearchParam("s", val, val);
					}
				} else if(p == "p"){
					let names = value.trim().split(" ");
					for(let n of names){
						if(n.length) namesQueue.push(n);
					}
				} else if(p == "sd"){
					this.useDates = true;
					this.dateToParts(value, this.startDate);
					this.addSearchParam("date", "dateplaceholder", "dateplaceholder");
				}
				else if(p == "ed"){
					this.useDates = true;
					this.dateToParts(value, this.endDate);
					this.addSearchParam("date", "dateplaceholder", "dateplaceholder");
				}

			}

			//if we need to complete huscs into readable names, do that and use callback to searhc
			if(namesQueue.length){
				this.fillHuscCache(namesQueue, (data) =>{
					for(let husc of namesQueue){
						let name = husc;
						if(data[husc]){
							name = this.personFacetName(data[husc]);
						}
						this.addSearchParam("p", husc, name);
					}

					this.search(fields.start);
				});

			} else this.search(fields.start);
		}

	}

}