import { TopicsLookup } from "/publications/template/js/topics-lookup-pvue.js";

export default {

	data(){
		return {
			noOfPopular: 20,
			props: {},
			allTopics: {},
			allDBTopics: {},
			allDBTopicNames: [],
			allDBTopicsByName: {},
			allSOLRTopicsNames: [],
			allSOLRTopics: {},
			crossEditionTopics: [],
			letters: [],
			popularTopics: [],
			projects: {},
			project: Env.projectShortname,
			coopHelpers: null,
			showTopics: false,
			mode: "project",
			getAllFromDBURL: "/subjectsmanager/ext/getallfromdb"
		}
	},

	components: {
		TopicsLookup
	},
	
	methods: {

		createLink(topic){
			let t = encodeURIComponent(topic);
			let part = Env.projectShortname == "coop" ? "" : Env.projectShortname + "/";
			let url = "/publications/" + part + "search#q%3D%2Bsubject%3A\""+ t +"\"";//"/read#0/20/terms//p//s/\"" + t + "\"";
			return url;
		},


		async getAllDBTopics(){
			let resp  = await Yodude.send(this.getAllFromDBURL);
			for(let n of resp){
				this.allDBTopics[n.id] = n;
				this.allDBTopicNames.push(n.topic_name);
				this.allDBTopicsByName[n.topic_name] = n;
			}
			return resp;
		},


		async getAllTopicRelationships(){
			let topics = this.allDBTopics;
			let topicRelationships = await Yodude.send("/subjectsmanager/ext/getalltopicrelationships");

			//group editions that use a topic
			let currentTopicId = -1;
			let topicGroups = {}
			let mostEditionsCount = 0;
			//go through each row and if repeat topic_id, we're in the same group
			for(let row of topicRelationships){

				//next row is a new topic, so move on and matching relationships to master topic list
				if(row.topic_id != currentTopicId){
					
					//update count of topic with most editions
					if(topicGroups[currentTopicId] && topicGroups[currentTopicId].editions.length > mostEditionsCount) mostEditionsCount = topicGroups[currentTopicId].editions.length;

					currentTopicId = row.topic_id;
					
					//find row from topics list
					topicGroups[currentTopicId] = {id: row.topic_id, name: topics[row.topic_id].topic_name, editions: []}
				}
			
				//add to group
				topicGroups[currentTopicId].editions.push(row.project_sitename);
			}

			//now sort by most editions, only those topics assigned to more than one edition
			for(let i=0;i<(mostEditionsCount - 1);i++){
				let count = mostEditionsCount - i;
				let labeled = false;

				let keys = Object.keys(topicGroups);
				for(let j=0; j<keys.length; j++){
					//make sure in SOLR
					if(!this.allSOLRTopicsNames.includes(topicGroups[keys[j]].name)) continue;

					if(topicGroups[keys[j]].editions.length == count){
						if(!labeled){
							labeled = true;
							topicGroups[keys[j]].label = count;
						}
						this.crossEditionTopics.push(topicGroups[keys[j]]);
					}
				}
			}

			return topics;
		},


		async getAllInSolr(){
			let url = "/subjectsmanager/ext/getinsolr";//don't limit this: just get ALL!!
			if(this.mode == "project"){
				let project = Env.projectShortname.toUpperCase();
				url += "?project=" + project;
			}

			let SOLRresp = await Yodude.send(url);

			this.allSOLRTopics = SOLRresp;
			this.allSOLRTopicsNames = Object.keys(SOLRresp);

			return SOLRresp;
		},


		sortDBAndSolr(resp){
			//build most frequent(popular) topics
			//this needs to be separate copy from this.allSOLRTopicsNames
			let unsortedNames = Object.keys(this.allSOLRTopics);
			this.popularTopics = [];
			let overflow = 0;
			while(unsortedNames.length){
				overflow++;
				if(overflow > 9999) break;
				let size = 0;
				let pointer = 0;
				//find largest
				for(let i=0;i<unsortedNames.length; i++){
					if(!this.allDBTopicNames.includes(unsortedNames[i])) continue;
					if(this.allSOLRTopics[unsortedNames[i]] > size){
						size = this.allSOLRTopics[unsortedNames[i]];
						pointer = i;
					}
				}

				this.popularTopics.push(unsortedNames[pointer]);
				unsortedNames.splice(pointer, 1);

				if(this.popularTopics.length == this.noOfPopular) break;
			}

			//get letters for alpha list
			this.letters = [];
			for(let topic of this.allSOLRTopicsNames){
				if(!this.allDBTopicNames.includes(topic)) continue;
				let letter = topic[0];
				if(!this.letters.includes(letter)){
					this.letters.push(letter);
					this.allTopics[letter] = [];
				}

				this.allTopics[letter].push(topic);
			}
		},


		chooseTopic(topic){
			let url = this.createLink(topic);
			location.href = url;
		},

		closeTopic(){
			this.showTopics = false;
			this.$refs.topicbox.close();
		},


		getCompleteTopic(topic){
			this.showTopics = true;
			this.$refs.topicbox.open(topic);







return;
			Yodude.send("/subjectsmanager/ext/getcompletetopic?name=" + topic).then((resp) => {

				this.selectedTopicData = resp;
				
				//build lookup by id
				let topics = {}
				if(resp.topics) for(let t of resp.topics){
					topics[t.id] = t.topic_name;
				}

				if(resp.relationships) for(let r of resp.relationships){
					if(r.relationship == "broadensTo"){
						//selected topic is main id, so it broadensTo related id
						if(r.topic_id == resp.id){
							this.relationships["broadensTo"].push({topic_name: topics[r.related_topic_id], id: parseInt(r.related_topic_id)});
						} else {
							//make sure this topic is in our list from SOLR
							let name = topics[r.topic_id];
							if(this.allSOLRTopics.includes(name)){
								this.relationships["narrowsTo"].push({topic_name: topics[r.topic_id], id: parseInt(r.topic_id)});
							}
						}
					}
				}
				//set initial tab that shows
				if(this.relationships.seeAlso.length) this.selectedRelationship = "seealso";
				if(this.relationships.broadensTo.length) this.selectedRelationship = "broaden";
				if(this.relationships.narrowsTo.length) this.selectedRelationship  = "narrow";
			});
		}
	},


	mounted(){
		this.projects = Projects;
		this.projects = Projects;
		this.coopHelpers = CoopHelpers;

		if(Env.projectShortname == "coop"){
			this.mode = "coopSearch";
			this.getAllFromDBURL += "?order=id";

			this.getAllDBTopics()
			.then(this.getAllInSolr)
			.then(this.getAllTopicRelationships)
			.then(this.sortDBAndSolr)

		} else {

			this.getAllDBTopics()
			.then(this.getAllInSolr)
			.then(this.sortDBAndSolr);
		}
	}

}
