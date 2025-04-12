<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto grid grid-cols-12 gap-x-2">
      
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&libraries=places,drawing,geometry,marker&loading=async&callback=initMap" async defer></script>
 
    
    <script>
        let map;
        let drawingManager;
        let polygons = [];
        // markers = [], 
        let markers = @json($markers);
        let circles = [], rectangles = [];
        let markerCounter = 0, polygonCounter = 1, circleCounter = 1, rectangleCounter = 1;
 

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
                
                // Call Livewire method to sync with the backend
                window.Livewire.dispatch('addMarker');

                let lastIndex = @this.get('marker_count'); // Get the Livewire markers array 

                let markerId = `marker-${lastIndex}`;


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

                // addToSidebar(markerId, "Marker", "N/A");

                


                console.log("Marker Array:", markers);


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

            

            // Area Display
            const areaLabel = document.createElement("span");
            areaLabel.setAttribute("id", `area-${id}`);
            areaLabel.classList.add(  "px-2", "block", "text-xs", "text-gray-500");
            areaLabel.textContent = `Area: ${area} sqm`;

           
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
            console.log("Markers:", markers);
            console.log("Raw ID:", id);


            if (obj) {
                // Get updated title and description
                const updatedTitle = document.getElementById(`title-${id}`).value;

                console.log("Updated title:", updatedTitle);

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




        // function deleteShape(id) {
        //     // Check and remove from polygons
        //     let polyIndex = polygons.findIndex(p => p.id === id);
        //     if (polyIndex !== -1) {
        //         polygons[polyIndex].polygon.setMap(null);
        //         polygons.splice(polyIndex, 1);
        //         document.getElementById(id)?.remove();
        //         return;
        //     }

        //     // Check and remove from markers
        //     let markerIndex = markers.findIndex(m => m.id === id);
        //     if (markerIndex !== -1) {
        //         markers[markerIndex].marker.setMap(null);
        //         markers.splice(markerIndex, 1);
        //         document.getElementById(id)?.remove();

        //          // Call Livewire to remove from backend
        //         window.Livewire.dispatch('removeMarker', index);


        //         return;
        //     }

        //     // Check and remove from circles
        //     let circleIndex = circles.findIndex(c => c.id === id);
        //     if (circleIndex !== -1) {
        //         circles[circleIndex].circle.setMap(null);
        //         circles.splice(circleIndex, 1);
        //         document.getElementById(id)?.remove();
        //         return;
        //     }

        //     // Check and remove from rectangles
        //     let rectIndex = rectangles.findIndex(r => r.id === id);
        //     if (rectIndex !== -1) {
        //         rectangles[rectIndex].rectangle.setMap(null);
        //         rectangles.splice(rectIndex, 1);
        //         document.getElementById(id)?.remove();
        //         return;
        //     }
        // }


        function deleteShape(id) {
            const shapeCollections = {
                polygons: polygons,
                markers: markers,
                circles: circles,
                rectangles: rectangles
            };

            for (let shapeType in shapeCollections) {
                let index = shapeCollections[shapeType].findIndex(s => s.id === id);
                if (index !== -1) {
                    // Remove from the map
                    shapeCollections[shapeType][index][shapeType.slice(0, -1)].setMap(null);
                    
                    // Remove from the array
                    shapeCollections[shapeType].splice(index, 1);
                    
                    // Remove from DOM
                    document.getElementById(id)?.remove();

                    // If deleting a marker, notify Livewire with the actual index
                    if (shapeType === 'markers') {
                            window.Livewire.dispatch('removeMarker', index);
                    }

                    return;
                }
            }
        }


        document.addEventListener("livewire:load", function () {
            window.Livewire.on("markerRemoved", (index) => {
                removeMarkerFromMap(index);
            });
        });

        function removeMarkerFromMap(index) {
            let markerId = `marker-${index}`;
            let markerIndex = markers.findIndex(m => m.id === markerId);

            if (markerIndex !== -1) {
                markers[markerIndex].marker.setMap(null); // Remove from map
                markers.splice(markerIndex, 1); // Remove from array
            }

            console.log("Updated Marker Array:", markers);
        }

  
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('updateMarkerTitle&Description', (data) => {
                console.log("Marker title update received:", data);

                // if (markers[key]) {
                //     markers[key].setTitle(title); // Assuming `markers` is your array of Google Maps markers
                // }
                
                // onUpdateShapeDetails(key); // Call function to update the map objects

                 // Extract the key from the object
                const key = data.key;
                console.log("Extracted key:", key);

                // Now you can use `key` to access the marker
                let obj = markers[key];  

                // Get updated title and description
                const updatedTitle = document.getElementById(`title-marker-${key}`).value;
                console.log("Updated Title:", updatedTitle);
                     

                const updatedDescription = document.getElementById(`textareaDescription-marker-${key}`).value;

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

                        // set the div id 
                        let id = `marker-${key}`;
                        highlightObject(id);

                    });
                }





            });
        });



        window.onload = initMap;
    </script>

     


    <form class="col-span-12  sm:col-span-10" wire:submit="save">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow ">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
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
                        id="search-box" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="">
                        

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
                        <h2 class="text-lg font-semibold text-gray-800 ">
                            Added Items
                        </h2>



                        <div id="list" class="overflow-y-auto max-h-[550px] ">

                            @foreach($markers as $index => $marker)
                                <div id="marker-{{ $index }}" class="sidebar-item bg-gray-50 border border-gray-200 rounded-lg p-2 space-y-2 mb-2">
                                    <span id="area-{{ $index }}" class="px-2 block text-xs text-gray-500">Area: {{ $marker['area'] ?? '0' }} sqm</span>
                                
                                    <div class="flex rounded-lg">
                                        <button type="button" id="collapse-{{ $index }}" aria-controls="collapse-heading-{{ $index }}"
                                            class="hs-collapse-toggle px-2 inline-flex items-center min-w-fit rounded-s-md border border-e-0 border-gray-200">
                                            
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#63E6BE" d="M352.2 425.8l-79.2 79.2c-9.4 9.4-24.6 9.4-33.9 0l-79.2-79.2c-15.1-15.1-4.4-41 17-41h51.2L228 284H127.2v51.2c0 21.4-25.9 32.1-41 17L7 272.9c-9.4-9.4-9.4-24.6 0-33.9L86.2 159.8c15.1-15.1 41-4.4 41 17V228H228V127.2h-51.2c-21.4 0-32.1-25.9-17-41l79.2-79.2c9.4-9.4 24.6-9.4 33.9 0l79.2 79.2c15.1 15.1 4.4 41-17 41h-51.2V228h100.8v-51.2c0-21.4 25.9-32.1 41-17l79.2 79.2c9.4 9.4 9.4 24.6 0 33.9L425.8 352.2c-15.1 15.1-41 4.4-41-17V284H284v100.8h51.2c21.4 0 32.1 25.9 17 41z"/></svg>
                                        </button>
                                
                                        <input type="text" id="title-marker-{{ $index }}" placeholder="Enter title"
                                            class="py-1.5 px-3 block w-full border-gray-200"
                                            wire:model.live="markers.{{ $index }}.title"
                                            {{-- wire:change="updateMarkerTitle({{ $index }})" --}}
                                            
                                            >
                                
                                        <button type="button" id="delete-{{ $index }}" 
                                            wire:click="removeMarker({{ $index }})" 
                                            onclick="removeMarkerFromMap({{ $index }})"
                                            class="px-2 inline-flex items-center rounded-e-md border border-gray-200">
                                            {{ $index }}

                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ff0000" d="M32 464a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128H32zm272-256a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zm-96 0a16 16 0 0 1 32 0v224a16 16 0 0 1 -32 0zM432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16z"/></svg>
                                        </button>
                                    </div>
                                
                                    <textarea id="textareaDescription-marker-{{ $index }}" placeholder="Enter description"
                                        class="py-1.5 px-3 block w-full border-gray-200"
                                        wire:model.live="markers.{{ $index }}.description"  
                                        ></textarea>
                                
                                    <div class="grid grid-cols-12 rounded-lg">
                                        <div class="col-span-6 p-2 inline-flex items-center justify-between border border-gray-200 bg-gray-50 text-xs">
                                            Stroke:
                                            <input type="color" id="stroke-{{ $index }}" class="max-w-10 p-1 border-none rounded-md"
                                                wire:model.defer="markers.{{ $index }}.stroke">
                                        </div>
                                        <div class="col-span-6 p-2 inline-flex items-center justify-between border border-gray-200 bg-gray-50 text-xs">
                                            Fill:
                                            <input type="color" id="fill-{{ $index }}" class="max-w-10 p-1 border-none rounded-md"
                                                wire:model.defer="markers.{{ $index }}.fill">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            

                        </div>

    
                        
                    </div>
    
                    <!-- ./ Added Items -->
    


                </div>



                
                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">

                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Name
                        </label>

                        <input
                        autofocus autocomplete="name"
                        wire:model="name"
                        id="name" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="">

                        @error('name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="federal_agency" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Company
                        </label>

                        <input
                        autofocus autocomplete="federal_agency"
                        wire:model="federal_agency"
                        id="federal_agency" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="">

                        @error('federal_agency')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>


                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Type
                        </label>


                        <select
                        autocomplete="type"
                        wire:model="type"
                        id="type"
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                            <option selected="">Select type</option>

                            @if(!empty($project_types))
                                @foreach ($project_types as $type_id => $type_name )
                                    <option>{{ $type_name }}</option> 
                                @endforeach 
                            @endif
                        </select>

                        {{-- <input
                        autofocus autocomplete="type"
                        wire:model="type"
                        id="type" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder=""> --}}

                        @error('type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>






                    <div class="space-y-2 col-span-12   ">
                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Description
                        </label>

                        <textarea
                        autofocus autocomplete="description"
                        wire:model="description"
                        id="description"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder=""></textarea>

                        @error('description')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>
                    
                    {{-- 
                    <div class="space-y-2 col-span-12     ">
                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Attachments
                        </label>

                        <livewire:dropzone
                            wire:model="attachments"
                            :rules="['file', 'mimes:png,jpeg,jpg,pdf,docx,xlsx,csv,txt,zip', 'max:20480']"
                            :multiple="true" />


                        @error('attachments')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror 

                    </div>
                    --}}

                    

                    <!-- Dynamic Project Documents Section -->
                    <div class="space-y-2 col-span-12  mt-5">
                        @foreach($projectDocuments as $index => $document)
                            <div class="border border-black p-2 rounded-md mb-4">
                                <label class="inline-block text-sm font-medium text-gray-800 ">
                                    Submission Type
                                </label>
                                <select
                                    class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                    wire:model.live="projectDocuments.{{ $index }}.document_type_id"
                                    
                                    >
                                    <option value="">Select Submission Type</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach

                                    


                                </select>

                                @error("projectDocuments.".$index.".document_type_id")
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror

                                <label class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                    Document Attachments ( Only PNG, JPEG, JPG, PDF, DOCX, XLSX, CSV, TXT, and ZIP files are allowed. )
                                </label>
                                <input class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none   file:bg-gray-50 file:border-0
                                file:me-4
                                file:py-3 file:px-4
                                "
                                type="file" wire:model.live="projectDocuments.{{ $index }}.attachments" multiple>

                                @error("projectDocuments.".$index.".attachments")
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror

                                

                                <!-- Show Selected Files Before Upload -->
                                <div class="my-2">
                                    @if(isset($projectDocuments[$index]['attachments']) && count($projectDocuments[$index]['attachments']) > 0) 
                                        <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                            Selected Files: (Choose files again to change the uploaded list)
                                        </p>
                                        @foreach($projectDocuments[$index]['attachments'] as $file)
                                            <div class="w-full mb-2 ">
                                                <span class="block py-3 px-4 text-sm font-medium bg-white border border-gray-200 text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg  ">{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                        @endforeach



                                    @endif
                                </div>

                                <!-- Remove Button -->
                                {{-- <button type="button" wire:click="removeProjectDocument({{ $index }})" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Remove
                                </button> --}}
                                
                            </div>
                        @endforeach
                        {{-- <div class="mb-2">
                            <button class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" type="button" wire:click="addProjectDocument">+ Add Document</button>
                        </div> --}}

                    </div>  

                    

                    


                </div> 


                <!-- section visible to admin only -->
                {{-- @if( Auth::user()->hasRole('DSI God Admin') && Auth::user()->hasPermissionTo('timer edit') )  --}}
                <!-- Timer -->
                <div class="grid grid-cols-12 gap-x-2  
                {{ ( Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasRole('Reviewer') )  ? 'block' : 'hidden'}}
                ">

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Submitter duration
                        </label>

                        <input
                        min="1"
                        autofocus autocomplete="submitter_response_duration"
                        wire:model.live="submitter_response_duration"
                        
                        id="submitter_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="">

                        @error('submitter_response_duration')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="submitter_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Submitter duration type
                        </label>

                        <select 
                        
                        autofocus autocomplete="submitter_response_duration_type"
                        wire:model.live="submitter_response_duration_type"
                        id="submitter_response_duration_type" 
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                            <option selected="">Select type</option>
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                        </select>

                        @error('submitter_response_duration_type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                        <label for="submitter_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Submitter response due date
                        </label>

                        <input readonly 
                        {{-- autofocus autocomplete="submitter_due_date"
                        wire:model.live="submitter_due_date" --}}
                        value="{{ \Carbon\Carbon::parse($submitter_due_date)->format('d M, h:i A') }}"
                        id="submitter_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="">

                        @error('submitter_due_date')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                </div>

                <!-- Recieved time or response is determined by the submitted -->
                <div class="grid grid-cols-12 gap-x-2  
                {{ ( Auth::user()->hasRole('DSI God Admin')  || Auth::user()->hasRole('Reviewer') 

                )  ? 'block' : 'hidden'
                
                
                }}
                ">

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="reviewer_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Reviewer duration
                        </label>

                        <input
                        min="1"
                        autofocus autocomplete="reviewer_response_duration"
                        wire:model.live="reviewer_response_duration"
                        id="reviewer_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="">

                        @error('reviewer_response_duration')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="reviewer_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Reviewer duration type
                        </label>

                        <select 
                        autofocus autocomplete="reviewer_response_duration_type"
                        wire:model.live="reviewer_response_duration_type"
                        id="reviewer_response_duration_type" 
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                            <option selected="">Select type</option>
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                        </select>

                        @error('reviewer_response_duration_type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>


                    


                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                        <label for="reviewer_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Reviewer response due date
                        </label>

                        <input readonly 
                        {{-- autocomplete="reviewer_due_date"
                        wire:model.live="reviewer_due_date" --}}    
                        value="{{ \Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') }}"
                        id="reviewer_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="">

                        @error('reviewer_due_date')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    
                    
                    @if(!empty($project_timer))
                    <p class="text-sm text-gray-500 mt-2 col-span-12"> 
                        Updated {{  \Carbon\Carbon::parse($project_timer->updated_at)->format('d M, h:i A') }} by {{ $project_timer->updator ? $project_timer->updator->name : '' }}
                                            
                    </p>

                    @else
                    <p class="text-sm text-gray-500 mt-2 col-span-12"> 
                        Project timers are set to default and haven't been updated.                   
                    </p>
                    @endif


                </div>
                <!-- End Timer -->
                {{-- @endif --}}


                <!-- -->

                @if(Auth::user()->hasRole('User')) 
                <!-- End Grid -->
                <p class="text-sm text-gray-600 mt-2">{{ !empty($reviewer_due_date) ? 'Expect to get a review at '.\Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') : '' }}</p>
                @endif

                @if ($errors->any())
                        
                    @foreach ($errors->all() as $error) 


                        <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4 " role="alert" tabindex="-1" aria-labelledby="hs-soft-color-danger-label">
                            <span id="hs-soft-color-danger-label" class="font-bold">Error: </span>
                            {{ $error }}
                        </div>


                    @endforeach 
                @endif 


                <div class="mt-5 flex justify-center gap-x-2">
                    <a href="{{ route('project.index') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a>
                    <button type="submit" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Save Draft
                    </button>

                    <button type="button" wire:click="addMarker(1)" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Add Marker
                    </button>

                    @if( Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project submit') )
                        <button  type="button"
                            onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"
                            wire:click.prevent="submit_project()"
                            
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-sky-600 text-white hover:bg-sky-700 focus:outline-none focus:bg-sky-700 disabled:opacity-50 disabled:pointer-events-none">
                            Submit
                        </button> 
                    @endif
                </div>


                

            </div>
        </div>
 
    </form>
    <aside class="col-span-12 md:col-span-2 mt-2 md:mt-0">
        <div class="bg-white rounded-xl shadow  ">
            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                        Project Subscribers  
                    </h2>
                    <p class="text-gray-500 text-xs">Users that will be notified on project updates</p>
                </div> 

                <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                    Search for User Name
                </label>

                <input type="text" wire:model.live="query" placeholder="Type to search..." class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                
                <!-- Search Results Dropdown -->
                @if(!empty($users))
                    <ul class="border rounded mt-2 bg-white w-full max-h-48 overflow-auto">
                        @foreach($users as $user)
                            <li wire:click="addSubscriber({{ $user->id }})" class="p-2 cursor-pointer hover:bg-gray-200">
                                {{ $user->name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            
                <!-- Selected Subscribers -->
                <div class="mt-4">
                    <h3 class="font-bold">Selected Subscribers:</h3>
                    
                    @if(!empty($selectedUsers))
                        <ul>
                            @foreach($selectedUsers as $index => $user)
                                <li class="flex items-center justify-between bg-gray-100 p-2 rounded mt-1 text-truncate">
                                    <span>{{ $user['name'] }}</span>
                                    <button wire:click="removeSubscriber({{ $index }})" class="text-red-500">❌</button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">No subscribers selected.</p>
                    @endif
                </div>

            </div>

        </div>
    </aside>




</div>
