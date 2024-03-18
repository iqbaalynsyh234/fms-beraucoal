<style media="screen">
.modalMaterial{
  z-index: 9999;
  width: 100%;
  height: auto;
  margin: auto;
}

#master-data{
  background-color: #221f1f;
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
          <header class="panel-heading panel-heading-red" id="master-data">Master Data Overspeed Level</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped" style="width:100%; font-size:11px;">
                <thead>
          				<tr>
          					<th>
                      <?php
                        if ($privilegecode == 0 || $privilegecode == 1 || $privilegecode == 4 ) {?>
                          <a class="btn btn-success btn-xs" href="<?php echo base_url() ?>masterdata/addspeedlevel" title="Add New">
                            <span class="fa fa-plus"></span>
                          </a>
                        <?php }?>
                      No
                    </th>
                    <th>Level Name</th>
                    <th>Level Alias</th>
                    <th>Level Value</th>
                    <th>Level Value MIN</th>
                    <th>Level Value MAX</th>
                    <th>Level Sanksi Lubang</th>
                    <th>Level Sanksi Skors</th>
                    <th>Sanksi Detail</th>
                    <th>Sanksi Remarks</th>
                    <?php
                      if ($privilegecode == 0 || $privilegecode == 1 || $privilegecode == 4) {?>
                        <th>Control</th>
                      <?php }?>
          				</tr>
          			</thead>
                <tbody>
                  <?php if (sizeof($speedlevel) > 0) {
                    for ($i=0; $i < sizeof($speedlevel); $i++) {?>
                      <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $speedlevel[$i]['level_name'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_alias'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_value'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_value_min'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_value_max'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_sanksi_lubang'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_sanksi_skors'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_sanksi_detail'] ?></td>
                        <td><?php echo $speedlevel[$i]['level_remark'] ?></td>
                        <?php
                          if ($privilegecode == 0 || $privilegecode == 1 || $privilegecode == 4) {?>
                            <td>
                              <a class="btn btn-sm btn-success" href="<?php echo base_url();?>masterdata/editspeedLevel/<?php echo $speedlevel[$i]['level_id'];?>">
                                <!-- <img src="<?php echo base_url();?>assets/images/edit.gif" /> -->
                                <span class="fa fa-edit"></span>
                              </a>

                              <a class="btn btn-sm btn-danger" href="<?php echo base_url();?>masterdata/deletespeedLevel/<?php echo $speedlevel[$i]['level_id'];?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')">
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

<script type="text/javascript">

</script>
