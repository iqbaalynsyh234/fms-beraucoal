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
	
	function frmslcars_onsubmit()
	{
		jQuery("#loader2").show();
		jQuery.post("<?=base_url()?>transporter/tupperware/saveedit_slcars", jQuery("#frmslcars").serialize(),
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
    <form id="frmslcars" onsubmit="javascript: return frmslcars_onsubmit(this)">		
        <table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
            <input type="hidden" name="id" id="id" value="<?php echo $data->slcars_id; ?>" />
			<input type="hidden" name="slcars_code_bf" id="slcars_code_bf" value="<?php echo $data->slcars_code; ?>" />
            <tr>
                <td style="text-align:left" colspan="2"><h2>Data Transporter ( EDIT )</h2></td>
            </tr>
            <tr>
                <td style="text-align:right">Transporter Name</td>
                <td>
                    <input type="text" name="slcars_name" id="slcars_name" value="<?php if (isset($data)) { echo $data->slcars_name; } ?>" />
                </td>
            </tr>
            <tr>
                <td style="text-align:right">SLCARS</td>
                <td>
                    <input type="text" size="50" name="slcars_code" id="slcars_code" value="<?php if (isset($data)) { echo $data->slcars_code; } ?>" />
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="submit" name="submit" id="submit" value="Save" />
                    <input type="button" name="btn_cancel" id="btn_cancel" value="Cancel" onclick="location='<?=base_url()?>transporter/tupperware/mn_slcars';" />
                    <img id="loader2" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                </td>
            </tr>
        </table>
    </form>
</div>