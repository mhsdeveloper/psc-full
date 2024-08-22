/* simple social media ANTI bloatware like addThis etc.
 *
 * USAGE:
 *
 * 1) place <span class="sharing-icons" data-services="fb tw gp email pin" data-img-src="/some/img/src.jpg" data-url=""></span>
 *
 *  with data-url being the target URL (leave blank to use the current page);
 *  with the string elements for each service you want a button for;
 *  with data-img-src being the src for an img to specify, e.g. for Pinterest to use.
 *  
 * 
 *	2) include something like this JS at the bottom, even after GA:
 *
 *
		<script>
			function SharingSetup(){
				var mhsass = new AntiSocial();
				mhsass.findButtons("sharing-icons");
			}
	   </script>
	   <script src="/lib/ui-library/antisocial.js" async="true" onload="SharingSetup();"></script>

	   
	3) the script will create the necessary markup for each span you placed on the page
	
	
	4) include the antisocial.scss SASS sheet in your sass build to style in a basic way.
	
 *
 *
 *
 */
	
	function AntiSocial() {
		
		this.serviceClass = {
			fb: "facebook",
			tw: "twitter",
			gp: "googleplus",
			pin: "pinterest",
			email: "email",
		}
		
		this.serviceURL = {
			fb: "http://www.facebook.com/sharer.php",
			tw: "https://twitter.com/share",
			gp: "https://plusone.google.com/share",
			pin: "https://www.pinterest.com/pin/create/button/",
			email: "mailto:",
		}
		
		this.urlParam = {
			fb: "u",
			tw: "url",
			gp: "url",
			pin: "url",
			email: "",
		}
		
		this.imgParam = {
			fb: "",
			tw: "",
			gp: "",
			pin: "media",
			email: "",
		}
		
		this.titleParam = {
			fb: "",
			tw: "text",
			gp: "text",
			pin: "description",
			email: "",
		}
		
		this.buttonHTML = {
			
			fb: '<svg viewBox="0 0 33 33" width="25" height="25" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M 17.996,32L 12,32 L 12,16 l-4,0 l0-5.514 l 4-0.002l-0.006-3.248C 11.993,2.737, 13.213,0, 18.512,0l 4.412,0 l0,5.515 l-2.757,0 c-2.063,0-2.163,0.77-2.163,2.209l-0.008,2.76l 4.959,0 l-0.585,5.514L 18,16L 17.996,32z"></path></g></svg>',
			
			tw: '<svg viewBox="0 0 33 33" width="25" height="25" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M 32,6.076c-1.177,0.522-2.443,0.875-3.771,1.034c 1.355-0.813, 2.396-2.099, 2.887-3.632 c-1.269,0.752-2.674,1.299-4.169,1.593c-1.198-1.276-2.904-2.073-4.792-2.073c-3.626,0-6.565,2.939-6.565,6.565 c0,0.515, 0.058,1.016, 0.17,1.496c-5.456-0.274-10.294-2.888-13.532-6.86c-0.565,0.97-0.889,2.097-0.889,3.301 c0,2.278, 1.159,4.287, 2.921,5.465c-1.076-0.034-2.088-0.329-2.974-0.821c-0.001,0.027-0.001,0.055-0.001,0.083 c0,3.181, 2.263,5.834, 5.266,6.438c-0.551,0.15-1.131,0.23-1.73,0.23c-0.423,0-0.834-0.041-1.235-0.118 c 0.836,2.608, 3.26,4.506, 6.133,4.559c-2.247,1.761-5.078,2.81-8.154,2.81c-0.53,0-1.052-0.031-1.566-0.092 c 2.905,1.863, 6.356,2.95, 10.064,2.95c 12.076,0, 18.679-10.004, 18.679-18.68c0-0.285-0.006-0.568-0.019-0.849 C 30.007,8.548, 31.12,7.392, 32,6.076z"></path></g></svg>',
			
			gp: '<svg viewBox="0 0 33 33" width="25" height="25" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M 17.471,2c0,0-6.28,0-8.373,0C 5.344,2, 1.811,4.844, 1.811,8.138c0,3.366, 2.559,6.083, 6.378,6.083 c 0.266,0, 0.524-0.005, 0.776-0.024c-0.248,0.475-0.425,1.009-0.425,1.564c0,0.936, 0.503,1.694, 1.14,2.313 c-0.481,0-0.945,0.014-1.452,0.014C 3.579,18.089,0,21.050,0,24.121c0,3.024, 3.923,4.916, 8.573,4.916 c 5.301,0, 8.228-3.008, 8.228-6.032c0-2.425-0.716-3.877-2.928-5.442c-0.757-0.536-2.204-1.839-2.204-2.604 c0-0.897, 0.256-1.34, 1.607-2.395c 1.385-1.082, 2.365-2.603, 2.365-4.372c0-2.106-0.938-4.159-2.699-4.837l 2.655,0 L 17.471,2z M 14.546,22.483c 0.066,0.28, 0.103,0.569, 0.103,0.863c0,2.444-1.575,4.353-6.093,4.353 c-3.214,0-5.535-2.034-5.535-4.478c0-2.395, 2.879-4.389, 6.093-4.354c 0.75,0.008, 1.449,0.129, 2.083,0.334 C 12.942,20.415, 14.193,21.101, 14.546,22.483z M 9.401,13.368c-2.157-0.065-4.207-2.413-4.58-5.246 c-0.372-2.833, 1.074-5.001, 3.231-4.937c 2.157,0.065, 4.207,2.338, 4.58,5.171 C 13.004,11.189, 11.557,13.433, 9.401,13.368zM 26,8L 26,2L 24,2L 24,8L 18,8L 18,10L 24,10L 24,16L 26,16L 26,10L 32,10L 32,8 z"></path></g></svg>',
			
			pin: '<svg viewBox="0 0 33 33" width="25" height="25" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M 16.5,0C 7.387,0,0,7.387,0,16.5s 7.387,16.5, 16.5,16.5c 9.113,0, 16.5-7.387, 16.5-16.5S 25.613,0, 16.5,0z M 18.1,22.047 c-1.499-0.116-2.128-0.859-3.303-1.573C 14.15,23.863, 13.36,27.113, 11.021,28.81 c-0.722-5.123, 1.060-8.971, 1.888-13.055c-1.411-2.375, 0.17-7.155, 3.146-5.977 c 3.662,1.449-3.171,8.83, 1.416,9.752c 4.79,0.963, 6.745-8.31, 3.775-11.325 c-4.291-4.354-12.491-0.099-11.483,6.135c 0.245,1.524, 1.82,1.986, 0.629,4.090c-2.746-0.609-3.566-2.775-3.46-5.663 c 0.17-4.727, 4.247-8.036, 8.337-8.494c 5.172-0.579, 10.026,1.898, 10.696,6.764 C 26.719,16.527, 23.63,22.474, 18.1,22.047z"></path></g></svg>',
			
			email: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="14" viewBox="-3 -3 46 35" ><g transform="translate(-238.396,-438.87448)"><path d="m 239.66833,439.57841 37.72451,0 -16.25915,12.32712 c 0,0 -2.516,1.59052 -5.20716,-0.19966 -0.58888,-0.39173 -16.2582,-12.12746 -16.2582,-12.12746 z"/><path d="m 277.31473,444.25213 -16.24575,12.49673 c 0,0 -2.26625,1.57783 -4.28371,0.0896 l -17.27315,-12.74249 0,22.14716 37.80261,0 z" /></g></svg>',
			
		}
		
		
		
		
		this.defaultTitle = "";
		
		//keep titles shorter than this (but it'll add a "...", so 3 chars longer)
		this.truncateTitlesAt = 80;
		
		//email subject and message begin with this
		this.emailSubjectPrefix = "Sharing: ";
		
		//dynamically grabbed images must be larger than this
		this.minImgDimension = 100;
		
		
		
		
		
		this.share = function(el){
			if (el.className.indexOf("twitter") > -1) {
				//request tinyUrl
				this.getTinyURL(el);
			} else {
				this.finishShare(el);
			}
		}
		
		
		this.finishShare = function(el){
			var u = el.getAttribute("data-url-param");
			var t = el.getAttribute("data-title-param");
			var i = el.getAttribute("data-img-param");
			var h = el.getAttribute("href");
			
			var imgcomponent = this.gleanImgURL(el);
			
			var title = this.gleanTitle(el);
			
			var url = this.gleanURL(el);
			
			var wurl = h + "?" + u + "=" + url;
			
			//does this service support title?
			if(t.length > 0 ){
				wurl += "&" + t + "=" + title;
				if (el.className.indexOf("twitter") > -1) {
					wurl += "%20" + url;
				}
			}

			//does this service support img?
			if (i.length > 0) {
				wurl += "&" + i + "=" + imgcomponent;
			}

			var wtitle = "Sharing " + t;
			
			var w = screen.width * .5;
			w = w > 500 ? w : screen.width * .8;
			
			var h = screen.height * .5;
			h = h > 350 ? h: screen.width * .8;
			
			var l = ((screen.width * .5) - (w * .5));
			var t = ((screen.height * .3) - (h * .3));
			
			var neww = window.open(wurl, wtitle, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + t + ', left=' + l);
	
			// Puts focus on the newWindow
			if (window.focus) {
				neww.focus();
			}
		}
		
		
		/* figure out the URL: either current page or wrapper element's data-url
		 */
		this.gleanURL = function(el){
			var url = el.parentNode.getAttribute('data-url');
			
			if (url == null || url.length == 0) {
				url = location.href;
			}
			
			if (url.indexOf("localhost")> -1) {
				url = url.replace("localhost", "www.masshist.org");
			}
			
			if (url.indexOf("10.1.1.10")> -1) {
				url = url.replace("10.1.1.10", "www.masshist.org");
			}

			return encodeURIComponent(url);
		}
		
		
		
		/* get page title, or h1. format and truncate if necessary
		 */
		this.gleanTitle = function(el){
			
			var t = el.parentNode.getAttribute("data-title");
			
			if (t == null || t.length == 0) {
				t = document.getElementsByTagName("title");
				if (t == null) t = document.getElementsByTagName("h1");
				if (t == null) return "Sharing a website ";
				
				t= t[0].textContent;
				if (t.length > this.truncateTitlesAt) {
					t = t.substr(0, this.truncateTitlesAt);
					t += "...";
				}
			}
			
			return encodeURIComponent(t);
		}
		
		
		
		
		/* first, look for data-imguri so that other JS (or static markup) can populate
		 * a ref to an img uri
		 *
		 * maybe find nearest img in nearest ancestor that's larger than x,y,or z?
		 * go out 10 levels of ancestors, otherwise first large image?
		 */
		this.gleanImgURL = function(el){
			var i = el.getAttribute("data-img-param");
			var uri = el.parentNode.getAttribute("data-img-src");
			
			//find other img
			if (uri == null || uri.length == 0) {
				var img = document.getElementsByTagName("img");
				if (img.length == 0) return "";
				
				var x=0;
				//find first img larger than
				while(img[x + 1] && (img[x].width < this.minImgDimension || img[x].height < this.minImgDimension)){
					x++;
				};
				
				if ((img[x].width < this.minImgDimension || img[x].height < this.minImgDimension)) return "";
				
				uri = img[x].getAttribute("src");
			}
			
			
			//make full uri
			var temp = new Image();
			temp.src = uri;
			uri = temp.src;

			return "&" + i + "=" + encodeURIComponent(uri);
		}
		
		


		/* we assume no recipient
		 */
		this.buildMailtoURL = function(el){
			var t = this.gleanTitle(el);
			var url = this.gleanURL(el);
			var u ="mailto:?subject=" + encodeURIComponent(this.emailSubjectPrefix) + t + "&body=" + url;
			return u;
		}
		
		
		this.getTinyURL = function(el){
			//look for jquery
			if (window.jQuery) {
				var longurl = this.gleanURL(el);//this should be urlencoded

				//if we've already got a tinyurl, skip
				if (longurl.indexOf("bit.ly") > -1) {
					this.finishShare(el);
					return;
				}
				
				var self = this;
				
				$.ajax({
					data: {
						long_url: longurl
					},
					method: "post",
					dataType: "html",
					url: "/lib/ui-library/tinyurl-request.php",
					success: function(html){

						//set url back to parent
						el.parentNode.setAttribute('data-url', html);
						self.finishShare(el);
					}
				})
				
			} else {
				//can't do it, just share full URL
				this.finishShare(el);
			}
		}
		


		
		this.findButtons = function(cls, parentEl){
			
			var me = this;
			
			var a, i, services, url, pinimg, email;
			
			if (typeof parentEl == "undefined") {
				parentEl = document;
			}
			
			var set = parentEl.querySelectorAll("." + cls);
			for(var x=0; x<set.length; x++){
				
				url = set[x].getAttribute("data-url");
				pinimg = set[x].getAttribute("data-pin-img");
				
				services = set[x].getAttribute("data-services").split(" ");

				for(i=0; i<services.length; i++){

					if (this.buttonHTML[services[i]]) {
						
						a = document.createElement("a");
						
						a.className = "sharing-icon " + this.serviceClass[services[i]];
						
						a.innerHTML = this.buttonHTML[services[i]];

						if (services[i] == "email") {
							set[x].appendChild(a);
							a.href = this.buildMailtoURL(a);
						} else {
							set[x].appendChild(a);
							a.href = this.serviceURL[services[i]];

							a.setAttribute("data-url-param", this.urlParam[services[i]]);
							a.setAttribute("data-img-param", this.imgParam[services[i]]);
							a.setAttribute("data-title-param", this.titleParam[services[i]]);

							a.addEventListener("click", function(e){
								e.preventDefault();
								me.share(this);
							}, false);
						}

						//add space
						a = document.createTextNode(" ");
						set[x].appendChild(a);
					}
				}
			}
		}
		
	}
