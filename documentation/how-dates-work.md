# HOW DATES WORK

Our TEI allows these date attributes:

	@when
	@from @to
	@notBefore @notAfter


These get transform into just two fields in SOLR, date_when & date_to, to make search results consistent and sortable. 

@when >>BECOMES SOLR FIELD>> date_when & date_to

@from >>BECOMES SOLR FIELD>> date_when

@to   >>BECOMES SOLR FIELD>> date_to

@notBefore >>BECOMES SOLR FIELD>> date_when

@notAfter >>BECOMES SOLR FIELD>> date_to


Additionally, when dates are incomplete, 0s and 9s get added so that SOLR get's a complete integer: 0s to starting dates, 9s to ending dates. 

Some examples of dates getting complete:

	TEI @when="1776-06" becomes 17760700 in SOLR date_when
	TEI @to="1776-06" becomes 17669999 in SOLR date_to
	TEI @from="1776" becomes 17760000 in SOLR date_when
	TEI @to="1776" becomes 17769999 in SOLR date_to


We store the dates as integers in SOLR because SOLR's native date type does not allow for incomplete dates. For this reason, and because of the need to render all dates as 8 digit integers, documents with dates before the year 1000 and after the year 9999 will not sort correctly.
