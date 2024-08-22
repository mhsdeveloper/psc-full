class Dragster {

	/**
	 * 
	 * Usage:
	 * 		const mybox = new Dragster()
	 * 
	 * 
	 */
	constructor(className = "dragster"){
		this.writeStyles(className);
		this.box = document.createElement('div');
		this.box.className = className;
		this.content = document.createElement('div');
		this.content.className = "dragsterContent";
		this.closer = document.createElement("button");
		this.closer.className = "closer";
		this.closer.textContent = "×";
		this.box.appendChild(this.closer);
		this.box.appendChild(this.content);

		this.dragging = false;
		this.draggable = false;
		this.closing = false;
		this.opening = false;
		this.dragStart = {x: 0, y: 0}
		this.boxStart = {x: 0, y: 0}
		this.marginLeft = .2;
		this.marginTop = .1;

		this.mask = document.createElement("div");
		this.mask.className = className + "Mask";
		document.body.appendChild(this.mask);
			
		this.box.addEventListener("transitionend", (e)=>{
			if(this.closing){
				this.box.classList.remove("placed");
				this.box.classList.remove("fullsize");
				this.mask.classList.remove("placed");
			} else {
				this.opening = false;
			}
		});


		this.box.addEventListener("pointerdown", e => {this.dragBegin(e);});
		this.closer.addEventListener("pointerdown", (e) => {this.close(e);});
		window.addEventListener("pointerup", (e)=>{this.closeFromPage(e);	}, false);
		window.addEventListener("pointermove", (e)=>{
			if(!this.dragging) return;	

			let dx = e.pageX - this.dragStart.x;
			let dy = e.pageY - this.dragStart.y;
			this.box.style.left = (window.scrollX + this.boxStart.x + dx) + "px";
			this.box.style.top = (window.scrollY + this.boxStart.y + dy) + "px";

		});
		addEventListener("pointerup", (e)=>{
			this.dragging = false;
			this.dragStart.x = e.pageX;
			this.dragStart.y = e.pageY;
			document.body.classList.remove("dragsterDragging");

		});

		window.addEventListener("keyup", (e)=>{
			if(e.key == "Escape" ){
				this.close(e);
			}
		})
			

		document.body.appendChild(this.box);
	}



	writeStyles(className){
		let css = `

			.${className} {
				position: absolute; 
				left: -500vw; 
				min-width: 50vw; 
				min-height: 50vh; 
				background: black; 
				color: white;
				transition: opacity .7s ease;
				box-sizing: border-box; 
				padding: 1rem;
				opacity: 0;
				z-index: 1000;
			}

			.${className} .closer {
				position: absolute;
				z-index: 100;
				border: none;
				color: white;
				font-size: 40px;
				background: rgba(0,0,0, .7);
				padding: 1px 12px;
				border-radius: 26px;
				top: 13px;
				right: 13px;
			}

			.${className}.placed,
			.${className}Mask.placed {
				left: 0;
			}

			.${className}.fullsize {
				position: fixed;
				left: 0;
				top: 0;
				width: 100% !important;
				height: 100% !important;
				margin: 0;
			}

			.${className}.open {
				opacity: 1;
			}

			.${className} .dragsterContent {
				width: 100%; height: 100%;
			}

			.${className}Mask {
				left: -500vw; 
				top: 0; 
				width: 100%; 
				height: 100%; 
				position:fixed; 
				background: black; 
				opacity: 0; 
				z-index: 900;
				transition: opacity .7s ease;
			}

			.${className}Mask.open {
				left: 0;
				opacity: .45;
			}
		`;

		let style = document.createElement("style");
		style.rel = "stylesheet";
		style.media = "all";
		style.textContent = css;
		document.getElementsByTagName("head")[0].appendChild(style);
	}

	dragBegin(e){
		if(!this.draggable) return;
		this.dragging = true;
		this.dragStart.x = e.pageX;
		this.dragStart.y = e.pageY;
		let rect = this.box.getBoundingClientRect();
		this.boxStart.x = rect.left;
		this.boxStart.y = rect.top;
		document.body.classList.add("dragsterDragging");

	}

	open(){
		this.closing = false;
		this.opening = true;
		this.box.classList.add("placed");
		this.mask.classList.add("placed");
		this.box.classList.add("open");
	}

	closeFromPage(e){
		if(this.opening) return;
		//when draggable, might need to click outside, so disable;
		if(this.draggable) return;
		//if we're in the box, don't close
		let par = e.target;
		while(par){
			if(par == this.box) return;
			par = par.parentNode;
		}

		this.close(e);
	}







	/* PUBLIC FUNCTIONS */


	/**
	 * 
	 * @param {*} cnt string that is text or html
	 */
	setContent(cnt){
		this.content.innerHTML = cnt;
		this.box.style.height = "auto";
	}




	/**
	 * 
	 * @param {*} leftRight margin as fraction of 1; gets centered on screen
	 * @param {*} top top margin as fraction of 1; height grows to fit content
	 */
	setMargins(leftRight = .2, top = .1){

	}



	/**
	 * call this to have the box fit the content exactly. 
	 * If just an image is passed, it will fit that image.
	 */
	setAutoSizing(){

	}

	openFullSize(){
		this.box.classList.add("fullsize");
		this.open();
	}


	/* width less margin, height auto*/
	openAsModal(){
		let marginTop = this.marginTop * window.innerHeight;
		let marginLeft = this.marginLeft * window.innerWidth;
		this.box.style.left = window.scrollX + marginLeft + "px";
		this.box.style.width = (window.innerWidth - (2 * marginLeft)) + "px";
		this.box.style.top = marginTop + window.scrollY + "px";
		//this.box.style.height = (window.innerHeight - (2 * marginTop)) + "px";
		this.boxStart.x = marginLeft;
		this.boxStart.y = marginTop;
		this.mask.classList.add("open");
		this.open();
	}


	/* width less margin, height auto*/
	openAsDraggable(margin = .2){
		this.draggable = true;
		this.open(margin);
	}



	close(e){
		e.preventDefault();
		e.stopPropagation();
		this.closing = true;
		this.box.classList.remove('open');
		this.mask.classList.remove("open");
	}

}


