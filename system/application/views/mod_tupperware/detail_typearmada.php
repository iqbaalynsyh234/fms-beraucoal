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
</script>

<div id="main_data">
    <form id="frmdist">		
        <table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
            <tr>
                <td style="text-align:center" colspan="2">
                    <h2>TYPE ARMADA ( DETAIL )</h2>
                </td>
            </tr>
            <tr>
                <td style="text-align:left">Type Armada</td>
                <td><?php if (isset($data)) { echo $data->typearmada_name; } ?></td>
            </tr>
            <tr>
                <td style="text-align:left">Description</td>
                <td><?php if (isset($data)) { echo $data->typearmada_description; } ?></td>
            </tr>
            <tr>
                <td style="text-align:left">Volume</td>
                <td><?php if (isset($data)) { echo $data->typearmada_volume; } ?></td>
            </tr>
        </table>
    </form>
</div>