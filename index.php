<!DOCTYPE html>
<html>
<head>
	
	<title>Metro &amp; Tram Amsterdam</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link href="https://fonts.googleapis.com/css?family=Nunito:300,700" rel="stylesheet">

	<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.1.0/dist/leaflet.css" integrity="sha512-wcw6ts8Anuw10Mzh9Ytw4pylW8+NAD4ch3lqm9lzAsTxg0GFeJgoAtxuCLREZSC5lUXdVyo/7yfsqFjQ4S+aKw==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.1.0/dist/leaflet.js" integrity="sha512-mNqn2Wg7tSToJhvHcqfzLMU6J4mkOImSPTxVZAdo+lcPlk+GhZmYgACEe0x35K7YzW1zJ7XyJV/TT1MrdXvMcA==" crossorigin=""></script>


	<style>
		html, body{
			height: 100%;
			margin:0;
			font-family: 'Nunito', sans-serif;
		}
		#map {
			width: 100%;
			height: 100%;
		}
		.leaflet-left .leaflet-control{
			margin-top: 30px;
			margin-left: 20px;
		}
		.leaflet-container .leaflet-control-attribution{
			color: #DD0005;
		}
		.leaflet-control-attribution a{
			color: #DD0005;
		}
		#linenumbers{
			color: #DD0005;
			position: absolute;
			z-index: 1000;
			left: 80px;
			top: 30px;
		}
		#linenumbers button{
			color: #000;
			border: 1px solid #000;
			margin-right: 3px;
		}
		button:focus {outline:0;}
		#linenumbers button.on{
			background-color: #DD0005;
			color: #fff;
		}
		#linenumbers button.off{
			background-color: #fff;
			color: #000;
		}
		#linenumbers .metrolijn{
			background-color: #DD0005;
			color: #fff;
		}
		#info{
			color: #DD0005;
			position: absolute;
			z-index: 1000;
			right: 30px;
			top: 20px;
			text-align: right;
		}
		a, a:visited, a:hover{
			text-decoration: none;
			color: #000;
		}
		a:hover{
			text-decoration: underline;
		}
		#pics span{
			text-shadow: 0 0 6px #fff;
		}
		#info h1{
			margin: 0 0 0 0;
			font-size: 38px;
		}
		#info p.wiki{
			margin:0;
			font-size: 18px;
			font-weight: 700;
			vertical-align: center;
		}
		#info p.wiki img{
			width: 30px;
			margin-top: -2px;
			float: left;
		}
		#pics{
			width: 400px;
			position: absolute;
			z-index: 10000;
			bottom: 0;
			padding-bottom: 30px;
			right: 30px;
			top:120px;
			overflow-y: scroll;
			font-size: 18px;
			font-weight: 700;

		}
		#pics img{
			width: 100%;
			margin-bottom: 20px;
		}
		#pics span{
			margin-top: -80px;
			margin-bottom: 40px;
			margin-left: 310px;
			font-weight: 700;
			font-size: 28px;
			z-index: 10001;
			display: block;
		}
	</style>

	
</head>
<body>

<div id="album"></div>
<div id='map'>
</div>

<div id="linenumbers">
	<div id="linenumbers2018">

	</div>

	<div id="linenumbers2017">

	</div>
	<button id="to2018" class="off">vanaf 22 juli</button>
	<button id="to2017" class="on">tot 22 juli</button>
</div>

<div id="info">
	<h1>Metro &amp; Tram Amsterdam</h1>

	<p class="wiki"></p>

	
</div>





