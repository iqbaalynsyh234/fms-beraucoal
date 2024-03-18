<head>
    <meta charset="UTF-8">
    <!-- Bootstrap 4 CSS -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://openlayers.org/api/OpenLayers.js"></script>

</head>

<body>

    <style>

        .modal-content {
            background-color: #f8f9fa;  /* Warna latar belakang modal */
            width: 100%;
        }
        .modal-backdrop.show{
            opacity: 0;
        }

        .panel-heading.panel-heading-green {
            background-color: #4bb036;
        }

        fieldset {
            margin-top: 20px; 
        }

        .sidebar-container { 
            width: 100%;
            max-width: 300px;
        }

        .page-content-wrapper {
            width: 100%;
            box-sizing: border-box;
            margin-top: 20px;
            padding: 1rem;
            background-color: white;

        }

        #map-canvas {
            width: 100%;
            height: 750px;
            margin-top: 0px;
        }

        @media (max-width: 768px) {
            .page-content-wrapper {
                width: 100%;
            }
        }
    </style>

    <script>
        function fetchDataAndShowData() { // show data
            $.ajax({
                url: '<?= base_url() ?>geofancedrawing/label', // Update the URL to the actual endpoint
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Assuming the response contains the geofence data in a property called 'geofence'
                        var geofenceData = response.geofence;

                        // Clear existing features
                        vectors.removeAllFeatures();

                        // Deserialize and add new features
                        for (var i = 0; i < geofenceData.length; i++) {
                            var style_green = {
                                strokeColor: "#f49440",
                                strokeOpacity: 0.6,
                                strokeWidth: 2,
                                fillColor: "#f6c79a",
                                fillOpacity: 0.4,
                                fontSize: '12px',
                                label: geofenceData[i].geofence_name
                            };
                            vectors.style = style_green;
                            deserialize(geofenceData[i].geofence_json);
                        }
                    } else {
                        alert('Failed to fetch geofence data.');
                    }
                },
                error: function() {
                    alert('Error fetching geofence data.');
                }
            });
        }
    </script>

    <script>
        function showDataPopup() {
            $.ajax({
                url: '<?= base_url() ?>geofence/label', // Update the URL to the actual endpoint
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Assuming the response contains the geofence data in a property called 'geofence'
                        var geofenceData = response.geofence;

                        // Build HTML content for the popup
                        var popupContent = '<h3>Geofence List</h3>';
                        for (var i = 0; i < geofenceData.length; i++) {
                            popupContent += '<p>' + geofenceData[i].geofence_name + '</p>';
                        }

                        // Show the popup
                        showPopup(popupContent);
                    } else {
                        alert('Failed to fetch geofence data.');
                    }
                },
                error: function() {
                    alert('Error fetching geofence data.');
                }
            });
        }

        function showPopup(content) {
            alert(content);
        }
    </script>


    <script>
        jQuery(document).ready(function() {
            showclock();

            <?php for ($i = 0; $i < count($geofence); $i++) { ?>
                var style_green = {
                    strokeColor: "#f49440",
                    strokeOpacity: 0.6,
                    strokeWidth: 2,
                    fillColor: "#f6c79a",
                    fillOpacity: 0.4,
                    fontSize: '12px',
                    label: '<?php echo $geofence[$i]->geofence_name; ?>'
                };
                vectors.style = style_green;
                deserialize('<?php echo $geofence[$i]->geofence_json; ?>');
            <?php } ?>

            var style_last = {
                strokeColor: "#f49440",
                strokeOpacity: 0.6,
                strokeWidth: 2,
                fillColor: "#f6c79a",
                fillOpacity: 0.4,
                fontSize: '12px'
            };
            vectors.style = style_last;

            $(".olControlDrawFeaturePointItemInactive").html('<i class="fas fa-paint-brush"></i>');
            $(".olControlDrawFeaturePathItemInactive").html('<i class="fas fa-paint-brush"></i>');

            <?php if ($showlabel) { ?>
                showdata();
            <?php } ?>

            $("#btnlabel").click(function() {
                showdata();
            });
        });
    </script>
    
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJCuzzQJJ7QHwAc6QoCoQXO4X1qQr54f0&libraries=drawing"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Bootstrap JS with Popper.js (dependency for Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <div class="sidebar-container">
        <?= $sidebar; ?>
    </div>

    <div class="page-content-wrapper">
        <div class="page-content">
		<div class="panel" id="panel_form">

        <fieldset style="text-align: center;">
            <legend>
                <?= $this->lang->line("lmangeofence"); ?> '<?php echo $vehicle->vehicle_name ?> - <?php echo $vehicle->vehicle_no ?>'
            </legend>
        </fieldset>

        <fieldset>
            <legend>
            <?php echo "Coordinate" . " " . $this->lang->line("llocation"); ?>
            </legend>
            <input type="text" class="formdefault" value="" id="lokasi" name="lokasi" size="30" />
            <input class="button" type="button" value="<?php echo $this->lang->line("lcenter"); ?>" onclick="javascript: carilokasi()" />
        </fieldset>
          
        <fieldset>
                <legend>Control</legend>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                    Save
                </button>
                    <a href="<?=base_url();?>geofencedatalistlive" class="btn btn-flat" type="button" ><?php echo $this->lang->line("lgeofence_list"); ?> </a>
                    <input class="button" type="button" name="btncancel" id="btncancel" style="margin-bottom: 20px; background-color: red;" value=" <?php echo $this->lang->line("lreset"); ?> " onclick="location='<?= base_url() ?>geofancedrawing/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid(); ?>'; "/>
        </fieldset>
             <header class="panel-heading panel-heading-green" style="background-color: #4bb036;">Geofence Drawing Polygon Area</header>
                <div id="map-canvas" style="height: 600px; width: 700px margin-left: 300px; "></div>
                <div id="info" style="position:absolute; color:red; font-family: Arial; height:200px; font-size: 30px;"></div>
                <div id="hasilGambar"></div>
			</div>
        </div>
    </div>

   <!-- Elemen modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        <!-- Header modal -->
        <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel">List Geofance Name</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Body modal -->
        <div class="modal-body">
            <table class="table">
            <thead>
                <tr>
                <th scope="col">Geofence Name</th>
                <th scope="col">Koordinat</th>
                </tr>
            </thead>
            <tbody id="modalBody">
                <!-- Daftar poligon akan ditampilkan di sini -->
            </tbody>
            </table>
        </div>

        <!-- Footer modal button-->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" id="saveGeofenceNameButton" class="btn btn-primary" data-dismiss="modal">Save</button>
        </div>

        </div>
    </div>
    </div>

    </div>

