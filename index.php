<?php

require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/lib.php');
require_once (dirname(__FILE__) . '/reference.php');

//--------------------------------------------------------------------------------------------------
function default_display()
{
	global $config;
	global $couch;
	
    $template = <<<EOT
<!DOCTYPE html>
	<html>
        <head>
            <meta charset="utf-8"/>
            
            <title>Biostor in the cloud</title>

			<!-- Le styles -->
			<link href="assets/css/bootstrap.css" rel="stylesheet">
			<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
			
			<link href='font-awesome.css' media='screen' rel='stylesheet' type='text/css' />			
		
			<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
			  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->            
			
		</head>
		<body>

		   <div class="navbar navbar-fixed-top">
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
					  <li class="active"><a href=".">Home</a></li>
					  <li><a href="?page=about">About</a></li>
					</ul>
				  </div><!--/.nav-collapse -->
				  
					<form class="navbar-search pull-left" method="get" action=".">
						<input type="text" id='q' name='q' class="search-query" placeholder="Search">
						
					</form>          
				  
				  
				</div>
			  </div>
			</div>
		
		
			<div style="margin-top:40px;" class="container-fluid">
			
				<!-- basic info about this site -->
				
				      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h1>BioStor in the cloud</h1>
        <p class="lead">Experiments with putting BioStor content in the cloud. <a href="?q=new species">Try it</a></p>
        <h3><COUNT> publications</h3>
      </div>

 <!-- Example row of columns -->
      <div class="row">
      	<!--
        <div class="span4">
        	<div style="text-align:center"><img src="images/iczn_logo.png" style="height:200px;"/></div>
          <p>Search for Cases and Opinions from the International Commission for Zoological Nomenclature.</p>
        </div>
        -->
        <!--
        <div class="span4">
          <div style="text-align:center"><img src="images/biostor-shadow.png" style="height:128px;padding:37px;"/></div>
          <p>Articles from BioStor, metadata from CrossRef and other sources.</p>
       </div>
        <div class="span4">
          <div style="text-align:center"><img src="images/BHL_Small_Logo.jpg" style="height:200px;"/></div>
          <p>Page images from the Biodiversity Heritage Library.</p>
       </div>
        <div class="span4">
          <div style="text-align:center"><img src="images/cloudant.png" style="height:200px;"/></div>
          <p>Data hosting and full-text search provided by Cloudant.</p>
       </div> -->
       
        <div class="span4">
          <div style="text-align:center"><a href="id/biostor/111313"><img src="images/119204721358747857.png" style="height:128px;padding:37px;"/></a></div>
          <p>On a new species of Pericrocotus from Sumbawa</p>
       </div>
              
         <div class="span4">
          <div style="text-align:center"><a href="id/biostor/59435"><img src="images/119204721357928704.png" style="height:128px;padding:37px;"/></a></div>
          <p>Culex aegypti Linnaeus, 1762 (Insecta, Diptera): proposed validation and interpretation under the plenary powers of the species so named. Z.N. (S.) 1216</p>
       </div>
       
       <!--
       <div class="span4">
          <div style="text-align:center"><a href="id/biostor/100935"><img src="images/119204721357814657.png" style="height:128px;padding:37px;"/></a></div>
          <p>Revision of the genus Islamia Radoman, 1973 (Gastropoda, Caenogastropoda, Hydrobiidae), on the Iberian Peninsula and description of two new genera and three new species</p>
       </div>
 		-->
 		
       <div class="span4">
          <div style="text-align:center"><a href="id/biostor/114"><img src="images/119204721358393734.png" style="height:128px;padding:37px;"/></a></div>
          <p>A new species of small Barbus (Pisces, Cyprinidae) from Tanzania, east Africa</p>
       </div>
 
       
        
      
       
      </div>
			

			</div>			


	   <!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/bootstrap.js"></script>
			
		
		</body>
	</html>
EOT;

	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/_design/count/_view/biostor");
	$result = json_decode($resp);
	if (isset($result->error))
	{
	}
	else
	{
		$template = str_replace('<COUNT>', $result->rows[0]->value, $template);
	}
	
	

	echo $template;

}

//--------------------------------------------------------------------------------------------------
function display_page($page)
{
    $template = <<<EOT
<!DOCTYPE html>
	<html>
        <head>
            <meta charset="utf-8"/>
            
            <title>BioStor in the cloud</title>

			<!-- Le styles -->
			<link href="assets/css/bootstrap.css" rel="stylesheet">
			<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
			
			<link href='font-awesome.css' media='screen' rel='stylesheet' type='text/css' />			
		
			<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
			  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->            
			
		</head>
		<body>

		   <div class="navbar navbar-fixed-top">
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
					  <li><a href=".">Home</a></li>
					  <li class="active"><a href="?page=about">About</a></li>
					</ul>
				  </div><!--/.nav-collapse -->
				  
					<form class="navbar-search pull-left" method="get" action=".">
						<input type="text" id='q' name='q' class="search-query" placeholder="Search">
					</form>          
				  
				  
				</div>
			  </div>
			</div>
		
		
			<div style="margin-top:40px;" class="container-fluid">
			
 <div class="container">

      <h1>About this site</h1>
      
      <table>
      	<tr><td><img src="images/cloudant.png" style="width:64px;" /></td><td>Data hosting and full-text search by <a href="http://cloudant.com">Cloudant</a>.</td></tr>
      	
      	<tr><td><img src="images/pagodabox.png" style="width:64px;" /></td><td>Hosted by <a href="http://pagodabox.com/">Pagoda Box</a>.</td></tr>
      	
      	<tr><td><img src="images/biostor-shadow.png" style="width:64px;" /></td><td>Article metadata from BioStor</td></tr>
      	
		<tr><td><img src="images/BHL_Small_Logo.jpg" style="width:64px;" /></td><td>Page scans from BHL</td></tr>

		<tr><td><img src="images/logo_gallica.png" style="width:64px;" /></td><td>Page scans from <a href="http://gallica.bnf.fr/">Gallica</a></td></tr>
		
		<tr><td><img src="images/documentcloud.png" style="width:64px;" /></td><td>Document viewer from DocumentCloud</td></tr>
		
		<tr><td><img src="images/citeproc-js-logo_avatar.png" style="width:64px;" /></td><td>Citation formatting using citeproc-js</td></tr>
		
		<tr><td><img src="images/crossrefsquare.gif" style="width:64px;" /></td><td>Metadata and inspiration</td></tr>
		
		<tr><td><img src="images/Mairi_drawing256x256.png" style="width:64px;" /></td><td>Blood, sweat, and tears</td></tr>
      </table>

    </div> <!-- /container -->
			</div>			


	   <!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/bootstrap.js"></script>
			
		
		</body>
	</html>
EOT;

	echo $template;
}

//--------------------------------------------------------------------------------------------------
function display_record($id)
{
	global $config;
	global $couch;
	
	$reference = null;
	
	if (0)
	{
		// for now grab this from BioStor
		$json = get('http://biostor.org/reference/' . $id . '.bibjson');
		$reference = json_decode($json);
	}
	else
	{
		// grab JSON from CouchDB
		$couch_id = $id;
		
		$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($couch_id));
		
		$reference = json_decode($resp);
		if (isset($reference->error))
		{
			// bounce
			header('Location: ' . $config['web_root'] . "\n\n");
			exit(0);
		}
	}
	
	
	$citeproc_obj = reference_to_citeprocjs($reference, 'ITEM-1');
	$bibdata['ITEM-1'] = $citeproc_obj;
	$bibdata_json =  json_encode($bibdata);

	// HTML template
    $template = <<<EOT
