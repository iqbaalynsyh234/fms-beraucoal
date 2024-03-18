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
	
	function frmtype_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/tupperware/saveedit_typearmada", jQuery("#frmtype").serialize(),
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
    <form id="frmtype" onsubmit="javascript: return frmtype_onsubmit(this)">		
        <table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
            <input type="hidden" name="id" id="id" value="<?php echo $data->typearmada_id; ?>" />
            <tr>
                <td style="text-align:left" colspan="2"><h2>Type Armada ( EDIT )</h2></td>
            </tr>
            <tr>
                <td style="text-align:right">Type Armada</td>
                <td>
                    <input type="text" name="typearmada_name" id="typearmada_name" value="<?php if (isset($data)) { echo $data->typearmada_name; } ?>" />
                </td>
            </tr>
            <tr>
                <td style="text-align:right">Description</td>
                <td>
                    <input type="text" size="50" name="typearmada_description" id="typearmada_description" value="<?php if (isset($data)) { echo $data->typearmada_description; } ?>" />
                </td>
            </tr>
            <tr>
                <td style="text-align:right">Volume</td>
                <td>
                    <input type="text" size="50" name="typearmada_volume" id="typearmada_volume" value="<?php if (isset($data)) { echo $data->typearmada_volume; } ?>" />
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="submit" id="submit" value="Save" />
                    <input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/tupperware/mn_type_armada';" />
                    <img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                </td>
            </tr>
        </table>
    </form>
</div>