<?php include("head.php"); 

?>
</head>
<body class="stepManager">
	<div class="masterc">
		<? include("stepsmanager.php");?>
	</div>
<?php include("quasarSetup.php");?>

  <script>

     var StepsApp = new Vue({
        el: '#stepsapp',

		data: function () {
          return {
			errorMessage: "",
			errorMsgs: [],
			steps: [],
			addDialogOpen: false,
			newStepName: "",
			newStepShortName: "",
			newStepDesc: "",
			newStepOrder: 1,
			newStepShareRequires: false,
			newStepColor: "#d0d0d0",
			editingStep: null,
			stepEdits: {

			},
			showEditor: false,
			showAdd: false,
			color: "",
			confirmDelete: false
		  }
        },

        methods: {
			showErrors: function(errArray){
				if(!errArray) {
					StepsApp.errorMsgs = [];
				} else {
					StepsApp.errorMsgs = errArray;
				}
			},
			closeError: function(e){
				StepsApp.errorMsgs = [];
			},

			add: function(e){
				StepsApp.addDialogOpen = true;
			},

			edit: function(e){
				if(this.editingStep){
					this.editingStep.classList.remove("editing");
				}

				let t = e.target;
				while(t && t.nodeName != "DIV"){
					t = t.parentNode;
				}

				if(t && t.nodeName == "DIV"){
					t.classList.add("editing");
					this.editingStep = t;
					let step = t.getAttribute("data-step");
					this.stepEdits = this.steps[step];
					this.showEditor = true;
				}

			},

			editCancel: function(){
				if(this.editingStep){
					this.editingStep.classList.remove("editing");
				}
			},

			deleteStep: function(e){
				this.showEditor = false;
				let form = new FormData();
				form.append("id", this.stepEdits.id);
		
				Yodude.send("/docmanager/project/deletestep", form, "POST", "raw").then((resp) => {
					StepsApp.showErrors(resp.errors);
					StepsApp.steps = resp.data.steps;
				});
			},

			sort: function(){

			},

			submitNewStep: function(e){
				let form = new FormData();
				form.append("name", this.newStepName);
				form.append("short_name", this.newStepShortName);
				form.append("description", this.newStepDesc);
				form.append("order", this.newStepOrder);
				form.append("color", this.newStepColor);
				form.append("share_requires", this.newStepShareRequires);
		
				Yodude.send("/docmanager/project/newstep", form, "POST", "raw").then((resp) => {
					StepsApp.showErrors(resp.errors);
					StepsApp.steps = resp.data.steps;
				});
			},

			updateStep: function(e){
				let form = new FormData();
				form.append("name", this.stepEdits.name);
				form.append("short_name", this.stepEdits.short_name);
				form.append("description", this.stepEdits.description);
				form.append("order", this.stepEdits.order - .5);
				form.append("color", this.stepEdits.color);
				form.append("share_requires", this.stepEdits.share_requires);
				form.append("id", this.stepEdits.id);
		
				Yodude.send("/docmanager/project/updatestep", form, "POST", "raw").then((resp) => {
					StepsApp.showErrors(resp.errors);
					StepsApp.steps = resp.data.steps;
				});
			},

			cancelAdd: function(e){
				this.addDialogOpen = false;
				this.newStepName = "";
				this.newStepShortName = "";
				this.newStepShareRequires = false;
			}

		},

		mounted: function(){
			Yodude.send("/docmanager/project/steps").then((resp) => {
				StepsApp.showErrors(resp.errors);
				StepsApp.steps = resp.data.steps;
			});

			window.addEventListener("keydown",function(e){
				if(e.key == "Escape") {
					StepsApp.editCancel();
				}
			})
		}
        // ...etc
      });
    </script>
</body>
</html>