<!DOCTYPE html>
	<html>
        <head>
            <meta charset="utf-8"/>
            
            <base href="<BASE>" />
            
            <title><TITLE></title>

			<!-- Le styles -->
			<link href="assets/css/bootstrap.css" rel="stylesheet">
			<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
			
			<link href='font-awesome.css' media='screen' rel='stylesheet' type='text/css' />			
		
			<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
			  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->            
			
			<!-- citeproc-js -->
			<script src="citeproc-js/loadabbrevs.js"></script>
			<script src="citeproc-js/xmldom.js"></script>
			<script src="citeproc-js/citeproc.js"></script>
			<script src="citeproc-js/loadlocale.js"></script>
			<script src="citeproc-js/loadsys.js"></script>
			<!--<script src="citeproc-js/loadcsl.js"></script>		-->
			
			<script>

			var bibdata = <BIBDATA>;
			
			// This defines the mechanism by which we get hold of the relevant data for
			// the locale and the bibliography. 
			// 
			// In this case, they are pretty trivial, just returning the data which is
			// embedded above. In practice, this might involving retrieving the data from
			// a standard URL, for instance. 
			var sys = {
				retrieveItem: function(id){
					return bibdata[id];
				},
			
				retrieveLocale: function(lang){
					return locale[lang];
				}
			}
						
			// This is the citation object. Here, we have hard-coded this, so it will only
			// work with the correct HTML. 
			var citation_object = 
				{
					// items that are in a citation that we want to add. in this case,
					// there is only one citation object, and we know where it is in
					// advance. 
					"citationItems": [
						{
							"id": "ITEM-1"
						}
					],
					// properties -- count up from 0
					"properties": {
						"noteIndex": 0
					}
					  
				}
			
			
			var cite_styles = new Array();
			
			cite_styles['chicago_author_date'] = "<style       xmlns=\"http://purl.org/net/xbiblio/csl\"      class=\"in-text\"   default-locale=\"en-US-x-sort-ja-alalc97-x-sec-en\">  <!-- BOGUS COMMENT -->  <info>    <title>Chicago Manual of Style (Author-Date format)</title>    <id>http://www.zotero.org/styles/chicago-author-date</id>    <link href=\"http://www.zotero.org/styles/chicago-author-date\" />    <author>      <name>Julian Onions</name>      <email>julian.onions@gmail.com</email>    </author>    <category term=\"author-date\" />    <category term=\"generic-base\" />    <updated />    <summary>The author-date variant of the Chicago style</summary>    <link href=\"http://www.chicagomanualofstyle.org/tools_citationguide.html\" rel=\"documentation\" />  </info> <macro name=\"secondary-contributors\">    <choose>      <if match=\"none\" type=\"chapter\">        <group delimiter=\". \">          <choose>            <if variable=\"author\">              <names variable=\"editor\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"capitalize-first\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>          <choose>            <if match=\"any\" variable=\"author editor\">              <names variable=\"translator\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"capitalize-first\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>        </group>      </if>    </choose>  </macro><!-- BOGUS COMMENT -->    <macro name=\"container-contributors\">    <choose>      <if type=\"chapter\">        <group delimiter=\", \" prefix=\",\">          <choose>            <if variable=\"author\">              <names variable=\"editor\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"lowercase\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>          <choose>            <if match=\"any\" variable=\"author editor\">              <names variable=\"translator\">                <label form=\"verb-short\" prefix=\" \" suffix=\". \" text-case=\"lowercase\" />                <name and=\"text\" delimiter=\", \" />              </names>            </if>          </choose>        </group>      </if>    </choose>  </macro>  <macro name=\"anon\">    <choose>      <if match=\"none\" variable=\"author editor translator\">        <text form=\"short\" term=\"anonymous\" text-case=\"capitalize-first\" />      </if>    </choose>  </macro>  <macro name=\"editor\">    <names variable=\"editor\">      <name and=\"text\" delimiter=\", \" delimiter-precedes-last=\"always\" name-as-sort-order=\"first\" sort-separator=\", \" />      <label form=\"short\" prefix=\", \" suffix=\".\" />    </names>  </macro>  <macro name=\"translator\">    <names variable=\"translator\">      <name and=\"text\" delimiter=\", \" delimiter-precedes-last=\"always\" name-as-sort-order=\"first\" sort-separator=\", \" />      <label form=\"verb-short\" prefix=\", \" suffix=\".\" />    </names>  </macro>  <macro name=\"recipient\">    <choose>      <if type=\"personal_communication\">        <choose>          <if variable=\"genre\">            <text text-case=\"capitalize-first\" variable=\"genre\" />          </if>          <else>            <text term=\"letter\" text-case=\"capitalize-first\" />          </else>        </choose>      </if>    </choose>    <names delimiter=\", \" variable=\"recipient\">      <label form=\"verb\" prefix=\" \" suffix=\" \" text-case=\"lowercase\" />      <name and=\"text\" delimiter=\", \" />    </names>  </macro>  <macro name=\"contributors\">    <names variable=\"author\">      <name and=\"text\" delimiter=\", \" delimiter-precedes-last=\"always\" name-as-sort-order=\"first\" sort-separator=\", \" />      <label form=\"verb-short\" prefix=\", \" suffix=\".\" text-case=\"lowercase\" />      <substitute>        <text macro=\"editor\" />        <text macro=\"translator\" />      </substitute>    </names>    <text macro=\"anon\" />    <text macro=\"recipient\" />  </macro>  <macro name=\"contributors-short\">    <names variable=\"author\">      <name and=\"text\" delimiter=\", \" form=\"short\" />      <substitute>        <names variable=\"editor\" />        <names variable=\"translator\" />      </substitute>    </names>    <text macro=\"anon\" />  </macro>  <macro name=\"interviewer\">    <names delimiter=\", \" variable=\"interviewer\">      <label form=\"verb\" prefix=\" \" suffix=\" \" text-case=\"capitalize-first\" />      <name and=\"text\" delimiter=\", \" />    </names>  </macro>  <macro name=\"archive\">    <group delimiter=\". \">      <text text-case=\"capitalize-first\" variable=\"archive_location\" />      <text variable=\"archive\" />      <text variable=\"archive-place\" />    </group>  </macro>  <macro name=\"access\">    <group delimiter=\". \">      <choose>        <if match=\"any\" type=\"graphic report\">          <text macro=\"archive\" />        </if>        <else-if match=\"none\" type=\"book thesis chapter article-journal article-newspaper article-magazine\">          <text macro=\"archive\" />        </else-if>      </choose>      <text prefix=\"doi:\" variable=\"DOI\" />      <text variable=\"URL\" />    </group>  </macro>  <macro name=\"title\">    <!-- BOGUS COMMENT -->  <choose>      <if match=\"none\" variable=\"title\">        <choose>          <if match=\"none\" type=\"personal_communication\">            <text text-case=\"capitalize-first\" variable=\"genre\" />          </if>        </choose>      </if>      <else-if type=\"book\">        <text font-style=\"italic\" variable=\"title\" />      </else-if>      <else>        <text variable=\"title\" />      </else>    </choose>  </macro>  <macro name=\"edition\">    <choose>      <if match=\"any\" type=\"book chapter\">        <choose>          <if is-numeric=\"edition\">            <group delimiter=\" \">              <number form=\"ordinal\" variable=\"edition\" />              <text form=\"short\" suffix=\".\" term=\"edition\" />            </group>          </if>          <else>            <text suffix=\".\" variable=\"edition\" />          </else>        </choose>      </if>    </choose>  </macro>  <macro name=\"locators\">    <choose>      <if type=\"article-journal\">        <text prefix=\" \" variable=\"volume\" />        <text prefix=\", no. \" variable=\"issue\" />      </if>      <else-if type=\"book\">        <group delimiter=\". \" prefix=\". \">          <group>            <text form=\"short\" suffix=\". \" term=\"volume\" text-case=\"capitalize-first\" />            <number form=\"numeric\" variable=\"volume\" />          </group>          <group>            <number form=\"numeric\" variable=\"number-of-volumes\" />            <text form=\"short\" plural=\"true\" prefix=\" \" suffix=\".\" term=\"volume\" />          </group>        </group>      </else-if>    </choose>  </macro>  <macro name=\"locators-chapter\">    <choose>      <if type=\"chapter\">        <group prefix=\", \">          <text suffix=\":\" variable=\"volume\" />          <text variable=\"page\" />        </group>      </if>    </choose>  </macro>  <macro name=\"locators-article\">    <choose>      <if type=\"article-newspaper\">        <group delimiter=\", \" prefix=\", \">          <group>            <text suffix=\" \" variable=\"edition\" />            <text prefix=\" \" term=\"edition\" />          </group>          <group>            <text form=\"short\" suffix=\". \" term=\"section\" />            <text variable=\"section\" />          </group>        </group>      </if>      <else-if type=\"article-journal\">        <text prefix=\": \" variable=\"page\" />      </else-if>    </choose>  </macro>  <macro name=\"point-locators\">    <group>      <choose>        <if locator=\"page\" match=\"none\">          <label form=\"short\" strip-periods=\"false\" suffix=\" \" variable=\"locator\" />        </if>      </choose>      <text variable=\"locator\" />    </group>  </macro>  <macro name=\"container-prefix\">    <text term=\"in\" text-case=\"capitalize-first\" />  </macro>  <macro name=\"container-title\">    <choose>      <if type=\"chapter\">        <text macro=\"container-prefix\" suffix=\" \" />      </if>    </choose>    <text font-style=\"italic\" variable=\"container-title\" form=\"short\"/>  </macro>  <macro name=\"publisher\">    <group delimiter=\": \">      <text variable=\"publisher-place\" />      <text variable=\"publisher\" />    </group>  </macro>  <macro name=\"date\">    <date variable=\"issued\" form=\"text\" date-parts=\"year\"><date-part name=\"year\"/></date>  </macro>  <macro name=\"day-month\">    <date variable=\"issued\">      <date-part name=\"month\" />      <date-part name=\"day\" prefix=\" \" />    </date>  </macro>  <macro name=\"collection-title\">    <text variable=\"collection-title\" />    <text prefix=\" \" variable=\"collection-number\" />  </macro>  <macro name=\"event\">    <group>      <text suffix=\" \" term=\"presented at\" />      <text variable=\"event\" />    </group>  </macro>  <macro name=\"description\">    <group delimiter=\". \">      <text macro=\"interviewer\" />      <text text-case=\"capitalize-first\" variable=\"medium\" />    </group>    <choose>      <if match=\"none\" variable=\"title\"> </if>      <else-if type=\"thesis\"> </else-if>      <else>        <text prefix=\". \" text-case=\"capitalize-first\" variable=\"genre\" />      </else>    </choose>  </macro>  <macro name=\"issue\">    <choose>      <if type=\"article-journal\">        <text macro=\"day-month\" prefix=\" (\" suffix=\")\" />      </if>      <else-if type=\"speech\">        <group delimiter=\", \" prefix=\" \">          <text macro=\"event\" />          <text macro=\"day-month\" />          <text variable=\"event-place\" />        </group>      </else-if>      <else-if match=\"any\" type=\"article-newspaper article-magazine\">        <text macro=\"day-month\" prefix=\", \" />      </else-if>      <else>        <group delimiter=\", \" prefix=\". \">          <choose>            <if type=\"thesis\">              <text text-case=\"capitalize-first\" variable=\"genre\" />            </if>          </choose>          <text macro=\"publisher\" />          <text macro=\"day-month\" />        </group>      </else>    </choose>  </macro>  <citation          disambiguate-add-givenname=\"true\"         disambiguate-add-names=\"true\"         disambiguate-add-year-suffix=\"true\"         et-al-min=\"4\"         et-al-subsequent-min=\"4\"         et-al-subsequent-use-first=\"1\"         et-al-use-first=\"1\">    <layout text-decoration=\"underline\" delimiter=\"; \" prefix=\"(\" suffix=\")\">      <group delimiter=\", \">        <group delimiter=\" \">          <text macro=\"contributors-short\" />          <text macro=\"date\" />        </group>        <text macro=\"point-locators\" />      </group>    </layout>  </citation>  <bibliography          entry-spacing=\"0\"         et-al-min=\"11\"         et-al-use-first=\"7\"         hanging-indent=\"true\"         subsequent-author-substitute=\"---\">    <sort>      <key macro=\"contributors\" />      <key macro=\"date\" />    </sort>    <layout suffix=\".\">      <text macro=\"contributors\" suffix=\". \" />      <text macro=\"date\" suffix=\". \" />      <text macro=\"title\" />      <text macro=\"description\"/>      <text macro=\"secondary-contributors\" prefix=\". \" />      <text macro=\"container-title\" prefix=\". \"/>      <text macro=\"container-contributors\" />      <text macro=\"locators-chapter\" />      <text macro=\"edition\" prefix=\". \" />      <text macro=\"locators\" />      <text macro=\"collection-title\" prefix=\". \" />      <text macro=\"issue\" />      <text macro=\"locators-article\" />      <text macro=\"access\" prefix=\". \" />    </layout>  </bibliography></style>";
			
			cite_styles['bibtex'] = "<style xmlns=\"http://purl.org/net/xbiblio/csl\" class=\"in-text\" version=\"1.0\" demote-non-dropping-particle=\"sort-only\">   <info>      <title>BibTex generic citation style</title>      <id>http://www.zotero.org/styles/bibtex</id>      <link href=\"http://www.zotero.org/styles/bibtex\" rel=\"self\"/>      <link href=\"http://www.bibtex.org/\" rel=\"documentation\"/>      <author>         <name>Markus Schaffner</name>      </author>      <contributor>         <name>Richard Karnesky</name>         <email>karnesky+zotero@gmail.com</email>         <uri>http://arc.nucapt.northwestern.edu/Richard_Karnesky</uri>      </contributor>      <category field=\"generic-base\"/>      <updated>2008-10-05T10:04:29-07:00</updated>      <rights>This work is licensed under a Creative Commons Attribution-Share Alike 3.0 License: http://creativecommons.org/licenses/by-sa/3.0/</rights>   </info>   <macro name=\"zotero2bibtexType\">      <choose>         <if type=\"bill book graphic legal_case motion_picture report song\" match=\"any\">            <text value=\"book\"/>         </if>         <else-if type=\"chapter paper-conference\" match=\"any\">            <text value=\"inbook\"/>         </else-if>         <else-if type=\"article article-journal article-magazine article-newspaper\" match=\"any\">            <text value=\"article\"/>         </else-if>         <else-if type=\"thesis\" match=\"any\">            <text value=\"phdthesis\"/>         </else-if>         <else-if type=\"manuscript\" match=\"any\">            <text value=\"unpublished\"/>         </else-if>         <else-if type=\"paper-conference\" match=\"any\">            <text value=\"inproceedings\"/>         </else-if>         <else-if type=\"report\" match=\"any\">            <text value=\"techreport\"/>         </else-if>         <else>            <text value=\"misc\"/>         </else>      </choose>   </macro>   <macro name=\"citeKey\">      <group delimiter=\"_\">         <text macro=\"author-short\" text-case=\"lowercase\"/>         <text macro=\"issued-year\"/>      </group>   </macro>   <macro name=\"author-short\">      <names variable=\"author\">         <name form=\"short\" delimiter=\"_\" delimiter-precedes-last=\"always\"/>         <substitute>            <names variable=\"editor\"/>            <names variable=\"translator\"/>            <choose>               <if type=\"bill book graphic legal_case motion_picture report song\" match=\"any\">                  <text variable=\"title\" form=\"short\"/>               </if>               <else>                  <text variable=\"title\" form=\"short\"/>               </else>            </choose>         </substitute>      </names>   </macro>   <macro name=\"issued-year\">      <date variable=\"issued\">         <date-part name=\"year\"/>      </date>   </macro>   <macro name=\"issued-month\">      <date variable=\"issued\">         <date-part name=\"month\" form=\"short\" strip-periods=\"true\"/>      </date>   </macro>   <macro name=\"author\">      <names variable=\"author\">         <name sort-separator=\", \" delimiter=\" and \" delimiter-precedes-last=\"always\" name-as-sort-order=\"all\"/>         <label form=\"long\" text-case=\"capitalize-first\"/>      </names>   </macro>   <macro name=\"editor-translator\">      <names variable=\"editor translator\" delimiter=\", \">         <name sort-separator=\", \" delimiter=\" and \" delimiter-precedes-last=\"always\" name-as-sort-order=\"all\"/>         <label form=\"long\" text-case=\"capitalize-first\"/>      </names>   </macro>   <macro name=\"title\">      <text variable=\"title\"/>   </macro>   <macro name=\"number\">      <text variable=\"issue\"/>      <text variable=\"number\"/>   </macro>   <macro name=\"container-title\">      <choose>         <if type=\"chapter paper-conference\" match=\"any\">            <text variable=\"container-title\" prefix=\" booktitle={\" suffix=\"}\"/>         </if>         <else>            <text variable=\"container-title\" prefix=\" journal={\" suffix=\"}\"/>         </else>      </choose>   </macro>   <macro name=\"publisher\">      <choose>         <if type=\"thesis\">            <text variable=\"publisher\" prefix=\" school={\" suffix=\"}\"/>         </if>         <else-if type=\"report\">            <text variable=\"publisher\" prefix=\" institution={\" suffix=\"}\"/>         </else-if>         <else>            <text variable=\"publisher\" prefix=\" publisher={\" suffix=\"}\"/>         </else>      </choose>   </macro>   <macro name=\"pages\">      <text variable=\"page\"/>   </macro>   <macro name=\"edition\">      <text variable=\"edition\"/>   </macro>   <citation et-al-min=\"10\" et-al-use-first=\"10\" et-al-subsequent-min=\"10\" et-al-subsequent-use-first=\"10\" disambiguate-add-year-suffix=\"true\" disambiguate-add-names=\"false\" disambiguate-add-givenname=\"false\" collapse=\"year\">      <sort>         <key macro=\"author\"/>         <key variable=\"issued\"/>      </sort>      <layout delimiter=\"_\">         <text macro=\"citeKey\"/>      </layout>   </citation>   <bibliography hanging-indent=\"false\" et-al-min=\"10\" et-al-use-first=\"10\">      <sort>         <key macro=\"author\"/>         <key variable=\"issued\"/>      </sort>      <layout>         <text macro=\"zotero2bibtexType\" prefix=\" @\" suffix=\"\"/>         <group prefix=\"{\" suffix=\"}\" delimiter=\", \">            <text macro=\"citeKey\"/>            <text variable=\"publisher-place\" prefix=\" place={\" suffix=\"}\"/><!--Fix This-->            <text variable=\"chapter-number\" prefix=\" chapter={\" suffix=\"}\"/><!--Fix This-->            <text macro=\"edition\" prefix=\" edition={\" suffix=\"}\"/><!--Is this in CSL? <text variable=\"type\" prefix=\" type={\" suffix=\"}\" />-->            <text variable=\"collection-title\" prefix=\" series={\" suffix=\"}\"/>            <text macro=\"title\" prefix=\" title={\" suffix=\"}\"/>            <text variable=\"volume\" prefix=\" volume={\" suffix=\"}\"/><!--Not in CSL<text variable=\"rights\" prefix=\" rights={\" suffix=\"}\" />-->            <text variable=\"ISBN\" prefix=\" ISBN={\" suffix=\"}\"/><!--Not in CSL <text variable=\"ISSN\" prefix=\" ISSN={\" suffix=\"}\" />--><!--Not in CSL <text variable=\"LCCN\" prefix=\" callNumber={\" suffix=\"}\" />-->            <text variable=\"archive_location\" prefix=\" archiveLocation={\" suffix=\"}\"/>            <text variable=\"URL\" prefix=\" url={\" suffix=\"}\"/>            <text variable=\"DOI\" prefix=\" DOI={\" suffix=\"}\"/>            <text variable=\"abstract\" prefix=\" abstractNote={\" suffix=\"}\"/>            <text variable=\"note\" prefix=\" note={\" suffix=\"}\"/>            <text macro=\"number\" prefix=\" number={\" suffix=\"}\"/>            <text macro=\"container-title\"/>            <text macro=\"publisher\"/>            <text macro=\"author\" prefix=\" author={\" suffix=\"}\"/>            <text macro=\"editor-translator\" prefix=\" editor={\" suffix=\"}\"/>            <text macro=\"issued-year\" prefix=\" year={\" suffix=\"}\"/>            <text macro=\"issued-month\" prefix=\" month={\" suffix=\"}\"/>            <text macro=\"pages\" prefix=\" pages={\" suffix=\"}\"/>            <text variable=\"collection-title\" prefix=\" collection={\" suffix=\"}\"/>         </group>      </layout>   </bibliography></style>";
			
			cite_styles['zookeys'] = "<style class=\"in-text\" version=\"1.0\" demote-non-dropping-particle=\"never\">	<info>		<title>			ZooKeys		</title>		<id>			zookeys		</id>		<link href=\"http://www.zotero.org/styles/apa\" rel=\"self\">		</link>		<link href=\"http://www.pensoft.net/journals/zookeys/about/Author%20Guidelines\" rel=\"documentation\">		</link>		<author>			<name>				Roderic D. M. Page			</name>			<email>				rdmpage@gmail.com			</email>			<uri>				http://iphylo.blogspot.com			</uri>		</author>		<category field=\"biology\">		</category>		<category field=\"generic-base\">		</category>		<category citation-format=\"author-date\">		</category>		<!--		<updated>			2010-01-27T20:08:03+00:00		</updated>		-->		<rights>			This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 License: http://creativecommons.org/licenses/by-sa/3.0/		</rights>		<issn>1313-2989</issn>	</info>	<locale lang=\"en\">		<terms>			<term name=\"translator\" form=\"short\">				<single>					trans.				</single>				<multiple>					trans.				</multiple>			</term>		</terms>	</locale>	<macro name=\"container-contributors\">		<choose>			<if type=\"chapter paper-conference\" match=\"any\">				<text term=\"in\" text-case=\"capitalize-first\" suffix=\": \">				</text>				<names variable=\"editor\" delimiter=\", \" suffix=\" \">					<name delimiter=\", \" and=\"\" initialize-with=\"\" sort-separator=\" \" name-as-sort-order=\"all\" >					</name>					<!-- strip-periods=\"true\" removes the '.' after Ed/Eds -->					<label form=\"short\" strip-periods=\"true\" text-case=\"capitalize-first\" prefix=\" (\" suffix=\")\">					</label>					<substitute>						<names variable=\"translator\">						</names>					</substitute>				</names>			</if>		</choose>	</macro>	<macro name=\"secondary-contributors\">		<choose>			<if type=\"chapter paper-conference\" match=\"none\">				<names variable=\"translator\" delimiter=\", \" prefix=\" (\" suffix=\")\">					<name and=\"symbol\" initialize-with=\". \" delimiter=\", \">					</name>					<label form=\"short\" prefix=\", \" text-case=\"capitalize-first\">					</label>					<substitute>						<names variable=\"editor\">						</names>					</substitute>				</names>			</if>		</choose>	</macro>	<macro name=\"author\">		<names variable=\"author\">			<name delimiter=\", \" delimiter-precedes-last=\"always\" initialize-with=\"\" sort-separator=\" \" name-as-sort-order=\"all\" >			</name>			<label form=\"short\" strip-periods=\"true\" text-case=\"capitalize-first\" prefix=\" (\" suffix=\".)\">			</label>			<substitute>				<names variable=\"editor\">				</names>				<names variable=\"translator\">				</names>				<choose>					<if type=\"report\">						<text variable=\"publisher\">						</text>						<text macro=\"title\">						</text>					</if>					<else>						<text macro=\"title\">						</text>					</else>				</choose>			</substitute>		</names>	</macro>	<macro name=\"author-short\">		<names variable=\"author\">			<name form=\"short\" and=\"text\" delimiter=\", \" initialize-with=\". \">			</name>			<substitute>				<names variable=\"editor\">				</names>				<names variable=\"translator\">				</names>				<choose>					<if type=\"report\">						<text variable=\"publisher\">						</text>						<text variable=\"title\" form=\"short\" font-style=\"italic\">						</text>					</if>					<else-if type=\"bill book graphic legal_case motion_picture song\" match=\"any\">						<text variable=\"title\" form=\"short\" font-style=\"italic\">						</text>					</else-if>					<else>						<text variable=\"title\" form=\"short\" quotes=\"false\">						</text>					</else>				</choose>			</substitute>		</names>	</macro>	<macro name=\"access\">		<choose>			<if type=\"thesis\">				<choose>					<if variable=\"archive\" match=\"any\">						<group>							<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">							</text>							<text term=\"from\" suffix=\" \">							</text>							<text variable=\"archive\" suffix=\".\">							</text>							<text variable=\"archive_location\" prefix=\" (\" suffix=\")\">							</text>						</group>					</if>					<else>						<group>							<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">							</text>							<text term=\"from\" suffix=\" \">							</text>							<text variable=\"URL\">							</text>						</group>					</else>				</choose>			</if>			<else>				<choose>					<if variable=\"DOI\">						<text variable=\"DOI\" prefix=\"doi: \" suffix=\".\">						</text>					</if>					<else>						<choose>							<if type=\"webpage\">								<group delimiter=\" \">									<!--									<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">									</text>									<group>										<date variable=\"accessed\" suffix=\", \">											<date-part name=\"month\" suffix=\" \">											</date-part>											<date-part name=\"day\" suffix=\", \">											</date-part>											<date-part name=\"year\">											</date-part>										</date>									</group>									<text term=\"from\">									</text> -->									<text variable=\"URL\">									</text>								</group>							</if>							<else>								<group>									<text term=\"retrieved\" text-case=\"capitalize-first\" suffix=\" \">									</text>									<text term=\"from\" suffix=\" \">									</text>									<text variable=\"URL\">									</text>								</group>							</else>						</choose>					</else>				</choose>			</else>		</choose>	</macro>	<macro name=\"title\">		<choose>			<if type=\"report thesis\" match=\"any\">				<text variable=\"title\">				</text>				<group prefix=\" (\" suffix=\")\">					<text variable=\"genre\">					</text>					<text variable=\"number\" prefix=\" No. \">					</text>				</group>			</if>			<else-if type=\"book graphic  motion_picture report song manuscript speech\" match=\"any\">				<text variable=\"title\" >				</text>			</else-if>			<else>				<text variable=\"title\">				</text>			</else>		</choose>	</macro>	<macro name=\"publisher\">		<choose>			<if type=\"report\" match=\"any\">				<group delimiter=\", \">					<text variable=\"publisher\">					</text>					<text variable=\"publisher-place\">					</text>				</group>			</if>			<else-if type=\"thesis\" match=\"any\">				<group delimiter=\", \">					<text variable=\"publisher\">					</text>					<text variable=\"publisher-place\">					</text>				</group>			</else-if>			<else>				<group delimiter=\", \">					<choose>						<if variable=\"event\" match=\"none\">							<text variable=\"genre\">							</text>						</if>					</choose>					<group delimiter=\", \">						<text variable=\"publisher\">						</text>						<text variable=\"publisher-place\">						</text>					</group>				</group>			</else>		</choose>	</macro>	<macro name=\"event\">		<choose>			<if variable=\"event\">				<choose>					<if variable=\"genre\" match=\"none\">						<text term=\"presented at\" text-case=\"capitalize-first\" suffix=\" \">						</text>						<text variable=\"event\">						</text>					</if>					<else>						<group delimiter=\" \">							<text variable=\"genre\" text-case=\"capitalize-first\">							</text>							<text term=\"presented at\">							</text>							<text variable=\"event\">							</text>						</group>					</else>				</choose>			</if>		</choose>	</macro>	<macro name=\"issued\">		<choose>			<if type=\"legal_case bill\" match=\"none\">				<choose>					<if variable=\"issued\">						<group prefix=\" (\" suffix=\")\">							<date variable=\"issued\">								<date-part name=\"year\">								</date-part>							</date>							<text variable=\"year-suffix\">							</text>							<choose>								<if type=\"bill book graphic legal_case motion_picture report song article-journal chapter paper-conference\" match=\"none\">									<date variable=\"issued\">										<date-part prefix=\", \" name=\"month\">										</date-part>										<date-part prefix=\" \" name=\"day\">										</date-part>									</date>								</if>							</choose>						</group>					</if>					<else>						<if type=\"webpage\">						</if>						<else>												<group prefix=\" (\" suffix=\").\">								<text term=\"no date\" form=\"short\">								</text>								<text variable=\"year-suffix\" prefix=\"-\">								</text>							</group>						</else>					</else>				</choose>			</if>		</choose>	</macro>	<macro name=\"issued-sort\">		<choose>			<if type=\"bill book graphic legal_case motion_picture report song article-journal chapter paper-conference\" match=\"none\">				<date variable=\"issued\">					<date-part name=\"year\">					</date-part>					<date-part prefix=\", \" name=\"month\">					</date-part>					<date-part prefix=\" \" name=\"day\">					</date-part>				</date>			</if>			<else>				<date variable=\"issued\">					<date-part name=\"year\">					</date-part>				</date>			</else>		</choose>	</macro>	<macro name=\"issued-year\">		<choose>			<if variable=\"issued\">				<date variable=\"issued\">					<date-part name=\"year\">					</date-part>				</date>				<text variable=\"year-suffix\">				</text>			</if>			<else>						<if type=\"webpage\">						</if>						<else>																<text term=\"no date\" form=\"short\">				</text>				<text variable=\"year-suffix\" prefix=\"-\">				</text>									</else>			</else>		</choose>	</macro>	<macro name=\"edition\">		<choose>			<if is-numeric=\"edition\">				<group delimiter=\" \">					<number variable=\"edition\" form=\"ordinal\">					</number>					<text term=\"edition\" form=\"short\" suffix=\".\" strip-periods=\"true\">					</text>				</group>			</if>			<else>				<text variable=\"edition\" suffix=\".\">				</text>			</else>		</choose>	</macro>	<macro name=\"locators\">		<choose>			<if type=\"article-journal article-magazine\" match=\"any\">				<group delimiter=\" \" prefix=\" \">					<group suffix=\":\">						<text variable=\"volume\" font-style=\"normal\">						</text>						<text variable=\"issue\" prefix=\"(\" suffix=\")\">						</text>					</group>					<text variable=\"page\">					</text>				</group>			</if>			<else-if type=\"article-newspaper\">				<group delimiter=\" \" prefix=\", \">					<label variable=\"page\" form=\"short\">					</label>					<text variable=\"page\">					</text>				</group>			</else-if>			<else-if type=\"book graphic motion_picture report song chapter paper-conference\" match=\"any\">				<group prefix=\"\" suffix=\" pp\" delimiter=\".\">					<text macro=\"edition\">					</text>					<group>						<text term=\"volume\" form=\"short\" plural=\"true\" text-case=\"capitalize-first\" suffix=\". \" strip-periods=\"true\">						</text>						<number variable=\"number-of-volumes\" form=\"numeric\" prefix=\"1-\">						</number>					</group>					<group>						<text term=\"volume\" form=\"short\" text-case=\"capitalize-first\" suffix=\". \" strip-periods=\"true\">						</text>						<number variable=\"volume\" form=\"numeric\">						</number>					</group>					<group>						<!--<label variable=\"page\" form=\"short\" suffix=\" \">						</label>-->						<text variable=\"page\">						</text>					</group>				</group>			</else-if>			<else-if type=\"legal_case\">				<group prefix=\" (\" suffix=\")\" delimiter=\" \">					<text variable=\"authority\">					</text>					<date variable=\"issued\" delimiter=\" \">						<date-part name=\"month\" form=\"short\">						</date-part>						<date-part name=\"day\" suffix=\",\">						</date-part>						<date-part name=\"year\">						</date-part>					</date>				</group>			</else-if>			<else-if type=\"bill\">				<date variable=\"issued\" prefix=\" (\" suffix=\")\">					<date-part name=\"year\">					</date-part>				</date>			</else-if>		</choose>	</macro>	<macro name=\"citation-locator\">		<group>			<label variable=\"locator\" form=\"short\">			</label>			<text variable=\"locator\" prefix=\" \">			</text>		</group>	</macro>	<macro name=\"container\">		<choose>			<if type=\"legal_case bill\" match=\"none\">				<text variable=\"container-title\" font-style=\"normal\">				</text>			</if>			<else>				<group delimiter=\" \" prefix=\", \">					<choose>						<if variable=\"container-title\">							<text variable=\"volume\">							</text>							<text variable=\"container-title\">							</text>							<group delimiter=\" \">								<text term=\"section\" form=\"symbol\">								</text>								<text variable=\"section\">								</text>							</group>							<text variable=\"page\">							</text>						</if>						<else>							<choose>								<if type=\"legal_case\">									<text variable=\"number\" prefix=\"No. \">									</text>								</if>								<else>									<text variable=\"number\" prefix=\"Pub. L. No. \">									</text>									<group delimiter=\" \">										<text term=\"section\" form=\"symbol\">										</text>										<text variable=\"section\">										</text>									</group>								</else>							</choose>						</else>					</choose>				</group>			</else>		</choose>	</macro>	<citation et-al-min=\"6\" et-al-use-first=\"1\" et-al-subsequent-min=\"3\" et-al-subsequent-use-first=\"1\" disambiguate-add-year-suffix=\"true\" disambiguate-add-names=\"true\" disambiguate-add-givenname=\"true\" collapse=\"year\" givenname-disambiguation-rule=\"primary-name\">		<sort>			<key macro=\"author\">			</key>			<key macro=\"issued-sort\">			</key>		</sort>		<layout prefix=\"(\" suffix=\")\" delimiter=\"; \">			<group delimiter=\" \">				<text macro=\"author-short\">				</text>				<text macro=\"issued-year\">				</text>				<text macro=\"citation-locator\">				</text>			</group>		</layout>	</citation>	<bibliography hanging-indent=\"true\" et-al-min=\"8\" et-al-use-first=\"7\" entry-spacing=\"0\" line-spacing=\"2\">		<sort>			<key macro=\"author\">			</key>			<key macro=\"issued-sort\" sort=\"ascending\">			</key>		</sort>		<layout>			<group suffix=\".\">				<group delimiter=\" \">					<text macro=\"author\">					</text>					<text macro=\"issued\">					</text>				</group>				<group delimiter=\". \">					<text macro=\"title\" prefix=\" \">					</text>					<group>						<text macro=\"container-contributors\">						</text>						<text macro=\"secondary-contributors\">						</text>						<group delimiter=\", \">							<text macro=\"container\">							</text>							<text variable=\"collection-title\">							</text>						</group>					</group>				</group>								<!-- publisher, place -->				<group delimiter=\", \" prefix=\". \" suffix=\", \">					<text macro=\"event\">					</text>					<text macro=\"publisher\">					</text>				</group>								<!-- volume, issue, pagination, DOI -->				<text macro=\"locators\">				</text>							</group>			<text macro=\"access\" prefix=\" \">			</text>		</layout>	</bibliography></style>";
			
			cite_styles['zootaxa'] = "<style xmlns=\"http://purl.org/net/xbiblio/csl\" class=\"in-text\" version=\"1.0\" demote-non-dropping-particle=\"sort-only\" default-locale=\"en-US\">  <info>    <title>Zootaxa</title>    <id>http://www.zotero.org/styles/zootaxa</id>    <link href=\"http://www.zotero.org/styles/zootaxa\" rel=\"self\"/>    <link href=\"http://www.mapress.com/zootaxa/support/author.html#Preparation%20of%20manuscripts\" rel=\"documentation\"/>    <link href=\"http://www.zotero.org/styles/apsa\" rel=\"template\"/>    <author>      <name>Bastian Drolshagen</name>      <email>bdrolshagen@gmail.com</email>    </author>    <category citation-format=\"author-date\"/>    <category field=\"biology\"/>    <issn>1175-5326</issn>    <eissn>1175-5334</eissn>    <updated>2012-11-18T18:42:51+00:00</updated>    <summary>The Zootaxa style.</summary>    <rights license=\"http://creativecommons.org/licenses/by-sa/3.0/\">This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 License</rights>  </info>  <locale xml:lang=\"en-US\">    <date form=\"text\">      <date-part name=\"month\" suffix=\" \"/>      <date-part name=\"day\" suffix=\", \"/>      <date-part name=\"year\"/>    </date>  </locale>  <macro name=\"editor\">    <names variable=\"editor\" delimiter=\", \">      <name and=\"text\" initialize-with=\". \" delimiter=\", \"/>      <label form=\"short\" prefix=\" (\" text-case=\"capitalize-first\" suffix=\")\" strip-periods=\"true\"/>    </names>  </macro>  <macro name=\"anon\">    <text term=\"anonymous\" form=\"short\" text-case=\"capitalize-first\" strip-periods=\"true\"/>  </macro>  <macro name=\"author\">    <names variable=\"author\">		<name name-as-sort-order=\"all\" and=\"text\" sort-separator=\", \" initialize-with=\".\" delimiter-precedes-last=\"never\" delimiter=\", \"/>	  <et-al font-style=\"italic\" prefix=\" \"/>      <label form=\"short\" prefix=\" \" suffix=\".\" text-case=\"lowercase\" strip-periods=\"true\"/>      <substitute>        <names variable=\"editor\"/>        <text macro=\"anon\"/>      </substitute>    </names>  </macro>  <macro name=\"author-short\">    <names variable=\"author\">		<name form=\"short\" and=\"symbol\" delimiter=\", \" delimiter-precedes-last=\"never\" initialize-with=\". \"/>		<et-al font-style=\"italic\" prefix=\" \"/>      <substitute>        <names variable=\"editor\"/>        <names variable=\"translator\"/>        <text macro=\"anon\"/>      </substitute>    </names>  </macro>  <macro name=\"access\">    <choose>      <if type=\"legal_case\" match=\"none\">        <choose>          <if variable=\"URL\">            <group delimiter=\" \">              <text variable=\"URL\" prefix=\"Available from: \"/>              <group prefix=\"(\" suffix=\")\">                <date variable=\"accessed\" form=\"text\"/>              </group>            </group>          </if>        </choose>      </if>    </choose>  </macro>  <macro name=\"title\">    <choose>      <if type=\"bill book graphic legal_case legislation motion_picture report song\" match=\"any\">        <text variable=\"title\" font-style=\"italic\"/>      </if>      <else>        <text variable=\"title\" quotes=\"false\"/>      </else>    </choose>  </macro>  <macro name=\"legal_case\">    <group prefix=\" \" delimiter=\" \">      <text variable=\"volume\"/>      <text variable=\"container-title\"/>    </group>    <text variable=\"authority\" prefix=\" (\" suffix=\")\"/>  </macro>  <macro name=\"publisher\">    <choose>      <if type=\"thesis\" match=\"none\">        <group prefix=\"\" suffix=\"\" delimiter=\", \">          <text variable=\"publisher\" suffix=\"\"/>          <text variable=\"publisher-place\"/>        </group>        <text variable=\"genre\" prefix=\". \"/>      </if>      <else>        <group delimiter=\". \">          <text variable=\"genre\"/>          <text variable=\"publisher\"/>        </group>      </else>    </choose>  </macro>  <macro name=\"year-date\">    <choose>      <if variable=\"issued\"><group prefix=\"(\">        <date variable=\"issued\">          <date-part name=\"year\"/>        </date>        </group>      </if>      <else>        <text term=\"no date\" form=\"short\"/>      </else>    </choose>  </macro>  <macro name=\"edition\">    <choose>      <if is-numeric=\"edition\">        <group delimiter=\" \">          <number variable=\"edition\" form=\"ordinal\"/>          <text term=\"edition\" form=\"short\" suffix=\".\" strip-periods=\"true\"/>        </group>      </if>      <else>        <text variable=\"edition\" suffix=\".\"/>      </else>    </choose>  </macro>  <macro name=\"locator\">    <choose>      <if locator=\"page\">        <text variable=\"locator\"/>      </if>      <else>        <group delimiter=\" \">          <label variable=\"locator\" form=\"short\"/>          <text variable=\"locator\"/>        </group>      </else>    </choose>  </macro>  <citation et-al-min=\"2\" et-al-use-first=\"2\" et-al-subsequent-min=\"2\" et-al-subsequent-use-first=\"1\" disambiguate-add-year-suffix=\"true\" disambiguate-add-names=\"true\" disambiguate-add-givenname=\"true\" collapse=\"year\" givenname-disambiguation-rule=\"primary-name\">       <sort>      <key macro=\"author-short\"/>      <key macro=\"year-date\"/>    </sort>    <layout prefix=\"\" delimiter=\"; \">      <group delimiter=\", \">        <group delimiter=\" \">          <text macro=\"author-short\"/><text macro=\"year-date\" suffix=\")\"/>        </group>        <text macro=\"locator\"/>      </group>    </layout>  </citation>  <bibliography hanging-indent=\"true\">    <sort>      <key macro=\"author\"/>      <key macro=\"year-date\"/>      <key variable=\"title\"/>    </sort>    <layout suffix=\" \">      <text macro=\"author\" suffix=\" (\"/>      <date variable=\"issued\">        <date-part name=\"year\" suffix=\")\"/>      </date>      <choose>        <if type=\"book\" match=\"any\">          <text macro=\"legal_case\"/>          <group prefix=\" \" delimiter=\" \">            <text macro=\"title\" suffix=\".\"/>            <text macro=\"edition\"/>            <text macro=\"editor\" suffix=\".\"/>          </group>          <group prefix=\" \" suffix=\".\" delimiter=\", \">          <text macro=\"publisher\"/>          <text variable=\"page\" prefix=\" \" suffix=\" pp\"/>          </group></if>        <else-if type=\"chapter paper-conference\" match=\"any\">                  <text macro=\"title\" prefix=\" \" suffix=\".\"/>          <group prefix=\" In: \" delimiter=\" \"><text macro=\"editor\" suffix=\",\"/>            <text variable=\"container-title\" font-style=\"italic\" suffix=\".\"/>            <text variable=\"collection-title\" suffix=\".\"/>            <group suffix=\".\">              <text macro=\"publisher\"/>              <group prefix=\", pp. \" delimiter=\" \" suffix=\".\">                <text variable=\"page\"/>              </group>            </group>          </group>        </else-if>                        <else-if type=\"bill graphic legal_case legislation manuscript motion_picture report song thesis\" match=\"any\">          <text macro=\"legal_case\"/>          <group prefix=\" \" delimiter=\" \">            <text macro=\"title\" suffix=\".\"/>            <text macro=\"edition\"/>            <text macro=\"editor\" suffix=\".\"/>          </group>          <group prefix=\" \" suffix=\"\" delimiter=\", \">          <text macro=\"publisher\"/>          <text variable=\"page\" prefix=\" \" suffix=\"pp.\"/>          </group>        </else-if>                <else>          <group prefix=\" \" delimiter=\" \" suffix=\".\">            <text macro=\"title\"/>            <text macro=\"editor\"/>          </group>          <group prefix=\" \" suffix=\".\">            <text variable=\"container-title\" font-style=\"italic\"/>            <group prefix=\" \">              <text variable=\"volume\"/>            </group>            <text variable=\"page\" prefix=\", \" suffix=\".\"/>          </group>        </else>              </choose>      <text prefix=\" \" macro=\"access\" suffix=\".\"/>    </layout>  </bibliography></style>";
			
			</script>
			
			<!-- documentcloud -->
			<!--[if (!IE)|(gte IE 8)]><!-->
				<link href="public/assets/viewer-datauri.css" media="screen" rel="stylesheet" type="text/css" />
				<link href="public/assets/plain-datauri.css" media="screen" rel="stylesheet" type="text/css" />
			<!--<![endif]-->
			<!--[if lte IE 7]>
			<link href="public/assets/viewer.css" media="screen" rel="stylesheet" type="text/css" />
			<link href="public/assets/plain.css" media="screen" rel="stylesheet" type="text/css" />
			<![endif]-->

			<script src="public/assets/viewer.js" type="text/javascript" charset="utf-8"></script>
			<script src="public/assets/templates.js" type="text/javascript" charset="utf-8"></script>	
			
		</head>
		<body onload="$(window).resize();">

		   <div class="navbar navbar-fixed-top">
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
					  <li class="active"><a href=".">Home</a></li>
					  <li><a href="?page=about">About</a></li>
					</ul>
				  </div><!--/.nav-collapse -->
				  
					<form class="navbar-search pull-left" method="get" action=".">
						<input type="text" id='q' name='q' class="search-query" placeholder="Search">
					</form>          
				  
				  
				</div>
			  </div>
			</div>
		
		
			<div style="margin-top:40px;padding:0px;" class="container-fluid">			
				<div class="row-fluid">
					<div class="span8">
						<div id="doc">Loading...</div>
					</div>
					<div class="span4" id="metadata" style="padding-right:40px;height:400px;overflow:auto;">
						<h4><TITLE></h4>
						
						<ul class="unstyled">
						<IDENTIFIER>
						</ul>
						
						<COINS>
						
						<h5><i class="icon-bullhorn"></i>Cite</h5>
						
						
						<div class="tabbable"> <!-- Only required for left/right tabs -->
						  <ul class="nav nav-tabs" id="myTab">
							<li class="active"><a href="#tab1" data-toggle="tab">Zootaxa</a></li>
							<li><a href="#tab2" data-toggle="tab">Zookeys</a></li>
							<li><a href="#tab3" data-toggle="tab">BibTeX</a></li>
						  </ul>						
						
							
							<div class="tab-content">
								<div class="tab-pane active" id="tab1">
									<pre id="zootaxa">
									</pre>	
								</div>
								<div class="tab-pane" id="tab2">
									<pre id="zookeys">
									</pre>	
								</div>
								<div class="tab-pane" id="tab3">
									<pre id="bibtex">
									</pre>	
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>			


	   <!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="assets/js/jquery.js"></script>
		<script src="assets/js/bootstrap.js"></script>
			
		<script type="text/javascript">
			
			var windowWidth = $(window).width();
			var windowHeight =$(window).height() - 40;
		
			/* Viewer */
			<DOCVIEWER>

			// http://stackoverflow.com/questions/6762564/setting-div-width-according-to-the-screen-size-of-user
			$(window).resize(function() { 
				var windowHeight =$(window).height() - 40;				
				$('#doc').css({'width':'100%' ,'height':windowHeight });
				$('#metadata').css({'height':windowHeight });
			});
			
		function show_formatted_citation(format)
		{
		  citeproc = new CSL.Engine( sys, cite_styles[format] );
  		citeproc.appendCitationCluster( citation_object )[ 0 ][ 1 ];
 			 $('#' + format).html(citeproc.makeBibliography()[ 1 ].join(""));
 		}

			
 		$(function () {
			$('#myTab a:first').tab('show');
			show_formatted_citation('zootaxa');
		  })	
		  
		  $('a[data-toggle="tab"]').on('shown', function (e) {
  //e.target // activated tab
  //e.relatedTarget // previous tab
  
  var format = $(e.target).text().toLowerCase();
  //alert(format);
  show_formatted_citation(format);
  
  
})
		</script>
		
		</body>
	</html>
