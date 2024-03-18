<script type="text/javascript">
function frmmdt_onsubmit(frm) {
  $("#loader").show();
  jQuery.post("<?=base_url();?>vehicles/savemdt", jQuery("#frmmdt").serialize(),
    function(r) {
      $("#loader").hide();
      if (r.error) {
        alert(r.message);
        return;
      }

      if (confirm(r.message)) {
        window.location = '<?php echo base_url()?>vehicles';
      }
      jQuery("#dialog").dialog('close');
      page(0);
    }, "json"
  );
  return false;
}
</script>

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<!-- <header class="panel-heading panel-heading-red">RESULT</header> -->
				<div class="panel-body" id="bar-parent10">
  				<div class="row">
  						<div class="col-lg-12 col-sm-12">
                <form id="frmmdt" onsubmit="javascript: return frmmdt_onsubmit(this)">
                  <input class="form-control" type="hidden" name="vehicle_id" id="vehicle_id" value="<?php if (isset($vehicleid)) { echo $vehicleid; } ?>" />
                  <tr>
                    <td>MDT Imei</td>
                    <td>
                      <input type="text" name="vehicle_mdt" id="vehicle_mdt" class="form-control" value="<?php echo $mdtnow[0]['vehicle_mdt']; ?>">
                    </td>
                  </tr>
                    <tr>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>
                        <div class="text-right">
                          <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
                          <input class="btn btn-primary" type="submit" name="btnsave" id="btnsave" value="Save">
                        </div>
                      </td>
                    </tr>
                  </table>
                </form>
  						</div>
  				</div>
	      </div>
    </div>
	</div>
</div>
