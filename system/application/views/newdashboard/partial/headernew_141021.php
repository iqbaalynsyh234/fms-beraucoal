<div class="page-header-inner ">
  <div class="page-logo">
    <a href="<?php echo base_url();?>maps">
    <img alt="" id="thislogo" src="<?php echo base_url();?>/assets/bib/images/temanindobara_inner.png" width="80%;" height="50px;" style="position:center;margin-left:20px;margin-top:-10px;">
		<span id="thislogo2" style="display:none;">TIB</span>
  </a>
  </div>
  <ul class="nav navbar-nav navbar-left in">
    <li>
      <a href="#" class="menu-toggler sidebar-toggler" onclick="showhidelogo();">
        <i class="icon-menu"  style="color:white;"></i>
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
    <ul class="nav navbar-nav pull-right">
      <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <i class="fa fa-bell-o"></i>
            <span class="badge headerBadgeColor1" id="totalnotifmaintenance" style="display:none;"> 6 </span>
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
          </li>
        </ul>
      </li>

      <li class="dropdown dropdown-extended dropdown-inbox" id="mo1">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
            <img alt="" class="img-circle " src="assets/img/dp.jpg" />
            <span class="username username-hide-on-mobile" style="color:white;"> <?php echo $this->sess->user_name; ?> </span>
            <i class="fa fa-angle-down"></i>
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

    </ul>
  </div>
</div>