EOT;

	
	$template = str_replace('<BASE>', $config['web_server'] . $config['web_root'], $template);

	$viewer = '';
	
	$identifiers = reference_identifiers($reference);
	
	// BioStor?
	if ($viewer == '')
	{
		if (isset($identifiers['biostor']))
		{
			$viewer = "var docUrl = 'http://biostor.org/dv/" . $identifiers['biostor'] . ".json';
				DV.load(docUrl, {
					container: '#doc',
					width:700,
					height:windowHeight,
					sidebar: false
				});";
		}
	}
	
	// PDF?
	if ($viewer == '')
	{
		// Do we have a PDF file?
		if (isset($reference->file))
		{
			// If we have a PDF sha1 and a thumbnail then this is a cached, viewable PDF
			if (isset($reference->file->sha1) && isset($reference->thumbnail))
			{
				$viewer = "var docUrl = 'http://bionames.org/archive/documentcloud/" . $reference->file->sha1 . ".json';
					DV.load(docUrl, {
						container: '#doc',
						width:700,
						height:windowHeight,
						sidebar: false
					});";
			}
		}
	}
	
	// Gallica
	if ($viewer == '')
	{
		if (isset($identifiers['ark']))
		{
			if (preg_match('/(?<namespace>\d+)\/(?<id>.*)\/f(?<page>\d+)$/', $identifiers['ark'], $m))
			{
				$namespace 	= $m['namespace'];
				$arkid 		= $m['id'];
				$start_page = $m['page'];

				$viewer = "var docUrl = 'http://bionames.org/gallica/documentcloud/" . $arkid . "f" . $start_page . ".json';
					DV.load(docUrl, {
						container: '#doc',
						width:700,
						height:windowHeight,
						sidebar: false
					});";
			}
		}
	}
	
	
	$template = str_replace('<DOCVIEWER>', $viewer, $template);


	$template = str_replace('<BIBDATA>', $bibdata_json, $template);
	$template = str_replace('<TITLE>', $reference->title, $template);
	$template = str_replace('<COINS>', reference_to_coins($reference), $template);
	
	$html = '';
	foreach ($identifiers as $k => $v)
	{
		switch ($k)
		{
			case 'ark':
				if (preg_match('/^12148/', $v))
				{
					$html .= '<li><a href="http://gallica.bnf.fr/ark:/' . $v . '" target="_new"><i class="icon-external-link"></i>ark:/' . $v . '</a></li>';
				}
				break;
		
			case 'bhl':
				$html .= '<li><a href="http://biodiversitylibrary.org/page/' . $v . '" target="_new"><i class="icon-external-link"></i>biodiversitylibrary.org/page/' . $v . '</a></li>';
				break;
				
			case 'biostor':
				$html .= '<li><a href="http://biostor.org/reference/' . $v . '" target="_new"><i class="icon-external-link"></i>biostor.org/reference/' . $v . '</a></li>';
				break;				

			case 'doi':
				$html .= '<li><a href="http://dx.doi.org/' . $v . '" target="_new"><i class="icon-external-link"></i>dx.doi.org/' . $v . '</a></li>';
				break;
				
			default:
				break;
		}		
	}
	$template = str_replace('<IDENTIFIER>', $html, $template);	

    echo $template;



}

