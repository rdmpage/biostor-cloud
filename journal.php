<?php

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/lib.php');


$issn = '';

if (isset($_GET['issn']))
{
	$issn = $_GET['issn'];
}

$journal_obj =  null;

if ($issn != '')
{
	$journal_obj = new stdclass;
	$journal_obj->issn = $issn;
	$journal_obj->preceding = array();
	$journal_obj->succeeding = array();
	$journal_obj->other = array();
	
	$couchdb_id = 'issn/' . $issn;
	
	// do we have this?
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($couchdb_id));	
	$obj = json_decode($resp);
	
	if (isset($obj->error))
	{
		// no, so fetch from WorldCat and store
		$url = 'http://xissn.worldcat.org/webservices/xid/issn/' . $issn . '?method=getHistory&format=json';
		
		$json = get($url);
		if ($json != '')
		{
			$couch->send('PUT', "/" . $config['couchdb_options']['database'] . "/" . urlencode($couchdb_id), $json);
	
			$obj = json_decode($json);
			$obj->_id = $couchdb_id;
		}
	}
	
	//print_r($obj);
	
	if (isset($obj->group))
	{
		foreach ($obj->group as $g)
		{
			foreach ($g->list as $list)
			{
				switch ($g->rel)
				{
					case 'this':
						if ($list->issn == $issn)
						{
							$journal_obj->title = $list->title;
							if (isset($list->rawcoverage))
							{
								$journal_obj->rawcoverage = $list->rawcoverage;
							}
							if (isset($list->rssurl))
							{
								$journal_obj->rssurl = $list->rssurl;
							}
							if (isset($list->issnl))
							{
								$journal_obj->issnl = $list->issnl;
							}
						}
						else
						{
							$journal_obj->other[] = $list->issn;
						}
						break;
						
					case 'preceding':
						$journal_obj->preceding[] = $list->issn;
						break;
						
					case 'succeeding':
						$journal_obj->succeeding[] = $list->issn;
						break;
						
						
					default:
						break;
				}
			}
		}
	}
			
	
	// clean
	$journal_obj->preceding = array_unique($journal_obj->preceding);
	$journal_obj->other = array_unique($journal_obj->other);
}

echo '<!DOCTYPE html>
	<html>
        <head>
            <meta charset="utf-8"/>
            
            <base href="<BASE>" />
            
            <title>';
            
           if ($journal_obj)
           {
           	echo $journal_obj->title;
           }
            
        echo '</title>

			<!-- Le styles -->
			<link href="assets/css/bootstrap.css" rel="stylesheet">
			<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

			<style type="text/css">
			  .item-links a { margin-right:40px; }			  
			</style>
			<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
			
			<link href="font-awesome.css" media="screen" rel="stylesheet" type="text/css" />						
		
			<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
			  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
			
			
			<script>';
			
			echo "

