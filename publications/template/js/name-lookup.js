function buildNameLookup(){
	//add basic styling
	let css = 
`
	.nameLookup {
		position: relative;
	}

	.nameLookupChoices {
		display: none;
		position: absolute;
		top: 2rem;
		background: white;
		left: 0;
		z-index: 10;
		max-height: 400px;
		overflow: auto;
		padding: 1rem;
		box-shadow: 0 5px 5px rgba(0,0,0,.15);
		font-size: 16px;
		font-family: 'Roboto', sans-serif;
		font-weight: normal;
	}

	.nameLookupChoices a {
		display: block;
		padding: 1rem 1rem 0.25rem 1rem;
	}
`;


	let nameLookupTimeout;

	let callback = null;

	let selected = false;


	function queueLookupName(e){
		clearTimeout(nameLookupTimeout);
		nameLookupTimeout = setTimeout(()=>{
			lookupName(e);
		}, 300);
	} 


	function lookupName(e){
		let name = e.target.value;
		if(name.length < 2) return;

		e.target.namesDiv.style.display = "block";
//		e.target.after(namesDiv);

		let url = Env.apiExtURL + "names/search?";

		if(Env.projectID > 0){
			url += "project=" + Env.projectID + "&";
		}
		
		Yodude.send(url +  "per_page=200&page=1&name=" + name).then((resp) => {
			if(resp.errors && resp.errors.length){
				this.logError(resp.errors);
				return;
			}
			if(!resp.data) return;

			e.target.namesDiv.innerHTML = "";
			let rect = e.target.getBoundingClientRect();
			e.target.namesDiv.style.top = rect.height + "px";
			for(let name of resp.data){
				let a = document.createElement("a");
				a.nameObj = name;
				a.setAttribute("data-husc", name.name_key);
				a.setAttribute("tabindex", "0");
				a.setAttribute("data-name", `${name.family_name}, ${name.given_name}`);
				let nameStr = CoopHelpers.nameMetadataFormat(name);
				a.innerHTML = `<b>${nameStr}</b>
				<i>${name.date_of_birth} &#8212; ${name.date_of_death}</i>`;
				e.target.namesDiv.appendChild(a);
			}
		});
	}



	function chooseName(e, input){
		let el = e.target;
		while(el && el.nodeName != "A"){
			el = el.parentNode;
		}
		if(el.nodeName != "A") return;

		let husc = el.getAttribute("data-husc");
		input.value = el.getAttribute("data-name");
		
		if(window[input.callback]) window[input.callback](husc, el.nameObj, input);
		closeLookup(e);
	}


	function closeLookup(){
		let set = document.getElementsByClassName("nameLookupChoices");
		for(let el of set){
			el.style.display = "none";
		}
	}



	//------BUILD----

	let style = document.createElement("style");
	style.setAttribute("rel", "stylesheet");
	style.innerHTML = css;
	document.getElementsByTagName("head")[0].appendChild(style);

	//find any name completion box
	let set = document.getElementsByClassName("nameLookup");
	if(set.length){
		for(let el of set){

			let input = null;

			let pl = el.getAttribute("data-placeholder");
			if(!pl) pl = "Last name, first name";
			input = document.createElement("input");
			input.setAttribute("type", "text");
			input.setAttribute("placeholder", pl);
			el.appendChild(input);

			//build our container for suggested names
			input.namesDiv = document.createElement("div");
			input.namesDiv.className = "nameLookupChoices";
			input.namesDiv.addEventListener("click", (e)=>{chooseName(e, input);});

			el.appendChild(input.namesDiv);

			//events
			input.addEventListener("keyup", (e)=>{
				if(e.key == "ArrowDown"){
					let f = input.namesDiv.firstChild;
					f.focus();
					return;
				}
				input.callback = el.getAttribute("data-callback");
				queueLookupName(e);
			});

				

			input.namesDiv.addEventListener("keydown", (e)=>{
				if(e.key != "ArrowDown" && e.key != "ArrowUp" && e.key != "Enter") return;

				if(e.target.nodeName != "A") return;
				let focus = document.activeElement;

				if(e.key == "ArrowDown"){
					e.preventDefault();
					e.stopPropagation();
					if(focus.nextSibling) focus.nextSibling.focus();
				}
				if(e.key == "ArrowUp"){
					e.preventDefault();
					e.stopPropagation();
					if(focus.previousSibling) focus.previousSibling.focus();
				}

				if(e.key == "Enter"){
					chooseName(e, input);
				}
			});

		}
	}


	window.addEventListener("keyup", (e)=>{
		if(e.key == "Escape") closeLookup();
	});

	window.addEventListener("click", (e) => {
		let el = e.target;
		while(el){
			if(el.nodeType != 1){
				closeLookup();
				return;
			}

			if(el.classList.contains("nameLookup")){
				return;
			}
			el = el.parentNode;
		}
		closeLookup();
	});

}