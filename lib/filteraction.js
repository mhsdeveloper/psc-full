var MHS = MHS || {};

/* this function should receive onclick and onkeydown events.
	will return true for button presses, and ENTER and SPACE keys
*/
MHS.filterAction = function(e){
	if(e.button == 0) return true;
	if(e.keyCode && e.keyCode == "32") return true;
	if(e.keyCode && e.keyCode == "13") return true;
	return false;
}