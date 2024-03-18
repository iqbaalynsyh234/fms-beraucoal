<style media="screen">
  div#modalchangepassword {
    margin-top: 30%;
    margin-left: 20%;
    /* overflow-x: auto; */
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    width: 56%;
  }
</style>

<script>
  function checkbrowser() {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var headerlogo_html = "";
    if (isMobile) {
      //headerlogo_html += "<img alt='' id='thislogo' src='<?php echo base_url(); ?>/assets/abditrack/images/logo2.png' style='margin-right:16px;margin-top:-17px;'>";
      //alert("MOBILE");
      //document.getElementById("thislogo").src="<?php echo base_url(); ?>/assets/bib/images/logo-abditrack.png";
    } else {
      //alert("DESKTOP");
      //document.getElementById("thislogo").src="<?php echo base_url(); ?>/assets/bib/images/logo-abditrack.png";
      //headerlogo_html += "<img alt='' id='thislogo' src='<?php echo base_url(); ?>/assets/abditrack/images/logo2.png' style='margin-left:-15px;margin-top:-17px;'>";
    }

    var privilegecode = '<?php echo $this->sess->user_id_role; ?>';

    console.log("privilegecode : ", privilegecode);
    if (privilegecode == 5 || privilegecode == 6) {} else {
      $("#myheaderlogo").html(headerlogo_html);
    }
  }
</script>

<div class="page-header-inner" style="background-color: #74bf43;">
  <div class="page-logo">
    <?php if (!$this->sess) { ?>

    <?php } else { ?>
      <?php
      $privilegecode   = $this->sess->user_id_role;
      if ($privilegecode == 7 || $privilegecode == 8) { ?>
        <a href="<?php echo base_url(); ?>wim">
        <?php }elseif ($privilegecode == 11) {?>
          <a href="<?php echo base_url(); ?>development/otherport">
        <?php } else { ?>
          <a href="<?php echo base_url(); ?>maps/heatmap">
          <?php } ?>
          <img alt="" id="thislogo" src="<?php echo base_url(); ?>/assets/abditrack/images/logo.png"  height="50px;" style="position:center;margin-left:-7px;margin-top:-5px;">
          <span id="myheaderlogo"></span>
          <span id="thislogo2" style="display:none;"><img src="<?php echo base_url(); ?>/assets/abditrack/images/logo2.png" style="position:center;margin-left:6px;"></span>
          </a>
        <?php } ?>
  </div>
  <ul class="nav navbar-nav navbar-left in">
    <li>
      <a href="#" class="menu-toggler sidebar-toggler" onclick="showhidelogo();">
        <i class="icon-menu" style="color:white;"></i>
      </a>
    </li>
  </ul>
  <input type="hidden" id="thislogovalue" value="0">

  <!-- start mobile menu -->
  <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
    <span></span>
  </a>
  <!-- end mobile menu -->
  <!-- start header menu -->
  <div class="top-menu">
    <?php
    if (!$this->sess) { ?>

    <?php } else { ?>
      <ul class="nav navbar-nav pull-right">
        <?php
        $privilegecode = $this->sess->user_id_role;
        if ($privilegecode != 7 || $privilegecode != 8) { ?>
          <li class="dropdown dropdown-extended dropdown-notification">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
              <i class="fa fa-mobile-phone"></i>
              <?php
              $privilegecode = $this->sess->user_id_role;
              if ($privilegecode == 0 || $privilegecode == 1 || $privilegecode == 2 || $privilegecode == 3) { ?>
                <span class="badge headerBadgeColor1" id="totaldevicealert" style="display:none;"> 2 </span>
              <?php } ?>
            </a>



            <ul class="dropdown-menu animated swing">
              <li class="external">
                <h3><span class="bold">Device Alert</span></h3>
                <!--<span class="notification-label purple-bgcolor">New 6</span>-->
              </li>
              <li>
                <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">

                </ul>
              </li>
            </ul>
          </li>

          <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
              <i class="fa fa-bell-o"></i>
              <span class="badge headerBadgeColor1" id="totalnotifmaintenance" style="display:none;"> 6 </span>
            </a>

            <ul class="dropdown-menu animated swing">
              <li class="external">
                <h3><span class="bold">Maintenance Scheduler</span></h3>
              </li>
              <li>
                <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
                  <li>
                    <div id="ultooltip" class="form-control">
                      <div id="tablebreakdownreport">
                        <table width="100%" class="table table-border" style="font-size:12px;">
                          <thead>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Category</th>
                            <th>Start Time</th>
                          </thead>
                          <tbody id="rowbreakdownreport">

                          </tbody>
                        </table>
                        <a href="<?php echo base_url() ?>maintenance/onprocess" class="btn btn-primary btn-sm form-control">
                          Go To Page
                        </a>
                      </div>

                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
        <?php } ?>


        <li class="dropdown dropdown-extended dropdown-inbox" id="mo1">
          <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <img alt="" class="img-circle " src="assets/img/dp.jpg" />
            <span class="username username-hide-on-mobile" style="color:white;">


              <?php echo $this->sess->user_name; ?>

            </span>
            <i class="fa fa-angle-down"></i>
          </a>

          <ul class="dropdown-menu dropdown-menu-default animated jello">
            <?php
            $userlevel = $this->sess->user_level;
            if ($userlevel == 1) { ?>
              <!--<li>
                <a href="<?= base_url(); ?>account/edit/<?= $this->sess->user_id; ?>">
                  <i class="icon-user"></i> Profile - <?= $this->sess->user_name ?>
                </a>
              </li> -->
            <?php } ?>
            <li>
              <a href="#" onclick="modalChangePasswordBaru('modalPasswordBaru');">
                <i class="icon-key"></i> Change Password
              </a>
            </li>
			<!-- <li>
              <a href="<?= base_url(); ?>turing" target="_blank">
                <i class="icon-notebook"></i> Help Center</a>
            </li> -->
            <!-- <li>
              <a href="<?= base_url(); ?>download/tutorial">
                <i class="icon-cloud-download"></i> Download Manual Book
              </a>
            </li> -->
            <!--<li>
                                      <a href="#">
                                          <i class="icon-settings"></i> Settings
                                      </a>
                                  </li>
                                  <li>
                                      <a href="#">
                                          <i class="icon-directions"></i> Help
                                      </a>
                                  </li>
                                  <li class="divider"> </li>
                                  <<li>
                                      <a href="lock_screen.html">
                                          <i class="icon-lock"></i> Lock
                                      </a>
                                  </li>-->
            <li>
              <a href="<?= base_url(); ?>member/logout">
                <i class="icon-logout"></i> Log Out </a>
            </li>
          </ul>
        </li>

      </ul>
    <?php } ?>

  </div>
