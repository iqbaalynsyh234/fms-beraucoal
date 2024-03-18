<?php if ($this->uri->segment(2) == "history") { ?>
<link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/maps.css" />
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/OpenLayers.js"></script>
<script type="text/javascript" src="<?=base_url();?>assets/js/openlayers/lib/OpenLayers/Layer/OpenStreetMap.js"></script>
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
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

			isnow_click();
		}
	);

	<?php if (isset($initmap)) echo $initmap; ?>

	function page(n, act)
	{
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

		jQuery("#dvresult").html("<?=$this->lang->line("lwait_loading_data");?>");
		jQuery.post("<?=base_url(); ?>trackers/search/<?=$this->uri->segment(2);?>/<?=$this->uri->segment(3);?>/<?=$this->uri->segment(4);?>/"+jQuery("#offset").val(), jQuery("#frmsearch").serialize(),
			function(r)
			{
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
</script><body onload="page()">
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?></div><br />

    <div style="margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<div id="main" style="margin: 0;">

		<table width="100%" align="center"><tr>
                    <td valign="top" align="right" width="100%" id="tdmap" style="display: none;">
					<div id="map" style="border: 0px #000000 solid; width: 100%; height: 450px; margin: 0;"></div>
					<div id="refmap" style="font-size:10px; font-style:oblique" ><a href="">[ <?=$this->lang->line('lfull_size');?> ]</a></div>
				</td></tr></table>

            <table width="100%" align="center">
			<tr>

				<td align="left">
                                    <div style="margin:35; padding: 0; z-index: 1000; width: 100%;">
                                                        </div>


							<table width="100%" class="tablelist">
                                                            <tr><td>

                                                            <form name="frmsearch" id="frmsearch" method="post" action="<?=base_url(); ?>trackers/search/<?=$this->uri->segment(2);?>/<?=$this->uri->segment(3);?>/<?=$this->uri->segment(4);?>" onsubmit="javascript: return frmsearch_submit()">
						<input type="hidden" name="offset" id="offset" value="0" />
						<input type="hidden" name="act" id="act" value="0" />
			      		<Font size="2"><b>
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
			      						echo $this->lang->line("lengine_1");
			      					break;
			      					case "geofence":
			      						echo $this->lang->line("lgeofence");
			      					break;
			      				}
			      			?></td>
			      			 	<td>" <font size="2" color="green"><b><?=$vehicle->vehicle_no?> <?=$vehicle->vehicle_name?></b></font> "</td></tr>
								<tr <?php if ($this->uri->segment(2) != "engine") { ?>style="display: none;"<?php } ?>>
                                                                    <td width="15%"><font size="2"><b><?=$this->lang->line("lengine_1"); ?></b></font>
										<td><select id="engine1" name="engine1" style="width:50px;background-color:#FF6820;color:#FFE4E1;font-size:10px;font-weight:bold;z-index:1">

											<option value="on"><?=$this->lang->line("lon"); ?></option>
											<option value="off"><?=$this->lang->line("loff"); ?></option>
										</select>
										<select id="engine2" name="engine2" style="width:80px;background-color:#FF6820;color:#FFE4E1;font-size:10px;display:none;font-weight:bold;z-index:1>

											<option value="on"><?=$this->lang->line("lrelease"); ?></option>

										</select></td>
                                                        </td>
								</tr>
								<tr <?php if ($this->uri->segment(2) != "overspeed") { ?>style="display: none;"<?php } ?>>
									<td width="12%" style="border: 0px;"><font size="2" color="red"><b><?=$this->lang->line("loverspeed_limit"); ?></b></font></td>
									<td colspan="3" style="border: 0px;"><font size="2" color="red"><b><input type='text' name="speedlimit" id="speedlimit" class='formshort' value="<?php if (isset($_POST['speedlimit'])) echo $_POST['speedlimit']; ?>" style="text-align: right;">&nbsp;&nbsp;<?=$this->lang->line("lkph"); ?></b></font></td>
								</tr>
								<tr <?php if ($this->uri->segment(2) != "parkingtime") { ?>style="display: none;"<?php } ?>>
									<td width="12%" style="border: 0px;"><font size="2"><b><?=$this->lang->line("lparking_time"); ?></b></font></td>
									<td colspan="3" style="border: 0px;">
											<input type='text' name="hparkingtime" id="hparkingtime" class='formshort' value="<?php if (isset($_POST['hparkingtime'])) echo $_POST['hparkingtime']; ?>" style="text-align: right;">&nbsp;&nbsp;<?=$this->lang->line("lhour"); ?>
											<input type='text' name="mparkingtime" id="mparkingtime" class='formshort' value="<?php if (isset($_POST['mparkingtime'])) echo $_POST['mparkingtime']; ?>" style="text-align: right;">&nbsp;&nbsp;<?=$this->lang->line("lminute"); ?>
									</td>
								</tr>
								<tr style="display: none">
									<td width="12%" style="border: 0px;"><font size="2"><b><?=$this->lang->line("ldatetime"); ?></b></font></td>
									<td colspan="3" style="border: 0px;">
										<input type="checkbox" id="isnow" name="isnow" value="1" onclick="javascript:isnow_click()" /> <?=$this->lang->line("lnow"); ?>
									</td>
								</tr>
								<tr id="tglperiod">
									<td width="15%" rowspan="2"><font size="2"><b><?=$this->lang->line("ldatetime"); ?></b></font></td>
									<td width="16%" style="border: 0px;">
										<input type='text' name="period1" id="period1"  class="date-pick" value="<?=$_POST['period1']; ?>"  maxlength='10' style="color:#FF6820;font-weight:bold;font-size:10px;">
									</td>
									<td width="1%" align="middle" rowspan="2"><font size="2"><b><?=$this->lang->line("luntil"); ?></b></font></td>
									<td style="border: 0px;"><input type='text' name="period2" id="period2"  class="date-pick" value="<?=$_POST['period2']; ?>"  maxlength='10' style="color:#FF6820;font-weight:bold;font-size:10px;"></td>
								</tr>
								<tr id="jamperiod">
									<td>
										<select name="hperiod1" id="hperiod1" style="font-size:10px;font-weight:bold;z-index:1">
										<?php for($i=0; $i < 24; $i++) { ?>
												<option value="<?=$i?>"<?=($i == $_POST['hperiod1']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>
										<?php } ?>
										</select>
										<select name="mperiod1" id="mperiod1" style="font-size:10px;font-weight:bold;z-index:1">
										<?php for($i=0; $i < 60; $i++) { ?>
												<option value="<?=$i?>"<?=($i == $_POST['mperiod1']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>
										<?php } ?>
										</select>
										<select name="speriod1" id="speriod1" style="font-size:10px;font-weight:bold;z-index:1">
										<?php for($i=0; $i < 60; $i++) { ?>
												<option value="<?=$i?>"<?=($i == $_POST['speriod1']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>
										<?php } ?>
										</select>
									</td>
									<td>
										<select name="hperiod2" id="hperiod2" style="font-size:10px;font-weight:bold;z-index:1">
										<?php for($i=0; $i < 24; $i++) { ?>
												<option value="<?=$i?>"<?php echo ($i == $_POST['hperiod2']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>
										<?php } ?>
										</select>
										<select name="mperiod2" id="mperiod2" style="font-size:10px;font-weight:bold;z-index:1">
										<?php for($i=0; $i < 60; $i++) { ?>
												<option value="<?=$i?>"<? echo ($i == $_POST['mperiod2']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>
										<?php } ?>
										</select>
										<select name="speriod2" id="speriod2" style="font-size:10px;font-weight:bold;z-index:1">
										<?php for($i=0; $i < 60; $i++) { ?>
												<option value="<?=$i?>"<? echo ($i == $_POST['speriod2']) ? " selected" : ""?>><?=sprintf('%02d', $i)?></option>
										<?php } ?>
										</select>
									</td>

								</tr>
								<tr>
									<td valign="top" style="border: 0px;">&nbsp;</td>

									<td valign="top" colspan="3" style="border: 0px;">
										<input type="submit" value="Search" style="width:62px;height:21px;background-color:#FF6820;color:#E6E6FA;font-family:Arial;font-weight:bold;font-size:11px;z-index:1" />
										<input type="button" value="Export" onclick="javascript:page(0, 'export')" style="width:62px;height:21px;background-color:#FF6820;color:#E6E6FA;font-family:Arial;font-weight:bold;font-size:11px;z-index:1" />
									</td>
								</tr>

					</form>
                                                                    </td></tr>
							</table>

		<div id="dvresult"></div>
	</div>
         <div id="paneldiv" style="visibility:hidden"></div>
</div>
</div>
</body>