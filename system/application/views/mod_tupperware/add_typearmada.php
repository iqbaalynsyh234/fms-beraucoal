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
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>transporter/tupperware/save_typearmada", jQuery("#frmtype").serialize(),
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
            <form class="block-content form" id="frmtype" onsubmit="javascript: return frmtype_onsubmit(this)">				
                <h1><?php echo "Add Type Armada"; ?></h1>
                <table width="100%" cellpadding="3" class="tablelist">	
                    <tr style="border: 0px;">
                        <td width="100" style="border: 0px;">Type Armada</td>
                        <td style="border: 0px;">
                            <input type="text" name="typearmada_name" id="typearmada_name" />
                            <font color="red">*</font>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr style="border: 0px;">
                        <td width="100" style="border: 0px;">Description</td>
                        <td style="border: 0px;">
                            <input type="text" name="typearmada_description" id="typearmada_description" />
                            <font color="red">*</font>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr style="border: 0px;">
                        <td width="100" style="border: 0px;">Volume</td>
                        <td style="border: 0px;">
                            <input type="text" name="typearmada_volume" id="typearmada_volume" />
                            <font color="red">*</font>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr style="border: 0px;">
                        <td style="border: 0px;" colspan="2">
                            <input type="submit" name="btnsave" id="btnsave" value=" Save " />
                            <input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/tupperware/mn_type_armada';" />
                            <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
                        </td>
                    </tr>					
                </table>
            </form>
        </div>
    </div>
</div>
			

			