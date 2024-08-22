

window.addEventListener("DOMContentLoaded", ()=>{
	let teiFragment = document.getElementsByClassName("teiFragment")[0];

	//show xml:id
	let TEI = document.getElementsByTagName("tei");
	if(TEI.length){
		TEI = TEI[0];
		let xmlid = TEI.getAttribute("id");
		let div = document.createElement("div");
		div.className = "documentID";
		div.innerHTML = "DOCUMENT ID: " + xmlid;
		teiFragment.insertBefore(div, TEI);
	}	

	function enhanceTags(parent, tagName){
		let els = parent.getElementsByTagName(tagName);
		for(let el of els){
			el.setAttribute("title", el.nodeName.toLowerCase());
			el.classList.add("showBrackets");
		}
	}

	function enhanceClasses(parent, clsName){
		let els = parent.getElementsByClassName(clsName);
		for(let el of els){
			el.setAttribute("title", el.className);
		}
	}

	function huscsInTitles(parent){
		let els = parent.getElementsByTagName("persRef");
		for(let el of els){
			let h = el.getAttribute("ref");
			let t = h;
			let k = el.getAttribute("key");
			if(k) t += "; " + k;
			el.setAttribute("title", t);
			el.innerHTML = "<span class='HUSC'>" + t + "</span>" + el.innerHTML;
		}
	}


	function fixGaps(parent){
		let set = parent.getElementsByTagName("gap");
		for(let gap of set){
			gap.innerHTML = "gap";
		}
	}


	let text = document.getElementsByTagName("text")[0];

	let tagsToEnhance = ["add", "del", "gap", "hi", "persRef", "unclear"];
	for(let tag of tagsToEnhance) enhanceTags(text, tag);

	let classesToEnhance = ["pb", "persRef"];
	for(let cls of classesToEnhance) enhanceClasses(text, cls);

	huscsInTitles(text);

	fixGaps(text);

});