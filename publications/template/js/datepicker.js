window.addEventListener("DOMContentLoaded", ()=>{

	const DatePicker = {


		container: null,
		yearsEl: null,
		monthsEl: null,
		daysEl: null,
		trigger: null,
		show: false,
		years: [],
		year: null,
		months: [],
		docs: {},
		days: [],
		selectedYearBut: null,	
		selectedMonth: null,
		selectedMonthBut: null,
		selectedDecade: null,
		selectedDecadeBut: null,


		togglePicker(){
			if(this.show){
				this.show = false;
				this.container.classList.add("hidden");
				return;
			}
			document.getElementById("site-navigation").classList.remove("open");
			this.container.classList.remove('hidden');
			this.show = true;
		},

		prettyMonth(num){
			switch(num){
				case "01": return "January";
				case "02": return "February";
				case "03": return "March";
				case "04": return "April";
				case "05": return "May";
				case "06": return "June";
				case "07": return "July";
				case "08": return "August";
				case "09": return "September";
				case "10": return "October";
				case "11": return "November";
				case "12": return "December";
			}
			return "(other)";
		},


		clearYear(){
			this.container.classList.remove("yearSelected");
			this.monthsEl.innerHTML = "";
			this.daysEl.innerHTML = "";
			if(this.selectedYearBut){
				this.selectedYearBut.focus();
				this.selectedYearBut.classList.remove("selected");
				this.selectedYearBut = null;
				this.storeSelection();
				this.yearsEl.classList.remove("show");
				return true;
			}

			return false;
		},

		clearMonth(){
			if(this.selectedMonthBut){
				this.daysEl.innerHTML = "";
				this.selectedMonthBut.focus();
				this.selectedMonthBut = null;
				this.storeSelection();
				return true;
			}
			return false;
		},


		selectDecade(el){
			this.clearYear();
			this.clearMonth();
			this.yearsEl.classList.add("show");
			let decades = this.yearsEl.getElementsByClassName('decade');
			for(let d of decades){
				if(this.selectedDecadeBut) this.selectedDecadeBut.classList.remove("selected");
				d.classList.remove("show");
			}
			let decade = el.getAttribute("data-decade");
			let qs = "[data-decade='" + decade + "']";
			let decadeDiv = this.yearsEl.querySelector(qs);
			decadeDiv.classList.add("show");
			decadeDiv.firstChild.focus();

			this.selectedDecadeBut = el;
			this.selectedDecadeBut.classList.add("selected");
			this.storeSelection();
			this.checkScaling();

		},

		clearDecade(){
			if(this.selectedDecadeBut){
				this.selectedDecadeBut.classList.remove("selected");
				let decades = this.yearsEl.getElementsByClassName('decade');
				for(let d of decades){
					d.classList.remove("show");
				}
				this.decadesEl.firstChild.focus();
				this.selectedDecadeBut = null;
				this.storeSelection();
				return true;
			}

			return false;
		},


		//when a year is selected
		getMonths(e, openMonth = null){
			if(this.selectedYearBut){
				this.selectedYearBut.classList.remove("selected");
			}
			this.selectedYearBut = e.target;
			this.container.classList.add("yearSelected");
			this.monthsEl.innerHTML = "";
			this.daysEl.innerHTML = "";

			this.selectedYearBut.classList.add("selected");

			let year = e.target.textContent;
			this.year = year;
			this.days = [];
			Yodude.send("/publications/" + Env.projectShortname + "/contextMonths?year=" + year).then((resp) => {
				let months = Object.keys(resp);
				months.sort();
				this.months = months;
				for(let m of months){
					let b = document.createElement("button");
					b.textContent = this.prettyMonth(m);
					b.setAttribute("data-month", m);
					b.addEventListener("click", (e)=>{this.getDocs(m, e);})
					this.monthsEl.appendChild(b);

					if(m == openMonth){
						this.getDocs(m, {target: b});
					}
				}

				this.monthsEl.firstChild.focus();
				this.storeSelection();
				this.checkScaling();

			});
		},



		getDocs(month, e){
			this.daysEl.innerHTML = "";
			if(this.selectedMonthBut){
				this.selectedMonthBut.classList.remove("selected");
			}
			this.selectedMonthBut = e.target;
			this.selectedMonthBut.classList.add("selected");
			this.storeSelection();


			this.docs = {};
			let days = [];
			Yodude.send("/publications/" + Env.projectShortname + "/contextMonthDocs?year=" + this.year + "&month=" + month).then((resp) => {
				let docs = resp.response.docs;
				for(let doc of docs){
					let date = "" + doc.date_when;
					let day = "date" + date.substring(6,8);
					if(!this.docs[day]){
						this.docs[day] = [];
						days.push(day);
					}

					this.docs[day].push(doc);
				}
				this.days = days;
				for(let d of days){
					let html = "<div class='day'>";
					html += this.buildDocs(d);
					html += "</div>";
					this.daysEl.innerHTML += html;
				}
				let links = this.daysEl.getElementsByTagName("a");
				links[0].focus();

			});
		},


		buildDocs(dayKey){
			if(!this.docs[dayKey]) return "No documents for this day.";

			let dayLabel = dayKey.replace("date", "");
			if(dayLabel == "99"){
				dayLabel = "other";
			} else if(dayLabel[0] == "0"){
				dayLabel = dayLabel.substring(1);
			}

			let html = `<h3>${dayLabel}</h3>`;
			let template = null;

			if(typeof datePickerDocList == "undefined"){
				console.log("Create a global function datePickerDocList(doc) to customize the individual doc display (see component_datepicker.html");
				template = this.defaultDatePickerDocList;
			} else {
				template = datePickerDocList;
			}

			for(let doc of this.docs[dayKey]){
				html += template(doc);
			}

			return html;
		},

		defaultDatePickerDocList(doc){
			let href = "/publications/" + Env.projectShortname + "/document/" + doc.id + "?navmode=chronological";
			return `<div class="doc"><a href="${href}">${doc.doc_beginning}...</a></div>`;
		},


		storeSelection(){
			let str = "";
			if(this.selectedDecadeBut){
				let decade = this.selectedDecadeBut.getAttribute('data-decade');
				str += decade;

				if(this.selectedYearBut){
					let year = this.selectedYearBut.getAttribute("data-year");
					str += "|" + year;

					if(this.selectedMonthBut){
						let mo = this.selectedMonthBut.getAttribute("data-month");
						str += "|" + mo;
					}
				}
			}

			let key = Env.projectShortname + "DateSelection";
			localStorage.setItem(key, str);
		},


		checkPreviousBrowse(){
			//look for previous date use
			let key = Env.projectShortname + "DateSelection";
			let str = localStorage.getItem(key);

			if(str && str.length){
				let parts = str.split("|");

				if(parts[0]){
					let set = this.decadesEl.getElementsByTagName("button");
					for(let b of set){
						if(b.getAttribute("data-decade") == parts[0]){
							this.selectDecade(b);
							break;
						}
					}

					let decadeDiv = null;
					if(parts[1]){
						//first find decade element
						let set = this.yearsEl.getElementsByClassName("decade");
						for(let d of set){
							if(d.getAttribute("data-decade") == parts[0]){
								decadeDiv = d;
								break;
							}
						}

						if(decadeDiv){
							//find year
							let set = decadeDiv.getElementsByTagName('button');
							for(let b of set){
								let y = b.getAttribute("data-year");
								if(b.getAttribute("data-year") == parts[1]){
									let e = {target: b}
									let openMonth = null;
									if(parts[2]) openMonth = parts[2];
									this.getMonths(e, openMonth);
									break;
								}
							}
						}
					}
	
				}
			}
		},


		checkScaling(){
			if(window.innerWidth < 845){
				this.container.classList.add("narrowDateView");
				return;
			}
			let rect = this.decadesEl.getBoundingClientRect();
			if(rect.height > 80){
				this.container.classList.add("narrowDateView");
				return;
			} else {
				let rect = this.yearsEl.getBoundingClientRect();
				if(rect.height > 80){
					this.container.classList.add('narrowDateView');
					return;
				}
			}

			this.container.classList.remove('narrowDateView');
		},



		mounted() {

			//tie in with wp menu
			let menu = document.getElementById("primary-menu-list");
			if(!menu) return;
			let links = menu.getElementsByTagName("a");
			let spot = null;
			for(let l of links){
				if(l.getAttribute("href") == "#date"){
					spot = l;
					break;
				}
			}

			if(!spot){
				console.log("For the date picker, #primary-menu-list must have a link with href=#date.");
				return;
			}

			this.trigger = document.createElement("div");
			this.trigger.id = "datePickerToggle";
			this.trigger.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
			<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
		</svg>`;

			spot.parentNode.appendChild(this.trigger);

			let self = this;

			function toggleIt(e){
				e.preventDefault();
				self.togglePicker();
			}

			spot.addEventListener("click", toggleIt);
			this.trigger.addEventListener("click", toggleIt);

			let cont = document.createElement("div");
			cont.id = "datePicker";
			cont.className = "datePicker hidden";
			cont.innerHTML = `
			<div class="datePickerForm">
				<label>Browse Dates</label>
				<div class="decades"></div>
				<div class="years"></div>
				<div class="months"></div>
				<div class="days"></div>
			</div>
	`;

			document.getElementsByTagName("header")[0].appendChild(cont);
			this.container = document.getElementById("datePicker");
			this.yearsEl = this.container.getElementsByClassName("years")[0];
			this.decadesEl = this.container.getElementsByClassName("decades")[0];
			this.monthsEl = this.container.getElementsByClassName("months")[0];
			this.daysEl = this.container.getElementsByClassName("days")[0];

			//pre-get the years involved
			let decadeTracker = 100;
			let currentDecadeEl = null;

			Yodude.send("/publications/" + Env.projectShortname + "/contextYears").then((resp) => {
				let years = Object.keys(resp);
				years.sort();
				this.years = years;
				let x = 0;
				let y = 0;
				let w = 73;
				let h = 35;
				let pad = 8;
				for(let year of years){
					let decade = parseInt(year.substring(0, 3));

					//look  for decade
					if(decade > decadeTracker){
						decadeTracker = decade;
						x = 0;
						let decBut = document.createElement("button");
						decBut.innerHTML = decade + "0s";
						this.decadesEl.appendChild(decBut);
						decBut.setAttribute("data-decade", decadeTracker);
						decBut.addEventListener("click", (el)=>{this.selectDecade(el.target);});
						decBut.addEventListener("keyup", (el)=>{if(el.key == "Enter") this.selectDecade(el.target);});

						let decEl = document.createElement("div");
						decEl.className = "decade";
						decEl.setAttribute("data-decade", decadeTracker);
						this.yearsEl.appendChild(decEl);
						currentDecadeEl = decEl;
					}


					let b = document.createElement("button");
					b.setAttribute("data-year", year);
					b.textContent = year;
					b.style.left = x + "px";
					b.style.top = y + "px";
					b.origX = x;
					b.origY = y;
					x += w + pad;
					b.addEventListener("click", (e)=>{
						this.getMonths(e);})
					currentDecadeEl.appendChild(b);
				}

				// this.yearsEl.style.height = y + "px";
				// this.yearsEl.origH = y;

				let cb = document.createElement("button");
				cb.className = "close";
				cb.id="datepickerCancelYear";
				cb.textContent = "×";
				this.container.appendChild(cb);
				cb.addEventListener("click", ()=>{
					this.togglePicker();
				});


				this.checkPreviousBrowse();

			});

			window.addEventListener("keyup", (e)=>{
				if(e.key == "Escape"){
					if(!this.clearMonth()){
						if(!this.clearYear()){
							if(!this.clearDecade()){
								if(this.show) this.togglePicker();
							}
						}
					}
				}
			});

			window.addEventListener("resize", ()=>{
				this.checkScaling();
			});
		}
	}


	DatePicker.mounted();

});