<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto grid grid-cols-12 gap-x-2">
      
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&libraries=places,drawing,geometry,marker&loading=async&callback=initMap" async defer></script>

</script>

<script>
    let map;
    let drawingManager;
    let polygons = [], markers = [], circles = [], rectangles = [];
    let markerCounter = 1, polygonCounter = 1, circleCounter = 1, rectangleCounter = 1;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 13.4443, lng: 144.7937 },
            zoom: 12,
            mapTypeId: "hybrid",
            mapID: "b8a417eb8ce636e",
        });


        const input = document.getElementById("search-box");
        const autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener("place_changed", function () {
            let place = autocomplete.getPlace();
            if (!place.geometry) {
                console.error("No geometry found for this place.");
                return;
            }
            
            // Move map to selected location
            map.setCenter(place.geometry.location);
            map.setZoom(15); // Zoom in
        });



        // Enable Drawing Tools
        drawingManager = new google.maps.drawing.DrawingManager({
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_LEFT,
                drawingModes: ["marker", "polygon", "circle", "rectangle"]
            },
            markerOptions: { draggable: true, icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png" },
            polygonOptions: { 
                editable: true, 
                draggable: true, 
                strokeColor: "#FF0000",  // Stroke color (border)
                strokeOpacity: 0.8,      // Stroke transparency
                strokeWeight: 2,         // Border thickness
                fillColor: "#FF0000",    // Background color
                fillOpacity: 0.35        // Background transparency
            },
            circleOptions: { editable: true, draggable: true, fillColor: "#00FF00", strokeColor: "#00AA00" },
            rectangleOptions: { editable: true, draggable: true, fillColor: "#0000FF", strokeColor: "#000088" }
        });

        drawingManager.setMap(map);

        

        // Marker Placement
        google.maps.event.addListener(drawingManager, "markercomplete", function (marker) {
            
            let markerId = `marker-${markerCounter++}`;


            // Define title and description for the InfoWindow
            const title = "New Marker";
            const description = "This is a description for the new marker.";

            // Create the content for the InfoWindow with title and description
            const contentString =
                
                `<h1>${title}</h1>` +
                `<p>${description}</p>`;

            const infowindow = new google.maps.InfoWindow({
                content: contentString,
                ariaLabel: "Marker",
            });


            // Keep using google.maps.Marker
            const newMarker = new google.maps.Marker({
                position: marker.getPosition(),
                map: map,
                title: "New Marker",
                draggable: true,
                editable: true
            });


            // ✅ Use a properly encoded SVG as the marker icon
            const encodedSVG = encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px">
                    <path fill="#FFFF00" d="M24,4C15.2,4,8,11.2,8,20c0,9.2,13.6,22.8,14.2,23.4c0.5,0.5,1.2,0.5,1.7,0C26.4,42.8,40,29.2,40,20 C40,11.2,32.8,4,24,4z"></path>
                    <circle fill="#FFFFFF" cx="24" cy="20" r="6"></circle>
                </svg>`);

            const customMarker = {
                url: `data:image/svg+xml;charset=UTF-8,${encodedSVG}`,
                scaledSize: new google.maps.Size(40, 40), // Resize if needed
            };

            newMarker.setIcon(customMarker);


            newMarker.addListener("click", () => {
                infowindow.open({
                    anchor: newMarker,
                    map: map,
                });

                highlightObject(markerId);
            });


            


            markers.push({ id: markerId, marker: newMarker });

            // Remove the old marker (optional)
            marker.setMap(null);

            addToSidebar(markerId, "Marker", "N/A");
        }); 

        // Polygon Drawing
        google.maps.event.addListener(drawingManager, "polygoncomplete", function (polygon) {
            polygon.setOptions({ 
                draggable: true, 
                editable: true,
                // strokeColor: "#FF0000",   // Red border
                strokeOpacity: 0.8,
                strokeWeight: 2,
                // fillColor: "#FF0000",     // Red fill
                fillOpacity: 0.35,
            });
            

            let polygonId = `polygon-${polygonCounter++}`;


            // Define title and description for the InfoWindow
            const title = "New Polygon";
            const description = "This is a description for the new polygon.";

            // Create the content for the InfoWindow with title and description
            const contentString =
                
                `<h1>${title}</h1>` +
                `<p>${description}</p>`;

            const infowindow = new google.maps.InfoWindow({
                content: contentString,
                ariaLabel: "Polygon",
            });


            // Get first vertex or centroid to place InfoWindow
            let path = polygon.getPath();
            let position = path.getAt(0); // First vertex

            polygon.addListener("click", (event) => {
                infowindow.setPosition(event.latLng || position); // Show at clicked point or first vertex
                infowindow.open(map);
                highlightObject(polygonId);
            });


            polygons.push({ id: polygonId, polygon });
            let area = google.maps.geometry.spherical.computeArea(polygon.getPath());


            

            addToSidebar(polygonId, "Polygon", area.toFixed(2));

            // Log the structure of the polygons array
            console.log("Polygon Array:", polygons); 
            
        });



        // Circle Drawing
        google.maps.event.addListener(drawingManager, "circlecomplete", function (circle) {
            circle.setOptions({ 
                draggable: true, 
                editable: true,
                // strokeColor: "#008000",   // Green border
                strokeOpacity: 0.8,
                strokeWeight: 2,
                // fillColor: "#008000",     // Green fill
                fillOpacity: 0.35,
            });

            let circleId = `circle-${circleCounter++}`;


            // Define title and description for the InfoWindow
            const title = "New Circle";
            const description = "This is a description for the new circle.";

            // Create the content for the InfoWindow with title and description
            const contentString =
                
                `<h1>${title}</h1>` +
                `<p>${description}</p>`;

            const infowindow = new google.maps.InfoWindow({
                content: contentString,
                ariaLabel: "Circle",
            });


            // Get center position to place InfoWindow
            let position = circle.getCenter();

            // Show InfoWindow on click
            circle.addListener("click", (event) => {
                infowindow.setPosition(event.latLng || position);
                infowindow.open(map);
                highlightObject(circleId);
            });


            circles.push({ id: circleId, circle });

            let radius = google.maps.geometry.spherical.computeArea(circle.getBounds());
            addToSidebar(circleId, "Circle", radius.toFixed(2));

            google.maps.event.addListener(circle, "radius_changed", function () {
                updateArea(circleId, google.maps.geometry.spherical.computeArea(circle.getBounds()).toFixed(2));
            });

            
        });

        // Rectangle Drawing
        google.maps.event.addListener(drawingManager, "rectanglecomplete", function (rectangle) {
            rectangle.setOptions({ 
                draggable: true, 
                editable: true,
                // strokeColor: "#FF00FF",   // Blue border
                strokeOpacity: 0.8,
                strokeWeight: 2,
                // fillColor: "#00FFFF",     // Blue fill
                fillOpacity: 0.35,  
            });

            let rectId = `rectangle-${rectangleCounter++}`;


            // Define title and description for the InfoWindow
            const title = "New Rectangle";
            const description = "This is a description for the new rectangle.";

            // Create the content for the InfoWindow with title and description
            const contentString =
                `<h1>${title}</h1>` +
                `<p>${description}</p>`;

            const infowindow = new google.maps.InfoWindow({
                content: contentString,
                ariaLabel: "Rectangle",
               
            });

            // Get center of rectangle bounds
            let bounds = rectangle.getBounds();
            let center = {
                lat: (bounds.getNorthEast().lat() + bounds.getSouthWest().lat()) / 2,
                lng: (bounds.getNorthEast().lng() + bounds.getSouthWest().lng()) / 2
            };

            // Show InfoWindow on click
            rectangle.addListener("click", (event) => {
                infowindow.setPosition(event.latLng || center);
                infowindow.open(map);
                highlightObject(rectId);
            });




            rectangles.push({ id: rectId, rectangle });

            let area = computeRectangleArea(rectangle);
            addToSidebar(rectId, "Rectangle", area.toFixed(2));

            google.maps.event.addListener(rectangle, "bounds_changed", function () {
                updateArea(rectId, computeRectangleArea(rectangle).toFixed(2));
            });

        });

        // google.maps.event.addListener(map, "click", function () {
        //     clearActiveHighlights();
        // });
    }

    function addToSidebar(id, type, area) {
        // Define default colors based on type
        let defaultStroke = "#000000"; // Default black stroke
        let defaultFill = "#FF0000";   // Default red fill

        
        if (type === "Rectangle") {
            defaultStroke = "#0000FF"; // Blue stroke for rectangle
            defaultFill = "#0000FF";   //  blue fill
        } else if (type === "Circle") {
            defaultStroke = "#008000"; // Green stroke for circle
            defaultFill = "#008000";   //  green fill
        } else if (type === "Polygon") {
            defaultStroke = "#FF0000"; // red stroke for polygon
            defaultFill = "#FF0000";   // red fill
        }else if(type == "Marker"){
            defaultFill = "#FFFF00";   // yellow fill
        }


        const list = document.getElementById("list");
        const item = document.createElement("div");
        item.setAttribute("id", id);
        


        item.classList.add("sidebar-item","bg-gray-50", "border", "border-gray-200", "rounded-lg", "p-2", "space-y-2", "mb-2");

        // // Title
        // const title = document.createElement("strong");
        // title.textContent = `${type} ${id.split('-')[1]}`;

        // // Title input
        // const titleInput = document.createElement("input");
        // titleInput.setAttribute("type", "text");
        // titleInput.setAttribute("id", `title-${id}`);
        // titleInput.setAttribute("placeholder", "Enter title");

        // // Description input
        // const descInput = document.createElement("input");
        // descInput.setAttribute("type", "text");
        // descInput.setAttribute("id", `desc-${id}`);
        // descInput.setAttribute("placeholder", "Enter description");

        // // Stroke Color
        // const strokeLabel = document.createElement("label");
        // strokeLabel.textContent = "Stroke Color:";
        // const strokeInput = document.createElement("input");
        // strokeInput.setAttribute("type", "color");
        // strokeInput.setAttribute("id", `stroke-${id}`);
        // strokeInput.setAttribute("value", "#FF0000");
        // strokeInput.setAttribute("onchange", `updateShapeColor('${id}', this.value, 'stroke')`);

        // // Fill Color
        // const fillLabel = document.createElement("label");
        // fillLabel.textContent = "Fill Color:";
        // const fillInput = document.createElement("input");
        // fillInput.setAttribute("type", "color");
        // fillInput.setAttribute("id", `fill-${id}`);
        // fillInput.setAttribute("value", "#FF0000");
        // fillInput.setAttribute("onchange", `updateShapeColor('${id}', this.value, 'fill')`);

        // Area Display
        const areaLabel = document.createElement("span");
        areaLabel.setAttribute("id", `area-${id}`);
        areaLabel.classList.add(  "px-2", "block", "text-xs", "text-gray-500");
        areaLabel.textContent = `Area: ${area} sqm`;

        

        // // Delete Button
        // const deleteButton = document.createElement("button");
        // deleteButton.textContent = "Delete";
        // deleteButton.onclick = function () {
        //     deleteFn(id);
        // };


        

        // Create the collapsible container
        const container = document.createElement("div");
        container.classList.add("flex", "rounded-lg");

        // Collapse button
        const locateButton = document.createElement("button");
        locateButton.setAttribute("type", "button");
        locateButton.setAttribute("id", `hs-basic-collapse-${id}`);
        locateButton.setAttribute("aria-expanded", "false");
        locateButton.setAttribute("aria-controls", `hs-basic-collapse-heading-${id}`);
        locateButton.setAttribute("data-hs-collapse", `#hs-basic-collapse-heading-${id}`);
        locateButton.classList.add("hs-collapse-toggle", "px-2", "inline-flex", "items-center", "min-w-fit", "rounded-s-md", "border", "border-e-0", "border-gray-200");
        locateButton.innerHTML = `<svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#63E6BE" d="M352.2 425.8l-79.2 79.2c-9.4 9.4-24.6 9.4-33.9 0l-79.2-79.2c-15.1-15.1-4.4-41 17-41h51.2L228 284H127.2v51.2c0 21.4-25.9 32.1-41 17L7 272.9c-9.4-9.4-9.4-24.6 0-33.9L86.2 159.8c15.1-15.1 41-4.4 41 17V228H228V127.2h-51.2c-21.4 0-32.1-25.9-17-41l79.2-79.2c9.4-9.4 24.6-9.4 33.9 0l79.2 79.2c15.1 15.1 4.4 41-17 41h-51.2V228h100.8v-51.2c0-21.4 25.9-32.1 41-17l79.2 79.2c9.4 9.4 9.4 24.6 0 33.9L425.8 352.2c-15.1 15.1-41 4.4-41-17V284H284v100.8h51.2c21.4 0 32.1 25.9 17 41z"/></svg>`;

        
        // Center map on selected object (marker, polygon, rectangle, or circle) when clicked
        locateButton.addEventListener("click", function () {
            let selectedObject = null;

            // Find the object by id (marker, polygon, rectangle, circle)
            if (id.startsWith("marker-")) {
                selectedObject = markers.find(m => m.id === id)?.marker;
            } else if (id.startsWith("polygon-")) {
                selectedObject = polygons.find(p => p.id === id)?.polygon;
            } else if (id.startsWith("rectangle-")) {
                selectedObject = rectangles.find(r => r.id === id)?.rectangle;
            } else if (id.startsWith("circle-")) {
                selectedObject = circles.find(c => c.id === id)?.circle;
            }

            if (selectedObject) {
                // If it's a marker, we center on its position
                if (selectedObject.getPosition) {
                    map.setCenter(selectedObject.getPosition());
                }
                // If it's a polygon, rectangle, or circle, we center on its bounds or path
                else if (selectedObject.getBounds) {
                    map.fitBounds(selectedObject.getBounds());
                }
                // If it's a polygon, center on the first path element (or centroid if necessary)
                else if (selectedObject.getPath) {
                    let path = selectedObject.getPath();
                    let center = path.getAt(0); // Or compute centroid
                    map.setCenter(center);
                }
                // If it's a circle, center on its center point
                else if (selectedObject.getCenter) {
                    map.setCenter(selectedObject.getCenter());
                }

                map.setZoom(16); // Adjust zoom level if needed

                // Highlight the selected object
                highlightObject(id);

            }
        });



        // Input field for the title
        const titleInput = document.createElement("input");
        titleInput.setAttribute("type", "text");
        titleInput.setAttribute("id", `title-${id}`); 
        titleInput.setAttribute("placeholder", "Enter title");
        titleInput.classList.add("py-1.5", "sm:py-2", "px-3", "pe-11", "block", "w-full", "border-gray-200", "border", "border-e-0", "sm:text-sm", "focus:z-10", "focus:border-blue-500", "focus:ring-blue-500");
        titleInput.setAttribute("onchange", `onUpdateShapeDetails('${id}')`);


        // Delete button
        const deleteButton = document.createElement("button");
        deleteButton.setAttribute("type", "button");
        deleteButton.setAttribute("id", `delete-button-${id}`);
        deleteButton.onclick = function () {
            deleteShape(id);
        };
        deleteButton.classList.add( "px-2", "inline-flex", "items-center", "min-w-fit", "rounded-e-md", "border", "border-gray-200");
        deleteButton.innerHTML = `<svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ff0000" d="M32 464a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128H32zm272-256a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zM432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16z"/></svg>`;

        // Input field inside collapsible
        const textareaDescription = document.createElement("textarea");
        textareaDescription.setAttribute("type", "text");
        textareaDescription.setAttribute("id",`textareaDescription-${id}`);
        textareaDescription.setAttribute("placeholder", "Enter description");
        textareaDescription.classList.add("py-1.5", "sm:py-2", "px-3", "pe-11", "block", "w-full", "border-gray-200", "border", "border-e-0", "sm:text-sm", "focus:z-10", "focus:border-blue-500", "focus:ring-blue-500");
        textareaDescription.setAttribute("onchange", `onUpdateShapeDetails('${id}')`);
        

        // // Collapse content container
        // const collapseContent = document.createElement("div");
        // collapseContent.classList.add("max-w-sm", "space-y-3", "hs-collapse");
        // collapseContent.setAttribute("id", `hs-basic-collapse-heading-${id}`);
        // collapseContent.setAttribute("aria-labelledby", `hs-basic-collapse-${id}`);


        // Inner grid container
        const gridContainer = document.createElement("div");
        gridContainer.classList.add("grid", "grid-cols-12", "rounded-lg");
        

        // Stroke color
        const strokeDiv = document.createElement("div");
        strokeDiv.classList.add("col-span-12", "sm:col-span-6", "p-2", "inline-flex", "items-center", "justify-between", "min-w-fit", "rounded-md", "border", "border-gray-200", "bg-gray-50", "text-xs", "text-gray-500");
        strokeDiv.innerHTML = `Stroke: `;

        const strokeInput = document.createElement("input");
        strokeInput.setAttribute("type", "color");
        strokeInput.setAttribute("class", `max-w-10 p-1 border-none rounded-md`);
        strokeInput.setAttribute("id", `fill-${id}`); 
        strokeInput.setAttribute("value", defaultStroke); 
        strokeInput.setAttribute("onchange", `updateShapeColor('${id}', this.value, 'stroke')`);
        strokeDiv.appendChild(strokeInput);

        // Fill color
        const fillDiv = document.createElement("div");
        fillDiv.classList.add("col-span-12", "sm:col-span-6", "p-2", "inline-flex", "items-center", "justify-between", "min-w-fit", "rounded-md", "border", "border-gray-200", "bg-gray-50", "text-xs", "text-gray-500");
        fillDiv.innerHTML = `Fill: `;

        const fillInput = document.createElement("input");
        fillInput.setAttribute("type", "color");
        fillInput.setAttribute("class", `max-w-10 p-1 border-none rounded-md`);
        fillInput.setAttribute("id", `fill-${id}`);
        fillInput.setAttribute("value", defaultFill); 
        fillInput.setAttribute("onchange", `updateShapeColor('${id}', this.value, 'fill')`);
        fillDiv.appendChild(fillInput);


        // Append elements to the main item
        // item.appendChild(title);
        // item.appendChild(titleInput);
        // item.appendChild(descInput);
        // item.appendChild(strokeLabel);
        // item.appendChild(strokeInput);
        // item.appendChild(fillLabel);
        // item.appendChild(fillInput);
        item.appendChild(areaLabel);
        // item.appendChild(deleteButton);

        // Append collapsible section
        container.appendChild(locateButton);
        container.appendChild(titleInput);
        container.appendChild(deleteButton);

        

       
        item.appendChild(container);
        item.appendChild(textareaDescription);

        item.appendChild(gridContainer);

        if (type !== "Marker") {
            gridContainer.appendChild(strokeDiv);
        }
        
        gridContainer.appendChild(fillDiv);

        
        // item.appendChild(collapseContent);

        
        list.appendChild(item);
    }

    function highlightObject(id) {
        // 1️⃣ Reset all sidebar items
        document.querySelectorAll(".sidebar-item").forEach(item => {
            item.classList.remove("border-blue-500");
            item.classList.add("border-gray-200"); 


        });

        
        // 3️⃣ Find the selected object and apply highlight
        let sidebarItem = document.getElementById(id);
        if (sidebarItem) {
            sidebarItem.classList.add("border-blue-500");
        }

        
    }



    function updateArea(id, newArea) {
        document.getElementById(`area-${id}`).innerText = `${newArea} sqm`;
    }

    function computeRectangleArea(rectangle) {
        let bounds = rectangle.getBounds();
        let ne = bounds.getNorthEast();
        let sw = bounds.getSouthWest();

        // Create a rectangle path to compute area
        let path = [
            ne, // Top-right
            { lat: ne.lat(), lng: sw.lng() }, // Top-left
            sw, // Bottom-left
            { lat: sw.lat(), lng: ne.lng() }, // Bottom-right
            ne  // Close the loop
        ];

        return google.maps.geometry.spherical.computeArea(path);
    }

    function updateShapeColor(id, color, type) {
        let obj = polygons.find(p => p.id === id) || 
                circles.find(c => c.id === id) || 
                rectangles.find(r => r.id === id) ||
                markers.find(m => m.id === id); // Include markers

        if (obj) {
            if (type === "stroke") {
                obj.polygon?.setOptions({ strokeColor: color });
                obj.circle?.setOptions({ strokeColor: color });
                obj.rectangle?.setOptions({ strokeColor: color });

                 

            } else if (type === "fill") {
                obj.polygon?.setOptions({ fillColor: color });
                obj.circle?.setOptions({ fillColor: color });
                obj.rectangle?.setOptions({ fillColor: color });

                // Handle marker fill color
                if (obj.marker) {
                    const encodedSVG = encodeURIComponent(`
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="48px" height="48px">
                            <path fill="${color}" d="M24,4C15.2,4,8,11.2,8,20c0,9.2,13.6,22.8,14.2,23.4c0.5,0.5,1.2,0.5,1.7,0C26.4,42.8,40,29.2,40,20 C40,11.2,32.8,4,24,4z"></path>
                            <circle fill="#FFFFFF" cx="24" cy="20" r="6"></circle>
                        </svg>`);

                    obj.marker.setIcon({
                        url: `data:image/svg+xml;charset=UTF-8,${encodedSVG}`,
                        scaledSize: new google.maps.Size(40, 40),
                    });
                }
            }
        }
    }



    function onUpdateShapeDetails(id) {
        let obj = markers.find(m => m.id === id) || 
                polygons.find(p => p.id === id) || 
                circles.find(c => c.id === id) || 
                rectangles.find(r => r.id === id);

        if (obj) {
            // Get updated title and description
            const updatedTitle = document.getElementById(`title-${id}`).value;
            const updatedDescription = document.getElementById(`textareaDescription-${id}`).value;

            // Update object properties
            obj.title = updatedTitle;
            obj.description = updatedDescription;

            // ✅ If it's a marker, update title and create a new InfoWindow
            if (obj.marker) {
                obj.marker.setTitle(updatedTitle); // Update marker title
                
                // ✅ Close and remove the old InfoWindow
                if (obj.infowindow) {
                    obj.infowindow.close();
                }

                // ✅ Create a completely new InfoWindow
                const newInfoWindow = new google.maps.InfoWindow({
                    content: `<h1>${updatedTitle}</h1><p>${updatedDescription}</p>`,
                    ariaLabel: updatedTitle,
                });

                obj.infowindow = newInfoWindow; // Save new InfoWindow
                
                // ✅ Ensure clicking the marker opens the updated InfoWindow
                google.maps.event.clearListeners(obj.marker, "click"); // Remove old click event
                obj.marker.addListener("click", () => {
                    obj.infowindow.open({
                        anchor: obj.marker,
                        map: map,
                    });

                    highlightObject(id);

                });
            }

            // ✅ If it's a shape (polygon, circle, rectangle), update label
            if (obj.polygon || obj.circle || obj.rectangle) {
                // ✅ Close and remove the old InfoWindow
                if (obj.infowindow) {
                    obj.infowindow.close();
                }


                // ✅ Create a completely new InfoWindow
                const newInfoWindow = new google.maps.InfoWindow({
                    content: `<h1>${updatedTitle}</h1><p>${updatedDescription}</p>`,
                    ariaLabel: updatedTitle,
                });

                obj.infowindow = newInfoWindow; // Save the new InfoWindow
                
                // ✅ Remove old click event before adding a new one
                google.maps.event.clearListeners(obj.polygon || obj.circle || obj.rectangle, "click");


                if (obj.label) { 
                    obj.label.setMap(null); // Remove old label
                }

                obj.label = new google.maps.InfoWindow({
                    content: `<h1>${updatedTitle}</h1><p>${updatedDescription}</p>`,
                });

                google.maps.event.addListener(obj.polygon || obj.circle || obj.rectangle, "click", function(event) {
                    obj.label.setPosition(event.latLng);
                    obj.label.open(map);
                    highlightObject(id);
                });
            }



            // // ✅ If it's a polygon, circle, or rectangle, update label
            // if (obj.polygon || obj.circle || obj.rectangle) {
            //     // ✅ Close and remove the old InfoWindow
            //     if (obj.infowindow) {
            //         obj.infowindow.close();
            //     }

            //     // ✅ Create a completely new InfoWindow
            //     const newInfoWindow = new google.maps.InfoWindow({
            //         content: `<h1>${updatedTitle}</h1><p>${updatedDescription}</p>`,
            //     });

            //     obj.infowindow = newInfoWindow; // Save the new InfoWindow
                
            //     // ✅ Remove old click event before adding a new one
            //     google.maps.event.clearListeners(obj.polygon || obj.circle || obj.rectangle, "click");

            //     // ✅ Reattach the updated click event for the polygon
            //     obj.polygon.addListener("click", function(event) {
            //         obj.infowindow.setPosition(event.latLng || obj.polygon.getPath().getAt(0)); // Show at clicked point
            //         obj.infowindow.open(map);
            //     });
            // }



        }
    }




    function deleteShape(id) {
        // Check and remove from polygons
        let polyIndex = polygons.findIndex(p => p.id === id);
        if (polyIndex !== -1) {
            polygons[polyIndex].polygon.setMap(null);
            polygons.splice(polyIndex, 1);
            document.getElementById(id)?.remove();
            return;
        }

        // Check and remove from markers
        let markerIndex = markers.findIndex(m => m.id === id);
        if (markerIndex !== -1) {
            markers[markerIndex].marker.setMap(null);
            markers.splice(markerIndex, 1);
            document.getElementById(id)?.remove();
            return;
        }

        // Check and remove from circles
        let circleIndex = circles.findIndex(c => c.id === id);
        if (circleIndex !== -1) {
            circles[circleIndex].circle.setMap(null);
            circles.splice(circleIndex, 1);
            document.getElementById(id)?.remove();
            return;
        }

        // Check and remove from rectangles
        let rectIndex = rectangles.findIndex(r => r.id === id);
        if (rectIndex !== -1) {
            rectangles[rectIndex].rectangle.setMap(null);
            rectangles.splice(rectIndex, 1);
            document.getElementById(id)?.remove();
            return;
        }
    }



    window.onload = initMap;
</script>




<form class="col-span-12  sm:col-span-10" wire:submit="save">
<!-- Card -->
<div class="bg-white rounded-xl shadow dark:bg-neutral-900">


    <div class="  p-4">

        <div class="sm:col-span-12">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
            Submit new project
            </h2>
        </div>
        <!-- End Col -->

        <div class="grid grid-cols-12 gap-x-2  ">

            <!-- Map -->
            <div class="space-y-2 col-span-12 sm:col-span-8 ">


                <h2>Search and Save Location</h2>
                
                <input
                autofocus autocomplete="location"
                wire:model.live="location"
                  placeholder="Search location"
                id="search-box" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">
                

                <input type="hidden" id="latitude" wire:model.live="latitude">
                <input type="hidden" id="longitude" wire:model.live="longitude">



                <div>
                    
                    <div id="map" style="height: 500px; width: 100%;" wire:ignore></div>
                {{-- <button wire:click="saveLocation">Save Location</button> --}}
                </div>


                <div>
                    @error('location')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror

                    @error('latitude')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror

                    @error('longitude')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <!-- ./ Map -->

           
            <div class="space-y-1 col-span-12 sm:col-span-4  ">
               
                 <!-- Added Items -->
                <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                    Added Items
                </h2>



                <div id="list" class="overflow-y-auto max-h-[550px] ">
                    {{-- <div>

                    
                        <div class="flex rounded-lg">

                        
                            <button type="button" id="hs-basic-collapse" aria-expanded="false" aria-controls="hs-basic-collapse-heading" data-hs-collapse="#hs-basic-collapse-heading"
                            class="hs-collapse-toggle px-2 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200 ">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg>
    
                            </button>
                            

                            <input type="text" class="py-1.5 sm:py-2 px-3 pe-11 block w-full border-gray-200 border border-e-0  sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">

                            <span class="px-2 inline-flex items-center rounded-e-lg min-w-fit  border  border-gray-200 ">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M32 464a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128H32zm272-256a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zM432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16z"/></svg>
                            </span>
                        </div>


                        <div class="max-w-sm space-y-3 hs-collapse hidden " id="hs-basic-collapse-heading" aria-labelledby="hs-basic-collapse">
                            <div>
    
                                <div class="grid grid-cols-12 rounded-lg"> 
                                
                                    <div class="col-span-12 sm:col-span-6 p-2 inline-flex items-center justify-between min-w-fit rounded-md border  border-gray-200 bg-gray-50 text-xs text-gray-500   ">
                                        Stroke:
                                        <input type="color" class=" max-w-10 p-1 border-none rounded-md">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6 p-2 inline-flex items-center justify-between min-w-fit rounded-md border  border-gray-200 bg-gray-50 text-xs text-gray-500 ">
                                        Backgound:
                                        <input type="color" class=" max-w-10 p-1 border-none rounded-md">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6 p-2 inline-flex items-center justify-between min-w-fit rounded-md border  border-gray-200 bg-gray-50 text-xs text-gray-500 ">
                                        Width:
                                        <input type="number" class=" max-w-10 p-1 w-full text-xs text-gray-500 border-none rounded-md" value="0">
                                    </div>
        
                                    <div class="col-span-12 sm:col-span-6 p-2 inline-flex items-center justify-between min-w-fit rounded-md border  border-gray-200 bg-gray-50 text-xs text-gray-500 ">
                                        Opacity:
                                        <input type="number" class=" max-w-10 p-1 w-full text-xs text-gray-500 border-none rounded-md" value="0">
                                    </div>
                                
                                
                                </div>
    
                                <div>
                                    
                                    <input type="text" placeholder="Title" class="py-2 px-3 pe-11 block w-full border-gray-200 rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <input type="text" placeholder="Description" class="py-2 px-3 pe-11 block w-full border-gray-200 rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                            </div>
    
                        </div>
                    </div> --}}


                </div>


                
            </div>

            <!-- ./ Added Items -->



        </div>



        



        

    </div>
</div>


</div>
