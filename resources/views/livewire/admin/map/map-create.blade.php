<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">
    {{-- The whole world belongs to you. --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&libraries=places"></script>

     
    {{-- <gmp-map center="40.12150192260742,-100.45039367675781" zoom="4" map-id="DEMO_MAP_ID">
      <gmp-advanced-marker position="40.12150192260742,-100.45039367675781" title="My location"></gmp-advanced-marker>
    </gmp-map> --}}

    <h2>Search and Save Location</h2>
    <input type="text" id="search-box" placeholder="Search location">
    <div id="map" style="height: 500px; width: 100%;"></div>
    <button onclick="saveLocation()">Save Location</button>

    <script>
        let map, marker, searchBox;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: -1.286389, lng: 36.817223 },
                zoom: 10
            });

            marker = new google.maps.Marker({
                position: map.getCenter(),
                map: map,
                draggable: true
            });

            const input = document.getElementById("search-box");
            searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            searchBox.addListener("places_changed", function () {
                let places = searchBox.getPlaces();
                if (places.length == 0) return;

                let place = places[0];
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
            });

            marker.addListener("dragend", function () {
                console.log(marker.getPosition().lat(), marker.getPosition().lng());
            });
        }

         

        window.onload = initMap;
    </script>


   
</div>
