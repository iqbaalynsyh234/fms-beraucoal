<div class="sidebar-container">
  <?= $sidebar; ?>
</div>

<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) { ?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif'); ?></div>
    <?php } ?>
    <div class="row">
      <div class="col-md-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading" style="background-color:#221f1f;color:white;"> Edit Users</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
              <input class="form-control" type="hidden" id="id" name="id" value="<?= $row->user_id; ?>" />

              <div class="row">
                <div class="col-md-6">
                  <h4>
                    <b style="color: blue;">Private Information</b>
                  </h4>
                  <table class="table">
                    <tr>
                      <td colspan="3">User Name</td>
                      <td>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo $row->user_name ?>">
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">SID</td>
                      <td>
                        <input type="text" name="email" id="email" class="form-control" value="<?php echo $row->user_mail ?>">
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Gender</td>
                      <td>
                        <select class="form-control" id="sex" name="sex">
                          <?php
                          $sex = $row->user_sex;
                          if ($sex == 1) { ?>
                            <option value="1" selected>Male</option>
                            <option value="2">Female</option>
                          <?php } else { ?>
                            <option value="1">Male</option>
                            <option value="2" selected>Female</option>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Birthdate</td>
                      <td>
                        <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd/mm/yyyy" data-link-format="dd-mm-yy">
                          <?php
                          $birthdate = $row->user_birth_date;
                          if ($birthdate != 0) {
                            $birthdatefix = date("d/m/Y");
                          } else {
                            $birthdatefix = "";
                          }
                          ?>
                          <input class="form-control" class="form-control" type="text" name="birthdate" id="birthdate" value="<?php echo $birthdatefix; ?>" maxlength='10'>
                          <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Address</td>
                      <td>
                        <textarea class="form-control" name="address" id="address"><?php echo $row->user_address; ?></textarea>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Mobile</td>
                      <td>
                        <input type="number" name="mobile" id="mobile" class="form-control" value="<?php echo $row->user_mobile; ?>">
                      </td>
                    </tr>

                  </table>
                </div>

                <div class="col-md-6">
                  <h4>
                    <b style="color: blue;">Login Information</b>
                  </h4>
                  <table class="table">
                    <tr>
                      <td colspan="3">Privilege</td>
                      <td>
                        <select class="form-control" name="privilege" id="privilege" onchange="privilegeOnchange();">
                          <?php for ($i = 0; $i < sizeof($role_list); $i++) { ?>
                            <?php
                            $user_id_role = $row->user_id_role;
                            if ($user_id_role == $role_list[$i]['privilege_id']) { ?>
                              <option value="<?php echo $role_list[$i]['privilege_id'] ?>" selected><?php echo $role_list[$i]['privilege_name'] ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $role_list[$i]['privilege_id'] ?>"><?php echo $role_list[$i]['privilege_name'] ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>

                    <?php
                    if ($user_id_role == 5 || $user_id_role == 6) { ?>
                      <tr id="showhidepjo">
                      <?php } else { ?>
                      <tr id="showhidepjo" style="display:none;">
                      <?php }
                      ?>
                      <td colspan="3">Contractor</td>
                      <td>
                        <select class="form-control select2" name="pjolist" id="pjolist" style="width:100%;" onchange="pjolistonchange();">
                          <option value="0">--Select Contractor</option>
                          <?php for ($i = 0; $i < sizeof($pjo_list); $i++) { ?>
                            <?php
                            $companyuser = $row->user_company;
                            if ($companyuser == $pjo_list[$i]['company_id']) { ?>
                              <?php
                              $company_exca = $pjo_list[$i]['company_exca'];
                               ?>
                              <option value="<?php echo $pjo_list[$i]['company_id'] . '|' . $pjo_list[$i]['company_name']; ?>" selected><?php echo $pjo_list[$i]['company_name'] ?></option>
                            <?php } else { ?>
                              <?php
                              $company_exca = 0;
                               ?>
                              <option value="<?php echo $pjo_list[$i]['company_id'] . '|' . $pjo_list[$i]['company_name']; ?>"><?php echo $pjo_list[$i]['company_name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </td>
                      </tr>

                      <!-- <tr>
            					<td colspan="3">User Login</td>
                      <td>
                        <input type="text" name="username" id="username" class="form-control">
                      </td>
            				</tr> -->

                      <tr>
                        <td colspan="12">
                          <div class="form-group">
                            <div class="checkbox checkbox-icon-black">
                              <label for="rememberChk1">
                                Allow Local Login
                              </label>
                              <?php
                              $locallogin = $row->user_local_login;
                              if ($locallogin == 0) {
                                $checked = "";
                              } else {
                                $checked = "checked";
                              }
                              ?>
                              <input id="locallogin" name="locallogin" type="checkbox" <?php echo $checked; ?>>
                            </div>

                            <img id="loader3" src="<?= base_url(); ?>assets/images/ajax-loader.gif" border="0" style="display:none;" />


                              <?php if ($company_exca == 2) {?>
                                <input type="text" name="iscompany_exca" id="iscompany_exca" value="2" hidden>
                                <?php
                                $isuserexca = $row->user_excavator;
                                if ($isuserexca == 0) {
                                  $checkedexca = ""; ?>

                                <div class="checkbox checkbox-icon-black" id="showhideuserexcaedit">
                                  <label for="rememberChk1">
                                    User Excavator
                                  </label>
                                  <input id="userexcavator" name="userexcavator" type="checkbox" <?php echo $checkedexca; ?>>
                                </div>
                              <?php }else {?>
                                <?php $checkedexca = "checked"; ?>
                                <div class="checkbox checkbox-icon-black" id="showhideuserexcaedit">
                                  <label for="rememberChk1">
                                    User Excavator
                                  </label>
                                  <input id="userexcavator" name="userexcavator" type="checkbox" <?php echo $checkedexca; ?>>
                                </div>
                              <?php } ?>
                            <?php }else {?>
                              <input type="text" name="iscompany_exca" id="iscompany_exca" value="0" hidden>
                              <div class="checkbox checkbox-icon-black" id="showhideuserexcaedit"  style="display:none;">
                                <label for="rememberChk1">
                                  User Excavator
                                </label>
                                <input id="userexcavator" name="userexcavator" type="checkbox">
                              </div>
                            <?php } ?>







                          </div>
                        </td>
                      </tr>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="text-right">
                    <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?= base_url() ?>account'" />
                    <input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Save " />
                    <img id="loader" src="<?= base_url(); ?>assets/images/ajax-loader.gif" border="0" style="display:none;" />
                  </div>
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
<script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  $("#notifnya").fadeIn(1000);
  $("#notifnya").fadeOut(5000);

  function frmadd_onsubmit() {
    $("#loader").show();
    jQuery.post("<?= base_url() ?>account/updateUser", jQuery("#frmadd").serialize(),
      function(r) {
        $("#loader").hide();
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

  function privilegeOnchange() {
    var privilegeval = $("#privilege").val();
    if (privilegeval == 5 || privilegeval == 6 || privilegeval == 10) {
      $("#showhidepjo").show();
    } else if (privilegeval == 9) {
      $.post("<?php echo base_url() ?>driver/alldatadriver", {}, function(response) {
        console.log("response : ", response);
        $("#showhidepjo").show();
        $("#showhidepjo").hide();
      });
    } else {
      $("#showhideuserexcaedit").hide();
      $("#showhidepjo").hide();
    }
  }

  function pjolistonchange(){
    var company_id = $("#pjolist").val();
    $("#loader3").show();
    $.post("<?php echo base_url() ?>account/getdetailcompany", {company_id : company_id}, function(response) {
      $("#loader3").hide();
      console.log("response : ", response);
      var company_exca = response.data[0].company_exca;
      $("#iscompany_exca").val(company_exca);
        if (company_exca == 2) {
          $("#showhideuserexcaedit").show();
          $("#showhideuserexca1").hide();
          $("#showhideuserexca2").hide();
        }else {
          $('input#userexcavator').prop('checked', false);
          $("#showhideuserexcaedit").hide();
          $("#showhideuserexca1").hide();
          $("#showhideuserexca2").hide();
        }
    },"json");
  }

  // FOR DISABLE SUBMIT FORM
  $(window).keydown(function(event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
</script>
