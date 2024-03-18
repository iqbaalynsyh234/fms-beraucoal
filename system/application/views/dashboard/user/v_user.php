<style media="screen">
  div#modalchangepass {
    margin-left: -1.5%;
    width: 27%;
    height: 100%;
    position: absolute;
    text-align: left;
    margin-top: -79%;
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
      <div class="col-md-12">
              <div class="panel" id="panel_form">
                <header class="panel-heading panel-heading-blue">User</header>
                <div class="panel-body" id="bar-parent10">
                <table id="example1" class="table table-striped" style="font-size: 14px;">
                  <thead>
                    <tr>
                      <th>
                        <a type="button" class="btn btn-success btn-xs" href="<?php echo base_url()?>account/adduser" title="Add User">
                          <span class="fa fa-plus"></span>
                        </a>
                        No
                      </th>
                      <th>Login</th>
                      <th>Name</th>
                      <th>Branch Office</th>
                      <th>Sub Branch Office</th>
                      <th>Customer Office</th>
                      <th>Sub Customer Office</th>
                      <th>Status</th>
                      <th>Option</th>
                    </tr>
                  </thead>
                    <tbody>
              			<?php $no = 1; for($i=0; $i < count($data); $i++) { ?>
              				<tr>
              					<td valign="top"><?=$no;?></td>
              					<td valign="top"><?=$data[$i]['user_login'];?></td>
              					<td valign="top"><?=$data[$i]['user_name'];?></td>
                        <td valign="top">
                          <?php for ($b=0; $b < count($branchoffice); $b++) {?>
                            <?php if ($data[$i]['user_company'] == $branchoffice[$b]->company_id) {?>
                              <?php echo $branchoffice[$b]->company_name ?>
                            <?php } ?>
                          <?php } ?>
                        </td>
                        <td valign="top">
                          <?php for ($c=0; $c < count($subbranchoffice); $c++) {?>
                            <?php if ($data[$i]['user_subcompany'] == $subbranchoffice[$c]->subcompany_id) {?>
                              <?php echo $subbranchoffice[$c]->subcompany_name ?>
                            <?php } ?>
                          <?php } ?>
                        </td>
                        <td valign="top">
                          <?php for ($d=0; $d < count($customer); $d++) {?>
                            <?php if ($data[$i]['user_group'] == $customer[$d]['group_id']) {?>
                              <?php echo $customer[$d]['group_name'] ?>
                            <?php } ?>
                          <?php } ?>
                        </td>
                        <td valign="top">
                          <?php for ($e=0; $e < count($subcustomer); $e++) {?>
                            <?php if ($data[$i]['user_subgroup'] == $subcustomer[$e]->subgroup_id) {?>
                              <?php echo $subcustomer[$e]->subgroup_name ?>
                            <?php } ?>
                          <?php } ?>
                        </td>
                        <td valgn="top">
                          <?php if ($data[$i]['user_status'] == 1) {
                            echo "Active";
                          }else {
                            echo "In Active";
                          } ?>
                        </td>
                        <td>
                          <a href="<?=base_url();?>account/edit/<?=$data[$i]['user_id'];?>"><img src="<?=base_url();?>assets/images/edit_male_user.png" border="0" width="32" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
                          <a href="#" onclick="changepass(<?=$data[$i]['user_id'];?>)"><img src="<?=base_url();?>assets/images/account.png" border="0" width="32" alt="<?=$this->lang->line("lchangepassword"); ?>" title="<?=$this->lang->line("lchangepassword"); ?>"></a>
                        </td>
                      </tr>
                    <?php $no++; } ?>
      							</tbody>
      						</table>
          </div>
        </div>
  </div>
</div>

<div id="modalchangepass" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header>Change Password</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalchangepass();">X</button>
                </div>
            </div>
            <div class="card-body">
                <form class="form-horizontal form" id="frmchangepass" onsubmit="javascript: return frmchangepass_onsubmit()">
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

<script type="text/javascript">
$("#notifnya").fadeIn(1000);
$("#notifnya").fadeOut(5000);

function changepass(id){
  $("#iddelete").val(id);
  $("#oldpass").val();
  $("#pass").val();
  $("#cpass").val();
  $("#modalchangepass").fadeIn(1000);
}

function closemodalchangepass(){
  $("#oldpass").val("");
  $("#pass").val("");
  $("#cpass").val("");
  $("#modalchangepass").fadeOut(1000);
}

function btnCloseModal(){
  $("#oldpass").val("");
  $("#pass").val("");
  $("#cpass").val("");
  $("#modalchangepass").fadeOut(1000);
}

function closemodalchangepass(){
  $("#oldpass").val("");
  $("#pass").val("");
  $("#cpass").val("");
  $("#modalchangepass").fadeOut(1000);
}

function frmchangepass_onsubmit()
{
  jQuery.post("<?=base_url()?>account/savepass/<?=$this->sess->user_id?>", jQuery("#frmchangepass").serialize(),
    function(r)
    {
      if (confirm(alert(r.message))) {
        window.location = '<?php echo base_url()?>account';
      }
      if (r.error)
      {
        return;
      }
      jQuery("#dialog").dialog("close");
    }
    , "json"
  );

  return false;
}

function showlink(id)
{
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
