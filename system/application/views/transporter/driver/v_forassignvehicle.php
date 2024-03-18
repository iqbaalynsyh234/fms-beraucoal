<!-- <div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> -->
	<div id="main">
    <div class="block-border">
        <table width="100%" cellpadding="8" class="table sortable no-margin">
          <tr>
            <td>Driver ID</td>
            <td>
              <?php echo $driver_id; ?>
            </td>
          </tr>

          <tr>
            <td>Driver Name</td>
            <td>
							<?php echo $row->driver_name;?>
            </td>
          </tr>

					<tr>
						<td>Status</td>
						<td>
							 <?php echo $row2;?>
						</td>
					</tr>

          <tr>
            <td>Choose Vehicle</td>
            <td>
              <select name="vehicle_choosed" id="vehicle_choosed">
                <option value="">--Choose Vehicle--</option>
								<option value="makeavailable">Make Available</option>
                <?php foreach ($data_vehicle as $key => $value) {?>
                  <option value="<?php echo $value['vehicle_id']; ?>"><?php echo $value['vehicle_no']; ?> - <?php echo $value['vehicle_name']; ?></option>
                <? } ?>
              </select>
            </td>
          </tr>

          <tr>
            <td></td>
            <td>
              <div style="margin-left: 70%;">
                <input type="submit" name="submit" value="Assign" onclick="gotoassignvehicle()">
                <input type="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" />
              </div>
            </td>
          </tr>
        </table>
    </div>
  </div>
</div>

<script type="text/javascript">
  function gotoassignvehicle(){
    var vehicle = jQuery("#vehicle_choosed").val();
    if (vehicle == "") {
      alert("Vehicle Can Not Empty");
    }else {
      var driver_id   = '<?php echo $driver_id; ?>';
      var driver_name = '<?php echo $driver_name; ?>';
      var user_id     = '<?php echo $this->sess->user_id; ?>';

      var data = {driver_id : driver_id, driver_name, driver_name, user_id : user_id, vehicle_id : vehicle};
      jQuery.post('<?=base_url()?>transporter/driver/assignnow/', data, function(response){
        if (response.msg == "error") {
          if (confirm("Update Data Failed")) {
          }
        }else if (response.msg == "already") {
					alert("Vehicle Already Set To Another Driver. Please Select Another Vehicle");
        }else if (response.msg == "notalready") {
					alert("Please Refresh This Page");
        }else{
          console.log("Berhasil terima data : ", response);
          if (confirm("Update Data Success")) {
            window.location = '<?=base_url()?>transporter/driver';
          }
        }
      }, "json");
    }
  }
</script>
