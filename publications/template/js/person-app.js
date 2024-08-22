export default {
    data() {
        return {
			showDescriptions: false,
            person: {
                displayName: "",
                given_name: "",
                family_name: "",
				middle_name: "",
                maiden_name: "",
                displayBirth: "",
                displayDeath: "",
				displayTitle: "",
				displayProfessions: "",
				variants: "",
				description: "",
				descriptions: [],
				links: [],
				projects: {},
				env: {}
            },
        }
    },

    methods: {
		pruneOddChars(text){
			text = text.trim();
			if(text[0] == ";") text = text.substring(1);
			if(text[text.length -1] == ";") text = text.substring(0, text.length -1);
			text = text.trim();
			return text;
		},

        renderNameCard(resp){
// console.log(JSON.stringify(resp));
		
			let DEBUG = false;

			if(DEBUG){
				resp = {
					"data": {
						"id": "95",
						"family_name": "Adams",
						"given_name": "Abigail",
						"middle_name": "",
						"maiden_name": "Smith",
						"suffix": "",
						"date_of_birth": "1744",
						"date_of_death": "1818",
						"created_at": "2021-11-16 15:13:08",
						"updated_at": "2021-12-10 18:44:11",
						"first_created_by": "excel",
						"name_key": "adams-abigail",
						"title": "",
						"variants": "AA",
						"professions": "",
						"identifier": "jqa: CJT",
						"first_mention": "1789-12-05|jqa",
						"birth_ca": "0",
						"death_ca": "0",
						"birth_era": "ce",
						"death_era": "ce",
						"verified": "NEM 1/3/2020",
						"sort_birth": "CE17440000",
						"sort_name": "AdamsAbigailAA",
						"descriptions": []
					},
					"errors": [],
					"messages": [],
					"html": ""
				}
			}

			this.person.displayName = CoopHelpers.nameMetadataFormat(resp.data);
			this.person.displayBirth = CoopHelpers.formatDate(resp.data.date_of_birth);
			this.person.displayDeath = CoopHelpers.formatDate(resp.data.date_of_death);
			this.person.maiden_name = resp.data.maiden_name;
			this.person.middle_name = resp.data.middle_name;
			this.person.displayTitle = this.pruneOddChars(resp.data.title);
			this.person.displayProfessions = this.pruneOddChars(resp.data.professions);
			this.person.variants = this.pruneOddChars(resp.data.variants);

			for(let l of resp.data.links){
				let link = {authority: "", title: "", url: "", updated_at: "", type: "", notes: "", not_found: "", authority_id: ""}
				if(l.authority && l.authority.length){
					link.authority = l.authority;
					link.title = l.authority;
					link.authority_id = l.authority_id;
				} else if (l.display_title && l.display_title.length) link.title = l.display_title;
				else continue;

				link.url = l.url;
				link.not_found = l.not_found;
				link.updated_at = l.updated_at;
				link.type = l.type;
				link.notes = l.notes;

				this.person.links.push(link);
			}


			//find project description
			for(let d of resp.data.descriptions){
				if(d.project_name == Env.projectShortname){
					this.person.description = d.notes;
				} else if(d.notes && d.notes.length && d.public == "1") {
					this.person.descriptions.push(d);
				}
			}

			if((DEBUG)){
				this.person.description = "Qui ei tacimates quaestio. Oporteat dignissim posidonium eu pro, usu facilis intellegebat eu. Sea mucius feugiat scaevola te. Unum essent impedit his ne, pri te voluptua constituam.";

				this.person.displayTitle = "Professor Emeritus";
				this.person.displayProfessions = "Home maker; entrepeneur";

				this.person.descriptions.push({
					project_id: "2",
					project_name: "cms",
					notes: "Qui ei tacimates quaestio. Oporteat dignissim posidonium eu pro, usu facilis intellegebat eu. Sea mucius feugiat scaevola te. Unum essent impedit his ne, pri te voluptua constituam."
				})
			}

			if(this.person.description.length || this.person.descriptions.length) this.showDescriptions = true;
        }
    },

    mounted(){
		this.projects = Projects;
		this.env = Env;

		//person only
		if(husc.length){
			let docs = document.getElementById("projectDocs");
			docs.parentNode.classList.add("hidden");
			let topics = document.getElementById("projectTopics");
			topics.parentNode.classList.add("hidden");

			let tokens = [];
			if(Env.projectShortname == "coop") tokens.push("allProjects");

			METACI.documentsByName(husc, tokens,{ element: docs});
			METACI.subjectsByName(husc, tokens,{ element: topics});
			METACI.finalCallback = (results) => {
				if(results.docs.length) docs.parentNode.classList.remove("hidden");
				if(results.subjects.length) topics.parentNode.classList.remove("hidden");
			}

            let viewBlock = {
                callback: this.renderNameCard
            }
            METACI.addNameCard(husc, null, viewBlock);
            METACI.run();
		}
    }
}