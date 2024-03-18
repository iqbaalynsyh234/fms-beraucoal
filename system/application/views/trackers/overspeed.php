<?php if ($this->uri->segment(2) == "history") { ?>
<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script> 
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script> 
<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>

<?php
	$key = $this->config->item("GOOGLE_MAP_API_KEY");
	
	if(isset($key) && $key != "") { ?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $key;?>" type="text/javascript"></script>
	<?php } else { ?>
		<script src="http://maps.google.com/maps/api/js?V=3.3&amp;sensor=false"></script> 
	<? } ?>



<?php } ?>
<script>
	var map;
	
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    /// <summary>
	    /// Returns the max zOrder in the document (no parameter)
	    /// Sets max zOrder by passing a non-zero number
	    /// which gets added to the highest zOrder.
	    /// </summary>    
	    /// <param name="opt" type="object">
	    /// inc: increment value, 
	    /// group: selector for zIndex elements to find max for
	    /// </param>
	    /// <returns type="jQuery" />
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
			
			jQuery("#period1").datepicker(
				{
							dateFormat: 'dd/mm/yy'
						, 	startDate: '01/01/1900'
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
			
			jQuery("#period2").datepicker(
				{
							dateFormat: 'dd/mm/yy'
						, 	startDate: '01/01/1900'
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
			
			jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#dvresult').html()));
			});
		
			isnow_click();	
			selectvehicle_onchange();	
		}
	);
	
	<?php if (isset($initmap)) echo $initmap; ?>
	
	function selectvehicle_onchange()
	{
		var v = jQuery("#selectvehicle").val();
		
		var myurl = "<?=base_url(); ?>trackers/search/<?=$this->uri->segment(2);?>/"+v;
		document.frmsearch.action = myurl;		
		
		jQuery.post("<?=base_url(); ?>trackers/menu/<?=$this->uri->segment(2);?>/"+v+"/", {},
			function(r)
			{
				jQuery("#reportmenu").html(r.html);
			}
			, "json"
		);
	}
	
	function page(n, act)
	{		
		jQuery("#isanimate").val(0);
		<?php if ($this->uri->segment(2) == "history") { ?>
			jQuery("#map").html("");
			jQuery("#tdmap").hide();
		<?php } ?>
		
		if (! act) act = "list";
		if (! n) n = 0;
		
		jQuery("#act").val(act);
		jQuery("#offset").val(n);
		
		if (act == "export")
		{
			document.frmsearch.submit();
			return;
		}
	
		var html = jQuery("#dvresult").html();
		var v = jQuery("#selectvehicle").val();
		jQuery("#dvresult").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');		
		jQuery.post("<?=base_url(); ?>trackers/search/<?=$this->uri->segment(2);?>/"+v+"/"+jQuery("#offset").val(), jQuery("#frmsearch").serialize(),
			function(r)
			{
				selectvehicle_onchange();
				
				if (! r)
				{
					alert("Query failed. Please retry!");
					return;
				}
				
				if (r.error)
				{
					jQuery("#dvresult").html(html);
					
					alert(r.message);
					return;
				}
				
				jQuery("#dvresult").html(r.html);
				jQuery("#reporttitle").html(r.title);
			}
			, "json"
		);						
	}
	
	function frmsearch_submit()
	{
		page(0);
		return false;
	}
	
	function isnow_click()
	{
		jQuery("#tglperiod").attr("disabled", jQuery("#isnow").attr("checked"));
		jQuery("#jamperiod").attr("disabled", jQuery("#isnow").attr("checked"));
		
		if (jQuery("#isnow").attr("checked"))
		{
			jQuery("#period1").css("background-color", "#cccccc");
			jQuery("#period2").css("background-color", "#cccccc");
			
			jQuery("#hperiod1").css("background-color", "#cccccc");
			jQuery("#mperiod1").css("background-color", "#cccccc");
			jQuery("#speriod1").css("background-color", "#cccccc");
			
			jQuery("#hperiod2").css("background-color", "#cccccc");
			jQuery("#mperiod2").css("background-color", "#cccccc");
			jQuery("#speriod2").css("background-color", "#cccccc");
		}
		else
		{
			jQuery("#period1").css("background-color", "#ffffff");
			jQuery("#period2").css("background-color", "#ffffff");
			
			jQuery("#hperiod1").css("background-color", "#ffffff");
			jQuery("#mperiod1").css("background-color", "#ffffff");
			jQuery("#speriod1").css("background-color", "#ffffff");
			
			jQuery("#hperiod2").css("background-color", "#ffffff");
			jQuery("#mperiod2").css("background-color", "#ffffff");
			jQuery("#speriod2").css("background-color", "#ffffff");			
		}
		
	}
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">        
<!-- new -->        
<article class="container_12">
<section class="grid_12">
<div class="block-border">
<form class="block-content form" name="frmsearch" id="frmsearch" method="post" action="<?=base_url(); ?>trackers/search/<?=$this->uri->segment(2);?>/<?=$this->uri->segment(3);?>/<?=$this->uri->segment(4);?>" onsubmit="javascript: return frmsearch_submit()">
<table>
<tr>
    <td <?php if ($this->uri->segment(2) == "history") { ?>width="70%"<?php } ?>>
        <input type="hidden" name="offset" id="offset" value="0" />
        <input type="hidden" name="act" id="act" value="0" />
        <input type="hidden" name="isanimate" id="isanimate" value="0" />
        <h2>
			      			<?php
			      				switch($this->uri->segment(2))
			      				{
			      					case "overspeed":
			      						echo $this->lang->line("loverspeed_report");
			      						break;
			      					case "parkingtime":
			      						echo $this->lang->line("lparking_time_report");
			      					break;
			      					case "history":
			      						echo $this->lang->line("lhistory_report");
			      					break;
			      					case "workhour":
			      						echo $this->lang->line("lworkhour_report");
			      					break;
			      					case "engine":
			      						echo $this->lang->line("lengine_report");
			      					break;
			      					case "door":
			      						echo $this->lang->line("ldoor_status_report");
			      					break;			      					
			      					case "geofence":
			      						echo $this->lang->line("lgeofence_report");
			      					break;
			      					case "odometer":
			      						echo $this->lang->line("lodometer_report");
			      					break;
				      				case "alarm":
			      						echo $this->lang->line("lalarm");
			      					break;
				      				case "pulse":
			      						echo $this->lang->line("lpulse_report");
			      					break;
		      				}      				
			      			?>
			      				"<span id='reporttitle'><?=$vehicle->vehicle_no?> <?=$vehicle->vehicle_name?></span>"			      				
			      		</h2>
                        <div id="reportmenu" style="height: 30px;"></div>	      		
						<p>
                            <div class="block-border">
							<table width="100%" cellpadding="3" class="tablelist">
								<tr>
                                    <td style="border: 0px;" width="15%" colspan="5">
                                    <fieldset class="grey-bg required">
                                    <legend><?php echo $this->lang->line("lvehicle"); ?></legend>
                                    <select name="selectvehicle" id="selectvehicle" onchange="javascript:selectvehicle_onchange()">	
										<?php for($i=0; $i < count($vehicles); $i++) { ?>
										<?php 
											if (in_array($this->uri->segment(2), array("workhour", "engine", "door", "alarm"))) 
											{
												if (! in_array(strtoupper($vehicles[$i]->vehicle_type), $this->config->item("vehicle_gtp")) && $vehicles[$i]->vehicle_type != "TK309PTO" && $vehicles[$i]->vehicle_type != "GT06PTO")
												{
													continue;
												}
											} 
										?> 
										<option value="<?php echo $vehicles[$i]->vehicle_device1; ?>"<?php if ($vehicle->vehicle_id == $vehicles[$i]->vehicle_id) { echo " selected"; } ?>><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." - "; } ?><?php echo $vehicles[$i]->vehicle_name; ?> - <?php echo $vehicles[$i]->vehicle_no; ?></option>
										<?php } ?>										
										</select>
                                        </fieldset>
                                    </td>
                                    
								</tr>
                                
								<?php if ((isset($geofencenames)) && (count($geofencenames))) { ?>
								<tr <?php if ($this->uri->segment(2) != "geofence") { ?>style="display: none;"<?php } ?>>
									<td width="15%" style="border: 0px;" colspan="5">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("lgeofence_name"); ?>
                                    </legend>
                                    <select id="geoname" name="geoname">
											<option value="All" >All</option>
											<?php for($i=0; $i < count($geofencenames); $i++) { ?>
											<option value="<?php echo $geofencenames[$i]->geofence_name; ?>"><?php echo $geofencenames[$i]->geofence_name; ?></option>
											<?php } ?>
										</select>
                                    <?=$this->lang->line("lstatus"); ?>
                                    <select id="geostatus" name="geostatus">
											<option value="">--</option>
											<option value="1"><?=$this->lang->line("lin"); ?></option>
											<option value="2"><?=$this->lang->line("lout"); ?></option>											
										</select>
                                    </fieldset>
                                    </td>
								</tr>
								<?php } ?>
                                
								<tr <?php if ($this->uri->segment(2) != "alarm") { ?>style="display: none;"<?php } ?>>
									<td width="15%" style="border: 0px;">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("lalarmtype"); ?>
                                    </legend>
                                    <select id="alarmtype" name="alarmtype">
											<option value="">--</option>
											<?php
												$alarms = $this->config->item("ALARMS");
												foreach($alarms as $key=>$val)
												{
											?>
												<option value="<?php echo $key; ?>">(<?php echo $key; ?>) <?php echo $val; ?></option>
											<?php
												} 
											?>
										</select>
                                    </fieldset>
                                    </td>
								</tr>
                                
								<tr <?php if ($this->uri->segment(2) != "engine") { ?>style="display: none;"<?php } ?>>
									<td width="15%" style="border: 0px;">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("lengine_1"); ?>
                                    </legend>
                                    <select id="engine1" name="engine1">
											<option value="">--</option>
											<option value="on"><?=$this->lang->line("lon"); ?></option>
											<option value="off"><?=$this->lang->line("loff"); ?></option>
										</select>
										<select id="engine2" name="engine2" style="display:none;">
											<option value="">--</option>
											<option value="on"><?=$this->lang->line("lrelease"); ?></option>
											<option value="off"><?=$this->lang->line("lunrelease"); ?></option>
										</select>
                                    </fieldset>
                                    </td>
								</tr>
                                
								<tr <?php if ($this->uri->segment(2) != "door") { ?>style="display: none;"<?php } ?>>
									<td width="15%" style="border: 0px;" colspan="5">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("ldoor_status"); ?>
                                    </legend>
                                    <select id="status" name="status">
											<option value="">--</option>
											<option value="opened"><?=$this->lang->line("lopened"); ?></option>
											<option value="closed"><?=$this->lang->line("lclosed"); ?></option>
									</select>
                                    </fieldset>
                                    </td>
								</tr>	
                                							
								<tr <?php if ($this->uri->segment(2) != "overspeed") { ?>style="display: none;"<?php } ?>>
                                    <td width="10%" style="border: 0px;" colspan="5">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("loverspeed_limit"); ?>
                                    </legend>
                                    <input type='text' name="speedlimit" id="speedlimit" class='formshort' value="<?php if (isset($_POST['speedlimit'])) echo $_POST['speedlimit']; ?>" style="text-align: right;">&nbsp;&nbsp;<?=$this->lang->line("lkph"); ?>
                                    </fieldset>
                                    </td>
								</tr>
                                
								<tr <?php if ($this->uri->segment(2) != "parkingtime") { ?>style="display: none;"<?php } ?>>
									<td style="border: 0px;" colspan="5">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("lparking_time"); ?>
                                    </legend>
                                    <input type='text' name="hparkingtime" id="hparkingtime" class='formshort' value="<?php if (isset($_POST['hparkingtime'])) echo $_POST['hparkingtime']; ?>" style="text-align: right;">&nbsp;&nbsp;<?=$this->lang->line("lhour"); ?>
											<input type='text' name="mparkingtime" id="mparkingtime" class='formshort' value="<?php if (isset($_POST['mparkingtime'])) echo $_POST['mparkingtime']; ?>" style="text-align: right;">&nbsp;&nbsp;<?=$this->lang->line("lminute"); ?>
                                    </fieldset>
                                    </td>
								</tr>
                                				
								<tr style="display: none">
									<td style="border: 0px;">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("ldatetime"); ?>
                                    </legend>
                                    <input type="checkbox" id="isnow" name="isnow" value="1" onclick="javascript:isnow_click()" /> <?=$this->lang->line("lnow"); ?>
                                    </fieldset>
                                    </td>
								</tr>
                                																	
								<tr id="tglperiod">
									<td style="border: 0px;" colspan="5">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?=$this->lang->line("ldatetime"); ?>
                                    </legend>
                                    <input type='text' name="period1" id="period1"  class="date-pick" value="<?=$_POST['period1']; ?>"  maxlength='10' />
                                    <?=$this->lang->line("luntil"); ?>
                                    <input type='text' name="period2" id="period2"  class="date-pick" value="<?=$_POST['period2']; ?>"  maxlength='10' />				
                                    </fieldset>
                                    </td>
								</tr>
                                
                                <tr>
                                <td colspan="5">
                                <fieldset class="grey-bg required">
                                <select name="hperiod1" id="hperiod1">
										<?php for($i=0; $i < 24; $i++) { ?>							
												<option value="<?=$i?>"<?=($i == $_POST['hperiod1']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
										<?php } ?>
									</select>
									<select name="mperiod1" id="mperiod1">
										<?php for($i=0; $i < 60; $i++) { ?>							
												<option value="<?=$i?>"<?=($i == $_POST['mperiod1']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
										<?php } ?>
                                    </select>						
									<select name="speriod1" id="speriod1">
										<?php for($i=0; $i < 60; $i++) { ?>							
												<option value="<?=$i?>"<?=($i == $_POST['speriod1']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
										<?php } ?>
									</select>
                                    <?=$this->lang->line("luntil"); ?>
                                    <select name="hperiod2" id="hperiod2">
										<?php for($i=0; $i < 24; $i++) { ?>							
												<option value="<?=$i?>"<?php echo ($i == $_POST['hperiod2']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
										<?php } ?>
									</select>
									<select name="mperiod2" id="mperiod2">
										<?php for($i=0; $i < 60; $i++) { ?>							
												<option value="<?=$i?>"<? echo ($i == $_POST['mperiod2']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
										<?php } ?>
									</select>						
									<select name="speriod2" id="speriod2">
										<?php for($i=0; $i < 60; $i++) { ?>							
												<option value="<?=$i?>"<? echo ($i == $_POST['speriod2']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>							
										<?php } ?>
									</select>
                                    </fieldset>
                                </td>
                                </tr>
                                
								<tr>
									<td valign="top" style="border: 0px;" colspan="5">
                                    <fieldset class="grey-bg required">
                                        <legend>
                                        <?php echo $this->lang->line("lshow_per"); ?>
                                        </legend>
                                        <select name="limit" id="limit">
											<?php 
											$limits = $this->config->item("LIMITS"); 
											foreach($limits as $limit) {
											?>
											<option value="<?php echo $limit; ?>"><?php echo $limit; ?></option>
											<?php } ?>
											?>
										</select>
										<?php echo $this->lang->line("ldata"); ?>
                                    </fieldset>
                                    </td>
								</tr>
                                
								<tr <?php if ($this->uri->segment(2) != "history") { ?>style="display: none;"<?php } ?>>
									<td width="15%" style="border: 0px;">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?php echo ucfirst(trim($this->lang->line("ldata"))); ?>
                                    </legend>
                                    <select id="data" name="data">
									<option value="1"><?=$this->lang->line("ldetail"); ?></option>
									<option value="2"><?=$this->lang->line("lsummary"); ?></option>
									</select>
                                    </fieldset>
                                    </td>
								</tr>	
                                							
								<tr>
									<td valign="top" style="border: 0px;" colspan="5">
                                    <fieldset class="grey-bg required">
                                    <legend>
                                    <?php echo $this->lang->line("lexport_format"); ?>
                                    </legend>
									
                                    <input type="radio" name="format" id="format" value="csv;" checked /><?php echo $this->lang->line("lcsv_dot_comma"); ?><br />
									<input type="radio" name="format" id="format" value="csv," /> <?php echo $this->lang->line("lcsv_comma"); ?><br />
									<input type="radio" name="format" id="format" value="kml" /> <?php echo "Data KML"; ?>
										
										<?php if ($this->uri->segment(2) == "history" || $this->uri->segment(2) == "overspeed" || $this->uri->segment(2) == "parkingtime" || 
                                        $this->uri->segment(2) == "workhour" || $this->uri->segment(2) == "engine" || $this->uri->segment(2) == "odometer" || $this->uri->segment(2) == "geofence") { ?>
										<br/><input type="radio" name="format" id="format" value="excell" /> <?php echo "Excel"; ?>
                                        <?php if ($this->sess->user_type == 1) { ?>
                                        <br/><input type="radio" name="format" id="format" value="pdf" /> <?php echo "Pdf"; ?>
                                        <?php } ?>
										<?php } ?>
										
                                    </fieldset>
                                    </td>
								</tr>
								<tr>
									<td valign="top" colspan="3" style="border: 0px;">
										<input class="button" type="submit" value="Search" />
										<input class="button" type="button" value="Export" onclick="javascript:page(0, 'export')" />
										<a class="button" href="javascript:void(0);" id="export_xcel">Export [InView] to Excel</a>
									</td>
								</tr>					
							</table>
                            </div>							
				</td>			
			</tr>		
    
    
    </td>
    </tr>
    </table>
	<br />
    <table width="100%">
    <tr><td>&nbsp;</td></tr>
    <tr>
    <td valign="top" align="right" id="tdmap" style="display: none;">
					<div id="map" style="border: 1px #000000 solid; width: 100%; height: 300px;"></div>
					<div id="refmap"><a href="">[ <?=$this->lang->line('lfull_size');?> ]</a></div>
				</td>	
    </table>
    </tr> 
    </form>
    </div>
    <div id="dvresult"></div>
    </section>
    </article>
        <!--end new-->
