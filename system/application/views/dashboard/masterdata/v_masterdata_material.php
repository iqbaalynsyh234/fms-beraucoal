<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content" style="width:160%;">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablecustomer">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Master Data Material</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped" style="width:100%;">
                <thead>
          				<tr>
          					<th>
                      <a class="btn btn-success btn-xs" href="<?php echo base_url() ?>masterdata/addMaterial" title="Add New Material">
                        <span class="fa fa-plus"></span>
                      </a>No
                    </th>
          					<th>ID Material</th>
                    <th>Material</th>
                    <th>Hauling</th>
                    <th>Coal</th>
                    <th>Material Reg Date</th>
                    <th>Description</th>
          					<th>Control</th>
          				</tr>
          			</thead>
                <tbody>
                  <?php if (sizeof($data_material) > 0) {
                    for ($i=0; $i < sizeof($data_material); $i++) {?>
                      <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $data_material[$i]['material_id'] ?></td>
                        <td><?php echo $data_material[$i]['material_name'] ?></td>
                        <td><?php echo $data_material[$i]['material_hauling'] ?></td>
                        <td><?php echo $data_material[$i]['material_coal'] ?></td>
                        <td><?php echo date("d-m-Y", strtotime($data_material[$i]['material_reg_date'])) ?></td>
                        <td><?php echo $data_material[$i]['material_description'] ?></td>
                        <td>
                          <a href="<?php echo base_url();?>masterdata/editMaterial/<?php echo $data_material[$i]['material_no'];?>">
                            <img src="<?php echo base_url();?>assets/images/edit.gif" />
                          </a>

                          <a href="<?php echo base_url();?>masterdata/deleteMaterial/<?php echo $data_material[$i]['material_no'];?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')">
                            <img src="<?php echo base_url();?>assets/images/trash.gif" />
                          </a>
                        </td>
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
