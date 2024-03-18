	
<br />
<style>
form select{
	position : relative;
}
#map_canvas { position:absolute; top:450px; width:95%; height:80%; }
#history_result { position:absolute; top:750px; }
#info { position:absolute; top:770px; }
.buttonanimation {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 15px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 10px;
}
</style>
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>

<script>
var geocoder;
	var map;
	var lat_lng = [];
	var lat_lng_animated = [];
	var source_destination = [];
	var route_time = [];
	var t=0; var t2 = 0;
	var isPaused = false;
	
	var directionDisplay;
	var directionsService;
	var stepDisplay;
	var markerArray = [];
	var position;
	var marker = null;
	var polyline = null;
	var poly2 = null;
	var speed = 0.000005, wait = 1;
	var infowindow = null;
	
	var myPano;   
	var panoClient;
	var nextPanoId;
	var timerHandle = null;
	var currentDistance;
	var waypoints = [];
	
	var xx = 0;
	var htmlreport;
	
	var step = 10; // 1; // metres
	var tick = 100; // milliseconds
	var eol;
	var k=0;
	var stepnum=0;
	var speed = "";
	var lastVertex = 1;
	
	var initok = false;
	
	
function pauseme()
 {
	isPaused = true;
 }
 function resumeme()
 {
	isPaused = false;
 }
 function animation()
 {
	 
		jQuery("map_canvas").hide();
		jQuery("history_result").hide();
		jQuery("#info").show();
		
		lat_lng = JSON.parse(localStorage.getItem("lat_lng"));
		source_destination = JSON.parse(localStorage.getItem("source_destination"));
		route_time = JSON.parse(localStorage.getItem("route_time"));
    
	 	jQuery.post("operational_report/gotoanimation", {},
		function(r)
		{
			xx = 0;
			initialize();
			calcRoute();
		}
		, "json"
		);
 }
 
function createMarker(latlng, label, html) 
{
    var contentString = '<b>'+label+'</b><br>'+html;
    var marker = new google.maps.Marker({position: latlng, map: map, title: label, zIndex: Math.round(latlng.lat()*-100000)<<5 });
    marker.myname = label;
    google.maps.event.addListener(marker, 'click', function() { infowindow.setContent(contentString);  infowindow.open(map,marker); });
    return marker;
}

function initialize() 
{
	infowindow = new google.maps.InfoWindow({ size: new google.maps.Size(150,50) });
    directionsService = new google.maps.DirectionsService();
    var myOptions = {zoom: 10,mapTypeId: google.maps.MapTypeId.ROADMAP}
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    address = 'new york'
    geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': address}, function(results, status) {map.setCenter(results[0].geometry.location);});
    var rendererOptions = {map: map}
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
    stepDisplay = new google.maps.InfoWindow();
    polyline = new google.maps.Polyline({ path: [], strokeColor: '#FF0000', strokeWeight: 3 });
    poly2 = new google.maps.Polyline({ path: [], strokeColor: '#FF0000', strokeWeight: 3 });
}
var steps = []

