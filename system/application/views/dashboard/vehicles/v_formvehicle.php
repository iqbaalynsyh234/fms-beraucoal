<script type="text/javascript">
function frmaddvehicle_onsubmit(frm) {
  $("#loader").show();
  jQuery.post("<?=base_url();?>vehicles/savevehicle", jQuery("#frmaddvehicle").serialize(),
    function(r) {
      $("#loader").hide();
      if (r.error) {
        alert(r.message);
        return;
      }

      if (confirm(alert(r.message))) {
        window.location = '<?php echo base_url()?>vehicles';
      }
      jQuery("#dialog").dialog('close');
      page(0);
    }, "json"
  );
  return false;
}

function vehicle_image_onchange() {
  jQuery.post("<?=base_url();?>vehicles/getimage", {
      vimage: jQuery("#vehicle_image").val()
    },
    function(r) {
      if (r.error) {
        alert(r);
        return;
      }

      jQuery("#dvvehicle_image").html(r.html);
    }, "json"
  );

}

function vehicle_type_onchange() {
  jQuery("#fuel").hide();
  var vtype = jQuery("#vehicle_type_2").val();

  if (vtype == 'T5 Fuel') {
    jQuery("#fuel").show();
  }
}

// FOR OPTION GROUP
function getsubcompanybyid(){
  // GET SUBCOMPANY BY COMPANY ID
  var branchoffice = $("#branchoffice").val();
  console.log("data : ", branchoffice);
  jQuery.post("<?=base_url()?>account/getsubcompanybyid", {id : branchoffice}, function(r){
      jQuery("#loader").hide();
      console.log("r : ", r);
      var size = r.data.length;
      var html = "";
            html += '<td>Sub Branch Office</td>';
              html += '<td>';
                html += '<select class="form-control" name="subbranchoffice" id="subbranchoffice" onchange="getcustomerbysubbranchofficeid();">';
                    html += '<option value="">--Select Subcompany--</option>';
                    html += '<option value="empty">Empty</option>';
                    for (var i = 0; i < size; i++) {
                      html += '<option value="'+r.data[i].subcompany_id+'">'+r.data[i].subcompany_name+'</option>';
                    }
              html += '</select>';
            html += '</td>';

            html += '<td>Current Sub Branch Office</td>';
            html += '<td>';
              html += '<input type="text" class="form-control" name="cur_subbranchoffice_id" id="cur_subbranchoffice_id" value="<?php echo $subbranchoffice['subcompany_id'] ?>" hidden>';
              html += '<input type="text" class="form-control" name="cur_subbranchoffice_name" id="cur_subbranchoffice_name" value="<?php echo $subbranchoffice['subcompany_name'] ?>" readonly>';
            html += '</td>';

      $("#showthissubcompany").html(html);
      $("#showthissubcompany").show();
    }, "json");
}

function getcustomerbysubbranchofficeid(){
  // GET CUSTOMER BY SUBCOMPANY ID
  var subcompany = $("#subbranchoffice").val();
  console.log("data : ", subcompany);
  jQuery.post("<?=base_url()?>account/getcustomerbysubcompanyid", {id : subcompany}, function(r){
      jQuery("#loader").hide();
      console.log("r : ", r);
      var size = r.data.length;
      var html = "";
            html += '<td>Customer</td>';
              html += '<td>';
                html += '<select class="form-control" name="customer" id="customer" onchange="getsubcustomerbyid();">';
                  html += '<option value="">--Select Customer--</option>';
                  html += '<option value="empty">Empty</option>';
                  for (var i = 0; i < size; i++) {
                    html += '<option value="'+r.data[i].group_id+'">'+r.data[i].group_name+'</option>';
                  }
            html += '</select>';
          html += '</td>';

          html += '<td>Current Customer</td>';
          html += '<td>';
            html += '<input type="text" class="form-control" name="cur_customer_id" id="cur_customer_id" value="<?php echo $customer['group_id'] ?>" hidden>';
            html += '<input type="text" class="form-control" name="cur_customer_name" id="cur_customer_name" value="<?php echo $customer['group_name'] ?>" readonly>';
          html += '</td>';

      $("#showthiscustomer").html(html);
      $("#showthiscustomer").show();
    }, "json");
}

