function PageImages(props) {

	return {
		$template: '#pageImages',
		images: [],
		viewer: null,
		pageImageMetadata: props.pageImageMetadata,


		viewImage(src, n = ""){
			let c = `<div id="osd" style="width: 100%; height: 100%"></div>`;
			this.box.setContent(c);

			if(this.viewer){
				this.viewer.destroy();
				this.viewer = null;
			}

			this.viewer = OpenSeadragon({
				id: "osd",
				zoomInButton:   "zoomIn",
				zoomOutButton:  "zoomOut",
				homeButton:     "osdReset",

				showRotationControl: true,
				tileSources: {
					type:"image",
					url: src
				}
			});
			this.viewer.addHandler("tile-loaded", () => {
//				this.viewer.viewport.goHome();
				let z = this.viewer.viewport.getHomeZoom();
				this.viewer.viewport.zoomTo(z);
			});

			let md = document.getElementById("OSDimageMetadata");
			md.innerHTML = this.pageImageMetadata;
			if(n.length) md.innerHTML += "; page " + n;

			this.box.openFullSize();
		},

		viewDraggable(src){
			let i = document.createElement("img");
			i.addEventListener("load", (e) =>{
				let scale = .6;
				if(i.naturalWidth > window.innerWidth){
					scale *= window.innerWidth / i.naturalWidth;
				}	
				let h = scale * i.naturalHeight;
				let w = scale * i.naturalWidth;
				this.box.content.innerHTML = "";
				this.box.content.style.height = h + "px";
				this.box.content.style.width = w + "px";
				this.box.content.style.backgroundImage = "url(" + src + ")";
				this.box.content.style.backgroundSize = "contain";
				this.box.box.style.width = "auto";
				this.box.openAsDraggable();
			});
			i.src = src;
	
			this.box.setImageContent(src, .5);
		},


		mounted() {
			this.box = new Dragster();
			//this.box.box.style.marginTop = "-10vh";
			this.box.box.insertBefore(document.getElementById("osdButtons"), this.box.content);
			this.pbs = document.getElementsByClassName("pb");

			//try to find an xml id
			let teiID = "";
			const doc = document.getElementById('document');
			if(doc){
				let div = doc.getElementsByTagName('div')[0];
				teiID = div.id;
			}


			for(let p of this.pbs){
				let src = p.getAttribute("facs");
				if(src){

					p.addEventListener("click", (e)=>{
						this.box.open();
					});

					let n = p.textContent;

					if(src == "yes" || src == "YES" || src == "Yes"){
						src = teiID + "-p" + n + ".jpg";
					}

					src = `/projects/${Env.projectShortname}/page-images/${src}`;
					this.images.push({src: src, n: n});
				}
			}
		},
	}
}


export { PageImages }