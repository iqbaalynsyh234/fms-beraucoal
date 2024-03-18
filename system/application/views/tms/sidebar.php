  <style>
    div.newex3 {
      color: white;
      width: auto;
      height: 600px;
      overflow: auto;
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

    <div class="newex3">
      <div id="remove-scroll">
        <div id="sidebar_monitoring">
          <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">

            <li class="sidebar-user-panel">
              <div class="profile-usertitle-job">
                <!-- Total(<a href="<?=base_url();?>maps" style="color:yellow;"><?=$total_vehicle;?></a>) |
                Online(<a href="<?=base_url();?>maps/online" style="color:yellow;"><?=$total_online;?></a>) |
                Offline(<a href="<?=base_url();?>maps/offline" style="color:yellow;"><?=$total_offline;?></a>) -->
              </div>
            </li>
            <li class="nav-item">
              <!-- <input type="text" name="searchnopol" id="searchnopol" placeholder="input vehicle no" size="15px;" style="margin-left: 1%;">
              <button type="button" class="btn btn-success btn-sm" id="btnSearchNopol" onclick="forsearchinput()">
                <span class="fa fa-search"></span>
              </button> -->
              <a href="<?=base_url();?>tms" class="nav-link nav-toggle">
                <i class="material-icons">dashboard</i>
                <span class="title">Dashboard</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link nav-toggle">
                <i class=""></i><small>List Vehicle</small>
                <span class="arrow"></span>
              </a>

              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="<?=base_url();?>maps" class="nav-link"><small>All Vehicle</small></a>
                </li>
                  <li class="nav-item">
                    <!-- <a href="<?=base_url();?>maps/tracking/<?=$vehicle_list[$i]->vehicle_id;?>" class="nav-link"><small><?=$vehicle_list[$i]->vehicle_no;?> </small></a> -->
                  </li>
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
                    <a href="<?=base_url();?>maps/area/<?=$company[$i]->company_id;?>" class="nav-link-togle">
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
        </div>

        <!-- report -->
        <div id="sidebar_report" style="display:none">
          <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="menu-heading">
              <span>-- Report</span>
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
              <a href="<?=base_url();?>historynew" class="nav-link ">
                <span class="title"><small>History New</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link ">
                <span class="title" onclick="showmodalfivereport()"><small>Workhour</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link ">
                <span class="title" onclick="showmodalfivereport()"><small>Geofence</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>report" class="nav-link ">
                <span class="title"><small>Trip Mileage</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>report/mn_playback" class="nav-link ">
                <span class="title"><small>Playback</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>report/mn_inout_geofence" class="nav-link ">
                <span class="title"><small>In Out Geofence</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>report/mn_driver_hist" class="nav-link ">
                <span class="title"><small>Driver History</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>transporter/ritase/menu_ritase_report" class="nav-link ">
                <span class="title"><small>Ritase Report</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>report/mn_door_status" class="nav-link ">
                <span class="title"><small>Door Report</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>report/mn_pto_status" class="nav-link ">
                <span class="title"><small>PTO Report</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?=base_url();?>operational_report" class="nav-link ">
                <span class="title"><small>Operational Report</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>operational_report/summary" class="nav-link " target="_blank">
                <span class="title"><small>Operational Report (Summary)</small></span>
              </a>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <small>Summary KM</small><span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <!--<li class="nav-item">
						<a href="<?=base_url();?>dashboard/summary/all" class="nav-link">All</a>
						</li>-->
                <?php
						if (isset($company) && (count($company)>0)){
						for ($i=0;$i<count($company);$i++){ ?>
                  <li class="nav-item">
                    <a href="#" class="nav-link"><small><?=$company[$i]->company_name;?></small></a>
                  </li>
                  <?php }} ?>
              </ul>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>tripreport/history" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title"><small>History Map</small></span>
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
              <a href="<?=base_url();?>tms/customer" class="nav-link ">
			  <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title"><small>Customer</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>tms/gardu" class="nav-link ">
			  <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title"><small>Substation</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>tms/technician" class="nav-link ">
			  <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title"><small>Technician</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>tms/ticketing" class="nav-link ">
			  <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title"><small>Ticketing System</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>user/add/<?=$this->sess->user_id;?>" class="nav-link ">
                <span class="title"><small>Private Information</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>transporter/branchoffice" class="nav-link ">
                <span class="title"><small>Branch Office</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>transporter/user" class="nav-link ">
                <span class="title"><small>User</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>transporter/mod_vehicle_maintenance" class="nav-link ">
                <span class="title"><small>Vehicle</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>transporter/driver" class="nav-link ">
                <span class="title"><small>Driver</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>transporter/customer" class="nav-link ">
                <span class="title"><small>Customer</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>destinationmaster" class="nav-link ">
                <span class="title"><small>Destination Master</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>transporter/ritase" class="nav-link ">
                <span class="title"><small>Ritase</small></span>
              </a>
            </li>
            <li class="nav-item start">
              <a href="<?=base_url();?>poi" class="nav-link ">
                <span class="title"><small>POI (Point of Interest)</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>street" class="nav-link ">
                <span class="title"><small>Street</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="" class="nav-link ">
                <span class="title"><small>Geofence</small></span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?=base_url();?>poolmaster" class="nav-link">
                <span class="title"><small>Pool Master</small></span>
              </a>
            </li>
          </ul>
        </div>

        <!-- billing -->
        <div id="sidebar_billing" style="display:none">
          <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
            <li class="menu-heading">
              <span>-- Billing</span>
            </li>
            <li class="menu-heading">
              <span>Under Development--</span>
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
