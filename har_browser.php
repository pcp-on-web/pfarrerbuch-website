<?php
include('har_config.php');

// Übernehme har und target- Parameter wenn nötig 
@$target=$_GET['target'];
@$har=$_GET['har'] ;

if ($target=='linkliste') {

	echo '<ul>';
	foreach($jsonObjekt->entries as $key=>$value) {
		echo '<li>'.$jsonObjekt->entries[$key]->response->content->mimeType.'</li>';
		if (str_starts_with($jsonObjekt->entries[$key]->response->content->mimeType,'text/html')||($jsonObjekt->entries[$key]->response->content->mimeType=='')||true) {
			echo '<li><a href="har_browser.php?har='.$har.'&target='.$jsonObjekt->entries[$key]->request->url.'">'.$jsonObjekt->entries[$key]->request->url.'</a></li>';
		}
	};
	echo '</ul>';
}
elseif ($target=='') {
	echo filter($jsonObjekt->entries[0]->response->content->text);
}
else {
	foreach($jsonObjekt->entries as $key=>$value) {
		if (str_ends_with($jsonObjekt->entries[$key]->request->url,$target)) {
			header('Content-Type:'.$jsonObjekt->entries[$key]->response->content->mimeType);
			if ($jsonObjekt->entries[$key]->response->content->encoding=='base64') {
				echo base64_decode($jsonObjekt->entries[$key]->response->content->text);
			}
			else {
				$result=$jsonObjekt->entries[$key]->response->content->text;
				echo filter($result);
			}
			exit;
		}
	}
	/* Sonderfälle */
	// Ersetze alle Zahlen mit mindestens 5 Ziffern (0-9) oder Buchstaben (a-f) durch "22222" 

	$target = preg_replace('/\b[\da-f]{5,}\b/', '22222', $target);
	


	foreach($jsonObjekt->entries as $key=>$value) {
		$request=$jsonObjekt->entries[$key]->request->url;
		$request = preg_replace('/\b[\da-f]{5,}\b/', '22222', $request);
		if (str_ends_with($request,$target)) {

			header('Content-Type:'.$jsonObjekt->entries[$key]->response->content->mimeType);
			if ($jsonObjekt->entries[$key]->response->content->encoding=='base64') {
				echo base64_decode($jsonObjekt->entries[$key]->response->content->text);
			}
			else {
				$result=$jsonObjekt->entries[$key]->response->content->text;
				echo filter($result);
			}
			exit;
			

		}
	}
	
	// Redirect to a different page
	$base_url = $jsonObjekt->entries['0']->request->url;
	
	$position_drittes_slash = strpos($base_url, '/', 8);
	$base_url = substr($base_url, 0, $position_drittes_slash+1); 
	
	header("Location: ".$base_url.$target);
	exit;

//	echo '<h1>Element is not captured!</h1>';
//	echo '<p><a href="'.$base_url.$target.'">'.$base_url.$target.'</a></p>';
}



/*
	$html=str_replace("browser.php?target=javascript:","javascript:",$html);

// Lese den Inhalt der JSON-Datei


//var_dump($jsonObjekt->entries[0]->response->content->text);
echo $jsonObjekt->entries[0]->response->content->text;
foreach($jsonObjekt->entries as $key=>$value) {
	//first level of json object as many as its items
  echo $key."\n===\n";
  echo $jsonObjekt->entries[$key]->request->url;
}
*/  


?>