function referenceToOpenUrl(reference)
{
	var openurl_keys=[];
	openurl_keys['rft_val_fmt'] = 'rft_val_fmt';
	openurl_keys['jtitle'] 		= 'rft.atitle';
	openurl_keys['title'] 		= 'rft.btitle';
	openurl_keys['authors'] 	= 'rft.au';
	openurl_keys['journal'] 	= 'rft.title';
	openurl_keys['volume'] 		= 'rft.volume';
	openurl_keys['issue'] 		= 'rft.issue';
	openurl_keys['spage'] 		= 'rft.spage';
	openurl_keys['epage'] 		= 'rft.epage';
	openurl_keys['year'] 		= 'rft.date';	
	openurl_keys['issn'] 		= 'rft.issn';	
	openurl_keys['rft_id'] 		= 'rft_id';	
	
	var parameters=[];
	parameters.push('url_ver=Z39.88-2004');
	var delimiter = '&';
	
	for (property in reference)
	{
		switch (property)
		{				
			case 'title':
				// what kind of reference?
				if (reference.journal)
				{
					parameters.push(openurl_keys['jtitle'] + '=' + encodeURIComponent(reference[property]));
				}
				else
				{
					parameters.push(openurl_keys['btitle'] + '=' + encodeURIComponent(reference[property]));
				}
				break;
				
			case 'identifier':
				for (j in reference.identifier)
				{
					switch (reference.identifier[j].type)
					{
						case 'biostor':
							parameters.push(openurl_keys['rft_id'] + '=' + 'http://biostor.org/reference/' + reference.identifier[j].id);
							break;

						case 'doi':
							parameters.push(openurl_keys['rft_id'] + '=' + 'info:doi/' + reference.identifier[j].id);
							break;

						case 'handle':
							parameters.push(openurl_keys['rft_id'] + '=' + 'info:hdl/' + reference.identifier[j].id);
							break;

						case 'pmid':
							parameters.push(openurl_keys['rft_id'] + '=' + 'info:pmid/' + reference.identifier[j].id);
							break;
							
						default:
							break;
					}
				}
				break;
				
				
			case 'journal':
				for (p in reference.journal)
				{
					switch(p)
					{
						case 'name':
							parameters.push(openurl_keys['journal'] + '=' + reference.journal[p]);
							break;
							
						case 'pages':
            				var startingPage = reference.journal.pages;
            				var endingPage = null;
              				var pagePelimiter = reference.journal.pages.indexOf('-');
              				if (pagePelimiter != -1)
              				{
              					startingPage = reference.journal.pages.substring(0, pagePelimiter);
              					endingPage = reference.journal.pages.substring(pagePelimiter+2);
               				}
               				parameters.push(openurl_keys['spage'] + '=' + startingPage);
               				if (endingPage)
               				{
               					parameters.push(openurl_keys['epage'] + '=' + endingPage);
               				} 						
							break;
							
						case 'identifier':
							for (j in reference.journal.identifier)
							{
								if (reference.journal.identifier[j].type == 'issn')
								{
									parameters.push(openurl_keys['issn'] + '=' + reference.journal.identifier[j].id);
								}
							}
							break;
							
						default:
							if (p in openurl_keys)
							{
								parameters.push(openurl_keys[p] + '=' + reference.journal[p]);
							}
							break;
					}
				}				
				break;
				
			default:
				if (property in openurl_keys)
				{
					parameters.push(openurl_keys[property] + '=' + reference[property]);
				}
				break;
		}
	}

  
	var openurl = parameters.join(delimiter);

	return openurl;
}
	";
	
	echo '
			
			
			 function volume(issn, year, volume)
			  {
				$("#volume").html("");
				
				var html = "<h2>Volume " + volume + " for " + year + "</h2>";
				$("#volume").html(html);

				var startkey = [];
				startkey.push(issn);
				startkey.push(parseInt(Math.floor(year/10)));
				startkey.push(parseInt(year));
				startkey.push(volume);

				var endkey = [];
				endkey.push(issn);
				endkey.push(parseInt(Math.floor(year/10)));
				endkey.push(parseInt(year));
				endkey.push(volume);
				endkey.push({});
				
				$.getJSON("issn_year_volume.php?startkey=" + encodeURIComponent(JSON.stringify(startkey)) + "&endkey=" + encodeURIComponent(JSON.stringify(endkey)) + "&callback=?",
					function(data){
						if (data.rows)
						{
							html += "<table class=\"table\">";
							html += "<thead>";
							html += "</thead>";
							html += "<tbody>";
							for (var i in data.rows)
							{							
								
								html += "<tr>";
								html += "<td style=\"text-align:center;width:100px;\">";
								if (data.rows[i].doc.thumbnail)
								{
									html += "<img style=\"box-shadow:2px 2px 2px #ccc;width:64px;\" src=\"" + data.rows[i].doc.thumbnail + "\">";
								}
								html += "</td>";
								
								html += "<td class=\"item-data\">";

								html += "<p style=\"font-size:16px;font-weight:200;line-height:18px;\">";
								html += "<a href=\"id/" + data.rows[i].doc._id + "\">";
								html += data.rows[i].doc.title;
								html += "</a>";
								html += "</p>";
								
								html += "<div>";
								
								if (data.rows[i].doc.author)
								{
									var authors = [];
									
									for (var j in data.rows[i].doc.author)
									{
										if (data.rows[i].doc.author[j].name)
										{
											authors.push(data.rows[i].doc.author[j].name);
										}
									}
									html += "by " + authors.join(", ");
									
								}
								
								if (data.rows[i].doc.journal)
								{
									if (data.rows[i].doc.journal.pages)
									{
										html += " pages <b>" + data.rows[i].doc.journal.pages.replace(/--/, "-") + "</b>";
									}
								}
								
								html += "</div>";
								
								html += "<span class=\"Z3988\" title=\"" + referenceToOpenUrl(data.rows[i].doc) + "\"></span>";								
								
								if (data.rows[i].doc.identifier)
								{
									html += "<div class=\"item-links\">";
									for (var j in data.rows[i].doc.identifier)
									{
										switch (data.rows[i].doc.identifier[j].type)
										{
											case "biostor":
												html += "<a href=\"http://biostor.org/reference/" + data.rows[i].doc.identifier[j].id + "\" target=\"_new\"><i class=\"icon-external-link\"></i>biostor.org/reference/" + data.rows[i].doc.identifier[j].id + "</a></li>";
												break;
												
											case "doi":
												html += "<a href=\"http://dx.doi.org/" + data.rows[i].doc.identifier[j].id + "\" target=\"_new\"><i class=\"icon-external-link\"></i>dx.doi.org/" + data.rows[i].doc.identifier[j].id + "</a></li>";
												break;
												
											default:
												break;
										}
									}
									html += "</div>";
								}
								
								
								
								html += "</td>";
								html += "</tr>";
								
							}
							html += "</tbody>";
							html += "</table>";
						}
						$("#volume").html(html);
					}
					);
				}
			</script>
				
				
		</head>
		<body>';
		
