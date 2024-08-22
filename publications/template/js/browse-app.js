window.addEventListener("DOMContentLoaded", function(){

	//for the browse facets search
	let BrowseSearcher = new Solr();
	var solrSetupB = {
		url: "/mhs-api/ext/metadata",
		highlightingFields: ["text_merge"],
		configURL: "",
		prevPageLabel: "prev",
		nextPageLabel: "next",
		rows: 20, //we don't really want the documents, we want the facets
		//this will tell solr.js to automatically update the pagination html
		paginationElement: document.getElementById("pagination"),

		//this is called when solr.js receives a reply from the server
		responseHandler: (resp) => {
			BrowseApp.parseResults(resp);
		},
		trackHash: false
	}
	BrowseSearcher.setup(solrSetupB);


	var BrowseApp = new Vue({
        el: '#browseApp',

        data: function () {
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
			}

		  }
        },

        methods: {
			gather: function(e){
				if(typeof HUSC != "undefined"){
					BrowseSearcher.setFields({"person_keyword": HUSC});
				}
				this.searchType = "metadata";
				BrowseSearcher.search();
			},

			query: function(fields){
				this.searchType = "documents";
				BrowseSearcher.search();
			},


			parseResults: function(obj){
				if(this.searchType == "metadata") this.parseMetadata(obj);
				else {
				}
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
			}

		},

		mounted: function(){
			this.gather();
		}
    });

});