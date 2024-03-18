<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geofence Map</title>
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/css/maps.css" />
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/openlayers/OpenLayers.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>
</head>

<body>
    <!-- start sidebar menu -->
    <div class="sidebar-container">
        <?= $sidebar; ?>
    </div>
    <!-- end sidebar menu -->

    <!-- start page content -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <br>
            <?php if ($this->session->flashdata('notif')) { ?>
                <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif'); ?></div>
            <?php } ?>
            <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
            <div class="row">
                <div class="col-md-12" id="tablevehicleforgeofence">
                    <div class="card-box">
                        <div class="card-body">
                            <div id="main">
                                <div class="block-border">
                                    <?= $contentgeofence ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page content -->

    <div class="container mt-5">
        <div id="map-canvas" style = "width: 100%; height: 600px"></div>
        <div id="info" style="position:absolute; color:red; font-family: Arial; height:300px; font-size: 30px;"></div>
    </div>

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJCuzzQJJ7QHwAc6QoCoQXO4X1qQr54f0&libraries=drawing"></script>

    <script>
        var mapOptions;
        var map;

        var coordinates = []
        let new_coordinates = []
        let lastElement

        function InitMap() {
            var location = new google.maps.LatLng(-6.2088, 106.8456);
            mapOptions = {
                zoom: 12,
                center: location,
                mapTypeId: google.maps.MapTypeId.RoadMap
            }
            map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions)
            var all_overlays = [];
            var selectedShape;
            var drawingManager = new google.maps.drawing.DrawingManager({
                //drawingMode: google.maps.drawing.OverlayType.MARKER,
                //drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [
                        //google.maps.drawing.OverlayType.MARKER,
                        //google.maps.drawing.OverlayType.CIRCLE,
                        google.maps.drawing.OverlayType.POLYGON,
                        //google.maps.drawing.OverlayType.RECTANGLE
                    ]
                },
                markerOptions: {
                    //icon: 'images/beachflag.png'
                },
                circleOptions: {
                    fillColor: '#ffff00',
                    fillOpacity: 0.2,
                    strokeWeight: 3,
                    clickable: false,
                    editable: true,
                    zIndex: 1
                },
                polygonOptions: {
                    clickable: true,
                    draggable: false,
                    editable: true,
                    // fillColor: '#ffff00',
                    fillColor: '#ADFF2F',
                    fillOpacity: 0.5,

                },
                rectangleOptions: {
                    clickable: true,
                    draggable: true,
                    editable: true,
                    fillColor: '#ffff00',
                    fillOpacity: 0.5,
                }
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

                //to delete the polygon
                controlUI.addEventListener('click', function () {
                    deleteSelectedShape();
                });
            }

            drawingManager.setMap(map);

            var getPolygonCoords = function (newShape) {

                coordinates.splice(0, coordinates.length)

                var len = newShape.getPath().getLength();

                for (var i = 0; i < len; i++) {
                    coordinates.push(newShape.getPath().getAt(i).toUrlValue(6))
                }
                document.getElementById('info').innerHTML = coordinates
            
            
            }

            google.maps.event.addListener(drawingManager, 'polygoncomplete', function (event) {
                event.getPath().getLength();
                google.maps.event.addListener(event, "dragend", getPolygonCoords(event));

                google.maps.event.addListener(event.getPath(), 'insert_at', function () {
                    getPolygonCoords(event)
                    
                });

                google.maps.event.addListener(event.getPath(), 'set_at', function () {
                    getPolygonCoords(event)
                })
            })

            google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
                all_overlays.push(event);
                if (event.type !== google.maps.drawing.OverlayType.MARKER) {
                    drawingManager.setDrawingMode(null);

                    var newShape = event.overlay;
                    newShape.type = event.type;
                    google.maps.event.addListener(newShape, 'click', function () {
                        setSelection(newShape);
                    });
                    setSelection(newShape);
                }
            })

            var centerControlDiv = document.createElement('div');
            var centerControl = new CenterControl(centerControlDiv, map);

            centerControlDiv.index = 1;
            map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(centerControlDiv);

        }

        InitMap()

    </script>
</body>

</html>
