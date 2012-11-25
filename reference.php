<?php

// bibliographic reference

//--------------------------------------------------------------------------------------------------
/**
 * @brief Create a citation string for indexing
 * *
 * @param reference Reference object to be encoded
 *
 * @return OpenURL
 */
function reference_to_citation_string($reference)
{
	$citation = '';
	
	if (isset($reference->author))
	{
		$authors = array();
		foreach ($reference->author as $author)
		{
			if (isset($author->firstname))
			{
				$authors[] = $author->lastname . ' ' . $author->firstname;
			}
			else
			{
				$authors[] = $author->name;
			}
		
		}
		$citation .= join(', ', $authors);
	}
	
	if (isset($reference->year))
	{
		$citation .= ' (' . $reference->year . ')';
	}
	
	if (isset($reference->title))
	{
		$citation .= ' ' . $reference->title . '.';
	}
	
	if (isset($reference->journal))
	{
		$citation .= ' ' . $reference->journal->name;
		if (isset($reference->journal->volume))
		{
			$citation .= ', ' . $reference->journal->volume;
		}
		if (isset($reference->journal->issue))
		{
			$citation .= '(' . $reference->journal->issue . ')';
		}		
		if (isset($reference->journal->pages))
		{
			$citation .= ': ' . $reference->journal->pages;
		}
	}
	else
	{
		// not a journal...
		$citation .= ': ' . $reference->pages;		
	}
	
	return $citation;
}


//--------------------------------------------------------------------------------------------------
/**
 * @brief Convert BibJSON object to citeproc-js object
 *
 * @param reference Reference object to be converted
 * @param id Local id for citeproc-js object 
 *
 * @return citeproc-js object
 */
function reference_to_citeprocjs($reference, $id = 'ITEM-1')
{
	$citeproc_obj = array();
	$citeproc_obj['id'] = $id;
	$citeproc_obj['title'] = $reference->title;
	
	if (isset($reference->journal))
	{	
		$citeproc_obj['type'] = 'article-journal';
	}
	
	$citeproc_obj['issued']['date-parts'][] = array($reference->year);
	
	if (isset($reference->author))
	{
		$citeproc_obj['author'] = array();
		foreach ($reference->author as $author)
		{
			$a = new stdclass;
			if (isset($author->firstname))
			{
				$a->given = $author->firstname;
				$a->family = $author->lastname;
			}
			else
			{
				$a->literal = $author->name;
			}
			$citeproc_obj['author'][] = $a;			
		}
	}
	
	if (isset($reference->journal))
	{
		$citeproc_obj['container-title'] = $reference->journal->name;
		$citeproc_obj['volume'] = $reference->journal->volume;
		if (isset($reference->journal->issue))
		{
			$citeproc_obj['issue'] = $reference->journal->issue;
		}
		$citeproc_obj['page'] = str_replace('--', '-', $reference->journal->pages);
	}
	
	if (isset($reference->identifier))
	{
		foreach ($reference->identifier as $identifier)
		{
			switch ($identifier->type)
			{
				case 'doi':
					$citeproc_obj['DOI'] = $identifier->id;
					break;
					
				default:
					break;
			}
		}
	}
	
	return $citeproc_obj;
}

//--------------------------------------------------------------------------------------------------
/**
 * @brief Create a COinS (ContextObjects in Spans) for a reference
 *
 * COinS encodes an OpenURL in a <span> tag. See http://ocoins.info/.
 *
 * @param reference Reference object to be encoded
 *
 * @return HTML <span> tag containing a COinS
 */
function reference_to_coins($reference)
{
	global $config;
	
	$coins = '<span class="Z3988" title="' 
		. reference_to_openurl($reference) 
//		. '&amp;webhook=' . urlencode($config['web_server'] . $config['web_root'] . 'webhook.php')
		. '"></span>';
	return $coins;
}

//--------------------------------------------------------------------------------------------------
/**
 * @brief Create an OpenURL for a reference
 * *
 * @param reference Reference object to be encoded
 *
 * @return OpenURL
 */
