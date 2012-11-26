biostor-cloud
=============

Cloud-based version of BioStor

Bibliographic data in [BibJSON](http://www.bibjson.org/) format from [BioStor](http://biostor.org) is stored in CouchDB hosted by [Cloudant](http://cloudant.com/] which supports [Lucene-based full text search] (https://cloudant.com/for-developers/search/).

Interface to display results borrows some ideas from [CrossRef metadata search](http://search.labs.crossref.org/) for which [code is available on GitHub](https://github.com/CrossRef/cr-search).

Citations are formated using [citeproc-js](https://bitbucket.org/fbennett/citeproc-js). CSL for Zootaxa from [https://github.com/bdrolshagen/mendeley](https://github.com/bdrolshagen/mendeley), BibTeX CSL from [Mendeley/csl-styles](https://github.com/Mendeley/csl-styles).

Project makes use of [Twitter Bootstrap](http://twitter.github.com/bootstrap/) and [Font Awesome](http://fortawesome.github.com/Font-Awesome/).

Documents are displayed using the [documentcloud / document-viewer](https://github.com/documentcloud/document-viewer).

Hosting using Pagodabox
-----------------------

This project is hosted by [Pagoda Box](https://pagodabox.com/). To deploy to Pagoda Box we use git. For example, having modified a file such as index.php we do this:

    git add index.php
    git commit -m "Some informative message"
    git push pagoda --all
    
This requires that we follow the instructions for [setting up git and SSH keys](http://help.pagodabox.com/customer/portal/articles/200927).

Because Pagoda Box runs a minimal PHP by default, we need to create a Boxfile to specify any extra things we need. For example, if we need curl then the Boxfile looks like this:

    web1:
        php_extensions:
            - curl



