<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<? } ?>
	<script src="<?php echo base_url();?>assets/js/v3_epoly.js" type="text/javascript"></script>
<style>
form select{
	position : relative;
}
#map_canvas { position:absolute; top:420px; width:95%; height:80%; }
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

	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
	
	jQuery(document).ready(
		function()
		{
				jQuery("#sdate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
				showclock();

				jQuery("#map_canvas").show();
		}
	);
	
	function frmhistory_onsubmit()
	{
		jQuery("#loader").show();
		jQuery("#history_result").html("");
		jQuery("#history_result").show();
		jQuery("#info").hide();
		
		stopAnimation();
		
		step = 10; // 1; // metres
		tick = 100; // milliseconds
		k=0;
		stepnum=0;
		speed = "";
		lastVertex = 1;
		
		var vehicle = jQuery("#vehicles").val();
		if(vehicle == "")
		{
			alert("Silahkan pilih vehicle terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var sdate = jQuery("#sdate").val();
		if(sdate == "")
		{
			alert("Silahkan pilih Date terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var stime = jQuery("#stime").val();
		if(stime == "")
		{
			alert("Silahkan pilih Start Time terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var etime = jQuery("#etime").val();
		if(etime == "")
		{
			alert("Silahkan pilih End Time terlebih dahulu!");
			jQuery("#loader").hide();
			return false;
		}
		var limit = jQuery("#limit").val();
		var htmlreport="";
		htmlreport += "<div id='main' style='margin: 20px;width:100%;'>";
		htmlreport += "<div class='block-border'>";
		htmlreport += "<center><br /><br /><input type='button' id='btnresume' onclick='javascript:resumeme();' class='buttonanimation' value='RESUME'>&nbsp;&nbsp;<input type='button' id='btnpause' onclick='javascript:pauseme();' class='buttonanimation' value='PAUSE'>&nbsp;&nbsp;";
		htmlreport += "<input type='button' id='btnanimation' onclick='javascript:animation();' class='buttonanimation' value='ANIMATION'></center>"
		jQuery.post("newhistory/gethistory", {id:vehicle,sdate:sdate,stime:stime,etime:etime,limit:limit},
		function(r)
		{
			jQuery("#loader").hide();
			if (r.m)
			{
				alert(r.m);
				return;
			}
    		
    		var datalength = r.data.length;
    		
    		
		
			if(datalength > 0)
			{
				
				var map = new google.maps.Map( 
    				  document.getElementById("map_canvas"), {
    			      center: new google.maps.LatLng(-6.266092, 106.980918),
    			      zoom: 15,
    			      mapTypeId: google.maps.MapTypeId.ROADMAP
    			    });
			
				htmlreport += "<table width='100%' cellpadding='3' class='table sortable no-margin' style='margin: 3px;'>";
				for(var i=0; i < datalength; i++)
				{
					
					lat_lng.push(new google.maps.LatLng(r.data[i].gps_latitude_real_fmt, r.data[i].gps_longitude_real_fmt));
					lat_lng_animated.push(new google.maps.LatLng(r.data[i].gps_latitude_real_fmt, r.data[i].gps_longitude_real_fmt));
					source_destination.push(r.data[i].georeverse.display_name);
					route_time.push(r.data[i].gps_date_fmt+" "+r.data[i].gps_time_fmt);
					
    				var k = i + 1;
    				var myLatLng = new google.maps.LatLng(r.data[i].gps_latitude_real_fmt, r.data[i].gps_longitude_real_fmt);
					var lbl = parseInt(i+1);
    				var marker = new google.maps.Marker({
		    		    position: myLatLng,
		    		    map: map,
    					icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + k + '|000000|FFFFFF'
		    		  });
		    		
		    		map.setCenter(myLatLng);
		    		  
					htmlreport += "<tr><td>";
					htmlreport += "<b>"+k +".</b>"+ " "+"Date :"+" "+r.data[i].gps_date_fmt+" "+r.data[i].gps_time_fmt+"<br />";
					htmlreport += r.data[i].georeverse.display_name+"<br />";
					htmlreport += r.data[i].gps_latitude_real_fmt+","+r.data[i].gps_longitude_real_fmt+"<br />";
					htmlreport += "Speed : "+" "+r.data[i].gps_speed_fmt+" "+"KpH"+" ";
					htmlreport += "Engine : "+" "+r.data[i].status1+" ";
					htmlreport += "GPS : "+" "+r.data[i].gpstatus+"<br /><br />";
					htmlreport += "</td></tr>";
				}
				
				htmlreport += "</table>";
				htmlreport += "</div>";
    		  	
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
    		  	
					
					},2000);
				
				
				
				jQuery("#history_result").css({"position":"absolute","top":"750px"});
				jQuery("#map_canvas").show();
			}
			else
			{
				htmlreport = "";
				htmlreport += "<div id='main' style='margin: 20px;width:100%;'>";
				htmlreport += "<div class='block-border'>";
				htmlreport += "<center><br /><br />";
				htmlreport += "<table width='100%' id='notavailable_info'>";
				htmlreport += "<tr><td>";
				htmlreport += "DATA NOT AVAILABLE !";
				htmlreport += "</td></tr>";
				htmlreport += "</table>";
				jQuery("#history_result").css({"position":"absolute","top":"400px"});
				jQuery("#btnanimation").hide();
				jQuery("#map_canvas").hide();
			}
			jQuery("#history_result").html(htmlreport);
		}
		, "json"
		);
		return false;
	
		
		
		
	}

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
    
	 	jQuery.post("newhistory/gotoanimation", {},
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
    var myOptions = {zoom: 13,mapTypeId: google.maps.MapTypeId.ROADMAP}
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
					map.setZoom(18);
					startAnimation();
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
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmhistory">				
            <h1><?php echo "History"; ?></h1>
			<table width="100%" cellpadding="3" class="tablelist">
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Select Vehicle</td>
					<td style="border: 0px;">
						<select name="vehicles" id="vehicles">
							<option value="">Select Vehicle</option>
							<?php 
								if(isset($vehicles))
								{
								  foreach($vehicles as $v)
								  {
							?>
							<option value="<?php echo $v->vehicle_id?>"><?php echo $v->vehicle_no." - ".$v->vehicle_name; ?></option>
							<?php 
								  }
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Date</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="sdate" id="sdate" value="<?php echo date("d-m-Y"); ?>" class="date-pick" />
						<font color="red">*</font>
					</td>
				</tr>
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Start Time</td>
					<td style="border: 0px;">
						<select name="stime" id="stime">
							<option value="00:00">00:00</option>						                
							<option value="01:00">01:00</option>						                
							<option value="02:00">02:00</option>						                
							<option value="03:00">03:00</option>						                
							<option value="04:00">04:00</option>						                
							<option value="05:00">05:00</option>						                
							<option value="06:00">06:00</option>						                
							<option value="07:00">07:00</option>						                
							<option value="08:00">08:00</option>						                
							<option value="09:00">09:00</option>
							<option value="10:00">10:00</option>						                
							<option value="11:00">11:00</option>						                
							<option value="12:00">12:00</option>						                
							<option value="13:00">13:00</option>						                
							<option value="14:00">14:00</option>						                
							<option value="15:00">15:00</option>						                
							<option value="16:00">16:00</option>						                
							<option value="17:00">17:00</option>						                
							<option value="18:00">18:00</option>						                
							<option value="19:00">19:00</option>						                
							<option value="20:00">20:00</option>						                
							<option value="21:00">21:00</option>						                
							<option value="22:00">22:00</option>						                
							<option value="23:00">23:00</option>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">End Time</td>
					<td style="border: 0px;">
						<select name="etime" id="etime">
							<option value="00:59">00:59</option>						                                                   	
							<option value="01:59">01:59</option>						                
							<option value="02:59">02:59</option>						                
							<option value="03:59">03:59</option>						                
							<option value="04:59">04:59</option>						                
							<option value="05:59">05:59</option>						                
							<option value="06:59">06:59</option>						                
							<option value="07:59">07:59</option>						                
							<option value="08:59">08:59</option>						                
							<option value="09:59">09:59</option>						                
							<option value="10:59">10:59</option>						                
							<option value="11:59">11:59</option>						                
							<option value="12:59">12:59</option>						                
							<option value="13:59">13:59</option>						                
							<option value="14:59">14:59</option>						                
							<option value="15:59">15:59</option>						                
							<option value="16:59">16:59</option>						                
							<option value="17:59">17:59</option>						                
							<option value="18:59">18:59</option>						                
							<option value="19:59">19:59</option>						                
							<option value="20:59">20:59</option>						                
							<option value="21:59">21:59</option>						                
							<option value="22:59">22:59</option>						                
							<option selected="" value="23:59">23:59</option>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Limit</td>
					<td style="border: 0px;">
						<select name="limit" id="limit">
							<option value="10">10</option>
							<option value="30">30</option>
							<option value="50">50</option>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				<tr style="border: 0px;">
					<td style="border: 0px;" colspan="2">
						<input type="button" name="btnsave" id="btnsave" onclick="javascript:frmhistory_onsubmit();" value=" Search " />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>				
			</table>
			</form>
			<div id="map_canvas"></div>
		</div>
	</div>
</div>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<div id="history_result" style="width:97%;"></div>
	<div id="info" style="width:97%;"></div>
</div>
			

			
