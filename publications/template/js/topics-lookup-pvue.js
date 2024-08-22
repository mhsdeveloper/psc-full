let TopicsLookup = {
	props: ['project'],
	expose: ['setFocus', 'selectTopic', "getCompleteTopic", "findTopics", "open", "close"],
	emits: ['add-topic', "close"],
	data() {
		return {
			isOpen: false,
			preselectedTopic: this.preselect,
			projectShortname: this.project.toUpperCase(),
			listShowing: false,
			listMode: "umbrella", // also "search" or "hidden"
			topicShowing: false,
			isSee:false,
			umbrellas: [],
			topicTerm: "",
			topicsNames: [],
			topics: [],
			selectedTopic: "",
			selectedInSOLR: false,
			selectedTopicData: {},
			relationships: {
				broadensTo: [],
				narrowsTo: [],
				seeAlso: []
			},
			topicNameLookup: {},
			allSOLRTopics: [],
			allDBTopicsReady: false,
			allDBTopics: {},
			allDBTopicNames: [],
			allDBTopicsByName: {},
			selectedRelationship: "",
			showDefinition: false,
			delay: null,
		}
	},

	methods: {

		getAllFromDB(callback = null){
			Yodude.send("/subjectsmanager/ext/getallfromdb").then((resp) => {
				this.allDBTopicsReady = true; // mark searched here so only once in case called outside of .findTopics()
				for(let n of resp){
					this.allDBTopics[n.id] = n;
					this.allDBTopicNames.push(n.topic_name);
					this.allDBTopicsByName[n.topic_name] = n;
				}
				if(callback) callback();
			});
		},


		// getUmbrellas(){
		// 	Yodude.send("/subjectsmanager/getumbrellaterms").then((resp) => {
		// 		if(Array.isArray(resp)){
		// 			this.umbrellas = resp;
		// 		}
		// 	});
		// },




		findTopics(){

			if(!this.allDBTopicsReady){
				this.allDBTopicsReady = true; //mark searched here so only once
				this.getAllFromDB(() =>{ 
					this.findTopics();
				});
				return;
			}

			this.topicShowing = false;
			clearInterval(this.delay);
			this.delay = setTimeout(()=>{
				let terms = this.topicTerm;

				if(terms.length == 0){
					this.listShowing = true;
					this.listMode = "umbrella";
				}

				else this.listMode = "search";

				let set = [];

				let test = terms.toLocaleLowerCase();

				//gather all topics in the set
				for(let t of this.allDBTopicNames){
					let topic = t.toLocaleLowerCase();

					if(topic.includes(test)){
						this.allDBTopicsByName[t].showDetails = false;
						set.push(this.allDBTopicsByName[t]);
					}
				}

				this.getAllInSOLR(()=>{
					this.listTopics(set);

					if(this.preselect && this.preselect.length > 0){
						this.selectTopic(this.preselect);
					}
	
				});

			}, 300);
		},


		getAllInSOLR(callback = null){
			let url = "/subjectsmanager/ext/getinsolr";//don't limit this: just get ALL!!
			if(this.project != "coop"){
				let project = Env.projectShortname.toUpperCase();
				url += "?project=" + project;
			}

			if(this.allSOLRTopics.length == 0){
				Yodude.send(url).then((resp) => {
					let topics = Object.keys(resp);
					this.allSOLRTopics = topics;
					if(callback) callback();
				});
			} else {
				callback();
			}
		},



		listTopics(resp){
			this.topicsNames = [];
			this.topics = []
			for(let i=0;i<resp.length; i++){
				if(resp[i].see_id || resp[i].is_umbrella == "1" || this.allSOLRTopics.includes(resp[i].topic_name)){
					this.topicsNames.push(resp[i].topic_name);
					this.topics.push(resp[i]);
				}
			}
			this.listShowing = true;
		},



		/* select topic by name or passing the topic name */
		selectTopic(topic, indx, isUmbrella = false){
			if(typeof topic == "string"){
				topic = this.allDBTopicsByName[topic];
			} else if(topic.see_id){
				topic = this.allDBTopics[topic.see_id];
			}

			this.selectedTopicData = {};
			this.selectedTopic = topic;
			if(this.allSOLRTopics.includes(topic.topic_name)) this.selectedInSOLR = true;
			else this.selectedInSOLR = false;
			//zero relationships
			this.relationships = {
				broadensTo: [],
				narrowsTo: [],
				seeAlso: []
			}

			this.getCompleteTopic(topic.id);
		},



		getCompleteTopic(id){
			this.listShowing = false;
			this.isSee = false;
			this.topicShowing = true;
			Yodude.send("/subjectsmanager/ext/getcompletetopic?name=" + id).then((resp) => {

				this.selectedTopicData = resp;
				//build lookup by id
				let topics = {}
				if(resp.topics) for(let t of resp.topics){
					topics[t.id] = t.topic_name;
				}

				if(resp.relationships) for(let r of resp.relationships){
					if(r.relationship == "broadensTo"){
						if(r.topic_id == r.related_topic_id) continue;
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
		},

		addTopic(topic){
			this.listShowing = false;
			this.$emit('add-topic', topic.topic_name);
		},

		open(topic = ""){
			if(topic.length) this.selectTopic(topic);
			this.isOpen = true;
		},

		close(){
			this.isOpen = false;
		},

		closeMe(){
			this.$emit("close");
		}
	},


	mounted() {
		this.getAllFromDB(()=>{
			this.$refs.myinput.focus();
			this.findTopics();
		});

	},


	template: `
			<div class="lookupTopic" :class="isOpen ? '' : 'hidden'">
				<button class="closer" @click="closeMe"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg></button>

				<div class="lookupRow">
					<label>Find a topic {{ projectShortname == "COOP" ? 'across editions' : 'in ' + projectShortname}}:</label>
					<input v-model="topicTerm" @keyup="findTopics" type="text" ref="myinput" autocomplete="off" placeholder="enter a keyword" />
				</div>

				<label v-if="listShowing && listMode =='umbrella'">Browse Umbrella Terms</label>
				
				<div v-if="listShowing" class="topics">
					<template v-for="topic, indx in topics">
						<div v-if="listMode !='umbrella' || (listMode == 'umbrella' && topic.is_umbrella == '1')" class="topic" :class="topic.see_id ? 'see' : ''"
							@click="selectTopic(topic)">
							
							<template v-if="!topic.see_id">
								<button class="moreStuffToggle">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
								</button>

							</template>


							{{topic.topic_name}} <span class="isUmbrella" v-if="topic.is_umbrella == '1'" title="This is an umbrella term and has narrower, related topics">*</span>
							
							<span v-if="topic.see_id"><i>see</i> <button @click="selectTopic(allDBTopics[topic.see_id])">{{allDBTopics[topic.see_id].topic_name}}</button></span>
						</div>
					</template>
				</div>



				<div v-if="topicShowing" class="selectedTopic">
					<button @click="topicShowing = false; listShowing = true"
						class="textOnly tiny backLink">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
							</svg>
						back to list</button>


					<div class="topicInfo">
						<div class="mainCol">
							<div class="topicTitleBar">
								<span v-if="relationships['broadensTo'].length" class="parentTopicPath">
									<a v-for="r in relationships['broadensTo']" tabindex="0" @keyup.enter="selectTopic(r.topic_name)" @click="selectTopic(r.topic_name)">{{r.topic_name}}</a>
								</span>
								<b  v-if="relationships['broadensTo'].length" class="arrow"> » </b>
								<h3>{{selectedTopic.topic_name}}</h3>
							</div>

							<span class="isUmbrella" v-if="selectedTopic.is_umbrella == '1'"><b>*</b> This is an umbrella term, a broad topic encompassing narrower topics listed below.</span>
							<span v-if="!selectedInSOLR" class="notice">No documents have been tagged with this topic. Editions are tracking this topic and may use it in the future.</span>

							<div v-if="selectedTopicData.consensusDefinition"
							class="definition"
							v-html="selectedTopicData.consensusDefinition"
							></div>

							<div :class="selectedRelationship" class="relationships">
		<!--						<h4 v-if="relationships['broadensTo'].length"  tabindex="0" class="tab" :class="selectedRelationship == 'broaden' ? 'selected': ''"
									@click="selectedRelationship = 'broaden'" 
									@keyup.enter="selectedRelationship = 'broaden'"
									>Choose a broader topic:</h4>
		-->
								<h4 v-if="relationships['narrowsTo'].length"   tabindex="0" class="tab" :class="selectedRelationship == 'narrow' ? 'selected': ''"
									@click="selectedRelationship = 'narrow'" 
									@keyup.enter="selectedRelationship = 'narrow'"
									>Choose a narrower topic:</h4>

								<h4 v-if="relationships['seeAlso'].length" tabindex="0" class="tab"  :class="selectedRelationship == 'seealso' ? 'selected': ''"
									@click="selectedRelationship = 'seealso'" 
									@keyup.enter="selectedRelationship = 'seealso'"
									>Choose a related topic:</h4>



		<!--						<div v-if="relationships['broadensTo'].length" class="broaderTopics relationship">
									<div v-for="r in relationships['broadensTo']" class="broadensTo" tabindex="0"
									@click="selectTopic(r.topic_name, true)" @keyup.enter="selectTopic(r.topic_name, true)" 
									>{{r.topic_name}}</div>
								</div>
		-->
								<div v-if="relationships['narrowsTo'].length"  class="narrowerTopics relationship">
									<div v-for="r in relationships['narrowsTo']" class="narrowsTo" tabindex="0"
										@click="selectTopic(r.topic_name)" @keyup.enter="selectTopic(r.topic_name)"
										>{{r.topic_name}}</div>
								</div>


								<div v-if="relationships['seeAlso'].length" class="seeAlsoTopics relationship">
									<div v-for="r in relationships['seeAlso']" class="seeAlso"
										@click="selectTopic(r.topic_name)" @keyup.enter="selectTopic(r.topic_name)" tabindex="0"
										>{{r.topic_name}}</div>
								</div>
							</div>
						</div>

						<div class="addTopicCol">
							<button v-if="selectedInSOLR" @click="addTopic(selectedTopic)" class="addTopicButton">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
									<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
								</svg>search this topic
							</button>
							<span class="isUmbrella" v-if="selectedTopic.is_umbrella == '1'"><span class="finePrint">Searching this topic will also find documents tagged with the narrower topics; you may also limit your search to one or several of the narrower topics.</span>
							</span>
						</div>

					</div>
				</div>
			</div>
		`
}

export { TopicsLookup }