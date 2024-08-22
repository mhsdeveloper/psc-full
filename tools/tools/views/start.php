<?php include("head.php"); ?>
	<style>
		h2 {
			margin-top: 4rem;
		}
	</style>
</head>
<body class="documentManager">
	<div class="masterc">
		<? include("body.php");?>
	</div>
	<?php include("quasarSetup.php");?>

    <script>
	  var MHSAPIURL = "/mhs-api/v1/";

      var DocApp = new Vue({
        el: '#docapp',

        data: function () {
          return {
			errorMessage: "",
			errorMsgs: [],
			statusOpen: false,
			syncStatus: null,
			statusLabel: "",
			statusText: "",
		  }
        },

        methods: {

			syncmenu: function(e){
				this.statusLabel = "Syncing header/footer/menu";
				this.statusOpen = true;
				this.statusText = "Gathering header and menu ...";

				let url = "/" + projectShortname;
				if(projectShortname == "coop"){
					url = "/";
				}
				Yodude.send(url, {}, "GET", "auto", "text").then((srcResp) => {
					let header = this.sliceHeader(srcResp);
					this.statusText += "storing header ...";

					let data = new FormData();
					data.append("menu", header);
					Yodude.send("/tools/tools/index.php/storemenu", data, "POST", "raw").then((resp) => {
						if(resp.data && resp.data.status && resp.data.status == "OK"){
							this.statusText += "<br/>\n Header and menu stored.";
						}
					});

				});
			},


			updateSchema: function(e){
				this.statusLabel = "Updating XSLT";
				this.statusOpen = true;
				this.statusText = "Gathering latest umbrella terms ...";

				Yodude.send("/tools/tools/updateschema", {}, "GET", "auto").then((resp) => {
					if(resp.data && resp.data.status && resp.data.status == "OK"){
						this.statusText += "<br/>\nXSLT updated.";
					}
				});
			},


			sliceHeader: function(resp){
				let parts = resp.split("<header");
				let temp = parts[1];
				parts = temp.split("</header>");
				let header = parts[0];
				header = "<header" + header + "</header>\n\n";

				return header;
			},

			sliceFooter: function(resp){
				let parts = resp.split("<footer");
				let temp = parts[1];
				parts = temp.split("</footer>");
				let f = parts[0];
				f = "<footer" + f + "</footer>\n\n";

				return f;
			},

			grabHeadParts: function(html){
				this.statusText += "<br/>\nGathering styles..";
				let csslinks = html.match(/(\<link.*\/>)/g);
				let out = "";
				for(let i=0;i<csslinks.length; i++){
					if(csslinks[i].indexOf("/global.css") > -1) out += csslinks[i] + "\n";
				}				

				let data = new FormData();
				data.append("css", out);
				Yodude.send("/tools/tools/index.php/storecss", data, "POST", "raw").then((resp) => {
					if(resp.data && resp.data.status && resp.data.status == "OK"){
						this.statusText += "<br/>\n Styles stored.";
					}
				});

			}
		},

		mounted: function(){
			this.syncStatus = document.getElementById("syncStatus");
		}
      })
    </script>

</body>
</html>