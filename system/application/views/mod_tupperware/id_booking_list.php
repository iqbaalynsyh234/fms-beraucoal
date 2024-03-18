<script type="text/javascript" src="<?=base_url();?>assets/kopindosat/js/ajaxfileupload.js"></script>
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
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
			
			jQuery("#startdate_loading").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
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
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
				);	
			
			jQuery("#enddate_loading").datepicker(
				{
							dateFormat: 'dd-mm-yy'
						//, 	showOn: 'button'
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
			field_onchange();
			page(0);			
		}
	);
	
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#loader").show();
		
		jQuery.post("<?=base_url();?>transporter/tupperware/search_id_booking/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);		
				jQuery("#total").html(r.total);				
			}
			, "json"
		);
	}
	
	function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#booking_vehicle").hide();
		jQuery("#booking_driver").hide();
		jQuery("#lbldate").hide();
		jQuery("#lbldate_loading").hide();
		jQuery("#startdate").hide();
		jQuery("#startdate_loading").hide();
		jQuery("#enddate").hide();
		jQuery("#enddate_loading").hide();
		jQuery("#booking_delivery_status").hide();
		jQuery("#lbltime").hide();
		jQuery("#lbltime_loading").hide();
		jQuery("#starttime").hide();
		jQuery("#starttime_loading").hide();
		jQuery("#lbltimesd").hide();
		jQuery("#lbltimesd_loading").hide();
		jQuery("#endtime").hide();
		jQuery("#endtime_loading").hide();
		jQuery("#booking_company").hide();
		jQuery("#booking_loading").hide();
		switch(v)
		{
			case "booking_vehicle":
				jQuery("#booking_vehicle").show();
				break;
			case "booking_driver":
				jQuery("#booking_driver").show();
				break;
			case "booking_date_in":
				jQuery("#lbldate").show();
				jQuery("#startdate").show();
				jQuery("#enddate").show();
				break;
			case "booking_delivery_status":
				jQuery("#booking_delivery_status").show();
				break;
			case "booking_datetime_in":
				jQuery("#lbltime").show();
				jQuery("#starttime").show();
				jQuery("#lbltimesd").show();
				jQuery("#endtime").show();
				jQuery("#startdate").show();
				jQuery("#enddate").show();
				jQuery("#lbldate").show();
			break;
			case "booking_loading_date":
				jQuery("#lbltime_loading").show();
				jQuery("#starttime_loading").show();
				jQuery("#lbltimesd_loading").show();
				jQuery("#endtime_loading").show();
				jQuery("#startdate_loading").show();
				jQuery("#enddate_loading").show();
				jQuery("#lbldate_loading").show();
			break;
			case "booking_loading":
				jQuery("#booking_loading").show();
			break;
			case "booking_company":
				jQuery("#booking_company").show();
			break;
			default:
				jQuery("#keyword").show();			
		}
	}
	
	function frmsearch_onsubmit()
	{
		page(0);
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
	
	function edit(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>transporter/tupperware/id_booking_edit/', {id: v},
		function(r)
		{
			showdialog(r.html, "ID Booking Edit");
		}
		, "json"
		);
	}
	
	function detail(v)
	{
		showdialog();
		jQuery.post('<?php echo base_url(); ?>transporter/tupperware/id_booking_detail/', {id: v},
		function(r)
		{
			showdialog(r.html, "ID Booking Detail");
		}
		, "json"
		);
	}
	
	
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>transporter/tupperware/delete_id_booking/' + id, {}, function(r){
					if (r.error) {
						alert(r.message);
						return;
					}else{
						alert(r.message);
						page();
						return;
					}
				}, "json");
			}		
		}
		
	function set_delivered(v)
		{
			showdialog();
			jQuery.post('<?php echo base_url(); ?>transporter/tupperware/mn_set_delivered/', {id: v},
			function(r)
			{
				showdialog(r.html, "ID Booking - Set To Delivered");
			}
			, "json"
			);
		}
	
	function set_loading(v)
		{
			showdialog();
			jQuery.post('<?php echo base_url(); ?>transporter/tupperware/mn_set_loading/', {id: v},
			function(r)
			{
				showdialog(r.html, "ID Booking - Set To Loading");
			}
			, "json"
			);
		}
	
	function excel_onsubmit(){
		jQuery("#loader").show();
		
		jQuery.post("<?=base_url();?>report/export_id_booking", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
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
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		[ <a href="<?=base_url();?>transporter/tupperware/add_id_booking"><font color="#0000ff">Add ID Booking</font></a> ]
		<br /><br /><br />
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>ID Booking List (<span id="total"></span>)</h1>
		<fieldset class="grey-bg required">
		<legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">
							<option value="booking_id">ID Booking</option>
							<option value="booking_destination">Destination</option>
							<option value="booking_armada_type">Type Armada</option>
							<option value="booking_cbm_loading">CBM Loading</option>
							<option value="booking_vehicle">Vehicle</option>
							<option value="booking_driver">Driver</option>
							<option value="booking_date_in">Date</option>
							<option value="booking_time_in">Time</option>
							<option value="booking_warehouse">Tujuan Gudang</option>
							<option value="booking_delivery_status">Status</option>
							<option value="booking_datetime_in">Date Time (In)</option>
							<option value="booking_loading">Status Loading</option>
							<option value="booking_loading_date">Loading Date</option>
							<?php 
								if ($this->config->item("app_tupperware"))
								{
							?>
								<option value="booking_company">Transporter</option>
							<?php
								}
							?>
						</select>
						<select id="booking_vehicle" name="booking_vehicle" style="display:none;">
							<option value="0">-Select Vehicle-</option>
							<?php 
								if (isset($vehicle))
								{
									for($i=0;$i<count($vehicle);$i++)
									{
							?>
								<option value="<?php echo $vehicle[$i]->vehicle_device; ?>">
									<?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no; ?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<select id="booking_driver" name="booking_driver" style="display:none;">
							<option value="0">-Select Driver-</option>
							<?php 
								if (isset($driver))
								{
									for($i=0;$i<count($driver);$i++)
									{
							?>
								<option value="<?php if (isset($driver[$i]->driver_id)) { echo $driver[$i]->driver_id; } ?>">
									<?php if (isset($driver[$i]->driver_name)) { echo $driver[$i]->driver_name; } ?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<select name="booking_delivery_status" id="booking_delivery_status" style="display:none;">
							<option value="1">Active</option>
							<option value="2">Delivered</option>
						</select>
						<select name="booking_loading" id="booking_loading" style="display:none;">
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>
						<select name="booking_company" id="booking_company" style="display:none;">
							<option value="0">-Select Transporter-</option>
							<?php 
								if (isset($company))
								{
									for($i=0;$i<count($company);$i++)
									{
							?>
								<option value="<?php echo $company[$i]->company_id; ?>">
									<?php echo $company[$i]->company_name; ?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="text" name="startdate" id="startdate" class="date-pick" style="display:none;"/>
						<span id="lbldate" name="lbldate" style="display:none;">s/d</span>
						<input type="text" name="enddate" id="enddate" class="date-pick" style="display:none;" />
						
						<input type="text" name="startdate_loading" id="startdate_loading" class="date-pick" style="display:none;"/>
						<span id="lbldate_loading" name="lbldate_loading" style="display:none;">s/d</span>
						<input type="text" name="enddate_loading" id="enddate_loading" class="date-pick" style="display:none;" />
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<span id="lbltime" name="lbltime" style="display:none;">Time ( In )</span>
						<select name="starttime" id="starttime" style="display:none;">
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
						<span id="lbltimesd" name="lbltimesd" style="display:none;">s/d</span>
						<select name="endtime" id="endtime" style="display:none;">
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
					<td>
						<span id="lbltime_loading" name="lbltime_loading" style="display:none;">Time</span>
						<select name="starttime_loading" id="starttime_loading" style="display:none;">
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
						<span id="lbltimesd_loading" name="lbltimesd_loading" style="display:none;">s/d</span>
						<select name="endtime_loading" id="endtime_loading" style="display:none;">
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
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
						<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
			</table>
		</fieldset>
		</form>		
		<br />
		</div>
		<div id="result"></div>		
		<iframe id="frmreq" style="display:none;"></iframe>
	</div>
</div>
