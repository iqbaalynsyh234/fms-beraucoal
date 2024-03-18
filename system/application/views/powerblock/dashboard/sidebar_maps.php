<?php
// $company = "";
$company                     = $this->dashboardmodel->getcompany_byowner();
$rstatus                     = $this->dashboardmodel->gettotalstatus($this->sess->user_id);
$getvehicle_byowner          = $this->dashboardmodel->getvehicle_byowner();
$totalmobilnya               = sizeof($getvehicle_byowner);
// $totalmobilnya      = 0;
  if ($totalmobilnya == 0) {
    $name         = "0";
    $host         = "0";
  }else {
    $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
    $name         = $arr[0];
    $host         = $arr[1];
  }

//tidak dipakai sementara
// $rstatus = $this->dashboardmodel->forcounttotal($this->sess->user_id);
// sizeof($resultactive).'-'.sizeof($resultexpired).'-'.sizeof($resulttotaldev)
// echo "<pre>";
// var_dump($getvehicle_byowneringofence);die();
// echo "<pre>";

$datastatus    = explode("|", $rstatus);
$total_online  = $datastatus[0]+$datastatus[1]; //p + K
$total_vehicle = $datastatus[3];
// $total_online  = $datastatus[0]; //p + K
$total_offline = $datastatus[2];

?>
<style>
  #newex3 {
    color: white;
    height: 554px;
    overflow-x: hidden;
    overflow-x: auto;
    text-align: justify;
  }

  div#modalfivereport {
    margin-top: 5%;
    margin-left: 30%;
    max-height: 500px;
    max-width: 900px;
    overflow-x: auto;
    position: absolute;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }
</style>

