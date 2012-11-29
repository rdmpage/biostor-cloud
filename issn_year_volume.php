<?php

// papers for a decade

require_once (dirname(__FILE__) . '/couchsimple.php');

$startkey = stripcslashes($_GET['startkey']);
$endkey = stripcslashes($_GET['endkey']);

$callback = '';
if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}

//echo $startkey;

/*
$startkey = array($issn, $decade, $decade * 10);
$endkey = array($issn, $decade, $decade * 10 + 9, new stdclass);
*/
		
$url = '_design/issn/_view/year?startkey=' . $startkey . '&endkey=' . $endkey . '&reduce=false&include_docs=true';

//echo $url;

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

if ($callback)
{
	echo $callback . '(';
}
echo $resp;
if ($callback)
{
	echo ')';
}

?>