//--------------------------------------------------------------------------------------------------
function display_search($text, $bookmark = '')
{
	global $couch;
	global $config;
	
	$rows_per_page = 10;
	
	$q = $text;
	$q = str_replace(':', ',' , $q);
	
	if ($q == '')
	{
		$obj = new stdclass;
		$obj->rows = array();
		$obj->total_rows = 0;
		$obj->bookmark = '';		
	}
	else
	{		
		
		$url = '/_design/citation/_search/all?q=' . urlencode($q) . '&limit=' . $rows_per_page . '&include_docs=true';
		
		if ($bookmark != '')
		{
			$url .= '&bookmark=' . $bookmark;
		}
		
		$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
		$obj = json_decode($resp);
		
		//print_r($config);
		//echo $config['couchdb_options']['database'] . $url;
		//exit();
	}
	
echo '<!DOCTYPE html>
	<html>
        <head>
            <meta charset="utf-8"/>

			<!-- Le styles -->
			<link href="assets/css/bootstrap.css" rel="stylesheet">
			<style type="text/css">
			  body {
				padding-top: 60px;
				padding-bottom: 40px;
			  }
			  .sidebar-nav {
				padding: 9px 0;
			  }
			  
			  .item-links a { margin-right:40px; }
			  
			</style>
			<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
			
			<link href="font-awesome.css" media="screen" rel="stylesheet" type="text/css" />						
		
			<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
			  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
			
</head>
<body>
		   <div class="navbar navbar-fixed-top">
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
					  <li class="active"><a href=".">Home</a></li>
					  <li><a href="?page=about">About</a></li>
					</ul>
				  </div><!--/.nav-collapse -->
				  
					<form class="navbar-search pull-left" method="get" action=".">
						<input type="text" id=\'q\' name=\'q\' class="search-query" placeholder="Search" value="' . $q . '">
					</form>          
				  
				  
				</div>
			  </div>
			</div>

<div class="container-fluid">';
	
	//print_r($obj);
	
	$total_rows = $obj->total_rows;
	$bookmark = $obj->bookmark;
	
	echo '<h3>' . $total_rows . ' hit(s)' . '</h3>';
	
	
	if ($total_rows > $rows_per_page)
	{
		echo '<p><a class="btn" href="?q=' . urlencode($q) . '&bookmark=' . $bookmark . '">More »</a></p>';
	}
	
	//echo '<p>' . "Bookmark=$bookmark " . '<a href="?q=' . urlencode($q) . '&bookmark=' . $bookmark . '">more</a>' . '</p>';
	
	
	echo '<table class="table">';
	echo '<thead>';
	echo '</thead>';
	echo '<tbody>';
	foreach ($obj->rows as $row)
	{
		$hit = $row->fields->default;
		$reference = $row->doc;
		$reference->id = $row->id;
		

		echo '<tr>';
		
		echo '<td style="text-align:center;width:100px;">';
		if (isset($reference->thumbnail))
		{
			echo '<img style="box-shadow:2px 2px 2px #ccc;width:64px;" src="' . $reference->thumbnail .  '">';								
		}
		echo '</td>';
		
		echo '<td class="item-data">';
		
		//echo $row->order[0];
		
		
		echo '<p class="lead">';
		echo '<a href="id/' . $reference->id . '">' . $reference->title . '</a>';
		echo '</p>';
		
		echo '<p>';
		
		if ($reference->year)
		{
			echo 'Published in <b>' . $reference->year . '</b>';
		}
		if (isset($reference->journal))
		{
			echo ' in <b>' . $reference->journal->name . '</b>';
			if (isset($reference->journal->volume))
			{
				echo ', volume <b>' . $reference->journal->volume . '</b>';
			}
			if (isset($reference->journal->issue))
			{
				echo ', issue <b>' . $reference->journal->issue . '</b>';
			}		
			if (isset($reference->journal->pages))
			{
				echo ', on pages <b>' . str_replace('--', '-', $reference->journal->pages) . '</b>';
			}
		}
		else
		{
			// not a journal...
			echo ', on pages <b>' . str_replace('--', '-', $reference->pages) . '</b>';
		}
		
		
		
		echo '</p>';
		
		echo '<p>';
		
		if (isset($reference->author))
		{
			$authors = array();
			foreach ($reference->author as $author)
			{
				if (isset($author->forename))
				{
					$authors[] = $author->lastname . ' ' . $author->forename;
				}
				else
				{
					$authors[] = $author->name;
				}
			
			}
			echo join(', ', $authors);
		}
		
		
		echo '</p>';
		
		// COinS
		echo reference_to_coins($reference);
		
		// Links
		echo '<div class="item-links">';
		
		//echo '<a href="">cite</a>';
				
		if (isset($reference->identifier))
		{
			foreach ($reference->identifier as $identifier)
			{
				switch ($identifier->type)
				{
					case 'biostor':
						echo '<a href="http://biostor.org/reference/' . $identifier->id . '" target="_new"><i class="icon-external-link"></i>biostor.org/reference/' . $identifier->id . '</a></li>';
						break;
	
					case 'doi':
						echo '<a href="http://dx.doi.org/' . $identifier->id . '" target="_new"><i class="icon-external-link"></i>doi.dx.org' . $identifier->id . '</a></li>';
						break;
						
					default:
						break;
				}
			}
		}
		echo '</div>';
		
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
	if ($total_rows > $rows_per_page)
	{
		echo '<p><a class="btn" href="?q=' . urlencode($q) . '&bookmark=' . $bookmark . '">More »</a></p>';
	}
	
	
	echo '</div>
</body>
</html>';	
}



//--------------------------------------------------------------------------------------------------
function main()
{	
	$query = '';
	$bookmark = '';
		
	// If no query parameters 
	if (count($_GET) == 0)
	{
		default_display();
		exit(0);
	}
	
	// If show a single record
	if (isset($_GET['id']))
	{	
		$id = $_GET['id'];
		display_record($id);
	}

	if (isset($_GET['q']))
	{	
		$query = $_GET['q'];
		
		if (isset($_GET['bookmark']))
		{
			$bookmark = $_GET['bookmark'];
		}
		display_search($query, $bookmark);
		exit(0);
	}	
	
	if (isset($_GET['page']))
	{	
		$page = $_GET['page'];
		display_page($page);
		exit(0);
	}	

	
	/*
	if (isset($_GET['author']))
	{	
		$query = $_GET['author'];
		display_author($query);
		exit(0);
	}
	*/
	
}


main();
		
?>