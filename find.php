<?php

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/lcs.php');

//--------------------------------------------------------------------------------------------------
function clean_string($str)
{
	$str = preg_replace("/[\.\?!\-|\s|:|,|;|\(|\)\[|\]]+/", ' ', $str);
	$str = preg_replace("/\s\s+/", ' ', $str);
	
	return $str;
}

//--------------------------------------------------------------------------------------------------
function find_citation($citation, &$result, $threshold = 0.8)
{
	global $config;
	global $couch;
	
	$q = clean_string($citation);

	$rows_per_page = 5;
	$url = '/_design/citation/_search/all?q=' . urlencode($q) . '&limit=' . $rows_per_page;
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	$obj = json_decode($resp);
		
	if (isset($obj->error))
	{
	}
	else
	{
		$result->query_ok = true;
		if ($obj->total_rows > 0)
		{
			$best_hit = 0;
		
			$q = strtolower($q);
		
			foreach ($obj->rows as $row)
			{
				$hit = $row->fields->default;
				$hit_original = $hit;
				$hit = clean_string($hit);
		
				$hit = strtolower($hit);
					
				$query_length = strlen($q);
				$hit_length = strlen($hit);
					
				$C = LCSLength($hit, $q);
					
				// length of subsequence as percentage of query string
				$subsequence_length =  round((100.0 * $C[$hit_length][$query_length])/$query_length);
						
				$symdiff = 1.0 - ($query_length + $hit_length - 2 * $C[$hit_length][$query_length])/($query_length + $hit_length);
				
				if ($symdiff > $threshold)
				{
					if ($symdiff >= $best_hit)
					{
						$best_hit = $symdiff;
						
						$match = new stdclass;
						$match->text = $citation;
						$match->hit = $hit_original;
						$match->match = true;
						$match->id = $row->id;
						$match->score = $row->order[0];
						$match->symdiff = $symdiff;
						
						if ($symdiff > $best_hit)
						{
							$result->results = array();
						}						
						$result->results[] = $match;
					}
				}
			}
		}
	}
	return (count($result->results) > 1);
}

if (0)
{
	// test
	
	$result = new stdclass;
	$result->results = array();
	$result->query_ok = false;
	
	
	$q = 'Monroe, R (1977) A new species of Euastacus (Decapoda: Parastacidae) from north Queensland. Memoirs of the Queensland Museum 18:65-67';
	
	$q = 'Clark, E (1936) The freshwater and land crayfishes of Australia. Memoirs of the Natural Museum of Victoria 10:5-58';

	
	find_citation($q, $result);
	
	print_r($result);
}


?>

