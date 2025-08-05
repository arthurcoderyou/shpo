<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ArcGIS Map</title>

     
  <script src="https://js.arcgis.com/4.33/"></script>
<script>
  require([
    "esri/config",
    "esri/Map",
    "esri/views/MapView"
  ], function(esriConfig, Map, MapView) {

    esriConfig.apiKey = "{{ $apiKey }}"; // or inject token

    const map = new Map({
      basemap: "satellite" // ✅ Correct basemap ID
    });

    const view = new MapView({
      container: "viewDiv",
      map: map,
      center: [120.9842, 14.5995], // Manila
      zoom: 13
    });

  });
</script>  


</head>
<body>
  <div id="viewDiv" style="height: 100vh;"></div>

  <script src="https://js.arcgis.com/calcite-components/3.2.1/calcite.esm.js" type="module"></script>
  <link rel="stylesheet" href="https://js.arcgis.com/calcite-components/3.2.1/calcite.css" />
</body>
</html>
