<head>
    <meta charset="UTF-8">
    <!-- Bootstrap 4 CSS -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://openlayers.org/api/OpenLayers.js"></script>
</head>

<body>

    <style>
    /* Custom CSS for responsive modal */
    #myModal .modal-dialog {
        max-width: 90%;
    }

    #myModal .modal-header,
    #myModal .modal-footer {
        padding: 10px;
    }

    #myModal .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }


    .modal-content {
        background-color: #f8f9fa;
        /* Warna latar belakang modal */
        width: 100%;
    }

    .modal-backdrop.show {
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

    /* CSS */
    input[type="range"] {
        -webkit-appearance: none;
        width: 70px;
        /* Lebar slider */
        height: 20px;
        /* Tinggi slider */
        background: #d3d3d3;
        outline: none;
        opacity: 0.7;
        -webkit-transition: .2s;
        transition: opacity .2s;
    }

    input[type="range"]:hover {
        opacity: 1;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        /* Lebar slider thumb */
        height: 20px;
        /* Tinggi slider thumb */
        background: #4CAF50;
        /* Warna slider thumb */
        cursor: pointer;
    }

    input[type="range"]::-moz-range-thumb {
        width: 20px;
        /* Lebar slider thumb */
        height: 20px;
        /* Tinggi slider thumb */
        background: #4CAF50;
        /* Warna slider thumb */
        cursor: pointer;
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
            url: '<?= base_url() ?>geofencedrawing/label', // Update the URL to the actual endpoint
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

    <?php 
		$key = $this->config->item("GOOGLE_MAP_API_KEY");
		// $key = "AIzaSyAgPzlDKOewmk4yCSmb-C4oEN10Y2211oA";
	?>

    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgPzlDKOewmk4yCSmb-C4oEN10Y2211oA&libraries=drawing">
    </script>
    <!-- <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgPzlDKOewmk4yCSmb-C4oEN10Y2211oA<?php echo $key;?>&libraries=drawing">
    </script> -->

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
                        <?= $this->lang->line("lmangeofence"); ?>
                    </legend>
                </fieldset>

                <?php if ($this->sess->user_id == "4408"){ ?>
                <fieldset>
                    <input type="text" class="formdefault" value="" id="lokasi" name="lokasi" size="30"
                        placeholder="input coordinate Ex: lat,long" />
                    <input class="button" type="button" value="<?php echo $this->lang->line("lcenter"); ?>"
                        onclick="javascript: cariDanMarkerLokasi()" />
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                        Save
                    </button>
                    <a href="<?=base_url();?>geofencedatalistlive" class="btn btn-flat"
                        type="button"><?php echo $this->lang->line("lgeofence_list"); ?> </a>
                    <input class="button" type="button" name="btncancel" id="btncancel"
                        style="margin-bottom: 20px; background-color: red;"
                        value=" <?php echo $this->lang->line("lreset"); ?> "
                        onclick="location='<?= base_url() ?>geofencedrawing/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid(); ?>'; " />

                </fieldset>
                <?php } ?>

                <header class="panel-heading panel-heading-green" style="background-color: #4bb036;">Geofence Drawing
                    Polygon</header>
                <div id="map-canvas" style="height: 600px; width: 700px margin-left: 300px; "></div>
                <div id="info" style="position:absolute; color:red; font-family: Arial; height:200px; font-size: 30px;">
                </div>
                <div id="hasilGambar"></div>
            </div>
        </div>
    </div>

    <!-- Elemen modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered mw-90">
            <div class="modal-content">

                <!-- Header modal -->
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">List geofence Name</h4>
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
                    <button type="button" id="saveGeofenceNameButton" class="btn btn-primary"
                        data-dismiss="modal">Save</button>
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
var koordinatArrayAll = [];
var polygons = [];
var showLabel = true;
var polygonsVisible = true;

function InitMap(koordinatArrayAll, showLabel = true) {

    map = new google.maps.Map(document.getElementById('map-canvas'), {

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
            fillColor: '#f6c79a', // field color draw polygon 
            fillOpacity: 0.5,
            strokeColor: 'transparent', // Make the border color transparent
            strokeOpacity: 0, // Make the border fully transparent
        },

    });

    drawingManager.setMap(map);

    function addMarkerToCenter() {
        // Add a marker to the center of the map
        var centerMarker = new google.maps.Marker({
            position: map.getCenter(),
            map: map,
            title: 'Center Marker',
        });

        // Push the marker to the markers array
        markers.push(centerMarker); // Tambahkan marker ke array markers di sini

        google.maps.event.addListener(centerMarker, 'click', function() {
            var latitude = centerMarker.getPosition().lat();
            var longitude = centerMarker.getPosition().lng();
            alert('Latitude: ' + latitude + ', Longitude: ' + longitude);
        });
    }

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
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
                '<td><input type="text" class="form-control" id="geofenceNameInput_' + i + '" value="' +
                geofenceName +
                '"></td>' +

                '</tr>';

            modalBody.innerHTML += '<tr>' +
                '<th scope="row"></th>' +
                '<td>' + JSON.stringify(listCreatePolygon[i]) + '</td>' +
                '</tr>';
        }

        var modalFooter = document.querySelector('.modal-footer');
        modalFooter.innerHTML =
            '<button type="button" class="btn btn-primary" onclick="simpanData(listCreatePolygon)">Save</button>' +
            '<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>';
    }

    // Iterate over koordinatArrayAll to draw existing polygons
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

        // Menambahkan polygon ke array polygons
        polygons.push(polygon);

        // Calculate the center of the polygon
        var bounds = new google.maps.LatLngBounds();
        paramCoordinates.forEach(function(point) {
            bounds.extend(point);
        });
        var center = bounds.getCenter();

        var geofenceName = "Geofence " + (i + 1);
        var marker = new google.maps.Marker({
            position: center,
            map: map,
            label: showLabel ? {
                text: koordinatArrayAll[i][0].geofence_name,
                color: 'black',
                fontWeight: 'normal'
            } : null,
            icon: {
                // url: 'https://maps.gstatic.com/mapfiles/transparent.png'
                size: new google.maps.Size(1, 1),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 32)
            }
        });

    }

    // buttom show hide polygon 
    var togglePolygonControlDiv = document.createElement('div');
    var togglePolygonControl = new TogglePolygonControl(togglePolygonControlDiv, map);
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(togglePolygonControlDiv);

    // buttom show hide label 
    var toggleMarkerControlDiv = document.createElement('div');
    var toggleMarkerControl = new ToggleMarkerControl(toggleMarkerControlDiv, map);
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(toggleMarkerControlDiv);

    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }

}

