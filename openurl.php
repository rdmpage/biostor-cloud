<?php

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/find.php');
require_once (dirname(__FILE__) . '/ISBN-ISSN.php');
require_once (dirname(__FILE__) . '/nameparse.php');
require_once (dirname(__FILE__) . '/reference.php');

//--------------------------------------------------------------------------------------------------
/**
 * @brief Parse OpenURL parameters and return context object
 *
 * @param params Array of OpenURL parameters
 * @param context_object Context object to populate
 *
 */
function parse_openurl($params, &$context_object)
{
	global $debug;
	
	$context_object->referring_entity = new stdClass;
	$context_object->referent = new stdClass;
	$context_object->referent->type = 'unknown';
		
	foreach ($params as $key => $value)
	{
		switch ($key)
		{
			case 'ctx_ver':
				$context_object->version = $value[0];
				break;
				
			case 'rfe_id':
				$context_object->referring_entity->id = $value[0];
				break;
		
			case 'rft_val_fmt':
				switch ($value)
				{
					case 'info:ofi/fmt:kev:mtx:journal':
						$context_object->referent->type = 'article';
						break;

					case 'info:ofi/fmt:kev:mtx:book':
						$context_object->referent->type = 'book';
						break;
						
					default:
						if (!isset($context_object->referent->type))
						{
							$context_object->referent->type = 'Unknown';
						}
						break;
				}
				break;
			
			// Article title
			case 'rft.atitle':
			case 'atitle':
				$title = $value[0];
				$title = preg_replace('/\.$/', '', $title);
				$title = strip_tags($title);
				$title = html_entity_decode($title, ENT_NOQUOTES, 'UTF-8');
				$context_object->referent->title = $title;
				$context_object->referent->type = 'article';
				break;

			// Book title
			case 'rft.btitle':
			case 'btitle':
				$context_object->referent->title = $value[0];
				$context_object->referent->type = 'book';
				break;
				
			// Journal title
			case 'rft.jtitle':
			case 'rft.title':
			case 'title':
				$publication_outlet = trim($value[0]);
				$publication_outlet = preg_replace('/^\[\[/', '', $publication_outlet);
				$publication_outlet = preg_replace('/\]\]$/', '', $publication_outlet);
				
				
				if (!isset($context_object->referent->journal))
				{
					$context_object->referent->journal = new stdclass;
				}
				$context_object->referent->journal->name = $publication_outlet;
				$context_object->referent->type = 'article';
				break;
				
			case 'rft.issn':
			case 'issn':
				$ISSN_proto = $value[0];			
				$clean = ISN_clean($ISSN_proto);
				$class = ISSN_classifier($clean);
				if ($class == "checksumOK")
				{
					$identifier = new stdclass;
					$identifier->type = 'issn';
					$identifier->id =canonical_ISSN($ISSN_proto);

					if (!isset($context_object->referent->journal))
					{
						$context_object->referent->journal = new stdclass;
					}
					$context_object->referent->journal->identifier[] = $identifier;
				}
				break;

			// Identifiers
			case 'rft_id':
			case 'id':
				foreach ($value as $v)
				{		
					// DOI
					if (preg_match('/^(info:doi\/|doi:)(?<doi>.*)/', $v, $match))
					{
						$identifier = new stdclass;
						$identifier->type = 'doi';
						$identifier->id = $match['doi'];
					
						$context_object->referent->identifier[] = $identifier;
					}
					// Handle
					if (preg_match('/^(info:hdl\/|hdl:)(?<hdl>.*)/', $v, $match))
					{
						$identifier = new stdclass;
						$identifier->type = 'handle';
						$identifier->id = $match['hdl'];
					
						$context_object->referent->identifier[] = $identifier;
					}
					// PMID
					if (preg_match('/^(info:pmid\/|pmid:)(?<pmid>.*)/', $v, $match))
					{
						$identifier = new stdclass;
						$identifier->type = 'pmid';
						$identifier->id = $match['pmid'];
						
						$context_object->referent->identifier[] = $identifier;
					}
					// PMC
					if (preg_match('/^(pmc:)(?<pmc>.*)/', $v, $match))
					{
						$identifier = new stdclass;
						$identifier->type = 'pmc';
						$identifier->id = $match['pmc'];
						
						$context_object->referent->identifier[] = $identifier;
					}
					
					// Without INFO-URI prefix
					// LSID
					if (preg_match('/^urn:lsid:/', $v))
					{
						$identifier = new stdclass;
						$identifier->type = 'lsid';
						$identifier->id = $v;
						
						$context_object->referent->identifier[] = $identifier;
					}
					// URL (including PDFs)
					if (preg_match('/^http:\/\//', $v))
					{
						$matched = false;
						// PDF
						if (!$matched)
						{
							if (preg_match('/\.pdf/', $v))
							{
								$matched = true;
								$context_object->referent->pdf = $v;
							}
						}
						// BioStor
						if (!$matched)
						{
							if (preg_match('/http:\/\/biostor.org\/reference\/(?<id>\d+)$/', $v, $match))
							{
								$matched = true;
								
								$identifier = new stdclass;
								$identifier->type = 'biostor';
								$identifier->id = $match['id'];
								
								$context_object->referent->identifier[] = $identifier;
							}
						}
						if (!$matched)
						{
							$context_object->referent->link = new stdclass;
							$context_object->referent->link->url = $v;
						}						
					}					
				}
				break;

			// Authors 
			case 'rft.au':
			case 'au':
				foreach ($value as $v)
				{
					$parts = parse_name($v);					
					$author = new stdClass();
					if (isset($parts['last']))
					{
						$author->lastname = $parts['last'];
					}
					if (isset($parts['suffix']))
					{
						$author->suffix = $parts['suffix'];
					}
					if (isset($parts['first']))
					{
						$author->forename = $parts['first'];
						
						if (array_key_exists('middle', $parts))
						{
							$author->forename .= ' ' . $parts['middle'];
						}
					}
					$context_object->referent->author[] = $author;					
				}
				break;
				
			// article details
			case 'rft.volume':
			case 'volume':
				if (!isset($context_object->referent->journal))
				{
					$context_object->referent->journal = new stdclass;
				}
				$context_object->referent->journal->volume = $value[0];
				break;

			case 'rft.issue':
			case 'issue':
				if (!isset($context_object->referent->journal))
				{
					$context_object->referent->journal = new stdclass;
				}
				$context_object->referent->journal->issue = $value[0];
				break;

			case 'rft.spage':
			case 'spage':
				if (!isset($context_object->referent->journal))
				{
					$context_object->referent->journal = new stdclass;
				}
				$context_object->referent->journal->pages = $value[0];
				break;

			case 'rft.epage':
			case 'epage':
				if (!isset($context_object->referent->journal))
				{
					$context_object->referent->journal = new stdclass;
				}
				if (isset($context_object->referent->journal->pages))
				{
					$context_object->referent->journal->pages .= '--' . $value[0];
				}
				else
				{
					$context_object->referent->journal->pages = $value[0];
				}
				break;

			case 'rft.pages':
			case 'pages':
				if (!isset($context_object->referent->journal))
				{
					$context_object->referent->journal = new stdclass;
				}
				$context_object->referent->journal->pages = $value[0];
				break;

						
			default:
				$k = str_replace("rft.", '', $key);
				$context_object->referent->$k = $value[0];				
				break;
		} 
	}
	
	// Clean
	
	
	// Dates
	if (isset($context_object->referent->date))
	{
		if (preg_match('/^[0-9]{4}$/', $context_object->referent->date))
		{
			$context_object->referent->year = $context_object->referent->date;
			$context_object->referent->date = $context_object->referent->date . '-00-00';
		}
		if (preg_match('/^(?<year>[0-9]{4})-(?<month>[0-9]{2})-(?<day>[0-9]{2})$/', $context_object->referent->date, $match))
		{
			$context_object->referent->year = $match['year'];
			$context_object->referent->date = $match['year'] . '-' . $match['month'] . '-' . $match['day'];
		}
	}	
	
	// Zotero
	if (isset($context_object->referent->pages))
	{
		// Note "u" option in regular expression, so that we match UTF-8 characters such as –
		if (preg_match('/(?<spage>[0-9]+)[\-|–](?<epage>[0-9]+)/u', $context_object->referent->pages, $match))
		{
			$context_object->referent->spage = $match['spage'];
			$context_object->referent->epage = $match['epage'];
			unset($context_object->referent->pages);
		}
	}
	
	// Endnote epage may have leading "-" as it splits spage-epage to generate OpenURL
	if (isset($context_object->referent->epage))
	{
		$context_object->referent->epage = preg_replace('/^\-/', '', $context_object->referent->epage);
	}
	
	// Journal titles with series numbers are split into title,series fields
	if (preg_match('/(?<title>.*),?\s+series\s+(?<series>[0-9]+)$/i', $context_object->referent->journal->name, $match))
	{
		$context_object->referent->journal->name= $match['title'];
		$context_object->referent->journal->series= $match['series'];
	}		

	// Volume might have series information
	if (preg_match('/^series\s+(?<series>[0-9]+),\s*(?<volume>[0-9]+)$/i', $context_object->referent->journal->volume, $match))
	{
		$context_object->referent->journal->volume= $match['volume'];
		$context_object->referent->journal->series= $match['series'];
	}		
	
	// Author array might not be populated, in which case add author from aulast and aufirst fields
	if ((count($context_object->referent->author) == 0) && (isset($context_object->referent->aulast) && isset($context_object->referent->aufirst)))
	{
		$author = new stdClass();
		$author->surname = $context_object->referent->aulast;
		$author->forename = $context_object->referent->aufirst;
		$context_object->referent->author[] = $author;
	}	
	
	// Use aulast and aufirst to ensure first author name properly parsed
	if (isset($context_object->referent->aulast) && isset($context_object->referent->aufirst))
	{
		$author = new stdClass();
		$author->surname = $context_object->referent->aulast;
		$author->forename = $context_object->referent->aufirst;
		$context_object->referent->author[0] = $author;
	}	
	
	// EndNote encodes accented characters, which break journal names
	if (isset($context_object->referent->publication_outlet))
	{
		$context_object->referent->publication_outlet = preg_replace('/%9F/', 'ü', $context_object->referent->publication_outlet);
	}
}



