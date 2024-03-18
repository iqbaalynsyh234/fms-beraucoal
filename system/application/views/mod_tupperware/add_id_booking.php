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
				jQuery("#booking_date_in").datepicker(
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
		jQuery.post("<?=base_url()?>transporter/tupperware/save_id_booking", jQuery("#frmidbooking").serialize(),
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
	
	function loadbarcode()
	{
		jQuery("#barcode_detail").hide();
		jQuery.post("<?php echo base_url(); ?>transporter/tupperware/barcode_options", jQuery("#frmidbooking").serialize(),
			function(r)
			{
				if (r.empty)
				{
					jQuery("#barcode_detail").hide();
					return;
				}

				jQuery("#barcode_detail").show();
				jQuery("#barcode_detail").html(r.html);
			}
			, "json"
		);
	}
	
		
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<form class="block-content form" id="frmidbooking" onsubmit="javascript: return frmdosj_onsubmit(this)">				
			<input type="hidden" name="dosj_no" id="dosj_no" class="formdefault" />
            <h1><?php echo "Add ID Booking"; ?></h1>
			
			<table width="100%" cellpadding="3" class="tablelist">
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">
						ID Booking
					</td>
					<td style="border: 0px;">
						<select name="booking_id" id="booking_id" onchange="javascript:loadbarcode()">
						<option value="">--Select ID Booking--</option>
						<?php 
							if (isset($slcars))
							{
								for ($i=0;$i<count($slcars);$i++)
								{
						?>
								<option value="<?php echo $slcars[$i]->transporter_barcode;?>">
									<?php echo $slcars[$i]->transporter_barcode;?>
								</option>
						<?php
								}
							}
						?>
						</select>
						<!--<input type="text" name="booking_id" id="booking_id" />-->
						<font color="red">*</font>
					</td>
				</tr>
				
                <tr><td>&nbsp;</td></tr>
			</table>
			
			<div id="barcode_detail" style="display:none;">
			</div>
			</form>
		</div>
	</div>
</div>
			

			