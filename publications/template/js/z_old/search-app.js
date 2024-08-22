
	var SearchApp = {
        el: '#searchApp',

        data(){
          return {
			facets: {
				person_keyword: [],
				date_year: [],
				subject: []
			},
			//facets that need further lookups to complete display
			rawFacets: {
				//this needs lookup to get name info from husc
				person_keyword: [],
			},

			fields: {
				text: "", //text search
				p: "" // person_keywords (huscs)
			},
			showResults: false,
			documents: [],
			docTexts: [],
			hitCount: 0,
			errorMessage: "",
			errorMsgs: [],

			selectedNames: [],
			selectedHuscs: "",

			results: [],
		  }
        },

        methods: {
			gather: function(e){
				this.searchType = "metadata";
			},

			query: function(fields){
				this.searchType = "documents";
			},


			parseBrowseResults: function(obj){
				if(this.searchType == "metadata") this.parseMetadata(obj);
				else {
console.log(obj);
				}
			},

			updateResults: function(e){
				let el = e.target;
				while(el && el.nodeName != "A"){
					el = el.parentNode;
				}
				if(!el || el.nodeName != "A") return;
				let str = el.getAttribute("href");
				if(str.indexOf("#") == -1) return false;
				str = str.split("#")[1];
				let set = Searcher.fieldsFromHashString(str);
				
				Searcher.addFields(set.fields);
				Searcher.rows = set.rows;
				Searcher.callSolr(set.start);
			},

			parseMetadata(obj){
				if(!obj.facet_counts){
					console.log("No solr facet counts returned");
					return;
				}
				let solrFacets = obj.facet_counts.facet_fields;
				if(solrFacets){
					let facets = Object.keys(this.facets);
					for(let i=0;i<facets.length;i++){
						let name = facets[i]
						if(solrFacets[name]){
							//remap from solr: even array members are facet item,
							// and odd are counts
							this.facets[name] = [];
							for(let i=0;i<solrFacets[name].length;i+=2){
								//skip zero counts
								if(solrFacets[name][i +1] < 1) continue;

								let text = solrFacets[name][i];
								if(name == "subject") text = "\"" + text + "\"";
								if(this.rawFacets[name]) this.rawFacets[name].push({name: text, count: solrFacets[name][i +1]});
								else this.facets[name].push({name: text, count: solrFacets[name][i +1]});
							}
						}
					}
					console.log(this.facets);
				}

				if(this.rawFacets.person_keyword.length) this.completeNames();
			},

			completeNames: function(){
				let names = [];
				for(let n of this.rawFacets.person_keyword){
					if(n.count == 0) continue;
					names.push(n.name);
				}
				names = names.join(";");

				Yodude.send(Env.apiExtURL + "names?huscs=" + names).then((resp) => {
					if(resp.errors && resp.errors.length){
						this.logError(resp.errors);
						return;
					}
					//go thru raw facet and see if we got a names from the db matching the husc ...
					for(let person of this.rawFacets.person_keyword){
						if(resp[person.name]){
							//... we did, so add count ..
							resp[person.name].count = person.count;
							// ... and then push to our live vue list
							this.facets.person_keyword.push(resp[person.name]);
						}
					}
				});
			},


			search: function(e){
				Searcher.setFields(this.fields);
				Searcher.search();
			},


			formatDate: function(d){

				return CoopHelpers.formatDate(d);

				d = d + "";
				d = d.replace("-", "");
				let year = d.substring(0,4);
				let monthDigits = d.substring(4,6);
				let month = "January";
				switch(monthDigits){
					case "02": month = "February"; break;
					case "03": month = "March"; break;
					case "04": month = "April"; break;
					case "05": month = "May"; break;
					case "06": month = "June"; break;
					case "07": month = "July"; break;
					case "08": month = "August"; break;
					case "09": month = "September"; break;
					case "10": month = "October"; break;
					case "11": month = "November"; break;
					case "12": month = "December"; break;
				}
				let day = d.substring(6,8);

				if(day[0] == "0") day = day.substring(1,2);

				return year + ", " + month + " " + day;
			},


			describeSearch: function(){
				let str = "Documents ";
				let parts = [];
				if(Searcher.fields.text){
					str = "Searching documents for the text <em>" + Searcher.fields.text + "</em> ";
				}
				if(Searcher.fields.p){
					let names = Searcher.fields.p.split(";");
					parts.push("mentioning " + names.join(","));
				}
				if(Searcher.fields.s){
					let subjects = Searcher.fields.s.split(";");
					parts.push("concerning subjects " + subjects.join(","));
				}

				str += parts.join(", ");

				return str;
			},


			chooseName: function(e){
				let el = e.target;
				while(el && el.nodeName != "A"){
					el = el.parentNode;
				}
				if(el.nodeName != "A") return;

				let husc = el.getAttribute("data-husc");
				let html = el.innerHTML;
				this.selectedNames.push({html: html, husc: husc});
				this.namesModal.close();
				this.updateSelectedHuscs();
			},

			updateSelectedHuscs(){
				let temp = [];
				for(let n of this.selectedNames){
					temp.push(n.husc);
				}
				this.fields.p = temp.join(";");
			},

			removeSelectedName: function(e){
				let el = e.target;
				while(el && el.nodeName != "SPAN"){
					el = el.parentNode;
				}
				if(el.nodeName != "SPAN") return;
				let i = el.getAttribute("data-index");
				this.selectedNames.splice(i,1);
			},

			parseResults: function(obj){
				if(obj.response){
					this.results = obj.response.docs;
					this.hitCount = obj.response.numFound;
					this.showResults = true;
				}
			}
		},

		mounted: function(){

			//for the full text + meta search
			let Searcher = new Solr();
			var solrSetup =	{
				url: "/publications/" + Env.projectShortname + "/searchQuery",
				highlightingFields: ["text_merge"],
				configURL: "",
				prevPageLabel: "prev",
				nextPageLabel: "next",
				rows: 20,
				paginationElement: document.getElementById("pagination"),
				responseHandler: (resp) => {
					let html = Searcher.createPagination();
					SearchApp.parseResults(resp);
				},
				trackHash: true
			}
			Searcher.setup(solrSetup);


			this.gather();
		}
      });
