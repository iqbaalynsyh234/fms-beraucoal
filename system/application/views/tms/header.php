<div class="page-header-inner ">
  <!-- logo start -->
  <!--<div class="page-logo" style="border:2px solid black; margin-left:-20px;" >-->
  <div class="page-logo" style="border:0px solid black; margin-left:-25px;">
    <a href="#">
    <!--<img alt="" src="<?php echo base_url();?>assets/dashboard/assets/img/logo-back.png">-->
		<img alt="" src="<?php echo base_url();?>assets/dashboard/assets/img/logoooo.png" height="55%">
		<span class="logo-default"></span></a>
  </div>
  <!-- start mobile menu -->
  <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
    <span></span>
  </a>
  <!-- end mobile menu -->
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
      <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
          <!--<i class="fa fa-envelope-o"></i>-->
          <button type="button" class="btn btn-warning waves-effect waves-light" title="Notification">
            <i class="fa fa-warning"> <!-- this is total alert--></i>
            <!--<span class="badge headerBadgeColor2"> 2 </span>-->
          </button>
        </a>
        <ul class="dropdown-menu animated slideInDown">
          <li class="external">
            <h3><span class="bold">Notification</span></h3>
            <!--<span class="notification-label cyan-bgcolor">New 2</span>-->
          </li>
          <div id="devicenotif">

          </div>
        </ul>
      </li>
      <!-- end message dropdown -->

      <!-- start manage user dropdown -->
      <!--<li class="dropdown dropdown-user">-->
      <li class="dropdown dropdown-extended dropdown-inbox" id="mo1">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <!--<img alt="" class="img-circle " src="<?php echo base_url();?>assets/dashboard/assets/img/dp.jpg" />-->
                                <!--<span class="username username-hide-on-mobile">  <?=$this->sess->user_name;?>  </span>
                                <i class="fa fa-angle-down"></i>-->
									<button type="button"
										class="btn btn-default waves-effect waves-light" title="Profile">
										<i class="fa fa-user"></i>
									</button>
                            </a>
        <ul class="dropdown-menu dropdown-menu-default animated jello">
          <li>
            <a href="<?=base_url();?>user/add/<?=$this->sess->user_id;?>">
              <i class="icon-user"></i> Profile
            </a>
          </li>
          <li>
            <a href="#">
              <i class="icon-key"></i> Change Password
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
