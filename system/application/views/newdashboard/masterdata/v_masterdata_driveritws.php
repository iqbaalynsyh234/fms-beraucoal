<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
  jQuery(document).ready(
    function() {
      jQuery("#export_xcel").click(function() {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      });
    }
  );
</script>
<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red">
            Master Data Driver ITWS
            <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm">Export Excel</button>
          </header>
          <div class="panel-body" id="bar-parent10">
            <div id="isexport_xcel" style="overflow-x: auto;">
              <table id="table_driveritws" class="table table-striped" width="100%" style="font-size:12px;">
                <thead>
          				<tr>
          					<th>
                      <?php
                        if ($privilegecode == 3 || $privilegecode == 8) {?>

                        <?php }else {?>
                          <!-- <a class="btn btn-success btn-xs" href="<?php echo base_url() ?>masterdata/addClient" title="Add New Material">
                            <span class="fa fa-plus"></span>
                          </a> -->
                        <?php }?>
                      No
                    </th>
                    <th>ID Driver</th>
                    <th>ID SIMPER</th>
          					<th>Driver</th>
                    <th>Contractor</th>
                    <th>Reg Date</th>
                    <th>Valid Date</th>
                    <?php
                      if ($privilegecode == 3 || $privilegecode == 8) {?>

                      <?php }else {?>
                        <!-- <th>Control</th> -->
                      <?php }?>
          				</tr>
          			</thead>
                <tbody>
                  <?php if (sizeof($datadriveritws) > 0) {
                    for ($i=0; $i < sizeof($datadriveritws); $i++) {?>
                      <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $datadriveritws[$i]['driveritws_id_driver'] ?></td>
                        <td><?php echo $datadriveritws[$i]['driveritws_id_simper'] ?></td>
                        <td><?php echo $datadriveritws[$i]['driveritws_driver_name'] ?></td>
                        <td><?php echo $datadriveritws[$i]['driveritws_company_name'] ?></td>
                        <td><?php echo date("d-m-Y", strtotime($datadriveritws[$i]['driveritws_driver_regdate'])) ?></td>
                        <td><?php echo date("d-m-Y", strtotime($datadriveritws[$i]['driveritws_valid'])) ?></td>
                        <?php if ($privilegecode == 3 || $privilegecode == 8) {?>

                        <?php }else {?>
                          <!-- <td>
                            <a class="btn btn-sm btn-success" href="<?php echo base_url();?>masterdata/editclient/<?php echo $datadriveritws[$i]['client_no'];?>">
                              <span class="fa fa-edit"></span>
                            </a>

                            <a class="btn btn-sm btn-danger" href="<?php echo base_url();?>masterdata/deleteClient/<?php echo $datadriveritws[$i]['client_no'];?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')">
                              <span class="fa fa-trash"></span>
                            </a>
                          </td> -->
                        <?php }?>
                      </tr>
                    <?php } } ?>
  							</tbody>
  						</table>
            </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $("#table_driveritws").DataTable( {
      // "scrollY": "200px",
      "scrollCollapse": true,
      "searching": false,
      "paging": false
  });

});
</script>
