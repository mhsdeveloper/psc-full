# Primary Source Cooperative Server Software

This is an open source server software system for online scholarly publication. It is the technical component of the Massachusetts Historical Society's implimentation of the ["Digital Edition Publishing Cooperatives"](https://www.archives.gov/nhprc/announcement/depc) grant, awarded by NHPRC-Mellon in 2019. This software (the "System") allows small teams of editors to create and deliver digital publications of primary source documents. It provides robust search capabilities with faceting on topics and historical names. It provides web-based tools for managing documents, topics, and a complete historical names managing system. Read more about the [Primary Source Cooperative at the Massachusetts Historical Society](https://www.primarysourcecoop.org) where you can find information about the human infrastructure of the project, such as editorial statements and approaches to governance.

Editors may begin transcription in an MS Word .docx file using a template and set of guidelines or work directly in XML (see our specific customized TEI schema). The System includes tools for checking and converting the .docx templates to XML. XML file can then be uploaded to a document manager: the docmanager allows checking in/out, proof reading, and checking for metadata integrity. In addition to delivering source documents and supporting metadata, the System provides the ability to edit the navigation and home page, and create supporting pages. With the help of a web developer, each edition can use a different schema (or schemas), and indeed, the use of TEI is entirely optional. Custom xslt can be created to allow gathering search and browse metadata from any XML schema. There is a user interface for uploading a custom css file that an edition can user to override most of the visual display. 

## How to use

A complete user's guide, intended for editors and administrators, will be forth coming.

## Software design motivations

The System was designed to be able to scale to support dozens of editions from one server with minimal ongoing maintenance. It was primarily created by one developer, and so many of the features of the System require editing code or configurations files to change; that is, there are few user interfaces beyond those for editing the principle content. It does not feature a single, simple installation they way, say, Drupal or Omeka do. But it will easily support tens of thousands of documents across dozens of editorial projects. 

As presented here, the System integrates very loosely with WordPress which then serves as a CMS frontend to provide user management and WYWIWYG editing of static pages, such as the home page. We provide a custom Wordpress template because of the very specific needs for gathering metadata from the main System. Wordpress was ideal for our needs because it supports multiple "sites" (which we use for edition subsites) right out of the box; and it is a familiar user interface for many of our team. Developer documention is provided to explain how to go about integrating the System with a different PHP-based CMS.

## Installation 

The System has been tested to run on Ubuntu Linux with nginx, php-fpm, mysql, and apache-solr. A complete and detailed guide to installation can be found in the server-install.md file, in the install directory.

## Contact

Please do not hesitate to reach out to the developer at bbeck@masshist.org

## Credits and thanks

The System was developed by Bill Beck, with help from Anshita Khare and Adam Botsford. The main Primary Source Cooperative grant team is Nancy Heywood, Sara Martin, Neal Millikan, Molly Mullin, Tess Renault, led by our principal investigator, Ondine LeBlanc, all of whom helped shape the goals and needs of the software. Thanks to the following folks for their expertise and input: Sid Bauman, Rob Chavez.

### Thanks to these open-source tools

Our package relies on the following open-source software, listed in no particular order; licenses can be found in the licenses folder:

Solr, Wordpress, Jing, Saxon-He, OpenSeaDragon, Quasar, Vue.js, petite-vue.js, Quill.js, Laravel

