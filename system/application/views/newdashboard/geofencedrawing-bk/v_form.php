<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>    
	<style type="text/css">
        .olControlEditingToolbar .olControlModifyFeatureItemInactive {
            background-position: -1px 0px ;
        }
        .olControlEditingToolbar .olControlModifyFeatureItemActive {
            background-position: -1px -23px ;
        }

    </style>
<script>
	jQuery(document).ready(
		function()
		{
			//showclock();
			init();
			initMap();

			<?php for($i=0; $i < count($geofence); $i++) { ?>
				var style_green =
				{
					strokeColor: "#f49440",
					strokeOpacity: 0.6,
					strokeWidth: 2,
					fillColor: "#f6c79a",
					fillOpacity: 0.4,
					fontSize:'12px',
					label:'<?php echo $geofence[$i]->geofence_name; ?>'
				};
				vectors.style = style_green;
				deserialize('<?php echo $geofence[$i]->geofence_json; ?>');
			<?php } ?>
				var style_last =
				{
					strokeColor: "#f49440",
					strokeOpacity: 0.6,
					strokeWidth: 2,
					fillColor: "#f6c79a",
					fillOpacity: 0.4,
					fontSize:'12px'
				};
				vectors.style = style_last;
			jQuery(".olControlDrawFeaturePointItemInactive").css("display", "none");
			jQuery(".olControlDrawFeaturePathItemInactive").css("display", "none");

			<?php if ($showlabel) { ?>
			showdata();
			<?php } ?>
		}
	);

	function frmadd_onsubmit(frm)
	{
        var out_options = {
            'internalProjection': map.baseLayer.projection,
            'externalProjection': new OpenLayers.Projection("EPSG:4326")
        };

		if (vectors.features.length == 0)
		{
			alert("<?php echo $this->lang->line('lgeofence_empty'); ?>");
			return false;
		}

		var allstr = "";
		for(i=<?php echo count($geofence); ?>; i < vectors.features.length; i++)
		{
			var formats = new OpenLayers.Format.GeoJSON(out_options)
			var str = formats.write(vectors.features[i], false);

			allstr += str + "\1";
		}

		save(allstr);

		alert("<?php echo $this->lang->line('lgeofence_success'); ?>");

		//location = '<?=base_url()?>geofencedatalive/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/label/<?php echo uniqid();?>';
		location = '<?=base_url()?>geofencedatalistlive';
		
		return false;
	}

	function save(str)
	{
		jQuery.post("<?=base_url()?>geofencedatalive/save/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", {json: str},
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return false;
				}
			}
			, "json"
		);

		return true;
	}

	function removegeofence()
	{
		if (! confirm("<?php echo $this->lang->line('lconfirm_delete'); ?>")) return;

		jQuery.post("<?=base_url()?>geofencedatalive/remove/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", {},
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return false;
				}

				location = '<?=base_url()?>geofencedatalive/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid();?>';
			}
			, "json"
		);

		return;

	}

	function initMap()
	{
		vectors = new OpenLayers.Layer.Vector("Vector Layer");

		var style_green =
		{
			strokeColor: "#f49440",
			strokeOpacity: 0.6,
			strokeWidth: 2,
			fillColor: "#f6c79a",
			fillOpacity: 0.4
		};
		vectors.style = style_green;

		map.addLayer(vectors);
		map.addControl(new OpenLayers.Control.EditingToolbar(vectors));

		var options =
		{
			hover: true
		};

		var select = new OpenLayers.Control.SelectFeature(vectors, options);
		map.addControl(select);
		select.activate();

        out_options = {
            'internalProjection': map.baseLayer.projection,
            'externalProjection': new OpenLayers.Projection("EPSG:4326")
        };
	}

	function deserialize(str)
	{
        var out_options = {
            'internalProjection': map.baseLayer.projection,
            'externalProjection': new OpenLayers.Projection("EPSG:4326")
        };

		var formats = new OpenLayers.Format.GeoJSON(out_options)
		var features = formats.read(str)
		var bounds;
		if(features)
		{
			if(features.constructor != Array)
			{
				features = [features];
			}

            for(var i=0; i<features.length; ++i)
            {
                if (!bounds)
                {
                    bounds = features[i].geometry.getBounds();
                } else
                {
                    bounds.extend(features[i].geometry.getBounds());
                }

            }
			vectors.addFeatures(features);
			map.zoomToExtent(bounds);
		}

	}

	function showdata()
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>geofencedatalive/label', {deviceid: '<?php echo $vehicle->vehicle_device; ?>'},
			function(r)
			{
				showdialog(r.html, r.title);
			}
			, "json"
		);
	}

	function gotogeofence(gid)
	{
		jQuery.post("<?php echo base_url(); ?>geofencedatalive/get/"+gid, {},
			function(r)
			{
				map.setCenter(new OpenLayers.LonLat(r.point[0], r.point[1]).transform(
                    			new OpenLayers.Projection("EPSG:4326"),
                    			map.getProjectionObject()
                		) , map.getZoom());

				jQuery("#dialog").dialog('close');
			}
			, "json"
		);
	}

	function removegeofence(gid)
	{
		if (! confirm("<?php echo $this->lang->line('lconfirm_delete'); ?>")) return;

		jQuery.post("<?php echo base_url(); ?>geofencedatalive/removebyid/"+gid, {},
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return;
				}

				alert(r.message);
				jQuery("#dialog").dialog('close');
				location = '<?=base_url()?>geofencedatalive/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/label/<?php echo uniqid();?>';
			}
			, "json"
		);
	}

	function removegeofence_byvehicle(gid)
	{
		if (! confirm("<?php echo $this->lang->line('lconfirm_delete'); ?>")) return;

		jQuery.post("<?php echo base_url(); ?>geofencedatalive/removebyvehicle/"+gid, {},
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return;
				}

				alert(r.message);
				jQuery("#dialog").dialog('close');
				location = '<?=base_url()?>geofencedatalive/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/label/<?php echo uniqid();?>';
			}
			, "json"
		);
	}

	function savegeo()
	{
		jQuery.post("<?php echo base_url(); ?>geofencedatalive/savelabel/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>", jQuery("#frmadd1").serialize(),
			function(r)
			{
				alert("<?php echo $this->lang->line("lsavelabel_successfully"); ?>");
				jQuery("#dialog").dialog('close');
			}
			, "json"
		);
	}

	function copyto()
	{
			showdialog();
			jQuery.post('<?php echo base_url(); ?>geofencedatalive/copyto', {vid: <?php echo $vehicle->vehicle_id ?>},
				function(r)
				{
					if (r.error)
					{
						alert(r.message);
						return;
					}
					showdialog(r.html, r.title);
				}
				, "json"
			);
	}

	function gotovehicle()
	{
		jQuery.post('<?php echo base_url(); ?>map/lastinfo', {device: '<?php echo $vehicle->vehicle_device ?>'},
			function(r)
			{
				if (! r.vehicle.gps) return;

				map.setCenter(new OpenLayers.LonLat(r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real).transform(
                    			new OpenLayers.Projection("EPSG:4326"),
                    			map.getProjectionObject()
                		) , 18);

                if (kml_tracker5 != null)
                {
					map.removeLayer(kml_tracker5);
				}

				addMarker(r.vehicle.vehicle_no, r.vehicle.gps.gps_longitude_real, r.vehicle.gps.gps_latitude_real, r.vehicle.vehicle_id, r.vehicle.gps.car_icon);
                location = "#mapref";
			}
			, "json"
		);
	}

        function addMarker(no, lng, lat, id, car)
        {
			kml_tracker5 = new OpenLayers.Layer.GML
			(
    			no,
    			"<?=base_url()?>map/kmllastcoord/"+lng+"/"+lat+"/"+id+"/"+car+"/0",
    			{
        			format: OpenLayers.Format.KML,
        			projection: new OpenLayers.Projection("EPSG:4326"),
        			formatOptions:
        			{
          				extractStyles: true,
          				extractAttributes: true,
          				maxDepth: 2
        			}

    			}
			);

			map.addLayer(kml_tracker5);
        }

		function carilokasi()
		{

			//new
			var marker = new OpenLayers.Layer.Markers("Markers");
			var size = new OpenLayers.Size(20, 20);
			var offset = new OpenLayers.Pixel(-(size.w / 2), -(size.h / 2));
			var icon = new OpenLayers.Icon('<?php echo base_url();?>assets/images/markergif.gif', size, offset);
			//end

			jQuery.post("<?php echo base_url(); ?>map/geocode2/", jQuery("#frmadd").serialize(),
				function(r)
				{
					if (r.error)
					{
						alert(r.message);
						return;
					}

					var lonlat = new OpenLayers.LonLat(r.lng, r.lat).transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));

					map.setCenter(new OpenLayers.LonLat(r.lng, r.lat).transform(
						new OpenLayers.Projection("EPSG:4326"),
						map.getProjectionObject()
					), 15);



					map.addLayer(marker);
					var trackMarker = new OpenLayers.Marker(lonlat, icon);
					marker.addMarker(trackMarker);

				}
				, "json"
			);
		}

		function othervehicle(elmt)
		{
			var v = jQuery("#vehicleid").val();
			location = '<?=base_url()?>geofencedatalive/manage/'+v;
		}

	var map, vectors, kml_tracker5 = null;