function calcRoute()
{
	jQuery("#history_result").hide();
	if (timerHandle) { clearTimeout(timerHandle); }
	if (marker) { marker.setMap(null);}
	polyline.setMap(null);
	poly2.setMap(null);
	directionsDisplay.setMap(null);
    polyline = new google.maps.Polyline({path: [],strokeColor: '#FF0000',strokeWeight: 3});
    poly2 = new google.maps.Polyline({path: [],strokeColor: '#FF0000',strokeWeight: 3});
    var rendererOptions = { map: map }
    directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

	var infohtml="";
	if(xx < lat_lng.length)
	{
		if(lat_lng[xx+1])
		{
			var start = lat_lng[xx];
			var end = lat_lng[lat_lng.length-1];
			infohtml += "<div id='main' style='margin: 20px;width:100%;'>";
			infohtml += "<div class='block-border'>";
			infohtml += "<center><table width='100%'><tr><td style='text-align:center;'>";
			infohtml += "<input type='button' id='btn_pause' onclick='javascript:stopAnimation();' class='buttonanimation' value='PAUSE'>&nbsp;&nbsp;";
			infohtml += "<input type='button' id='btn_continue' onclick='javascript:continueAnimation();' class='buttonanimation' value='CONTINUE'><br />"
			infohtml += "Start :"+" "+source_destination[xx]+","+" "+"Date"+" "+route_time[xx]+"<br />";
			infohtml += "End :"+" "+source_destination[lat_lng.length-1]+","+" "+"Date"+" "+route_time[lat_lng.length-1]+"<br />";
			infohtml += "</td></tr></table>";
			infohtml += "</center>";
			infohtml += "</div>";
			infohtml += "</div>";
			jQuery("#info").html(infohtml);
			
			for(y=0;y<20;y++)
			{
				if(lat_lng[y])
				{
					waypoints.push({location: lat_lng[y],stopover: true});
				}
			}
			
			xx++;
			var travelMode = google.maps.DirectionsTravelMode.DRIVING
			var request = { origin: start, destination: end, waypoints:waypoints, travelMode: travelMode };
			directionsService.route(request, function(response, status) 
			{
				if (status == google.maps.DirectionsStatus.OK)
				{
					directionsDisplay.setDirections(response);
					var bounds = new google.maps.LatLngBounds();
					var route = response.routes[0];
					startLocation = new Object();
					endLocation = new Object();

					// For each route, display summary information.
					var path = response.routes[0].overview_path;
					var legs = response.routes[0].legs;
					for (i=0;i<legs.length;i++) 
					{
						if (i == 0) 
						{ 
							startLocation.latlng = legs[i].start_location;
							startLocation.address = legs[i].start_address;
							marker = createMarker(legs[i].start_location,"start",legs[i].start_address,"green");
						}
						endLocation.latlng = legs[i].end_location;
						endLocation.address = legs[i].end_address;
						var steps = legs[i].steps;
						for (j=0;j<steps.length;j++) 
						{
							var nextSegment = steps[j].path;
							for (k=0;k<nextSegment.length;k++) 
							{
								polyline.getPath().push(nextSegment[k]);
								bounds.extend(nextSegment[k]);
							}
						}
					}
					polyline.setMap(map);
					map.fitBounds(bounds);
					//        createMarker(endLocation.latlng,"end",endLocation.address,"red");
					//map.setZoom(18);
					animation();
				}                                                    
			});
		}
	}
}


function updatePoly(d) 
{
	// Spawn a new polyline every 20 vertices, because updating a 100-vertex poly is too slow
	if (poly2.getPath().getLength() > 20) 
	{
		poly2=new google.maps.Polyline([polyline.getPath().getAt(lastVertex-1)]);
		// map.addOverlay(poly2)
	}
	if (polyline.GetIndexAtDistance(d) < lastVertex+2) 
	{
		if (poly2.getPath().getLength()>1) 
		{
			poly2.getPath().removeAt(poly2.getPath().getLength()-1)
        }
        poly2.getPath().insertAt(poly2.getPath().getLength(),polyline.GetPointAtDistance(d));
    } 
    else 
    {
		poly2.getPath().insertAt(poly2.getPath().getLength(),endLocation.latlng);
    }
 }
 
function animate(d) 
{
	//alert("animate("+d+")");
	if (d>eol) 
	{
		map.panTo(endLocation.latlng);
		marker.setPosition(endLocation.latlng);
		//Looping
		/*
		if(xx < lat_lng.length)
		{
			if(lat_lng[xx+1])
			{
				calcRoute();
			}
		}*/
		calcRoute();
		return;
	}
	var p = polyline.GetPointAtDistance(d);
	map.panTo(p);
	marker.setPosition(p);
	updatePoly(d);
	timerHandle = setTimeout("animate("+(d+step)+")", tick);
	currentDistance=d+step;
}

function startAnimation()
{
	eol=polyline.Distance();
	map.setCenter(polyline.getPath().getAt(0));
	poly2 = new google.maps.Polyline({path: [polyline.getPath().getAt(0)], strokeColor:"#0000FF", strokeWeight:10});        
	setTimeout("animate(0)",2000);  // Allow time for the initial map display
}