</body>

<script>
        var map;
        var drawingManager;
        var selectedShape;
        var markers = [];
        var coordinates = [];
        var listCreatePolygon = [];
        var editedGeofenceIndex;
        
        function InitMap(koordinatArrayAll) {
        // console.log('koordinatArrayAll:', koordinatArrayAll);

        var map = new google.maps.Map(document.getElementById('map-canvas'), {

            center: {
                lat: koordinatArrayAll[0][0].lat,
                lng: koordinatArrayAll[0][0].lng
            },

            zoom: 18,

            panControl: true, // Aktifkan kontrol geser peta
                zoomControl: true,
                mapTypeControl: true,
                scaleControl: true,
                streetViewControl: true,
                overviewMapControl: true,
        });

        // Create a drawing manager
        drawingManager = new google.maps.drawing.DrawingManager({
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [
                    google.maps.drawing.OverlayType.POLYGON,
                ]
            },
            polygonOptions: {
                clickable: true,
                draggable: false,
                editable: true,
                fillColor: '#f6c79a',  // field color draw polygon 
                fillOpacity: 0.5,
                strokeColor: 'transparent',  // Make the border color transparent
                strokeOpacity: 0,  // Make the border fully transparent
            },

        });


        function clearSelection() {
            if (selectedShape) {
                selectedShape.setEditable(false);
                selectedShape = null;
            }
        }

        //to disable drawing tools
        function stopDrawing() {
            drawingManager.setMap(null);
        }

        function editGeofenceName(index) {
            editedGeofenceIndex = index;
            var geofenceName = "Polygon " + (index + 1);
            
            // Check if the element with id 'editGeofenceNameInput' exists
            var modalInput = document.getElementById('editGeofenceNameInput');
            
            if (modalInput) {
                modalInput.value = geofenceName;
            } else {
                console.error("Element with id 'editGeofenceNameInput' not found.");
            }
        }

        function clearMarkers() {
            markers.forEach(function (marker) {
                marker.setMap(null);
            });
            markers = [];
        }

        function setSelection(shape) {
            clearSelection();
            stopDrawing()
     
            selectedShape = shape;
            shape.setEditable(true);
        }


        function deleteSelectedShape() {
            if (selectedShape) {
                selectedShape.setMap(null);
                drawingManager.setMap(map);
                coordinates.splice(0, coordinates.length)
                document.getElementById('info').innerHTML = ""
            }
        }

        function CenterControl(controlDiv, map) {

            // Set CSS for the control border.
            var controlUI = document.createElement('div');
            controlUI.style.backgroundColor = '#fff';
            controlUI.style.border = '2px solid #fff';
            controlUI.style.borderRadius = '3px';
            controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
            controlUI.style.cursor = 'pointer';
            controlUI.style.marginBottom = '22px';
            controlUI.style.textAlign = 'center';
            controlUI.title = 'Select to delete the shape';
            controlDiv.appendChild(controlUI);

            // Set CSS for the control interior.
            var controlText = document.createElement('div');
            controlText.style.color = 'rgb(25,25,25)';
            controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
            controlText.style.fontSize = '16px';
            controlText.style.lineHeight = '38px';
            controlText.style.paddingLeft = '5px';
            controlText.style.paddingRight = '5px';
            controlText.innerHTML = 'Delete Selected Area';
            controlUI.appendChild(controlText);

            controlUI.addEventListener('click', function () {
                deleteSelectedShape();
                clearMarkers(); // Clear markers when deleting the polygon
            });

        }

        drawingManager.setMap(map);

        // document.getElementById('searchButton').addEventListener('click', function () {
        //     carilokasi(map); 
        // });

        google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
            if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                var polygon = event.overlay;
                var path = polygon.getPath();
                var onePolygon = [];

                for (var i = 0; i < path.getLength(); i++) {
                    var latLng = path.getAt(i);
                    var lat = latLng.lat();
                    var lng = latLng.lng();
                    console.log('Koordinat Titik ' + (i + 1) + ': ' + lat + ', ' + lng);
                    onePolygon.push(lat + "," + lng);
                }

                var result_string = onePolygon.join(' ');
                console.log(result_string)
                listCreatePolygon.push(result_string);
                console.log(listCreatePolygon);
                console.log(result_string);
                polygon.geofenceName = "Polygon " + (listCreatePolygon.length + 1);
                tampilkanListCreatePolygonUI();
            }
        });

       // Declare tampilkanListCreatePolygonUI globally
       function tampilkanListCreatePolygonUI() {
            var modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = '';

            for (var i = 0; i < listCreatePolygon.length; i++) {
                var geofenceName = "Polygon " + (i + 1);

                modalBody.innerHTML += '<tr>' +
                    '<th scope="row">' + (i + 1) + '</th>' +
                    '<td><input type="text" class="form-control" id="geofenceNameInput_' + i + '" value="' + geofenceName + '"></td>' +
                    '<td><button type="button" class="btn btn-danger" onclick="hapusData(' + i + ')">Delete</button></td>' +
                    '</tr>';

                modalBody.innerHTML += '<tr>' +
                    '<th scope="row"></th>' +
                    '<td>' + JSON.stringify(listCreatePolygon[i]) + '</td>' +
                    '</tr>';
            }

            var modalFooter = document.querySelector('.modal-footer');
            modalFooter.innerHTML = '<button type="button" class="btn btn-primary" onclick="simpanData(listCreatePolygon)">Save</button>' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>';
        }

        for (var i = 0; i < koordinatArrayAll.length; i++) {
            var paramCoordinates = koordinatArrayAll[i];

            var polygon = new google.maps.Polygon({
                paths: paramCoordinates,
                strokeColor: '#f6c79a',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#f6c79a',
                fillOpacity: 0.35,
                editable: false,
                map: map
            });

            // Calculate the center of the polygon
            var bounds = new google.maps.LatLngBounds();
            paramCoordinates.forEach(function (point) {
                bounds.extend(point);
            });
            var center = bounds.getCenter();

            var geofenceName = "Geofence " + (i + 1); 
            var marker = new google.maps.Marker({
                position: center,
                map: map,
                label: {
                    text: koordinatArrayAll[i][0].geofence_name,
                    color: 'black',
                    fontWeight: 'normal'
                },
                icon: {
                    url: 'https://maps.gstatic.com/mapfiles/transparent.png',
                    size: new google.maps.Size(1, 1),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(0, 32)
                }
            });
        }

    }

    // google.maps.event.addDomListener(window, 'load', initializeMap);
    // Function to parse coordinates from a string (e.g., "lat, lng")
    function parseLatLng(input) {
        var matches = input.match(/(-?\d+(\.\d+)?)\s*,\s*(-?\d+(\.\d+)?)/);
        if (matches && matches.length >= 5) {
            var lat = parseFloat(matches[1]);
            var lng = parseFloat(matches[3]);
            return new google.maps.LatLng(lat, lng);
        } else {
            return null;
        }
    }

    // Declare hapusData globally
    function hapusData(index) {
            listCreatePolygon = listCreatePolygon.filter(function (_, i) {
                return i !== index;
            });
            tampilkanListCreatePolygonUI(); // Update the UI
    }

 
    function simpanData(listCreatePolygon) {

    var polygon = JSON.parse(JSON.stringify(listCreatePolygon))
    // var coordinatesArray = polygon[0];
    var listGeofenceName = []
    for (var i = 0; i < polygon.length; i++) {
        var geofenceNameInput = document.getElementById(`geofenceNameInput_${i}`);
    var geofenceName = geofenceNameInput.value;
    listGeofenceName.push(geofenceName)
    console.log(listGeofenceName);

    }


    var postData = {
    geofenceName: listGeofenceName,
    listCreatePolygon: polygon
    };
    console.log(postData)

    // Melakukan request AJAX untuk menyimpan data
    $.post('<?= base_url() ?>geofancedrawing/save', postData, function(response) {
    try {
        var jsonResponse = JSON.parse(response);

        if (jsonResponse && jsonResponse.error === false) {
            console.log('Data saved successfully:', jsonResponse.message);
        } else {
            console.error('Error saving data:', jsonResponse ? jsonResponse.message : 'Invalid JSON response');
        }
    } catch (error) {
        console.error('Error parsing JSON response:', error);
    }
    })
    .fail(function(error) {
    console.error('Error in AJAX request:', error.responseText);
    });
    }

    function showdata() {
        
        var deviceid = '<?php echo $vehicle->vehicle_device; ?>';

        jQuery.post('<?php echo base_url(); ?>geofancedrawing/label', {
                deviceid: deviceid
            },
            function(r) {
                if (r.success) {
                    InitMap(r.koordinat_array_all);

                } else {

                    InitMap(r.koordinat_array_all);
                }
            },
            "json"
        );
    }
    showdata()   


