<script>
	jQuery(document).ready(
		function()
		{
			showclock();
			
		}
	);

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
	
	function frmadd_onsubmit()
	{
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>andalas_config/update", jQuery("#frmadd").serialize(),	
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
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">	
		<h1>Edit Config</h1>
		
				<table width="90%" cellpadding="3" class="table sortable no-margin">
					
				<tr>
					<td colspan="3"><h2>Require Information</h2></td>
				</tr>
				
    			<input type="hidden" id="config_id" name="config_id" value="<?=isset($row) ? htmlspecialchars($row->config_id, ENT_QUOTES) : "";?>" />
				<input type="hidden" id="config_company" name="config_company" value="<?php echo $this->sess->user_company; ?>" />
				
				<tr>
					<td>Config Name</td>
					<td>:</td>
					<td><input type="text" size="35" name="config_name" id="config_name" value="<?=isset($row) ? htmlspecialchars($row->config_name, ENT_QUOTES) : "";?>" class="formdefault" /></span></td>
				</tr>	
    			
				<tr>
					<td>Alert Time</td>
					<td>:</td>
					<td>
						<input name="config_type" type="radio" value="1 Hour" <? if (( isset($row)) && ($row->config_type == '1 Hour')) { ?>checked<?php } ?>> 1 Hour </option>
						<input name="config_type" type="radio" value="2 Hour" <? if (( isset($row)) && ($row->config_type == '2 Hour')) { ?>checked<?php } ?>> 2 Hour </option>
					</td>
				</tr>
				
				<tr>
					<td>Status Active</td>
					<td>:</td>
					<td>
						<input type="checkbox" name="config_status" id="config_status" value="1" <?php if (isset($row) && ($row->config_status == 1)) { echo "checked"; } ?>/> Active 
					</td>
				</tr>
				
				<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="submit" id="submit" value=" Update " />
						
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>andalas_config';" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		
		</div>
	</div>
</div>
			
