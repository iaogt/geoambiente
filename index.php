<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<title>Guate - GeoAmbiente</title>
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css" />
	<script src="http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js"></script>
    <script src="https://earthdata.nasa.gov/labs/gibs/examples/leaflet/lib/proj4-2.0.0/proj4.js"></script>
    <script src="https://earthdata.nasa.gov/labs/gibs/examples/leaflet/lib/proj4leaflet-0.7.0/proj4leaflet.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<style type="text/css">
		#map { 
			height: 400px;
			margin-top:60px; 
		}
		
		#map2 { 
			height: 400px;
			margin-top:60px; 
		}
	</style>
	<script>
		var map=null;
		var map2=null;
		var cargaDatos=0;
		var layer1;
		var ubicacion;
		var marcador2=false;
		var radius;
		
		var EPSG4326 = new L.Proj.CRS(
	        "EPSG:4326",
	        "+proj=longlat +ellps=WGS84 +datum=WGS84 +no_defs", {
	            origin: [-180, 90],
	            resolutions: [
	                0.5625,
	                0.28125,
	                0.140625,
	                0.0703125,
	                0.03515625,
	                0.017578125,
	                0.0087890625,
	                0.00439453125,
	                0.002197265625
	            ],
	            // Values are x and y here instead of lat and long elsewhere.
	            bounds: [
	               [-180, -90],
	               [180, 90]
	            ]
	        }
	    );
		
		$(document).ready(function(){
			iniciarMapa();
			localizar();
			$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				e.target // activated tab
				e.relatedTarget // previous tab
			})
		});
		
		function iniciarMapa(){
			//API GOOGLE
			//https://www.googleapis.com/fusiontables/v1/query?sql=SELECT%20name%20FROM%201jJiPsvwyqQHAppMqBzGomtvRfIbsoYy3qmbfVCm5%20limit%2010&key=AIzaSyAzFJf7mcuzbJuf_eE0F4KZh9fhVKab0jk
		    map = L.map("map",{
		    	maxBounds: [
		            [18.7, -92.27],
		            [13.55,-88.27]
		        ]    
		    }).setView([15,-90],13);
			L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
						attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
					}).addTo(map);
		}
		
		function onLocationFound(e) {
			ubicacion=e.latlng;
			radius = e.accuracy / 2;

			L.marker(ubicacion).addTo(map)
				.bindPopup("You are within " + radius + " meters from this point").openPopup();

			L.circle(ubicacion, radius).addTo(map);
				cargarInfo(e);
		}
		
		function cargarInfo(e){
			$('#loader').css('visibility','visible');
			cargaDatos=0;
			$('#txtPosicion').val(e.latlng.lat+','+e.latlng.lng);
			$.getJSON('https://www.googleapis.com/fusiontables/v1/query?sql=SELECT%20name%2Cdescription%20FROM%201p_0SSTFeLhrhGxCt2e8RvLU3qEAo80RcFX0y2KWh%20where%20st_intersects(geometry%2Ccircle(latlng('+e.latlng.lat+'%2C'+e.latlng.lng+'),1466))%20limit%2010&key=AIzaSyAzFJf7mcuzbJuf_eE0F4KZh9fhVKab0jk', 
			function( data ) {
				cargaDatos=cargaDatos+1;
				$('#txtUbica').html(data['rows'][0][1]);
				actualizaInterfaz();
			});
			$.getJSON('https://www.googleapis.com/fusiontables/v1/query?sql=SELECT%20name%2Cdescription%20FROM%201jJiPsvwyqQHAppMqBzGomtvRfIbsoYy3qmbfVCm5%20where%20st_intersects(geometry%2Ccircle(latlng('+e.latlng.lat+'%2C'+e.latlng.lng+'),1466))%20limit%2010&key=AIzaSyAzFJf7mcuzbJuf_eE0F4KZh9fhVKab0jk', 
			function( data ) {
				cargaDatos=cargaDatos+1;
				$('#txtLugar').val(data['rows'][0][0]);
				var tipo = data['rows'][0][1];
				$('#txtTipoLugar').html(tipo);
				actualizaInterfaz();
			});
			$.getJSON('https://www.googleapis.com/fusiontables/v1/query?sql=SELECT%20name%2Cdescription%20FROM%2013Bto_iZ1WNTzKBaj0L2VkberRVeKHrblK4vYXrMF%20where%20st_intersects(geometry%2Ccircle(latlng('+e.latlng.lat+'%2C'+e.latlng.lng+'),1466))%20limit%2010&key=AIzaSyAzFJf7mcuzbJuf_eE0F4KZh9fhVKab0jk', 
			function( data ) {
				cargaDatos=cargaDatos+1;
				$('#txtGeologia').html(data['rows'][0][1]);
				actualizaInterfaz();
			});
		}
		
		function actualizaInterfaz(){
			if(cargaDatos==3){
				$('#loader').css('visibility','hidden');
			}
		}

		function onLocationError(e) {
			alert(e.message);
		}
		
		function localizar(){
			map.on('locationfound', onLocationFound);
			map.on('locationerror', onLocationError);
			map.locate({setView: true, maxZoom: 2});
		}
		
		function vistaNormal(){
			$('#map2').css('display','none');
			$('#map').css('display','block');
			return false; 
		}
		
		function vistaCorrected(){
			$('#map').css('display','none');
		    $('#map2').css('display','block');
		    if(map2==null){
			    map2 = L.map("map2", {
			        zoom: 2,
			        crs: EPSG4326,
			        maxBounds: [
			            [18.7, -92.27],
			            [13.55,-88.27]
			        ]
			    }).setView(ubicacion,4);
		    }
		    var template = "http://map1.vis.earthdata.nasa.gov/wmts-geo/MODIS_Terra_CorrectedReflectance_TrueColor/default/{time}/{tileMatrixSet}/{z}/{y}/{x}.jpg";
		    if(layer1!=null){
		    	map2.removeLayer(layer1);
		    }
		    layer1 = L.tileLayer(template, {
		        layer: "MODIS_Terra_CorrectedReflectance_TrueColor",
		        tileMatrixSet: "EPSG4326_250m",
		        time: "2013-03-29",
		        tileSize: 512,
		        subdomains: "abc",
		        noWrap: true,
		        continuousWorld: true,
		        // Prevent Leaflet from retrieving non-existent tiles on the
		        // borders.
		        bounds: [
		            [18.7, -92.27],
		            [13.55,-88.27]
		        ],
		        attribution:
		            "<a href='https://earthdata.nasa.gov/gibs'>" +
		            "NASA EOSDIS GIBS</a>&nbsp;&nbsp;&nbsp;" +
		            "<a href='https://github.com/nasa-gibs/web-examples/blob/release/leaflet/js/geographic-epsg4326.js'>" +
		            "View Source" +
		            "</a>"
		    });
			    map2.addLayer(layer1);
		    if(!marcador2){
		    	marcador2=true;
				L.marker(ubicacion).addTo(map2)
					.bindPopup("You are within " + radius + " meters from this point").openPopup();
				L.circle(ubicacion, radius).addTo(map2);
			}
			return false;
		}
		
		function vistaTemp(){
			$('#map').css('display','none');
		    $('#map2').css('display','block');
		    if(map2==null){
			    map2 = L.map("map2", {
			        zoom: 2,
			        crs: EPSG4326,
			        maxBounds: [
			            [18.7, -92.27],
			            [13.55,-88.27]
			        ]
			    }).setView(ubicacion,4);
		    }
		    var template = "http://map1.vis.earthdata.nasa.gov/wmts-geo/MODIS_Terra_Land_Surface_Temp_Day/default/{time}/{tileMatrixSet}/{z}/{y}/{x}.png";
		    if(layer1!=null){
		    	map2.removeLayer(layer1);
		    }
		    layer1 = L.tileLayer(template, {
		        layer: "MODIS_Terra_Land_Surface_Temp_Day",
		        tileMatrixSet: "EPSG4326_1km",
		        time: "2013-03-29",
		        tileSize: 512,
		        subdomains: "abc",
		        noWrap: true,
		        continuousWorld: true,
		        // Prevent Leaflet from retrieving non-existent tiles on the
		        // borders.
		        bounds: [
		            [18.7, -92.27],
		            [13.55,-88.27]
		        ],
		        attribution:
		            "<a href='https://earthdata.nasa.gov/gibs'>" +
		            "NASA EOSDIS GIBS</a>&nbsp;&nbsp;&nbsp;" +
		            "<a href='https://github.com/nasa-gibs/web-examples/blob/release/leaflet/js/geographic-epsg4326.js'>" +
		            "View Source" +
		            "</a>"
		    });
			map2.addLayer(layer1);
		    if(!marcador2){
		    	marcador2=true;
				L.marker(ubicacion).addTo(map2)
					.bindPopup("You are within " + radius + " meters from this point").openPopup();
				L.circle(ubicacion, radius).addTo(map2);
			}
			return false;
		}
		
		function vistaNoche(){
			$('#map').css('display','none');
		    $('#map2').css('display','block');
		    if(map2==null){
			    map2 = L.map("map2", {
			        zoom: 2,
			        crs: EPSG4326,
			        maxBounds: [
			            [18.7, -92.27],
			            [13.55,-88.27]
			        ]
			    }).setView(ubicacion,4);
		    }
		    var template = "http://map1.vis.earthdata.nasa.gov/wmts-geo/VIIRS_CityLights_2012/default/{tileMatrixSet}/{z}/{y}/{x}.jpg";
		    if(layer1!=null){
		    	map2.removeLayer(layer1);
		    }
		    layer1 = L.tileLayer(template, {
		        layer: "VIIRS_CityLights_2012",
		        tileMatrixSet: "EPSG4326_500m",
		        tileSize: 512,
		        subdomains: "abc",
		        noWrap: true,
		        continuousWorld: true,
		        // Prevent Leaflet from retrieving non-existent tiles on the
		        // borders.
		        bounds: [
		            [18.7, -92.27],
		            [13.55,-88.27]
		        ],
		        attribution:
		            "<a href='https://earthdata.nasa.gov/gibs'>" +
		            "NASA EOSDIS GIBS</a>&nbsp;&nbsp;&nbsp;" +
		            "<a href='https://github.com/nasa-gibs/web-examples/blob/release/leaflet/js/geographic-epsg4326.js'>" +
		            "View Source" +
		            "</a>"
		    });
			map2.addLayer(layer1);
		    if(!marcador2){
		    	marcador2=true;
				L.marker(ubicacion).addTo(map2)
					.bindPopup("You are within " + radius + " meters from this point").openPopup();
				L.circle(ubicacion, radius).addTo(map2);
			}
			return false;
		}
	</script>
  </head>
  <body>
	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Guate - GeoAmbiente</a>
        </div>
      </div>
    </div>
    <div class="container">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" style="margin-top:60px">
		  <li class="active"><a href="#mapa" data-toggle="tab">Mapa</a></li>
		  <li><a href="#info" data-toggle="tab">Información</a></li>
		  <li><a href="#acerca" data-toggle="tab">Acerca de</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
		  <div class="tab-pane active" id="mapa"><br />
				<div id="map"></div>
				<div id="map2" style="display: none;"></div>
				<hr/>
			  	<blockquote>
					<h4>Vistas Satelitales</h4>
				</blockquote>
				<form style="margin-left:30px;">
					<div class="row">
					<button type="button" class="btn btn-primary" onclick="return vistaNormal();">Normal</button>
					<button type="button" class="btn btn-primary" onclick="return vistaCorrected();">Satelital</button>
					<button type="button" class="btn btn-primary" onclick="return vistaTemp();">Temperatura</button>
					<button type="button" class="btn btn-primary" onclick="return vistaNoche();">Nocturno</button>
					</div>
				</form>
		  </div>
		  <div class="tab-pane" id="info">
		  	<img src="ajax-loader.gif" id="loader" style="visibility:hidden"/>
				<form role="form">
					<h3>Información General</h3>
					<div class="form-group">
						<label>Posición (Lat,Lng):</label>
						<input type="text" class="form-control" id="txtPosicion"/>
					</div>
					<div class="form-group">
						<label>Ubicación:</label>
						<div id="txtUbica"></div>
					</div>
					<div class="form-group">
						<label>Tipo lugar:</label>
						<div id="txtTipoLugar"></div>
					</div>
					<div>
						<div id="txtGeologia"></div>
					</div>
					<div class="form-group">
						<label>Id lugar:</label>
						<input type="text" class="form-control" id="txtLugar"/>
					</div>
				</form>
		  </div>
		  <div class="tab-pane" id="acerca">
		  	<div class="row">
		  	<h3>Guate - GeoAmbiente</h3>
		  	<p><b>Fuentes de Información:</b><ul><li>NASA</li><li>Instituto Geográfico Nacional</li></ul></p>
		  	</div>
		  </div>
		</div>

	</div>
	<div id="footer" style="background-color: #222">
      <div class="container">
        <p style="color:#fff"><a href="#map">Volver arriba</a></p>
      </div>
    </div>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>