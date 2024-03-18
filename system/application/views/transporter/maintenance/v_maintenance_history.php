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
    jQuery("#date").datepicker(
      {
            dateFormat: 'yy-mm-dd'
          , 	startDate: '1900/01/01'
          , 	showOn: 'button'
          //, 	changeYear: true
          //,	changeMonth: true
          , 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
          , 	buttonImageOnly: true
          ,	beforeShow:
              function()
              {
                jQuery('#ui-datepicker-div').maxZIndex();
              }
      }
    );

    jQuery("#enddate").datepicker(
      {
            dateFormat: 'yy-mm-dd'
          , 	startDate: '1900/01/01'
          , 	showOn: 'button'
          //, 	changeYear: true
          //,	changeMonth: true
          , 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
          , 	buttonImageOnly: true
          ,	beforeShow:
              function()
              {
                jQuery('#ui-datepicker-div').maxZIndex();
              }
      }
    );
  }
);

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
    jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
		jQuery("#loader").show();

		jQuery.post("<?=base_url();?>transporter/maintenancemanagement/showmaintenancehistory/", jQuery("#frmsearch").serialize(),
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
    jQuery("#loader").show();
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

</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
		<form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1>Maintenance History</h1>
      <table>
        <tr>
          <td>Servicess</td>
          <td>
            <select class="formdefault" name="selectservicess" id="selectservicess">
              <option value="all">All</option>
              <?php for ($i=0; $i < sizeof($servicetype); $i++) {?>
                <option value="<?php echo $servicetype[$i]['service_type_id'];?>">
                  <?php echo $servicetype[$i]['service_type'];?>
                </option>
                <?php } ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Vehicle</td>
          <td>
            <select class="formdefault" name="selectvehicle" id="selectvehicle">
              <option value="all">All</option>
              <?php for ($i=0; $i < sizeof($vehicle); $i++) {?>
                <option value="<?php echo $vehicle[$i]['vehicle_no'];?>">
                  <?php echo $vehicle[$i]['vehicle_no'];?> - <?php echo $vehicle[$i]['vehicle_name'];?>
                </option>
                <?php } ?>
            </select>
          </td>
        </tr>
        <tr id="filterdatestartend">
          <td width="10%">Date</td>
          <td>
            <input type="text" name="date" id="date" class="date-pick" value="<?php echo date("Y-m-d"); ?>"/>
            s/d
            <input type="text" name="enddate" id="enddate" class="date-pick" value="<?php echo date("Y-m-d"); ?>"/>
          </td>
          <td>
            <button type="submit" name="btnsearchhistory" id="btnsearchhistory">Search</button>
          </td>
        </tr>
      </table>
		</form>
		<br />
		</div>
		<div id="result"></div>
		<iframe id="frmreq" style="display:none;"></iframe>
	</div>
</div>
