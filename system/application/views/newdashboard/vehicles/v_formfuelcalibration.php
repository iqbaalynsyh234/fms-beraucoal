<script type="text/javascript">
  function frmaddvehiclefuelcalibration(frm) {
    $("#loader").show();
    jQuery.post("<?=base_url();?>vehicles/savecalibration", jQuery("#frmfuelcalibration").serialize(),
      function(r) {
        $("#loader").hide();

        if (r.error) {
          alert(r.message);
          return;
        }

        if (confirm(r.message)) {
          window.location = '<?php echo base_url()?>vehicles';
        }else {
          window.location = '<?php echo base_url()?>vehicles';
        }
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
                <form id="frmfuelcalibration" onsubmit="javascript: return frmaddvehiclefuelcalibration(this)">
                  <input class="form-control" type="hidden" name="vehicle_id" id="vehicle_id" value="<?php echo $datavehicle[0]['vehicle_id'] ?>"/>
                    <table class="table" width="100%" cellpadding="3" style="font-size: 12px;">
                      <tr>
                        <td>Full Capacity (Ltr)</td>
                        <td>
                          <input type="text" class="form-control" name="vehicle_fuel_capacity" id="vehicle_fuel_capacity" value="<?php echo $datavehicle[0]['vehicle_fuel_capacity'] ?>">
                        </td>
                      </tr>

                      <tr>
                        <td>Voltage on Full Capacity</td>
                        <td>
                          <input type="text" class="form-control" name="vehicle_fuel_volt" id="vehicle_fuel_volt" value="<?php echo $datavehicle[0]['vehicle_fuel_volt'] ?>">
                        </td>
                      </tr>

                      <tr>
                        <td></td>
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
