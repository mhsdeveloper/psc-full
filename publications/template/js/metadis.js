/*
	Display logic for the Metadata API responses

*/


class MetaDis {

	constructor(){
		this.teaserLength = -1;

		this.itemClassName = "metadataItem";
	}


	/* passing an object that has the names as sub objects by HUSC as key
	*/
	learnNames(names){
		this.names = names;
	}
	


	renderViewBlocks(data, viewBlocks){
		for(let viewBlock of viewBlocks){
			switch(viewBlock.type){
				case "project_names":
					if(!data || !data.facet_counts || !data.facet_counts.facet_fields || !data.facet_counts.facet_fields.person_keyword){
						viewBlock.element.innerHTML = "No facets found for subjects.";
						break;
					}
					this.renderNameFacets(data.facet_counts.facet_fields.person_keyword, viewBlock.element);
					break;

				case "project_subjects":
					if(!data || !data.facet_counts || !data.facet_counts.facet_fields || !data.facet_counts.facet_fields.person_keyword){
						viewBlock.element.innerHTML = "No facets found for subjects.";
						break;
					}
					this.renderFacets(data.facet_counts.facet_fields.subject, viewBlock.element, "topic");
					break;
	
				case "name_documents":
					if(!data.response || !data.response.docs){
						viewBlock.element.innerHTML = "No content found.";
						break;
					}
					this.renderDocuments(data.response.docs, viewBlock.element, viewBlock.fields, viewBlock.teaserLength);
					break;
				
				case "name_subjects":
					if(!data || !data.facet_counts || !data.facet_counts.facet_fields || !data.facet_counts.facet_fields.subject){
						viewBlock.element.innerHTML = "No facets found for subjects.";
						break;
					}
					this.renderFacets(data.facet_counts.facet_fields.subject, viewBlock.element, "subject nameSubject");
					break;

				case "name":
					if(viewBlock.callback) viewBlock.callback(data);
					this.renderNameCard(data, viewBlock.element);
					break;
			}
		}
	}




	renderNameFacets(data, element){
		for(let i=0;i<data.length; i+=2){
			let husc = data[i];
			let last_name = husc;
			let first_name = "";
			let maiden_name = "";

			let a = document.createElement("a");
			a.className = this.itemClassName + " name";
			a.href = "/publications/" + Env.projectShortname + "/explore/person/" + husc;

			if(!this.names) continue;
			if(!this.names[husc]) continue;
			if(!this.names[husc].family_name){
console.log("NOTICE: unable to display name. No family name for HUSC: " + husc);
				continue;
			}
			
			let html = `
				<div class="item">
					<h4>`;
								
			html += CoopHelpers.nameMetadataFormat(this.names[husc]);
					
			html += `<span class="count">${data[i+1]}</span>
				</div>`;

			a.innerHTML = html;
			element.appendChild(a);
		}
	}



///publications/cms/search#0/20/terms/brother/p//s/"Children/Childhood"/a//r//y/

	renderFacets(data, element, type = "name"){
		let wrappingClass = type;
		for(let i=0;i<data.length; i+=2){
			let a = document.createElement("a");
			a.className = this.itemClassName + " " + wrappingClass;

			let value = encodeURIComponent(data[i]);
			let param = "s";
			a.href = "/publications/" + Env.projectShortname + "/read#0/20/";
			if(type.includes("nameSubject")){
				param = "s"; 
				value = encodeURIComponent('"' + data[i] + '"'); 
				a.href += param + "/" + value;
			} else {
				
			}

			if(husc){
				a.href += "/p/" + husc;
			}

			let html = `
				<div class="item">
					<h4>${data[i]}</h4>
					<span class="count">${data[i+1]}</span>
				</div>
			`;

			a.innerHTML = html;
			element.appendChild(a);
		}
	}



	renderDocuments(data, element, fields, teaserLength = 200){

		for(let doc of data){
			let d = document.createElement("div");
			d.className = "documentCard";
			d.setAttribute("tabindex", "0");
			let href = "/publications/" + doc.index + "/document/" + doc.id;
			let div = document.createElement("div");
			for(let field of fields){
				let text = "";
				if(field == "date_when"){
					text = CoopHelpers.formatDate(doc[field]);
					div.innerHTML += `<p class="${field}">${text}</p>\n`;
				} else if(field == "doc_beginning" && teaserLength > 0){
					text = doc[field].substring(0, teaserLength);
					div.innerHTML += `<p class="${field}">${text}</p>\n`;
				} else if(field == "title"){
					text = doc[field];
					div.innerHTML += `<h3>${text}</h4>\n`;
				} else {
					text = doc[field];
					div.innerHTML += `<p class="${field}">${text}</p>\n`;
				}
			}
			d.addEventListener("click", ()=>{location.href = href;});
			d.addEventListener("keyup", (e)=>{if(e.key == "Enter") location.href = href;});
			d.appendChild(div);
			element.appendChild(d);
		}
	}


	renderNameCard(data, element){
		let html = `<h4>`;
		html += CoopHelpers.nameMetadataFormat(data.data);
		html += "</h4>";
		let dob = CoopHelpers.formatDate(data.data.date_of_birth);
		let dod = CoopHelpers.formatDate(data.data.date_of_death);
		html += `<h5> ${dob} &#8212; ${dod}</h5>`;
		//find this projects descriptions
		let descs = CoopHelpers.buildNameDescriptions(data.data, Env.projectShortname);
		html += `<p>${descs[0]}</p>`;

		if(element){
			element.classList.add("nameCard");
			element.innerHTML = html;
		}
	}
}