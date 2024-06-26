<style>
  /* White sidebar color */
  .white-sidebar-color .sidemenu-container {
    background-color: #ffffff;
  }

  .white-sidebar-color .sidemenu-container .sidemenu>li.active.open>a,
  .white-sidebar-color .sidemenu-container .sidemenu>li.active>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.active.open>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.active>a {
    background-color: #74bf43;
    border-top-color: transparent;
    color: white;
  }

  /* .white-sidebar-color .sidemenu-container .sidemenu>li>a{
	color: #555;
	border-bottom:none;
	background-color: #ffffff;
} */
  .white-sidebar-color .sidemenu-container .sidemenu>li.open>a,
  .white-sidebar-color .sidemenu-container .sidemenu>li:hover>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.open>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li:hover>a {
    background-color: #74bf43;
    opacity: 0.8;
    border-top-color: transparent;
    color: white;
  }

  .white-sidebar-color .user-panel,
  .white-sidebar-color .txtOnline,
  .white-sidebar-color .sidemenu-container .sidemenu>li>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li>a {
    color: #444;
  }

  .white-sidebar-color .sidemenu-container .sidemenu>li.open>a>.arrow.open:before,
  .white-sidebar-color .sidemenu-container .sidemenu>li.open>a>.arrow:before,
  .white-sidebar-color .sidemenu-container .sidemenu>li.open>a>i,
  .white-sidebar-color .sidemenu-container .sidemenu>li:hover>a>.arrow.open:before,
  .white-sidebar-color .sidemenu-container .sidemenu>li:hover>a>.arrow:before,
  .white-sidebar-color .sidemenu-container .sidemenu>li:hover>a>i,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.open>a>.arrow.open:before,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.open>a>.arrow:before,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.open>a>i,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li:hover>a>.arrow.open:before,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li:hover>a>.arrow:before,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li:hover>a>i {
    color: white;
  }

  .white-sidebar-color .sidemenu-container .sidemenu .sub-menu,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu {
    background-color: #F4F6F9;
  }

  .white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li>a {
    color: #444;
  }

  .white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li.active>a,
  .white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li.open>a,
  .white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li:hover>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li.active>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li.open>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li:hover>a {
    background-color: #74bf43;
    color: white;
  }

  .white-sidebar-color .page-container {
    background-color: #ffffff;
  }

  .selector-title {
    margin-top: 0px !important;
  }

  .white-sidebar-color .sidemenu-hover-submenu li:hover a>.arrow {
    border-right: 8px solid #4680ff;
  }

  .white-sidebar-color .sidemenu-hover-submenu li:hover>.sub-menu {
    background-color: #F5F5F5;
  }

  .white-sidebar-color .sidemenu-container .sidemenu>li.active>a>i,
  .white-sidebar-color .sidemenu-container .sidemenu li.active>a>.arrow.open:before,
  .white-sidebar-color .sidemenu-container .sidemenu li.active>a>.arrow:before,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu li.active>a>.arrow.open:before,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu li.active>a>.arrow:before {
    color: white;
  }

  .white-sidebar-color .menu-heading {
    color: #52545b;
  }
</style>