echo '		   <div class="navbar navbar-fixed-top">
			  <div class="navbar-inner">
				<div class="container">
				  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				  </a>
				  <a class="brand" href=".">Biostor in the cloud</a>
				  <div class="nav-collapse">
					<ul class="nav">
					  <li  class="active"><a href=".">Home</a></li>
					  <li><a href="?page=about">About</a></li>
					</ul>
				  </div><!--/.nav-collapse -->
				  
					<form class="navbar-search pull-left" method="get" action=".">
						<input type="text" id=\'q\' name=\'q\' class="search-query" placeholder="Search">
					</form>          
				  
				  
				</div>
			  </div>
			</div>
		
		
			<div style="margin-top:40px;" class="container-fluid">';		
			
		if ($journal_obj)
           {
            	echo '<h1>' .  $journal_obj->title . '</h1>';
           }

			echo '<div style="margin-top:40px;padding:0px;" class="container-fluid">			
				<div class="row-fluid">
					<div class="span4">'; 


// Search for journal articles

$startkey = array($issn);
$endkey = array($issn, new stdclass);

//echo json_encode($endkey);

$url = '_design/issn/_view/year?startkey=' . json_encode($startkey) . '&endkey=' . json_encode($endkey) . '&group_level=4';

//echo $url . "\n";

//exit();

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

		
$obj = json_decode($resp);
if (isset($obj->error))
{
}
else
{
	//print_r($obj);
	
	// group into decades	
	$decades = array();
	foreach ($obj->rows as $row)
	{
		if (!isset($row->key[1]))
		{
			$decades[$row->key[1]] = array();
		}
		$volume = new stdclass;
		$volume->volume = $row->key[3];
		$volume->count = $row->value;
		$decades[$row->key[1]][$row->key[2]] = $volume;
	}	
	
	echo '<div class="accordion" id="accordion">';
	
	foreach ($decades as $decade => $years)
	{
		echo '<div class="accordion-group">';
		echo '  <div class="accordion-heading">';
		echo '  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $decade . '">';
		echo $decade . "0's";
        echo '  </a>';
        echo '  </div>';
        
        echo '  <div id="collapse' . $decade . '" class="accordion-body collapse">';
		echo '    <div class="accordion-inner" id="accordionInner' . $decade . '">';
		
		echo '<ul>';
		foreach ($years as $year => $volume)
		{
			$startkey = array($issn, (Integer)$decade, (Integer)$year, $volume->volume);
			$endkey = array($issn, (Integer)$decade, (Integer)$year, $volume->volume, new stdclass);
			
//			echo '<li onclick="volume(\'' . urlencode(json_encode($startkey)) . '\',\'' . urlencode(json_encode($endkey)) . '\')" />' . $year . ' Vol. ' .  $volume->volume . ' [' . $volume->count . ']' . '</li>';
			echo '<li onclick="volume(\'' . $issn . '\',' . (Integer)$year . ',\'' . $volume->volume . '\')" />' . $year . ' Vol. ' .  $volume->volume . ' [' . $volume->count . ']' . '</li>';
		}
		echo '</ul>';
		
		// get papers grouped by year
		/*
		$startkey = array($issn, $row->key[1], $row->key[1] * 10);
		$endkey = array($issn, $row->key[1], $row->key[1] * 10 + 9, new stdclass);
		
		$url = '_design/issn/_view/year?startkey=' . json_encode($startkey) . '&endkey=' . json_encode($endkey) . '&group_level=3';


		//echo $url . "\n";
		
		$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
		
		//echo $resp;
		
		$decade = json_decode($resp);
		
		// now list articles...
		
		foreach ($decade->rows as $year)
		{
			$startkey = array($issn, $year->key[1], $year->key[2]);
			$endkey = array($issn, $year->key[1], $year->key[2], new stdclass);
		
			$url = '_design/issn/_view/year?startkey=' . json_encode($startkey) . '&endkey=' . json_encode($endkey) . '&reduce=false&include_docs=true';
			echo $url . "\n";
		
			$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
		
			echo $resp;
		
			
		}
		
		*/
		
		echo '    </div>';
		echo '  </div>';
		echo '</div>';
		
		
	}
	echo '</div>';
}

echo '		</div>
	<div class="span8">
		<div id="volume"></div>
	</div>';

echo '</div>';

echo 	   '<!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/bootstrap.js"></script>

		
		</body>
	</html>'

?>