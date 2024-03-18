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

$resultactive   = $this->dashboardmodel->vehicleactive();
$resultexpired  = $this->dashboardmodel->vehicleexpired();
$resulttotaldev = $this->dashboardmodel->totaldevice();

?>
  <style>
    #newex3 {
      color: white;
      height: 554px;
      overflow-x: hidden;
      overflow-x: auto;
      text-align:justify;
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
    /* #fivereportheader {
    padding: 10px;
    cursor: move;
    z-index: 10;
    background-color: #2196F3;
    color: #fff;
  } */
  </style>
  <div class="sidemenu-container navbar-collapse collapse fixed-menu" style="border:0px solid black; margin-left:0px;">
    <!-- mn monitoring -->

    <div id="newex3">
      <div id="remove-scroll">
        <?php if (isset($code_view_menu)) {?>
          <!-- <?php echo $code_view_menu ?> -->
          <?php if ($code_view_menu == "monitor") {?>
            <div id="sidebar_monitoring">
              <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                <li class="sidebar-user-panel">
                  <div class="profile-usertitle-job">
                    Total(<a href="<?=base_url();?>maps" style="color:yellow;"><?=$total_vehicle;?></a>) |
                    Online(<a href="<?=base_url();?>maps/online" style="color:yellow;"><?=$total_online;?></a>) |
                    Offline(<a href="<?=base_url();?>maps/offline" style="color:yellow;"><?=$total_offline;?></a>)
                  </div>
                </li>

                   <li class="nav-item">
                     <input type="text" name="searchnopol" id="searchnopol" placeholder="input vehicle no" size="15px;" style="margin-left: 1%;">
                     <button type="button" class="btn btn-success btn-sm" id="btnSearchNopol" onclick="forsearchinput()">
                       <span class="fa fa-search"></span>
                     </button>
                     <a href="<?=base_url();?>dashboard" class="nav-link nav-toggle">
                       <i class="material-icons">dashboard</i>
                       <span class="title">Dashboard</span>
                     </a>
                   </li>

                <li>
                  <a href="<?php echo base_url()?>maps/outofgeofence" class="nav-link nav-toggle">
                    <i class=""></i><small>Out Of Geofence</small>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="#" class="nav-link nav-toggle">
                    <i class=""></i><small>List Vehicle</small>
                    <span class="arrow"></span>
                  </a>

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

    							<ul class="sub-menu">
                    <li class="nav-item">
                      <a href="<?=base_url();?>maps" class="nav-link"><small>All Vehicle</small></a>
                    </li>
                    <?php
    					$vehicledata = $this->dashboardmodel->getvehicle_bydefault2();
              // echo "<pre>";
              // var_dump($vehicledata);die();
              // echo "<pre>";
    						if (isset($vehicle_list) && (count($vehicle_list)>0)){
    							for ($i=0;$i<count($vehicle_list);$i++){
    								/*if($vehicle_list[$i]->auto_last_engine == "ON"){
    										$engine_tag = "<font color='blue'>ON</font>";
    								}else{
    									$engine_tag = "<font color='grey'>OFF</font>";
    								}

    								if($vehicle_list[$i]->auto_status == "P"){
    									$status_tag = "<font color='blue'>Online</font>";
    								}else if($vehicle_list[$i]->auto_status == "K"){
    									$status_tag = "<font color='yellow'>Online</font>";
    								}else if($vehicle_list[$i]->auto_status == "K"){
    									$status_tag = "<font color='grey'>Offline</font>";
    								}else{
    									$status_tag = "-";
    								}*/
    						?>
                      <li class="nav-item">
                        <!-- <a href="<?=base_url();?>maps/tracking/<?=$vehicle_list[$i]->vehicle_id;?>" class="nav-link"><small><?=$vehicle_list[$i]->vehicle_no;?> </small></a> -->
                        <a href="#" class="nav-link" onclick="forgetcenter2('<?=$vehicledata[$j]->vehicle_id;?>')"><small><?=$vehicle_list[$i]->vehicle_no;?> </small></a>
                      </li>
                      <?php }} ?>
                  </ul>
                </li>
                <?php
    										if (isset($company) && (count($company)>0)){
    											for ($i=0;$i<count($company);$i++){
    											$totaldata = $this->dashboardmodel->gettotalengine($company[$i]->company_id);
    											$totalengine = explode("|", $totaldata);

    											$vehicledata = $this->dashboardmodel->getvehicle_bycompany_master($company[$i]->company_id);
    										?>
                  <li class="nav-item">
                    <a href="javascript:;" class="nav-link nav-toggle">

                      <small><?=$company[$i]->company_name." "."(".$totalengine[2].")";?></small><span class="arrow"></span>
                    </a>

                    <ul class="sub-menu">
                      <li class="nav-item">
                        <a href="<?=base_url();?>maps" class="nav-link-togle">
                          <small>All <?=$company[$i]->company_name;?></small></a> <span class="arrow"></span>
                      </li>
                      <!-- looping vehicle -->
                      <?php
    											if (isset($vehicledata) && (count($vehicledata)>0)){
    												for ($j=0;$j<count($vehicledata);$j++){
    													$lastengine = "";
    													$lastspeed = "";

    													//check expired
    													if($vehicledata[$j]->vehicle_active_date2 < date("Ymd")){
    														$view_status = "<font color='black'>Expired</font>";
    													}else{
    														$datajson = $this->dashboardmodel->getjson_status2($vehicledata[$j]->vehicle_device);
    														if($datajson != ""){
    															// if($datajson->auto_last_speed > 0){
    																$lastengine = $datajson->auto_last_engine;
    																$lastspeed = round($datajson->auto_last_speed)." kph";
    																if($lastspeed > 0){
    																	$view_status = "<font color='yellow'>".$lastspeed." Moving </font>";
    																}else{
    																	$view_status = "<font color='white'>Stop ". $datajson->auto_last_update ."</font>";
    																}
    															// }else{
    															// 	$view_status = "<font color='black'>Offline</font>";
    															// }
    														}
    													}
    												?>
                        <li class="nav-item">

                          <!-- <a href="<?=base_url();?>maps/tracking/<?=$vehicledata[$j]->vehicle_id;?>" class="nav-link-togle"> -->
                          <a href="#" class="nav-link-togle" onclick="forgetcenter2('<?=$vehicledata[$j]->vehicle_device;?>')">
                            <small><?=$vehicledata[$j]->vehicle_no." ".$view_status;?></small></a> <span class="arrow"></span>
                        </li>
                        <?php }} ?>
                          <!-- end looping -->
                    </ul>
                  </li>
                  <?php }} ?>
                    <!-- end looping company -->
              </ul>
    						<?php }else{?>
                  <li>
                    <a href="<?php echo base_url()?>maps" class="nav-link nav-toggle">
                      <i class=""></i><small>MAPS</small>
                    </a>
                  </li>
    						<? } ?>
            </div>
        <?php }elseif ($code_view_menu == "configuration") {?>
          <div id="sidebar_config">
            <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
              <li class="menu-heading">
                <span>-- Configuration</span>
              </li>
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
                Total(<a href="<?=base_url();?>maps" style="color:yellow;"><?=$total_vehicle;?></a>) |
                Online(<a href="<?=base_url();?>maps/online" style="color:yellow;"><?=$total_online;?></a>) |
                Offline(<a href="<?=base_url();?>maps/offline" style="color:yellow;"><?=$total_offline;?></a>)
              </div>
            </li>
            <li class="nav-item">
              <input type="text" name="searchnopol" id="searchnopol" placeholder="input vehicle no" size="15px;" style="margin-left: 1%;">
              <button type="button" class="btn btn-success btn-sm" id="btnSearchNopol" onclick="forsearchinput()">
                <span class="fa fa-search"></span>
              </button>
              <a href="<?=base_url();?>dashboard" class="nav-link nav-toggle">
                <i class="material-icons">dashboard</i>
                <span class="title">Dashboard</span>
              </a>
            </li>

            <li>
              <a href="<?php echo base_url()?>maps/outofgeofence" class="nav-link nav-toggle">
                <i class=""></i><small>Out Of Geofence</small>
              </a>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link nav-toggle">
                <i class=""></i><small>List Vehicle</small>
                <span class="arrow"></span>
              </a>

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

							<ul class="sub-menu">
                <li class="nav-item">
                  <a href="<?=base_url();?>maps" class="nav-link"><small>All Vehicle</small></a>
                </li>
                <?php
					$vehicledata = $this->dashboardmodel->getvehicle_bydefault2();
          // echo "<pre>";
          // var_dump($vehicledata);die();
          // echo "<pre>";
						if (isset($vehicle_list) && (count($vehicle_list)>0)){
							for ($i=0;$i<count($vehicle_list);$i++){
								/*if($vehicle_list[$i]->auto_last_engine == "ON"){
										$engine_tag = "<font color='blue'>ON</font>";
								}else{
									$engine_tag = "<font color='grey'>OFF</font>";
								}

								if($vehicle_list[$i]->auto_status == "P"){
									$status_tag = "<font color='blue'>Online</font>";
								}else if($vehicle_list[$i]->auto_status == "K"){
									$status_tag = "<font color='yellow'>Online</font>";
								}else if($vehicle_list[$i]->auto_status == "K"){
									$status_tag = "<font color='grey'>Offline</font>";
								}else{
									$status_tag = "-";
								}*/
						?>
                  <li class="nav-item">
                    <!-- <a href="<?=base_url();?>maps/tracking/<?=$vehicle_list[$i]->vehicle_id;?>" class="nav-link"><small><?=$vehicle_list[$i]->vehicle_no;?> </small></a> -->
                    <a href="#" class="nav-link" onclick="forgetcenter2('<?=$vehicledata[$j]->vehicle_id;?>')"><small><?=$vehicle_list[$i]->vehicle_no;?> </small></a>
                  </li>
                  <?php }} ?>
              </ul>
            </li>
            <?php
										if (isset($company) && (count($company)>0)){
											for ($i=0;$i<count($company);$i++){
											$totaldata = $this->dashboardmodel->gettotalengine($company[$i]->company_id);
											$totalengine = explode("|", $totaldata);

											$vehicledata = $this->dashboardmodel->getvehicle_bycompany_master($company[$i]->company_id);
										?>
              <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">

                  <small><?=$company[$i]->company_name." "."(".$totalengine[2].")";?></small><span class="arrow"></span>
                </a>

                <ul class="sub-menu">
                  <li class="nav-item">
                    <a href="<?=base_url();?>maps" class="nav-link-togle">
                      <small>All <?=$company[$i]->company_name;?></small></a> <span class="arrow"></span>
                  </li>
                  <!-- looping vehicle -->
                  <?php
											if (isset($vehicledata) && (count($vehicledata)>0)){
												for ($j=0;$j<count($vehicledata);$j++){
													$lastengine = "";
													$lastspeed = "";

													//check expired
													if($vehicledata[$j]->vehicle_active_date2 < date("Ymd")){
														$view_status = "<font color='black'>Expired</font>";
													}else{
														$datajson = $this->dashboardmodel->getjson_status2($vehicledata[$j]->vehicle_device);
														if($datajson != ""){
															// if($datajson->auto_last_speed > 0){
																$lastengine = $datajson->auto_last_engine;
																$lastspeed = round($datajson->auto_last_speed)." kph";
																if($lastspeed > 0){
																	$view_status = "<font color='yellow'>".$lastspeed." Moving </font>";
																}else{
																	$view_status = "<font color='white'>Stop ". $datajson->auto_last_update ."</font>";
																}
															// }else{
															// 	$view_status = "<font color='black'>Offline</font>";
															// }
														}
													}
												?>
                    <li class="nav-item">

                      <!-- <a href="<?=base_url();?>maps/tracking/<?=$vehicledata[$j]->vehicle_id;?>" class="nav-link-togle"> -->
                      <a href="#" class="nav-link-togle" onclick="forgetcenter2('<?=$vehicledata[$j]->vehicle_device;?>')">
                        <small><?=$vehicledata[$j]->vehicle_no." ".$view_status;?></small></a> <span class="arrow"></span>
                    </li>
                    <?php }} ?>
                      <!-- end looping -->
                </ul>
              </li>
              <?php }} ?>
                <!-- end looping company -->
          </ul>
						<?php }else{?>
              <li>
                <a href="<?php echo base_url()?>maps" class="nav-link nav-toggle">
                  <i class=""></i><small>MAPS</small>
                </a>
              </li>
						<? } ?>
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
                <a href="inoutgeofence" class="nav-link ">
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
              <a href="<?php echo base_url()?>pbi_ritase" class="nav-link ">
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
              <!--<a href="<?=base_url();?>transporter/user" class="nav-link ">-->
			  <a href="<?=base_url();?>account" class="nav-link ">
                <span class="title"><small>User</small></span>
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
              <a href="<?=base_url();?>destinationmaster" class="nav-link ">
                <span class="title"><small>Destination Master</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>ritase" class="nav-link ">
                <span class="title"><small>Ritase</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>poidata" class="nav-link ">
                <span class="title"><small>POI (Point of Interest)</small></span>
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
            <li class="nav-item">
              <a href="<?=base_url();?>poolmaster" class="nav-link">
                <span class="title"><small>Pool Master</small></span>
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

  <div id="modalfivereport" style="display: none;">
    <div id="fivereportheader"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-topline-yellow">
          <div class="card-head">
            <header>List of Vehicle</header>
            <div class="tools">
              <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
              <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
              <button type="button" class="btn btn-danger" name="button" onclick="closemodalfivereport();">X</button>
            </div>
          </div>
          <div class="card-body">
            <table class="table" class="display" class="full-width">
              <thead>
                <tr>
                  <th></th>
                  <th>No</th>
                  <th>Vehicle</th>
                  <th>Option</th>
                </tr>
              </thead>
              <tbody id="vehiclelistfivereport">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
