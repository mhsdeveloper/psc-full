/*
	JQuery version

	This version adds "stuck" class when stuck, and removes. Previous versions
	actually changed the style.
	simple object to create div that stays fixed to top when page
	scrolls it off screen. Tries it's best to keep it right there, and maintain width.

	Create the object, passing the div to stick:

	var mysticky = new mhsSticky(element);

	// and init it!
	mysticky.init();

	use the second argument "clone" set true to create a clone of the stuck element, to remain behind and
	fill up the space of the sticking element


	You can set mystick.verticalOffset to some integer to make it stick
	that many pixels from the top.

	mystick.minScreenWidth defaults to 800; screen width below this and sticky won't trigger


*/

	var mhsSticky = function(element, clone){

		this.element = $(element);

		//determine if element has none-specific width

		this.clone = (typeof clone == "undefined" ? false : true);

		this.verticalOffset = 0;
		this.startY = 0;
		this.minScreenWidth = 800;
		this.extraHeight = 0;
		this.positionX = false;

		this.unstick = function(){
			this.element.removeClass("stuck");
			this.stuck = false;

			//remove clone from DOM
			if (this.clone) {
				this.cln.remove();
			}

		}

		this.stick = function(wScrollY){

			//grab original state
			this.originalPos = this.element.css('position');

			this.origl = this.element.offset().left;

			if (this.clone) {
				this.cln = $("<div></div>");
				var rect = this.element[0].getBoundingClientRect();
				this.cln.addClass("stickyClone");
				this.cln.width(rect.width);
				this.cln.height(rect.height + this.extraHeight);
				this.cln.insertBefore(this.element);
			}

			this.element.addClass("stuck");
			if(this.positionX) this.element.css("left", this.origl + "px");
			this.stuck = true;

		}


		this.init = function(){

			//mark the position
			this.yspot = this.element.offset().top;

			//set flag
			this.stuck = false;


			//setup events
			var me = this;
			$(window).on(
				"scroll", function(){

					var y = $(window).scrollTop();

					if(window.innerWidth < me.minScreenWidth) return;

					//look to unstick
					if (me.stuck) {
						if (y <= (me.yspot - me.verticalOffset) + me.startY) {
							me.unstick();
						}
					}

					else {
						me.yspot = me.element.offset().top;

						if ( y > (me.yspot - me.verticalOffset) + me.startY) {
							me.stick(y);
						}
					}
				}
			);

		}

	}
