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
	
	function frmrequest_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/pgn_carpool/save_process_request", jQuery("#frmrequest").serialize(),
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
    <form id="frmrequest" onsubmit="javascript: return frmrequest_onsubmit(this)">		
        <table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
            <input type="hidden" name="request_id" id="request_id" value="<?php echo $data->request_id; ?>" />
            <tr>
                <td style="text-align:left" colspan="2"><h2>Process Request</h2></td>
            </tr>
			<tr>
				<td colspan="2">Total Available Vehicle : <?php echo $total;?></td>
			</tr>
			<tr style="border: 0px;">
				<td width="100" style="border: 0px;">Select Vehicle</td>
				<td style="border: 0px;">
					<select name="request_car" id="request_car">
						<option value="0">--Select Avaliable Vehicles--</option>
						<?php 
							if(isset($vehicle)) 
							{
								for($i=0;$i<count($vehicle);$i++)
								{
						?>
						<option value="<?php echo $vehicle[$i]->pgn_vehicle_device;?>">
						<?php echo $vehicle[$i]->pgn_vehicle_no." ".$vehicle[$i]->pgn_vehicle_name;?>
						</option>
						<?php
								}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
                <td style="text-align:left">Driver</td>
				<td style="border: 0px;">
					<input type="text" name="request_driver" id="request_driver" />
				</td>
            </tr>
			<tr><td>&nbsp;</td></tr>
			<tr style="border: 0px;">
			<td style="border: 0px;" colspan="2">
			<input type="submit" name="btnsave" id="btnsave" value=" Save " />
			<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/pgn_carpool/mn_request';" />
			<img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
			</td>
			</tr>					
        </table>
    </form>
</div>