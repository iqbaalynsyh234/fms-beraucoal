<style media="screen">
  div#modalchangepassword {
    margin-top: 15%;
    margin-left: 26%;
    overflow-x: auto;
    position: absolute;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    width: 56%;
  }
</style>
<div class="page-header-inner ">
  <!-- logo start -->
  <!--<div class="page-logo" style="border:2px solid black; margin-left:-20px;" >-->
  <div class="page-logo">
    <a href="<?php echo base_url();?>maps">
    <img alt="" src="<?php echo base_url();?>/assets/bib/images/temanindobara_inner.png" width="58%;" style="position:center;margin-left:20px;margin-top:-10px;">
		<!-- <img alt="" src="<?php echo base_url();?>assets/dashboard/assets/img/logoooo.png" height="55%"> -->
		<span class="logo-default"></span></a>
  </div>
  <!-- start mobile menu -->
  <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
    <span></span>
  </a>
  <!-- end mobile menu -->
      <?php
        $userlevel = $this->sess->user_level;
          if ($userlevel == 1) {?>
            <div class="nav navbar-nav navbar-left out" style="border:0px solid black; margin-left:10px; margin-top:10px;">
              <div class="col-md-12">
                <a href="javascript: monitoring_sidebar();">
          				<img title="Monitor" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_monitor_2.png" width="17%">
          			</a>
                <a href="javascript: config_sidebar();">
          				<img title="Configuration" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_config_2.png" width="17%">
          			</a>
                <a href="javascript: report_sidebar();">
          				<img title="Report" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_report_2.png" width="17%">
          			</a>
                <a href="javascript: billing_sidebar();">
          				<img title="Billing" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_bill_2.png" width="17%">
          			</a>
              </div>
            </div>
          <?php }elseif ($userlevel == 2) {?>
            <div class="nav navbar-nav navbar-left out" style="border:0px solid black; margin-left:10px; margin-top:10px;">
              <div class="col-md-12">
                <a href="javascript: monitoring_sidebar();">
          				<img title="Monitor" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_monitor_2.png" width="30%">
          			</a>
                <a href="javascript: config_sidebar();">
                  <img title="Configuration" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_config_2.png" width="30%">
                </a>
                <a href="javascript: report_sidebar();">
          				<img title="Report" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_report_2.png" width="30%">
          			</a>
              </div>
            </div>
          <?php }elseif ($userlevel == 3) { ?>
            <div class="nav navbar-nav navbar-left out" style="border:0px solid black; margin-left:10px; margin-top:10px;">
              <div class="col-md-12">
                <a href="javascript: monitoring_sidebar();">
          				<img title="Monitor" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_monitor_2.png" width="30%">
          			</a>
                <a href="javascript: report_sidebar();">
          				<img title="Report" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_report_2.png" width="30%">
          			</a>
              </div>
            </div>
          <?php }elseif ($userlevel == 4) {?>
            <div class="nav navbar-nav navbar-left out" style="border:0px solid black; margin-left:10px; margin-top:10px;">
              <div class="col-md-12">
                <a href="javascript: monitoring_sidebar();">
          				<img title="Monitor" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_monitor_2.png" width="70%">
          			</a>
              </div>
            </div>
          <?php }else { ?>
            <div class="nav navbar-nav navbar-left out" style="border:0px solid black; margin-left:10px; margin-top:10px;">
              <div class="col-md-12">
                <a href="javascript: monitoring_sidebar();">
          				<img title="Monitor" src="<?php echo base_url();?>assets/dashboard/assets/img/lacakmobil_monitor_2.png" width="70%">
          			</a>
              </div>
            </div>
          <?php } ?>
  <!--<small><span id='ct' style="color:white;"></span> <font color="white"> | Login As : <?=$this->sess->user_name?></font></small>-->
  <!-- search bar -->
  <!--<form class="search-form-opened" action="#" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search..." name="query">
                        <span class="input-group-btn search-btn">
                          <a href="javascript:;" class="btn submit">
                             <i class="icon-magnifier"></i>

                           </a>
                        </span>
                    </div>
                </form>-->

  <!-- start header menu -->
  <div class="top-menu">
    <ul class="nav navbar-nav pull-right">
      <!-- start notification dropdown -->
      <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
          <!--<i class="fa fa-bell-o"></i>-->
          <!--<span class="badge headerBadgeColor1"> 6 </span>-->
          <button type="button" class="btn btn-success waves-effect waves-light" title="WhatsApp">
            <i class="fa fa-whatsapp"></i>
          </button>
        </a>

        <ul class="dropdown-menu animated swing">
          <li class="external">
            <h3><span class="bold">Contact Support</span></h3>
            <!--<span class="notification-label purple-bgcolor">New 6</span>-->
          </li>
          <li>
            <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
              <li>
                <a href="https://api.whatsapp.com/send?phone=+628558208484&text=Halo.%20Ada%20yang%20ingin%20saya%20tanyakan." target="_blank">
                  <span class="time">24 Hours</span>
                  <span class="details"></span>
                  <button type="button" class="btn btn-circle btn-success"><i class="fa fa-whatsapp"></i> Monitoring 1 </button>
                </a>
              </li>
              <li>
                <a href="https://api.whatsapp.com/send?phone=+628111178162&text=Halo.%20Ada%20yang%20Bisa%20Saya%20tanyakan." target="_blank">
                  <span class="time">24 Hours</span>
                  <span class="details"></span>
                  <button type="button" class="btn btn-circle btn-success"><i class="fa fa-whatsapp"></i> Monitoring 2 </button>
                </a>
              </li>
              <li>
                <a href="https://api.whatsapp.com/send?phone=+628161424999&text=Halo.%20Ada%20yang%20ingin%20saya%20tanyakan." target="_blank">
                  <span class="time">Office Hours</span>
                  <span class="details"></span>
                  <button type="button" class="btn btn-circle btn-info"><i class="fa fa-whatsapp"></i> Billing </button>
                </a>
              </li>

            </ul>
            <div class="dropdown-menu-footer">
              <!--<a href="javascript:void(0)"> All notifications </a>-->
            </div>
          </li>
        </ul>
      </li>

      <?php if ($this->sess->user_level == 1) {?>
      <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
          <!--<i class="fa fa-bell-o"></i>-->
          <!--<span class="badge headerBadgeColor1"> 6 </span>-->
            <button type="button" class="btn btn-danger waves-effect waves-light" title="Maintenance Alert">
              <!-- id="total_notif" -->
              <i class="fa fa-cog"></i>
            </button>
            <span class="badge headerBadgeColor2" id="totalnotifmaintenance" style="display:none;"> </span>
        </a>

        <ul class="dropdown-menu animated swing">
          <li class="external">
            <h3><span class="bold">Maintenance Alert</span></h3>
            <!--<span class="notification-label purple-bgcolor">New 6</span>-->
          </li>
          <li>
            <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
              <li>
                <div id="ultooltip">
                  <div id="tableserviceperkm">
                  <table width="100%" class="table table-border">
                    <a href="<?php echo base_url()?>vehicles/maintenance" class="btn btn-primary btn-sm">
                      SERVICE/ KM
                    </a>
                    <thead>
                        <th>No</th>
                        <th>Vehicle</th>
                        <th>Actual Odometer</th>
                        <th>Odometer For Service</th>
                    </thead>
                    <tbody id="serviceperkm">

                    </tbody>
                  </table>
                </div>

                  <div id="tableservicepermonth">
                    <table width="100%" class="table table-border">
                      <a href="<?php echo base_url()?>vehicles/maintenance" class="btn btn-primary btn-sm">
                        SERVICE / MONTH
                      </a>
                      <thead>
                          <th>No</th>
                          <th>Vehicle</th>
                          <th>Last Service</th>
                          <th>Next Service</th>
                      </thead>
                      <tbody id="servicepermonth">
                      </tbody>
                    </table>
                  </div>

                  <div id="tablekir">
                    <table width="100%" class="table table-border">
                      <a href="<?php echo base_url()?>vehicles/maintenance" class="btn btn-primary btn-sm">
                        KIR
                      </a>
                      <thead>
                          <th>No</th>
                          <th>Vehicle</th>
                          <th>Exp. Date</th>
                      </thead>
                      <tbody id="kirexpdate">

                      </tbody>
                    </table>
                  </div>

                  <div id="tablestnk">
                    <table width="100%" class="table table-border">
                      <a href="<?php echo base_url()?>vehicles" class="btn btn-primary btn-sm">
                        STNK
                      </a>
                      <thead>
                          <th>No</th>
                          <th>Vehicle</th>
                          <th>Exp. Date</th>
                      </thead>
                      <tbody id="stnkexpdate">

                      </tbody>
                    </table>
                  </div>
                </div>
              </li>
            </ul>
            <div class="dropdown-menu-footer">
              <!--<a href="javascript:void(0)"> All notifications </a>-->
            </div>
          </li>
        </ul>
      </li>
    <?php } ?>

      <!-- start notification dropdown -->
      <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
          <!--<i class="fa fa-bell-o"></i>-->
          <!--<span class="badge headerBadgeColor1"> 6 </span>-->
          <button type="button" class="btn btn-info waves-effect waves-light" title="Call Center">
            <i class="fa fa-phone"></i>
          </button>
        </a>

        <ul class="dropdown-menu animated swing">

          <li class="external">
            <h3><span class="bold">Call Support</span></h3>
            <!--<span class="notification-label purple-bgcolor">New 6</span>-->
          </li>
          <li>
            <ul class="dropdown-menu-list small-slimscroll-style" data-handle-color="#637283">
              <li>
                <a href="javascript:;">
                  <span class="time">24 Hours</span>
                  <span class="details"></span>
                  <button type="button" class="btn btn-circle btn-info"><i class="fa fa-phone"></i> 085 5820 8484 </button> Monitoring 1
                </a>
              </li>
              <li>
                <a href="javascript:;">
                  <span class="time">24 Hours</span>
                  <span class="details"></span>
                  <button type="button" class="btn btn-circle btn-info"><i class="fa fa-phone"></i> 081 1117 8162 </button> Monitoring 2
                </a>
              </li>
              <!--<li>
                                            <a href="javascript:;">
                                                <span class="time">24 Hours</span>
                                                <span class="details"></span>
                                                <button type="button" class="btn btn-circle btn-info"><i class="fa fa-phone"></i> 081617467868 </button> Monitoring 3
                                            </a>
                                        </li>-->
              <li>
                <a href="javascript:;">
                  <span class="time">Office Hours</span>
                  <span class="details"></span>
                  <button type="button" class="btn btn-circle btn-info"><i class="fa fa-phone"></i> 021 8243 4946 </button> Office
                </a>
              </li>
              <li>
                <a href="javascript:;">
                  <span class="time">Office Hours</span>
                  <span class="details"></span>
                  <button type="button" class="btn btn-circle btn-info"><i class="fa fa-phone"></i> 081 6142 4999 </button> Billing
                </a>
              </li>

            </ul>
            <div class="dropdown-menu-footer">
              <!--<a href="javascript:void(0)"> All notifications </a>-->
            </div>
          </li>
        </ul>
      </li>
      <!-- end notification dropdown -->

      <!-- start message dropdown -->
      <?php if ($this->sess->user_level == 1) {?>
        <!-- <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
          <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <button type="button" class="btn btn-warning waves-effect waves-light" title="Device Alert">
              <i class="fa fa-warning"> </i>
            </button>
            <span class="badge headerBadgeColor1" id="totalnotifdevicealert" style="display:none;"> </span>
          </a>
          <ul class="dropdown-menu animated slideInDown">
            <li class="external">
              <h3><span class="bold">Device Alert</span></h3>
            </li>
            <?php echo $this->config->item('device_alert'); ?>
            <div id="devicenotif">

            </div>
          </ul>
        </li> -->
      <?php } ?>
      <!-- end message dropdown -->

      <!-- start manage user dropdown -->
      <!--<li class="dropdown dropdown-user">-->
      <li class="dropdown dropdown-extended dropdown-inbox" id="mo1">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
    <!-- <img alt="" class="img-circle " src="<?php echo base_url();?>assets/dashboard/assets/img/dp.jpg" />-->
          <!-- <span class="username username-hide-on-mobile">  <?=$this->sess->user_name;?>  </span>
          <i class="fa fa-angle-down"></i> -->
  				<button type="button"
  					class="btn btn-default waves-effect waves-light" title="Profile">
  					<i class="fa fa-user"></i>
  				</button>
        </a>
        <ul class="dropdown-menu dropdown-menu-default animated jello">
          <?php
            $userlevel = $this->sess->user_level;
            if ($userlevel == 1) {?>
              <li>
                <a href="<?=base_url();?>account/edit/<?= $this->sess->user_id;?>">
                  <i class="icon-user"></i> Profile - <?= $this->sess->user_name ?>
                </a>
              </li>
          <?php } ?>
          <li>
            <a href="#" onclick="modalchangepassword();">
              <i class="icon-key"></i> Change Password
            </a>
          </li>
          <li>
            <a href="<?=base_url();?>download/tutorial">
              <i class="icon-cloud-download"></i> Download Manual Book
            </a>
          </li>
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
            <a href="<?=base_url();?>member/logout">
              <i class="icon-logout"></i> Log Out </a>
          </li>
        </ul>
      </li>

      <!-- end manage user dropdown -->
      <!--<li class="dropdown dropdown-quick-sidebar-toggler">
                             <a id="headerSettingButton" class="mdl-button mdl-js-button mdl-button--icon pull-right" data-upgraded=",MaterialButton">
	                           <i class="material-icons">settings</i>
	                        </a>
                        </li>-->
    </ul>
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
        			<tr>
    						<td colspan="2">
                  <legend><?=$this->lang->line("llogin");?></legend>
                </td>
                <td>
                  <label id="loginya"></label>
                </td>
                <td>
                <?=$this->lang->line("lname");?>
                </td>
                <td>
                  <label id="namenya"></label>
                </td>
    					</tr>

        			<tr>
                <?php if ($this->sess->user_type == 2) { ?>
        			<tr>
    						<td>
                  <?=$this->lang->line("loldpassword");?>
                </td>
                <td>
                  <input type="password" name="oldpass" id="oldpass" class="form-control"/>
                </td>
        			<?php } ?>

    						<td>
                  <?=$this->lang->line("lnewpassword");?>
                </td>
                <td>
                  <input type="password" name="pass" id="pass" class="form-control"/>
                </td>

    						<td>
                  <?=$this->lang->line("lconfirm_password");?>
                </td>
                <td>
                  <input type="password" name="cpass" id="cpass" value="" class="form-control"/>
                </td>
    					</tr>
    				</table>
            <span id="capslockoldpass" style="color:red; display: none;">Capslock is on!</span>
            <div class="text-right">
              <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="closemodalchangepassword();"/>
              <input class="btn btn-primary" type="submit" name="btnsave" id="btnsave" value=" Save " />
            </div>
    			</form>

        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function frmchangepass_onsubmit()
  {
    jQuery.post("<?=base_url()?>user/savepass/<?=$this->sess->user_id?>", jQuery("#frmchangepass").serialize(),
      function(r)
      {
        if (confirm(alert(r.message))) {
          window.location = '<?php echo base_url()?>dashboard';
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

  function modalchangepassword(){
    jQuery.post("<?=base_url()?>user/changepass/<?=$this->sess->user_id?>", {},function(r){
      console.log("r : ", r);
        if (r.error == "false")
        {
          alert("Error, please contact Administrator");
        }else {
          $("#loginya").html(r.row.user_login);
          $("#namenya").html(r.row.user_name);
          $("#modalchangepassword").show();
        }
      }, "json");
  }

  function closemodalchangepassword(){
    $("#modalchangepassword").hide();
  }

  document.querySelector("#oldpass").addEventListener('keyup', checkCapsLock);
  document.querySelector("#oldpass").addEventListener('mousedown', checkCapsLock);
  document.querySelector("#pass").addEventListener('keyup', checkCapsLock);
  document.querySelector("#pass").addEventListener('mousedown', checkCapsLock);

  function checkCapsLock(e) {
  	var caps_lock_on = e.getModifierState('CapsLock');
    //
  	if(caps_lock_on == true){
      $("#capslockoldpass").show();
  	}else{
      $("#capslockoldpass").hide();
    }
  }

</script>
