let query = `
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
SELECT DISTINCT ?Label ?Resource WHERE {
  ?Resource a  <http://meta-pfarrerbuch.evangelische-archive.de/vocabulary#Pfarrer-in> .
  ?Resource rdfs:label ?Label .
  FILTER contains(?Label,'$search$')
} 
LIMIT 100
`;

let count_query = `
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
SELECT DISTINCT (count(?Resource) as ?Anzahl) WHERE {
  ?Resource a  <http://meta-pfarrerbuch.evangelische-archive.de/vocabulary#Pfarrer-in> .
  ?Resource rdfs:label ?Label .
  FILTER contains(?Label,'$search$')
} 
`;

let endpoints=['https://meta-pfarrerbuch.evangelische-archive.de/meta-daten/kps/sparql','https://meta-pfarrerbuch.evangelische-archive.de/meta-daten/sachsen/sparql','https://meta-pfarrerbuch.evangelische-archive.de/meta-daten/brandenburg/sparql'];

let result = '';

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');

    searchButton.addEventListener('click', function() {

        const resultContainer = document.getElementById("results");
        const countContainer = document.getElementById("counts");
    
        resultContainer.innerHTML='';
        countContainer.innerHTML='';
    
        searchInput.value = searchInput.value.trim().replace(new RegExp("'", 'g'),"")

        let sparql_query = count_query.replace('$search$', searchInput.value);
        executeQueryOverMultipleEndpoints(sparql_query,endpoints,countContainer);

        sparql_query = query.replace('$search$', searchInput.value);

        // Hier kannst du deine Logik einfügen, um die SPARQL-Abfrage auszuführen
        // und die Ergebnisse anzuzeigen
        executeQueryOverMultipleEndpoints(sparql_query,endpoints, resultContainer);
    });
    
    // Event-Listener für das Keydown-Ereignis auf dem Eingabefeld
    searchInput.addEventListener("keydown", function(event) {
    		// Überprüfen, ob die gedrückte Taste die Enter-Taste ist (Key-Code 13)
    		if (event.keyCode === 13) searchButton.click();
     });
});


// Funktion zum Ausführen eines SPARQL-Query
function executeSPARQLQuery(query, endpoint) {
    return new Promise((resolve, reject) => {
        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    resolve(JSON.parse(this.responseText));
                } else {
                    reject(new Error(this.statusText));
                }
            }
        };
        xhr.open("POST", endpoint, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("query=" + encodeURIComponent(query));
    });
}

// Funktion zum Ausführen des SPARQL-Query über mehrere Endpunkte
async function executeQueryOverMultipleEndpoints(query, endpoints,tableContainer) {
    try {
        const results = await Promise.all(endpoints.map(endpoint => {
            return executeSPARQLQuery(query, endpoint);
        }));
        
        // Mergen der Ergebnisse und Hinzufügen des Endpunkts
        const mergedResult = results.reduce((acc, cur, index) => {
            return acc.concat(cur.results.bindings.map(result => {
                result.Endpoint = { value: endpoints[index] };
                return result;
            }));
        }, []);


        //console.log("Merged Result:", mergedResult);


        displayTable(mergedResult,tableContainer) ;

    } catch (error) {
        console.error("Fehler beim Ausführen der Queries:", error);
    }
}

// Funktion zum Erstellen und Anzeigen der Ergebnis-Div-Tabelle
function displayTable(results,tableContainer) {
    
    // Erstellen der Tabellenüberschriften
    const headers = Object.keys(results[0]);
    const headerRow = document.createElement("div");
    headerRow.className = "row header-row";
    headers.forEach(header => {
        const cell = document.createElement("div");
        cell.className = "cell header-cell";
        cell.textContent = header;
        headerRow.appendChild(cell);
    });
    tableContainer.appendChild(headerRow);

    // Einfügen der Daten in die Tabelle
    results.forEach(result => {
        const row = document.createElement("div");
        row.className = "row";
        headers.forEach(header => {

            const cell = document.createElement("div");
            cell.className = "cell";
            if (result[header].type=='uri') {
                cell.innerHTML = '<a href="'+result[header].value+'">'+result[header].value+'</a>';
            }
            else {
                cell.textContent = result[header].value;
            }
            row.appendChild(cell);
        });
        tableContainer.appendChild(row);
    });
 
 		if (tableContainer.id=='results') {
 			document.getElementById("resultscounts").innerText = "Die Anzahl der angezeigten Einträge beträgt: " + results.length;
 		} 
   
}


