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
				jQuery("#destination_date").datepicker(
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
	
	function frmco_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>mod_co/saveedit_co", jQuery("#frmco").serialize(),
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
	
	function numbersonly(myfield, e, dec)
	{
		var key;
		var keychar;
		
		if (window.event)
		key = window.event.keyCode;
		else if (e)
		key = e.which;
		else
		return true;
	
		keychar = String.fromCharCode(key);

		// control keys
		if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) )
		return true;

		// numbers
		else if ((("0123456789").indexOf(keychar) > -1))
		return true;

		// decimal point jump
		else if (dec && (keychar == "."))
		{
			myfield.form.elements[dec].focus();
			return false;
		}
		else
		return false;
	}
		
</script>
<div id="main_data">
		<form id="frmco" onsubmit="javascript: return frmco_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="destination_id" id="destination_id" value="<?php echo $data->destination_id; ?>" />
				<input type="hidden" name="destination_name1_bf" id="destination_name1_bf" value="<?php echo $data->destination_name1; ?>" />
				<tr>
					<td style="text-align:left" colspan="2"><h2>Manage No. CO ( EDIT )</h2></td>
				</tr>
				<tr>
					<td style="text-align:right">No. CO</td>
					<td>
						<input type="text" name="destination_name1" id="destination_name1" value="<?php if (isset($data)) { echo $data->destination_name1; } ?>" />
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Vehicle</td>
					<td>
						<select name="destination_vehicle" id="destination_vehicle">
							<option value="0" >--Select Type Armada--</option>
							<?php
							foreach($vehicle as $v)
							{
								if (isset($data) && $data->destination_vehicle == $v->vehicle_device)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $v->vehicle_device ."' " . $selected . ">" . $v->vehicle_no. "</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Date</td>
					<td>
						<input type="text" name="destination_date" id="destination_date" value="<?php if (isset($data)) { echo date("d-m-Y",strtotime($data->destination_date)); } ?>" class="date-pick" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>mod_co/menu';" />
						<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>