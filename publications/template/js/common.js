/*
	JS enhancements for all views
	mobile hamburger menu
	Back-to-top

	useBackToTop: true|false
	backToTopY:
	 	how many pixels down do you scroll before back-to-top button appears
		default is screenheight * .5
	hamActuationWidth: [pixels]
	    the window's innerWidth at which the Hamburger is triggered. Coordinate this with
		the style sheet including

*/
window.addEventListener("DOMContentLoaded", function(){

	let maxHamburgerWidth = 900; //coordinate this with css



	var allViewsConfig = window.allViewsConfig;
	//prepare config defaults
	if(typeof allViewsConfig == "undefined"){
		allViewsConfig = {
			useTextSizers: true,
			textSizersElement: null,
			textSizersPlacement: "bottom",
			useBackToTop: true,
			backToTopY: 150,
		};
	}


	function checkHamburger(){
		if(window.innerWidth <= maxHamburgerWidth){
			document.body.classList.add("useHamburger");
		} else {
			document.body.classList.remove("useHamburger");
		}
	}


	function setupNav(){
		let ham = document.getElementById("hamburger");
		let nav = document.getElementById("site-navigation");
		if(!nav) return;
		let closer = document.getElementById("navCloser");

		let toggleNav = function(){
			if(nav.classList.contains("open")){
				nav.classList.remove("open");
			} else {
				nav.classList.add('open');
				closer.focus();
			}
		}

		closer.addEventListener("click", ()=>{nav.classList.remove("open");});
		closer.addEventListener("keyup", (e)=>{if(e.key != "Enter") return; nav.classList.remove("open");});

		ham.addEventListener("click", toggleNav);
		ham.addEventListener("keyup", (e)=>{if(e.key != "Enter") return; toggleNav();});

		window.addEventListener("resize", checkHamburger);

	}


	/* set of props and functions for the Back to Top feature
	 * This creates an element #BTT, and adds class "hidden". As you scroll,
	 * it removes that class, making the element visible
	 */


	function initBackToTop(){

		if(typeof allViewsConfig.backToTopY != "undefined") var triggerPoint = allViewsConfig.backToTopY;
		else var triggerPoint = screen.height * .5;
		
		//add back to top fixed
		var BTT = document.createElement('div');
		BTT.id = "BTT";
		BTT.innerHTML = "&nbsp;";
		BTT.className = "hidden";

		BTT.addEventListener('click', function(){
			document.body.scrollIntoView({behavior: "smooth", block: "start", inline: "nearest"});
		});
		document.body.appendChild(BTT);
		BTT.below = false;

		window.addEventListener('scroll', function(){
			var y = window.pageYOffset;

			//process flags
			if((y > triggerPoint) && BTT.below == false){
				BTT.below = true;
				BTT.className = BTT.className.replace(/hidden/g, "");
			}

			if((y < triggerPoint) && BTT.below == true){
				BTT.below = false;
				BTT.className += "hidden";
			}
		}, false);
	}


	let TextSizers = {
		textSize: "1.1",

		setTextSize(){
			var t = document.getElementsByClassName("teiFragment");
			for(var x=0; x<t.length;x++){
				t[x].style.fontSize = this.textSize + "rem";
			}
		},

		smallerText(){
			this.textSize = this.textSize - .1;
			if (this.textSize < .8) this.textSize = .8;
			localStorage.setItem("teiTextSize", "" + this.textSize);
			this.setTextSize();
		},

		largerText(){
			this.textSize = this.textSize + .1;
			if (this.textSize > 1.5) this.textSize = 1.5;
			localStorage.setItem("teiTextSize", "" + this.textSize);
			this.setTextSize();
		},

		init(){
			let ts = document.getElementById("txtSizeSmaller");
			let tl = document.getElementById("txtSizeLarger");

			if(!ts || !tl) return;
			ts.addEventListener("click", ()=>{this.smallerText();});
			ts.addEventListener("keyup", (e) =>{if(e.key == "Enter") this.smallerText();});
			tl.addEventListener("click", ()=>{this.largerText();});
			tl.addEventListener("keyup", (e) =>{if(e.key == "Enter") this.largerText();});

			//read local Storage for initial sizing
			this.textSize = localStorage.getItem("teiTextSize");
			if(isNaN(this.textSize)) this.textSize = 1.1;
			if (this.textSize == null || this.textSize.length == 0) {
				localStorage.setItem("teiTextSize", this.textSize);
			}
			this.textSize = parseFloat(this.textSize);
			this.setTextSize();
		}
	}
	TextSizers.init();


	setupNav();
	checkHamburger();
	initBackToTop();

});