</div>

<div id="modalPasswordBaru" class="modalPasswordBaru" style="display: none;">
  <div class="modal-content-PasswordBaru">
    <div class="row">
      <div class="col-md-10">
        <!-- <p class="modalTitleforAll" id="modalStateTitle">
				</p> -->
      </div>
      <div class="col-md-2">
        <div class="closethismodalallModalPasswordBaru btn btn-danger btn-sm">X</div>
      </div>
    </div>
    <div class="modalStateContentPasswordBaru">
      <div class="row">
        <div class="col-md-12">
          <p style="font-size:14px;">
            <b>
              Change Password
            </b>
          </p>
          <form class="block-content form" id="frmchangepass" onsubmit="javascript: return frmchangepass_onsubmit()">
            <table width="100%" cellpadding="3" class="table">
              <tr>
                <?php if ($this->sess->user_type == 2) { ?>
                  <!-- <tr>
                <td>
                  <?= $this->lang->line("loldpassword"); ?>
                </td>
                <td>
                  <input type="password" name="oldpass" id="oldpass" class="form-control" />
                </td> -->
                <?php } ?>

                <td>
                  <?= $this->lang->line("lnewpassword"); ?>
                </td>
                <td>
                  <input type="password" name="pass" id="pass" class="form-control" />
                </td>

                <td>
                  <?= $this->lang->line("lconfirm_password"); ?>
                </td>
                <td>
                  <input type="password" name="cpass" id="cpass" value="" class="form-control" />
                </td>
              </tr>
            </table>
            <span id="capslockoldpass" style="color:red; display: none;">Capslock is on!</span>
            <div class="text-right">
              <!-- <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="closemodalchangepassword();"/> -->
              <input class="btn btn-primary" type="submit" name="btnsave" id="btnsave" value=" Save " />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="firstchangepassword" class="modalPasswordBaru" style="display: <?php if (isset($this->sess->user_password_default)) {
                                                                          if (($this->sess->user_local_login == 1) && ($this->sess->user_password_default == 0)) {
                                                                            echo 'block';
                                                                          } else {
                                                                            echo 'none';
                                                                          }
                                                                        } else {
                                                                          echo 'none';
                                                                        } ?>;">
  <div class="modal-content-PasswordBaru">
    <div class="row">
      <div class="col-md-10">
        <!-- <p class="modalTitleforAll" id="modalStateTitle">
				</p> -->
        <p style="font-size:14px;">
          <b>
            Change Password for the first time.
          </b>
        </p>
      </div>
      <div class="col-md-2">
        <div class="closefirstchange btn btn-danger btn-sm" onclick="closefirstchange()">X</div>
      </div>
    </div>
    <div class="modalStateContentPasswordBaru">
      <div class="row">
        <div class="col-md-12">
          <form class="block-content form" id="firstchangepass" onsubmit="javascript: return first_change()">
            <table width="50%" cellpadding="3" class="table">
				<tr>
					<td>
					  <small><?= $this->lang->line("lnewpassword"); ?></small>
					</td>
				</tr>
				<tr>
					<td>
					  <input type="hidden" value="1" name="firsttime">
					  <input type="password" name="pass" id="pass" class="form-control" />
					</td>
				</tr>

			   <tr>
					<td>
					  <small><?= $this->lang->line("lconfirm_password"); ?></small>
					</td>
				</tr>
				<tr>
					<td>
					  <input type="password" name="cpass" id="cpass" value="" class="form-control" />
					</td>
              </tr>
            </table>
            <span id="capslockoldpass" style="color:red; display: none;">Capslock is on!</span>
            <div class="text-right">
              <!-- <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="closemodalchangepassword();"/> -->
              <input class="btn btn-primary" type="submit" name="btnsave" id="btnsave" value=" Save " />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="modalchangepassword" style="display: none;">
  <div id="changepassreport"></div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Change Password</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodalchangepassword();">X</button>
          </div>
        </div>
        <div class="card-body">

          <form class="block-content form" id="frmchangepass" onsubmit="javascript: return frmchangepass_onsubmit()">
            <table width="100%" cellpadding="3" class="table">
              <!-- <tr>
    						<td colspan="2">
                  <legend><?= $this->lang->line("llogin"); ?></legend>
                </td>
                <td>
                  <label id="loginya"></label>
                </td>
                <td>
                <?= $this->lang->line("lname"); ?>
                </td>
                <td>
                  <label id="namenya"></label>
                </td>
    					</tr> -->

              <tr>
                <?php if ($this->sess->user_type == 2) { ?>
                  <!-- <tr>
                <td>
                  <?= $this->lang->line("loldpassword"); ?>
                </td>
                <td>
                  <input type="password" name="oldpass" id="oldpass" class="form-control" />
                </td> -->
                <?php } ?>

                <td>
                  <?= $this->lang->line("lnewpassword"); ?>
                </td>
                <td>
                  <input type="password" name="pass" id="pass" class="form-control" />
                </td>

                <td>
                  <?= $this->lang->line("lconfirm_password"); ?>
                </td>
                <td>
                  <input type="password" name="cpass" id="cpass" value="" class="form-control" />
                </td>
              </tr>
            </table>
            <span id="capslockoldpass" style="color:red; display: none;">Capslock is on!</span>
            <div class="text-right">
              <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="closemodalchangepassword();" />
              <input class="btn btn-primary" type="submit" name="btnsave" id="btnsave" value=" Save " />
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function frmchangepass_onsubmit() {
    jQuery.post("<?= base_url() ?>user/savepass/<?= $this->sess->user_id ?>", jQuery("#frmchangepass").serialize(),
      function(r) {
        if (r.error) {
          alert(r.message);
        } else {
          // if (confirm(alert(r.message))) {
          alert(r.message)
          window.location = '<?php echo base_url() ?>maps/heatmap';
          // }
        }
        // jQuery("#dialog").dialog("close");
      }, "json"
    );

    return false;
  }


  function first_change() {
    var data = jQuery("#firstchangepass").serialize();
    jQuery.post("<?= base_url() ?>user/savepass/<?= $this->sess->user_id ?>", data,
      function(r) {
        if (r.error) {
          alert(r.message);
        } else {
          alert(r.message)
          window.location = '<?php echo base_url() ?>member/logout';
        }
        jQuery("#dialog").dialog("close");
      }, "json"
    );

    return false;
  }

  function closefirstchange() {
    document.getElementById("firstchangepassword").style.display = "none";
  }


  function modalchangepassword() {
    jQuery.post("<?= base_url() ?>user/changepass/<?= $this->sess->user_id ?>", {}, function(r) {
      console.log("r : ", r);
      if (r.error == "false") {
        alert("Error, please contact Administrator");
      } else {
        // $("#loginya").html(r.row.user_login);
        // $("#namenya").html(r.row.user_name);
        $("#modalchangepassword").show();
      }
    }, "json");
  }

  function closemodalchangepassword() {
    $("#modalchangepassword").hide();
  }

  document.querySelector("#oldpass").addEventListener('keyup', checkCapsLock);
  document.querySelector("#oldpass").addEventListener('mousedown', checkCapsLock);
  document.querySelector("#pass").addEventListener('keyup', checkCapsLock);
  document.querySelector("#pass").addEventListener('mousedown', checkCapsLock);

  function checkCapsLock(e) {
    var caps_lock_on = e.getModifierState('CapsLock');
    //
    if (caps_lock_on == true) {
      $("#capslockoldpass").show();
    } else {
      $("#capslockoldpass").hide();
    }
  }
</script>
