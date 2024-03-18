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
		jQuery.post("<?=base_url()?>transporter/route/save", jQuery("#frmroute").serialize(),
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
			<form class="block-content form" id="frmroute" onsubmit="javascript: return frmroute_onsubmit(this)">				
            <h1><?php echo "Add Route"; ?></h1>
			<table width="100%" cellpadding="3" class="tablelist">
				<!-- DO Type -->
				<tr style="border: 0px;">
					<td width="100" style="border: 0px;">Route Name</td>
					<td style="border: 0px;">
						<input type="text" name="route_name" id="route_name" />
						<font color="red">*</font>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr style="border: 0px;">
					<td valign="top" align="top" style="text-align:top">Note</td>
					<td><input type="text" size="50" name="route_note" id="route_note" /></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
    			<tr style="border: 0px;">
					<td style="border: 0px;" colspan="2">
						<input class="button" type="submit" name="btnsave" id="btnsave" value=" Save " />
						<input class="button" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/route';" />
						<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
					</td>
				</tr>					
			</table>
			</form>
		</div>
	</div>
</div>	