// Custom control untuk toggle visibilitas marker
function ToggleMarkerControl(controlDiv, map) {
    // Create a container for the switch
    var switchContainer = document.createElement('div');
    switchContainer.style.marginBottom = '10px';
    controlDiv.appendChild(switchContainer);

    // Create the toggle slider
    var toggleSlider = document.createElement('input');
    toggleSlider.type = 'range';
    toggleSlider.min = '0';
    toggleSlider.max = '1';
    toggleSlider.step = '1';
    toggleSlider.value = showLabel ? '1' : '0'; // Set initial state
    switchContainer.appendChild(toggleSlider);

    // Event listener for slider change
    toggleSlider.addEventListener('input', function() {
        // Toggle the showLabel variable
        showLabel = parseInt(this.value) === 1;

        // Toggle markers and labels
        for (var i = 0; i < markers.length; i++) {
            markers[i].setVisible(showLabel); // Toggle marker visibility
        }

        // Call the showdata function
        showdata();
    });

    // Create the label for the switch
    var switchLabel = document.createElement('label');
    switchLabel.textContent = 'Toggle Markers and Labels';
    switchLabel.style.fontSize = '11px';
    switchContainer.appendChild(switchLabel);
}


// Custom control untuk toggle visibilitas polygon
function TogglePolygonControl(controlDiv, map) {
    var switchContainer = document.createElement('div');
    switchContainer.style.marginBottom = '10px';
    controlDiv.appendChild(switchContainer);

    // Create the toggle slider
    var toggleSlider = document.createElement('input');
    toggleSlider.type = 'range';
    toggleSlider.min = '0';
    toggleSlider.max = '1';
    toggleSlider.step = '1';
    toggleSlider.value = polygonsVisible ? '1' : '0'; // Set initial state
    switchContainer.appendChild(toggleSlider);

    toggleSlider.addEventListener('input', function() {
        // Toggle the polygonsVisible variable
        polygonsVisible = parseInt(this.value) === 1;

        // Toggle visibility of polygons
        for (var i = 0; i < polygons.length; i++) {
            if (polygonsVisible) {
                polygons[i].setMap(map);
            } else {
                polygons[i].setMap(null);
            }
        }
    });

    // Create the label for the switch
    var switchLabel = document.createElement('label');
    switchLabel.textContent = 'Toggle Polygons';
    switchContainer.appendChild(switchLabel);
}

