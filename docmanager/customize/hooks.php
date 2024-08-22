<?php

	$Hooks = \DocManager\Hooks::getInstance();

	/* Adding a hook to be called before anything is done with the uploaded file

	 $Hooks->add([hook name], [full namespaced class name], [method name]);

	*/

	$Hooks->add("filenameChange", "\Customize\UploadHooks", "normalizeFilename");
	$Hooks->add("publish", "\Customize\PublishHooks", "publish");
	$Hooks->add("unPublish", "\Customize\PublishHooks", "unPublish");
