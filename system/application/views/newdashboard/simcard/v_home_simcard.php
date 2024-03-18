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
          <header class="panel-heading panel-heading-red">DATA SIMCARD</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped" width="100%" style="font-size:12px;">
                <thead>
          				<tr>
          					<th>
                      <a class="btn btn-success btn-xs" href="<?php echo base_url() ?>simcard/addSimcard" title="Add New Data">
                        <span class="fa fa-plus"></span>
                      </a>
                      No
                    </th>
                    <th>Vehicle No</th>
                    <th>Simcard Type</th>
                    <th>Simcard No</th>
          					<th>Last Top Up Date</th>
                    <th>Expired Date</th>
                    <th>Action</th>
          				</tr>
          			</thead>
                <tbody>
                  <?php for ($i=0; $i < sizeof($data_simcard); $i++) {?>
                    <tr>
                      <td><?php echo $i+1 ?></td>
                      <td><?php echo $data_simcard[$i]['simcard_vehicle_no'].' '.$data_simcard[$i]['simcard_vehicle_name'] ?></td>
                      <td><?php echo $data_simcard[$i]['simcard_type'] ?></td>
                      <td><?php echo $data_simcard[$i]['simcard_number'] ?></td>
                      <td><?php echo $data_simcard[$i]['simcard_last_topup'] ?></td>
                      <td><?php echo $data_simcard[$i]['simcard_expired'] ?></td>
                      <td>
                        <button type="button" class="btn btn-danger btn-sm" name="button" onclick="deleteSimcard('<?php echo $data_simcard[$i]['simcard_id'] ?>')">
                          <span class="fa fa-trash"></span>
                        </button>
                      </td>
                    </tr>
                  <?php } ?>
  							</tbody>
  						</table>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function deleteSimcard(id){
    console.log("id : ", id);
    if (confirm("Anda yakin akan menghapus data ini ?")) {
      $.post("<?php echo base_url() ?>simcard/delete_simcard", {id : id}, function(response){
        if (r.error == true) {
          if (confirm(r.message)) {
            window.location = '<?php echo base_url() ?>simcard';
          }else {
            window.location = '<?php echo base_url() ?>simcard';
          }
        }else {

        }
      }, "json");
    }
  }
</script>
