<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <script src="https://js.arcgis.com/4.33/"></script>
  <style>
    html, body, #viewDiv { margin: 0; height: 100%; width: 100%; }
  </style>
</head>
<body>
  <div id="viewDiv"></div>
  <script>
    require(["esri/config", "esri/Map", "esri/views/MapView"], function(esriConfig, Map, MapView) {
      esriConfig.request.interceptors.push({
        urls: /.*\.arcgis\.com\/sharing\/rest\/.*/,
        before: function(params) {
          params.requestOptions.query = {
            ...params.requestOptions.query,
            token: "{{ $token }}"
          };
        }
      });

      const map = new Map({ basemap: "satellite" });

      const view = new MapView({
        container: "viewDiv",
        map: map,
        center: [120.9842, 14.5995],
        zoom: 13
      });
    });
  </script>
</body>
</html>
