<style media="screen">
  .table thead tr th {
    font-size: 10px;
    font-weight: 600;
  }
</style>

<script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
jQuery(document).ready(
		function()
		{
			jQuery("#export_xcel").click(function()
			{
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			});
		}
	);
</script>

    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
      <div class="row">
        <div class="col-md-12">
          <div class="panel" id="panel_form">
            <header class="panel-heading" style="background-color: #221f1f; color:white;">RESULT</header>
            <div class="panel-body" id="bar-parent10" style=" overflow-x:auto;">

              <div class="col-lg-4 col-sm-4">
								<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
							</div>

							<div id="isexport_xcel" style="overflow-x:auto;">
                  <table id="example1" class="table table-striped" style="font-size:10px;" >
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Vehicle</th>
						<th>Imei</th>
                        <th>Contractor</th>
                        <th>SID</th>
                        <th>Driver</th>
                        <th>Face Detected (WITA)</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (sizeof($data) < 1) {?>
                        <tr>
                          <td>Data is empty</td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                      <?php }else {?>
                        <?php for($i=0;$i<count($data);$i++) { ?>
                          <tr>
                            <td width="2%"><?=$i+1?></td>
                            <td><?=$data[$i]['change_driver_vehicle_no'];?></td>
							<td><?=$data[$i]['change_imei'];?></td>
                            <td><?=$data[$i]['change_driver_company_name'];?></td>
                            <td><?=strtoupper($data[$i]['change_driver_id']);?></td>
                            <td><?=$data[$i]['change_driver_name'];?></td>
                            <td><?=$data[$i]['change_driver_time'];?></td>
                          </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
            </div>
      		</div>
        </div>
      </div>