// function toggle markers
function toggleMarkers() {
    InitMap(koordinatArrayAll, showLabel);
}


function cariDanMarkerLokasi() {
    var lokasiInput = document.getElementById("lokasi").value;
    var coordinates = lokasiInput.split(',');
    var lat = parseFloat(coordinates[0]);
    var lng = parseFloat(coordinates[1]);

    if (!isNaN(lat) && !isNaN(lng)) {
        var location = new google.maps.LatLng(lat, lng);

        // Tambahkan marker ke lokasi yang dicari
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            title: 'Lokasi Pencarian',
        });

        // Pindahkan kamera ke lokasi yang dicari
        map.panTo(location);

        // Tampilkan info window dengan informasi koordinat
        var infoWindow = new google.maps.InfoWindow({
            content: 'Latitude: ' + lat + '<br>Longitude: ' + lng
        });

        // Tampilkan info window saat marker di-klik
        google.maps.event.addListener(marker, 'click', function() {
            infoWindow.open(map, marker);
        });
    } else {
        alert(`Masukkan koordinat yang valid. ${lat} ${lng}`);
    }


}

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

function simpanData(listCreatePolygon) {
    // Clone the listCreatePolygon to avoid unintended modifications
    var polygon = JSON.parse(JSON.stringify(listCreatePolygon));

    // Create an array to store geofence names
    var listGeofenceName = [];

    // Iterate over the polygon array to extract geofence names
    for (var i = 0; i < polygon.length; i++) {
        var geofenceNameInput = document.getElementById(`geofenceNameInput_${i}`);
        var geofenceName = geofenceNameInput.value;
        listGeofenceName.push(geofenceName);
    }

    // Prepare the data to be sent in the AJAX request
    var postData = {
        geofenceName: listGeofenceName,
        listCreatePolygon: polygon
    };

    console.log('Post Data:', postData);

    // Perform AJAX request to save data
    $.post('<?= base_url() ?>geofencedrawing/save', postData)
        .done(function(response) {
            try {
                // Check if the response seems to be in JSON format
                if (response.startsWith('{') || response.startsWith('[')) {
                    var jsonResponse = JSON.parse(response);

                    if (jsonResponse && jsonResponse.error === false) {
                        console.log('Data saved successfully:', jsonResponse.message);

                        // Display success alert (optional, can be removed)
                        alert('Data saved successfully: ' + jsonResponse.message);
                    } else {
                        console.error('Error saving data:', jsonResponse ? jsonResponse.message :
                            'Invalid JSON response');
                    }
                } else {
                    // If not JSON, treat it as success and log
                    console.log('Data saved successfully:', response);
                }

                // Refresh the page
                window.location.reload();
            } catch (error) {
                console.error('Error parsing JSON response:', error);

                // Display success alert (optional, can be removed)
                alert('Data saved successfully: ' + response);

                // Refresh the page
                window.location.reload();
            }
        })
        .fail(function(error) {
            console.error('Error in AJAX request:', error.responseText);
            alert('Error in AJAX request: ' + error.responseText);
        });
}

// Declare hapusData globally
function hapusData(index) {
    listCreatePolygon = listCreatePolygon.filter(function(_, i) {
        return i !== index;
    });
    tampilkanListCreatePolygonUI(); // Update the UI
    console.log(tampilkanListCreatePolygonUI);
}

// function showLabelGeofence(showLabel) {
//     InitMap(koordinatArrayAll, showLabel);
// }

function showdata() {
    var deviceid = '<?php echo $vehicle->vehicle_device; ?>';
    jQuery.post('<?php echo base_url(); ?>geofencedrawing/label', {
        deviceid: deviceid
    }, function(r) {
        if (r.success) {
            // Pass showLabel value when initializing the map
            InitMap(r.koordinat_array_all, showLabel);
        } else {
            InitMap(r.koordinat_array_all, showLabel);
        }
    }, "json");
}
showdata()

