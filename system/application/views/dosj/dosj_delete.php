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
	
	function frmdosj_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/dosj/delete_dosj", jQuery("#frmdosj").serialize(),
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
		<form id="frmdosj" onsubmit="javascript: return frmdosj_onsubmit(this)">		
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
				<input type="hidden" name="dosj_no" id="dosj_no" value="<?php echo $data->dosj_no; ?>" />
				<tr>
					<td style="text-align:left"><h2>Delete DO Number ( <?php if (isset($data)) { echo $data->dosj_no; } ?> )</h2></td>
				</tr>
				<tr>
					<td style="text-align:left">Apakah anda yakin akan menghapus DO No. <?php if (isset($data)) { echo $data->dosj_no_block." ".$data->dosj_no_mortar; } ?></td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="submit" id="submit" value="Delete" />
						<input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/dosj';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>
		</table>
		</form>
</div>