<style media="screen">
  #master-unit{
    background-color: #1f50a2;
    color: white;
  }

</style>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
      <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading" id="master-unit">MASTER UNIT ITWS</header>
            <div class="panel-body" id="bar-parent10">
            <table id="example1" class="table table-striped" style="font-size: 12px; overflow-x:auto;">
              <thead>
                <tr>
                  <th>
                    No
                  </th>
                  <th>ID</th>
                  <th>Type</th>
                  <th>Merk</th>
                  <th>Model</th>
                  <th>Series</th>
                  <th>No lambung</th>
                  <th>No Mesin</th>
                  <th>No Rangka</th>
                  <th>Expired</th>
                  <th>Thn Pembuatan</th>
                  <th>Mitra Kerja</th>
                  <th>Department</th>
				  <th>Tare</th>
                  <th>RFID SPIP</th>
				  <th>RFID WIM</th>
				  <th>GPS Device</th>
                  <th>Status</th>
                  <th>Created Date</th>
                  <th>Updated Date</th>
                </tr>
              </thead>
                <tbody>
                  <?php for ($i=0; $i < sizeof($datafromportal); $i++) {?>
                    <tr>
                      <td><?php echo $i+1; ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_id'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_type'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_merk'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_model'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_series'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_nolambung'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_nomesin'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_norangka'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_expired'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_thnpembuatan'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_mitrakerja'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_department'] ?></td>
					  <td><?php echo $datafromportal[$i]['master_portal_gps_tare'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_norfid_sys'] ?></td>
					  <td><?php echo $datafromportal[$i]['master_portal_gps_rfid_wim'] ?></td>
					  <td><?php echo $datafromportal[$i]['master_portal_gps_vdevice'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_status'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_createddate'] ?></td>
                      <td><?php echo $datafromportal[$i]['master_portal_updateddate_new'] ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
  						</table>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
  $("#notifnya").fadeIn(1000);
  $("#notifnya").fadeOut(5000);
</script>
