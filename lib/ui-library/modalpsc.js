/*
	to use:
		instantiate and pass the element that will be the modal. It's up to you
		to style it, but this class will make it position fixed, and center it
		in the window.
		ALSO: this class imposed transitions for fading in, and uses that
		to trigger display: none etc.

*/

class  ModalPsc {
	
	constructor(el, sharedMaskEl = null){
		this.el = el;
		let timing = .5;
		if(sharedMaskEl) this.maskEl = sharedMaskEl;
		el.style.display = "none";
		el.style.transition = "opacity " + timing + "s ease";
		el.style.opacity = "0";

		this.isOpen = false;
		this.opening = false;
		this.closing = false;

		el.addEventListener("transitionend", (e)=>{
			if(this.closing){
				this.closing = false;
				this.el.style.display = "none";
				if(this.maskEl){
					this.maskEl.style.display = "none";
				}
			} else if(this.opening){
				this.opening = false;
			}
		});

		el.PSCModal = this;
	}
	
	open(){
		if(this.isOpen) return;//prevent double open
		this.opening = true;
		this.isOpen = true;
		this.el.style.display = "block";
		let rect = this.el.getBoundingClientRect();
		let wtotal = window.innerWidth;
		this.el.style.left = (wtotal - rect.width) * .5 + "px";
		if(this.maskEl) this.maskEl.style.display = "block";
		setTimeout(() => {
			this.el.style.opacity = "1";
			if(this.maskEl) this.maskEl.style.opacity = "1";
		}, 20);
	}

	close(){
		if(!this.isOpen) return;
		this.closing = true;
		this.isOpen = false;
		this.el.style.opacity = "0";
		if(this.maskEl) this.maskEl.style.opacity = "0";
	}


	static MakeFromClass(cls, maskEl = null){
		let set = document.getElementsByClassName(cls);
		let modals = [];
		for(let el of set){
			modals.push(new ModalPsc(el, maskEl));
		}

		return modals;
	}
}