
	function setupMetadataTabs(){
		let metadataTab = null;
		let tabsHeight = 0;

		function selectMetadataTab(which){
			if(metadataTab){
				metadataTab.classList.remove("selected");
				metadataTab.tabContent.classList.remove("selected");
			}
			if(metadataTab == which){
				metadataTab = null;
				return;
			}
			metadataTab = which;
			metadataTab.classList.add("selected");
			metadataTab.tabContent.classList.add("selected");
		}

		function addTab(wrapper, which, label){
			let tab = document.createElement("div");
			tab.id = "tabid" + label;
			tab.setAttribute("tabindex", "0");
			tab.innerHTML = label + '<img class="svg" src="/publications/template/images/arrow-drop-down.svg"/>';
			tab.className = "tab";
			tab.tabContent = which;
			wrapper.insertBefore(tab, wrapper.firstChild);
			tab.addEventListener("click", function(){ selectMetadataTab(this);});
			tab.addEventListener("keyup", function(e){if(e.key != "Enter") return; selectMetadataTab(this);});
			//adjust wrapper height
			let rect = tab.tabContent.getBoundingClientRect();
			if(rect.height > tabsHeight) tabsHeight = rect.height;
		}


		let set = document.getElementsByTagName("teiheader");
		if(set.length == 0) return;
		let teiheader = set[0];

		let teidiv = document.createElement("div");
		teidiv.className = "teiHeaderWrapper";
		teiheader.prepend(teidiv);
		let filedesc = teiheader.getElementsByTagName("filedesc")[0];
		let profiledesc = teiheader.getElementsByTagName("profiledesc")[0];
		addTab(teidiv, filedesc, "Metadata");
		addTab(teidiv, profiledesc, "Subjects");
		let extraH = window.getComputedStyle(teiheader);
		let eh = parseFloat(extraH.getPropertyValue("margin-top").replace(/[a-zA-Z]/g, ""));
		eh += parseFloat(extraH.getPropertyValue("margin-bottom").replace(/[a-zA-Z]/g, ""));
		eh += parseFloat(extraH.getPropertyValue("padding-top").replace(/[a-zA-Z]/g, ""));
		eh += parseFloat(extraH.getPropertyValue("padding-bottom").replace(/[a-zA-Z]/g, ""));
		teiheader.style.minHeight = tabsHeight + (2*eh) + "px";
	}




	function buildToolTips(){
		if(null == displayConfigEl) return;

		var hints = displayConfigEl.getElementsByTagName("hint");

		var hintElements = {};
		var match, type, j, set, span, cls, text, span2;

		var toolbox = document.createElement("div");
		toolbox.className = "toolTipBox";
		toolbox.isopen = false;
		document.body.appendChild(toolbox);
		toolbox.open = function(el){
			toolbox.isopen = true;
			var rect = el.getBoundingClientRect();
			var top = $(el).offset();
			toolbox.style.top = top.top + 'px';
			toolbox.style.left = rect.left + "px";
			toolbox.classList.add("open");
		}
		toolbox.close = function(){
			toolbox.isopen = false;
			toolbox.classList.remove("open");
		}

		for(var i=0; i< hints.length; i++){
			match = hints[i].getAttribute("match");
			type = hints[i].getAttribute("type");
			if(type == "hover"){
				//add @title to all elements
				set = document.getElementsByTagName(match);
				for(j=0; j<set.length; j++){
					set[j].setAttribute("title", hints[i].textContent);
				}
			} else {
				//track elements we need to have click events for
				hintElements[match] = hints[i].innerHTML;
				text = hints[i].getAttribute("indicatorText");
				cls = hints[i].getAttribute("cssClass");
				span = document.createElement("span");
				span.className = "tooltip " + cls;
				span.innerHTML = text;

				set = document.getElementsByTagName(match);
				for(j=0; j<set.length; j++){
					span2 = span.cloneNode(true);
					span2.setAttribute("data-hint", match);
					set[j].appendChild(span2);
					set[j].style.position = "relative";
				}
			}
		}

		window.addEventListener("click", function(e){
			e.stopPropagation();
			if(e.target.classList.contains("tooltip")){
				var hint = e.target.getAttribute("data-hint");
				if(typeof hintElements[hint] != "undefined"){
					toolbox.innerHTML = hintElements[hint];
					toolbox.open(e.target);
				}
			}

			else if(toolbox.isopen) {
				toolbox.close();
			}
		});
	}




	buildColumns(){
		var containers = [];
		var contCount = 0;
		containers[contCount] = null;

		//find all column breaks
		var set = this.doc.getElementsByTagName("cb");
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

