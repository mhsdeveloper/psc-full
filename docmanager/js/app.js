

var App = {

	el: "#docapp",

	mounted: function() {
		docManagerCustomizer(this);
		//add in our searcher module
		this.searcher = new Searcher(this);
		this.getDocs();

		window.addEventListener("keydown",function(e){
			if(e.key == "Escape") {
			}
		})
	}
}



let AppData = {
	Env: Env,
	username: Env.username,
	userRole: Env.role,
	userLevel: Env.level,
	activeGroup: "all project names",
	namesGroups: ['all project names'],

	warnSolrError: false,

	docHits: 0,
	docStart: 0,
	docPage: 1,
	docsPerPage: 20,
	docPageTotal: 1,
	documents: [],


	searchHits: 0,
	searchResults: [],

	doclistFields: {
		filename: "",
		user: ""
	},

	doclistOrder: "filename",
	order: "ASC",

	showFileTab: true,
	showSearchTab: false,
	showNewFileDialog: false,
	uploadChoices: null,
	uploadFiles: null,
	checkinFile: null,
	uploadTotal: 0,
	uploadProgress: 0,
	uploading: false,
	validationLog: [],
	validationMsg: "",
	statusLog: [],
	statusTitle: "File Uploads",
	statusDialogMode: 'tall', // or 'wide', or 'bar' for progress bar 
	showStatusDialog: false,

	retrieveMode: "workflow", // "workflow" if getting docs DB,  or "search" if getting docs SOLR + DB

	//for the search field
	fields: {
		text: "",
	},
	sort: {
		field: "date_when",
		dir: "asc"
	},

	documents: [],


	errorMessage: "",
	errorMsgs: [],

	checkinDoc: {filename: ""}, //ref to document in the currently displayed doc list
	showCheckinDialog: false,

	changeNote: "",

	publishDocid: 0, // store this so confirm knows who to publish
	publishDialog: false,

	deleteID: 0,
	deleteDialog: false,
	deleteFilename: "",

	reindexFiles: [],
	reindexTotal: 0,
	reindexCount: 0,
	reindexProgress: 0,

	//things related to customization
	appEvents: {
		postUpload: [],
		parseReturnedSolrField: [],
		editDocument: [],
		releaseDocument: []
	},

	prePublishCheck: null,


	showResync: false,
	showPleaseWait: false,
	showContReindex: false

};

App.watch = {
	docPage: function(val){
		let start = (val - 1) * this.docsPerPage;
		this.searcher.getDocuments(start);
	},

	uploadChoices(e){
		this.upload(e);
	}
}

App.computed = {}

