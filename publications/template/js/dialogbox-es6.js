/* general purpose dialog box */

/*
 * Some useful properties:
 *
 * 	this.closeButton 		: a ref to the closebutton element
 * 	this.box				: ref to outer most div element
 * 	this.content			: ref to div holding content of dialog
 * 	this.title				: ref to h3 within the toolbar
 *  this.width				: set to false to use CSS only for width and height; otherwise set to a percentage: defaults to 80 meaning 80%
 *

 *	Usage:
 *
 *	on opening and closing, the main div box gets these classes added:
 *		.opening (when this.open() first fired)
 *		.open (when transitionend is reached)
 *		.closing (when this.closed() first fired)
 *
 *		[no class] (when transitionend reached)
 *
 *	HTML:

	// this div represents content to get placed. You can hide it by
	// targeting the wrapping div
	// any element with a data-action attribute will listen for clicks,
	// if a callback is passed to box.open()

	<div id="sampleChoices" class="hidden">
		Pick your favorite fruit:
		<button data-action="apples">Apples</button>
		<button data-action="strawberries">Strawberries</button>
	</div>


	function callback(e, box, arguments){

				var choice = e.target.getAttribute("data-action");
				if(choice == "apples") alert("Crunchy choice!");
				else if(choice == "strawberries") alert("Juicy red!");

				box.close();
	}

 	window.addEventListener("DOMContentLoaded", function(){

		var box = new DialogBox();
		box.init("actionbox");

		mybutton.addEventListener("click", function(e){

			//the content of the passed element, but not the wrapping element itself,
			// will be copied (via innerHTML) into the dialog box

			box.open(document.getElementById("sampleChoices"), e, callback, arguments);

	});
 *

		 See: dialogBox.open() returns false if already open, so another purpose can't instantly change the dialog
		 if it's in use, if you make it conditional on .open()
 *
 */



class DialogBox {

	constructor(){
		//some defaults
		this.useToolbar = true;
		this.state = 'closed';
		this.boxStart = {}; //track dragging
		this.dragStart = {};

		/* "absolute" will center  the box at top of viewport, but allow it to scroll with window.
		* "fixed" will size the box complete centered, so use css to make content scrollable.
		*/
		this.positioning = "absolute";

		/* target width relative to viewport, in percent. also used to set height for "fixed" positioning
		*/
		this.width = 80;

		/* set to "modal" for usual modal, in which case the width is a percentage, or
		"contextual" to use css width, auto height, and position relative to triggering element
		*/
		this.mode = "modal";

		/* for contextual, placement relative to target */
		this.placement = "above"; // or below


		/* set to false to not use a full window mask behind the box
		*/
		this.useMask = true;

		/* dont' change this unless you know what you're doing. This blocks new .open() calls;
		.close() needs to be used so that content can be returned to it's original DOM location
		*/
		this.blockReopen = true;

		//callback to handle any events on content in the box
		this.callback = null;

		this.callbackArguments = "";

		this.contentParent = null;
		this.contentSib = null;

		this.transitionTimer = null;
		this.transitionMS = 500;

		this.draggin = false;

	}

	/* element: the element whose contents get copied into the dialog box. Be sure not
	 *			to relie on IDs, because copying those will break the markup.
	 *			<or> string with simple HTML content.
	 * e: the event obj that triggered the box to open
	 * callback: a function called when any element with @data-action is clicked
	 *            function signature: (event, element, args)
	 * arguments: array or single variable to pass to call back function
	 */
	open(element, e, callback, args){
		if(this.state == 'open' && this.blockReopen) return false;

		clearTimeout(this.transitionTimer); //stop any close triggers
		this.stopClosing(); //removes closing classes

		if(typeof callback != "undefined") this.callback = callback;
		else this.callback = null;

		if(typeof args != "undefined") this.callbackArguments = args;
		else this.callbackArguments = "";

		this.state = 'open';
		this.box.classList.add("opening");
		if(this.useMask) this.mask.classList.add("opening");

		//positioning
		this.box.style.position = this.positioning;

		if(this.mode == "contextual"){
			e.target.style.position = "relative";
			let rect = e.target.getBoundingClientRect();
			this.box.style.top = "auto";
			this.box.style.bottom = "auto";
			this.box.style.left = "0";

			if(this.placement == "below"){
				this.box.style.top = rect.height + "px";
			} else {
				this.box.style.bottom = rect.height + "px";
			}
			e.target.appendChild(this.box);

		} else {
			this.box.style.width = this.width + "%";
			var margin = (100 - this.width) * .5;
			this.box.style.left = margin + "%";

			if (this.positioning == "fixed") {
				if(this.width) this.box.style.height = this.width + "%";
				this.box.style.top = margin + "%";
			} else {
				if (typeof e != "undefined") {
					this.box.style.top = e.pageY + "px";
				}
				else this.box.style.top = (margin * .01 * window.innerHeight + window.pageYOffset) + "px";
			}
		}


		//content
		if (typeof element == "string" && element.length > 0) {
			this.content.innerHTML = element;
			this.contentParent = this.contentSib = null;

		} else if(typeof element != "undefined") {
			this.contentParent = element.parentNode;
			this.contentSib = element.nextElementSibling;
			this.content.innerHTML = "";
			this.content.appendChild(element);
		}

		return true;
	}

