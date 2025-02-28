<?php

// Erzeugen eines 8-zeichenlangen zufälligen Hashs bestehend aus Buchstaben
function generateRandomString($length = 8) {
    // Verwende zufällige Bytes, wandle in Hex um, und filtere nur Buchstaben heraus
    $randomBytes = random_bytes($length);
    $hash = bin2hex($randomBytes);
    
    // Substring nur aus Buchstaben, verwerfe Zahlen
    $filtered = preg_replace('/[^a-zA-Z]/', '', $hash);
    
    // Kürzen oder aufstocken des Filters (notwendig falls Filter weniger als 8 Zeichen ergibt)
    return substr(str_pad($filtered, $length, 'A'), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Daten aus dem Formular sammeln
    $name = htmlspecialchars(($_POST['name']));
    $institution = htmlspecialchars(($_POST['institution']));
    $email = htmlspecialchars(($_POST['email']));
    $project_description = htmlspecialchars(($_POST['project_description']));
    $dataset_reference = htmlspecialchars(($_POST['dataset_reference']));
    
    $index = date('YmdHis-').generateRandomString();

    // Speichere die Daten in einer Datei oder Datenbank (im Beispiel speichern wir es in eine Datei)
    $fileContent = "Name: $name\nForschungseinrichtung: $institution\nEmail: $email\nProjektbeschreibung: $project_description\nBezug zum Datensatz: $dataset_reference\n---\n\n";
    file_put_contents('.submission-'.$index.'.txt', $fileContent);
    
    
    if ($content=file_get_contents('.submission-'.$index.'.txt')) {
        echo "<p>Folgende Daten wurden erfolgreich übermittelt:</p>";
        echo '<textarea style="width: 100%" rows=5 readonly>'.$content.'</textarea>';
    } else {
        echo "Es gab ein Problem beim Senden der Daten.";
    }
    echo '<p><a href="index.html">Formular erneut ausfüllen</a></p>';

	
/*
    // Sende die E-Mail
    $to = 'thomas.riechert@htwk-leipzig.de';
    $subject = 'Neue Formularübermittlung';
    $message = "Name: $name\nForschungseinrichtung: $institution\nEmail: $email\nProjektbeschreibung: $project_description\nBezug zum Datensatz: $dataset_reference";
    $headers = "From: $to";

    if (mail($to, $subject, $message, $headers)) {
        echo "Daten erfolgreich übermittelt!";
        
    } else {
        echo "Es gab ein Problem beim Senden der E-Mail.";
    }
*/
}
?>
