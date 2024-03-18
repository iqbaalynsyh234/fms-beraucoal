<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper" style="width:185%;">
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
              <header class="panel-heading panel-heading-blue">Edit Customer</header>
              <div class="panel-body" id="bar-parent10">
              <form class="block-content form" id="frmgroup" onsubmit="javascript: return frmgroup_onsubmit(this)">
                  <table width="100%" cellpadding="3" class="tablelist">
                    <?php if (isset($curcustomer)) { ?>
                      <input type="hidden" id="id" name="id" value="<?=$curcustomer[0]['group_id'];?>" />
                      <tr style="border: 0px;">
                        <td style="border: 0px;">Current Branch Office</td>
                        <td style="border: 0px;">
                          <input class="form-control" type="text" name="curbranchoffice" id="curbranchoffice" value="<?=$curbranchoffice[0]['company_name'];?>" readonly>
                        </td>
                      </tr>

                      <tr style="border: 0px;">
                        <td style="border: 0px;">Current Sub Branch Office</td>
                        <td style="border: 0px;">
                          <input class="form-control" type="text" name="cursubbranchoffice" id="cursubbranchoffice" value="<?=$cursubbranchoffice[0]['subcompany_name'];?>" readonly>
                        </td>
                      </tr>

                      <tr style="border: 0px;">
                        <td style="border: 0px;">ID Customer</td>
                        <td style="border: 0px;">
                          <?=$curcustomer[0]['group_id'];?>
                        </td>
                      </tr>
                      <?php } ?>
                        <tr style="border: 0px;">
                          <td width="100" style="border: 0px;">Branch Office</td>
                          <td style="border: 0px;">
                            <select class="form-control" name="nowbranchoffice" id="nowbranchoffice" onchange="getsubcompanybyid();">
                                <option value="<?=$curbranchoffice[0]['company_id'];?>"><?=$curbranchoffice[0]['company_name'];?></option>
                                <option value="empty">Empty</option>
                                <?php for ($i=0; $i < sizeof($allbranchoffice); $i++) {?>
                                  <option value="<?php echo $allbranchoffice[$i]->company_id; ?>"><?php echo $allbranchoffice[$i]->company_name; ?></option>
                                <?php } ?>
                            </select>
                          </td>
                        </tr>

                        <tr id="showthissubcompany">

                        </tr>

                        <tr style="border: 0px;">
                          <td width="100" style="border: 0px;">Customer</td>
                          <td style="border: 0px;">
                            <input type="text" class="form-control" name="groupname" id="groupname" value="<?=isset($curcustomer) ? htmlspecialchars($curcustomer[0]['group_name'], ENT_QUOTES) : " ";?>" class="formdefault" />
                          </td>
                        </tr>
                  </table>
                  <div class="text-right">
                    <input type="button" class="btn btn-warning" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>account/customer';" />
                    <input type="submit" class="btn btn-success" name="btnsave" id="btnsave" value=" Save " />
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
    $.post("<?=base_url()?>account/customerupdate", $("#frmgroup").serialize(),
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
    var companyid = $("#nowbranchoffice").val();
    console.log("data : ", companyid);
    jQuery.post("<?=base_url()?>account/getsubcompanybyid", {id : companyid}, function(r){
				jQuery("#loader").hide();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Subcompany</td>';
                html += '<td>';
                  html += '<select class="form-control" name="nowsubbranchoffice" id="nowsubbranchoffice">';
                      html += '<option value="">--Select Sub Branch Office--</option>';
                      html += '<option value="empty">Empty</option>';
                      for (var i = 0; i < size; i++) {
                        html += '<option value="'+r.data[i].subcompany_id+'">'+r.data[i].subcompany_name+'</option>';
                      }
                html += '</select>';
              html += '</td>';
        $("#showthissubcompany").html(html);
			}, "json");
  }
</script>
