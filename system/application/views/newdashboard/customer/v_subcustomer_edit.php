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
      <div class="alert alert-success" id="notifnya" style="display: none;">
        <?php echo $this->session->flashdata('notif');?>
      </div>
      <?php }?>
        <div class="row">
          <div class="col-md-12">
              <div class="panel" id="panel_form">
                <header class="panel-heading panel-heading-blue">Edit Sub Customer</header>
                <div class="panel-body" id="bar-parent10">
              <form class="block-content form" id="frmgroup" onsubmit="javascript: return frmgroup_onsubmit(this)">
                <input class="form-control" type="text" name="subcustomer_id" id="subcustomer_id" value="<?php echo $datasubcustomer[0]['subgroup_id']?>" hidden>
                <table class="table">
                  <tr>
                    <td>Current Branch Office</td>
                    <td>
                      <input class="form-control" type="text" name="curbranchoffice" id="curbranchoffice" value="<?php echo $data_branchoffice[0]['company_name']?>" readonly>
                    </td>
                  </tr>

                  <tr>
                    <td>Current Sub Branch Office</td>
                    <td>
                      <input class="form-control" type="text" name="cursubbranchoffice" id="cursubbranchoffice" value="<?php echo $data_subbranchoffice[0]['subcompany_name']?>" readonly>
                    </td>
                  </tr>

                  <tr>
                    <td>Current Customer</td>
                    <td>
                      <input class="form-control" type="text" name="curcustomer" id="curcustomer" value="<?php echo $data_customer[0]['group_name']?>" readonly>
                    </td>
                  </tr>

                  <tr>
                    <td>Branch Office</td>
                    <td>
                      <select class="select2" name="company" id="company" onchange="getsubcompanybyid();">
                        <option value="">--Choose Branch Office--</option>
                        <?php for ($i=0; $i < sizeof($branchoffice); $i++) {?>
                          <option value="<?php echo $branchoffice[$i]->company_id;?>"><?php echo $branchoffice[$i]->company_name;?></option>
                        <?php } ?>
                      </select>
                    </td>
                  </tr>

                  <tr id="showthissubcompany" style="display: none;">

                  </tr>

                  <tr id="showthiscustomer" style="display: none;">

                  </tr>

                  <tr>
                    <td>Sub Customer Name</td>
                    <td>
                      <input class="form-control" type="text" name="subcustomername" id="subcustomername" value="<?php echo $datasubcustomer[0]['subgroup_name']?>">
                    </td>
                  </tr>
                </table>
                  <div class="text-right">
                    <input type="button" class="btn btn-warning" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>account/subcustomer';" />
                    <input type="submit" class="btn btn-success" name="btnsave" id="btnsave" value=" Update " />
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

  function btncancel() {
    $("#formaddcustomermaster").hide();
    $("#formtablecustomermaster").show();
  }

  // FOR DISABLE SUBMIT FORM
  $(window).keydown(function(event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });


  function frmgroup_onsubmit() {
    $.post("<?=base_url()?>account/updatesubcustomer", $("#frmgroup").serialize(),
      function(r) {
        if (r.error) {
          alert(r.message);
          return false;
        }

        alert(r.message);
        location = r.redirect;
      }, "json"
    );
    return false;
  }

  function getsubcompanybyid(){
    // GET SUBCOMPANY BY COMPANY ID
    var companyid = $("#company").val();
    console.log("data : ", companyid);
    jQuery.post("<?=base_url()?>account/getsubcompanybyid", {id : companyid}, function(r){
				jQuery("#loader").hide();
        $("#showthissubcompany").show();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Sub Branch Office</td>';
                html += '<td>';
                  html += '<select class="form-control" name="subcompany" id="subcompany" onchange="getcustomerbysubcompanyid();">';
                      html += '<option value="">--Select Subcompany--</option>';
                      html += '<option value="empty">Empty</option>';
                      for (var i = 0; i < size; i++) {
                        html += '<option value="'+r.data[i].subcompany_id+'">'+r.data[i].subcompany_name+'</option>';
                      }
                html += '</select>';
              html += '</td>';
        $("#showthissubcompany").html(html);
			}, "json");
  }

  function getcustomerbysubcompanyid(){
    // GET CUSTOMER BY SUBCOMPANY ID
    var subcompany = $("#subcompany").val();
    console.log("data : ", subcompany);
    jQuery.post("<?=base_url()?>account/getcustomerbysubcompanyid", {id : subcompany}, function(r){
				jQuery("#loader").hide();
        $("#showthiscustomer").show();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Customer</td>';
                html += '<td>';
                  html += '<select class="form-control" name="customer" id="customer">';
                    html += '<option value="">--Select Customer--</option>';
                    html += '<option value="empty">Empty</option>';
                    for (var i = 0; i < size; i++) {
                      html += '<option value="'+r.data[i].group_id+'">'+r.data[i].group_name+'</option>';
                    }
              html += '</select>';
            html += '</td>';
        $("#showthiscustomer").html(html);
			}, "json");
  }

</script>
