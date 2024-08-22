
import { DateAs8 } from "./dateas8-vue.js";
import { Faceter } from "./faceter-vue.js";
import { TopicsLookup } from "./topics-lookup-pvue.js";


export default {


	data() {
		return {
			searched: false,
			showChooseField: false,
			showNames: false,
			showTopics: false,
			showLoading: false,
			needsResearch: false,
		
			groupLimit: 3,
			groupEditions: true,
			highlightField: "text_merge",
			pendingNameField: "person_keyword",
			pendingNameHusc: "",
			pendingNameObj: null,
			namesInput: null,
			readOnlyFields: ["person_keyword", "author", "recipient", "subject"],
		
			nameFacets: [],
			subjectFacets: [],
		
			hitCount: 0,
			groups: [],
			documents: [],
			highlighting: null,
			mode: 'search', // or "read"
			project: Env.projectShortname,
		
			//this becomes a direct reference to the solr-direct.js queryObjects property, see postMount in html implementation
			queryObjects: [],
		
			//cache for last, first display friendly version of names from HUSCS
			huscCache: {},
			prettyHuscs: {}, //display version names indexed by huscs
		}
	},

	methods: {

		addField(type, value = "", display = "", isPhrase = false, searchNow = false){
			this.needsResearch = true;

			//we ask Searcher for an empty query object, which represents a query element (usually a field and value)
			//Search returns an object with all the necessary props
			if(type == "subject") isPhrase = true;

			let obj = this.Searcher.newQueryObject();
			obj.field = type;
			if(type == "date_when"){
				obj.rangeStart = "18000101";
				obj.rangeEnd = "18991231";
			} else {
				obj.isPhrase = isPhrase;
				obj.terms = value;
				obj.display = display;
			}



			//because this.queryObjects is a ref to Searcher's own obj, we can just push the new obj onto the array.
			//both vue and Searcher will do what they need with the single source of data
			this.queryObjects.push(obj);

			if(searchNow) this.search();
		},

		removeField(indx){
			this.queryObjects.splice(indx, 1);
			this.needsResearch = true;
		},


		addFacet(data){
			let display = data.name;
			if(data.field == "person_keyword") display = this.prettyHuscs[data.name];
			this.addField(data.field, data.facet.name, display, false, true);
		},


		addSearchName(){
			let displayName = CoopHelpers.nameMetadataFormat(this.pendingNameObj);
			let value = this.pendingNameObj.name_key;
			let obj = this.Searcher.newQueryObject();
			obj.field = this.pendingNameField;
			obj.terms = value;
			obj.display = displayName;
			this.queryObjects.push(obj);
			this.needsResearch = true;

			//reset
			this.showNames = false;
			this.namesInput.value = "";
			this.pendingNameRole = "person_keyword";
		},
		

		openTopics(){
			this.showTopics = true;
			this.$refs.topicbox.open();
			this.showNames = false;
		},


		closeTopic(){
			this.showTopics = false;
			this.$refs.topicbox.close();
		},

		chooseTopic(data){
			this.showTopics = false;
			this.$refs.topicbox.close();
			data = data.trim();
			this.addField("subject", data, data, true);
			this.showTopics = false;
		},


		updateDate(data){
			this.queryObjects[data.index][data.prop] = data.dateString;
			this[data.prop] = data.dateString;
			this.needsResearch = true;
		},


		fieldLabel(fieldName){
			switch(fieldName){
				case "date_when": return "dated from";
				case "index": return "edition is";
				case "text_merge": return "text contains";
				case "person_keyword": return "has name";
				case "author": return "author is";
				case "recipient": return "recipient is";
				case "subject": return "has topic";
				default: return fieldName;
			}
			return fieldName;
		},



		search(){
			this.showLoading = true;
			this.groups = [];
			this.documents = [];
			this.highlighting = null;

			this.Searcher.start = 0;

			if(this.groupEditions){
				this.Searcher.groupBy("index", this.groupLimit);
			} else {
				this.Searcher.groupBy("");
			}

			if(this.highlightField.length){
				this.Searcher.setHighlightField(this.highlightField);
			}
			this.Searcher.setSort("date_when", "asc");
			this.Searcher.queryObjects = this.queryObjects;
			this.Searcher.search();

			this.needsResearch = false;
		},



		completeNames: function(huscs, callback = ()=>{}){
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
				callback(person_keyword);
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
				if(callback) return callback(this.huscCache);
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

					this.prettyHuscs[husc] = this.huscCache[husc].family_name + ", " + this.huscCache[husc].given_name;
				}
				
				if(callback) return callback(this.huscCache);
			});
		},



		// personFacetName(f){
		// 	let out = CoopHelpers.nameMetadataFormat(f);
		// 	return out;
		// },

		// facetName(f){
		// 	return f;
		// },


		formatBody(body){
			let trimmed = body.substring(0, 200);
			return trimmed + "...";
		},


		buildLink(doc, project, index){
			let url = "/publications/" + project + "/document/"  + doc.id;
		
			index = parseInt(this.Searcher.start) + parseInt(index);

			return url + '?navmode=searchresults&doci=' + index;// + '&from=' + this.mode;
		},

		reset(){
			location.href = "/publications/" + Env.projectShortname + "/search";
		},



		buildEditionSearchLink(project_id){
			let hash = location.href.split("#")[1];
			return "/publications/" + project_id + "/search#" + hash;
		},


		colatefacets(facets){
			let out = [];
			for(let i=0;i<facets.length; i+=2){
				out.push({name: facets[i], count: facets[i+1]});
			}

			return out;
		}
	},



	components: {
		DateAs8,
		TopicsLookup,
		Faceter,
	},



	mounted(){
		if(document.body.classList.contains("editionSearch")) this.groupEditions = false;

		this.Searcher = Searcher;
		this.projects = Projects;
		this.coopHelpers = CoopHelpers;

		if(Env.projectShortname == "coop") this.mode = "coopSearch";
		localStorage.setItem("COOPSEARCH-results-mode", this.mode);

		const initialSearch = this.Searcher.setup(
			{
				localStoragePrefix: "COOPSEARCH-",
				url: Env.searchAPIURL,
				responseHandler: (resp) => {
					this.showLoading = false;
					this.highlighting = resp.highlighting;

					let div = document.getElementById("summary");
					if(div){
						let y = div.getBoundingClientRect().top;
						window.scrollTo({top: y, behavior: "smooth"});
					}

					if(resp.grouped){
						this.groups = resp.grouped.index.groups;
					} else {
						if(resp.facet_counts && resp.facet_counts.facet_fields){
							this.facetNames = [];
							this.facetSets = [];
							let keys = Object.keys(resp.facet_counts.facet_fields);
							for(let key of keys){
								if(key == "subject"){
									this.subjectFacets = this.colatefacets(resp.facet_counts.facet_fields[key]);
								}
								else if(key == "person_keyword"){
									let names = this.colatefacets(resp.facet_counts.facet_fields[key]);
									this.completeNames(names, (completedNames) => {
										this.nameFacets = names;
									});
								}
							}

						}

						this.hitCount = resp.response.numFound;
						this.documents = resp.response.docs;
						let pagination = this.paginator.build(this.Searcher.start, this.hitCount);
						let cont = document.getElementById("paginationBot");
						cont.innerHTML = "";
						cont.appendChild(pagination);

						//2nd  copy
						pagination = this.paginator.build(this.Searcher.start, this.hitCount);
						let cont2 = document.getElementById("paginationTop");
						cont2.innerHTML = "";
						cont2.appendChild(pagination);

					}
				}
			}
		);

		this.queryObjects = this.Searcher.queryObjects;
		if(this.queryObjects.length === 0) this.queryObjects.push(this.Searcher.newQueryObject());

		//find huscs for any pre-populated fields
		let huscs = [];
		for(let obj of this.queryObjects){
			if(obj.field == "person_keyword" || obj.field == "author" || obj.field == "recipient"){
				huscs.push(obj.terms);
			}
		}
		this.fillHuscCache(huscs, null);

		this.Searcher.addReturnFields(["id", "index", "title", "filename", "resource_group_name", "date_when", "date_to", "author", "recipient", "person_keyword", "subject", "doc_beginning"]);

		window.addNameToSearch = (husc, obj, input) => {
			this.pendingNameHusc = husc;
			this.pendingNameObj = obj;
			this.namesInput = input;

			this.huscCache[husc] = obj;
			this.prettyHuscs[husc] = this.huscCache[husc].family_name + ", " + this.huscCache[husc].given_name;
		}

		buildNameLookup();

		this.paginator = new Paginator({
			prevPageLabel: "Prev",
			nextPageLabel: "Next",
			pageClickCallback: (e) => {
				// let par = e.target.parentNode;
				// let currentPages = par.getElementsByClassName("currentPage");
				// if(currentPages.length) currentPages[0].classList.remove('currentPage');
				// e.target.classList.add('currentPage');
				let start = e.target.getAttribute("data-start");
				this.Searcher.start = start;
				this.Searcher.search();
			}
		});

		if(initialSearch) this.search();
	}

}