<div class="sidemenu-container navbar-collapse collapse fixed-menu" style="border:0px solid black; margin-left:0px;">
  <!-- mn monitoring -->

  <div id="newex3">
    <div id="remove-scroll">
      <?php if (isset($code_view_menu)) {?>
        <?php if ($code_view_menu == "monitor") {?>
          <div id="sidebar_monitoring">
            <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
              <li class="sidebar-user-panel">
                <div class="profile-usertitle-job">
                  Total(
                  <a href="<?=base_url();?>maps" style="color:yellow;">
                    <?=$total_vehicle;?>
                  </a>) | Online(
                  <a href="<?=base_url();?>maps/online" style="color:yellow;">
                    <?=$total_online;?>
                  </a>) | Offline(
                  <a href="<?=base_url();?>maps/offline" style="color:yellow;">
                    <?=$total_offline;?>
                  </a>)
                </div>
              </li>

              <li class="nav-item">
                <input type="text" name="searchnopol" id="searchnopol" placeholder="input vehicle no" size="10px;" style="margin-left: 1%;">
                <button type="button" class="btn btn-success btn-sm" id="btnSearchNopol" onclick="forsearchinput()">
                  <span class="fa fa-search"></span>
                </button>
                <?php
              $userid = $this->sess->user_id;
               if ($userid == 3212 || $userid == 1445) {?>
                  <a href="<?=base_url();?>dashboard" class="nav-link nav-toggle">
                    <i class="material-icons">dashboard</i>
                    <span class="title">Dashboard</span>
                  </a>
              </li>
              <?php } ?>

                <li>
                  <a href="<?php echo base_url()?>maps/outofgeofence" class="nav-link nav-toggle">
                    <i class=""></i><small>Out Of Geofence</small>
                  </a>
                </li>

                <li class="nav-item">
          			  <?php
                    if (isset($url_code_view)) {
                      $url_code_viewfix = $url_code_view;
                    }else {
                      $url_code_viewfix = 0;
                    }
                  ?>
    						<?php
    						//echo $cur_url;
    						if($url_code_viewfix == 1){?>
                  <a href="#" class="nav-link nav-toggle">
                    <i class=""></i><small>List Vehicle</small>
                    <span class="arrow"></span>
                  </a>
    							<ul class="sub-menu">
                    <li class="nav-item">
                      <a href="<?=base_url();?>maps" class="nav-link"><small>All Vehicle</small></a>
                    </li>
                    <?php
            						if (isset($vehicledata) && (count($vehicledata)>0)){
            							for ($i=0;$i<count($vehicledata);$i++){
            						?>
                      <li class="nav-item">
                        <a href="#" class="nav-link" onclick="forgetcenter2('<?=$vehicledata[$i]['vehicle_device'];?>')"><small><?=$vehicledata[$i]['vehicle_no'].' '.$vehicledata[$i]['vehicle_name'];?> </small></a>
                      </li>
                      <?php }} ?>
                  </ul>
                </li>

                <?php if (isset($vehicle)) {?>
                  <?php for ($j=0; $j < sizeof($vehicle); $j++) {?>
                    <li class="nav-item">
                      <a href="javascript:;" class="nav-link nav-toggle">
                        <small><?=$vehicle[$j]['company_name'];?></small><span class="arrow"></span>
                      </a>

                      <ul class="sub-menu">
                        <li class="nav-item">
                          <a href="<?=base_url();?>maps/area/<?=$vehicle[$j]['company_id'];?>" class="nav-link-togle">
                            <small>All <?=$vehicle[$j]['company_name'];?></small></a> <span class="arrow"></span>
                        </li>

                        <?php for ($k=0; $k < sizeof($vehicle[$j]['vehicle']); $k++) {?>
                          <?php
                          $lastengine = "";
													$lastspeed = "";

													//check expired
													if($vehicle[$j]['vehicle'][$k]->vehicle_active_date2 < date("Ymd")){
														$view_status = "<font color='black'>Expired</font>";
													}else {
                            $datajson = $this->dashboardmodel->getjson_status2($vehicle[$j]['vehicle'][$k]->vehicle_device);
														if($datajson != ""){
															// if($datajson->auto_last_speed > 0){
																$lastengine = $datajson->auto_last_engine;
																$lastspeed  = round($datajson->auto_last_speed)." kph";
																if($lastspeed > 0){
																	$view_status = "<font color='yellow'>".$lastspeed." Moving </font>";
																}else{
																	$view_status = "<font color='white'>Stop </br>". date("d-m-Y H:i:s", strtotime($datajson->auto_last_update)) ."</font>";
																}
														}
                          }
                          ?>
                          <li class="nav-item">
                            <a href="#" class="nav-link-togle" onclick="forgetcenter2('<?=$vehicle[$j]['vehicle'][$k]->vehicle_device;?>')">
                              <small><?=$vehicle[$j]['vehicle'][$k]->vehicle_no . ' ' . $view_status;?></small></a>
                              <span class="arrow"></span>
                          </li>
                        <?php } ?>

                      </ul>
                    </li>
                  <?php } ?>
                <?php } ?>
            <?php } ?>
            </div>
      <?php }elseif ($code_view_menu == "configuration") {?>
        <div id="sidebar_config">
          <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="menu-heading">
              <span>-- Configuration</span>
            </li>

            <?php
            $userid = $this->sess->user_id;
             if ($userid == "389") {?>
               <li class="nav-item start">
                 <span class="label label-rouded label-menu label-danger">new</span>
                 <a href="<?=base_url();?>project/schedule" class="nav-link">
                   <span class="title"><small>Project Schedule</small></span>
                 </a>
               </li>
            <?php } ?>

            <li class="nav-item start">
              <!-- <a href="<?=base_url();?>user/add/<?=$this->sess->user_id;?>" class="nav-link "> -->
              <a href="<?=base_url();?>account/add/<?=$this->sess->user_id;?>" class="nav-link">
                <span class="title"><small>Private Information</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <!-- <a href="<?=base_url();?>transporter/branchoffice" class="nav-link "> -->
              <a href="<?=base_url();?>account/branch" class="nav-link ">
                <span class="title"><small>Branch Office</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <!-- <a href="<?=base_url();?>transporter/branchoffice" class="nav-link "> -->
              <a href="<?=base_url();?>account/subbranchoffice" class="nav-link ">
                <span class="title"><small>Sub Branch Office</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <!-- <a href="<?=base_url();?>transporter/mod_vehicle_maintenance" class="nav-link "> -->
                <a href="<?=base_url();?>vehicles" class="nav-link">
                <span class="title"><small>Vehicle</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <!--<a href="<?=base_url();?>transporter/driver" class="nav-link ">-->
             <a href="<?=base_url();?>driver" class="nav-link ">
                <span class="title"><small>Driver</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <!--<a href="<?=base_url();?>transporter/customer" class="nav-link ">-->
              <a href="<?=base_url();?>account/customer" class="nav-link ">
                <span class="title"><small>Customer</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <!--<a href="<?=base_url();?>transporter/customer" class="nav-link ">-->
              <a href="<?=base_url();?>account/subcustomer" class="nav-link ">
                <span class="title"><small>Sub Customer</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <!--<a href="<?=base_url();?>transporter/user" class="nav-link ">-->
               <a href="<?=base_url();?>account" class="nav-link ">
                <span class="title"><small>User</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>poolmaster" class="nav-link">
                <span class="title"><small>Pool</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>destinationmaster" class="nav-link ">
                <span class="title"><small>Destination</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>streetdata" class="nav-link ">
                <span class="title"><small>Street</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>geofencedata" class="nav-link ">
                <span class="title"><small>Geofence Setup</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>geofencedatalist" class="nav-link ">
                <span class="title"><small>Geofence List</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>poidata" class="nav-link ">
                <span class="title"><small>POI (Point of Interest)</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>ritase" class="nav-link ">
                <span class="title"><small>Ritase</small></span>
              </a>
            </li>
            <?php
            $userid = $this->sess->user_id;
            if ($userid == 4050) {?>
              <li class="nav-item">
               <a href="<?=base_url();?>tms" class="nav-link">
                 <span class="label label-rouded label-menu label-danger">beta</span>
                 <span class="title"><small>TMS</small></span>
               </a>
             </li>
            <?php } ?>
          </ul>
        </div>
      <?php }elseif ($code_view_menu == "report") {?>
        <div id="sidebar_report">
          <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="menu-heading">
              <span>-- Report</span>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>fib" class="nav-link">
                <span class="title"><small>FIB Dashboard</small></span>
                <span class="label label-rouded label-menu label-danger">new</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>pbimanage" class="nav-link ">
                <span class="title"><small>SJ List</small></span>
                <span class="label label-rouded label-menu label-danger">new</span>
              </a>
            </li>

            <li class="nav-item start">
              <a href="#" class="nav-link ">
                <span class="title" onclick="showmodalfivereport()"><small>Overspeed</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link ">
                <span class="title" onclick="showmodalfivereport()"><small>Parking</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link ">
                <span class="title" onclick="showmodalfivereport()"><small>History</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link ">
                <span class="title" onclick="showmodalfivereport()"><small>Geofence</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>tripreport/tripmileage" class="nav-link ">
                <span class="title"><small>Trip Mileage</small></span>
              </a>
            </li>

            <li class="nav-item">
              <!-- <a href="<?=base_url();?>report/mn_playback" class="nav-link "> -->
                <a href="<?=base_url();?>tripreport/playback" class="nav-link ">
                <span class="title"><small>Playback</small></span>
              </a>
            </li>

            <li class="nav-item">
              <!-- <a href="<?=base_url();?>report/mn_playback" class="nav-link "> -->
                <a href="<?=base_url();?>inoutgeofence" class="nav-link ">
                <span class="title"><small>In Out Geofence</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url()?>pbi_operationalreport/mn_dataoperational" class="nav-link ">
                <span class="title"><small>Operational Report</small></span>
              </a>
            </li>

            <li class="nav-item">
              <!-- <a href="<?=base_url();?>report/mn_driver_hist" class="nav-link "> -->
                <a href="<?=base_url();?>driverhistory" class="nav-link ">
                <span class="title"><small>Driver History</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url()?>pbi_ritase" class="nav-link">
                <span class="title"><small>Ritase Report</small></span>
              </a>
            </li>
          </ul>
        </div>
      <?php }else {?>
        <div id="sidebar_billing">
          <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="menu-heading">
              <span>-- Billing</span>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link nav-toggle">
                <i class=""></i><small>Vehicle Status</small>
                <span class="arrow"></span>
              </a>

              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="<?=base_url();?>billing/active" class="nav-link"><small>Active (<?php echo sizeof($resultactive); ?>)</small></a>
                </li>

                <li class="nav-item">
                  <a href="<?=base_url();?>billing/expired" class="nav-link"><small>Expired (<?php echo sizeof($resultexpired); ?>)</small></a>
                </li>

                <li class="nav-item">
                  <a href="<?=base_url();?>billing/devices" class="nav-link"><small>Devices (<?php echo sizeof($resulttotaldev); ?>)</small></a>
                </li>
              </ul>
            </li>

          </ul>
        </div>
      <?php } ?>
  <?php } ?>

  <div id="sidebar_monitoring" style="display: none;">
    <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
      <li class="sidebar-user-panel">
        <div class="profile-usertitle-job">
          Total(
          <a href="<?=base_url();?>maps" style="color:yellow;">
            <?=$total_vehicle;?>
          </a>) | Online(
          <a href="<?=base_url();?>maps/online" style="color:yellow;">
            <?=$total_online;?>
          </a>) | Offline(
          <a href="<?=base_url();?>maps/offline" style="color:yellow;">
            <?=$total_offline;?>
          </a>)
        </div>
      </li>

      <li class="nav-item">
        <input type="text" name="searchnopol" id="searchnopol" placeholder="input vehicle no" size="10px;" style="margin-left: 1%;">
        <button type="button" class="btn btn-success btn-sm" id="btnSearchNopol" onclick="forsearchinput()">
          <span class="fa fa-search"></span>
        </button>
        <?php
      $userid = $this->sess->user_id;
       if ($userid == 3212 || $userid == 1445) {?>
          <a href="<?=base_url();?>dashboard" class="nav-link nav-toggle">
            <i class="material-icons">dashboard</i>
            <span class="title">Dashboard</span>
          </a>
      </li>
      <?php } ?>

        <li>
          <a href="<?php echo base_url()?>maps/outofgeofence" class="nav-link nav-toggle">
            <i class=""></i><small>Out Of Geofence</small>
          </a>
        </li>

        <li class="nav-item">
          <?php
            if (isset($url_code_view)) {
              $url_code_viewfix = $url_code_view;
            }else {
              $url_code_viewfix = 0;
            }
          ?>
                <?php
          //echo $cur_url;
          if($url_code_viewfix == 1){?>
            <a href="#" class="nav-link nav-toggle">
              <i class=""></i><small>List Vehicle</small>
              <span class="arrow"></span>
            </a>
            <ul class="sub-menu">
              <li class="nav-item">
                <a href="<?=base_url();?>maps" class="nav-link"><small>All Vehicle</small></a>
              </li>
            </ul>
        </li>

        <?php if (isset($vehicle)) {?>
          <?php for ($j=0; $j < sizeof($vehicle); $j++) {?>
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <small><?=$vehicle[$j]['company_name'];?></small><span class="arrow"></span>
              </a>

              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="<?=base_url();?>maps/area/<?=$vehicle[$j]['company_id'];?>" class="nav-link-togle">
                    <small>All <?=$vehicle[$j]['company_name'];?></small></a> <span class="arrow"></span>
                </li>

                <?php for ($k=0; $k < sizeof($vehicle[$j]['vehicle']); $k++) {?>
                  <?php
                  $lastengine = "";
                  $lastspeed = "";

                  //check expired
                  if($vehicle[$j]['vehicle'][$k]->vehicle_active_date2 < date("Ymd")){
                    $view_status = "<font color='black'>Expired</font>";
                  }else {
                    $datajson = $this->dashboardmodel->getjson_status2($vehicle[$j]['vehicle'][$k]->vehicle_device);
                    if($datajson != ""){
                      // if($datajson->auto_last_speed > 0){
                        $lastengine = $datajson->auto_last_engine;
                        $lastspeed  = round($datajson->auto_last_speed)." kph";
                        if($lastspeed > 0){
                          $view_status = "<font color='yellow'>".$lastspeed." Moving </font>";
                        }else{
                          $view_status = "<font color='white'>Stop </br>". date("d-m-Y H:i:s", strtotime($datajson->auto_last_update)) ."</font>";
                        }
                    }
                  }
                  ?>
                  <li class="nav-item">
                    <a href="#" class="nav-link-togle" onclick="forgetcenter2('<?=$vehicle[$j]['vehicle'][$k]->vehicle_device;?>')">
                      <small><?=$vehicle[$j]['vehicle'][$k]->vehicle_no . ' ' . $view_status;?></small></a>
                      <span class="arrow"></span>
                  </li>
                <?php } ?>

              </ul>
            </li>
          <?php } ?>
        <?php } ?>
    <?php } ?>
    </div>

    <!-- report -->
    <div id="sidebar_report" style="display:none">
        <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
          <li class="menu-heading">
            <span>-- Report</span>
          </li>

          <li class="nav-item">
            <a href="<?=base_url();?>fib" class="nav-link">
              <span class="title"><small>FIB Dashboard</small></span>
              <span class="label label-rouded label-menu label-danger">new</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url();?>pbimanage" class="nav-link ">
              <span class="title"><small>SJ List</small></span>
              <span class="label label-rouded label-menu label-danger">new</span>
            </a>
          </li>

          <li class="nav-item start">
            <a href="#" class="nav-link ">
              <span class="title" onclick="showmodalfivereport()"><small>Overspeed</small></span>
            </a>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link ">
              <span class="title" onclick="showmodalfivereport()"><small>Parking</small></span>
            </a>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link ">
              <span class="title" onclick="showmodalfivereport()"><small>History</small></span>
            </a>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link ">
              <span class="title" onclick="showmodalfivereport()"><small>Geofence</small></span>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?=base_url();?>tripreport/tripmileage" class="nav-link ">
              <span class="title"><small>Trip Mileage</small></span>
            </a>
          </li>

          <li class="nav-item">
            <!-- <a href="<?=base_url();?>report/mn_playback" class="nav-link "> -->
              <a href="<?=base_url();?>tripreport/playback" class="nav-link ">
              <span class="title"><small>Playback</small></span>
            </a>
          </li>

          <li class="nav-item">
            <!-- <a href="<?=base_url();?>report/mn_playback" class="nav-link "> -->
              <a href="<?=base_url();?>inoutgeofence" class="nav-link ">
              <span class="title"><small>In Out Geofence</small></span>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url()?>pbi_operationalreport/mn_dataoperational" class="nav-link ">
              <span class="title"><small>Operational Report</small></span>
            </a>
          </li>

          <li class="nav-item">
            <!-- <a href="<?=base_url();?>report/mn_driver_hist" class="nav-link "> -->
              <a href="<?=base_url();?>driverhistory" class="nav-link ">
              <span class="title"><small>Driver History</small></span>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo base_url()?>pbi_ritase" class="nav-link">
              <span class="title"><small>Ritase Report</small></span>
            </a>
          </li>
        </ul>
      </div>

    <!-- configuration -->
    <div id="sidebar_config" style="display:none">
      <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
        <li class="menu-heading">
          <span>-- Configuration</span>
        </li>

        <?php
        $userid = $this->sess->user_id;
         if ($userid == "389") {?>
           <li class="nav-item start">
             <span class="label label-rouded label-menu label-danger">new</span>
             <a href="<?=base_url();?>project/schedule" class="nav-link">
               <span class="title"><small>Project Schedule</small></span>
             </a>
           </li>
        <?php } ?>

        <li class="nav-item start">
            <a href="<?=base_url();?>account/add/<?=$this->sess->user_id;?>" class="nav-link">
            <span class="title"><small>Private Information</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <!-- <a href="<?=base_url();?>transporter/branchoffice" class="nav-link "> -->
            <a href="<?=base_url();?>account/branch" class="nav-link ">
            <span class="title"><small>Branch Office</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <!-- <a href="<?=base_url();?>transporter/branchoffice" class="nav-link "> -->
          <a href="<?=base_url();?>account/subbranchoffice" class="nav-link ">
            <span class="title"><small>Sub Branch Office</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <!-- <a href="<?=base_url();?>transporter/mod_vehicle_maintenance" class="nav-link "> -->
            <a href="<?=base_url();?>vehicles" class="nav-link">
            <span class="title"><small>Vehicle</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <!--<a href="<?=base_url();?>transporter/driver" class="nav-link ">-->
         <a href="<?=base_url();?>driver" class="nav-link ">
            <span class="title"><small>Driver</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <!--<a href="<?=base_url();?>transporter/customer" class="nav-link ">-->
          <a href="<?=base_url();?>account/customer" class="nav-link ">
            <span class="title"><small>Customer</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <!--<a href="<?=base_url();?>transporter/customer" class="nav-link ">-->
          <a href="<?=base_url();?>account/subcustomer" class="nav-link ">
            <span class="title"><small>Sub Customer</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <!--<a href="<?=base_url();?>transporter/user" class="nav-link ">-->
           <a href="<?=base_url();?>account" class="nav-link ">
            <span class="title"><small>User</small></span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url();?>poolmaster" class="nav-link">
            <span class="title"><small>Pool</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <a href="<?=base_url();?>destinationmaster" class="nav-link ">
            <span class="title"><small>Destination</small></span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url();?>streetdata" class="nav-link ">
            <span class="title"><small>Street</small></span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url();?>geofencedata" class="nav-link ">
            <span class="title"><small>Geofence Setup</small></span>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?=base_url();?>geofencedatalist" class="nav-link ">
            <span class="title"><small>Geofence List</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <a href="<?=base_url();?>poidata" class="nav-link ">
            <span class="title"><small>POI (Point of Interest)</small></span>
          </a>
        </li>
        <li class="nav-item start">
          <a href="<?=base_url();?>ritase" class="nav-link ">
            <span class="title"><small>Ritase</small></span>
          </a>
        </li>
        <?php
        $userid = $this->sess->user_id;
        if ($userid == 4050) {?>
          <li class="nav-item">
           <a href="<?=base_url();?>tms" class="nav-link">
             <span class="label label-rouded label-menu label-danger">beta</span>
             <span class="title"><small>TMS</small></span>
           </a>
         </li>
        <?php } ?>
      </ul>
    </div>

    <!-- billing -->
    <div id="sidebar_billing" style="display:none">
      <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
        <li class="menu-heading">
          <span>-- Billing</span>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link nav-toggle">
            <i class=""></i><small>Vehicle Status</small>
            <span class="arrow"></span>
          </a>

          <ul class="sub-menu">
            <li class="nav-item">
              <a href="<?=base_url();?>billing/active" class="nav-link"><small>Active (<?php echo sizeof($resultactive); ?>)</small></a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>billing/expired" class="nav-link"><small>Expired (<?php echo sizeof($resultexpired); ?>)</small></a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>billing/devices" class="nav-link"><small>Devices (<?php echo sizeof($resulttotaldev); ?>)</small></a>
            </li>
          </ul>
        </li>

      </ul>
    </div>

    </div>
  </div>
</div>
