<?php

// Import BioStor references and add to CouchDB

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/reference.php');


$id = 80383;


$ids = array(
71416,
59371,
66840,
88069,
611,
100200,
107030,
88902,
107951,
1386,
59435,
111313,
114
);

//$ids=array(0);

//$ids = array(50335);

$ids = array(20000);

// to do...
for ($id = 13647; $id <= 13647;$id++)
//foreach ($ids as $id)
{
	
	$json = get('http://biostor.org/reference/' . $id . '.bibjson');
	
	if ($json != '')
	{
		$reference = json_decode($json);
		
		if ($reference)
		{
			// ignore stuff tnot linked to BHL
			$go = true;
			
			if ($reference->link)
			{
				foreach ($reference->link as $link)
				{
					if ($link->url == 'http://www.biodiversitylibrary.org/page/0')
					{
						$go = false;
					}
				}
			}
			
			if ($go)
			{
			
				$reference->_id = 'biostor/' . $id;
				
				$reference->citation = reference_to_citation_string($reference);
				
				
				// thumbnail
				$url = 'http://biostor.org/reference/' . $id . '.json';
				$json = get($url);
				
				if ($json != '')
				{				
					$obj = json_decode($json);
					$reference->thumbnail = $obj->thumbnails[0];		
				}
				
				$couch->add_update_or_delete_document($reference,  $reference->_id);
			}
		}		
	}
}

?>