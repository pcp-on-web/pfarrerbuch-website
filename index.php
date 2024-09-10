<?php
if (isset($_GET['site'])) $site_name = $_GET['site'];
else $site_name="index.html";

include('har_config.php');

$html = filter($jsonObjekt->entries[0]->response->content->text);;


// Ein neues DOMDocument-Objekt erstellen
$dom = new DOMDocument;

// Die HTML-Datei in das DOM-Objekt laden
$dom->loadHTML($html);

// Löschen des Cookie-Managers
$elementToDelete = $dom->getElementById('cookieman-modal');
if ($elementToDelete && $elementToDelete->parentNode) {
    $elementToDelete->parentNode->removeChild($elementToDelete);
}


// Titel ändern
$elementToModify = $dom->getElementById('c779');
$elementToModify->nodeValue='';
// Fügen Sie neuen HTML-Code zum Element hinzu
$elementToModify->appendChild($dom->createDocumentFragment());
$elementToModify->appendChild($dom->createCDATASection('<h1><span class="text--primary"><strong>AG Meta-Pfarrerbuch</strong></span></h1><p>Datenbank zur Pfarrerschaft im deutschsprachigen Raum</p>'));


$elementToModify = $dom->getElementById('c1082')->parentNode->parentNode->parentNode;
$elementToModify->nodeValue='';
$elementToModify->setAttribute('class', 'container container--small margin-top-m');
$elementToModify->appendChild($dom->createDocumentFragment());
$file=file_get_contents($site_name);

// Hinzufügen eines Projekt-Menüs

$file = '<link rel="stylesheet" href="index.css">
<div id="project-menu">
	<ul>
		<li><a href="/">Übersicht</a></li>
		<li><a href="/suche/">Suche</a></li>
		<li><a href="/#Mitwirkende">Mitwirkende</a></li>
		<li><a href="/#Ergebnisse">Aktueller Stand</a></li>	
	</ul>
</div>
'.$file;


$elementToModify->appendChild($dom->createCDATASection($file));







// Das HTML-Dokument als String erhalten
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$html=$dom->saveHTML();
$html= str_replace('href="har_browser.php?target=projekte/"', 'href="https://www.evangelische-archive.de/projekte/"', $html);
$html= str_replace('href="har_browser.php?target=#cookie" onclick="cookieman.show()"', 'href="#cookie" onclick="alert('."'".'Dies iste eine Projektseite und verwendet keine Cookies.'."'".')"', $html);
echo $html;


?>

