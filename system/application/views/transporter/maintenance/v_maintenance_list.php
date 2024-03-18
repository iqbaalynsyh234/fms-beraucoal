<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
  <thead>
    <tr>
      <th>No</th>
      <th>Vehicle No / Vehicle Name</th>
      <th>STNK Exp Date</th>
      <th>KIR Exp Date</th>
      <th>Serviced By</th>
      <th>Service Per (KM / Month)</th>
      <th>Alert Limit (KM / Month)</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; foreach ($vehicle as $vehiclenya) {?>
      <tr>
        <td><?php echo $no; ?></td>
        <!-- <td>
          <?php echo $vehicle[$i]['vehicle_no'] . " - " . $vehicle[$i]['vehicle_name']; ?>
        </td> -->
      <td>
          <?php
           echo $vehiclenya['vehicle_no']." - ".$vehiclenya['vehicle_name'] ;
           ?>
      </td>
      <td>
        <?php
        if (isset($vehicle_config)) {
          foreach ($vehicle_config as $v_config) {
            if ($v_config['maintenance_conf_vehicle_no'] == $vehiclenya['vehicle_no']) {
            echo date("d-m-Y", strtotime($v_config['maintenance_conf_stnkexpdate']));
            }
          }
        }
          ?>
      </td>

      <td>
        <?php
        if (isset($vehicle_config)) {
          foreach ($vehicle_config as $v_config) {
            if ($v_config['maintenance_conf_vehicle_no'] == $vehiclenya['vehicle_no']) {
              echo date("d-m-Y", strtotime($v_config['maintenance_conf_kirexpdate']));
            }
          }
        }
          ?>
      </td>

      <td>
        <?php
        if (isset($vehicle_config)) {
          foreach ($vehicle_config as $v_config) {
            if ($v_config['maintenance_conf_vehicle_no'] == $vehiclenya['vehicle_no']) {
            echo strtoupper($v_config['maintenance_conf_servicedby']);
            }
          }
        }
          ?>
      </td>

      <td>
        <?php
        if (isset($vehicle_config)) {
          foreach ($vehicle_config as $v_config) {
            if ($v_config['maintenance_conf_vehicle_no'] == $vehiclenya['vehicle_no']) {
            echo $v_config['maintenance_conf_valueservicedby'];
            }
          }
        }
          ?>
      </td>

      <td>
        <?php
        if (isset($vehicle_config)) {
          foreach ($vehicle_config as $v_config) {
            if ($v_config['maintenance_conf_vehicle_no'] == $vehiclenya['vehicle_no']) {
            echo $v_config['maintenance_conf_alertlimit'];
            }
          }
        }
          ?>
      </td>

        <td>
          <a href="javascript:configthisvehicle(<?php echo $vehiclenya['vehicle_id'];?>)">
            <img src="<?=base_url();?>assets/images/addvehicle.png" width="30px" height="30px" border="0" alt="<?php echo "Assign Vehicle"; ?>" title="Configuration Setup">
          </a>

          <a href="javascript:setservicess(<?php echo $vehiclenya['vehicle_id'];?>)">
            <img src="<?=base_url();?>assets/images/learning_profile.png" width="30px" height="30px" border="0" alt="<?php echo "Assign Vehicle"; ?>" title="Set Servicess">
          </a>
        </td>
      </tr>
    <?php $no++; } ?>
  </tbody>
</table>
