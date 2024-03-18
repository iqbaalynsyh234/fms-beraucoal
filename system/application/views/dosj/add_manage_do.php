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
	
	function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj/save_manage_do", jQuery("#frmdosj").serialize(),
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
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmdosj" onsubmit="javascript: return frmdosj_onsubmit(this)">				
			<h1><?php echo "Add SO ( Delivery Schedule )"; ?></h1>
			
			<table width="100%" cellpadding="3" class="tablelist">
			
				<!-- DO Number -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">SO Number</td>
					<td style="border: 0px;">
						<select name="dosj_no" id="dosj_no" >
							<option value="0">-- Select SO Number --</option>
							<?php 
								if (isset($dosj))
								{
									for ($i=0;$i<count($dosj);$i++)
									{
							?>
									<option value="<?php echo $dosj[$i]->dosj_no;?>">
										<?php echo $dosj[$i]->dosj_no;?>
									</option>
							<?php
									}
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				
                <tr><td>&nbsp;</td></tr>
				
				
				<!-- Vehicle -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Vehicle</td>
					<td>
						<select name="vehicle">
							<option value="0">--Select Vehicle--</option>
							<?php
								if ((isset($vehicle)) && count($vehicle)>0)
								{
								for($i=0;$i<count($vehicle);$i++)
								{
							?>
								<option value="<?php echo $vehicle[$i]->vehicle_id."#".$vehicle[$i]->vehicle_device."#".$vehicle[$i]->vehicle_name."#".$vehicle[$i]->vehicle_no;?>">
								<?php echo $vehicle[$i]->vehicle_name." ".$vehicle[$i]->vehicle_no;?></option>
							<?php
								}
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- Driver -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Driver</td>
					<td>
						<select name="driver">
							<option value="0">--Select Driver--</option>
							<?php
								if ((isset($driver)) && count($driver)>0)
								{
								for($i=0;$i<count($driver);$i++)
								{
							?>
								<option value="<?php echo $driver[$i]->driver_id;?>"><?php echo $driver[$i]->driver_name;?></option>
							<?php
								}
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- Quantity -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Quantity</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="quantity" id="quantity" class="formdefault" />
						<br />( On Delivery ) 
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Ship Date -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Ship Date</td>
					<td style="border: 0px;">
						<input size="10" maxlength="10" type="text" name="ship_date" id="ship_date" class="date-pick" />
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<!-- Cost On Delivery -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Cost On Delivery</td>
					<td style="border: 0px;">
						<select name="cost" id="cost">
						<option value="0" >--Select Destination--</option>
							<?php 
								if (isset($cost))
								{
									for($i=0;$i<count($cost);$i++)
									{
							?>
								<option value="<?php echo $cost[$i]->cost_id;?>">
									<?php echo $cost[$i]->destination_name." - ".$cost[$i]->cost_vehicle_type;?>
								</option>
							<?php
									}
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
				<tr>
					<td colspan="2">
						<font color="red">*</font> ) Harus Di Isi
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>
				
    			<tr style="border: 0px;">
						<td style="border: 0px;" colspan="2">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/dosj/mn_manage_do';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>					
				
				
				</table>
			</form>
		</div>
	</div>
</div>
			