function refresh() 
{
	animation();
}

function stopAnimation() {
  clearTimeout(timerHandle);
}

function continueAnimation() {
	d=currentDistance;
	setTimeout("animate("+d+")", tick);
}

</script>

<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);
					//alert("TES");
					var datalength = <?=count($data_summary);?>;
					var data_summary = <?=json_encode($data_summary);?>;
					if(datalength > 0)
					{
						
						var map = new google.maps.Map( 
							  document.getElementById("map_canvas"), {
							  center: new google.maps.LatLng(-6.266092, 106.980918),
							  zoom: 12,
							  mapTypeId: google.maps.MapTypeId.ROADMAP
							});
					
						/*htmlreport += "<table width='100%' cellpadding='3' class='table sortable no-margin' style='margin: 3px;'>"; */
						for(var i=0; i < datalength; i++)
						{
							lat_lng.push(new google.maps.LatLng(data_summary[i].summary_lat, data_summary[i].summary_lng));
							lat_lng_animated.push(new google.maps.LatLng(data_summary[i].summary_lat, data_summary[i].summary_lng));
							source_destination.push(data_summary[i].summary_location);
							route_time.push(data_summary[i].summary_gps_time);
							
							var k = i + 1;
							var myLatLng = new google.maps.LatLng(data_summary[i].summary_lat, data_summary[i].summary_lng);
							var lbl = parseInt(i+1);
							var marker = new google.maps.Marker({
								position: myLatLng,
								map: map,
								icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + k + '|000000|FFFFFF'
							  });
							
							map.setCenter(myLatLng);
							  
							/*htmlreport += "<tr><td>";
							htmlreport += "<b>"+k +".</b>"+ " "+"Date :"+" "+r.data[i].gps_date_fmt+" "+r.data[i].gps_time_fmt+"<br />";
							htmlreport += r.data[i].georeverse.display_name+"<br />";
							htmlreport += r.data[i].gps_latitude_real_fmt+","+r.data[i].gps_longitude_real_fmt+"<br />";
							htmlreport += "Speed : "+" "+r.data[i].gps_speed_fmt+" "+"KpH"+" ";
							htmlreport += "Engine : "+" "+r.data[i].status1+" ";
							htmlreport += "GPS : "+" "+r.data[i].gpstatus+"<br /><br />";
							htmlreport += "</td></tr>";*/
						}
						/*
						htmlreport += "</table>";
						htmlreport += "</div>";
						*/
						window.localStorage.removeItem("lat_lng");
						window.localStorage.removeItem("source_destination");
						window.localStorage.removeItem("route_time");
						window.localStorage.setItem("lat_lng",JSON.stringify(lat_lng_animated));
						window.localStorage.setItem("source_destination",JSON.stringify(source_destination));
						window.localStorage.setItem("route_time",JSON.stringify(route_time));
						
						setInterval(function(){
							
							//for (t = 0; (t + 1) < lat_lng.length; t++) 
							//{
						 if(!isPaused) {
							if(t < lat_lng.length)
							{
								var service = new google.maps.DirectionsService();
								var directionsDisplay = new google.maps.DirectionsRenderer();
							
								var bounds = new google.maps.LatLngBounds();
								if ((t + 1) < lat_lng.length) 
								{

								var src = lat_lng[t];
								var des = lat_lng[t + 1];
								service.route({
										origin: src,
										destination: des,
										travelMode: google.maps.DirectionsTravelMode.DRIVING
										}, 
										function(result, status) 
										{
											if (status == google.maps.DirectionsStatus.OK) 
											{
												// new path for the next result
												var path = new google.maps.MVCArray();
												//Set the Path Stroke Color
												// new polyline for the next result
												var poly = new google.maps.Polyline({
												map: map,
												strokeColor: '#0faf0f',
												strokeOpacity: 0.5,
												strokeWeight: 2
												});
												poly.setPath(path);
												for (var k = 0, len = result.routes[0].overview_path.length; k < len; k++) 
												{
													path.push(result.routes[0].overview_path[k]);
													bounds.extend(result.routes[0].overview_path[k]);
													map.fitBounds(bounds);
												}
											} 
											//else alert("Directions Service failed:" + status);
										});
										t++
							}
						}
					}
						//}
						
							
							},500);
						
						
						
						//jQuery("#history_result").css({"position":"absolute","top":"750px"});
						jQuery("#map_canvas").show();
						startAnimation
					}
					else
					{
						/*htmlreport = "";
						htmlreport += "<div id='main' style='margin: 20px;width:100%;'>";
						htmlreport += "<div class='block-border'>";
						htmlreport += "<center><br /><br />";
						htmlreport += "<table width='100%' id='notavailable_info'>";
						htmlreport += "<tr><td>";
						htmlreport += "DATA NOT AVAILABLE !";
						htmlreport += "</td></tr>";
						htmlreport += "</table>"; */
						//jQuery("#history_result").css({"position":"absolute","top":"400px"});
						jQuery("#btnanimation").hide();
						jQuery("#map_canvas").hide();
					}
