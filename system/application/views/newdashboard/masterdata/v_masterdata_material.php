<style media="screen">
.modalMaterial{
  z-index: 9999;
  width: 100%;
  height: auto;
  margin: auto;
}

#master-material{
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
          <header class="panel-heading panel-heading-red" id="master-material">Master Data Material</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped" style="width:100%; font-size:11px;">
                <thead>
          				<tr>
          					<th>
                      <?php
                        if ($privilegecode == 3 || $privilegecode == 8) {?>

                        <?php }else {?>
                          <a class="btn btn-success btn-xs" href="<?php echo base_url() ?>masterdata/addMaterial" title="Add New Material">
                            <span class="fa fa-plus"></span>
                          </a>
                        <?php }?>
                      No
                    </th>
                    <th>ID Material</th>
          					<th>Material</th>
                    <th>Hauling</th>
                    <th>Coal</th>
                    <th>Geofence</th>
                    <th>Material Reg Date</th>
                    <th>Description</th>
                    <?php
                      if ($privilegecode == 3 || $privilegecode == 8) {?>

                      <?php }else {?>
                        <th>Control</th>

                      <?php }?>
          				</tr>
          			</thead>
                <tbody>
                  <?php if (sizeof($data_material) > 0) {
                    for ($i=0; $i < sizeof($data_material); $i++) {?>
                      <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $data_material[$i]['material_shortcut'] ?></td>
                        <td><?php echo $data_material[$i]['material_id'] ?></td>
                        <td><?php echo $data_material[$i]['material_hauling'] ?></td>
                        <td><?php echo $data_material[$i]['material_coal'] ?></td>
                        <td><?php echo $data_material[$i]['material_geofence'] ?></td>
                        <td><?php echo date("d-m-Y", strtotime($data_material[$i]['material_reg_date'])) ?></td>
                        <td><?php echo $data_material[$i]['material_description'] ?></td>
                        <?php
                          if ($privilegecode == 3 || $privilegecode == 8) {?>

                          <?php }else {?>
                            <td>
                              <button class="btn btn-warning btn-sm" type="button" data-toggle="modal" data-target="#materialModal" onclick="thismaterial('<?php echo $data_material[$i]['material_no'];?>','<?php echo $data_material[$i]['material_id'];?>')">
                                <span class="fa fa-info"></span>
                              </button>

                              <a class="btn btn-sm btn-success" href="<?php echo base_url();?>masterdata/editMaterial/<?php echo $data_material[$i]['material_no'];?>">
                                <!-- <img src="<?php echo base_url();?>assets/images/edit.gif" /> -->
                                <span class="fa fa-edit"></span>
                              </a>

                              <a class="btn btn-sm btn-danger" href="<?php echo base_url();?>masterdata/deleteMaterial/<?php echo $data_material[$i]['material_no'];?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')">
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

<div class="modal fade modalMaterial" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="materialModal" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="width:150%; margin-left: -20%; height:420px;">
      <div class="modal-header">
        <h5 class="modal-title" id="materialModal">
          <b>
            FORM
          </b>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal form" name="frmaddromtomaterial" id="frmaddromtomaterial" onsubmit="javascript:return frmromtomaterial_onsubmit()">
          <input type="text" name="material_no" id="material_no" class="form-control" hidden>
          <div class="row">
            <div class="col-lg-2 col-md-2">
              <h5 class="modal-title" id="materialModal">
                <b>
                  Material
                </b>
              </h5>
            </div>

            <div class="col-lg-3 col-md-3">
              <input type="text" name="material_ame" id="material_name" class="form-control" readonly>
            </div>

            <div class="col-lg-2 col-md-2">
              <h5 class="modal-title" id="materialModal">
                <b>
                  Select ROM
                </b>
              </h5>
            </div>
            <div class="col-lg-3 col-md-3">
              <select class="form-control select2" name="select_rom" id="select_rom">
                <?php
                if (isset($data_rom)) {
                  for ($i=0; $i < sizeof($data_rom); $i++) {?>
                    <option value="<?php echo $data_rom[$i]['street_name']; ?>"><?php echo $data_rom[$i]['street_name']; ?></option>
                  <?php }
                }
                 ?>
              </select>
            </div>

            <div class="col-lg-2 col-md-2">
              <button type="submit" class="btn btn-primary" name="button">Submit</button>
            </div>
          </div> <br>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function frmromtomaterial_onsubmit(){
    jQuery("#loader2").show();
    jQuery.post("<?= base_url(); ?>masterdata/romtomaterial", jQuery("#frmaddromtomaterial").serialize(),
      function(response) {
        console.log("response itws_update_data : ", response);
        if (response.error) {
          alert(response.message);
          jQuery("#loader2").hide();
        } else {
          if(confirm(response.message)){
            window.location = '<?php echo base_url() ?>masterdata/material';
          }else {
            window.location = '<?php echo base_url() ?>masterdata/material';
          }
        }
      }, "json"
    );
    return false;
  }

  function thismaterial(materialno, material_name){
    console.log("materialno : ", materialno);
    console.log("material_name : ", material_name);

    $("#material_no").val(materialno);
    $("#material_name").val(material_name);
  }
</script>