//--------------------------------------------------------------------------------------------------
/**
 * @brief Handle OpenURL request
 *
 * We may have more than one parameter with same name, so need to access QUERY_STRING, not _GET
 * http://stackoverflow.com/questions/353379/how-to-get-multiple-parameters-with-same-name-from-a-url-in-php
 *
 */
function main()
{
	global $config;
	global $couch;
	
	global $debug;
	$debug = false;
	
	$webhook = '';
	$callback = '';
			
	// If no query parameters 
	if (count($_GET) == 0)
	{
		//display_form();
		exit(0);
	}	
	
	if (isset($_GET['webhook']))
	{
		$webhook = $_GET['webhook'];
	}
	
	if (isset($_GET['debug']))
	{	
		$debug = true;
	}
	
	if (isset($_GET['callback']))
	{	
		$callback = $_GET['callback'];
	}
	
	
	// Handle query and display results.
	$query = explode('&', html_entity_decode($_SERVER['QUERY_STRING']));
	$params = array();
	foreach( $query as $param )
	{
	  list($key, $value) = explode('=', $param);
	  
	  $key = preg_replace('/^\?/', '', urldecode($key));
	  $params[$key][] = trim(urldecode($value));
	}
	
	if ($debug)
	{
		echo '<h1>Params</h1>';
		echo '<pre>';
		print_r($params);
		echo '</pre>';
	}

	// This is what we got from user
	$context_object = new stdclass;
	parse_openurl($params, $context_object);
	
	if ($debug)
	{
		echo '<h1>Referent</h1>';
		echo '<pre>';
		print_r($context_object);
		echo '</pre>';
	}
	
	// OK, can we find this?
	
	// result object
	$openurl_result = new stdclass;
	$openurl_result->results = array();
	$openurl_result->query_ok = false;
	
		
	// via DOI or other identifier
	if (count($openurl_result->results) == 0)
	{			
		// identifier search
		if (isset($context_object->referent->identifier))
		{
			//print_r($context_object->referent->identifier);
			$found = false;
			foreach ($context_object->referent->identifier as $identifier)
			{
				//print_r($identifier);
				if (!$found)
				{
					switch ($identifier->type)
					{
						case 'doi':
							$url =  $config['couchdb_options']['database'] . "/_design/identifier/_view/doi?key=" . urlencode('"' . $identifier->id . '"');
							//echo $url . '<br />';
							
		
							$resp = $couch->send("GET", "/" . $url . '&limit=1' );
							$result = json_decode($resp);
				
							if (count($result->rows) == 1)
							{
								$found = true;
								
								$match = new stdclass;
								$match->match = true;
								$match->id = $result->rows[0]->id;
								$match->{$identifier->type} = $identifier->id;
								$openurl_result->results[] = $match;
								$openurl_result->query_ok = true;
							}
							
	
							break;
							
						default:
							break;
					}
				}
			}
		}
	}
	
	// classical OpenURL lookup using [ISSN,volume,spage] triple
	if (count($openurl_result->results) == 0)
	{
		$triple = false;
		if (isset($context_object->referent->journal->name))
		{
			$journal = $context_object->referent->journal->name;
			
			$journal = strtolower($journal);
			
			$journal = preg_replace('/\bfor\b/', '', $journal);
			$journal = preg_replace('/\band\b/', '', $journal);
			$journal = preg_replace('/\bof\b/', '', $journal);
			$journal = preg_replace('/\bthe\b/', '', $journal);
			
			// whitespace
			$journal = preg_replace('/\s+/', '', $journal);
		
			// punctuation
			$journal = preg_replace('/[\.|,|\'|\(|\)]+/', '', $journal);
	
			if (isset($context_object->referent->journal->volume))
			{
				$volume = $context_object->referent->journal->volume;
				
				if (isset($context_object->referent->journal->pages))
				{
					if (is_numeric($context_object->referent->journal->pages))
					{
						$spage = $context_object->referent->journal->pages;
						$triple = true;
					}
					else
					{
						if (preg_match('/^(?<spage>\d+)--(?<epage>\d+)$/', $context_object->referent->journal->pages, $m))
						{
							$spage = $m['spage'];
							$triple = true;					
						}
					}
				}
			}
			
			if ($triple)
			{
				$openurl_result->query_ok = true;
			
				// i. get ISSN
				
				$url =  $config['couchdb_options']['database'] . "/_design/openurl/_view/journal_to_issn?key=" . urlencode('"' . $journal . '"');
		
				//echo $url;
		
				$resp = $couch->send("GET", "/" . $url . '&limit=1' );
				$result = json_decode($resp);
				
				if (count($result->rows) == 1)
				{
					// we have an ISSN for this journal
					$issn = $result->rows[0]->value;
	
					// build triple [ISSN, volume, spage]			
					$keys = array(
						$issn, 
						$volume,
						$spage
						);
			
					$url = $config['couchdb_options']['database'] . "/_design/openurl/_view/triple?key=" . urlencode(json_encode($keys));
					//echo $url . '<br />';
	
					$resp = $couch->send("GET", "/" . $url );
					$r = json_decode($resp);
					//print_r($r);
					
					if (count($r->rows) > 0)
					{
						foreach ($r->rows as $row)
						{
							$match = new stdclass;
							$match->match = true;
							$match->triple = $keys;
							$match->id = $row->id;
							$openurl_result->results[] = $match;
						}
					}
	
				}
			}
				
		}
	}
	
	// full text search
	if (count($openurl_result->results) == 0)
	{			
		find_citation(reference_to_citation_string($context_object->referent), $openurl_result);
	}
	
	
	// ok, if we have one or more results we return these and let user/agent decide what to do
	
	// populate with details
	$num_results = count($openurl_result->results);
	for ($i = 0; $i < $num_results; $i++)
	{
		$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($openurl_result->results[$i]->id));
		$reference = json_decode($resp);
		if (!isset($reference->error))
		{
			$openurl_result->results[$i]->reference = $reference;
		}
	}
	
	
	// for now just return JSON	
	if ($callback != '')
	{
		echo $callback . '(';
	}
	echo json_encode($openurl_result);
	if ($callback != '')
	{
		echo ')';
	}
	
	
	
}

main();

?>