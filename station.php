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


$url = "https://api.data.adamlink.nl/datasets/menno/alles/services/alles/sparql?default-graph-uri=&query=" . urlencode($sparqlquery) . "&format=application%2Fsparql-results%2Bjson&timeout=120000&debug=on";

$querylink = "https://data.adamlink.nl/AdamNet/all/services/endpoint#query=" . urlencode($sparqlquery) . "&contentTypeConstruct=text%2Fturtle&contentTypeSelect=application%2Fsparql-results%2Bjson&endpoint=https%3A%2F%2Fdata.adamlink.nl%2F_api%2Fdatasets%2Fmenno%2Falles%2Fservices%2Falles%2Fsparql&requestMethod=POST&tabTitle=Query&headers=%7B%7D&outputFormat=table";


$json = file_get_contents($url);

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


