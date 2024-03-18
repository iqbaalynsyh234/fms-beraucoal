<style media="screen">
#on-process{
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
      <div class="row">
        <div class="col-md-12" id="tablevehicles">
          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red" id="on-process">On Process Maintenance</header>
            <div class="panel-body" id="bar-parent10">
              <table class="table table-striped" id="example1" style="font-size:12px; width:100%;">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>StartDate</th>
                    <th>StartTime</th>
                    <th>Vehicle</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Info</th>
                    <th>Creator</th>
                    <th>Mode</th>
                    <?php
                      if ($privilegecode == 5 || $privilegecode == 6) {?>
                        <th>Control</th>
                      <?php }?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    if (sizeof($dataonprocess) > 0) {?>
                      <?php
                        for ($i=0; $i < sizeof($dataonprocess); $i++) {?>
                          <tr>
                            <td><?php echo $i+1 ?></td>
                              <?php
                                $breakdown_startdate = date("d-m-Y", strtotime($dataonprocess[$i]['breakdown_start_time']));
                                $breakdown_starttime = date("H:i", strtotime($dataonprocess[$i]['breakdown_start_time']));
                               ?>
                            <td>
                               <?php echo $breakdown_startdate; ?>
                            </td>
                            <td>
                              <?php echo $breakdown_starttime; ?>
                            </td>
                            <td><?php echo $dataonprocess[$i]['breakdown_vehicle_no']; ?></td>
                            <td><?php echo $dataonprocess[$i]['breakdown_type_name']; ?></td>
                            <td><?php echo $dataonprocess[$i]['breakdown_kat_name']; ?></td>
                            <td><?php echo $dataonprocess[$i]['breakdown_info']; ?></td>
                            <td><?php echo $dataonprocess[$i]['breakdown_creator_name']; ?></td>
                            <td><?php echo $dataonprocess[$i]['breakdown_mode']; ?></td>
                            <?php
                            if ($privilegecode == 5 || $privilegecode == 6) {
                             ?>
                            <td>
                              <?php
                                $creator_id = $dataonprocess[$i]['breakdown_creator_id'];
                                  if ($creator_id == $this->sess->user_company || $creator_id == $this->sess->user_id) {?>
                                    <button type="button" class="btn btn-warning" onclick="completeThisMaintenance('<?php echo $dataonprocess[$i]['breakdown_id'] ?>')">
                                      <span class="fa fa-edit"></span>
                                    </button>

                                    <button type="button" class="btn btn-danger" onclick="deleteThisBreakdown('<?php echo $dataonprocess[$i]['breakdown_id'] ?>')">
                                      <span class="fa fa-trash"></span>
                                    </button>
                                  <?php }  ?>
                            </td>
                            <?php } ?>
                          </tr>
                        <?php  } ?>
                    <?php }else {?>
                      <tr>
                        <td><?php echo "Data is empty"; ?></td>
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

<div id="modalMaintenanceShow" class="modalformaintenance">
  <div class="modal-content-maintenance">
    <div class="row">
      <div class="col-md-10">
        <p class="modalTitleforAll" id="modalStateTitle"></p>
      </div>
      <div class="col-md-2">
        <div class="closethismodalmaintenance btn btn-danger btn-sm">X</div>
      </div>
    </div>
      <div class="row">
        <div class="col-md-12">
          <div id="modalStateMaintenance">
            <div id="maintenancetype_content">
              <form class="form-horizontal" id="frmCompletedMaintenance" onsubmit="javascript:return completedthismaintenance();">
                <input type="hidden" name="breakdown_id" id="breakdown_id" value="">

                <table class="table table-striped" width="100%">
                  <tr>
                    <td>Completed Date Time</td>
                    <td>
                      <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                      </div>
                      <input type="hidden" id="dtp_input2" value="" />
                    </td>
                    <td>
                      <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                        <input class="form-control switch" size="5" type="text" readonly id="ehour" name="ehour" value="08:10" onclick="houronclick();">
                        <!-- value="<?=date("H:i",strtotime("23:59:59"))?>" -->
                        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                      </div>
                      <input type="hidden" id="dtp_input3" value="" />
                    </td>
                  </tr>

                  <tr>
                    <td></td>
                    <td></td>
                    <td>
                      <button type="submit" class="btn btn-success">Completed Now</button>
                    </td>
                  </tr>
                </table>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
function houronclick() {
  $(".switch").html("<?php echo date("Y F d ")?>");
}

  function completeThisMaintenance(breakdown_id){
    $("#modalStateTitle").html("Maintenance complete form");
    $("#breakdown_id").val(breakdown_id);
    modalformaintenance('modalMaintenanceShow');
  }

  function completedthismaintenance(){
    console.log("completed");
    if (confirm("Are you sure want to completed this breakdown ?")) {
      $.post("<?php echo base_url() ?>maintenance/completedonprocess", $("#frmCompletedMaintenance").serialize(), function(response){
        console.log("response completed : ", response);
        if (response.msg == "success") {
          if (confirm("Maintenance has been completed")) {
            window.location = '<?php echo base_url() ?>maintenance/onprocess';
          }else {
            if (confirm("Failed completed this maintenance")) {
              window.location = '<?php echo base_url() ?>maintenance/onprocess';
            }else {
              window.location = '<?php echo base_url() ?>maintenance/onprocess';
            }
          }
        }else if (response.msg == "failed2") {
          alert("Wrong Completed Date Time, please choose Date Time correctly");
        }
      }, "json");
      return false;
    }
  }

  function deleteThisBreakdown(breakdownid){
    if (confirm("Are you sure want to deleted this breakdown ?")) {
      $.post("<?php echo base_url() ?>maintenance/deleteBreakdown", {id : breakdownid}, function(response){
        console.log("response completed : ", response);
        if (response.msg == "success") {
          if (confirm("Maintenance has been deleted")) {
            window.location = '<?php echo base_url() ?>maintenance/onprocess';
          }else {
            if (confirm("Failed deleted this maintenance")) {
              window.location = '<?php echo base_url() ?>maintenance/onprocess';
            }else {
              window.location = '<?php echo base_url() ?>maintenance/onprocess';
            }
          }
        }
      }, "json");
      return false;
    }
  }
</script>
