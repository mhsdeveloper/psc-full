

	function downloadFile(data, fileName, type="text/plain") {
	  // Create an invisible A element
	  const a = document.createElement("a");
	  a.style.display = "none";
	  document.body.appendChild(a);

	  // Set the HREF to a Blob representation of the data to be downloaded
	  a.href = window.URL.createObjectURL(
		new Blob([data], { type })
	  );

	  // Use download attribute to set set desired file name
	  a.setAttribute("download", fileName);

	  // Trigger the download by simulating click
	  a.click();

	  // Cleanup
	  window.URL.revokeObjectURL(a.href);
	  document.body.removeChild(a);
	}




	var DB;

	window.addEventListener("DOMContentLoaded", function(){

		let historyName = "wetvacHistory";
		let optionsKey = "wetvacOptions";
		let historyMaxLen = 50;

		DB = new DialogBox();
		DB.init(convertConfig.dialogBoxID);

		//load history
		let history = this.localStorage.getItem(historyName);

		if(history != null){
			history = JSON.parse(history);
			buildHistory(history);
		} else history = [];

		var Uploader = new PubToolsUploader();

		Uploader.responseHandler = function(resObj, e){
			
			DB.box.style.display = "block";

			DB.content.innerHTML = "Processing " + resObj.filename + ".<br/><br/>\n";
			startTime = new Date().getTime();
			if(typeof runAnimation != "undefined") runAnimation();
			DB.open();
			DB.title.textContent = "Processing";

			if (typeof resObj.errors !== "undefined"){
				DB.style.display = "none";

				DB.content.innerHTML = "<h3>Sorry there were errors: </h3>";
				if(typeof resObj.errors == "string") {
					if((resObj.errors.indexOf("type") > -1) && (resObj.errors.indexOf("file") > -1)){
						resObj.errors = "Please only upload .docx files.";
					}
					var lis = "<li>" + resObj.errors + "</li>";
				} else {
					var lis = resObj.errors.join("</li>\n<li>");
					lis = "<li>" + lis + "</li>\n";
				}
				DB.content.innerHTML += "<ul class=\"errors\">" + lis + "</ul>\n";
				DB.title.textContent = "Error";
			} else {

				if(typeof resObj.filename == "undefined") {
					DB.open("Error, no filename returned; cannot continue.", e);
					DB.title.textContent = "Error";
					return;
				}

				var f = resObj.filename;

				callConverter(f,e);
			}
		}


		function addToHistory(filename, status, message, link){
			let date = new Date();
			let dateStr = date.toLocaleDateString() + " " + date.toLocaleTimeString();
			history.push({filename, status, message, date: dateStr, link});
			if(history.length > historyMaxLen) history.shift();
			let dataStr = JSON.stringify(history);
			localStorage.setItem(historyName, dataStr);
			buildHistory(history);
		}


		function buildHistory(data){
			let box = document.getElementById("historyContent");
			box.innerHTML = "";

			for(let i = data.length -1; i>-1; i--){
				let message = "error";
				if(data[i].status == "OK"){
					message = `<a href="${data[i].link}" download>Download</a>`;
				} else {
					message = data[i].message;
				}
				let item = `<div class="item">
								<span class="date">${data[i].date}</span>
								<span class="filename">${data[i].filename}</span>
								<span class="status">${data[i].status}</span>
								<p class="message">${message}</p>
							</div>`;
				box.innerHTML += item;
			}
		}


		function saveOptions(){
			let data = {}
			//get milestone param overriders
			let mile = document.getElementById("transcriptionMilestone").value;
			data.transcriptionMilestone = mile;
			mile = document.getElementById("persrefsMilestone").value;
			data.persrefsMilestone = mile;
			mile = document.getElementById("subjectsMilestone").value;
			data.subjectsMilestone = mile;
			mile = document.getElementById("annotationsMilestone").value;
			data.annotationsMilestone = mile;
			
			localStorage.setItem(optionsKey, JSON.stringify(data));
		}
	

		function loadOptions(){
			let data = localStorage.getItem(optionsKey);
			if(data){
				data = JSON.parse(data);
				let keys = Object.keys(data);
				for(let key of keys){
					let select = document.getElementById(key);
					let options = select.getElementsByTagName('option');
					for(let option of options){
						if(option.value == data[key]){
							option.selected = true;
							break;
						}
					}
				}
			}
		}
	

		async function callConverter(f, e){

			let temp = f.split("/");
			let filename = temp[temp.length -1];

			let data = {filename: f}

			//get milestone param overriders
			let mile = document.getElementById("transcriptionMilestone").value;
			if(mile != "x"){
				data.transcriptionMilestone = mile;
			}			
			mile = document.getElementById("persrefsMilestone").value;
			if(mile != "x"){
				data.persrefsMilestone = mile;
			}			
			mile = document.getElementById("subjectsMilestone").value;
			if(mile != "x"){
				data.subjectsMilestone = mile;
			}			
			mile = document.getElementById("annotationsMilestone").value;
			if(mile != "x"){
				data.annotationsMilestone = mile;
			}			
console.log(filename);

			let json = await Yodude.send(convertConfig.processURL, data);

			DB.box.style.display = "none";

			if(json.status == "download"){
				addToHistory(filename, "OK", "Download: ", installDIR + 'uploads/' + json.filename);

				if(typeof json.fileContent != "undefined"){
					downloadFile(json.fileContent, json.filename);
				}


			} else {
				let lis;
				if(typeof json.errors == "object") lis = json.errors.join("</li>\n<li>");
				else lis = json.errors;

				if(lis.indexOf("XSLT") > -1){
					DB.content.innerHTML += `
					<p>WETVAC was able to process your markers, but the XML was unable to be formed. This means that there is likely something too complex, such as superscript with markers in it, or some other nested set of markers.</p>
					`;
				} else {
					lis = "<li>" + lis + "</li>\n";
					lis = "<ul class=\"errors\">" + lis + "</ul>\n";
				}

				addToHistory(filename, "ERROR", lis, "");
			}
		}

		document.getElementById("saveMilestones").addEventListener("click", saveOptions);

		loadOptions();

		Uploader.init(document.getElementById(convertConfig.dragdropBoxID));

	});
