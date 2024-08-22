
function AuthorRecipient(props) {

	return {
		$template: '#authorRecipient',
		authors: [],
		recipients: [],
		config: props,
	
		/**
		 * 
		 * @param {*} husc 
		 * @param {*} destinationProp 
		 * 
		 * this one is loading for the context bar, but not persRefs within the text
		 */
		loadContextName(husc, destinationProp){
			Yodude.send(Env.apiExtURL + "name?husc=" + husc).then((resp) => {
				if(typeof resp.errors != "undefined" && resp.errors.length){
					console.log(resp.errors);
					return;
				}
	
				//find description for this project
				let descs = CoopHelpers.buildNameDescriptions(resp.data, Env.projectShortname);
	
				let person = {};
				person.displayName = CoopHelpers.nameMetadataFormat(resp.data);
				person.birthDate = CoopHelpers.formatDate(resp.data.date_of_birth);
				person.deathDate = CoopHelpers.formatDate(resp.data.date_of_death);
				person.descriptions = descs;
	
				//add to vue reactives
				this[destinationProp].push(person);
			});
		},
	 
		getAuthorsRecipients(){
			//get date from teiHeader, for single document xml files
			let xp = document.evaluate(this.config.xpaths.author, document, null).iterateNext();
			if(xp !== null){
	
				let authorsHuscs = null;
				let temp = xp.textContent;
				if(temp.includes(";")) authorsHuscs = temp.split(";");
				else authorsHuscs = [temp];
	
				for(let husc of authorsHuscs){
					this.loadContextName(husc, "authors");
				}
			}
	
			xp = document.evaluate(this.config.xpaths.recipient, document, null).iterateNext();
			if(xp !== null){
	
				let huscs = null;
				let temp = xp.textContent;
				if(temp.includes(";")) huscs = temp.split(";");
				else huscs = [temp];
	
				for(let husc of huscs){
					this.loadContextName(husc, "recipients");
				}
			}
		},

		toggleName(e){
			let par = e.target;
			while(par){
				if(par.classList && par.classList.contains("name")) break;
				par = par.parentNode;
			}

			if(par.classList.contains("open")) par.classList.remove("open");
			else par.classList.add('open');
		},
	
	
		mounted() {
			this.getAuthorsRecipients();
		}
	}
}


export { AuthorRecipient }