function reference_to_openurl($reference)
{
	$openurl = '';
	$openurl .= 'ctx_ver=Z39.88-2004';

	// Local publication identifier
	if (isset($reference->id))
	{
		$openurl .= '&amp;rfe_id=' . urlencode($reference->id);
	}
	
	//print_r($reference);
	
	if (isset($reference->journal))
	{
		$openurl .= '&amp;rft_val_fmt=info:ofi/fmt:kev:mtx:journal';
		$openurl .= '&amp;genre=article';
		$openurl .= '&amp;rft.atitle=' . urlencode($reference->title);
		$openurl .= '&amp;rft.jtitle=' . urlencode($reference->journal->name);
	
		if (isset($reference->journal->series))
		{
			$openurl .= '&amp;rft.series=' . urlencode($reference->journal->series);
		}
		
		if (isset($reference->journal->identifier))
		{
			foreach ($reference->journal->identifier as $identifier)
			{
				switch ($identifier->type)
				{
					case 'issn':
						$openurl .= '&amp;rft.issn=' . $identifier->id;
						break;
						
					default:
						break;
				}
			}
		}
		
		if (isset($reference->journal->volume))
		{
			$openurl .= '&amp;rft.volume=' . $reference->journal->volume;
		}
		if (isset($reference->journal->issue))
		{
			$openurl .= '&amp;rft.issue=' . $reference->journal->issue;
		}		
		if (isset($reference->journal->pages))
		{
			if (preg_match('/^(?<spage>.*)--(?<epage>.*)/', $reference->journal->pages, $m))
			{
				$openurl .= '&amp;rft.spage=' . $m['spage'];
				$openurl .= '&amp;rft.epage=' . $m['epage'];
			}
			else
			{
				$openurl .= '&amp;rft.pages=' . $reference->journal->pages;
			}
		}
	}
	else
	{
		// not a journal...
		$openurl .= '&amp;rft.pages=' . $reference->journal->pages;
		
	}
	
	// generic stuff
	
	// authors
	if (isset($reference->author))
	{
		if (count($reference->author) > 0)
		{
			if (isset($reference->author[0]->lastname))
			{
				$openurl .= '&amp;rft.aulast=' . urlencode($reference->author[0]->lastname);
				if ($reference->author[0]->firstname)
				{
					$openurl .= '&amp;rft.aufirst=' . urlencode($reference->author[0]->firstname);
				}
			}
		}
		foreach ($reference->author as $author)
		{
			$openurl .= '&amp;rft.au=' . urlencode($author->name);
		}
	}
	
	// date
	$openurl .= '&amp;rft.date=' . $reference->year;
	
	// identifiers
	if (isset($reference->identifier))
	{
		foreach ($reference->identifier as $identifier)
		{
			switch ($identifier->type)
			{
				case 'doi':
					$openurl .= '&amp;rft_id=info:doi/' . urlencode($identifier->id);
					break;
					
				case 'handle':
					$openurl .= '&amp;rft_id=info:hdl/' . urlencode($identifier->id);
					break;

				case 'pmid':
					$openurl .= '&amp;rft_id=info:pmid/' . urlencode($identifier->id);
					break;

				default:
					break;
			}
		}				
	}
	
	if (isset($reference->link))
	{
		foreach ($reference->link as $link)
		{
			if (isset($link->anchor))
			{
				if ($link->anchor == 'LINK')
				{
					$openurl .= '&amp;rft_id='. urlencode($link->url);
				}
			}
		}
	}
	
	
	return $openurl;
}

//--------------------------------------------------------------------------------------------------
function reference_to_ris($reference)
{
	$ris = '';
	
	if (isset($reference->journal))
	{
		$ris .= "TY  - JOUR\n";
	}
	else
	{
		$ris .= "TY  - GEN\n";
	}

	
	if (isset($reference->id))
	{
		$ris .=  "ID  - " . $reference->id . "\n";
	}
	
	if (isset($reference->author))
	{
		foreach ($reference->author as $a)
		{
			if (is_object($a))
			{
				$ris .= "AU  - " . $a->name . "\n";
			}
		}
	}
	
	if (isset($reference->title))
	{
		$ris .=  "TI  - " . strip_tags($reference->title) . "\n";
	}
	
	if (isset($reference->journal)) 
	{
		$ris .=  "JF  - " . $reference->journal->name . "\n";
		if (isset($reference->journal->volume))
		{
			$ris .=  "VL  - " . $reference->journal->volume . "\n";
		}
		if (isset($reference->journal->issue))
		{
			$ris .=  "IS  - " . $reference->journal->issue . "\n";
		}
		
		foreach ($reference->journal->identifier as $identifier)
		{
			switch ($identifier->type)
			{
				case 'issn':
					$ris .=  "SN  - " . $identifier->id . "\n";
					break;
					
				default:
					break;
			}
		}
		
		if (isset($reference->journal->pages))
		{
			if (preg_match('/^(?<spage>.*)--(?<epage>.*)/', $reference->journal->pages, $m))
			{
				$ris .=  "SP  - " . $m['spage'] . "\n";
				$ris .=  "EP  - " . $m['epage'] . "\n";
			}
			else
			{
				$ris .=  "SP  - " . $reference->journal->pages . "\n";
			}
		}
		
		
	}
	
	if (isset($reference->year))
	{
		$ris .=  "Y1  - " . $reference->year . "\n";
	}
	
	if (isset($reference->identifier))
	{
		foreach ($reference->identifier as $identifier)
		{
			switch ($identifier->type)
			{
				case 'doi':
					$ris .=  "DO  - " . $identifier->id . "\n";
					break;
					
				default:
					break;
			}
		}
	}	
	
	$ris .=  "ER  - \n";
	$ris .=  "\n";
	
	return $ris;
}

?>