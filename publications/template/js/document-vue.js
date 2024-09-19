
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
		let docback = document.querySelector("div[type=docback]");
		if(!docback) return;

		let edhead = document.createElement("h2");
		edhead.className = "docbackHeading";
		docback.id = "docback";
		edhead.setAttribute("tabindex", "0");
		edhead.innerHTML = "Notes";
		el.appendChild(edhead);
		el.appendChild(docback);
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