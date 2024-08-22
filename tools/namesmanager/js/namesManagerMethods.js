
NamesManager.methods = {

	chooseFields(){
		if(!this.showFieldChoices) this.showFieldChoices = true;
		else this.showFieldChoices = false;
	},

	toggleField(){

	},

    showLoading(){
      this.loadingDW.canv.classList.add("show");
      this.loadingDW.resume();
    },

    hideLoading(){
      this.loadingDW.canv.classList.remove("show");
      this.loadingDW.pause();
    },

    setNameKey(){
      if(this.autoNameKey){
        this.name.name_key = this.name.family_name + "-" + this.name.given_name;
        this.checkNameKey();
      }
    },

    stopAutoNamekey(){
      this.autoNameKey = false;
    },

    checkNameKey () {
      clearTimeout(this.checkNameKeyTimer);
      this.checkNameKeyTimer = setTimeout(() => {
        this.callNameKeyChecker();
      }, 400);
    },

    async callNameKeyChecker(){
      if(this.name.name_key == ""){
        this.suggestNameFlag = false; return;
      }
        this.name.name_key = this.name.name_key.toLowerCase().replace(/\s/g, "").replace(/[!@#$%^&*`'";,<.>/?:+=_~(){}\[\]|\\]/g, "");
        const response = await axios.get(this.baseURL + 'names/name-key-available?q=' + this.name.name_key);
        this.nameKeyAvailable = (response.data === 0);
		if(!this.nameKeyAvailable) this.suggestNameFlag = true;
		else this.suggestNameFlag = false;
    },

    async suggestNameKey () {
      this.name.name_key = this.name.name_key.toLowerCase().replace(/\s/g, "").replace(/[!@#$%^&*`'";,<.>/?:+=_~(){}\[\]|\\]/g, "");
      const response = await axios.get(this.baseURL + 'names/name-key-suggest?q=' + this.name.name_key);
      this.name.name_key = response.data;
	  this.callNameKeyChecker();
    },

    resetNameObj () {
      this.name = {
        id: null,
        name_key: "",
        family_name: "",
        given_name: "",
        maiden_name: "",
        middle_name: "",
        suffix: "",
        keywords: "",
        variants: "",
        professions: '',
        title: "",
        date_of_birth: "",
        date_of_death: "",
        birth_ca: false,
        death_ca: false,
        birth_era: "ce",
        death_era: "ce",
        public_notes: "",
        staff_notes: "",
        projectmetadata: [],
        notes: [],
		visible: false
      }
    },

    async viewGroup (event, group) {
      this.drawerContent = 'group'
      this.loading = true
      this.loadingGroupNames = true
      this.drawerOpen = true

      try {
        const response = await axios.get(this.baseURL + 'lists/' + group.id)
        this.group = response.data
      } catch (error) {
        console.error(error)
      }
      
      this.loading = false
      this.loadingGroupNames = false
    },
    async deleteGroup (group = null)
    {
      group = (group) ? group : this.group
      this.$q.dialog({
        title: 'Confirm',
        message: `Are you sure you want to delete ${group.name}?`,
        cancel: true,
        persistent: true
      }).onOk(async () => {
        try {
          this.loading = true
          await axios.delete(this.baseURL + 'lists/' + group.id)
          await this.getGroups()
          this.drawerOpen = false
          this.loading = false
        } catch (error) {
          this.loading = false
          this.$q.notify({
            message: 'Sorry, something went wrong.',
            color: 'red'
          }, this)
        }
      })
    },

	searchboxKeys(event){
		if(event.key == "Enter") this.search();
		else if(this.drawerOpen) this.drawerOpen = false;
	},

  addSearchField(){
    this.searchFields.push({value: "", field: "any"});
  },

  removeSearchField(e){
    if(!e.srcElement) return;
    let i = e.srcElement.getAttribute("data-index");
    this.searchFields.splice(i, 1);
  },
  
  async search (page = 1) {
	  if(typeof page == "undefined") page = this.pagination.page;
      else this.pagination.page = page;
      this.saveSettings();
       //assemble query
      let query = "";
      for(let i=0;i<this.searchFields.length;i++){
        query += "&" + this.searchFields[i].field + "=";
        if(this.searchFields[i].field == "date_of_birth"){
          this.searchFields[i].value =  this.searchFields[i].value.substr(0, 4);
        }
        query += this.searchFields[i].value;
      }

      query += "&sort=" + this.searchSort;
	  if(this.limitProject) query += "&projectonly=1";

      this.drawerContent = 'search';
      this.loading = true;
      this.searchResultsData = [];
      this.searchResultsLoading = true;
      this.showLoading();
      this.drawerOpen = true;
      let request = "";
      if (this.searchOnlyMyProject) {
        request = "projects/" + this.project + "/";
      }
      try {
        const response = await axios.get(this.altapiURL + request + 'names/search' + '?per_page=' + this.pagination.rowsPerPage + '&page=' + this.pagination.page + query);//'&q=' + this.namesFilter)
        //collate notes into names

		for(let name of response.data.data){
	      name.notes = [];
		  name.selected = false;
		  name.showCheck = true;

		  //look if name in project
		  if(this.limitProject){
			  name.inProject = true;
		  } else if(response.data.inProjectIds.indexOf(name.id) > -1){
			name.inProject = true;
			name.showCheck = false;
		  } else name.inProject = false;
          
          for(let i=0; i < response.data.staffNotes.length;){
            let note = response.data.staffNotes[i];
            if(note.name_id == name.id){
              let n = response.data.staffNotes.splice(i, 1)[0];
              name.notes.push(n);
              continue;
            }
            i++;
          }

	        name.projectmetadata = [];
          
          for(let i=0; i < response.data.publicNotes.length;){
            let note = response.data.publicNotes[i];
            if(note.name_id == name.id){
              let n = response.data.publicNotes.splice(i, 1)[0];
              name.projectmetadata.push(n);
              continue;
            }
            i++;
          }
        }

        this.searchResultsData = response.data.data;
        this.pagination.rowsNumber = response.data.count;
        this.pagination.prevPage = this.pagination.page > 1 ? this.pagination.page - 1 : 0;
        let totalPages = Math.ceil(this.pagination.rowsNumber / this.pagination.rowsPerPage);
        this.pagination.totalPages = totalPages;
        this.pagination.nextPage = this.pagination.page < (totalPages) ? this.pagination.page + 1 : 0;
        this.hideLoading(); 

      } catch (error) {
        console.error(error)
      }
      this.loading = false
      this.searchResultsLoading = false
    },

	changeLimitProject(){

		this.updateResults();
	},

	updateResults(){
		setTimeout(()=>{this.search(this.pagination.page)}, 200);
	},


    copyNameKey (name_key) {
      Quasar.copyToClipboard(name_key).then(() => {
        this.$q.notify({
          message: 'name-key copied! Ready to be pasted anywhere, in any application, including XML!',
          color: 'green'
        }, this)
      })
    },

    saveAndContinue (){
      this.continue = true;
      this.saveName();
    },

    async saveName () {
      let name_key = this.name.name_key;
      if(this.name.family_name == "" && this.name.given_name == "" && this.name.variants == ""){
alert("A name must have a value for either family, given, or variants");
        return;
      }

      this.$refs.nameForm.validate().then(async success => {
        if (success) {
          this.loading = true
		      let response;
console.log("MODE: " + this.modeNameModal);
          try {
            if (this.modeNameModal === 'Add') {
              response = await axios.post(this.baseURL + 'names', this.name)

            }else{
              response = await axios.patch(this.baseURL + 'names/' + this.name.id, this.name)
              this.search();//this.reInjectName();
            }

            this.$q.notify({
              message: (this.modeNameModal === 'Add') ? 'Name created successfully' : `${this.name.given_name} ${this.name.family_name} updated successfully`,
              color: 'green'
            }, this)

            this.copyNameKey(name_key);

            this.isExistingName = true;
            this.loading = false;
            this.showEditNameModal = false;

            if(this.continue){
              this.continue = false;
              this.name = response.data;
              this.editExistingName();
            }

          } catch (error) {
console.log(error);
            this.loading = false;
            let status = error.status || error.response.status;
            let data = error.data || error.response.data;
            if (status === 422) {
              for (const [key, value] of Object.entries(data)) {
                this.$q.notify({
                  message: value,
                  color: 'red'
                }, this)
              }
            }
          }
        }
      })
    },

    newName(){
      this.resetNameObj();
      this.isExistingName = false;
      this.modeNameModal = 'Add';
console.log("NEW NAME");
      this.autoNameKey = true;
      this.editName();
    },

    editExistingName(){
      this.isExistingName = true;
      this.modeNameModal = 'Edit';
console.log("EDITIN EXISTING NAME");
      this.autoNameKey = false;
      this.editName();
    },

    editName () {
      this.continue = false;
      //find description for this project
      for(let desc of this.name.projectmetadata){
        if(desc.project_id == this.project){
          this.name.public_notes = desc.notes;
          break;
        }
      }

      //find notes for this project
      for(let note of this.name.notes){
        if(note.project_id == this.project){
          this.name.staff_notes = note.notes;
          break;
        }
      }

      this.showEditNameModal = true
      this.showNameModal = false
    },
    
    async viewName (name){
      try {
        let response = await axios.get(this.baseURL + 'names/' + name.id);

        //reset view ids to our project
        this.viewingDescription = this.project;
        this.viewingNote = this.project;
        this.showNameModal = true;
        this.name = response.data;// name; //{...name}

        if(this.name.birth_ca === 1) this.name.birth_ca = true;
        else this.name.birth_ca = false;
        if(this.name.death_ca === 1) this.name.death_ca = true;
        else this.name.death_ca = false;

		for(let desc of this.name.projectmetadata){
			if(desc.project_id == this.project){
				if(desc.public == 1) this.name.visible = true;
				else this.name.visible = false;
			  break;
			}
		  }
      } catch(error) {
        console.log(error);
      }
    },
    viewSubject () {

    },
    async deleteName (name = null)
    {
      name = (name) ? name : this.name
      this.$q.dialog({
        title: 'Confirm',
        message: `Are you sure you want to delete ${name.given_name} ${name.family_name}?`,
        cancel: true,
        persistent: true
      }).onOk(async () => {
        try {
          this.loading = true
          await axios.delete(this.baseURL + 'names/' + name.id)
          await this.getProjectNames()
          this.loading = false
        } catch (error) {
          this.loading = false
          this.$q.notify({
            message: 'Sorry, something went wrong.',
            color: 'red'
          }, this)
        }
      })
    },    
    editLink (link = null) {
      this.showEditLinkModal = true
      if(link) {
        this.linkMode = "edit";
        this.link = {...link}
  	  }else{
        this.linkMode = "new";
        this.link = {
          authority: null,
          authority_id: null,
          display_title: null,
          type: null,
          url: null
        }
      }
    },
    async saveLink () {
      this.loading = true
	    let response;
      if(!this.link.url) this.link.url = "";
      else if(this.link.url.indexOf("http") == -1){
        this.link.url = "http://" + this.link.url;
      }
      try {
        if (this.link.id) {
          response = await axios.patch(this.baseURL + 'links/' + this.link.id, this.link)
        } else {
          this.link.linkable_type = 'Models\\Name'
          this.link.linkable_id = this.name.id
          response = await axios.post(this.baseURL + 'links', this.link)
        }

   	  	this.updateLink(this.link);
//        this.name = this.nameData.find(name => name.id === this.name.id)
        this.loading = false
        this.showEditLinkModal = false

      } catch (error) {
		console.log(error);
		this.loading = false
        this.showEditLinkModal = false
        if (error.response && error.response.status === 422) {
          for (const [key, value] of Object.entries(error.response.data)) {
            this.$q.notify({
              message: value,
              color: 'red'
            }, this)
          }
        }
      }
    },

	async deleteLink (link) {
      this.$q.dialog({
        title: 'Confirm',
        message: `Are you sure you want to delete this link?`,
        cancel: true,
        persistent: true
      }).onOk(async () => {
        try {
          this.loading = true
          await axios.delete(this.baseURL + 'links/' + link.id)
          this.loading = false
          let links = this.name.links;
          for(let i=0;i<links.length; i++){
            if(links[i].id == link.id){
              links.splice(i,1);
              break;
            }
          }
        } catch (error) {
          this.loading = false
          this.$q.notify({
            message: 'Sorry, something went wrong.',
            color: 'red'
          }, this)
        }
      })
    },

	updateLink(link){
		let links = this.name.links;
    if(this.linkMode == "edit")	for(let i=0;i<links.length; i++){
			if(links[i].id == link.id){
				links[i] = link;
				return;
			}
    } else {
console.log("new name");
      this.name.links.push(link);
    
		}
	},

    editGroup(group = null) {
      this.modeGroupModal = (group) ? 'Edit' : 'Add'
      this.showGroupModal = true
      if (group === null) {
        this.group = {
          id: null,
          project_id: 1,
          type: 'name',
          name: null,
          names: []
        }
      }
    },
    async saveGroup () {
      this.$refs.groupForm.validate().then(async success => {
        if (success) {
          this.loading = true
          try {
            if (this.modeGroupModal === 'Add') {
              const response = await axios.post(this.baseURL + 'lists', this.group)
            }else{
              const response = await axios.patch(this.baseURL + 'lists/' + this.group.id, this.group)
            }
            await this.getGroups()
            
            this.$q.notify({
              message: (this.modeGroupModal === 'Add') ? 'Group created successfully' : `${this.group.name} updated successfully`,
              color: 'green'
            }, this)

            this.loading = false
            this.showGroupModal = false
          } catch (error) {
            this.loading = false
            if (error.response.status === 422) {
              for (const [key, value] of Object.entries(error.response.data)) {
                this.$q.notify({
                  message: value,
                  color: 'red'
                }, this)
              }
            }
          }
        }
      })
    },
    async removeNamesFromGroup() {
      this.loading = true

      this.selectedNames.forEach(async name => {
        try {
          await axios.patch(this.baseURL + 'lists/' + this.group.id + '/name', {
            name_id: name.id
          })
          this.group.names = this.group.names.filter(obj => obj.id !== name.id);
        } catch (error) {
            this.$q.notify({
              message: 'Sorry, something went wrong',
              color: 'red'
            }, this)
        }
      })         
    
      this.$q.notify({
        message: 'Names removed successfully',
        color: 'green'
      }, this)

      this.selectedNames = []
      this.loading = false
    },
    async addNamesToGroup() {
      this.$refs.addToGroupForm.validate().then(async success => {
        if (success) {
          this.loading = true
          const response = await axios.get(this.baseURL + 'lists/' + this.selectedGroup)
          var groupNameIDs = response.data.names.map(name => name.id);
          this.selectedNames.filter(name => !groupNameIDs.includes(name.id)).forEach(async name => {
            try {
              await axios.patch(this.baseURL + 'lists/' + this.selectedGroup + '/name', {
                name_id: name.id
              })
            } catch (error) {
                this.$q.notify({
                  message: 'Sorry, something went wrong',
                  color: 'red'
                }, this)
            }
          }) 
          
          this.$q.notify({
            message: 'Names added successfully',
            color: 'green'
          }, this)

          this.selectedNames = []
          this.loading = false
          this.showAddToGroupModal = false
        }
      })
    },
    openDrawer (type) {
      this.drawerContent = type
      this.drawerOpen = true
    },
    async getProjectNames(props = null) {
      if (!props) {
        var page = this.thisProjectsNamesPagination.page
        var rowsPerPage = this.thisProjectsNamesPagination.rowsPerPage
      }else{
        var { page, rowsPerPage, sortBy, descending } = props.pagination
      }
      
      this.loadingNames = true
      try {
        const response = await axios.get(this.baseURL + 'projects/' + this.project + '/names' + '?per_page=' + rowsPerPage + '&page=' + page);
        this.projectNameData = [...response.data.data]

        this.thisProjectsNamesPagination.rowsNumber = response.total
        this.thisProjectsNamesPagination.page = page
        this.thisProjectsNamesPagination.rowsPerPage = rowsPerPage
      } catch (error) {
        console.error(error)
      }
      this.loadingNames = false
    },
    async getProjectNamesDrawer(props = null) {
      if (!props) {
        var page = this.thisProjectsNamesDrawerPagination.page
        var rowsPerPage = this.thisProjectsNamesDrawerPagination.rowsPerPage
      }else{
        var { page, rowsPerPage, sortBy, descending } = props.pagination
      }
      
      this.loadingNames = true
      try {
        const response = await axios.get(this.baseURL + 'projects/' + this.project + '/names' + '?per_page=' + rowsPerPage + '&page=' + page);
        this.projectNameDrawerData = [...response.data.data]

        this.thisProjectsNamesDrawerPagination.rowsNumber = response.total
        this.thisProjectsNamesDrawerPagination.page = page
        this.thisProjectsNamesDrawerPagination.rowsPerPage = rowsPerPage
      } catch (error) {
        console.error(error)
      }
      this.loadingNames = false
    },

    async getGroups() {
      this.loadingGroups = true
      try {
        const response = await axios.get(this.baseURL + 'lists' + '?per_page=100');
        this.groupData = response.data.data
      } catch (error) {
        console.error(error)
      }
      this.loadingGroups = false
    },

	setActionConfirm() {
		//get all the ids & huscs for the checked boxes
		this.setActionIds = [];
		this.setActionHuscs = [];
		for(let i=0;i<this.searchResultsData.length; i++){
			let name = this.searchResultsData[i];
			if(!name.selected) continue;
			this.setActionIds.push(name.id);
			this.setActionHuscs.push(name.name_key);
		}

		if(this.setActionIds.length == 0){
			this.showNotice("Please use the checkboxes to select names.");
			return;
		}

		if(this.limitProject) this.setAction = this.setProjectAction;
		else this.setAction = this.setGlobalAction;

		this.showSetActionConfirm = true;
	},


	async doSetAction(){
		let ids = this.setActionIds.join(",");

		let msg = "";
		let url = "";

		switch(this.setAction){
			case "remove":
				msg = "Removing names from your project...";
				url = "remove-from-project?ids=";
				break;
			
			case "add":
				msg = "Adding names to your project...";
				url = "add-to-project?ids=";
				break;
		}

		this.showWorking(msg);
		this.showSetActionConfirm = false;

		const response = await axios.get(this.altapiURL + url + ids);
		if(response.data.errors && response.data.errors.length){
			this.showErrors(response.data.errors);
			return;
		}

		this.showWorkingMessage = false;
		this.updateResults();
	},



	showNotice(msg, timing = 4000){
		this.noticeMessage = msg;
		this.showNoticeMessage = true;
		setTimeout(()=>{this.showNoticeMessage = false;}, timing);
	},

	showWorking(msg){	
		this.workingMessageMode = "normal";
		this.workingMessage = msg;
		this.showWorkingMessage = true;
	},

	showErrors(errors){
		this.workingMessageMode = "error";
		let errorMsg = "<p>";
		errorMsg += errors.join("</p><p>");
		errorMsg += "</p>";
		this.workingMessage = errorMsg;
		this.showWorkingMessage = true;
	},


    saveSettings(){
      let set = {
        rowsPerPage: this.pagination.rowsPerPage
      }
      localStorage.setItem("namesUISettings", JSON.stringify(set));
    },

    loadSettings(){
      let set = localStorage.getItem("namesUISettings");
      set = JSON.parse(set);
      if(set == null) return;
      this.pagination.rowsPerPage = set.rowsPerPage;
    }

  }

