#!/bin/bash


# replace solr auto create fields, and setup the prop

sed -i 's/update.autoCreateFields:true/update.autoCreateFields:false/g' /var/solr/data/publications/conf/solrconfig.xml

# we need to add: ​<schemaFactory class="ClassicIndexSchemaFactory"/> any where in the root element, so tack on the end

sed -i ​'s@</config>@<schemaFactory class="ClassicIndexSchemaFactory"/></config>@g' /var/solr/data/publications/conf/solrconfig.xml


# first remove closing root element tag

#sed -i 's@</config>@ @g' /var/solr/data/publications/conf/solrconfig.xml


# and then append our line

#echo ​"<schemaFactory class=\"ClassicIndexSchemaFactory\"/>" >> /var/solr/data/publications/conf/solrconfig.xml

# and append the closing root element tag

#echo "</config>" >> /var/solr/data/publications/conf/solrconfig.xml