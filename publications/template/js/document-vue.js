
import { NextPrev } from "./nextprev-vue.js";
import { SidebarContextualizer } from "./sidebarContextualizer-vue.js";
import { AuthorRecipient } from "./author-recipient-vue.js";
import { PageImages} from "./page-images-vue.js";
import { TimePeriods} from "./timeperiods-vue.js";

const DocumentEnhancements = {

	sequenceMode: localStorage.getItem(Env.projectShortname + "_results_mode"),
	//props
	docid: "",
	sequenceMode: null,
	hasSearchCxt: false,
	startLabel: 0,
	searchPrevDoc: null,
	searchNextDoc: null,
	searchTotal: 0,
	searchParams: "",
	seqReadOrSearch: "read",
	pageImageMetadata: "", //for page images

	config: {},



	moveTopics(el){
		//first store outside of DOM, else the xp will break
		let topics = [];
		function insertTopic(topic){
			let t = encodeURIComponent(topic.textContent);
			let link = `<a href='/publications/${Env.projectShortname}/read#0/20/terms//p//s/"${t}"'>${topic.textContent}</a>`;
			topics.push(link);
		}

		//grab elements
		let xp = document.evaluate(this.config.xpaths.topics, document, null);
		let node = xp.iterateNext();
		while(node) { insertTopic(node); node = xp.iterateNext();}
	
		//done with xpath, now create topics container
		let topicsC = document.createElement("div");
		topicsC.className = "topics";
		topicsC.innerHTML = topics.join("");

		if(topicsC.innerHTML.length < 5) return;

		let h = document.createElement("h2");
		h.className = "docbackHeading";
		h.innerHTML = "Topics";

		el.appendChild(h);
		el.appendChild(topicsC);
	},


	moveDocback(el){
		//first is there a source note?
		let source = document.querySelector("note[type=source]");
		if(source){
			let edhead = document.createElement("h2");
			edhead.className = "docbackHeading";
			edhead.innerHTML = "Source Notes";
			el.appendChild(edhead);
			el.appendChild(source);
		}

		let docback = document.querySelector("div[type=docback]");
		if(!docback) return;

		let othernotes = docback.querySelectorAll("note");

		if(othernotes && othernotes.length){
			let edhead = document.createElement("h2");
			edhead.className = "docbackHeading";
			edhead.innerHTML = "Editor's Notes";
			el.appendChild(edhead);

			for(let note of othernotes){
				el.appendChild(note);
				let type = note.getAttribute("type");
				if(type == "fn"){
					note.classList.add("jumpback");

					el.addEventListener("click", function(e){
						e.stopPropagation();
						let el = e.target;
						while(el && el.nodeName != "NOTE") el = el.parentNode;
						let selector = "a.noteRef[data-fnid='" + el.id + "']";
						let ref = document.querySelector(selector);
						if (ref){
							let rect = ref.getBoundingClientRect();
							window.scrollTo(0, rect.y + window.scrollY - 100);
							setTimeout(() => {ref.classList.add("reveal");}, 700);
							setTimeout(() =>{ref.classList.remove("reveal")}, 8000);
						}
					});
				}
			}
		}
	},


	moveInsertions(el){
		let insRefs = document.getElementsByClassName("insRef");
		for(let ref of insRefs){
			let id = ref.getAttribute("data-insid");
			let ins = document.getElementById(id);
			if(ins){
				ref.appendChild(ins);

				ref.addEventListener("click", ()=>{
					if(ref.classList.contains("open")) ref.classList.remove("open");
					else ref.classList.add("open");
				})
			}
		}
	},

	highlight(){
		
		const searchParams = new URLSearchParams(location.href);
		// Iterating the search parameters
		for (const p of searchParams) {
			if(p[0] == "ss"){
				let words = p[1].split("|");
				let hilite = null;

				for(let i=0;i<words.length; i++){
					if(i==0) hilite = new Highlighter("document", "div");
					else hilite.reset();

					hilite.highlight(words[i]);
				}

				hilite.apply();
				break;
			}
		}
	},

	chooseDate(){

	},

	mounted(){
		if(this.config.postMount) this.config.postMount(this);
	},


	//COMPONENTS
	SidebarContextualizer,
	AuthorRecipient,
	NextPrev,
	PageImages,
	TimePeriods
}




export {DocumentEnhancements}