function getsubcustomerbyid(){
  // GET CUSTOMER BY SUBCOMPANY ID
  var customerid = $("#customer").val();
  console.log("data : ", customerid);
  jQuery.post("<?=base_url()?>account/getsubcustomerbysubcompanyid", {id : customerid}, function(r){
      jQuery("#loader").hide();
      console.log("r : ", r);
      var size = r.data.length;
      var html = "";
            html += '<td>Sub Customer</td>';
              html += '<td>';
                html += '<select class="form-control" name="subcustomer" id="subcustomer">';
                  html += '<option value="">--Select Sub Customer--</option>';
                  html += '<option value="empty">Empty</option>';
                  for (var i = 0; i < size; i++) {
                    html += '<option value="'+r.data[i].subgroup_id+'">'+r.data[i].subgroup_name+'</option>';
                  }
            html += '</select>';
          html += '</td>';

          html += '<td>Current Sub Customer</td>';
          html += '<td>';
            html += '<input type="text" class="form-control" name="cur_subcustomer_id" id="cur_subcustomer_id" value="<?php echo $subcustomer['subgroup_id'] ?>" hidden>';
            html += '<input type="text" class="form-control" name="cur_subcustomer_name" id="cur_subcustomer_name" value="<?php echo $subcustomer['subgroup_name'] ?>" readonly>';
          html += '</td>';

      $("#showthissubcustomer").html(html);
      $("#showthissubcustomer").show();
    }, "json");
}
</script>

