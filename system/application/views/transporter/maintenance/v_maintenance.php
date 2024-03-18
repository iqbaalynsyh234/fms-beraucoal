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

			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>')

			page(0);
		}
  );


	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#loader").show();

		jQuery.post("<?=base_url();?>transporter/maintenancemanagement/showvehicleonfirstpage/", jQuery("#frmsearch").serialize(),
			function(r)
			{
        console.log("response : ", r);
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);
				jQuery("#total").html(r.total);
			}
			, "json"
		);
	}


	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}

	function order(by)
	{
		if (by == jQuery("#sortby").val())
		{
			if (jQuery("#orderby").val() == "asc")
			{
				jQuery("#orderby").val("desc");
			}
			else
			{
				jQuery("#orderby").val("asc");
			}
		}
		else
		{
			jQuery("#orderby").val('asc')
		}

		jQuery("#sortby").val(by);
		page(0);
	}

  function setservicess(idnya){
		// console.log(idnya);
		showdialog();
		jQuery.post('<?php echo base_url(); ?>transporter/maintenancemanagement/forsetservicess/', {id: idnya},
			function(response)
			{
				showdialog(response.html, "Set Servicess for this Vehicle");
				// console.log(response);
			}
			, "json"
		);
	}

  function configthisvehicle(idnya){
		// console.log(idnya);
		showdialog();
		jQuery.post('<?php echo base_url(); ?>transporter/maintenancemanagement/forconfigservicess/', {id: idnya},
			function(response)
			{
        if (response.isirow > 0) {
          showdialog(response.html, "Update Configuration for this Vehicle");
        }else {
          showdialog(response.html, "Set Configuration for this Vehicle");
        }
				// console.log("response : ", response);
			}
			, "json"
		);
	}



</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<!-- <div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Maintenance Management</h1>
		</form>
		<br />
		</div> -->
    <a class="button" href="<?=base_url();?>transporter/mod_vehicle_maintenance/workshop"><font color="#0000ff">Manage Workshop</font></a>
    <a class="button" href="<?=base_url();?>transporter/maintenancemanagement/maintenanceshistory"><font color="#0000ff">Maintenance History</font></a>

		<div id="result"></div>
		<iframe id="frmreq" style="display:none;"></iframe>
	</div>
</div>
