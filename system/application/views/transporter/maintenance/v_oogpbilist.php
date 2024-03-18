<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
  <thead>
    <tr>
      <th>No</th>
      <th>Vehicle No / Vehicle Name</th>
      <th>Position</th>
      <th>GPS Time</th>
      <!-- <th>Action</th> -->
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; foreach ($dataoog as $rowdataoog) {?>
      <tr>
        <td><?php echo $no; ?></td>
      <td>
          <?php
           echo $rowdataoog['transporter_alert_vehicleno']." - ".$rowdataoog['transporter_alert_vehiclename'] ;
           ?>
      </td>
      <td>
        <?php
         echo $rowdataoog['transporter_alert_position'];
         ?>
         <br>
         <a href="http://maps.google.com/maps?z=12&t=m&q=loc:<?php echo $rowdataoog['transporter_alert_latitude']. "," .$rowdataoog['transporter_alert_longitude']?>" target="_blank">
           <?php
            echo $rowdataoog['transporter_alert_latitude']. "," .$rowdataoog['transporter_alert_longitude']
            ?>
         </a>
      </td>
      <td>
        <?php
         echo $rowdataoog['transporter_alert_gpstime'];
         ?>
      </td>
        <!-- <td>
          <a href="javascript:configthisvehicle(<?php echo $rowdataoog['transporter_alert_vehicledevice'];?>)">
            <img src="<?=base_url();?>assets/images/addvehicle.png" width="30px" height="30px" border="0" alt="<?php echo "Assign Vehicle"; ?>" title="Configuration Setup">
          </a>

          <a href="javascript:setservicess(<?php echo $rowdataoog['transporter_alert_vehicledevice'];?>)">
            <img src="<?=base_url();?>assets/images/learning_profile.png" width="30px" height="30px" border="0" alt="<?php echo "Assign Vehicle"; ?>" title="Set Servicess">
          </a>
        </td> -->
      </tr>
    <?php $no++; } ?>
  </tbody>
</table>