<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<!-- <header class="panel-heading panel-heading-red">RESULT</header> -->
				<div class="panel-body" id="bar-parent10">
  				<div class="row">
  						<div class="col-lg-12 col-sm-12">
                <form id="frmaddvehicle" onsubmit="javascript: return frmaddvehicle_onsubmit(this)">
                  <input class="form-control" type="hidden" name="vehicle_id" id="vehicle_id" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_id; } ?>" />
                  <table width="100%" cellpadding="3" class="table" style="font-size: 12px;">
                    <tr>
                      <td width="160" style="display:none;"><?=$this->lang->line("lvehicle_device");?></td>
                      <td width="1" style="display:none;">:</td>
                      <td style="display:none";><input type="text" name="vehicle_device" id="vehicle_device" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_device; } ?>" class="formdefault" /></td>
                    </tr>
                    <?php if ($this->config->item('vehicle_type_fixed')) { ?>
                    <input class="form-control" type="hidden" name="vehicle_type" id="vehicle_type" value="<?php echo $this->config->item('vehicle_type_fixed'); ?>" />
                  <?php } else if (isset($vehicle)) { ?>
                    <input class="form-control" type="hidden" name="vehicle_type" id="vehicle_type" value="<?php echo $vehicle->vehicle_type; ?>" />
                <?php } else { ?>
                    <tr>
                      <td style="display:none";><?=$this->lang->line("lvehicle_type");?></td>
                      <td style="display:none";>:</td>
                      <td style="display:none";>
                        <select class="form-control" name='vehicle_type' id='vehicle_type_2' onchange="javascript:vehicle_type_onchange();">
                          <?php
                            $vehicle_type_admin = $this->config->item("vehicle_type_admin");
                            $vehicle_type_replace = $this->config->item("vehicle_type_replace");

                            foreach($this->config->item("vehicle_type") as $key=>$val) { ?>
                            <?php
                              if ($this->sess->user_type != 1)
                              {
                                if (is_array($vehicle_type_admin) && in_array($key, $vehicle_type_admin))
                                {
                                  continue;
                                }
                              }

                              if (! in_array($key, $this->config->item('vehicle_type_visible'))) continue;
                            ?>
                          <option value="<?php echo isset($vehicle_type_replace[$key]) ? $vehicle_type_replace[$key] : $key; ?>"<?php if (isset($vehicle) && (strtoupper($vehicle->vehicle_type) == strtoupper($key))) { echo " selected"; } ?>><?php echo $key; ?></option>
                          <?php } ?>
                        </select>
                      </td>
                  </tr>
                    <?php } ?>
                  <?php
                  if (isset($vehicle) && $vehicle->vehicle_type == 'T5 Fuel'){
                    $showfuel = "";
                  }else{
                    $showfuel = "style='display:none;'";
                  }
                  ?>

                  <tr <?=$showfuel?> id="fuel">
                      <td style="display:none";><?=$this->lang->line("lvehicle_fuel_capacity");?></td>
                      <td style="display:none";>:</td>
                      <td style="display:none";>
                        <select class="form-control" name='vehicle_fuel_capacity' id='vehicle_fuel_capacity'>
                          <option value="0">--Select Fuel Capacity--</option>
                          <?php

                            foreach($fuel as $f){
                              if (isset($vehicle) && $vehicle->vehicle_fuel_capacity == $f->fuel_tank_capacity){
                                $selected = "selected";
                              }else{
                                $selected = "";
                              }
                              echo "<option value='" . $f->fuel_tank_capacity ."' " . $selected . ">" . $f->fuel_tank_capacity . "L</option>";
                            }

                          ?>
                        </select>
                      </td>
                  </tr>
                  <?php if (count($companies)) { ?>
                    <tr>
                      <td>Branch Office</td>
                      <td>
                        <select class="form-control" name="branchoffice" id="branchoffice" onchange="getsubcompanybyid();">
                          <option value="0"><?php echo $this->lang->line("lprivate"); ?></option>
                            <?php foreach($companies as $company) { ?>
                              <option value="<?php echo $company->company_id; ?>"><?php echo $company->company_name; ?></option>
                            <?php } ?>
                        </select>
                      </td>

                      <td>Current Branch Office</td>
                      <td>
                        <input type="text" class="form-control" name="cur_branchoffice_id" id="cur_branchoffice_id" value="<?php echo $branchoffice['company_id'] ?>" hidden>
                        <input type="text" class="form-control" name="cur_branchoffice_name" id="cur_branchoffice_name" value="<?php echo $branchoffice['company_name'] ?>" readonly>
                      </td>
                    </tr>
                    <tr id="showthissubcompany" style="display: none;">

                    </tr>
                    <tr id="showthiscustomer" style="display: none;">

                    </tr>
                    <tr id="showthissubcustomer" style="display: none;">

                    </tr>
                  <?php } ?>

                <tr id="trowner">
                  <td><?=$this->lang->line("lusername");?></td>
                  <td>
                  <select class="form-control" name="vehicle_user_id" id="vehicle_user_id">
                    <?php for($i=0; $i < count($users); $i++) { ?>
                    <?php if ($users[$i]->user_type == 1) continue; ?>
                    <option value="<?php echo $users[$i]->user_id; ?>"<?php if (isset($owner) && ($owner == $users[$i]->user_id)) { echo " selected"; } ?>><?php echo $users[$i]->user_name; ?></option>
                    <?php } ?>
                  </select>
                  </td>
                      <td>No. Vehicle Bckp</td>
                      <td><input class="form-control" type="text" name="vehicle_no" id="vehicle_no" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_no; } ?>" class="formdefault" readonly/></td>
                    </tr>

                    <tr>
                      <td><?=$this->lang->line("lvehicle_name");?></td>
                      <td><input class="form-control" type="text" name="vehicle_name" id="vehicle_name" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_name; } ?>" class="formdefault" /></td>

                      <td><?=$this->lang->line("lvehicle_no") . " New";?></td>
                      <td><input class="form-control" type="text" name="vehicle_no_new" id="vehicle_no_new" class="formdefault" /></td>

                  <?php if (($this->sess->user_type == 1) || (($this->sess->user_type == 3) && ($this->sess->agent_canedit_vactive == 1))) { ?>
                      <td ><?=$this->lang->line("lexpire_date");?></td>
                      <td>
                          <table width="100%" cellpadding="3">
                            <tr>
                              <td><input type='text' name="vehicle_active_date1" id="vehicle_active_date1"  class="date-pick" value="<?php if (isset($vehicle)) { echo date('d/m/Y', $vehicle->vehicle_active_date1_t); } ?>"  maxlength='10'></td>
                              <td><?=$this->lang->line("luntil");?></td>
                              <td><input class="form-control" type='text' name="vehicle_active_date2" id="vehicle_active_date2"  class="date-pick" value="<?php if (isset($vehicle)) { echo date('d/m/Y', $vehicle->vehicle_active_date2_t); } ?>"  maxlength='10'></td>
                            </tr>
                          </table>
                      </td>
                    </tr>
                  <?php } ?>
                    <tr>
                      <td style="display:none";><?=$this->lang->line("lexpire_card_no");?></td>
                      <td style="display:none";><input class="form-control" type="text" name="vehicle_card_no" id="vehicle_card_no" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_card_no; } ?>" class="formdefault" /></td>
                      <td style="display:none";><?=$this->lang->line("lexpire_card_op");?></td>
                      <td style="display:none";><input class="form-control" type="card_op" name="vehicle_operator" id="vehicle_operator" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_operator; } ?>" class="formdefault" /></td>
                    </tr>

                    <tr>
                      <td style="display:none";><?=$this->lang->line("lexpire_card_expired_date");?></td>
                      <td style="display:none";><input class="form-control" type='text' name="vehicle_active_date" id="vehicle_active_date"  class="date-pick" value="<?php if (isset($vehicle)) { echo date('d/m/Y', $vehicle->vehicle_active_date_t); } ?>"  maxlength='10'></td>
                      <td><?=$this->lang->line("lmobil_image");?></td>
                      <td>
                        <select class="form-control" name='vehicle_image' id='vehicle_image' onchange="vehicle_image_onchange()">
                        <?php foreach($this->config->item('vehicle_image') as $key=>$val) { ?>
                        <option value="<?php echo $key; ?>"<?php if (isset($vehicle) && ($vehicle->vehicle_image == $key)) { echo " selected"; } ?>><?php echo $this->lang->line($val); ?></option>
                        <?php } ?>
                      </select><span id="dvvehicle_image"></span>
                      </td>
                    </tr>

                    <tr>
                      <td ><?=$this->lang->line("lodometer_init");?></td>
                      <td><input class="form-control" type='text' name="vehicle_odometer" id="vehicle_odometer"  class="formshort" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_odometer; } ?>"  maxlength='9'> <?php echo $this->lang->line('lkm'); ?></td>
                      <td><?=$this->lang->line("lmax_speed");?></td>
                      <td><input class="form-control" type='text' name="vehicle_maxspeed" id="vehicle_maxspeed"  class="formshort" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_maxspeed; } ?>"  maxlength='4'> <?php echo $this->lang->line('lkph'); ?></td>
                    </tr>

                    <tr>
                      <td><?=$this->lang->line("lmax_parking_time");?></td>
                      <td><input class="form-control" type='text' name="vehicle_maxparking" id="vehicle_maxparking"  class="formshort" value="<?php if (isset($vehicle)) { echo $vehicle->vehicle_maxparking; } ?>"  maxlength='4'> <?php echo $this->lang->line('lminute'); ?></td>
                      <td style="display:none";>Server</td>
                      <td style="display:none";>
                        <select class="form-control" name="vehicle_ip" id="vehicle_ip">
                      <?php foreach($this->config->item("SERVER_TRACKERS") as $key=>$val) { ?>
                      <option value="<?php echo $key; ?>"<?php echo (isset($vehicle) && ($key==$vehicle->vehicle_ip)) ? " selected" : "";?>><?php echo $val; ?></option>
                      <?php } ?>
                        </select>
                      </td>

                  <?php
                    if (count($drivers)) {
                    $appdosj = $this->config->item("app_dosj");
                    if (!$appdosj)
                    {
                  ?>
                        <td><?php echo "Driver"?></td>
                        <td>
                          <?php

                            $app_route = $this->config->item("app_route");
                            if (isset($app_route) && $app_route == 1)
                            {
                              foreach($drivers as $driver)
                              {
                                if ($vehicle->vehicle_id == $driver->driver_vehicle)
                                {
                                  echo $driver->driver_name;
                                }
                              }
                            }
                            else
                            {
                          ?>
                          <select class="form-control" name="driver" id="driver">
                            <option value="0"><?php echo "NONE"; ?></option>
                            <?php foreach($drivers as $driver) { ?>
                            <option value="<?php echo $driver->driver_id; ?>" <?php if (isset($vehicle) && ($vehicle->vehicle_id == $driver->driver_vehicle)) { echo "selected"; } ?>><?php echo $driver->driver_name; ?></option>
                            <?php } ?>
                          </select>
                          <?php } ?>
                        </td>
                      </tr>
                  <?php } } ?>

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