</script>

<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a> 
<!--
<input type='button' id='btnanimation' onclick='javascript:animation();' class='buttonanimation' value='ANIMATION'>
<input type='button' id='btn_pause' onclick='javascript:stopAnimation();' class='buttonanimation' value='PAUSE'>
<input type='button' id='btn_continue' onclick='javascript:continueAnimation();' class='buttonanimation' value='CONTINUE'>-->
<div id="map_canvas"></div>
<div id="isexport_xcel">
<table width="95%" cellpadding="3" class="table sortable no-margin" style="margin: 3px; position:absolute; top:840px;"  >
	<thead>
    	<tr>
	        <th style="text-align:center;" width="3%">No</th>
			<th style="text-align:center;" width="10%">Vehicle No</th>
			<!--<th style="text-align:center;" width="10%">Vehicle Device</th>-->
			<th style="text-align:center;" width="10%">GPS Info</th>
			<th style="text-align:center;" width="20%">Position</th>
			<!--<th style="text-align:center;" width="15%">Cordinate</th>-->
			<!--<th style="text-align:center;" width="5%">Status</th>
			<th style="text-align:center;" width="5%">Speed (km/jam)</th>
			<th style="text-align:center;" width="5%">Engine</th>-->
			<!--<th style="text-align:center;" width="20%">Odometer(km)</th>-->
		</tr>
    </thead>
	<tbody>
		<?php	
			if ((isset($data_summary) && count($data_summary)>0))
			{
					
				for ($i=0;$i<count($data_summary);$i++)
				{
				?>
				<tr>
					<td style="text-align:center;"><?php echo $i+1;?></td>
					<td style="text-align:center;">
						<?php echo $data_summary[$i]->summary_vehicle_no;?> <br /> <?php echo $data_summary[$i]->summary_vehicle_name;?> 
					</td>
					<td style="text-align:left;">
					<?php echo "GPS Time: ".date("d-m-Y H:i:s", strtotime($data_summary[$i]->summary_gps_time));?><br />
					<?php echo "GPS Status: ".$data_summary[$i]->summary_gps_status;?><br />
					<?php echo "Speed: ".$data_summary[$i]->summary_speed;?> <br />
					<?php echo "Engine: ".$data_summary[$i]->summary_engine;?>
					</td>
					<td style="text-align:left;">
						<?php echo $data_summary[$i]->summary_location;?> <br />
						<a href="http://maps.google.com/maps?q=<?=$data_summary[$i]->summary_lat." ".$data_summary[$i]->summary_lng;?>" target="_blank"><b>
						<?php echo $data_summary[$i]->summary_lat." ".$data_summary[$i]->summary_lng;?> </b></a>
					</td>
					
				</tr>
		<?php
				}
			}
			else
			{
				echo "<tr><td colspan='12'>No Data Available</td></tr>";
			}
			?>
    </tbody>
	
</table>
</div>