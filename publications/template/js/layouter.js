

window.addEventListener("DOMContentLoaded", ()=>{

    let tabDiv = null;
    let tabSet = [];
    let currentTab = 0;
    let hasSidebar = false;


    function boxesFromHeading(el){
		let htype = "h3";
        let set = el.getElementsByTagName(htype);
        if(set.length == 0){
			return; //disable using h2
			// htype = "h2";
			// set = el.getElementsByTagName(htype);
			// if(set.length == 0) return;
		}

        //we need the direct parent of h3, so find it, assuming all h3's at same level
        el = set[0].parentNode;

        //gather just at this inner level
        set = el.getElementsByTagName(htype);

        //place containers
        let divs = [];
        for(let i=0;i<set.length;i++){
            let div = document.createElement("div");
            div.className = "box";
            el.insertBefore(div, set[i]);
            divs.push(div);
        }

        //now move items, starting at the ned
        let divi = divs.length -1;
        let childi = el.children.length -1;
        while(childi){
            //when are target div is past, exit
            if(divi == -1) break;
            let child = el.children[childi];
            //if we hit one of our div containers, stepback
            if(child.classList.contains("box")){
                divi--;
                childi--
                continue;
            }
            //this is not one of the divs, so add to current div
            divs[divi].prepend(el.children[childi]);
            childi--;
        }
    }




    function selectTabSection(e){
        let el = e.target;
        if(currentTab){
            currentTab.classList.remove("selected");
            currentTab.TABSECTION.classList.remove("open");
        }
        e.target.classList.add("selected");        
        e.target.TABSECTION.classList.add("open");
        currentTab = e.target;
    }


    function addTabSection(el){
        if(hasSidebar) el.classList.add("withSidebar");

        //look for label in first h2, skip if we don't have one
        let set = el.getElementsByTagName("h2");
        if(set.length == 0) return;

        let label = set[0].textContent;
        set[0].parentNode.removeChild(set[0]);

        //setup tabset if not yet
        if(tabSet.length == 0){
            tabDiv = document.createElement('div');
            el.before(tabDiv);
            tabDiv.className = "tabSet";
        }

        //create the tab for this one
        let div = document.createElement("div");

        div.className = "tab";
        div.innerHTML = label;
        tabDiv.appendChild(div);

        div.TABSECTION = el;
        div.tabIndex = 0;
        div.addEventListener("click", selectTabSection);
        div.addEventListener("keypress", (e) => {
            if(e.key == "Enter" || e.key == "Space") selectTabSection(e);
        });

        //the first one needs selecting
        if(tabSet.length == 0) div.click();
        tabSet.push(div);
    }


	function addSearchbox(el){
		//look for code block
		let codes = el.getElementsByTagName("code");
		for(let code of codes){
			if(code.textContent.includes("searchbox")){

				let temp = code.textContent.split(":");

				let f = document.createElement("div");
				f.classList.add("formRow");
				f.setAttribute("method", "get");
				let formLink = "/" + Env.projectShortname + "/search";
				f.setAttribute("action", formLink);
				let i = document.createElement("input");
				i.className = "searchTerms";
				if(temp[1]) i.setAttribute("placeholder", temp[1]);
				f.appendChild(i);

				function submit(){
					let url = "/publications/" + Env.projectShortname + "/search#q=text_merge:";
					let v = i.value;
					location.href = url + v;					
				}


				i.addEventListener("keydown", (e)=>{if(e.key == "Enter") submit();});
				let but = document.createElement('button');
				but.className = "searchGo";
				but.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>';
				f.appendChild(but);
				but.addEventListener("click", submit);

				code.after(f);
				break;
			}
		}
	}


	function collectSidebars(){
		let groups = document.getElementsByClassName("hassidebars");
		for(let wrapper of groups){
			let g = wrapper.getElementsByClassName("wp-block-group__inner-container")[0];
			let set = g.getElementsByClassName('sidebar');
			if(set.length === 0) continue;
			
			//get non-dynamic set of sidebar els
			let temp = [];
			for(let el of set){
				temp.push(el);
			}

			//create col 1
			let col1 = document.createElement('div');
			col1.className = "column66";

			//move sidebar items to inside sidebar div			
			let d = document.createElement("div");
			d.classList.add("sidebar");
			g.appendChild(d);
	
			for(let el of temp){
				el.classList.remove('sidebar');
				d.appendChild(el);
			}

			//move remaining inside first column
			set = g.children;
			temp = [];
			for(let el of set){
				if(el.classList.contains('sidebar')) continue;
				temp.push(el);
			}
			for(let el of temp){
				col1.appendChild(el);
			}

			d.before(col1);
		}
	}


	function quotesRotator(){
		let temp = document.getElementsByClassName("quotes");
		if(temp.length === 0) return;

		for(let group of temp){
			group.classList.add("cycling");
			group.style.visibility = "hidden";
			let set = group.getElementsByTagName("blockquote");
			let timing = group.classList.contains("faster") ? 3000: 5000;
			let height = 0;
			for(let q of set){
				let rect = q.getBoundingClientRect();
				if(rect.height > height){
					height = rect.height;
				}
				q.classList.add("hiding");
			}

			//kludge arbitrary extra height of 1.5 !! 
			group.style.height = height * 1.5 + "px";
			
			let indx = set.length -1;
			function nextQuote(){
				set[indx].classList.add("hiding");
				indx++;
				if(indx == set.length) indx = 0;
				set[indx].classList.remove("hiding");
	
				setTimeout(()=> {nextQuote()}, timing);
			}
			group.style.visibility = "visible";
			nextQuote();
		}
	}


	function buildColumns(){
		var containers = [];
		var contCount = 0;
		containers[contCount] = null;

		//find all column breaks
		let doc = document.getElementById("document");
		if(!doc) return;
		var set = doc.getElementsByTagName("cb");
		if(!set.length) return;
		var parent, i,j,k,el,temp,place;
		var elements = [];
		var columns = [];
		var ccount = 0;
		//iterate thru all cb's
		for(i=0; i<set.length; i++){
			parent = set[i].parentNode;
			if(null == parent) continue;
			//skip if already build (cb's that share a parent will logically always be the most recent, since we go in doc order)
			if(containers[contCount] == parent) continue;

			containers[contCount] = parent;
			//find only children (not grandchildren) cb's
			temp = parent.children;
			ccount = 0;
			for(j=0;j<temp.length; j++){
				if(temp[j].nodeName == "CB") ccount++;
			}
			ccount++; //there are, say, 2 column breaks, thus 3 columns
			//style
			parent.className += " columns" + ccount;
			//build columns
			for(j=0; j<ccount; j++){
				el = document.createElement("column");
				//add to parent
				parent.insertBefore(el,parent.firstChild);
				columns.unshift(el);//because insertBefore will put the columns in revers order, we also add to array in reverse order
			}
			//move all elements into columns
			elements = parent.children;
			place = 0;
			//start after newly added columns, who are at the top of the element list now
			k = ccount;
			//we don't need to use a for loop, because appendChild will remove the node
			while(typeof elements[k] != "undefined"){
				//when we hit a cb, inc the k and leave it there
				if(elements[k].nodeName == "CB") {
					k++;
					place++;
					continue;
				}
				columns[place].appendChild(elements[k]);
			}
		}
	} //buildColumns()






	//run all the enhancements
	let set = document.getElementsByClassName("boxes");
	for(let el of set){
		boxesFromHeading(el);
	}

	set = document.getElementsByClassName("tab-section");
	for(let el of set){
		addTabSection(el);
	}

	set = document.getElementsByClassName("searchbox");
	for(let el of set){
		addSearchbox(el);
	}

	collectSidebars();
	quotesRotator();

	buildColumns();

});