
let PersRefHunter = {

	displayListeners: [],
	preCallbacks: [],
	nameCache: {},
	queue: [],
	displayPending: [],

	enhanceClasses(clsname = "persRef"){
		let set = document.getElementsByClassName(clsname);
		for(let p of set){
			p.addEventListener("click", (e)=>{this.load(e);});
			p.setAttribute("tabindex", 0);
			p.addEventListener("keyup", (e)=>{
				if(e.key != "Enter") return;
				this.load(e);
			});
		}
	},

	addListener(type = "display", fn){
		if(type == "display") this.displayListeners.push(fn);
		else this.preCallbacks.push(fn);
	},


	getUnknownTemplate(husc){
		return {
			displayName: "[unknown]",
			name_key: husc,
			family_name: "", given_name: "", middle_name: "", maiden_name: "",
			date_of_birth: "", date_of_death: "", birth_era: "", death_era: "",
			descriptions: [],
			unknown: true
		}
	},

	

	load(e){
		let el = e.target;
		for(let fn of this.preCallbacks) fn(e);
		while(el && !el.classList.contains("persRef")){
			el = el.parent;
		}

		if(!el || !el.classList.contains("persRef")) return;

		let attr = el.getAttribute("data-husc");
		if(!attr){
			console.log("No attribute for husc: ");
			console.log(el);
			return;
		}

		//handle multiple HUSCS in one attr
		if(attr.includes(";")){
			let set = attr.split(";");
			for(let a of set){
				this.queuePersRef(a.trim(), e);
			}
		} else {
			this.queuePersRef(attr, e);
		}

		this.loadNext();
	},

	queuePersRef(husc, e){
		this.queue.push({e: e, husc: husc});
	},

	queueDisplay(husc, e){
		this.displayPending.push({husc: husc, e: e});
	},


	cacheName(data){
		this.nameCache[data.name_key] = JSON.parse(JSON.stringify(data));
	},


	loadNext(){
		if(this.queue.length == 0){
			this.displayAll();
			return;
		}

		let persRef = this.queue.shift();

		//look for cached name and use that instead
		if(this.nameCache[persRef.husc]){
			this.queueDisplay(persRef.husc, persRef.e);
			this.loadNext();			
			return;
		}

		Yodude.send(Env.apiExtURL + "name?husc=" + persRef.husc)
		.then((resp) => {
			//handle unknowns
			if(Array.isArray(resp.data) && resp.data.length === 0){

				let nameData = this.getUnknownTemplate(persRef.husc);
				this.cacheName(nameData);
				this.queueDisplay(persRef.husc, persRef.e);
			}

			//handle errors
			else if(typeof resp.errors != "undefined" && resp.errors.length){
				return;

			//regular names
			} else {
				//cache the returned response
				this.cacheName(resp.data);
				this.queueDisplay(persRef.husc, persRef.e);
			}

			//check for more
			this.loadNext();
		});
	},

	displayAll(){
		while(this.displayPending.length){
			let item = this.displayPending.shift();
			this.display(this.nameCache[item.husc], item.e);
		}
	},


	display(data, e){
		let template = "";
		if(data.family_name == "" && data.given_name == "" && data.middle_name == "" && data.maiden_name == ""){
			data.birthDate = "";
			data.deathDate = "";
		}

		else {
			let name = CoopHelpers.nameMetadataFormat(data);
			let birthDate = CoopHelpers.formatDate(data.date_of_birth);
			let deathDate = CoopHelpers.formatDate(data.date_of_death);
			data.birthDate = birthDate;
			data.deathDate = deathDate;

			if(data.birth_era && data.birth_era == "bce"){
				data.birthDate += " " + data.birth_era;
				data.deathDate += " " + data.death_era;
			}

			data.displayName = name;
			data.fullDisplayName = CoopHelpers.fullNameFormat(data);
		}

		for(let fn of this.displayListeners){
			fn(template, e, data);
		}
	}

}