<script>
	var map = L.map('map',{
		attributionControl: false
	}).setView([52.35870, 4.94249], 12);

	

	L.tileLayer('https://korona.geog.uni-heidelberg.de/tiles/roadsg/x={x}&y={y}&z={z}', {
	maxZoom: 19,
	attribution: 'Imagery from <a href="http://giscience.uni-hd.de/">GIScience Research Group @ University of Heidelberg</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
	}).addTo(map);

	L.control.attribution({position: 'bottomleft'}).addTo(map);

	
	lines2018 = L.geoJson(null, {
	    style: function(feature) {
	        return {
	            color: "#DD0005",
	            weight: 2,
	            opacity: 1,
	            clickable: true
	        };
	    },
	    onEachFeature: function(feature, layer) {
			layer.on({
		        click: whenClicked
		    });
		    if(layer.feature.properties.linetype == 'metrolijn'){
		    	layer.setStyle({weight: 3});
			};
			
			$('#linenumbers2018').append($("<button></button>")
					.click(function(){showLine(layer.feature.properties.linenr)})
					.addClass(layer.feature.properties.linetype)
                    .text(layer.feature.properties.linenr));
	    }
	}).addTo(map);

	$.getJSON('lines2018.geojson', function(data) {
        lines2018.addData(data).bringToBack();
    });

    lines2017 = L.geoJson(null, {
	    style: function(feature) {
	        return {
	            color: "#DD0005",
	            weight: 2,
	            opacity: 1,
	            clickable: true
	        };
	    },
	    onEachFeature: function(feature, layer) {
			layer.on({
		        click: whenClicked
		    });
		    if(layer.feature.properties.linetype == 'metrolijn'){
		    	layer.setStyle({weight: 3});
			};
			
			$('#linenumbers2017').append($("<button></button>")
					.click(function(){showLine(layer.feature.properties.linenr)})
					.addClass(layer.feature.properties.linetype)
                    .text(layer.feature.properties.linenr));
	    }
	}).addTo(map);

	$.getJSON('lines2017.geojson', function(data) {
        lines2017.addData(data).bringToBack();
    });

    stations = L.geoJson(null, {
	    onEachFeature: function(feature, layer) {
			layer.on({
		        click: whenStationClicked
		    });
		    
		    if(layer.feature.properties.linetype == 'metrolijn'){
		    	layer.setStyle({weight: 3});
			};
	    },
        pointToLayer: function (feature, latlng) {
			return L.circleMarker(latlng, {
				radius: 6,
				fillColor: "#fff",
				color: "#000",
				weight: 4,
				opacity: 1,
				fillOpacity: 1
			});
		}
	}).addTo(map);

	geojsonfile = 'stations.geojson';
	
	$.getJSON(geojsonfile, function(data) {
        stations.addData(data).bringToFront();
    });

	showYear(2018);
	$('#to2018').click(function(){showYear(2018)});
	$('#to2017').click(function(){showYear(2017)});

    function showYear(year){
    	//return true;
    	if(year==2017){
    		map.removeLayer(lines2018);
    		map.addLayer(lines2017);
    		lines2017.bringToBack();
    		$('#linenumbers2018').hide();
    		$('#linenumbers2017').show();
    		$('#to2017').removeClass('off').addClass('on');
    		$('#to2018').removeClass('on').addClass('off');
			stations.eachLayer(function(layer) {
				if(layer.feature.properties.stationlabel=="Metrostation Noord"
					|| layer.feature.properties.stationlabel=="Metrostation Rokin"
					|| layer.feature.properties.stationlabel=="Metrostation Vijzelgracht"
					|| layer.feature.properties.stationlabel=="Metrostation De Pijp"
					|| layer.feature.properties.stationlabel=="Metrostation Europaplein"
					|| layer.feature.properties.stationlabel=="Metrostation Noorderpark"){
			    	layer.setStyle({opacity: 0, fillColor: 'none'});
				}
		    });
    	
    	}
    	if(year==2018){
    		map.removeLayer(lines2017);
    		map.addLayer(lines2018);
    		lines2018.bringToBack();
    		$('#linenumbers2017').hide();
    		$('#linenumbers2018').show();
    		$('#to2017').removeClass('on').addClass('off');
    		$('#to2018').removeClass('off').addClass('on');
    		stations.eachLayer(function(layer) {
				layer.setStyle({opacity: 1, fillColor: '#fff'});
		    });
    	}
    }


    function whenClicked(e) {
    	var props = e['target']['feature']['properties'];
		showLine(props.linenr);
	  	
	}

	function whenStationClicked(e) {
    	var props = e['target']['feature']['properties'];
		stations.eachLayer(function(layer) {

	    	lines2018.eachLayer(function(layer) {
				if(layer.feature.properties.linetype == 'metrolijn'){
			    	layer.setStyle({weight: 3});
				}else{
					layer.setStyle({weight: 2});
				}
		    });
	    	lines2017.eachLayer(function(layer) {
				if(layer.feature.properties.linetype == 'metrolijn'){
			    	layer.setStyle({weight: 3});
				}else{
					layer.setStyle({weight: 2});
				}
		    });
			layer.setStyle({radius: 6});
			if(layer.feature.properties.stationlabel == props.stationlabel){
		    	layer.setStyle({radius: 8});
			}
	    });
    	$('#album').html('');
        
        $('#info h1').html(props.stationlabel.replace('Metrostation ',''));
		$('#info p.wiki').html('gebouwd in ' + props.year);
		$('#album').load('station.php?uri=' + props.station);
	  	
	}

	function showLine(nr) {
		stations.eachLayer(function(layer) {
			layer.setStyle({radius: 6});
	    });
    	lines2018.eachLayer(function(layer) {
			layer.setStyle({color: "#DD0005"});
			if(layer.feature.properties.linenr == nr){
		    	layer.setStyle({weight: 6});
		    	showAlbum(layer.feature.properties);
			}else if(layer.feature.properties.linetype == 'metrolijn'){
		    	layer.setStyle({weight: 3});
			}else{
				layer.setStyle({weight: 2});
			}
	    });
    	lines2017.eachLayer(function(layer) {
			layer.setStyle({color: "#DD0005"});
			if(layer.feature.properties.linenr == nr){
		    	layer.setStyle({weight: 6});
		    	showAlbum(layer.feature.properties);
			}else if(layer.feature.properties.linetype == 'metrolijn'){
		    	layer.setStyle({weight: 3});
			}else{
				layer.setStyle({weight: 2});
			}
	    });
    	
	  	
	}

	function showAlbum(props){
		$('#album').html('');
        
        $('#info h1').html(props.linelabel);
		$('#info p.wiki').html('<a href="' + props.linewiki + '" target="_blank">' + props.linelabel + ' op Wikipedia &gt;</a>');
		$('#album').load('lijn.php?nr=' + props.linenr);
	}


</script>



</body>
</html>
