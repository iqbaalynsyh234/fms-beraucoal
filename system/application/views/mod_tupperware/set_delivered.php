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
				jQuery("#set_delivered").datepicker(
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
		}
	);
	
	function frmset_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/tupperware/set_delivered", jQuery("#frmset").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
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
<div id="main_data">
		<form id="frmset" onsubmit="javascript: return frmset_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="id" id="id" value="<?php echo $data->id; ?>" />
				<input type="hidden" name="booking_id" id="booking_id" value="<?php echo $data->booking_id; ?>" />
				<tr>
					<td style="text-align:left" colspan="2"><h2>ID Booking ( Set To Delivered )</h2></td>
				</tr>
				<tr>
					<td style="text-align:right">ID Booking</td>
					<td>
						<?php if (isset($data)) { echo $data->booking_id; } ?>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Delivered Date</td>
					<td>
						<input type="text" name="set_delivered" id="set_delivered" class="date-pick" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Delivered Time</td>
					<td>
						<select name="delivered_time" id="delivered_time" >
							<?php 
								if (isset($timecontrol))
								{
									for ($i=0;$i<count($timecontrol);$i++)
									{
							?>
									<option value="<?php echo $timecontrol[$i]->time; ?>">
									<?php echo $timecontrol[$i]->time; ?>
									</option>
							<?php
									}
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Set to Delivered" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/tupperware/booking_id';" />
						<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>