	close(){
		this.state = '';
		this.box.classList.remove("opening");
		this.box.classList.add("closing");
		if(this.useMask){
			this.mask.classList.remove("opening");
			this.mask.classList.add("closing");
		}
		this.transitionTimer = setTimeout(()=>{this.stopClosing();}, this.transitionMS);

	}

	stopClosing(){
		this.box.classList.remove("closing");
		this.mask.classList.remove("closing");
		if(this.contentParent != null) {
			this.contentParent.insertBefore(this.content.firstChild, this.contentSib);
			this.contentParent = null;
			this.contentSib = null;
		}
	}


	startDrag(e){
		//track drag start
		this.dragStart = { x: e.pageX, y: e.pageY};
		this.boxStart =  this.getPosition(this.box);
		this.draggin = true;
	}
	endDrag(){
		this.draggin = false;
	}
	drag(e) {
		var x = e.pageX - this.dragStart.x;
		var y = e.pageY - this.dragStart.y;
		this.box.style.top = this.boxStart.y + y + "px";
		this.box.style.left = this.boxStart.x + x + "px";
	}

	getPosition(el) {
		var x = 0; var y = 0;

		while(el){
			x += (el.offsetLeft - el.scrollLeft + el.clientLeft);
			y += (el.offsetTop - el.scrollTop + el.clientTop);
			el = el.offsetParent;
		}
		return { x: x, y: y };
	}


	/*
	 	used fixed to set box as fixed, otherwise will be placed in visible
		space according to scroll point when called
	*/
	place(fixed){
		if(typeof fixed == "undefined") fixed = false;
		var top, left;
		top = window.innerHeight * .05;

		if(fixed == false){
			top += window.pageYOffset;
			this.box.style.position = "fixed";
		}

		left = (window.innerWidth - this.box.getBoundingClientRect().width) * .5;
		this.box.style.left = left + 'px';
		this.box.style.top = top + "px";
	}





	// build dbox with outer div having the passed id and inject end of doc
	init(id){
		var box = document.createElement('div');
		box.setAttribute('id', id);
		box.className = 'DialogBox';

		//toolbar
		if(this.useToolbar){
			var tb = document.createElement('div');
			tb.setAttribute('class', 'toolbar');
			this.toolbar = tb;
			this.toolbar.par = this; //ref for toolbar to parent

			var cl = document.createElement('a');
			cl.setAttribute('class', 'close');
			cl.innerHTML = '×';

			tb.appendChild(cl);
			this.closeButton = cl;

			//close function
			cl.addEventListener('click', ()=>{this.close();});

			var titl = document.createElement('h3');
			tb.appendChild(titl);
			this.title = titl;


			box.appendChild(tb);
		}
		this.box = box;

		//content container
		var ct = document.createElement('div');
		ct.setAttribute('class', 'content');
		box.appendChild(ct);
		this.content = ct;


		//events
		if(this.mode != "contextual"){
			tb.addEventListener('mousedown', (e)=>{this.startDrag(e);}, false);
			window.addEventListener('mouseup', (e)=>{if(this.draggin) this.endDrag();}, false);
			window.addEventListener("mousemove", (e)=>{ if(this.draggin) this.drag(e);}, false);
		}
		window.addEventListener("keydown", (e)=>{ 
			if(this.state == 'open' && e.keyCode == 27) this.close();
		}, false);

		this.content.addEventListener("click", (e)=>{
			var attr = e.target.getAttribute("data-action");
			if (this.callback != null && attr != "" && attr != null) {
				if (typeof this.callbackArguments != "undefined") this.callback(e, this, this.callbackArguments);
				else this.callback(e, this);
			}
		});

		//finally, place it

		document.body.appendChild(box);

		//mask
		this.mask = document.createElement("div");
		this.mask.className = "DialogBoxMask";

		this.mask.addEventListener("click", ()=>{this.close();});

		document.body.appendChild(this.mask);

	}

}