</script>

<script>
    function carilokasi(map) {
        var lokasiInput = document.getElementById('lokasi').value;

        // Check if lokasiInput is empty or undefined
        if (!lokasiInput) {
            console.error("Invalid or empty input for location");
            return;
        }

        var coordinates = lokasiInput.split(',');

        // Check if there are enough coordinates
        if (coordinates.length < 2) {
            console.error("Invalid coordinates format");
            return;
        }

        var lat = parseFloat(coordinates[0].trim());
        var lng = parseFloat(coordinates[1].trim());

        // Check if the map is defined
        if (map) {
            var location = new google.maps.LatLng(lat, lng);

            // Clear existing markers
            clearMarkers();

            // Define a custom red marker icon
            var redMarkerIcon = {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                size: new google.maps.Size(32, 32),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(16, 32),
            };

            // Add a new marker with the custom icon for the specified location
            var marker = new google.maps.Marker({
                position: location,
                map: map,
                title: 'Selected Location',
                icon: redMarkerIcon, // Use the custom red marker icon
            });

            // Center the map on the specified location
            map.setCenter(location);

            // Add the marker to the markers array for future reference
            markers.push(marker);
        } else {
            console.error("Map object is not defined.");
        }
    }

</script>

<script>
    $(document).ready(function() {
        showclock();
        init();
        initMap();

        // Existing code for loading geofence data from the server
        <?php for ($i = 0; $i < count($geofence); $i++) { ?>
            var style_green = {
                strokeColor: "#f49440",
                strokeOpacity: 0.6,
                strokeWidth: 2,
                fillColor: "#f6c79a",
                fillOpacity: 0.4,
                fontSize: '12px',
                label: '<?php echo $geofence[$i]->geofence_name; ?>'
            };
            vectors.style = style_green;
            deserialize('<?php echo $geofence[$i]->geofence_json; ?>');
        <?php } ?>

        var style_last = {
            strokeColor: "#f49440",
            strokeOpacity: 0.6,
            strokeWidth: 2,
            fillColor: "#f6c79a",
            fillOpacity: 0.4,
            fontSize: '12px'
        };
        vectors.style = style_last;

        $(".olControlDrawFeaturePointItemInactive").html('<i class="fas fa-paint-brush"></i>');
        $(".olControlDrawFeaturePathItemInactive").html('<i class="fas fa-paint-brush"></i>');

        <?php if ($showlabel) { ?>
            showdata();
        <?php } ?>
    });

    $("#btnlabel").click(function() {
        showdata();
    });


    function frmadd_onsubmit() {
       alert(r.listCreatePolygon)
    }

    function save(str) {
        jQuery.post("<?= base_url() ?>geofancedrawing/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", {
                json: str
            },
            function(r) {
                if (r.error) {
                    alert(r.message);
                } else {
                    alert("Geofence polygon successfully saved!");
                }
            },
            "json"
        );
    }

    function removegeofence() {
        if (!confirm("<?php echo $this->lang->line('lconfirm_delete'); ?>")) return;

        jQuery.post("<?= base_url() ?>geofence/remove/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", {},
            function(r) {
                if (r.error) {
                    alert(r.message);
                    return false;
                }

                location = '<?= base_url() ?>geofancedrawing/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid(); ?>';
            }, "json"
        );

        return;

    }

    function deserialize(str) {

        var formats = new OpenLayers.Format.GeoJSON(out_options)
        var features = formats.read(str)
        var bounds;
        if (features) {
            if (features.constructor != Array) {
                features = [features];
            }

            for (var i = 0; i < features.length; ++i) {
                if (!bounds) {
                    bounds = features[i].geometry.getBounds();
                } else {
                    bounds.extend(features[i].geometry.getBounds());
                }

            }
            vectors.addFeatures(features);
            map.zoomToExtent(bounds);
        }

    }

    function gotogeofence(gid) {
        jQuery.post("<?php echo base_url(); ?>geofence/get/" + gid, {},
            function(r) {
                map.setCenter(new OpenLayers.LonLat(r.point[0], r.point[1]).transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    map.getProjectionObject()
                ), map.getZoom());

                jQuery("#dialog").dialog('close');
            }, "json"
        );
    }

    function removegeofence(gid) {
        if (!confirm("<?php echo $this->lang->line('lconfirm_delete'); ?>")) return;

        jQuery.post("<?php echo base_url(); ?>geofence/removebyid/" + gid, {},
            function(r) {
                if (r.error) {
                    alert(r.message);
                    return;
                }

                alert(r.message);
                jQuery("#dialog").dialog('close');
                location = '<?= base_url() ?>geofence/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/label/<?php echo uniqid(); ?>';
            }, "json"
        );
    }

    function removegeofence_byvehicle(gid) {
        if (!confirm("<?php echo $this->lang->line('lconfirm_delete'); ?>")) return;

        jQuery.post("<?php echo base_url(); ?>geofence/removebyvehicle/" + gid, {},
            function(r) {
                if (r.error) {
                    alert(r.message);
                    return;
                }

                alert(r.message);
                jQuery("#dialog").dialog('close');
                location = '<?= base_url() ?>geofence/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/label/<?php echo uniqid(); ?>';
            }, "json"
        );
    }

    function savegeo() {
        jQuery.post("<?php echo base_url(); ?>geofence/savelabel/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", jQuery("#frmadd1").serialize(),
            function(r) {
                alert("<?php echo $this->lang->line("lsavelabel_successfully"); ?>");
                jQuery("#dialog").dialog('close');
            }, "json"
        );
    }

    function copyto() {
        showdialog();
        jQuery.post('<?php echo base_url(); ?>geofence/copyto', {
                vid: <?php echo $vehicle->vehicle_id ?>
            },
            function(r) {
                if (r.error) {
                    alert(r.message);
                    return;
                }
                showdialog(r.html, r.title);
            }, "json"
        );
    }

    function gotovehicle() {
        jQuery.post('<?php echo base_url(); ?>map/lastinfo', {
                device: '<?php echo $vehicle->vehicle_device ?>'
            },
            function(r) {
                if (!r.vehicle.gps) return;

                map.setCenter(new OpenLayers.LonLat(r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real).transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    map.getProjectionObject()
                ), 18);

                if (kml_tracker5 != null) {
                    map.removeLayer(kml_tracker5);
                }

                addMarker(r.vehicle.vehicle_no, r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real, r.vehicle.vehicle_id, r.vehicle.gps.car_icon);
                location = "#mapref";
            }, "json"
        );
    }

    function addMarker(no, lng, lat, id, car) {
        kml_tracker5 = new OpenLayers.Layer.GML(
            no,
            "<?= base_url() ?>map/kmllastcoord/" + lng + "/" + lat + "/" + id + "/" + car + "/0", {
                format: OpenLayers.Format.KML,
                projection: new OpenLayers.Projection("EPSG:4326"),
                formatOptions: {
                    extractStyles: true,
                    extractAttributes: true,
                    maxDepth: 2
                }

            }
        );

        map.addLayer(kml_tracker5);
    }

    function othervehicle(elmt) {
        var v = jQuery("#vehicleid").val();
        location = '<?= base_url() ?>geofancedrawing/manage/' + v;
    }

    var map, vectors, kml_tracker5 = null;
</script>

<script>
    $(document).ready(function() {
        // When the "Tutup" button is clicked
        $('.btn-secondary').click(function () {
            // Change the button color to red
            $(this).removeClass('btn-secondary').addClass('btn-danger');
            // Close the modal
            $('#myModal').modal('hide');
        });

        // When the modal is closed
        $('#myModal').on('hidden.bs.modal', function () {
            // Reset the button color to secondary
            $('.btn-danger').removeClass('btn-danger').addClass('btn-secondary');
        });

        showdata(); // Moved inside the document ready block
    });
</script>


</html>