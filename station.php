<?php

$sparqlquery = '
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT ?cho ?year ?img ?title  WHERE {
	?cho dct:spatial <' . $_GET['uri'] . '> .
	?cho dc:title ?title .
	?cho sem:hasBeginTimeStamp ?start .
	?cho foaf:depiction ?img .
	BIND (year(xsd:dateTime(?start)) AS ?year) .
}
ORDER BY DESC(?start)
LIMIT 20
';


$url = "https://api.druid.datalegend.net/datasets/adamnet/all/services/endpoint/sparql?query=" . urlencode($sparqlquery) . "";

$querylink = "https://druid.datalegend.net/AdamNet/all/sparql/endpoint#query=" . urlencode($sparqlquery) . "&endpoint=https%3A%2F%2Fdruid.datalegend.net%2F_api%2Fdatasets%2FAdamNet%2Fall%2Fservices%2Fendpoint%2Fsparql&requestMethod=POST&outputFormat=table";


// Druid does not like url parameters, send accept header instead
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Accept: application/sparql-results+json\r\n"
    ]
];

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$json = file_get_contents($url, false, $context);

$data = json_decode($json,true);

echo '<div id="pics">';
foreach ($data['results']['bindings'] as $row) {
	echo '<div class="pic">';
	echo '<a title="' . $row['title']['value'] . '" target="_blank" href="' . $row['cho']['value'] . '">';
	echo '<img src="' . $row['img']['value'] . '">';
	echo '</a>';
	if(isset($row->year)){
		$year = substr($row->year,0,4);
	}else{
		$year = "????";
	}
	echo '<span>' . $row['year']['value'] . '</span>';
	echo '</div>';


}
if(count($data['results']['bindings'])>1){
	echo '<a href="' . $querylink . '">Datanerds SPARQLen het zelf &gt;</a>';
}
echo '</div>';


