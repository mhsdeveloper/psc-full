
let CoopHelpers = {

	dateFormatString: null,

	formatDate(idate){
		if(!idate){
			return "[date unknown]";
		}
		idate = idate + "";
		if(typeof idate != "string"){
			console.log("CoopHelpers.formatDate requires a numeric string input.");
			return "";
		}

		//regularize incoming date
		idate = idate.replace(/[^0-9~]/g, "");
		if(idate.length < 4){
			console.log("CoopHelpers.formatDate require an numeric YYYY style or longer input.");
			return idate;
		}

//		console.log(idate);

		let fullDate = "";

		if(idate[0] == "~"){
			fullDate += "<i>not before</i> ";		
			idate = idate.substring(1);
		}

		if(idate.length > 8){
			let dates = idate.split("~");
			fullDate += CoopHelpers.formatOneDate(dates[0]);
			fullDate += ", <i>not after</i> ";
			fullDate += CoopHelpers.formatOneDate(dates[1]);
		} else {
			fullDate = CoopHelpers.formatOneDate(idate);
		}

		return fullDate.trim();
	},



	formatOneDate(idate){
		let YEAR = idate.substring(0, 4);
		let MONTH2 = "";
		let DAY2 = "";
		if(idate.length > 5) MONTH2 = idate.substring(4,6);
		if(idate.length > 7) DAY2 = idate.substring(6,8);

		//make formats
		let DAY = DAY2[0] == "0" ? DAY2.substring(1) : DAY2;
		if(DAY == "0") DAY = "";
		if(DAY == "99") DAY = "";
		if(DAY2 == "99") DAY2 = "";

		let MONTH = "";
		switch(MONTH2){
			case "01": MONTH = "January"; break;
			case "02": MONTH = "February"; break;
			case "03": MONTH = "March"; break;
			case "04": MONTH = "April"; break;
			case "05": MONTH = "May"; break;
			case "06": MONTH = "June"; break;
			case "07": MONTH = "July"; break;
			case "08": MONTH = "August"; break;
			case "09": MONTH = "September"; break;
			case "10": MONTH = "October"; break;
			case "11": MONTH = "November"; break;
			case "12": MONTH = "December"; break;
			default: MONTH = "";
		}
		let MONTH3 = MONTH.substring(0,3);
		let MONTH3ALT = MONTH3;
		if(MONTH2 == "06") MONTH3ALT = "June";
		if(MONTH2 == "07") MONTH3ALT = "July";


		//parse and store date format string
		if(typeof DATE_FORMAT == "undefined"){
			DATE_FORMAT = "DAY MONTH YEAR";
			console.log("Load the helpers.php and create a customize/date-format.txt to change the format");
		}

		let map = {
			YEAR: YEAR,
			MONTH3ALT: MONTH3ALT,
			MONTH3: MONTH3,
			MONTH: MONTH,
			DAY2: DAY2,
			DAY: DAY
		}
		return CoopHelpers.tokenReplacer(map, DATE_FORMAT);
	},


	nameMetadataFormat(person){
		if(TITLE_STOP_WORDS){
			for(let word of TITLE_STOP_WORDS){
				person.title = person.title.replace(word, "");
			}

			person.title = person.title.trim();
		}

		let map = {
			LAST_NAME: person.family_name,
			FIRST_NAME: person.given_name,
			BIRTH_NAME: person.maiden_name,
			MIDDLE_NAME: person.middle_name,
			SUFFIX: person.suffix,
			TITLE: person.title
		}

		let outname = CoopHelpers.tokenReplacer(map, NAME_FORMAT);
		return outname;
	},



	fullNameFormat(person){

		if(TITLE_STOP_WORDS){
			for(let word of TITLE_STOP_WORDS){
				person.title = person.title.replace(word, "");
			}

			person.title = person.title.trim();
		}

		let map = {
			LAST_NAME: person.family_name,
			FIRST_NAME: person.given_name,
			BIRTH_NAME: person.maiden_name,
			MIDDLE_NAME: person.middle_name,
			SUFFIX: person.suffix,
			TITLE: person.title
		}

		let outname = CoopHelpers.tokenProcessor(map, FULL_NAME_FORMAT);
		return outname;
	},



	/* this adds a bit more sophistication to tokenReplacer()
		strings inside [ ] will not be rendered unless all the
		tokens inside can be replaced with a non-empty string. 
		Using brackets, the brackets must be the first and last characters in the group.
		Groups are space separated.
		so: TOKEN string [string TOKEN] etc.
		not: TOKEN string[string TOKEN] etc.

		So in this way, you could have a label or punctuation only appear if there's content:
		FIRST_NAME LAST_NAME [, SUFFIX]
	*/
	tokenProcessor(tokenMap, format){
		//this didn't find complex formats to parse, so revert to other approach
		if(format.includes("[") === false){
			return CoopHelpers.tokenReplacer(tokenMap, format);
		}

		let parts = [];
		let accum = "";

		function addPart(space = ""){
			if(accum.length === 0) return;
			parts.push(accum + space);
			accum = "";
		}

		//char by char
		let isOpen = false;
		for(let i=0;i<format.length;i++){
			if(!isOpen && format[i] == " "){
				addPart(" ");
			}

			else if(!isOpen && format[i] == "["){
				addPart();
				isOpen = true;
			}

			else if(isOpen && format[i] == "]"){
				addPart("!!COND!!");
				isOpen = false;
			}

			else {
				accum += format[i];
			}
		}
		addPart();


		let out = '';

		for(let part of parts){
			//needs bracket parsing
			if(part.includes("!!COND!!")){
				let raw = part.replace(/!!COND!!/g, "");
				//make sure all token have values
				let mapKeys = Object.keys(tokenMap);
				let incomplete = false;
				for(let key of mapKeys){
					if(raw.includes(key)){
						if(tokenMap[key].length == 0){
							incomplete = true;
							break;
						}
					}
				}
				if(!incomplete){
					out += CoopHelpers.tokenReplacer(tokenMap, raw) + " ";
				}
			} else {
				out += CoopHelpers.tokenReplacer(tokenMap, part) + " ";
			}
		}

		return out;
	},



	/* 
		this function allows you to represent a format for data with a single string
		with placeholder tokens, and a map that shows how real data properties should
		be swapped into the string. By convention, use uppercase tokens.
		e.g.:
			const format = "DAY MONTH, YEAR";
			const map = {
				DAY: [value to put in place of "DAY" token],
				MONTH: [value to put in place of "MONTH" token]
			}

			let formattedString = CoopHelpers.tokenReplace(map, format);

		This assumes using spaces as the token separator
	*/
	tokenReplacer(tokenMap, format){

		let mapKeys = Object.keys(tokenMap);
		let out = format;

		for(let key of mapKeys){
			out = out.replace(key, tokenMap[key]);
		}

		return out;
	},



	buildNameDescriptions(data, projectShortname){
		let desc = "";
		for( let d of data.descriptions){
			if(!d.notes) continue;
			if(d.notes.length < 3) continue;
			if(d.project_name == projectShortname){
				desc = d.notes + " ";
			}
		}	
		return desc;
	},
	


	getPosition(element){
		let x = 0;
		let y = 0;

		let r = element.getBoundingClientRect();
		x += r.left;
		y += r.top;
		y += window.scrollY;
		return { x: x, y: y};
	},



	getDocumentDate(xpathToDate){
		let date = CoopHelpers.getDocumentISODate(xpathToDate);
		if(!date) return;

		let numericDate = date.includes("-") ? date.replace(/\-/g, "") : date;
		//the context sidebar date display
		return numericDate;
	},

	getDocumentISODate(xpathToDate){
		//get date from teiHeader, for single document xml files
		let xp = document.evaluate(xpathToDate, document, null).iterateNext();
		if(null == xp){
			console.log("XPATH couldn't find document date in: " + xpathToDate);
			return;
		}

		let date = xp.getAttribute("when");
		if(date) return date;

		date = xp.getAttribute("notBefore");
		if(date){
			let out = "~" + date + " ~ ";
			date = xp.getAttribute("notAfter");
			if(date) {
				return out + " " + date;
			}
		}

		return;
	}
}