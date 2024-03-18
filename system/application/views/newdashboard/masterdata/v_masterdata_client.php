<style media="screen">
#master-data{
  background-color: #1f50a2;
  color: white;
}
</style>

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
          <header class="panel-heading panel-heading-red" id="master-data">Master Data Client</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped" width="100%">
                <thead>
          				<tr>
          					<th>
                      <?php
                        if ($privilegecode == 3 || $privilegecode == 8) {?>

                        <?php }else {?>
                          <a class="btn btn-success btn-xs" href="<?php echo base_url() ?>masterdata/addClient" title="Add New Material">
                            <span class="fa fa-plus"></span>
                          </a>
                        <?php }?>
                      No
                    </th>
                    <th>ID Client</th>
          					<th>Client</th>
                    <th>Client Reg Date</th>
                    <th>Description</th>
                    <?php
                      if ($privilegecode == 3 || $privilegecode == 8) {?>

                      <?php }else {?>
                        <th>Control</th>
                      <?php }?>
          				</tr>
          			</thead>
                <tbody>
                  <?php if (sizeof($data_client) > 0) {
                    for ($i=0; $i < sizeof($data_client); $i++) {?>
                      <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $data_client[$i]['client_shortcut'] ?></td>
                        <td><?php echo $data_client[$i]['client_id'] ?></td>
                        <td><?php echo date("d-m-Y", strtotime($data_client[$i]['client_reg_date'])) ?></td>
                        <td><?php echo $data_client[$i]['client_description'] ?></td>
                        <?php if ($privilegecode == 3 || $privilegecode == 8) {?>

                        <?php }else {?>
                          <td>
                            <a class="btn btn-sm btn-success" href="<?php echo base_url();?>masterdata/editclient/<?php echo $data_client[$i]['client_no'];?>">
                              <!-- <img src="<?php echo base_url();?>assets/images/edit.gif" /> -->
                              <span class="fa fa-edit"></span>
                            </a>

                            <a class="btn btn-sm btn-danger" href="<?php echo base_url();?>masterdata/deleteClient/<?php echo $data_client[$i]['client_no'];?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')">
                              <!-- <img src="<?php echo base_url();?>assets/images/trash.gif" /> -->
                              <span class="fa fa-trash"></span>
                            </a>
                          </td>
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
