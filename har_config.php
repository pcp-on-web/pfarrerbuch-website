<?php
$har='www.evangelische-archive.de_Archive.har';
$target='';

// Lade Archiv File - JSON-Datei
$jsonInhalt = file_get_contents($har);
$jsonHAR = json_decode($jsonInhalt); 
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Fehler beim Dekodieren der JSON-Datei: ' . json_last_error_msg());
}
// Wähle root element der JSON
$jsonObjekt=$jsonHAR->log;

// Filterfunktion
function filter($html) {

	$html=str_replace('href="','href="har_browser.php?target=',$html);
	$html=str_replace("href='","href='har_browser.php?target=",$html);
	$html=str_replace('src="','src="har_browser.php?target=',$html);
	$html=str_replace("src='","src='har_browser.php?target=",$html);
	$html=str_replace("srcset='","nosrcset=",$html);
	
	
	$html=str_replace("url(","url!!(",$html);
	$html=str_replace('url!!("','url("har_browser.php?target=',$html);
	$html=str_replace("url!!('","url('har_browser.php?target=",$html);
	$html=str_replace('url!!(','url(har_browser.php?target=',$html);
	
	$html=str_replace("har_browser.php?target=javascript:","javascript:",$html);
	$html=str_replace("har_browser.php?target=data:","data:",$html);
	$html=str_replace('har_browser.php?target=+','+',$html);
	$html=str_replace('"har_browser.php?target="','""',$html);
	$html=str_replace("'har_browser.php?target='","''",$html);
	
	
	$html=str_replace('target=/','target=',$html);
	$html=str_replace('target=../','target=',$html);
	$html=str_replace('target=../','target=',$html);
	$html=str_replace('target=../','target=',$html);
	$html=str_replace('target=../','target=',$html);
	$html=str_replace('target=../','target=',$html);
	$html=str_replace('</html>','<script src="har_browser.js"></script></html>',$html);
	$html=str_replace('</HTML>','<script src="har_browser.js"></script></HTML>',$html);
	
	return $html;
}