// Function to update the marker position based on user input
function carilokasi() {
    var lokasiInput = document.getElementById("lokasi").value;
    var coordinates = lokasiInput.split(',');

    if (coordinates.length === 2) {
        var lat = parseFloat(coordinates[0]);
        var lng = parseFloat(coordinates[1]);

        if (!isNaN(lat) && !isNaN(lng)) {
            var newPosition = new google.maps.LatLng(lat, lng);

            // Check if peta is defined before using it
            if (peta) {
                // Set the new position for the existing marker
                marker.setPosition(newPosition);
                // Center the map on the new position
                peta.setCenter(newPosition);
            } else {
                alert('Map not initialized. Please make sure the map is loaded before using this function.');
            }
        } else {
            alert('Invalid coordinates. Please enter valid latitude and longitude.');
        }
        console.log("sadawd")
    } else {
        alert('Invalid input. Please enter coordinates in the format: latitude, longitude.');
    }
}

// Function to toggle visibility of all polygons
function togglePolygons() {
    for (var i = 0; i < polygons.length; i++) {
        if (polygonsVisible) {
            polygons[i].setMap(null);
        } else {
            polygons[i].setMap(map);
        }
    }
    polygonsVisible = !polygonsVisible;
}

// Function to show data
function showdata() {
    var deviceid = '<?php echo $vehicle->vehicle_device; ?>';

    jQuery.post('<?php echo base_url(); ?>geofencedrawing/label', {
        deviceid: deviceid
    }, function(r) {
        if (r.success) {
            InitMap(r.koordinat_array_all, showLabel);
        } else {
            InitMap(r.koordinat_array_all, showLabel);
        }
    }, "json");
}

// Function to initialize the map
function initialize() {
    showdata();
}

function toggleMarkersAndLabels() {
    setMapOnAll(null);
    // var showMarkers = markers.length > 0 && markers[0].getMap() !== null;
    // showMarkers = !showMarkers;

    // // Toggle markers
    // for (var i = 0; i < markers.length; i++) {
    //     markers[i].setMap(showMarkers ? map : null);
    // }

    // // Toggle labels
    // showLabel = showMarkers; // Update the global variable
    // showLabelGeofence(showLabel); // Toggle geofence label visibility
}

google.maps.event.addDomListener(window, 'load', initialize);
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
    jQuery.post(
        "<?= base_url() ?>geofencedrawing/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", {
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

    jQuery.post(
        "<?= base_url() ?>geofence/remove/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", {},
        function(r) {
            if (r.error) {
                alert(r.message);
                return false;
            }

            location =
                '<?= base_url() ?>geofencedrawing/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid(); ?>';
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
            location =
                '<?= base_url() ?>geofence/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/label/<?php echo uniqid(); ?>';
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
            location =
                '<?= base_url() ?>geofence/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/label/<?php echo uniqid(); ?>';
        }, "json"
    );
}

function savegeo() {
    jQuery.post(
        "<?php echo base_url(); ?>geofence/savelabel/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>",
        jQuery("#frmadd1").serialize(),
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

            map.setCenter(new OpenLayers.LonLat(r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real)
                .transform(
                    new OpenLayers.Projection("EPSG:4326"),
                    map.getProjectionObject()
                ), 18);

            if (kml_tracker5 != null) {
                map.removeLayer(kml_tracker5);
            }

            addMarker(r.vehicle.vehicle_no, r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real, r
                .vehicle.vehicle_id, r.vehicle.gps.car_icon);
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
    location = '<?= base_url() ?>geofencedrawing/manage/' + v;
}

var map, vectors, kml_tracker5 = null;
</script>

<script>
$(document).ready(function() {
    // When the "Tutup" button is clicked
    $('.btn-secondary').click(function() {
        // Change the button color to red
        $(this).removeClass('btn-secondary').addClass('btn-danger');
        // Close the modal
        $('#myModal').modal('hide');
    });

    // When the modal is closed
    $('#myModal').on('hidden.bs.modal', function() {
        // Reset the button color to secondary
        $('.btn-danger').removeClass('btn-danger').addClass('btn-secondary');
    });

    showdata(); // Moved inside the document ready block
});
</script>


</html>