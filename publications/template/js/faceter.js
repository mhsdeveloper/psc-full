const Faceter = {

	facetFields: [],
	facets: {
		// see formEmptyFacet for how each facet sub-object should be structured
	},



	//object with keys that are the facet field, to arrays that are lists of facets to skip displaying
	blacklistFacets: {

	},
	// note if there's a whitelist, it overrides the black list
	whitelistFacets: {

	},


	setFacetFields(f){
		this.facetFields = f;
	},

	setFacetsWhitelist(objSet){
		this.whitelistFacets = objSet;
	},

	setFacetsBlacklist(objSet){
		this.blacklistFacets = objSet;
	},


	loadSolrResponse(obj){
		if(!obj.facet_counts) return;

		this.facets = {};

		let set = obj.facet_counts.facet_fields;

		for(let field of this.facetFields){
			if(set[field]){
				this.addFieldsFacets(field, set[field]);
			}
		}

		return this.facets;
	},


	addFieldsFacets(field, facetResults){
		if(facetResults.length == 0){
			//this.facets[field].facets = [];
			return;
		}
		
		if(!this.facets[field]) this.facets[field] = this.formEmptyFacet(field);
		let whitelist = this.whitelistFacets[field] ? this.whitelistFacets[field] : false;
		let blacklist = this.blacklistFacets[field] ? this.blacklistFacets[field] : false;

		//SOLR alternates the facet with the counts, so let's fix that
		for(let i=0; i<facetResults.length; i+=2){
			let name = facetResults[i];
			let count = facetResults[i + 1];

			if(whitelist){
				if(!whitelist.includes(name)) continue;
			} else if(blacklist) {
				if(blacklist.includes(name)) continue;
			}
			
			this.facets[field].facets.push({name: name, count: count});
		}
	},


	//formalize our facet structure
	formEmptyFacet(field){
		return {
			name: field,
			facets: []
		}
	}

}

export { Faceter }