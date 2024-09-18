# Using the timeperiods.xml and creating a Chronology page

Create (or copy an existing) timeperiods.xml and upload using the supporting files interface.

In Oxygen, you can associate the proper schema with the timeperiods with this line:

	<?xml-model href="https://www.primarysourcecoop.org/publications/pub/schema/timeperiods.rng" type="application/xml" schematypens="http://relaxng.org/ns/structure/1.0"?>


to create a Chronology that is automatically built from the xml, create a new page in Wordpress, and choose the time_periods template. That's it!