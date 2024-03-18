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

		//var vehicle   = jQuery("#vehicle").val();
		var driver_idcard    = jQuery("#driver_idcard").val();
		var startdate = jQuery("#startdate").val();
		var shour     = jQuery("#shour").val();
		var enddate   = jQuery("#enddate").val();
		var ehour     = jQuery("#ehour").val();

		var data = {
			//vehicle				: vehicle,
			driver_idcard		: driver_idcard,
			startdate			: startdate,
			shour				: shour,
			enddate				: enddate,
			ehour				: ehour
		};

		//console.log("Ini Data : ", data);

		jQuery.post("<?=base_url();?>transporter/driver/report_rfid_result", data, function(r){
				console.log("respon : ", r);
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);
			}, "json");
	}

	function frmsearch_onsubmit()
	{
		jQuery("#loader").show();
		page(0);
		return false;
	}

	function searchOnSubmit(){

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
	
	#vehicle{
		z-index: 9999 !important;
	}

</style>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
	<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        <h1>RF ID History Report</h1>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />
			<table width="100%" cellpadding="3" cellspacing="5" class="tablelist" style="font-size: 12px;">
				<tr>
					<!--<td width="10%">Vehicle</td>
					<td>
						<select id="vehicle" name="vehicle" class="chosen" style="width:30%">
							<option value="0">All</option>
							<?php for($i=0; $i < count($vehicles); $i++) { ?>
							<option value="<?php echo $vehicles[$i]->vehicle_id; ?>"><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." - "; } ?><?php echo $vehicles[$i]->vehicle_name; ?> - <?php echo $vehicles[$i]->vehicle_no; ?></option>
							<?php } ?>
						</select>-->
					<td width="10%">Driver</td>
					<td>
						<select id="driver_idcard" name="driver_idcard" class="chosens" style="width:40%">
							<!--<option value="0">All</option>-->
							<?php for($i=0; $i < count($drivers); $i++) { ?>
							<option value="<?php echo $drivers[$i]->driver_idcard;?>"><?php echo $drivers[$i]->driver_name." (".$drivers[$i]->driver_idcard.")";?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr></tr>
				<tr id="filterdatestartend">
					<td width="10%">Date</td>
					<td>
						<input type='text' readonly name="startdate" id="startdate" class="date-pick" value="<?=date('Y/m/d')?>"  maxlength='10'>
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

						~ <input type='text' readonly name="enddate" id="enddate"  class="date-pick" value="<?=date('Y/m/d')?>"  maxlength='10'>
						<select class="textgray" style="font-size: 11px; width: 65px;" id="ehour" name="ehour">

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
					</td>
				</tr>

				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input class="btn_search2" id="btnsearchreport" type="submit" value="Search" />
					<!-- <input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" /> -->
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
<script type="text/javascript">
	$(".chosen").chosen();
	$(".chosens").chosen();
</script>