</script>
        <form class="block-content form" id="frmadd" onsubmit="javascript: return carilokasi()">
                <!--<h4>Manage Geofence (Live)</h4>-->
                <table width="100%">
                  <tr>
                    <td>
                      <fieldset>
                        <legend>
                        <?=$this->lang->line("lmangeofence"); ?> (Live) '<?php echo $vehicle->vehicle_name ?> - <?php echo $vehicle->vehicle_no ?>'
                        </legend>
                        <!--<button class="btn btn-flat" type="button" name="btncopy" id="btncopy" onclick="javascript:copyto();" />Copy Geofence To</button>-->
                        <button class="btn btn-flat" type="button" name="btncopy" id="btncopy" onclick="javascript:gotovehicle();" />Center To Vehicle Position</button>
                        <button class="btn btn-flat" type="button" name="btncancel" id="btncancel" onclick="location='<?=base_url()?>geofencedatalive/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid();?>';" />Center To Geofence</button>
                      </fieldset>
                    </td>

                    <td>
                      <fieldset>
                        <legend>
                        <?php echo "Coordinate" . " " . $this->lang->line("llocation"); ?>
                        </legend>
                        <input type="text" class="form-control" value="" id="lokasi" name="lokasi" size="30" />
                        <input class="form-control btn btn-primary" type="button" value="<?php echo $this->lang->line("lcenter"); ?>" onclick="javascript: carilokasi()" />
                      </fieldset>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <fieldset>
                        <legend>Vehicle List</legend>
                        <select name="vehicleid" id="vehicleid" >
                        <?php for($i=0; $i < count($vehicles); $i++) { ?>
                          <?php
                            $curdev = sprintf("%s@%s", $this->uri->segment("3"), $this->uri->segment("4"));
                            $v1 = str_replace("@", "/", $vehicles[$i]->vehicle_device);
                          ?>
                        <option value="<?php echo $v1; ?>"<?php if ($curdev == $vehicles[$i]->vehicle_device) { echo " selected"; }?>><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." "; } ?><?php echo $vehicles[$i]->vehicle_no; ?> - <?php echo $vehicles[$i]->vehicle_name; ?></option>
                        <?php } ?>
                        </select>
                      <input class="button" type="button" name="btnmove" id="btnmove" value=" <?php echo $this->lang->line('lgo'); ?> " onclick="javascript: othervehicle(this)" />
                      </fieldset>
                    </td>

                    <td>
                      <fieldset>
                      <legend>Control</legend>
                          <input class="btn btn-success" type="button" name="btnsave" id="btnsave" value=" Save " onclick="javascript: frmadd_onsubmit(this)" />
                          <a href="<?=base_url();?>geofencedatalistlive" class="btn btn-flat" type="button" ><?php echo $this->lang->line("lgeofence_list"); ?> </a>
                          <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" <?php echo $this->lang->line("lreset"); ?> " onclick="location='<?=base_url()?>geofencedatalive/manage/<?php echo $this->uri->segment("3"); ?>/<?php echo $this->uri->segment("4"); ?>/<?php echo uniqid();?>';" />
                      </fieldset>
                    </td>
                  </tr>
                </table>
				<br />
        			     <a name="mapref"></a>
        			   <div id="map" style="width: 100%; height: 700px;"></div>
      		    </form>
