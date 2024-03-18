<style media="screen">
  #modalchangepass {
    position: absolute;
    margin-top: -10px;
    left: 30%;
    right: 30%;
    bottom: 30%;
    top: 30%;
    width: 27%;
    z-index: 1;
  }
</style>
<!-- start sidebar menu -->
<div class="sidebar-container">
  <?= $sidebar; ?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) { ?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif'); ?></div>
    <?php } ?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="col-md-12">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red">User</header>
        <div id="modalchangepass" style="display: none;">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-topline-yellow">
                <div class="card-head">
                  <header>Change Password</header>
                  <div class="tools">
                    <button type="button" class="btn btn-danger" name="button" onclick="closemodalchangepass();">X</button>
                  </div>
                </div>
                <div class="card-body">
                  <form class="form-horizontal form" id="frmChangethispass" onsubmit="javascript: return frmchangepass_onsubmit()">
                    <input type="hidden" name="iddelete" id="iddelete">

                    <tr>
                      <td>Old Password</td>
                      <td>
                        <input type="password" class="form-control" name="oldpass" id="oldpass">
                      </td>
                    </tr>

                    <tr>
                      <td>New Password</td>
                      <td>
                        <input type="password" class="form-control" name="pass" id="pass">
                      </td>
                    </tr>

                    <tr>
                      <td>Retype New Password</td>
                      <td>
                        <input type="password" class="form-control" name="cpass" id="cpass">
                      </td>
                    </tr>

                    <br>
                    <div class="text-right">
                      <button type="button" name="button" class="btn btn-warning" onclick="btnCloseModal();">Cancel</button>
                      <button type="submit" name="button" class="btn btn-danger">Update Password</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body" id="bar-parent10">
          <table id="example1" class="table table-striped" style="font-size: 14px; width:100%;">
            <thead>
              <tr>
                <th>
                  <?php if ($privilegecode == 3) { ?>

                  <?php } else { ?>
                    <a type="button" class="btn btn-success btn-xs" href="<?php echo base_url() ?>account/addnewuser" title="Add User">
                      <span class="fa fa-plus"></span>
                    </a>
                  <?php } ?>
                  No
                </th>
                <th>Login</th>
                <th>Name</th>
                <!-- <th>Branch Office</th>
                      <th>Sub Branch Office</th>
                      <th>Customer Office</th>
                      <th>Sub Customer Office</th> -->
                <th>Company</th>
                <th>Type</th>
                <th>Local Login</th>
                <th>Status</th>
                <?php if ($privilegecode == 3) { ?>

                <?php } else { ?>
                  <th>Option</th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              for ($i = 0; $i < count($data); $i++) { ?>
                <tr>
                  <td valign="top"><?= $no; ?></td>
                  <td valign="top"><?= $data[$i]['user_login']; ?></td>
                  <td valign="top"><?= $data[$i]['user_name']; ?></td>
                  <!-- <td valign="top">
                          <?php for ($b = 0; $b < count($branchoffice); $b++) { ?>
                            <?php if ($data[$i]['user_company'] == $branchoffice[$b]->company_id) { ?>
                              <?php echo $branchoffice[$b]->company_name ?>
                            <?php } ?>
                          <?php } ?>
                        </td>
                        <td valign="top">
                          <?php for ($c = 0; $c < count($subbranchoffice); $c++) { ?>
                            <?php if ($data[$i]['user_subcompany'] == $subbranchoffice[$c]->subcompany_id) { ?>
                              <?php echo $subbranchoffice[$c]->subcompany_name ?>
                            <?php } ?>
                          <?php } ?>
                        </td>
                        <td valign="top">
                          <?php for ($d = 0; $d < count($customer); $d++) { ?>
                            <?php if ($data[$i]['user_group'] == $customer[$d]['group_id']) { ?>
                              <?php echo $customer[$d]['group_name'] ?>
                            <?php } ?>
                          <?php } ?>
                        </td>
                        <td valign="top">
                          <?php for ($e = 0; $e < count($subcustomer); $e++) { ?>
                            <?php if ($data[$i]['user_subgroup'] == $subcustomer[$e]->subgroup_id) { ?>
                              <?php echo $subcustomer[$e]->subgroup_name ?>
                            <?php } ?>
                          <?php } ?>
                        </td> -->
                  <td><?= $data[$i]['company_name'] ?></td>
                  <td><?= $data[$i]['privilege_name'] ?></td>
                  <td valgn="top">
                    <?php if ($data[$i]['user_local_login'] == 1) {
                      echo "Yes";
                    } else {
                      echo "No";
                    } ?>
                  </td>
                  <td valgn="top">
                    <?php if ($data[$i]['user_status'] == 1) {
                      echo "Active";
                    } else {
                      echo "In Active";
                    } ?>
                  </td>
                  <?php if ($privilegecode == 3) { ?>

                  <?php } else { ?>
                    <td>
                      <a href="<?= base_url(); ?>account/edituser/<?= $data[$i]['user_id']; ?>"><img src="<?= base_url(); ?>assets/images/edit_male_user.png" border="0" width="32" alt="<?= $this->lang->line("ledit_data"); ?>" title="<?= $this->lang->line("ledit_data"); ?>"></a>
                      <a href="#" onclick="changepass(<?= $data[$i]['user_id']; ?>)"><img src="<?= base_url(); ?>assets/images/account.png" border="0" width="32" alt="<?= $this->lang->line("lchangepassword"); ?>" title="<?= $this->lang->line("lchangepassword"); ?>"></a>
                    </td>
                  <?php } ?>
                </tr>
              <?php $no++;
              } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="js/script.js"></script>
  <script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

  <script type="text/javascript">
    $("#notifnya").fadeIn(1000);
    $("#notifnya").fadeOut(5000);

    function changepass(id) {
      $("#iddelete").val(id);
      $("#oldpass").val();
      $("#pass").val();
      $("#cpass").val();
      $("#modalchangepass").fadeIn(1000);
    }

    function closemodalchangepass() {
      $("#oldpass").val("");
      $("#pass").val("");
      $("#cpass").val("");
      $("#modalchangepass").fadeOut(1000);
    }

    function btnCloseModal() {
      $("#oldpass").val("");
      $("#pass").val("");
      $("#cpass").val("");
      $("#modalchangepass").fadeOut(1000);
    }

    function closemodalchangepass() {
      $("#oldpass").val("");
      $("#pass").val("");
      $("#cpass").val("");
      $("#modalchangepass").fadeOut(1000);
    }

    function frmchangepass_onsubmit() {
      jQuery.post("<?= base_url() ?>account/savepass/<?= $this->sess->user_id ?>", jQuery("#frmChangethispass").serialize(),
        function(r) {
          console.log("response : ", r);
          if (r.error) {
            alert(r.message);
            return;
          } else {
            if (confirm(alert(r.message))) {
              window.location = '<?php echo base_url() ?>account';
            }
          }
          jQuery("#dialog").dialog("close");
        }, "json"
      );

      return false;
    }

    // var iddelete = $("#iddelete").val();
    // var oldpass  = $("#oldpass").val();
    // var pass     = $("#pass").val();
    // var cpass    = $("#cpass").val();
    //
    // // console.log("iddelete : ", iddelete);
    // // console.log("oldpass : ", oldpass);
    // // console.log("pass : ", pass);
    // // console.log("cpass : ", cpass);
    //
    // var data = {
    //   iddelete : iddelete,
    //   oldpass : oldpass,
    //   pass : pass,
    //   cpass : cpass,
    // };
    //
    // console.log("data : ", data);


    // jQuery.post("<?= base_url() ?>account/savenewpass/"+iddelete, data, function(r){
    //     console.log("response : ", r);
    //     // if (confirm(alert(r.message))) {
    //     //   window.location = '<?php echo base_url() ?>account';
    //     // }
    //     // if (r.error)
    //     // {
    //     //   return;
    //     // }
    //     // jQuery("#dialog").dialog("close");
    //   }, "json");

    function showlink(id) {
      console.log("");
      // $("[id]").filter(function() {
      //     if (this.id.match(/^link\d+/))
      //     {
      //       if (this.id != ("link"+id))
      //       {
      //         $("#"+this.id).hide();
      //       }
      //     }
      // });
      //
      // var disp = $("#link"+id).css('display');
      // if (disp == "none")
      // {
      //   $("#link"+id).show();
      // }
      // else
      // {
      //   $("#link"+id).hide();
      // }
    }
  </script>