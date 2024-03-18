<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
<!-- <link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.css"> -->
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">

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
							dateFormat: 'yy-mm-dd'
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
							dateFormat: 'yy-mm-dd'
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
		jQuery.post("<?=base_url();?>transporter/newritase/new_ritase_report", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);
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
		jQuery.post("<?=base_url();?>report/ritase_report_excel", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
				if(r.success == true){
					jQuery("#frmreq").attr("src", r.filename);
				}else{
					alert(r.errMsg);
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


</script>
<style media="screen">
	.ui-datepicker{
		z-index: 9999 !important;
	}

</style>
<div id="contoh">

</div>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<br />
	<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        <h1>Ritase Report</h1>
				<fieldset>
				 <table width="100%" class="tablelist" style="font-size: 12px;">
				 	<tr>
						<td>Vehicle</td>
						<td>
							<select id="vehicle" name="vehicle" class="chosen" style="width:200%;">>
								<?php for($i=0; $i < count($vehicle); $i++) { ?>
								<option value="<?php echo $vehicle[$i]->vehicle_device; ?>">
	                            <?php echo $vehicle[$i]->vehicle_no . " - " . $vehicle[$i]->vehicle_name; ?>
	                            </option>
								<?php } ?>
							</select>
						</td>
						<td>Ritase</td>
						<td>
							<select name="ritase" id="ritase"  class="chosen" style="width:80%;">
								<?php
									if (isset($geofence_name) && count($geofence_name)>0)
									{
										for ($i=0;$i<count($geofence_name);$i++)
										{
								?>
											<!-- <option value="<?php echo $ritase[$i]->ritase_id.",".$ritase[$i]->ritase_geofence_name;?>"><?php echo $ritase[$i]->ritase_geofence_name;?></option> -->
											<option value="<?php echo $geofence_name[$i]->geofence_name;?>"><?php echo $geofence_name[$i]->geofence_name;?></option>
								<?php
										}
									}
								?>
							</select>
							 <b>In</b>
							 <td>
								 <select name="ritaseout" id="ritaseout"  class="chosen" style="width:60%;">
									 <?php
										 if (isset($geofence_name) && count($geofence_name)>0)
										 {
											 for ($i=0;$i<count($geofence_name);$i++)
											 {
									 ?>
												 <!-- <option value="<?php echo $ritase[$i]->ritase_id.",".$ritase[$i]->ritase_geofence_name;?>"><?php echo $ritase[$i]->ritase_geofence_name;?></option> -->
												 <option value="<?php echo $geofence_name[$i]->geofence_name;?>"><?php echo $geofence_name[$i]->geofence_name;?></option>
									 <?php
											 }
										 }
									 ?>
								 </select>
									<b>Out</b>
							 </td>
						</td>
				 	</tr>
				 </table>
			 </fieldset>
				<fieldset>
					<table>
						<tr id="filterdatestartend">
							<td width="10%">Date</td>
							<td>
								<input type="text" name="date" id="date" class="date-pick" />
								s/d
								<input type="text" name="enddate" id="enddate" class="date-pick" />
							</td>
						</tr>

						<tr>
							<td>Time</td>
							<td>
								<select class="textgray" style="font-size: 11px; width: 65px;" id="shour" name="shour">
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
							s/d
									<select class="textgray" style="font-size: 11px; width: 65px;" id="ehour" name="ehour">
																				<option value="23:59">23:59</option>
																				<option value="22:00">22:00</option>
																				<option value="21:00">21:00</option>
																				<option value="20:00">20:00</option>
																				<option value="19:00">19:00</option>
																				<option value="18:00">18:00</option>
																				<option value="17:00">17:00</option>
																				<option value="16:00">16:00</option>
																				<option value="15:00">15:00</option>
																				<option value="14:00">14:00</option>
																				<option value="13:00">13:00</option>
																				<option value="12:00">12:00</option>
																				<option value="11:00">11:00</option>
																				<option value="10:00">10:00</option>
																				<option value="09:00">09:00</option>
																				<option value="08:00">08:00</option>
																				<option value="07:00">07:00</option>
																				<option value="06:00">06:00</option>
																				<option value="05:00">05:00</option>
																				<option value="04:00">04:00</option>
																				<option value="03:00">03:00</option>
																				<option value="02:00">02:00</option>
																				<option value="01:00">01:00</option>
																				<option value="00:00">00:00</option>
															 </select>
								</td>
						</tr>

						<tr><td>&nbsp;</td></tr>
						<tr>
							<td style="border: 0px;">&nbsp;</td>
							<td style="border: 0px;">
								<input class="btn_search2" id="btnsearchreport" type="submit" value="Search" />
								<!-- <input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" /> -->
		                    <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
							<!--input type="button" name="pdf" value="Export To PDF" onclick="javascript:return pdf_onsubmit()" /-->
							</td>
						</tr>
					</table>
				</fieldset>
		</form>
		<br />
		<div id="result"></div>
		<iframe id="frmreq" style="display:none;"></iframe>
	</div>
    </div>
</div>
<script type="text/javascript">
	$(".chosen").chosen();

</script>
