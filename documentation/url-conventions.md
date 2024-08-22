#URL Conventions

The urls for all of the automatically generated pages, such as a document view or an overview of a person, are based on predictable patterns. This document explains those patterns.


##Some definitions

###Project shortname

This is basically the abbreviation of the project. The shortname shouldbe all lower case letters only.
examples: 
	jqa
	cms
	rbt

	DEVELOPER:  this is referenced in code as 
		PHP: \MHS\Env::PROJECT_SHORTNAME
		JS: Env.projectShortname

_NOTE_ do not use "coop" for any abbreviation, this is reserved.


##Link Formats for Automatically Generated Pages

###Persons

	/publications/[project abbreviation]/explore/person/[HUSC]

To see a generic view outside of any specific project, use "coop" as the project abbreviation


###Home pages

	/[project shortname]


###Document Views

	/publications/[project shortname]/document/[document xml:id]

###Read page

	/publications/[project shortname]/read

###Search page

	/publications/[project shortname]/search
