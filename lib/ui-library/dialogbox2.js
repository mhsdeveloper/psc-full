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



function DialogBox() {

	var self = this;

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


	/* element: the element whose contents get copied into the dialog box. Be sure not
	 *			to relie on IDs, because copying those will break the markup.
	 *			<or> string with simple HTML content.
	 * e: the event obj that triggered the box to open
	 * callback: a function called when any element with @data-action is clicked
	 *            function signature: (event, element, args)
	 * arguments: array or single variable to pass to call back function
	 */
	this.open = function(element, e, callback, args){
		if(this.state == 'open' && this.blockReopen) return false;

		clearTimeout(this.transitionTimer); //stop any close triggers
		this.stopClosing(); //removes closing classes

		if(typeof callback != "undefined") this.callback = callback;
		else this.callback = null;

		if(typeof args != "undefined") this.callbackArguments = args;
		else this.callbackArguments = "";

		this.state = 'open';
		this.box.className = "DialogBox opening";
		if(this.useMask) if(this.mask.className.indexOf(" opening") == -1) this.mask.className = this.mask.className + " opening";

		//positioning
		this.box.style.position = this.positioning;

		if(this.mode == "contextual"){
			if(this.box.className.indexOf(" contextual") == -1) this.box.className = this.box.className + " contextual";
			var rect = e.target.getBoundingClientRect();
			var rectb = this.box.getBoundingClientRect();
			this.box.style.left = rect.x + rect.width + window.pageXOffset + "px";
			var y = rect.y + window.pageYOffset;
			if((y + rectb.height) > (window.innerHeight + window.pageYOffset)) y = (window.innerHeight + window.pageYOffset) -rectb.height;
			this.box.style.top = y + "px";
		} else {
			if(this.box.className.indexOf(" contextual") > -1) this.box.className = this.box.className.replace(/ contextual/g, "");
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
			this.title.innerHTML = "";
			this.content.innerHTML = element;
			var temp = this.content.firstElementChild.getAttribute("data-boxtitle");
			if(temp != null) this.title.innerHTML = temp;
			this.contentParent = this.contentSib = null;

		} else if(typeof element != "undefined") {
			var title = element.getAttribute("data-boxtitle");
			if (title != null && title != "") {
				this.title.innerHTML = title;
			}
			this.contentParent = element.parentNode;
			this.contentSib = element.nextElementSibling;
			this.content.innerHTML = "";
			this.content.appendChild(element);
		}

		return true;
	}

	this.close = function(){
		this.state = '';
		this.box.className = this.box.className.replace(/ opening/g, "");
		if(this.box.className.indexOf(" closing") == -1) this.box.className = this.box.className + " closing";
		if(this.useMask){
			this.mask.className = this.mask.className.replace(/ opening/g, "");
			if(this.mask.className.indexOf(" closing") == -1) this.mask.className = this.mask.className + " closing";
		}
		this.transitionTimer = setTimeout(function(){self.stopClosing();}, self.transitionMS);

	}

	this.stopClosing = function(){
		this.box.className = this.box.className.replace(/ closing/g, "");
		this.mask.className = this.mask.className.replace(/ closing/g, "");
		if(this.contentParent != null) {
			this.contentParent.insertBefore(this.content.firstChild, this.contentSib);
			this.contentParent = null;
			this.contentSib = null;
		}
	}


	this.draggin = false;
	this.startDrag = function(e, par){
		//track drag start
		par.dragStart = { x: e.pageX, y: e.pageY};
		var p = par.getPosition(par.box);
		par.boxStart = p;
		par.draggin = true;
	}
	this.endDrag = function(par){
		par.draggin = false;
	}
	this.drag = function(e, me) {

		var x = e.pageX - me.dragStart.x;
		var y = e.pageY - me.dragStart.y;
		me.box.style.top = me.boxStart.y + y + "px";
		me.box.style.left = me.boxStart.x + x + "px";
	}

	this.getPosition = function(el) {
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
	this.place = function(fixed){
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
	this.init = function(id){

		var me = this;

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
			cl.addEventListener('click', function(){me.close();}, false);

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
		tb.addEventListener('mousedown', function(e){me.startDrag(e,me);}, false);
		window.addEventListener('mouseup', function(e){if(me.draggin) me.endDrag(me);}, false);
		window.addEventListener("mousemove", function(e){ if(me.draggin) me.drag(e, me);}, false);
		window.addEventListener("keydown", function(e) { if(me.state == 'open' && e.keyCode == 27) me.close();}, false);

		this.content.addEventListener("click", function(e){
			var attr = e.target.getAttribute("data-action");
			if (self.callback != null && attr != "" && attr != null) {
				if (typeof self.callbackArguments != "undefined") self.callback(e, self, self.callbackArguments);
				else self.callback(e, self);
			}
		});

		//finally, place it

		document.body.appendChild(box);

		//mask
		this.mask = document.createElement("div");
		this.mask.className = "DialogBoxMask";

		this.mask.addEventListener("click", function(){me.close();});

		document.body.appendChild(this.mask);

	}

}
