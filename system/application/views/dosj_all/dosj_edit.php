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
				jQuery("#ship_date").datepicker(
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
	
	/* function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj_all/saveedit_dosj", jQuery("#frmdosj").serialize(),
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
	} */
	
	function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj_all/saveedit_dosj", jQuery("#frmdosj").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				alert(r.message);
										
				if (r.error)
				{								
					return;									
				}								
					page();
					jQuery("#dialog").dialog("close");
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
		<form id="frmdosj" onsubmit="javascript: return frmdosj_onsubmit()">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				
                <input type="hidden" name="dosj_id" id="dosj_id" value="<?php echo $data->dosj_id; ?>" />
				
                <input type="hidden" name="dosj_no_awal" id="dosj_no_awal" value="<?php echo $data->dosj_no; ?>" />
                <input type="hidden" name="dosj_no_block_awal" id="dosj_no_block_awal" value="<?php echo $data->dosj_no_block; ?>" />
                <input type="hidden" name="dosj_no_mortar_awal" id="dosj_no_mortar_awal" value="<?php echo $data->dosj_no_mortar; ?>" />
				
                <input type="hidden" name="dosj_quantity_awal" id="dosj_quantity_awal" value="<?php echo $data->dosj_item_quantity; ?>" />
                <input type="hidden" name="dosj_quantity_mortar_awal" id="dosj_quantity_mortar_awal" value="<?php echo $data->dosj_item_quantity_mortar; ?>" />
				
                <input type="hidden" name="dosj_no" id="dosj_no" value="<?php if (isset($data)) { echo $data->dosj_no; } ?>" />
				
                <tr>
					<td style="text-align:left" colspan="2"><h2>EDIT <?php if (isset($data)) { echo $data->dosj_type; } ?> - Number ( <?php if (isset($data)) { echo $data->dosj_no; } ?> )</h2></td>
				</tr>
                <tr>
                    <td style="text-align:right">SO Number</td>
                    <td>
                        Block <input type="text" name="dosj_no_block" id="dosj_no_block" value="<?php if (isset($data)) { echo $data->dosj_no_block; } ?>" />
                        Mortar <input type="text" name="dosj_no_mortar" id="dosj_no_mortar" value="<?php if (isset($data)) { echo $data->dosj_no_mortar; } ?>" />
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right">SJP</td>
                    <td>
                        Block <input type="text" name="dosj_block_no" id="dosj_block_no" value="<?php if (isset($data)) { echo $data->dosj_block_no; } ?>" />
                        Mortar <input type="text" name="dosj_mortar_no" id="dosj_mortar_no" value="<?php if (isset($data)) { echo $data->dosj_mortar_no; } ?>" />
                    </td>
                </tr>
				<tr>
					<td style="text-align:right">Customer</td>
					<td>
						<select name="dosj_customer" id="dosj_customer" >
							<option value="0" >--Select Customer--</option>
						<?php
							foreach($customer as $cust)
							{
								if (isset($data) && $data->dosj_customer_id == $cust->group_id)
								{
									$selected = "selected"; 
								}
								else
								{
									$selected = "";
								}
								echo "<option value='" . $cust->group_id ."' " . $selected . ">" . $cust->group_name . "</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<!--<tr>
					<td style="text-align:right">Customer ( Excel )</td>
					<td><input type="text" name="dosj_customer_tmp" id="dosj_customer_tmp" value="<?php if (isset($data)) { echo $data->dosj_customer_tmp; } ?>" /></td>
				</tr>-->
				<tr>
					<td style="text-align:right">Item Desc.</td>
					<td>
						<!--<input type="text" name="dosj_item_desc" id="dosj_item_desc" value="<?php if (isset($data)) { echo $data->dosj_item_desc; } ?>" />-->
						<select name="dosj_item_desc" id="dosj_item_desc" >
						<?php if (isset($data->dosj_item_desc) && $data->dosj_item_desc == "Mortar + Block") { ?>
							<option value="Mortar + Block">Mortar + Block</option>
							<option value="Mortar">Mortar</option>
							<option value="Block">Block</option>
						<?php } ?>
						<?php if (isset($data->dosj_item_desc) && $data->dosj_item_desc == "Mortar") { ?>
							<option value="Mortar">Mortar</option>
							<option value="Block">Block</option>
							<option value="Mortar + Block">Mortar + Block</option>
						<?php } ?>
						<?php if (isset($data->dosj_item_desc) && $data->dosj_item_desc == "Block") { ?>
							<option value="Block">Block</option>
							<option value="Mortar + Block">Mortar + Block</option>
							<option value="Mortar">Mortar</option>
						<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Size</td>
					<td>
						PxLxT <input type="text" size="25" name="dosj_item_size" id="dosj_item_size" value="<?php if (isset($data)) { echo $data->dosj_item_size; } ?>" />
						<!--P <input type="text" size="5" name="dosj_item_panjang" id="dosj_item_panjang" onKeyPress="return numbersonly(this, event)" value="<?php if (isset($data)) { echo $data->dosj_item_panjang; } ?>" />-->
						<!--L <input type="text" size="5" name="dosj_item_lebar" id="dosj_item_lebar" onKeyPress="return numbersonly(this, event)" value="<?php if (isset($data)) { echo $data->dosj_item_lebar; } ?>" />-->
						<!--T <input type="text" size="5" name="dosj_item_tinggi" id="dosj_item_tinggi" onKeyPress="return numbersonly(this, event)" value="<?php if (isset($data)) { echo $data->dosj_item_tinggi; } ?>" />-->
					</td>
				</tr>
				<tr>
					<td style="text-align:right">Unit</td>
					<td>
                        <input type="text" name="dosj_item_unit" id="dosj_item_unit" value="<?php if (isset($data)) { echo $data->dosj_item_unit; } ?>" /> M3
                        <input type="text" name="dosj_item_unit_mortar" id="dosj_item_unit_mortar" value="<?php if (isset($data)) { echo $data->dosj_item_unit_mortar; } ?>" /> Sak
                    </td>
				</tr>
				<tr>
					<td style="text-align:right">Total Quantity</td>
					<td>
                        Block <input type="text" size="7" name="dosj_quantity" id="dosj_quantity"  value="<?php if (isset($data)) { echo $data->dosj_item_quantity; } ?>" />
                        Mortar <input type="text" size="7" name="dosj_quantity_mortar" id="dosj_quantity_mortar"  value="<?php if (isset($data)) { echo $data->dosj_item_quantity_mortar; } ?>" />
                    </td>
				</tr>
				<tr>
					<td style="text-align:right">Note</td>
					<td>
						<textarea name="dosj_note" id="dosj_note" cols="40" rows="3" ><?php if (isset($data)) { echo $data->dosj_note; } ?></textarea>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" id="submit" value="Save" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/dosj_all';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>