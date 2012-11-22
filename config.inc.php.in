<?php

// $Id: //

/**
 * @file config.php
 *
 * Global configuration variables (may be added to by other modules).
 *
 */

global $config;

// Date timezone
date_default_timezone_set('UTC');

// Server-------------------------------------------------------------------------------------------
$config['web_server']	= 'http://localhost'; 
$config['site_name']	= 'BioStor in the cloud';

// Files--------------------------------------------------------------------------------------------
$config['web_dir']		= dirname(__FILE__);
$config['web_root']		= '/~rpage/biostor-cloud/';


// Proxy settings for connecting to the web--------------------------------------------------------- 
// Set these if you access the web through a proxy server. 
$config['proxy_name'] 	= '';
$config['proxy_port'] 	= '';

//$config['proxy_name'] 	= 'wwwcache.gla.ac.uk';
//$config['proxy_port'] 	= '8080';

// CouchDB------------------------------------------------------------------------------------------

// local CouchDB
$config['couchdb_options'] = array(
		'database' => '<database>',
		'host' => 'localhost',
		'port' => 5984
		);

// Cloudant
$config['couchdb_options'] = array(
		'database' => '<database>',
		'host' => '<username>:<password>@rdmpage.cloudant.com',
		'port' => 5984
		);


// HTTP proxy
if ($config['proxy_name'] != '')
{
	$config['couchdb_options']['proxy'] = $config['proxy_name'] . ':' . $config['proxy_port'];
}

?>