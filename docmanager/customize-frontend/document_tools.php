<?php ?>
	Check: 
	<q-btn unevelated style="background: black; color: white" label="HUSCS" @click="checkHuscs(document.filename)" @keydown.enter="checkHuscs(document.filename)"></q-btn> &nbsp;
	<q-btn unevelated style="background: black; color: white" label="Topics" @click="auditSubjects(document.filename)" @keydown.enter="auditSubjects(document.filename)"></q-btn> &nbsp;
	<q-btn unevelated style="background: #003388; color: white" label="Revision Desc" @click="checkRevDesc(document.filename)" @keydown.enter="checkRevDesc(document.filename)"></q-btn> &nbsp;
	<q-btn unevelated style="background: #e0e0e0; color: #002255" label="Proofread" @click="proofread(document.xmlid)" @keydown.enter="proofread(document.xmlid)"></q-btn> &nbsp; 
	<q-btn unevelated style="background: #d7e781; color: #000000" label="+image attrs" title="click to add @facs to pbs and insert leading pb" @click="addImageAttrs(document)" @keydown.enter="addImageAttrs(document)"></q-btn>
