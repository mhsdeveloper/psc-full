function TimePeriods(props) {

	return {
		$template: "#timePeriod",
		props: ['date'],
		timePeriodTitle: "",
		startDate: "",
		endDate: "",
		periodDescription: "",
		showPeriodDesc: false,
		periodDescEl: null,
		extLink: "",
		extLinkText: "",
		showTimePeriod: false,

		toggleDesc(e){
			let rect = e.target.getBoundingClientRect();
			this.periodDescEl.style.top = rect.top + rect.height + window.scrollY + "px";
			this.periodDescEl.style.left = rect.left + "px";
			if(this.showPeriodDesc){
				this.showPeriodDesc = false;
			} else {
				this.showPeriodDesc = true;
			}
		},

		mounted() {
			const template = document.getElementById("timePeriods");
			if(!template) return;

			//BTW this.config belongs to the parent Obj, DocumentEnhancements,
			//because of the way petite vue just add components sub objects.
			//Subjects all share a common "this", which is the parent obj
			const thisDate = parseInt(CoopHelpers.getDocumentDate(this.config.xpaths.date));
			
			const clone = template.content.cloneNode(true);
			let set = clone.querySelectorAll("period");


			function fixDate(date, month = "00", day = "00"){
				date = date.replace("-", "");
				if(date.length == 4) return date + month + day;
				if(date.length == 6) return date + month;

				date = parseInt(date);
				return date;
			}

			for(let p of set){
				let startDateEl = p.querySelector("startDate");
				let endDateEl = p.querySelector("endDate");
				let startDate = fixDate(startDateEl.getAttribute("date"));
				//make sure full date
				let endDate = fixDate(endDateEl.getAttribute("date"), "99", "99");
				if(thisDate >= startDate && thisDate <= endDate){
					this.timePeriodTitle = p.querySelector("heading").innerHTML;
					this.startDate = startDateEl.textContent;
					this.endDate = endDateEl.textContent;
					this.periodDescription = p.querySelector("shortDescription").innerHTML;
					if(p.querySelector("extLink")){
						this.extLinkText = p.querySelector("extLink").textContent;
						this.extLink = p.querySelector("extLink").getAttribute("href");
					}
					const t = document.getElementById("timePeriodDesc");
					document.body.appendChild(t);
					this.periodDescEl = t;
		
					this.showTimePeriod = true;
					return;
				}
			}
		}
	}
}


export { TimePeriods }