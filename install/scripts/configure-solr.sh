#!/bin/bash


# replace solr auto create fields, and setup the prop

sed -i 's_update.autoCreateFields:true_update.autoCreateFields:false_g' /var/solr/data/publications/conf/solrconfig.xml

# we need to add: ​<schemaFactory class="ClassicIndexSchemaFactory"/> any where in the root element, so tack on the end

sed -i 's_</config>_<schemaFactory class="ClassicIndexSchemaFactory"/></config>_g' /var/solr/data/publications/conf/solrconfig.xml


