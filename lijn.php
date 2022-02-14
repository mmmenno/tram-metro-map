<?php

if(isset($_GET['nr'])){
	$nr = $_GET['nr'];
}else{
	$nr = 52;
}

$sparqlquery = '
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT ?cho ?year ?img ?title ?description WHERE {
  {
    ?cho dc:description ?description .
    ?cho dc:title ?title .
    ?cho sem:hasBeginTimeStamp ?start .
    ?cho foaf:depiction ?img .
    BIND (year(xsd:dateTime(?start)) AS ?year) .
    FILTER REGEX(?description,"lijn ' . $nr . '[^0-9]", "i")
  }
  UNION
  {
    ?cho dc:description ?description .
    ?cho dc:title ?title .
    ?cho sem:hasBeginTimeStamp ?start .
    ?cho foaf:depiction ?img .
    BIND (year(xsd:dateTime(?start)) AS ?year) .
    FILTER REGEX(?title,"lijn ' . $nr . '[^0-9]", "i")
  }
  
}ORDER BY ASC(?start)
';

$sparqlquery = '
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT ?cho ?year ?img ?title ?description WHERE {
	?cho dc:description ?description .
	?cho dc:title ?title .
	?cho sem:hasBeginTimeStamp ?start .
	?cho foaf:depiction ?img .
	BIND (year(xsd:dateTime(?start)) AS ?year) .
	FILTER REGEX(?description,"lijn ' . $nr . '[^0-9]", "i")  
}
ORDER BY DESC(?start)
LIMIT 20
';


$url = "https://api.druid.datalegend.net/datasets/adamnet/all/services/endpoint/sparql?query=" . urlencode($sparqlquery) . "";

$querylink = "https://druid.datalegend.net/AdamNet/all/sparql/endpoint#query=" . urlencode($sparqlquery) . "&endpoint=https%3A%2F%2Fdruid.datalegend.net%2F_api%2Fdatasets%2FAdamNet%2Fall%2Fservices%2Fendpoint%2Fsparql&requestMethod=POST&outputFormat=table";


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch,CURLOPT_USERAGENT,'adamlink');
$headers = [
  'Accept: application/sparql-results+json'
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$json = curl_exec ($ch);
curl_close ($ch);

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


