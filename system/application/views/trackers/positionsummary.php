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
			jQuery("#date").datepicker(
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
			
		}
	);
	
function frmsummary_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>report/positionsummary", jQuery("#frmsummary").serialize(),
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
<form class="block-content" id="frmsummary" onsubmit="javascript:return frmsummary_onsubmit()" >	
<table>
	<tr>
		<td>Select vehicle : </td>
		<td>&nbsp;</td>
		<td>
		<select name="vehicle">
			<option value="0">All</option>
			<?php 
				for ($i=0;$i<count($myvehicles);$i++)
				{
			?>
				<option value="<?php echo $myvehicles[$i]->vehicle_device; ?>">
					<?php echo $myvehicles[$i]->vehicle_name." ".$myvehicles[$i]->vehicle_no ;?>
				</option>
			<?php
				}
			?>
		</select>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Date :</td>
		<td>&nbsp;</td>
		<td>
			<input size="10" value="<?=date('d-m-Y')?>" maxlength="10" type="text" name="date" id="date" class="date-pick" />
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Tanggal :</td>
		<td>&nbsp;</td>
		<td>
			<select style="font-size: 11px; width: 65px;" id="hour" name="hour">
						                
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
		<td colspan="2">
			<input class="button" type="submit" name="submit" value="PROSES"></submit>
			<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
		</td>
	</tr>
</table>
<iframe id="frmreq" style="display:none;"></iframe>
</form>
