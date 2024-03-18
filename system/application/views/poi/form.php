<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			poicat_onchange();
			showmap('initclick()');
		}
	);
	
	function showmapex()
	{
		showmap('initclick()');
	}
	
	OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, 
	{
		defaultHandlerOptions: 
		{
			'single': true,
			'double': true,
			'pixelTolerance': 0,
			'stopSingle': false,
			'stopDouble': true
		},
	
		initialize: 
			function(options) 
			{
				this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
				OpenLayers.Control.prototype.initialize.apply(this, arguments); 
				this.handler = new OpenLayers.Handler.Click(this, 
					{
						'click': this.trigger
						,'dblclick': this.trigger
					}, this.handlerOptions
				);
			}, 
				
		trigger: 
			function(e) 
			{
				var lonlat = map.getLonLatFromViewPortPx(e.xy);
				lonlat.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));				
												
				if (map)
				{
					var zoom = map.getZoom();
					if (zoom >= <?php echo $this->config->item('zoom_poi'); ?>)
					{
						m_latlot = lonlat.lat+","+lonlat.lon;									
					}
					
            		map.setCenter(new OpenLayers.LonLat(lonlat.lon, lonlat.lat).transform(
                    	new OpenLayers.Projection("EPSG:4326"),
                    	map.getProjectionObject()
                	), zoom); 
                }
				
				location = '#frmlink';
			}
	
	});	
	
	function initclick()
	{
        var click = new OpenLayers.Control.Click();
        map.addControl(click);
		click.activate();		
		
		<?php if (isset($row)) { ?>
        		map.setCenter(new OpenLayers.LonLat(<?php echo $row->poi_longitude;?>, <?php echo $row->poi_latitude;?>).transform(
                	new OpenLayers.Projection("EPSG:4326"),
                	map.getProjectionObject()
            	), <?php echo $this->config->item('zoom_poi'); ?>); 
			
		<?php } ?>		
	}
	
	function frmadd_onsubmit(frm)
	{
		jQuery.post("<?=base_url()?>poi/save", jQuery("#frmadd").serialize(),
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"
		);
		return false;
	}
	
	function poicat_onchange()
	{
		<?php for($i=0; $i < count($categories); $i++) { ?>
		jQuery("#img<?=$categories[$i]->poi_cat_id;?>").hide();
		<?php } ?>
		
		var poiid = jQuery("#poicat").val();
		jQuery("#img"+poiid).show();
	}
	
	function carilokasi()
	{
		jQuery.post("<?php echo base_url(); ?>map/geocode/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return;
				}
				
				map.setCenter(new OpenLayers.LonLat(r.lng, r.lat).transform(
					new OpenLayers.Projection("EPSG:4326"),
					map.getProjectionObject()
				), 15); 
				
			}
			, "json"
		);
	}
	
	var m_latlot = "";
</script>
        <div class="block-border">
        <form class="block-content form" id="frmsearch" name="frmsearch" onsubmit="javascript:carilokasi(); return false;">
		<?php if (! isset($row)) { ?>
		<h1><?=$this->lang->line("lpoi_add"); ?></h1>
		<?php } else { ?>
		<h1><?=$this->lang->line("lpoi_update"); ?></h1>
		<?php } ?>
			<?php echo $this->lang->line("llocation"); ?>: <input type="text" class="formdefault" value="" id="lokasi" name="lokasi" />
			<input type="button" value="<?php echo $this->lang->line("lcenter"); ?>" onclick="javascript: carilokasi()" /> ,<?php echo $this->lang->line("llocation_sample"); ?>
		</form>
        
        <form class="block-content form" id="frmadd" onsubmit="javascript: return frmadd_onsubmit(this)">
        <fieldset>
        <legend><?php echo $this->lang->line('laddupdate_poi'); ?></legend>
        <div id="map" style="width: 100%; height: 400px;"></div>
		<a name="frmlink"></a>
        </fieldset>
					
				<table width="100%" cellpadding="3" class="tablelist">
					<?php if (isset($row)) { ?>
					<input type="hidden" id="id" name="id" value="<?=$row->poi_id;?>" />
					<tr style="border: 0px;">
						<td style="border: 0px;">ID</td>
						<td style="border: 0px;"><?=$row->poi_id;?></td>
					</tr>
					<?php } ?>
    			<tr style="border: 0px;">
						<td width="100" style="border: 0px;"><?=$this->lang->line('lpoi_category'); ?></td>
						<td style="border: 0px;">
							<select id="poicat" name="poicat" onchange="javascript:poicat_onchange()">
							<?php for($i=0; $i < count($categories); $i++) { ?>
							<option value="<?=$categories[$i]->poi_cat_id;?>"<?php if (isset($row) && ($categories[$i]->poi_cat_id == $row->poi_category)) { echo " selected"; } ?>><?=$categories[$i]->poi_cat_name;?></option>
							<?php } ?>
						</select>
						<br />
						<?php for($i=0; $i < count($categories); $i++) { ?>
							<?php if ($categories[$i]->poi_cat_icon) { ?>
								<span id="img<?=$categories[$i]->poi_cat_id;?>" style="display: none;"><img src="<?=base_url()?>assets/images/poi/<?=$categories[$i]->poi_cat_icon;?>" border="0" /></span>
								<?php } else { ?>
								<span id="img<?=$categories[$i]->poi_cat_id;?>">&nbsp;</span>
								<?php } ?>											
						<?php } ?>
						</td>
					</tr>
    			<tr style="border: 0px;">
						<td style="border: 0px;"><?=$this->lang->line('lpoi_name'); ?></td>
						<td style="border: 0px;"><input type="text" name="poiname" id="poiname" value="<?=isset($row) ? htmlspecialchars($row->poi_name, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>	
    			<tr style="border: 0px;">
						<td style="border: 0px;"><?=$this->lang->line('lcoordinate'); ?></td>
						<td style="border: 0px;">
							<input type="text" name="coord" id="coord" value="<?=isset($row) ? htmlspecialchars($row->poi_latitude.",".$row->poi_longitude, ENT_QUOTES) : "";?>" class="formdefault" /> 
							<input class="button" type="button" value="Get LatLng" onclick='javascript: jQuery("#coord").val(m_latlot);' />
							<!-- <?=$this->lang->line('laltlngpoidesc');?> -->
						</td>
					</tr>									
    			<tr style="border: 0px;">
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">
								<input class="button" type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input class="button" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>poi';" />
						</td>
					</tr>					
				</table>
			</form>
            <footer>
        <div class="float-right">
        <a href="#top" class="button"><img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/navigation-090.png" width="16" height="16" /> Page top</a>
        </div>
        </footer>  		
            </div>