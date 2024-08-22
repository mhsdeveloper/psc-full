class Paginator {

	constructor(config = {}){
		this.config = config;
		this.rowCount = 20;

		let defaults = {
			prevPageLabel: "Prev",
			nextPageLabel: "Next",
			pageClickCallback: (e) => {
				let start = e.target.getAttribute("data-start");
				console.log("Create a pageClickCallback f() in your config and grab data-start attr from e.target");
				console.log("like this one: " + start);
			}
		}

		let keys = Object.keys(defaults);
		for(let key of keys){
			if(typeof this.config[key] == "undefined"){
				this.config[key] = defaults[key];
			}
		}
	}

	build(start = 0, numFound = 0){
		this.start = start;
		let pages = Math.ceil(numFound / this.rowCount);
		let page = Math.floor(start / this.rowCount);

		let wrapper = document.createElement("div");
		wrapper.className = "pagination";

		//prev button
		if(start > 0){
			let div = this.createButton(page - 1, this.config.prevPageLabel);
			div.classList.add("prevPage");
			wrapper.appendChild(div);
		}

		//always show page 1
		let div = this.createButton(0);
		wrapper.appendChild(div);

		//simply list all pages
		if(pages < 14){
			for(let i=1;i<pages;i++){
				let div = this.createButton(i);
				wrapper.appendChild(div);
			}
		}

		//something more complex...
		else {
			let maxButtons = 10;
			let pageButtons = 0;
				
			if(numFound > this.rowCount){

				//if further along in list, provide context of pages before
				if(page > 5){
					let a = document.createElement("a");
					a.classList.add("page");
					a.innerHTML = "...";
					wrapper.appendChild(a);

					// let div = this.createButton(page);
					// wrapper.appendChild(div);

					for(let i= page - 1;i<pages;i++){
						let div = this.createButton(i);
						wrapper.appendChild(div);
						pageButtons++;
						if(pageButtons == maxButtons) break;
					}
				}

				//simply the first ten pages
				else {
					for(let i=1;i<pages;i++){
						let div = this.createButton(i);
						wrapper.appendChild(div);
						pageButtons++;
						if(pageButtons == maxButtons) break;
					}
				}

			}

		}

		//next button
		if(page + 1 < pages){
			div = this.createButton(page + 1, this.config.nextPageLabel);
			div.classList.add("nextPage");
			wrapper.appendChild(div);
		}

		wrapper.addEventListener('click', (e) => {this.config.pageClickCallback(e);});
		return wrapper;
	}


	createButton(page, label = ""){
		let a = document.createElement("a");
		a.classList.add("page");
		let start = page * this.rowCount;
		a.title = start;
		if(start != this.start) a.href = "#";
		else {
			a.classList.add("currentPage");
		}
		if(label.length) a.innerHTML = label;
		else a.innerHTML = page  + 1;
		a.setAttribute("data-start", start);
		return a;
	}

}