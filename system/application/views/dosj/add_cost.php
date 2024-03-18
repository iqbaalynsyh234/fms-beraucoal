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
		}
	);
	
	function frmcost_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj/save_cost", jQuery("#frmcost").serialize(),
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
			<form class="block-content form" id="frmcost" onsubmit="javascript: return frmcost_onsubmit(this)">				
			<h1><?php echo "Add Cost"; ?></h1>
			
			<table width="100%" cellpadding="3" class="tablelist">
			
				<!-- Destination -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Destination</td>
					<td style="border: 0px;">
						<select name="cost_destination" id="cost_destination" >
							<option value="0">--Select Destination--</option>
							<?php 
								if (isset($destination))
								{
									for ($i=0;$i<count($destination);$i++)
									{
							?>
								<option value="<?php echo $destination[$i]->destination_id;?>" ><?php echo $destination[$i]->destination_name;?></option>
							<?php
									}
								}
							?>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
				
                <tr><td>&nbsp;</td></tr>
				
				<!-- Vehicle Type-->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Vehicle Type</td>
					<td>
						<select name="cost_vehicle_type" id="cost_vehicle_type">
							<option value="COLT DIESEL">COLT DIESEL</option>
							<option value="FUSO">FUSO</option>
						</select>
						<font color="red">*</font>
					</td>
				</tr>
                
				<tr><td>&nbsp;</td></tr>
				
				<!-- COST -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Cost</td>
					<td style="border: 0px;">
						<input type="text" name="cost" id="cost" class="formdefault" onKeyPress="return numbersonly(this, event)" /><br />
						<small>Ex: xxxxxx ( Otomatis akan dikonversi ke xxx,xxx )</small>
					</td>
				</tr>
				
				<tr><td>&nbsp;</td></tr>

    			<tr style="border: 0px;">
						<td style="border: 0px;" colspan="2">
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/dosj/cost';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
				</tr>					
				
				
				</table>
			</form>
		</div>
	</div>
</div>
			
