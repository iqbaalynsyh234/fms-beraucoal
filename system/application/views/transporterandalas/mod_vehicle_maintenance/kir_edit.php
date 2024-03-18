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
			jQuery("#mobil_kir_active_date").datepicker(
				{
							dateFormat: 'yy-mm-dd'
						, 	startDate: '1900-01-01'
						, 	showOn: 'button'
						, 	changeYear: true
						,	changeMonth: true
						, 	buttonImage: '<?php echo base_url();?>assets/images/calendar.gif'
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
	
function frmedit_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/ppi_mod_vehicle_maintenance/kir_update/", jQuery("#frmedit").serialize(),	
			function(r)
			{
				jQuery("#loader").hide();
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

	
</script>
		<h2><?php echo "Vehicle"." ".$row->mobil_name." ".$row->mobil_no;?></h2>
		<form class="block-content form" name="frmedit" id="frmedit" method="post" onsubmit="javascript: return frmedit_onsubmit()">	
		<input type="hidden" name="mobil_id" id="mobil_id" value="<?php if(isset($row->mobil_id)) { echo $row->mobil_id; };?>" />		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<tr>
				<td colspan="6">
					<b>Profile</b>
					<br />
					<hr />
				</td>
			</tr>
			
			<tr>
				<td>Vehicle</td>
				<td>:</td>
				<td>
					<?=$row->mobil_name;?>
				</td>
				
				<td style="text-align:right">Vehicle No</td>
				<td width="2%">:</td>
				<td>
					<?=$row->mobil_no;?>
				</td>
			</tr>
			<tr>
				<td>KIR No</td>
				<td>:</td>
				<td>
					<?=$row->mobil_stnk_no;?>
				</td>
				
				<td style="text-align:right">KIR Exp. Date</td>
				<td width="2%">:</td>
				<td>
					<input type='text' readonly name="mobil_kir_active_date"  id="mobil_kir_active_date" maxlength='10' style="width:150px;" value="<?=isset($row) ? htmlspecialchars($row->mobil_kir_active_date, ENT_QUOTES) : "";?>" class="formdefault" />
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<input type="submit" name="submit" id="submit" value="Update" />
					<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;">
				</td>
			</tr>
		</table>
		</form>
		
