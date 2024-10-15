<?php

	//this endpoint check if husc exist and adds huscs from general names to project assigments
	$mvc->route("/document/audithuscs", "Customize\CoopDocChecks@auditHuscs");

	$mvc->route("/document/makenamespublic", "Customize\CoopDocChecks@makeNamesPublic");

	//this endpoint just checks if the HUSCS exist or not
	$mvc->route("/document/checkhuscs", "Customize\CoopDocChecks@checkHuscs");
	$mvc->route("/document/getpersrefs", "Customize\CoopDocChecks@getPersrefs");

	$mvc->route("/document/checkrevdesc", "Customize\CoopDocChecks@checkRevDesc");

	$mvc->route("/document/getsubjects", "Customize\CoopDocChecks@getSubjects");

	$mvc->route("/document/addimageattrs", "Customize\CoopImageTools@addPBAttrs");