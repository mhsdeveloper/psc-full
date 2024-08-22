var NamesManager  = {
	el: '#q-app',
	data: function () {
	  return {
		tabNameNotes: 'public',
		project: window.PROJECT_ID,
		projects: projects,
		viewingDescription: 0,
		viewingNote: 0,
		userRole: role,
		userLevel: level,
		splitterModel: 15,
		autoNameKey: true,
		suggestNameFlag: false,
		continue: false,
		advancedSearch: 0,
		searchSort: "name",
		nameTab: 'general',
		model: '',
		searchHelp: false,
		editHelp: false,
		isExistingName: false,

		limitProject: 0,
		searchFields: [
			{ 	value: "",	field: "any"}
		],

		fields: {
			notes: true,
			firstMentioned: true,
			verified: false
		},

		showFieldChoices: false,

		fieldOptions: [
			{label: "any", value: "any"},
			{label: "name", value: "name"},
			{label: "HUSC", value: "name_key"},
			{label: "identifier", value: "identifier"},
			{label: "birth year", value: "date_of_birth"},
			{label: "staff notes", value: "notes"},
			{label: "public notes", value: "descriptions"},
			{label: "last name", value: "family_name"},
			{label: "first name", value: "given_name"},
			{label: "birth name", value: "maiden_name"},
			{label: "verified by", value: "verified"}
		],

		authorityOptions: [
		  'SNAC',
		  'LCNAF'
		],
		linkOptions: [
		  'authority',
		  'source'
		],

		setGlobalAction: "add",
		setProjectAction:  "remove",
		setAction: "",
		
		showSetActionConfirm: false,
		setActionHuscs: [],
		setActionIds: [],

		showNoticeMessage: false,
		noticeMessage: "",

		showWorkingMessage: false,
		workingMessage: "",
		workingMessageMode: "normal",

		dateEras: [
			{
				label: "CE",
				value: "ce"
			},
			{
				label:"BCE",
				value: "bce"
			}
		],
		circa: [
			{
				label: "",
				value: 0,
			},
			{
				label: "circa",
				value: 1,
			}
		],
		showEditLinkModal: false,
  
		baseURL: API_URL,
		altapiURL: ALTAPI_URL,
		namesFilter: '',
		projectNamesFilter: '',
		groupNamesFilter: '',
		subjectsFilter: '',
		model: 'one',
//		selectedNames: [],
//		selectedProjectNames: [],
//		searchOnlyMyProject: false,
//		showRecent: true,
		loading: false,
		loadingNames: false,
		loadingGroups: false,
		loadingSubjects: false,
		loadingGroupNames: false,
		searchResultsLoading: false,
//		drawerOpen: false,
//		drawerContent: null,
		showEditNameModal: false,
		showNameModal: false,
		modeNameModal: null,
		showSubjectModal: false,
		modeSubjectModal: null,
		showGroupModal: false,
		modeGroupModal: null,
		showAddToGroupModal: false,
//		selected: [],
		selectedGroup: null,
		groupTypeOptions: [
		  'subject',
		  'name'
		],
		link: {
		  authority: null,
		  authority_id: null,
		  display_title: null,
		  type: null,
		  url: null
		},
		name: {
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
		},
		subject: {
		  subject_name: null,
		  display_name: null,
		  keywords: null,
		  staff_notes: null
		},
		group: {
		  project_id: 1,
		  type: 'name',
		  name: null,
		  names: []
		},
		groupColumns: [
		  { 
			name: 'name', 
			label: 'NAME', 
			field: 'name',
			align: 'left',
			sortable: true 
		  }
		],
		pagination: {
		  sortBy: 'desc',
		  descending: false,
		  page: 1,
		  rowsPerPage: 25,
		  rowsNumber: 0
		},
		thisProjectsNamesPagination: {
		  sortBy: 'desc',
		  descending: false,
		  page: 1,
		  rowsPerPage: 25,
		  rowsNumber: 1
		},
		thisProjectsNamesDrawerPagination: {
		  sortBy: 'desc',
		  descending: false,
		  page: 1,
		  rowsPerPage: 25,
		  rowsNumber: 1
		},
		defaultPagination: {
		  rowsPerPage: 25
		},
		editNameIndex: null,
		groupData: [],
		searchResultsData: [],
		subjectData: [],
		projectNameData: [],
		projectNameDrawerData: [],
		nameKeyAvailable: false
	  }
	}
}