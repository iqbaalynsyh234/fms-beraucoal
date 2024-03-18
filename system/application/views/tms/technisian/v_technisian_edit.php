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
      <div class="col-md-12">
        <div class="card-box">
          <div class="card-header">
            Edit technician
          </div>

          <div class="card-body">
            <form class="form-horizontal" action="<?php echo base_url()?>tms/updatetechnician" method="post" enctype="multipart/form-data">
              <input type="hidden" name="id" id="id" value="<?php echo $data[0]['technician_id']?>">
              <table class="table table-striped table-hover">
                <tr>
                  <td>Technician Name</td>
                  <td>
                    <input type="text" class="form-control" id="technician_name" name="technician_name" value="<?php echo $data[0]['technician_name']?>" placeholder="technician Name">
                  </td>
                </tr>

                <tr>
                  <td>Technician Phone</td>
                  <td>
                    <input type="number" class="form-control" id="technician_phone" name="technician_phone" value="<?php echo $data[0]['technician_phone']?>">
                  </td>
                </tr>

                <tr>
                  <td>Technician E-mail</td>
                  <td>
                    <input type="email" class="form-control" id="technician_email" name="technician_email" value="<?php echo $data[0]['technician_email']?>">
                  </td>
                </tr>

                <tr>
                  <td>Technician License</td>
                  <td>
                    <input type="text" class="form-control" id="technician_license" name="technician_license" value="<?php echo $data[0]['technician_licence']?>">
                  </td>
                </tr>

                <tr>
                  <td>Technician Sex</td>
                  <td>
                    <select class="form-control" name="technician_sex" id="technician_sex">
                      <?php
                      $sex = $data[0]['technician_sex'];
                        if ($sex = "M") {
                          $sexnya = "Male";
                        }else {
                          $sexnya = "Female";
                        }
                       ?>
                      <option value="<?php echo $data[0]['technician_sex'] ?>"><?php echo $sexnya ?></option>
                      <option value="M">Male</option>
                      <option value="F">Female</option>
                    </select>
                  </td>
                </tr>

                <tr>
                  <td>Technician Address</td>
                  <td>
                    <textarea name="technician_address" rows="8" cols="80" class="form-control"><?php echo $data[0]['technician_address'] ?></textarea>
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

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
$("#notifnya").fadeIn(1000);
$("#notifnya").fadeOut(5000);

// FOR DISABLE SUBMIT FORM
jQuery(window).keydown(function(event){
  if(event.keyCode == 13) {
    event.preventDefault();
    return false;
  }
});

function btncancel(){
  window.location = '<?php echo base_url()?>tms/technician';
}
</script>
