/* usage:

		let data = {
			key: value,
			key2: value2,
		}

		Yodude.send("/tools/docmanager/index.php/project/steps", data, "GET").then((resp) => {
			console.log(resp);
		});

		send a form:

		let form = new FormData();
		form.append("key", "value");

		Yodude.send("/tools/docmanager/index.php/project/steps", form, "POST", "raw").then((resp) => {
		});

*/



var Yodude = {

	status: {
		ok: "OK",
		failed: "FAILED"
	},

	send: async function(url = "", data = false, method = "GET", dataType = "auto", receiveType = "JSON"){
		if(url == ""){

		} 

		method = method.toUpperCase();

		if(method == "GET" && typeof data == "object"){
			if(url.indexOf("?")) url = url.split("?")[0];//just in case user has data in the url
			url += this.prepGet(data);
		}

		let package = {
			method: method,
			mode: 'cors', // no-cors, *cors, same-origin
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			credentials: 'same-origin', // include, *same-origin, omit
			headers: {
			  'Content-Type': 'application/json'
//			   'Content-Type': 'application/x-www-form-urlencoded',
			},
//			redirect: 'follow', // manual, *follow, error
//			referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
		}

		if(method == "POST" && dataType == "raw"){
			package.body = data;
			package.headers = {}
		}
		else if(method == "POST" && typeof data == "object"){
			package.body = JSON.stringify(data); // body data type must match "Content-Type" header
		} else if(method == "PATCH") {
			package.body = JSON.stringify(data);
		}

		try {
			let response = await fetch(url, package);
		
			if (!response.ok) {
				let msg = `HTTP error. Status: ${response.status}`;
				if(this.onError) this.onError(msg);
				else console.log(msg);
				return response.json();

			} else {
	/*		  if(type === 'blob') {
				content = await response.blob();
			} else if(type === 'text') {
				content = await response.text();
			}
	*/
				if(receiveType == "TEXT" || receiveType == "text"){
					return await response.text();
				}
				else {
					return await response.json();
				} 
			}

		} catch(err){
			console.log(err);
			return {error: err};
		}
	},


	prepGet: function(data){
		//encode data into url for get requests
		let str="?";
		
		let keys = Object.keys(data);
		for(let i=0;i<keys.length; i++){
			if(i>0) str += "&";
			str += encodeURIComponent(keys[i]);
			str += "=";
			str += encodeURIComponent(data[keys[i]]);
		}

		return str;
	}

}