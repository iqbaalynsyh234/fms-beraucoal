<!--<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />-->
<!--<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script> -->
<!-- <script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>-->
<!--<script src="http://maps.google.com/maps/api/js?sensor=false"></script>-->

<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<? } ?>
	<script src="<?php echo base_url();?>assets/js/v3_epoly.js" type="text/javascript"></script>
	
<script>
	
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
			showclock();
			jQuery("#date").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);	

			jQuery("#enddate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			jQuery("#histstartdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);	

			jQuery("#histenddate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, 	changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
		}
	);
	
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>operational_report/dataoperational/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				if (r.error) {
					alert(r.message);
					jQuery("#loader").hide();
					jQuery("#result").hide();
					return;
				}else{
					jQuery("#loader").hide();
					jQuery("#result").show();
					jQuery("#result").html(r.html);		
					jQuery("#total").html(r.total);	
					
				}		
			}
			, "json"
		);
	}
	
	
	function frmsearch_onsubmit()
	{
		jQuery("#loader").show();
		page(0);
		return false;
	}
	
	function excel_onsubmit(){
		jQuery("#loader2").show();
		
		jQuery.post("<?=base_url();?>operational_report/dataoperasional_excel/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				if (r.error) {
					alert(r.message);
					jQuery("#loader2").hide();
					return;
				}else{
					jQuery("#loader2").hide();
					if(r.success == true){
						jQuery("#frmreq").attr("src", r.filename);			
					}else{
						alert(r.errMsg);
					}	
				}		
			}
			
			, "json"
		);
		
		return false;
	}
	
	
	function order(by)
	{						
		if (by == jQuery("#sortby").val())
		{
			if (jQuery("#orderby").val() == "asc")
			{
				jQuery("#orderby").val("desc");
			}
			else
			{
				jQuery("#orderby").val("asc");
			}
		}
		else
		{
			jQuery("#orderby").val('asc')
		}
		
		jQuery("#sortby").val(by);
		page(0);
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
	infowindow = new google.maps.InfoWindow({ size: new google.maps.Size(50,50) });
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

function company_onchange(){
		var data_company = jQuery("#company").val();
		if(data_company == 0){
			alert('Silahkan Pilih Cabang!!');
			jQuery("#mn_vehicle").hide();
			
			jQuery("#vehicle").html("<option value='0' selected='selected'>--Select Vehicle--</option>");
		}else{
			jQuery("#mn_vehicle").show();
			
			var site = "<?=base_url()?>operational_report/get_vehicle_by_company/" + data_company;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
					jQuery("#vehicle").html("");
		            jQuery("#vehicle").html(response);
		        },
		    	dataType:"html"
		    });

		}

	}

function option_type_location(v)
		{
			switch(v)
			{
				case "location_no":
					jQuery('#location').val("");
					jQuery("#location_view").hide();
				break;
				case "location_yes":
					jQuery("#location_view").show();
				break;
			}
		}
		
function option_type_duration(v)
		{
			switch(v)
			{
				case "duration_no":
					jQuery('#s_minute').val("");
					jQuery('#e_minute').val("");
					jQuery("#duration_view").hide();
				break;
				case "duration_yes":
					jQuery("#duration_view").show();
				break;
			}
		}
