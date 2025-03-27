You can integrate Google Maps into your Laravel project using JavaScript and save the selected location in your database. Here's how you can achieve this:

---

### **1. Set Up Google Maps API**
- Get a Google Maps API key from the [Google Cloud Console](https://console.cloud.google.com/)
- Enable the **Places API** and **Maps JavaScript API**.

---

### **2. Create Migration for Locations**
Run the following command to create a migration:
```bash
php artisan make:migration create_locations_table
```
Modify the migration file:
```php
public function up()
{
    Schema::create('locations', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Store a label for the location
        $table->decimal('latitude', 10, 7);
        $table->decimal('longitude', 10, 7);
        $table->timestamps();
    });
}
```
Run the migration:
```bash
php artisan migrate
```

---

### **3. Create the Location Model**
Run:
```bash
php artisan make:model Location
```
Modify `app/Models/Location.php`:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'latitude', 'longitude'];
}
```

---

### **4. Create Controller**
Run:
```bash
php artisan make:controller LocationController
```
Modify `app/Http/Controllers/LocationController.php`:
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        return view('map.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Location::create($validated);

        return response()->json(['message' => 'Location saved successfully!']);
    }

    public function show($id)
    {
        $location = Location::findOrFail($id);
        return view('map.show', compact('location'));
    }
}
```

---

### **5. Define Routes**
Modify `routes/web.php`:
```php
use App\Http\Controllers\LocationController;

Route::get('/map', [LocationController::class, 'index'])->name('map.index');
Route::post('/map/store', [LocationController::class, 'store'])->name('map.store');
Route::get('/map/{id}', [LocationController::class, 'show'])->name('map.show');
```

---

### **6. Create Blade Views**

#### **(A) Search & Save Location - `resources/views/map/index.blade.php`**
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Map Integration</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>
</head>
<body>
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

        function saveLocation() {
            const lat = marker.getPosition().lat();
            const lng = marker.getPosition().lng();
            const name = document.getElementById("search-box").value;

            fetch("{{ route('map.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ name, latitude: lat, longitude: lng })
            })
            .then(response => response.json())
            .then(data => alert(data.message))
            .catch(error => console.error(error));
        }

        window.onload = initMap;
    </script>
</body>
</html>
```

---

#### **(B) Show Saved Location - `resources/views/map/show.blade.php`**
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Location</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
</head>
<body>
    <h2>Saved Location</h2>
    <p><strong>Location Name:</strong> {{ $location->name }}</p>
    <div id="map" style="height: 500px; width: 100%;"></div>

    <script>
        function initMap() {
            let map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: {{ $location->latitude }}, lng: {{ $location->longitude }} },
                zoom: 15
            });

            new google.maps.Marker({
                position: { lat: {{ $location->latitude }}, lng: {{ $location->longitude }} },
                map: map
            });
        }

        window.onload = initMap;
    </script>
</body>
</html>
```

---

### **7. Test the Implementation**
1. Visit `/map` to search for a location and save it.
2. Check your database to confirm the saved coordinates.
3. Visit `/map/{id}` to see the saved location on the map.

---

### **Bonus: Show All Saved Locations**
Modify `LocationController.php`:
```php
public function list()
{
    $locations = Location::all();
    return view('map.list', compact('locations'));
}
```
Modify `routes/web.php`:
```php
Route::get('/map-list', [LocationController::class, 'list'])->name('map.list');
```
Create `resources/views/map/list.blade.php`:
```html
<h2>Saved Locations</h2>
<ul>
    @foreach($locations as $location)
        <li><a href="{{ route('map.show', $location->id) }}">{{ $location->name }}</a></li>
    @endforeach
</ul>
```

---

### **Conclusion**
You have successfully integrated Google Maps into your Laravel project:
- Users can **search for a location**.
- Clicking **"Save Location"** stores the coordinates in the database.
- Users can **view the saved location** on a map.

Let me know if you need enhancements like filtering locations by proximity or clustering markers! 🚀