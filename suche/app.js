let queryTemplate = `
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
SELECT DISTINCT ?Label ?Resource WHERE {
  ?Resource a  <http://meta-pfarrerbuch.evangelische-archive.de/vocabulary#$FILTER$> .
  ?Resource rdfs:label ?Label .
  FILTER contains(?Label,'$SEARCH$')
} 
ORDER BY ?Label
`;

const endpointUrl = 'https://meta-pfarrerbuch.evangelische-archive.de/meta-daten/combined/sparql';
let currentPage = 1;
const pageSize = 10;
let results = [];

document.getElementById('search-button').addEventListener('click', () => {
    const searchString = document.getElementById('search-input').value;
    const searchFilter = document.querySelector('.filter:checked').value; // Nur eine Auswahl
    fetchResults(searchString,searchFilter);
});

async function fetchResults(searchString,searchFilter) {
    try {
        const query = queryTemplate.replace('$SEARCH$', searchString).replace('$FILTER$', searchFilter);
        const response = await fetch(endpointUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/sparql-query',
                'Accept': 'application/json',
            },
            body: query
        });
        const data = await response.json();
        results = data.results.bindings;
        currentPage = 1; // Reset to first page
        renderPage();
    } catch (error) {
        console.error('Error fetching the data:', error);
    }
}

function renderPage() {
    const startIndex = (currentPage - 1) * pageSize;
    const pageResults = results.slice(startIndex, startIndex + pageSize);

    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';

    pageResults.forEach(result => {
        // Extract <zk> from URL
        const match = result.Resource.value.match(/data\/(.*?)\//);
        const zk = match ? match[1] : '';

        const row = document.createElement('div');
        row.className = `row ${zk}`;

        const labelCell = document.createElement('div');
        labelCell.className = 'cell';
        labelCell.textContent = result.Label.value;

        const resourceCell = document.createElement('div');
        resourceCell.className = 'cell';

        const link = document.createElement('a');
        link.href = result.Resource.value;
        link.textContent = result.Resource.value;
        link.target = '_blank'; // Open link in a new tab
        resourceCell.appendChild(link);

        row.appendChild(labelCell);
        row.appendChild(resourceCell);
        resultsDiv.appendChild(row);
    });

    renderPagination();
}

function renderPagination() {
    const paginationDiv = document.getElementById('pagination');
    paginationDiv.innerHTML = '';

    const totalPages = Math.ceil(results.length / pageSize);
    for (let i = 1; i <= totalPages; i++) {
        const pageDiv = document.createElement('div');
        pageDiv.className = 'page';
        pageDiv.textContent = i;
        if (i === currentPage) {
            pageDiv.classList.add('active');
        }
        pageDiv.addEventListener('click', () => {
            currentPage = i;
            renderPage();
        });
        paginationDiv.appendChild(pageDiv);
    }
}
