let TopicsLookup = {
	props: [],
	data() {
		return {
			listShowing: false,
			topicShowing: false,
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

			allSOLRTopics: [],

			selectedRelationship: "",
			showDefinition: false,
			delay: null
		}
	},

	methods: {
		getAllInSOLR(callback = null){
			if(this.allSOLRTopics.length){
				if(callback) return callback();
				else return;
			}

			let project= Env.projectShortname.toUpperCase();
			Yodude.send("/subjectsmanager/ext/getallinsolr?project=" + project).then((resp) => {
				this.allSOLRTopics = Object.keys(resp);
				if(callback) callback();
			});
		},


		findTopics(){
			this.topicShowing = false;
			clearInterval(this.delay);
			this.delay = setTimeout(()=>{
				let terms = encodeURIComponent(this.topicTerm);
				let project= Env.projectShortname.toUpperCase();
				Yodude.send("/subjectsmanager/ext/findtopic?terms=" + terms + "&project=" + project).then((resp) => {
					this.getAllInSOLR( ()=>{
						this.listTopics(resp);
					});
				});
			}, 300);
		},


		listTopics(resp){
			this.topicsNames = [];
			this.topics = []

			for(let i=0;i<resp.length; i++){
				if(resp[i].is_umbrella == "1" || this.allSOLRTopics.includes(resp[i].topic_name)){
					this.topicsNames.push(resp[i].topic_name);
					this.topics.push(resp[i]);
				}
			}
			this.listShowing = true;
		},



		selectTopic(topic, isUmbrella = false){
			this.selectedTopicData = {};
			this.selectedTopic = topic;//e.target.getAttribute("data-topic-name");
			if(this.allSOLRTopics.includes(topic)) this.selectedInSOLR = true;
			else this.selectedInSOLR = false;
			this.listShowing = false;
			this.topicShowing = true;
			//zero relationships
			this.relationships = {
				broadensTo: [],
				narrowsTo: [],
				seeAlso: []
			}

			this.getCompleteTopic();
		},


		getCompleteTopic(){
			Yodude.send("/subjectsmanager/ext/getcompletetopic?name=" + this.selectedTopic).then((resp) => {

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
		},

		addTopic(topic){
			this.listShowing = false;
			this.$emit("add-topic", topic);
		}

	},

	template: `
		<div class="topicLookup">
		<label>Find a topic:</label>
			<input v-model="topicTerm" @keyup="findTopics" type="text" />

			<div v-if="listShowing" class="topics">
				<div v-for="topic in topicsNames" class="topic"
					tabindex="0" @click="selectTopic(topic)" @keyup.enter="selectTopic(topic)"
					:data-topic-name="topic"
				>{{topic}}
				
				<button v-if="allSOLRTopics.includes(topic)" @click="addTopic(topic)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
			  </svg>
			  </button>
				</div>
			</div>

			<div v-if="topicShowing" class="selectedTopic topic">
				<button @click="topicShowing = false; listShowing = true"
					class="textOnly tiny">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
						</svg>
					back to list</button>
				<h3>{{selectedTopic}}
					<button v-if="selectedTopicData.consensusDefinition || selectedTopicData.publicNote"
						class="tiny clear"
						@click="showDefinition"	
					>
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
						</svg>
					</button>

					<button v-if="selectedInSOLR" @click="addTopic(selectedTopic)">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
				  		</svg>
				  </button>
				</h3>

				<div :class="selectedRelationship" class="relationships">
					<h4 v-if="relationships['broadensTo'].length"  tabindex="0" class="tab" :class="selectedRelationship == 'broaden' ? 'selected': ''"
						@click="selectedRelationship = 'broaden'" 
						@keyup.enter="selectedRelationship = 'broaden'"
						>Broadens to</h4>

					<h4 v-if="relationships['narrowsTo'].length"   tabindex="0" class="tab" :class="selectedRelationship == 'narrow' ? 'selected': ''"
						@click="selectedRelationship = 'narrow'" 
						@keyup.enter="selectedRelationship = 'narrow'"
						>Narrows to</h4>

					<h4 v-if="relationships['seeAlso'].length" tabindex="0" class="tab"  :class="selectedRelationship == 'seealso' ? 'selected': ''"
						@click="selectedRelationship = 'seealso'" 
						@keyup.enter="selectedRelationship = 'seealso'"
						>see also</h4>



					<div v-if="relationships['broadensTo'].length" class="broaderTopics relationship">
						<div v-for="r in relationships['broadensTo']" class="broadensTo" tabindex="0"
						@click="selectTopic(r.topic_name, true)" @keyup.enter="selectTopic(r.topic_name, true)" 
						>{{r.topic_name}}</div>
					</div>

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
		</div>`
}

export { TopicsLookup }