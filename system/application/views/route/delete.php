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
	
	function frmroute_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/route/delete_data", jQuery("#frmroute").serialize(),
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

<div id="main_data">
	<form id="frmroute" onsubmit="javascript: return frmroute_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<input type="hidden" name="route_id" id="route_id" value="<?php echo $data->route_id; ?>" />
			<tr>
				<td style="text-align:left"><h2>Delete Route ( <?php if (isset($data)) { echo $data->route_name; } ?> )</h2></td>
			</tr>
			<tr>
				<td style="text-align:left">Apakah anda yakin akan menghapus Data Ini !</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="submit" id="submit" value="Delete" />
					<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/route';" />
					<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
				</td>
			</tr>
		</table>
	</form>
</div>