<?php
$verzeichnis = '/data/';
$txtDateien = glob($verzeichnis . '*.txt');

// Sortieren nach letztem Änderungsdatum (neueste zuerst)
usort($txtDateien, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Dateien ausgeben
echo '<ol>';
foreach ($txtDateien as $datei) {
    echo '<li>'. date('Y-m-d H:i:s', filemtime($datei)) . '</li>'. PHP_EOL;
}
echo '</ol>';
