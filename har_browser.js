console.log('HTML Page');

// Funktion, um srcset-Attribute in img-Tags zu entfernen
function removeSrcsetAttributes() {
  var imgTags = document.getElementsByTagName("img");
  for (var i = 0; i < imgTags.length; i++) {
    imgTags[i].removeAttribute("srcset");
  }
}

// Intervall erstellen, um die Funktion alle 5 Sekunden auszuführen
var intervalId = setInterval(removeSrcsetAttributes, 1000);

