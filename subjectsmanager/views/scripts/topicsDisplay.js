export default {
    components: {
        // EditableNote
    },
	data() {
		return {
          isSuperAdmin: false,
          isEditor: false,
		  allUmbrellas: [],
          allOptions: [],
          currAccordian: [],
          searchResults: [],
          selectedProj: "",
          projTopicData: [],
          recentlyEditedTopics: [],
          groupedData: {},
          currSubject: "",
          currTopicData: {},
          currProjectTopicData: {},
          currUmbrella: "",
          searchString: "",
		  searchTimeout: null,
          editPlaceholder: "Enter text here",
          

          broader: [],
          narrower: [],
          
          publicNoteContent: "",
          publicNoteIsEditing: false,
          publicNoteEditedContent: "",

          internalNoteContent: "",
          internalNoteIsEditing: false,
          internalNoteEditedContent: "",

          isModalOpen: false,

          editSearchString: "",
          editSearchResults: [],
          editTopic: "",

          editTopicData: {},
          editOgUmbTerms: [],

          editIsUmbrella: false,
          editHasSee: false,

          editSee: "",

          editSelectedUmbrellaTerm: "",
         
          editUmbrellaTerms: [],
          editChangedUmbrellaTerms: {
            add: [],
            delete: []
          },
          
          editConsensusDef: "", 

          isSee: false,
          seeTarget: ""

		};
	},
	mounted() {
        this.getProject();
		this.fetchUmbrellas();
        this.setAllOptions();
        this.isSuperAdmin = isSuperAdmin
        this.isEditor = isEditor
        
	},
    // props: {
    //     'selectedProj': String
    // },
    watch: {
        searchString: async function(searchStringVal) {
            if(this.searchString == "*"){
                return this.searchResults = this.allOptions
            }
            if(this.searchString.length < 1) {
                this.searchResults = [];    
                return
            }

			clearTimeout(this.searchTimeout);
			this.searchTimeout = setTimeout(async ()=>{
                const url = `subjectsmanager/searchtopic?search=${this.searchString}`;
                const response = await fetch(url);
            
                if(!response.ok){
                    console.log(response)
                }
                this.searchResults = await response.json();
			}, 300);

            // else if(this.searchString.length === 1){
            //     const url = `subjectsmanager/searchtopic?search=${this.searchString}`

            //     const response = await fetch(url);
            
            //     if(!response.ok){
            //         console.log(response)
            //     }

            //     this.searchResults = await response.json();
            //     // console.log(this.searchResults)
            // // if(this.searchResults.length === 0){
            // //     this.searchResults = ["No Topics Found"];
            // // }
            // } else if(this.searchResults.length > 0){
            //     this.searchResults = this.searchResults.filter((result) => result.topic_name.toLowerCase().includes(searchStringVal.toLowerCase()))
            // }
        },
        editSearchString: async function(editSearchStringVal) {
            if(this.editSearchString.length < 1) {
                this.editSearchResults = [];  
                this.editTopic = ""  
                return
            }

			clearTimeout(this.searchTimeout);
			this.searchTimeout = setTimeout(async ()=>{
                const url = `subjectsmanager/searchtopic?search=${this.editSearchString}`;
                const response = await fetch(url);
            
                if(!response.ok){
                    console.log(response)
                }
                this.editSearchResults = await response.json();
			}, 300);

            // else if(this.editSearchString.length === 1){
            //     const url = `subjectsmanager/searchtopic?search=${this.editSearchString}`

            //     const response = await fetch(url);
            
            //     if(!response.ok){
            //         console.log(response)
            //     }

            //     this.editSearchResults = await response.json();

            //     // console.log(this.editSearchResults)
            // // if(this.editSearchResults.length === 0){
            // //     this.editSearchResults = ["No Topics Found"];
            // // }
            // } else if(this.editSearchResults.length > 0){
            //     this.editSearchResults = this.editSearchResults.filter((result) => result.topic_name.toLowerCase().includes(editSearchStringVal.toLowerCase()))
                
            // }
        },
        selectedProj: async function(){
            if(this.selectedProj){
                this.fetchAllProjectTopics();
                this.fetchRecentlyEditedTopics();
            }
        }
        
    },
	methods: {
        openModal() {
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        async getProject(){
            const url = `subjectsmanager/getproject`
            const response = await fetch(url);

            if(!response.ok){
                console.log(response)
            }

            const data = await response.json();
            const project = data.project.replace("/", "")
            this.selectedProj = project.toUpperCase();
            
        },

        async setAllOptions(){
            const url = `/subjectsmanager/searchtopic?search=`

            const response = await fetch(url);
        
            if(!response.ok){
                console.log(response)
            }

            this.allOptions = await response.json();
        },
        async fetchAllProjectTopics(){
            console.log(this.selectedProj)
            const url = `subjectsmanager/gettopicnames?project=${this.selectedProj}`
            const response = await fetch(url);
            
            // console.log(response)

            if(!response.ok){
                console.log(response)
            }

            this.projTopicData = await response.json();
            
            const sortedData = this.projTopicData.sort((a, b) => a.topic_name.localeCompare(b.topic_name));

            sortedData.forEach(item => {
                const firstLetter = item.topic_name.charAt(0).toUpperCase();
                if (!this.groupedData[firstLetter]) {
                    this.groupedData[firstLetter] = [];
                }
                this.groupedData[firstLetter].push(item);
            });

        },
        async fetchRecentlyEditedTopics(){
            const url = `subjectsmanager/getrecentlyeditedtopics?project=${this.selectedProj}`;

            const response = await fetch(url);
            // console.log(response)

            if(!response.ok){
                console.log(response)
            }

            this.recentlyEditedTopics = await response.json();
        },

		async fetchUmbrellas() {
            const response = await fetch('subjectsmanager/getumbrellaterms');
            // console.log(response)

            if(!response.ok){
                console.log(response)
            }

            this.allUmbrellas = await response.json();
		},
		async fetchSubTopics(topicName, $event) {
            if (this.currUmbrella === topicName){
                this.currUmbrella = "";
                this.currAccordian = [];
                $event.target.classList.remove('active');
                return;
            }

            const url = `subjectsmanager/getsubtopics?topic=${topicName}`
			const response = await fetch(url);
        
            if(!response.ok){
                console.log(response)
            }

            this.currAccordian = await response.json();

            this.currUmbrella = topicName;

            Array.from(document.getElementsByClassName("termtitle")).forEach(
                function(element, index, array) {
                    element.classList.remove('active');
                }
            );

            $event.target.classList.add('active');

            await this.setTopic(topicName)
		},
        async getNarrower(topicName){
            const url = `subjectsmanager/getsubtopics?topic=${topicName}`
			const response = await fetch(url);
        
            if(!response.ok){
                console.log(response)
            }

            const result = await response.json();
            
            return result 
        },


        async getBroader(topicName){
            const url = `subjectsmanager/getbroadertopics?topic=${topicName}`
            const response = await fetch(url);
        
            if(!response.ok){
                console.log(response)
            }

            const result = await response.json();
            
            return result 
        },

		async search(){
            if(this.searchString.length < 1) {
                this.searchResults = [];    
                return
            };
			const url = `subjectsmanager/searchtopic?search=${this.searchString}`

            const response = await fetch(url);
        
            if(!response.ok){
                console.log(response)
            }

            this.searchResults = await response.json();
            
            // if(this.searchResults.length === 0){
            //     this.searchResults = ["No Topics Found"];
            // }
			
		},

        async removeTopic(){
            // const payload = {
            //     topic_id: this.currTopicData.id,
            //     project: this.selectedProj
            // }
            

            // const data = new FormData();
            // data.append( "json", JSON.stringify( payload ) );

            fetch(`subjectsmanager/deleteprojecttopicrelationship?topic_id=${this.currTopicData.id}&project=${this.selectedProj}`,
            {
                method: "DELETE",
            })
            .then(function(res){ return res.json(); })
            .then(function(data){ alert( JSON.stringify( data )) })

            console.log(this.currTopicData)
            console.log(this.projTopicData[0])

            let newCurrProjTopicData = this.projTopicData.filter(data =>data.topic_name !== this.currTopicData.topic_name)
            this.projTopicData = newCurrProjTopicData

        },
 
        async addTopic(){
            // const payload = {
            //     topic_id: this.currTopicData.id,
            //     project: this.selectedProj
            // }
            

            // const data = new FormData();
            // data.append( "json", JSON.stringify( payload ) );

            fetch(`subjectsmanager/createprojecttopicrelationship?topic_id=${this.currTopicData.id}&project=${this.selectedProj}`,
            {
                method: "POST",
            })
            .then(function(res){ return res.json(); })
            .then(function(data){ alert( JSON.stringify( data )) })

            this.projTopicData.push({"topic_name": this.currTopicData.topic_name})

        },

        async setEditTopic(topicName, newCreationBool){
            if (newCreationBool){
                this.editTopicData = {}
                this.editOgUmbTerms = [],
                this.editIsUmbrella = false,
                this.editHasSee = false,
                this.editUmbrellaTerm = ""
                this.editConsensusDef = ""
                this.editUmbrellaTerms = []
                this.editSee = ""
                this.editChangedUmbrellaTerms = {
                        add: [],
                        delete: []
                    }
            } else{
                const url = `subjectsmanager/alltopicdata?topic=${topicName}`;
                const topicRes = await fetch(url);
    
                const data = await topicRes.json();
                this.editTopicData = data[0]
    
                this.editIsUmbrella = this.editTopicData.is_umbrella == 1
                this.editConsensusDef = this.editTopicData.consensusDefinition || ""
                this.editSee = this.editTopicData.see_id || ""
    
                if (data[0].is_umbrella && data[0].is_umbrella == 0){
                    const result = await this.getBroader(topicName) 
                    this.editOgUmbTerms = result.map(item=>item.topic_name)
                    this.editUmbrellaTerms = result.map(item=>item.topic_name)
                }
            }

            this.editTopic = topicName
            this.editSearchResults = []; 
            
            
        },

        addUmbrella(){
            if (this.editSelectedUmbrellaTerm && !this.editUmbrellaTerms.includes(this.editSelectedUmbrellaTerm)) {
                this.editUmbrellaTerms.push(this.editSelectedUmbrellaTerm); 
            }
            if(this.editOgUmbTerms.indexOf(editSelectedUmbrellaTerm) !== 0){
                this.editChangedUmbrellaTerms.add.push(this.editSelectedUmbrellaTerm)
            }
        },

        setEditUmb(topicName){
            this.editUmbrellaTerms.append(topicName)
        },

        // setEditSeeOption(topicName){
        //     this.editSee = topicName
        //     console.log(topicName)
        // },

        deleteEditUmb(topicName){
            const index = this.editUmbrellaTerms.indexOf(topicName);
            const inOg = this.editOgUmbTerms.indexOf(topicName)
            if(inOg !== -1){
                this.editChangedUmbrellaTerms.delete.push(topicName)
            }
            if (index !== -1) {
                this.editUmbrellaTerms.splice(index, 1);
            }
        },

        async getTopidId(topicName){
            const url = `subjectsmanager/gettopicid?topic=${topicName}`

            const response = await fetch(url);
        
            if(!response.ok){
                console.log(response)
            }

            const result = await response.json();
            console.log(result[0].id)
            return result[0].id
            
        },
        async saveEdit(){
            let url
            
            if ( this.editTopicData != {} ){
                if(this.editSee.length > 0){
                    const seeId = this.getTopidId(this.editSee)
                    url = `subjectsmanager/updatetopic?topic_id=${this.editTopicData.id}&topic=${this.editTopic}&consensusDefinition=&see=${seeId}`
                } else{
                    url = `subjectsmanager/updatetopic?topic_id=${this.editTopicData.id}&topic=${this.editTopic}&consensusDefinition=${this.editConsensusDef}&see=${this.editSee}`
                }                
                fetch(url,
                    {
                        method: "PUT",
                    })
                    .then(function(res){ return res.json(); })
                    .then(function(data){ alert( JSON.stringify( data )) })       
            } else{
                if(this.editSee.length > 0){
                    const seeId = this.getTopidId(this.editSee)
                    url = `subjectsmanager/createtopic?topic_id=${this.editTopicData.id}&topic=${this.editTopic}&consensusDefinition=&see=${seeId}`
                }
                url = `subjectsmanager/createtopic?&topic=${this.editTopic}&consensusDefinition=${this.editConsensusDef}`
                fetch(url,
                    {
                        method: "POST",
                    })
                    .then(function(res){ return res.json(); })
                    .then(function(data){ alert( JSON.stringify( data )) })
            }

            //add new topic-topic relationships
            for (let i = 0; i < this.editChangedUmbrellaTerms.add.length; i++){
                const umbTopic = this.editChangedUmbrellaTerms.add[i]
                console.log(umbTopic)
                const relatedId = await this.getTopidId(umbTopic)
                const url = `subjectsmanager/createtopicrelationship?topic_id=${this.editTopicData.id}&relationship=broadensTo&related_topic_id=${relatedId}`
                fetch(url,
                    {
                        method: "POST",
                    })
                    .then(function(res){ return res.json(); })
                    .then(function(data){console.log( data ) })
            }

            //delete topic-topic relationships
            for (let i = 0; i < this.editChangedUmbrellaTerms.delete.length; i++){
                const umbTopic = this.editChangedUmbrellaTerms.delete[i]
                console.log(umbTopic)
                const relatedId = await this.getTopidId(umbTopic)
                const url = `subjectsmanager/deletetopicrelationship?topic_id=${this.editTopicData.id}&relationship=broadensTo&related_topic_id=${relatedId}`
                fetch(url,
                    {
                        method: "DELETE",
                    })
                    .then(function(res){ return res.json(); })
                    .then(function(data){console.log( data ) })
            }

        },

        async setTopic(topicName){
            this.currSubject = topicName;
            const url = `subjectsmanager/alltopicdata?topic=${topicName}`;
            const topicRes = await fetch(url);

            const data = await topicRes.json();
            this.currTopicData = data[0]

            if (this.currTopicData.see_id != null){
                console.log(this.currTopicData.see_id)
                this.isSee = true
                
                const url = `subjectsmanager/gettopicnamebyid?id=${this.currTopicData.see_id}`
                const res = await fetch(url);
                
                const seeData = await res.json(url);

                this.seeTarget = seeData[0].topic_name; 
                console.log(seeData[0].topic_name)
                this.currSubject = this.seeTarget
                
                return
            } else{
                this.isSee == false
                this.seeTarget = ""
            }

            this.broader = []
            this.narrower = []

            if (data[0].is_umbrella && data[0].is_umbrella == 1){
                
                const result = await this.getNarrower(topicName)
                this.narrower = result

                // const li = document.createElement('li');
                // li.appendChild(document.createTextNode("There are no topics broader than this one"));
                // broader.appendChild(li);
            } else{
                const result = await this.getBroader(topicName) 
                this.broader = result
                // if (result.length > 0){
                //     for (let i = 0; i < result.length; i++) {
                //         const topic = result[i].topic_name;
                //         const li = document.createElement('li');
                //         li.appendChild(document.createTextNode(topic));
                //         broader.appendChild(li);
                //     }
                // } else {
                //     console.log("no broader result is empty" + result)
                //     broader.parentElement.style.display = 'none'
                // }
                // const li = document.createElement('li');
                // li.appendChild(document.createTextNode("There are no topics narrower than this one"));
                // narrower.appendChild(li);
                // narrower.parentElement.style.display = 'none'
            }

            const url2 = `subjectsmanager/getprojecttopicrelationship?project=${this.selectedProj}&topic_id=${this.currTopicData.id}`;
            const projTopRes = await fetch(url2);

            let data2 = await projTopRes.json();
            // console.log(data2[0]);
            

            if (data2[0]) {
                // if (data2[0].see_id != null){
                //     seeAlso
                // }
                if ( data2[0].publicNote){
                    this.publicNoteContent = data2[0].publicNote
                } else{
                    this.publicNoteContent =  ""
                }        
    
                if(data2[0].internalNote){
                    this.internalNoteContent = data2[0].internalNote;
                } else{
                    this.internalNoteContent =  ""
                } 
            }
            

            window.scrollTo({ top: 0, behavior: 'smooth' });
            // this.searchString = ""
        },

        unclampProjTopicDisplay(){
            this.innerHTML = "(Collapse)";
            const $el = document.getElementById("projectTopicDisplay");
            $el.classList.add('noClamp')
        },

        onInput(sourceString, event) {
            this.localContent = event.target.innerText;
            
            this.$emit(sourceString, this.localContent);
        },

        updateContent(newContent) {
            this.content = newContent;
        },
        viewAll(){
            this.currSubject = "";
            this.currTopicData = {};
        },
        publicNoteToggleEdit() {
            console.log("toggled")
            this.publicNoteIsEditing = true;
            this.publicNoteEditedContent = this.publicNoteContent;
        },
        publicNoteSaveChanges() {
            const url = `subjectsmanager/updateprojecttopicrelationship?topic_id=${this.currTopicData.id}&project=${this.selectedProj}&description=&internalNote=${this.internalNoteContent}&publicNote=${this.publicNoteEditedContent}`
            
            console.log(url)

            fetch(url,
            {
                method: "POST",
            })
            .then(function(res){ return res.json(); })
            .then(function(data){ alert( JSON.stringify( data )) })
           // Trigger API call with this.editedContent
           
            this.publicNoteIsEditing = false;
            this.publicNoteContent = this.publicNoteEditedContent
            
        },

        internalNoteToggleEdit() {
            console.log("toggled")
            this.internalNoteIsEditing = true;
            this.internalNoteEditedContent = this.internalNoteContent;
        },
        internalNoteSaveChanges() {
            const url = `subjectsmanager/updateprojecttopicrelationship?topic_id=${this.currTopicData.id}&project=${this.selectedProj}&description=&internalNote=${this.internalNoteEditedContent}&publicNote=${this.publicNoteContent}`
            
            console.log(url)
        
            fetch(url,
            {
                method: "POST",
            })
            .then(function(res){ return res.json(); })
            .then(function(data){ alert( JSON.stringify( data )) })
           
            this.internalNoteIsEditing = false;
            this.internalNoteContent = this.internalNoteEditedContent
            
        }

	}
  };