<?php
$getvehicle_byowneringofence = $this->dashboardmodel->getvehicle_byowneringeofence();
$totalvehicleingeofence      = sizeof($getvehicle_byowneringofence);
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

  if ($totalvehicleingeofence == 0) {
    $namegeofence = "0";
    $hostgeofence = "0";
  }elseif ($totalvehicleingeofence > 1) {
    $arrgeofence  = explode("@", $getvehicle_byowneringofence[1]->geofence_vehicle);
    $namegeofence = $arrgeofence[0];
    $hostgeofence = $arrgeofence[1];
  }else {
    $arrgeofence  = explode("@", $getvehicle_byowneringofence[0]->geofence_vehicle);
    $namegeofence = $arrgeofence[0];
    $hostgeofence = $arrgeofence[1];
  }
 ?>

 <div class="sidebar-container">
  <div class="sidemenu-container navbar-collapse collapse fixed-menu">
    <div id="remove-scroll">
      <ul class="sidemenu page-header-fixed p-t-20" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
        <li class="sidebar-toggler-wrapper hide">
          <div class="sidebar-toggler">
            <span></span>
          </div>
        </li>
        <li class="sidebar-user-panel">
          <div class="user-panel">
            <div class="text-left">
              <div style="font-size:11px;">
                <?php if (isset($code_view_menu)) {
                  if ($code_view_menu == "monitor") {?>
                    Total(<a href="<?=base_url();?>maps" style="color:black;"><?=$total_vehicle;?></a>) |
                    Eng. On(<a onclick="listEngine(1);" style="color:darkgreen;"><?=$engine_on;?></a>) |
                    Eng. Off(<a onclick="listEngine(0);" style="color:red;"><?=$engine_off;?></a>)
                    <!-- Online(<a href="#" style="color:darkgreen;"><?=$total_online;?></a>) |
                    Offline(<a href="#" style="color:red;"><?=$total_offline;?></a>) -->
                  <?php }
                } ?>
              </div>
            </div>
          </div>
        </li>

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "configuration") {
            $menuactive = "active";
          }else {
            $menuactive = "";
          }
        }else {
          $menuactive = "";
        }  ?>

        <?php if (isset($code_view_submenu)) {
          if ($code_view_submenu == "branchoffice") {
            $submenuactive = "active";
          }else {
            $submenuactive = "";
          }
        }else {
          $submenuactive = "";
        }  ?>

	      <!-- <li class="nav-item">
          <a href="<?php echo base_url() ?>maps" class="nav-link">
            <i class="material-icons">room</i>
            <span class="title">Monitoring</span>
          </a>
        </li> -->

        <!-- <li class="nav-item">
          <a href="<?php echo base_url() ?>maps/heatmap" class="nav-link">
            <i class="material-icons">room</i>
            <span class="title">Monitoring (Heatmap 1)</span>
          </a>
        </li> -->

        <!-- <li class="nav-item">
          <a href="<?php echo base_url() ?>maps/heatmap" class="nav-link">
            <i class="material-icons">room</i>
            <span class="title">Monitoring</span>
          </a>
        </li> -->

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "monitor") {
            $openparentmenu = "open";
            $opensubmenu = "display:block";
          } else {
            $openparentmenu = "";
            $opensubmenu = "display:none";
          }
        } else {
          $openparentmenu = "";
          $opensubmenu = "display:none";
        }  ?>
        <li class="nav-item <?php echo $openparentmenu ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">room</i>
            <span class="title">Monitoring</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu ?>">
            <li class="nav-item">
              <a href="<?php echo base_url() ?>maps/heatmap" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">Operation</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/quickcount" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">Quickcount</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/rom" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">ROM</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/port" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">PORT</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/pool" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">POOL</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/outofhauling" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">Out of Hauling</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/mapsstandard" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">Maps Standard</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>violation" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">Violation</span>
              </a>
            </li>

            <!-- <?php
              $user_id       = $this->sess->user_id;
              $mitra_streaming_registered = array(5174, 5168, 5172, 5167);

              if (in_array($user_id, $mitra_streaming_registered)) {?> -->
              <!-- <?php }else {?>

              <?php }
             ?> -->

            <li class="nav-item">
              <a href="<?= base_url(); ?>live_monitoring" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Live Monitoring</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>development/dashboardunitmonitoring" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Dashboard Unit Monitoring</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>dashboard/intervention-1" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">New</span>
                <span class="title">Dashboard Intervention</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>dashboard/post-event" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Dashboard Post Event</span>
              </a>
            </li>

            <!-- <li class="nav-item">
              <a href="<?= base_url(); ?>development/intervention_development" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">DEV</span>
                <span class="title">Dashboard Intervention</span>
              </a>
            </li> -->

            <!-- <li class="nav-item">
              <a href="<?= base_url(); ?>dashboard/intervention" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Dashboard Intervention</span>
              </a>
            </li> -->

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Dashboard Trend Alert</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">

                <li class="nav-item">
                  <!-- <a href="<?= base_url(); ?>hseboard/violation" class="nav-lin"> -->
                    <a href="<?= base_url(); ?>hseboard/violation2" class="nav-lin">
                    <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                    <span class="title">Board</span>
                  </a>
                </li>

                <!-- <li class="nav-item">
                  <a href="<?= base_url(); ?>hse/summary" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Summary</span>
                  </a>
                </li> -->

              </ul>
            </li>

            <!-- <li class="nav-item">
              <a href="<?php echo base_url() ?>violation/table" class="nav-link">
                <i class="material-icons">room</i>
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Violation Table</span>
              </a>
            </li> -->
          </ul>
        </li>

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "configuration") {
            $openparentmenu = "open";
            $opensubmenu = "display:block";
          } else {
            $openparentmenu = "";
            $opensubmenu = "display:none";
          }
        } else {
          $openparentmenu = "";
          $opensubmenu = "display:none";
        }  ?>
        <li class="nav-item" <?php echo $openparentmenu ?>>
            <a href="#" class="nav-link nav-toggle active">
                <i class="material-icons">settings</i>
                <span class="title">Maintenance & Configuration</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu" style="<?php echo $opensubmenu ?>">
              <li class="nav-item">
                <a href="<?=base_url();?>maintenance" class="nav-link">
                  <span class="title">Set Maintenance</span>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?=base_url();?>maintenance/onprocess" class="nav-link">
                  <span class="title">On Process Status</span>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?= base_url(); ?>geofencedatalistlive" class="nav-link ">
                  <span class="title">Geofence List (Live)</span>
                </a>
              </li>
            </ul>
        </li>

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "report") {
            $openparentmenu = "open";
            $opensubmenu = "display:block";
          } else {
            $openparentmenu = "";
            $opensubmenu = "display:none";
          }
        } else {
          $openparentmenu = "";
          $opensubmenu = "display:none";
        }  ?>
        <li class="nav-item <?php echo $openparentmenu ?>">
            <a href="#" class="nav-link nav-toggle">
                <i class="material-icons">report</i>
                <span class="title">Report</span>
                <span class="arrow"></span>
            </a>
            <ul class="sub-menu" style="<?php echo $opensubmenu ?>">
              <?php
              $privilegecode = $this->sess->user_id_role;
                if ($privilegecode == 5) {?>

                  <li class="nav-item">
                    <a href="<?= base_url(); ?>violation/historikal" class="nav-link">
                      <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">Violation Historical</span>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?= base_url(); ?>live_monitoring/manualevidence" class="nav-link">
                      <span class="label label-rouded label-menu label-danger">new</span>
                      <span class="title">Manual Evidence</span>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?php echo base_url() ?>devicereport/mdvrreportstatus" class="nav-link">
                      <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">History MDVR</span>
                    </a>
                  </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>devicereport/gpsoffline" class="nav-link">
                    <!-- <i class="material-icons">room</i> -->
                    <span class="title">History GPS Offline</span>
                  </a>
                </li>

              <li class="nav-item">
                <a href="javascript:;" class="nav-link nav-toggle">
                  <span class="title">Truck On Duty</span>
                  <span class="arrow"></span>
                </a>
                <ul class="sub-menu">
                  <li class="nav-item">
                    <a href="<?= base_url(); ?>truck/hour" class="nav-lin">
                      <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">Board</span>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?= base_url(); ?>truck/summarynew" class="nav-lin">
                      <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">Summary</span>
                    </a>
                  </li>

                  <!-- <li class="nav-item">
                    <a href="<?= base_url(); ?>truck/month" class="nav-lin">
                      <span class="label label-rouded label-menu label-danger">new</span>
                      <span class="title">Summary</span>
                    </a>
                  </li> -->
                </ul>
              </li>

			  <li class="nav-item">
                <a href="<?=base_url();?>truck/pool" class="nav-lin">
                  <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                  <span class="title">Truck On Pool</span>
                </a>
              </li>

			  <li class="nav-item">
                <a href="<?=base_url();?>locationhour" class="nav-lin">
                  <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                  <span class="title">Location Hour</span>
                </a>
              </li>

                  <!-- <li class="nav-item">
                    <a href="<?php echo base_url()?>overspeedreport" class="nav-link ">
                      <span class="title">Overspeed</span>
                    </a>
                  </li> -->

                  <li class="nav-item">
                    <a href="<?= base_url(); ?>ritasereport/full" class="nav-link">
                      <span class="label label-rouded label-menu label-danger">new</span>
                      <span class="title">Ritase Report</span>
                    </a>
                  </li>

                  <!-- <li class="nav-item">
                    <a href="<?=base_url();?>tripreport/history" class="nav-link">
                      <span class="title">History Map</span>
                    </a>
                  </li> -->

                  <li class="nav-item">
                    <a href="<?=base_url();?>tripreport/playbackhistory" class="nav-link">
                      <span class="title">History Map</span>
                    </a>
                  </li>

                  <!-- <li class="nav-item">
                    <a href="<?=base_url();?>securityevidence" class="nav-link">
                      <span class="label label-rouded label-menu label-danger">new</span>
                      <span class="title">Security Evidence</span>
                    </a>
                  </li> -->

                  <!-- <li class="nav-item">
                    <a href="<?=base_url();?>driverdetected" class="nav-link"> -->
                      <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <!-- <span class="title">Driver Detected</span>
                    </a>
                  </li> -->



                  <li class="nav-item">
                    <a href="<?=base_url();?>driverbreakdown" class="nav-link ">
    	                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">Breakdown Report</span>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?=base_url();?>locationreport" class="nav-link">
                      <span class="title">Location Report</span>
                    </a>
                  </li>

                  <!-- <li class="nav-item">
                    <a href="<?= base_url(); ?>fuelreport" class="nav-lin"> -->
                      <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <!-- <span class="title">Fuel Report</span> -->
                    <!-- </a>
                  </li> -->

                  <li class="nav-item">
                    <a href="<?= base_url(); ?>installedfms" class="nav-link">
                      <span class="label label-rouded label-menu label-danger">new</span>
                      <span class="title">Installed FMS</span>
                    </a>
                  </li>
                <?php }else {?>
                  <li class="nav-item">
                    <a href="<?= base_url(); ?>violation/historikal" class="nav-link">
                      <span class="label label-rouded label-menu label-danger">new</span>
                      <span class="title">Violation Historical</span>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?php echo base_url() ?>devicereport/mdvrreport" class="nav-link">
                      <!-- <i class="material-icons">room</i> -->
                      <span class="title">History MDVR</span>
                    </a>
                  </li>

                  <li class="nav-item">
                    <a href="<?php echo base_url() ?>devicereport/gpsoffline" class="nav-link">
                      <!-- <i class="material-icons">room</i> -->
                      <span class="title">History GPS Offline</span>
                    </a>
                  </li>

			  <li class="nav-item">
                <a href="<?=base_url();?>truck/hour" class="nav-lin">
                  <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                  <span class="title">Truck On Duty</span>
                </a>
              </li>
                  <!-- <li class="nav-item">
                    <a href="<?=base_url();?>devicealert" class="nav-lin">
                      <span class="label label-rouded label-menu label-danger">new</span>
                      <span class="title">Device Alert</span>
                    </a>
                  </li> -->
                  <li class="nav-item">
                    <a href="<?php echo base_url()?>overspeedreport" class="nav-link ">
                      <span class="title">Overspeed</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?=base_url();?>securityevidence" class="nav-link">
                      <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">Security Evidence</span>
                    </a>
                  </li>
                  <!-- <li class="nav-item">
                    <a href="<?=base_url();?>tripreport/history" class="nav-link">
                      <span class="title">History Map</span>
                    </a>
                  </li> -->

                  <li class="nav-item">
                    <a href="<?=base_url();?>tripreport/playbackhistory" class="nav-link">
                      <span class="title">History Map</span>
                    </a>
                  </li>
                  <li class="nav-item">
                <a href="<?=base_url();?>operational" class="nav-link ">
                  <span class="title">Operational Report</span>
                </a>
              </li>
                  <li class="nav-item">
                      <a href="<?php echo base_url()?>driverchange" class="nav-link">
                      <span class="title">Driver Change</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?=base_url();?>driverabsensi" class="nav-link ">
    	                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">Driver Absensi</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?=base_url();?>ritasereport" class="nav-link">
                      <span class="title">Ritase Report</span>
                    </a>
                  </li>

                  <li class="nav-item">
                      <a href="javascript:;" class="nav-link nav-toggle">
                          <span class="title">Wim Report</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub-menu">
                          <li class="nav-item">
                            <a href="<?=base_url();?>wimreport" class="nav-link">
                              <span class="title">Raw Data</span>
                            </a>
                          </li>

                          <li class="nav-item">
                            <a href="<?=base_url();?>tonasereport/jam" class="nav-link">
                              <span class="title">Tonase Per jam</span>
                            </a>
                          </li>

                          <li class="nav-item">
                            <a href="<?=base_url();?>tonasereport/wb" class="nav-link">
                              <span class="title">WB Report</span>
                            </a>
                          </li>

                          <li class="nav-item">
                            <a href="<?=base_url();?>tonasereport/stockpile" class="nav-link">
                              <span class="title">Stockpile</span>
                            </a>
                          </li>
                      </ul>
                  </li>

    	             <li class="nav-item">
                    <a href="<?=base_url();?>audittrail" class="nav-link ">
    	                 <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                      <span class="title">Audit Trail</span>
                    </a>
                  </li>
                <?php } ?>

            </ul>
        </li>

      </ul>
    </div>
  </div>
</div>
