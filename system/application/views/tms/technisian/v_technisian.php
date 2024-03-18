<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">
<style media="screen">
/* MODAL STYLE */
div#modalDeletetechnician {
  margin-top: 5%;
  margin-left: 45%;
  max-height: 300px;
  max-width: 400px;
  position: absolute;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
}

div#modalAssignTechnician {
  visibility:hidden;
  margin-top: 5%;
  margin-left: 45%;
  max-height: 300px;
  max-width: 400px;
  position: absolute;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
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
      <div class="col-md-12" id="formtabletechnicianmaster">
          <div class="card-box">
            <!-- <div class="card-header">
              <button type="button" class="btn btn-success btn-xs" onclick="showaddpool()">
                <span class="fa fa-plus"></span>
              </button>
            </div> -->

            <div class="card-body">
              <table class="table" class="display" class="full-width">
                  <thead>
                      <tr>
                          <th>
                            <button type="button" class="btn btn-success btn-xs" onclick="showaddtmstechnician()" title="Add New Substation">
                            <span class="fa fa-plus"></span>
                          </button>No
                          </th>
                          <th>technician Name</th>
                          <th>Phone</th>
                          <th>Vehicle</th>
                          <th>E-mail</th>
                          <th>Address</th>
                          <th>Option</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php $no = 1; foreach ($datatechnician as $rowdatatechnician) {?>
                      <tr>
                        <td class="text-center"><?php echo $no ?></td>
                        <td><?php echo $rowdatatechnician['technician_name'] ?></td>
                        <td><?php echo $rowdatatechnician['technician_phone'] ?></td>
                        <td>
                          <?php
                              for ($i=0; $i < sizeof($datavehicle); $i++) {
                                if ($rowdatatechnician['technician_vehicle_device'] == $datavehicle[$i]['vehicle_device']) {
                                  echo $datavehicle[$i]['vehicle_no'];
                                }
                              }
                           ?>
                        </td>
                        <td><?php echo $rowdatatechnician['technician_email'] ?></td>
                        <td>
                          <?php echo $rowdatatechnician['technician_address'] ?> <br>
                        </td>
                        <td>
                          <button type="button" class="btn btn-success" onclick="btnAssignVehicle('<?php echo $rowdatatechnician['technician_id'].'-'.$rowdatatechnician['technician_vehicle_device'];?>')" title="Assign Vehicle">
                            <span class="fa fa-list"></span>
                          </button>

                          <a type="button" class="btn btn-primary" href="<?php echo base_url();?>tms/tms_technician_edit/<?php echo $rowdatatechnician['technician_id'];?>" title="Edit Data">
                            <span class="fa fa-edit"></span>
                          </a>

                          <button type="button" class="btn btn-danger" onclick="btnDelete('<?php echo $rowdatatechnician['technician_id'];?>')" title="Delete Data">
                            <span class="fa fa-trash"></span>
                          </button>
                        </td>
                      </tr>
                    <?php $no++; } ?>
                  </tbody>
              </table>
            </div>
          </div>
      </div>

      <div class="col-md-12" id="formaddtechnicianmaster" style="display: none;">
        <div class="card-box">
          <div class="card-header">
            Add technician
          </div>

          <div class="card-body">
            <form class="form-horizontal" action="<?php echo base_url()?>tms/savetechnician" method="post" enctype="multipart/form-data">
              <table class="table table-striped table-hover">
                <tr>
                  <td>Technician Name</td>
                  <td>
                    <input type="text" class="form-control" id="technician_name" name="technician_name" placeholder="technician Name">
                  </td>
                </tr>

                <tr>
                  <td>Technician Phone</td>
                  <td>
                    <input type="number" class="form-control" id="technician_phone" name="technician_phone">
                  </td>
                </tr>

                <tr>
                  <td>Technician E-mail</td>
                  <td>
                    <input type="email" class="form-control" id="technician_email" name="technician_email">
                  </td>
                </tr>

                <tr>
                  <td>Technician License</td>
                  <td>
                    <input type="text" class="form-control" id="technician_license" name="technician_license">
                  </td>
                </tr>

                <tr>
                  <td>Technician Sex</td>
                  <td>
                    <select class="form-control" name="technician_sex" id="technician_sex">
                      <option value="M">Male</option>
                      <option value="F">Female</option>
                    </select>
                  </td>
                </tr>

                <tr>
                  <td>Technician Address</td>
                  <td>
                    <textarea name="technician_address" rows="8" cols="80" class="form-control"></textarea>
                  </td>
                </tr>
              </table>
              <div class="form-group text-right">
                  <div class="offset-md-3 col-md-9">
                      <button type="submit" class="btn btn-info">Save</button>
                      <button type="button" class="btn btn-default" onclick="btncancel()">Cancel</button>
                  </div>
              </div>
          </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="modalDeletetechnician" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Delete technician Master Data</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodallistoftechnician();">X</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="<?php echo base_url()?>tms/deletetechnician" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="iddelete" id="iddelete">
                  Are you sure want to delete this data?<br><br>
                  <div class="text-right">
                    <button type="button" name="button" onclick="btnCloseModal();">Cancel</button>
                    <button type="submit" name="button" class="btn btn-danger">Delete</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
  </div>
</div>

<div id="modalAssignTechnician">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Assign Vehicle To Technician</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="btnCloseModalTechnician();">X</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="<?php echo base_url()?>tms/assignvehicletotechnician" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="id_teknisi" id="id_teknisi">
                    <tr>
                      <td>Vehicle</td>
                      <td>
                        <select class="form-horizontal" name="vehicle" id="vehicle">
                          <option value="">--Choose Vehicle--</option>
                          <option value="0000">Make Available</option>
                          <?php foreach ($datavehicle as $rowvehicle) {?>
                            <option value="<?php echo $rowvehicle['vehicle_device']?>"><?php echo $rowvehicle['vehicle_no'].' - '.$rowvehicle['vehicle_name']?></option>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>
                    <br><br>
                  <div class="text-right">
                    <button type="button" name="button" onclick="btnCloseModalTechnician();">Cancel</button>
                    <button type="submit" name="button" class="btn btn-primary">Assign Now</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- <script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script> -->

<script type="text/javascript">
$("#notifnya").fadeIn(1000);
$("#notifnya").fadeOut(5000);
$("#vehicle").chosen();
// $("#modalAssignTechnician").hide();


function showaddtmstechnician(){
  $("#formaddtechnicianmaster").show();
  $("#formtabletechnicianmaster").hide();
}

function btncancel(){
  $("#formaddtechnicianmaster").hide();
  $("#formtabletechnicianmaster").show();
}

function btnDelete(id){
  $("#iddelete").val(id);
  $("#modalDeletetechnician").show();
}

function closemodallistoftechnician(){
  $("#modalDeletetechnician").hide();
}

function btnCloseModal(){
  $("#modalDeletetechnician").hide();
}

function btnAssignVehicle(idteknisi){
  $("#id_teknisi").val(idteknisi);
  $("#modalAssignTechnician").show();
  document.getElementById("modalAssignTechnician").style.visibility = "visible";
}

function btnCloseModalTechnician(){
  $("#modalAssignTechnician").hide();
}

function closemodallistoftechnician(){
  $("#modalDeletetechnician").hide();
}

// FOR DISABLE SUBMIT FORM BY ENTER BUTTON
jQuery(window).keydown(function(event){
  if(event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});
</script>
