<?php

if(isset($_GET['year'])){
	$year = $_GET['year'];
}else{
	$year = 2018;
}

for($year=2017; $year<2018; $year++){
	$sparqlquery = '
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
	PREFIX dc: <http://purl.org/dc/elements/1.1/>
	PREFIX geo: <http://www.opengis.net/ont/geosparql#>
	PREFIX dct: <http://purl.org/dc/terms/>
	PREFIX schema: <http://schema.org/>
	SELECT ?line ?linewkt ?linenr ?linelabel ?linewiki ?linetype WHERE {
		{
			?line dc:type ?linetype .
			?line dc:identifier ?linenr .
			?line rdfs:label ?linelabel .
			?line schema:subjectOf ?linewiki .
			?line geo:hasGeometry ?geom .
			?geom dct:valid "' . $year . '"^^xsd:gYear .
			?geom geo:asWKT ?linewkt .
			FILTER (?linetype = "metrolijn"^^xsd:string)
		}UNION{
			?line dc:type ?linetype .
			?line dc:identifier ?linenr .
			?line rdfs:label ?linelabel .
			?line schema:subjectOf ?linewiki .
			?line geo:hasGeometry ?geom .
			?geom dct:valid "' . $year . '"^^xsd:gYear .
			?geom geo:asWKT ?linewkt .
			FILTER (?linetype = "tramlijn"^^xsd:string)
		}
	} ORDER BY ASC(?linenr)
	';


	$url = "https://api.data.adamlink.nl/datasets/menno/alles/services/alles/sparql?default-graph-uri=&query=" . urlencode($sparqlquery) . "&format=application%2Fsparql-results%2Bjson&timeout=120000&debug=on";

	$querylink = "https://data.adamlink.nl/AdamNet/all/services/endpoint#query=" . urlencode($sparqlquery) . "&contentTypeConstruct=text%2Fturtle&contentTypeSelect=application%2Fsparql-results%2Bjson&endpoint=https%3A%2F%2Fdata.adamlink.nl%2F_api%2Fdatasets%2Fmenno%2Falles%2Fservices%2Falles%2Fsparql&requestMethod=POST&tabTitle=Query&headers=%7B%7D&outputFormat=table";



	$json = file_get_contents($url);

	$data = json_decode($json,true);

	$fc = array("type"=>"FeatureCollection","query" => $querylink, "features"=>array());


	foreach ($data['results']['bindings'] as $row) {
		$line = array("type"=>"Feature");
		$props = array(
			"line" => $row['line']['value'],
			"linelabel" => $row['linelabel']['value'],
			"linenr" => $row['linenr']['value'],
			"linewiki" => $row['linewiki']['value'],
			"linetype" => $row['linetype']['value']
		);
		$line['geometry'] = wkt2geojson($row['linewkt']['value']);
		$line['properties'] = $props;
		$fc['features'][] = $line;
	}


	$json = json_encode($fc);

	file_put_contents('lines' . $year . '.geojson', $json);

}




$sparqlquery = '
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX geo: <http://www.opengis.net/ont/geosparql#>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX schema: <http://schema.org/>
PREFIX hg: <http://rdf.histograph.io/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>
SELECT ?station ?wkt ?label ?year WHERE {
  ?station rdf:type hg:Building .
  ?station dc:type <http://vocab.getty.edu/aat/300007780> .
  ?station geo:hasGeometry/geo:asWKT ?wkt .
  ?station skos:prefLabel ?label .
  OPTIONAL { ?station sem:hasEarliestBeginTimeStamp ?year .}
}
';


$url = "https://api.data.adamlink.nl/datasets/menno/alles/services/alles/sparql?default-graph-uri=&query=" . urlencode($sparqlquery) . "&format=application%2Fsparql-results%2Bjson&timeout=120000&debug=on";

$querylink = "https://data.adamlink.nl/AdamNet/all/services/endpoint#query=" . urlencode($sparqlquery) . "&contentTypeConstruct=text%2Fturtle&contentTypeSelect=application%2Fsparql-results%2Bjson&endpoint=https%3A%2F%2Fdata.adamlink.nl%2F_api%2Fdatasets%2Fmenno%2Falles%2Fservices%2Falles%2Fsparql&requestMethod=POST&tabTitle=Query&headers=%7B%7D&outputFormat=table";



$json = file_get_contents($url);

$data = json_decode($json,true);

$fc = array("type"=>"FeatureCollection","query" => $querylink, "features"=>array());


foreach ($data['results']['bindings'] as $row) {
	$line = array("type"=>"Feature");
	if($row['year']['value']==null){
		$year = "????";
	}else{
		$year = $row['year']['value'];
	}
	$props = array(
		"station" => $row['station']['value'],
		"stationlabel" => $row['label']['value'],
		"year" => $year
	);
	$line['geometry'] = wkt2geojson($row['wkt']['value']);
	$line['properties'] = $props;
	$fc['features'][] = $line;
}


$json = json_encode($fc);

file_put_contents('stations.geojson', $json);



function wkt2geojson($wkt){
	$coordsstart = strpos($wkt,"(");
	$type = trim(substr($wkt,0,$coordsstart));
	$coordstring = substr($wkt, $coordsstart);

	switch ($type) {
	    case "LINESTRING":
	    	$geom = array("type"=>"LineString","coordinates"=>array());
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$pairs = explode(",", $coordstring);
	    	foreach ($pairs as $k => $v) {
	    		$coords = explode(" ", $v);
	    		$geom['coordinates'][] = array((double)$coords[0],(double)$coords[1]);
	    	}
	    	return $geom;
	    	break;
	    case "POLYGON":
	    	$geom = array("type"=>"Polygon","coordinates"=>array());
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$pairs = explode(",", $coordstring);
	    	foreach ($pairs as $k => $v) {
	    		$coords = explode(" ", $v);
	    		$geom['coordinates'][0][] = array((double)$coords[0],(double)$coords[1]);
	    	}
	    	return $geom;
	    	break;
	    case "MULTILINESTRING":
	    	$geom = array("type"=>"MultiLineString","coordinates"=>array());
	    	preg_match_all("/\([0-9. ,]+\)/",$coordstring,$matches);
	    	//print_r($matches);
	    	foreach ($matches[0] as $linestring) {
	    		$linestring = str_replace(array("(",")"), "", $linestring);
		    	$pairs = explode(",", $linestring);
		    	$line = array();
		    	foreach ($pairs as $k => $v) {
		    		$coords = explode(" ", $v);
		    		$line[] = array((double)$coords[0],(double)$coords[1]);
		    	}
		    	$geom['coordinates'][] = $line;
	    	}
	    	return $geom;
	    	break;
	    case "POINT":
			$coordstring = str_replace(array("(",")"), "", $coordstring);
	    	$coords = explode(" ", $coordstring);
	    	//print_r($coords);
	    	$geom = array("type"=>"Point","coordinates"=>array((double)$coords[0],(double)$coords[1]));
	    	return $geom;
	        break;
	}
}