function option_type_km(v)
		{
			switch(v)
			{
				case "km_no":
					jQuery('#km_start').val("");
					jQuery('#km_end').val("");
					jQuery("#km_view").hide();
				break;
				case "km_yes":
					jQuery("#km_view").show();
				break;
			}
		}
		
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        <h1>Operational Report</h1>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist" style="font-size: 12px;">
			<?php if($this->sess->user_level == "1"){ ?>
				<tr id="mn_company">
					<td>Cabang</td>
					<td>
						<select id="company" name="company" onchange="javascript:company_onchange()">
							<option value="" selected='selected'>--Cabang--</option>
							<?php 
								$ccompany = count($rcompany);
									for($i=0;$i<$ccompany;$i++){
										if (isset($rcompany)&&($row->parent_company == $rcompany[$i]->company_id)){
												$selected = "selected"; 
											}else{
												$selected = "";
											}
										echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
										}
							?>
						</select>
					</td>
				</tr>
				<tr id="mn_vehicle" style="display:none">
					<td>Vehicle</td>
					<td>
						<select id="vehicle" name="vehicle">
							<!--<option value="" selected='selected'>--Select Vehicle--</option>-->
							<?php 
								$cvehicle = count($vehicles);
									for($i=0;$i<$cvehicle;$i++){
										if (isset($vehicles)&&($row->vehicle_company == $vehicles[$i]->company_id)){
												$selected = "selected"; 
											}else{
												$selected = "";
											}
										echo "<option value='" . $vehicles[$i]->vehicle_device ."' " . $selected . ">" . $vehicles[$i]->vehicle_no ." - ".$vehicles[$i]->vehicle_name. "</option>";
										}
							?>
						</select>
					</td>
				</tr>
			<?php }else{ ?>
				<tr id="mn_vehicle">
					<td>Vehicle</td>
					<td>
						<select id="vehicle" name="vehicle">
							<!--<option value="" selected='selected'>--Select Vehicle--</option>-->
							<?php 
								$cvehicle = count($vehicles);
									for($i=0;$i<$cvehicle;$i++){
										if (isset($vehicles)&&($row->vehicle_company == $vehicles[$i]->company_id)){
												$selected = "selected"; 
											}else{
												$selected = "";
											}
										echo "<option value='" . $vehicles[$i]->vehicle_device ."' " . $selected . ">" . $vehicles[$i]->vehicle_no ." - ".$vehicles[$i]->vehicle_name. "</option>";
										}
							?>
						</select>
					</td>
				</tr>
				
			<?php } ?>
				
				<tr id="filterdatestartend">
					<td width="10%">Date</td>
					<td>
						<input type='text' readonly name="startdate" id="startdate" class="date-pick" value="<?=date('Y/m/d',strtotime("yesterday") )?>"  maxlength='10'>         <select class="textgray" style="font-size: 11px; width: 65px;" id="shour" name="shour">						                
						                    <option value="00:00:00" selected>00:00</option>						                
						                    <option value="01:00:00">01:00</option>						                
						                    <option value="02:00:00">02:00</option>						                
						                    <option value="03:00:00">03:00</option>						                
						                    <option value="04:00:00">04:00</option>						                
						                    <option value="05:00:00">05:00</option>						                
						                    <option value="06:00:00">06:00</option>						                
						                    <option value="07:00:00">07:00</option>						                
						                    <option value="08:00:00">08:00</option>						                
						                    <option value="09:00:00">09:00</option>						                
						                    <option value="10:00:00">10:00</option>						                
						                    <option value="11:00:00">11:00</option>						                
						                    <option value="12:00:00">12:00</option>						                
						                    <option value="13:00:00">13:00</option>						                
						                    <option value="14:00:00">14:00</option>						                
						                    <option value="15:00:00">15:00</option>						                
						                    <option value="16:00:00">16:00</option>						                
						                    <option value="17:00:00">17:00</option>						                
						                    <option value="18:00:00">18:00</option>						                
						                    <option value="19:00:00">19:00</option>						                
						                    <option value="20:00:00">20:00</option>						                
						                    <option value="21:00:00">21:00</option>						                
						                    <option value="22:00:00">22:00</option>						                
						                    <option value="23:00:00">23:00</option>
						                
						             </select>  
						~ <input type='text' readonly name="enddate" id="enddate"  class="date-pick" value="<?=date('Y/m/d', strtotime("yesterday"))?>"  maxlength='10'>
						<select class="textgray" style="font-size: 11px; width: 65px;" id="ehour" name="ehour">
						                
						                    <option value="00:59:59">00:59</option>						                
						                    <option value="01:59:59">01:59</option>						                
						                    <option value="02:59:59">02:59</option>						                
						                    <option value="03:59:59">03:59</option>						                
						                    <option value="04:59:59">04:59</option>						                
						                    <option value="05:59:59">05:59</option>						                
						                    <option value="06:59:59">06:59</option>						                
						                    <option value="07:59:59">07:59</option>						                
						                    <option value="08:59:59">08:59</option>						                
						                    <option value="09:59:59">09:59</option>						                
						                    <option value="10:59:59">10:59</option>						                
						                    <option value="11:59:59">11:59</option>						                
						                    <option value="12:59:59">12:59</option>						                
						                    <option value="13:59:59">13:59</option>						                
						                    <option value="14:59:59">14:59</option>						                
						                    <option value="15:59:59">15:59</option>						                
						                    <option value="16:59:59">16:59</option>						                
						                    <option value="17:59:59">17:59</option>						                
						                    <option value="18:59:59">18:59</option>						                
						                    <option value="19:59:59">19:59</option>						                
						                    <option value="20:59:59">20:59</option>						                
						                    <option value="21:59:59">21:59</option>						                
						                    <option value="22:59:59">22:59</option>						                
						                    <option value="23:59:59" selected >23:59</option>
						                </select>
					</td>
				</tr>
				<tr>
					<td>Engine</td>
					<td>
						<select id="engine" name="engine">
							<option value="">All</option>
							<option value="1">ON</option>
							<option value="0">OFF</option>
						</select>
					</td>
				</tr>
				
				<!--<tr>
				<td>Filter</td>
					<td>
						<select id="duration" name="duration">
							<option value="0">Detail</option>
							<option value="61">Summary</option>
						</select>
					</td>
				</tr>
				-->
				<tr>
					<td><br />Location </td>
					<td><br /><input name="type_location" type="radio" value="" onClick="option_type_location('location_no')" checked >No</input>
						<input name="type_location" type="radio" value="1" onClick="option_type_location('location_yes')">Yes</input> 
						<div id="location_view" style="display:none"> 
							Location Start: <input type="text" name="location_start" id="location_start" value="" size="30" placeholder="Ex: jakarta selatan"/> 
							Location End: <input type="text" name="location_end" id="location_end" value="" size="30" placeholder="Ex: jakarta selatan"/>
						</div>
					</td>
				</tr>
				
				<tr>
					<td><br />Duration(minute)</td>
					<td><br />
						<input name="type_duration" type="radio" value="" onClick="option_type_duration('duration_no')" checked >No</input>
						<input name="type_duration" type="radio" value="1" onClick="option_type_duration('duration_yes')">Yes</input> 
						<div id="duration_view" style="display:none"> 
							From: <input type="text" name="s_minute" id="s_minute" value="" size="3" placeholder="0~9" maxlength="4"/> 
							To: <input type="text" name="e_minute" id="e_minute" value="" size="3" placeholder="0~9" maxlength="4"/> 
						</div>
					</td>
				</tr>
				<tr>
					<td><br />Filter KM</td>
					<td><br />
						<input name="type_km" type="radio" value="" onClick="option_type_km('km_no')" checked >No</input>
						<input name="type_km" type="radio" value="1" onClick="option_type_km('km_yes')">Yes</input> 
						<div id="km_view" style="display:none"> 
							From: <input type="text" name="km_start" id="km_start" value="" size="3" placeholder="0~9" maxlength="3"/> KM
							To: <input type="text" name="km_end" id="km_end" value="" size="3" placeholder="0~9" maxlength="3" /> KM
						</div>
					</td>
				</tr>
				
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input class="btn_search2" id="btnsearchreport" type="submit" value="Search" />
					<!--<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />-->
                    <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
					<!--input type="button" name="pdf" value="Export To PDF" onclick="javascript:return pdf_onsubmit()" /-->
					</td>
				</tr>
				
			</table>
		</form>		
		<br />
		<div id="result"></div>	
		
		<iframe id="frmreq" style="display:none;"></iframe>
        </div>
	</div>
</div>
