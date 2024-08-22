	/******************************************************************
	 
		Here we add properties to our vue app, that get folded into
		the app's initialization with data: function(){return {etc}}

		the global AppData will get returned to Vue: App.data = function() { return AppData}

	 ******************************************************************/

	AppData.xmlUpdateQueue = [];





	App.methods.TESTXXXX = function(document){
		let f = document.filename.split(".xml")[0];
		return f;
	}
	
	

	//this is the method when docs are uploaded or checked in
	App.methods.XXXX = async function(filename, callbackF){
		Yodude.send(Env.baseURL + "document/audithuscs?f=" + filename).then((resp) => {
			if(resp.errors && resp.errors.length){
				this.logError(resp.errors);
				callbackF(true);
				return;
			}

			if(resp.messages && resp.messages.length){
				this.log(resp.messages.join("<br/>\n"));
			}

			let msg = resp.data.join(", ");
			this.log("Please note: the file " + filename + " contains the following HUSCS which are not in the database: " + msg, "notice");

			Yodude.send(Env.baseURL + "document/makenamespublic?f=" + filename).then((resp) => {
				if(resp.errors && resp.errors.length){
					this.logError(resp.errors);
					callbackF(true);
					return;
				}
	
				if(resp.messages && resp.messages.length){
					this.log(resp.messages.join("<br/>\n"));
				}
	
				let msg = "";
				if(Array.isArray(resp.data.messages)) msg = resp.data.messages.join(", ");
				else if(resp.data.messages) msg = resp.data.messages;
				else if(Array.isArray(resp.data)) msg = resp.data.join(", ");

				this.log("Making names from the document publicly searchable: " + msg, "notice");
				callbackF(true);
			});
		});
	}

