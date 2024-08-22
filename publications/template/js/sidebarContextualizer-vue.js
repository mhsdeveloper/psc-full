
function SidebarContextualizer(props) {

	return {
		$template: '#sidebarContextualizer',
		sideNote: {},
		sideNames: [],
		//ref to the html el
		sidebarContextualizer: null, 

	
		showNote(e){
			e.preventDefault();
			let ref = e.target.getAttribute("data-fnid");
			let note = document.getElementById(ref);
			if(note){
				let text = note.innerHTML;
				this.clearCXTContent();
				this.setCXTContent(text, e, {});
			}
		},
	
	
		clearCXTContent(){
			this.sideNote = {text: ""};
			this.sideNames = [];
		},
	
		//content, e, and data are in that order to match the callback signature for persrefhunter.js
	
		setCXTContent(content, e, data){
			if(data.displayName){
				data.showDetails = false;
				let desc = CoopHelpers.buildNameDescriptions(data, Env.projectShortname);
				data.description = desc;
				this.sideNames.push(data);
			} else {
				this.sideNote.text += content;
			}
			this.showCXT(e);
		},
	
		showCXT(e){
			//our text col and sidebar column are vertically aligned, so we just need how far down the persref is
			//from it's parent that is position: relative
			let y = e.target.offsetTop;
			this.sidebarContextualizer.style.top = y + "px";
			this.sidebarContextualizer.style.display = "block";
			this.sidebarContextualizer.classList.add("showing");
		},
	
		hideCXT(){
			this.sidebarContextualizer.classList.remove("showing");
		},
	


		mounted() {
			this.sidebarContextualizer = document.getElementsByClassName("sidebarContextualizer")[0];

			//setup other events, post Vue re-Doming
			this.sidebarContextualizer.addEventListener("transitionend", (e)=>{
				if(!this.sidebarContextualizer.classList.contains("showing")){
					this.sidebarContextualizer.style.display = "none";
				}
			});
			PersRefHunter.enhanceClasses("persRef");
			PersRefHunter.addListener("display", this.setCXTContent);
			PersRefHunter.addListener("pre", this.clearCXTContent);
	
			//prepare foot note events
			let doc = document.getElementById("document");
			let set = doc.getElementsByClassName("noteRef");
			for(let a of set){
				a.addEventListener("click", this.showNote);
				a.addEventListener("keyup", (e)=>{
					if(e.key != "Enter") return;
					this.showNote(e);
				})
			}
	
		},
	}
}


export { SidebarContextualizer }