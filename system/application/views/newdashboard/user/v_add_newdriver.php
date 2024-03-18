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
          <header class="panel-heading panel-heading-red"> Add New Driver Account</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
              <div class="row">
                <div class="col-md-6">
                  <h4>
                    <b style="color: blue;">Private Information</b>
                  </h4>
                  <table class="table">
                    <tr>
                      <td colspan="3">Driver Name</td>
                      <td>
                        <input type="text" name="name" id="name" placeholder="NAMA PENGEMUDI" class="form-control">
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">ID Simper</td>
                      <td>
                       <input type="text" name="email" id="email" placeholder="CONTOH : 012345" class="form-control">
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Gender</td>
                      <td>
                        <select class="form-control" id="sex" name="sex">
                          <option value="1">Male</option>
                          <option value="2">Female</option>
                        </select>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Birthdate</td>
                      <td>
                        <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd/mm/yyyy" data-link-format="dd-mm-yy">
                          <input class="form-control" class="form-control" type="text" name="birthdate" id="birthdate" value="<?php if (isset($row)) echo $row->user_date_fmt; ?>" maxlength='10'>
                          <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Address</td>
                      <td>
                        <textarea class="form-control" name="address" id="address"></textarea>
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Mobile</td>
                      <td>
                        <input type="number" name="mobile" id="mobile" class="form-control">
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
                        <select class="form-control" name="privilege" id="privilege" readonly >
                          <?php for ($i = 0; $i < sizeof($role_list); $i++) { ?>
                            <option <?php if ($role_list[$i]['privilege_id'] == 9) {
                                      echo "style=\"display:none\"";
                                    } ?> value="<?php echo $role_list[$i]['privilege_id'] ?>"><?php echo $role_list[$i]['privilege_name'] ?></option>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>

                    <tr id="showhidepjo" >
                      <td colspan="3">Contractor</td>
                      <td>
                        <select class="form-control select2" name="pjolist" id="pjolist" style="width:100%;">
                          <?php for ($i = 0; $i < sizeof($pjo_list); $i++) { ?>
                            <option value="<?php echo $pjo_list[$i]['company_id'] . '|' . $pjo_list[$i]['company_name']; ?>"><?php echo $pjo_list[$i]['company_name']; ?></option>
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

                    <!-- <tr>
                      <td colspan="3">Password</td>
                      <td>
                        <input type="password" name="pass" id="pass" class="form-control">
                      </td>
                    </tr>

                    <tr>
                      <td colspan="3">Confirm Password</td>
                      <td>
                        <input type="password" name="cpass" id="cpass" class="form-control">
                      </td>
                    </tr> -->

                    <tr>
                      <td colspan="12">
                        <div class="form-group">
                          <div class="checkbox checkbox-icon-black">
                            <label for="rememberChk1">
                              Allow Local Login
                            </label>
                            <input id="locallogin" name="locallogin" type="checkbox" checked>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="text-right">
                    <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?= base_url() ?>account/driver'" />
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
    jQuery.post("<?= base_url() ?>account/savenewdriver", jQuery("#frmadd").serialize(),
      function(r) {
        $("#loader").hide();
        console.log("response : ", r);
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
    if (privilegeval == 5 || privilegeval == 6) {
      $("#showhidepjo").show();
    } else if (privilegeval == 9) {
      $.post("<?php echo base_url() ?>driver/alldatadriver", {}, function(response) {
        console.log("response : ", response);
        $("#showhidepjo").show();
      });
    } else {
      $("#showhidepjo").hide();
    }
  }

  // FOR DISABLE SUBMIT FORM
  $(window).keydown(function(event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
</script>