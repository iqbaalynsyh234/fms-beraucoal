<style>
  /* White sidebar color */
  .white-sidebar-color .sidemenu-container {
    background-color: #ffffff;
  }

  .white-sidebar-color .sidemenu-container .sidemenu>li.active.open>a,
  .white-sidebar-color .sidemenu-container .sidemenu>li.active>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.active.open>a,
  .white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.active>a {
    background-color: #4bb036;
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
    background-color: #4bb036;
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
    background-color: #4bb036;
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
} else {
  $arr          = explode("@", $getvehicle_byowner[0]->vehicle_device);
  $name         = $arr[0];
  $host         = $arr[1];
}

if ($totalvehicleingeofence == 0) {
  $namegeofence = "0";
  $hostgeofence = "0";
} elseif ($totalvehicleingeofence > 1) {
  $arrgeofence  = explode("@", $getvehicle_byowneringofence[1]->geofence_vehicle);
  $namegeofence = $arrgeofence[0];
  $hostgeofence = $arrgeofence[1];
} else {
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
                  if ($code_view_menu == "monitor") { ?>
                    Total(<a href="#" style="color:black;"><?= $total_vehicle; ?></a>) |
                    Eng. On(<a onclick="listEngine(1);" style="color:darkgreen;"><?= $engine_on; ?></a>) |
                    Eng. Off(<a onclick="listEngine(0);" style="color:red;"><?= $engine_off; ?></a>)
                    <!-- Online(<a href="#" style="color:darkgreen;"><?= $total_online; ?></a>) |
                    Offline(<a href="#" style="color:red;"><?= $total_offline; ?></a>) -->
                <?php }
                } ?>
              </div>
            </div>
          </div>
        </li>

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "configuration") {
            $menuactive = "active";
          } else {
            $menuactive = "";
          }
        } else {
          $menuactive = "";
        }  ?>

        <?php if (isset($code_view_submenu)) {
          if ($code_view_submenu == "branchoffice") {
            $submenuactive = "active";
          } else {
            $submenuactive = "";
          }
        } else {
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

        <?php
          if ($this->sess->user_id == 4408) {?>
            <?php if (isset($code_view_menu)) {
              if ($code_view_menu == "monitoradminonly") {
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
                <span class="title">Admin Only</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">

                <li class="nav-item">
                  <a href="javascript:;" class="nav-link nav-toggle">
                    <span class="title">MDVR Anomali</span>
                    <span class="arrow"></span>
                  </a>
                  <ul class="sub-menu">

                    <li class="nav-item">
                      <a href="<?= base_url(); ?>development/mdvranomalilist" class="nav-lin">
                        <span class="label label-rouded label-menu label-danger">new</span>
                        <span class="title">Dashboard</span>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="<?= base_url(); ?>development/mdvranomali" class="nav-lin">
                        <span class="label label-rouded label-menu label-danger">new</span>
                        <span class="title">Report</span>
                      </a>
                    </li>

                  </ul>
                </li>

                <li class="nav-item">
                  <a href="javascript:;" class="nav-link nav-toggle">
                    <span class="title">Dashboard</span>
                    <span class="arrow"></span>
                  </a>
                  <ul class="sub-menu">

                    <!-- <li class="nav-item">
                      <a href="<?= base_url(); ?>development/livemonitoring" class="nav-lin">
                        <a href="http://live.abditrack.com/attachmentview/livemonitoring/<?php echo $this->sess->user_id; ?>" class="nav-lin">
                        <span class="label label-rouded label-menu label-danger">new</span>
                        <span class="title">Live Monitoring</span>
                      </a>
                    </li> -->

                    <li class="nav-item">
                      <a href="<?= base_url(); ?>live_monitoring" class="nav-lin">
                        <span class="label label-rouded label-menu label-danger">new</span>
                        <span class="title">Dashboard Live Monitoring</span>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="<?= base_url(); ?>development/dashboardunitmonitoring" class="nav-lin">
                        <span class="label label-rouded label-menu label-danger">new</span>
                        <span class="title">Unit Monitoring</span>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="<?= base_url(); ?>development/dashboardpostevent" class="nav-lin">
                        <span class="label label-rouded label-menu label-danger">new</span>
                        <span class="title">Dashboard Post Event</span>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="<?= base_url(); ?>development/posteventcontrolroom" class="nav-lin">
                        <span class="label label-rouded label-menu label-danger">new</span>
                        <span class="title">Dashboard Intervention</span>
                      </a>
                    </li>

                  </ul>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>adminonly/locationreport" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Location Report</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>development/violation_historikalreport" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Violation Historical Report</span>
                  </a>
                </li>



                <li class="nav-item">
                  <a href="<?= base_url(); ?>dashboard/intervention-1" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Dashboard Intervention</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>dashboard/intervention-2" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Dashboard Intervention Stage 2</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>dashboard/intervention/pushalert-1" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Dashboard Intervention (Pushalert)</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>dashboard/intervention/pushalert-2" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Dashboard Intervention (Pushalert) Stage 2</span>
                  </a>
                </li>

                <!-- <li class="nav-item">
                  <a href="<?= base_url(); ?>hse/violation" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Daily Violation</span>
                  </a>
                </li> -->

                <!-- <li class="nav-item">
                  <a href="<?php echo base_url() ?>development/pocinrommapsexca" class="nav-link">
                    <i class="material-icons">room</i>
                    <span class="title">POC in ROM</span>
                  </a>
                </li> -->

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>maps/devmonitoringnew" class="nav-link">
                    <!-- <i class="material-icons">room</i> -->
                    <span class="title">Maps</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>maps_development/quickcount_pmo" class="nav-link">
                    <!-- <i class="material-icons">room</i> -->
                    <span class="title">Quickcount PMO</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>historical" class="nav-link">
                    <!-- <i class="material-icons">room</i> -->
                    <span class="title">Historical Quick Count</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>report/history/view" class="nav-link">
                    <!-- <i class="material-icons">room</i> -->
                    <span class="title">History Detail</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>devicereport/mdvrreport" class="nav-link">
                    <!-- <i class="material-icons">room</i> -->
                    <span class="title">History MDVR</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>development/mdvrreport" class="nav-link">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Summary MDVR Report</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>devicereport/gpsoffline" class="nav-link">
                    <!-- <i class="material-icons">room</i> -->
                    <span class="title">History GPS Offline</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?php echo base_url() ?>development/fuelsensorhistory" class="nav-link">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">History Fuel Sensor</span>
                  </a>
                </li>

              </ul>
            </li>
          <?php }
         ?>

         <?php if (isset($code_view_menu)) {
           if ($code_view_menu == "abdiwatch") {
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
             <span class="title">Abdiwatch</span>
             <span class="arrow"></span>
           </a>
           <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
             <li class="nav-item">
               <a href="<?php echo base_url() ?>abdiwatch/dashboard" class="nav-link">
                 <!-- <i class="material-icons">room</i> -->
                 <span class="title">Dashboard</span>
               </a>
             </li>
           </ul>
         </li>

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
             <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
               <li class="nav-item">
                 <a href="<?php echo base_url() ?>maps/heatmap" class="nav-link">
                   <!-- <i class="material-icons">room</i> -->
                   <span class="title">Maps</span>
                 </a>
               </li>

               <li class="nav-item">
                 <a href="<?php echo base_url() ?>view/quickcount" class="nav-link">
                   <!-- <i class="material-icons">room</i> -->
                   <span class="title">Quickcount</span>
                 </a>
               </li>

               <!-- <li class="nav-item">
                 <a href="<?php echo base_url() ?>view/tia/operation" class="nav-link">
                   <i class="material-icons">room</i>
                   <span class="title">Quickcount TIA</span>
                 </a>
               </li> -->

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

               <!-- <li class="nav-item">
                 <a href="<?= base_url(); ?>development/dashboardunitmonitoring" class="nav-lin">
                   <span class="label label-rouded label-menu label-danger">new</span>
                   <span class="title">Dashboard Unit Monitoring</span>
                 </a>
               </li> -->

               <li class="nav-item">
                 <a href="<?= base_url(); ?>development/livemonitoring" class="nav-lin">
                   <span class="label label-rouded label-menu label-danger">new</span>
                   <span class="title">Live Monitoring</span>
                 </a>
               </li>

               <li class="nav-item">
                 <a href="<?= base_url(); ?>development/dashboardunitmonitoring" class="nav-lin">
                   <span class="label label-rouded label-menu label-danger">new</span>
                   <span class="title">Unit Monitoring</span>
                 </a>
               </li>

               <li class="nav-item">
                 <a href="<?= base_url(); ?>dashboard/post-event" class="nav-lin">
                   <span class="label label-rouded label-menu label-danger">new</span>
                   <span class="title">Dashboard Post Event</span>
                 </a>
               </li>

               <li class="nav-item">
                 <a href="<?= base_url(); ?>dashboard/intervention" class="nav-lin">
                   <span class="label label-rouded label-menu label-danger">new</span>
                   <span class="title">Dashboard Intervention</span>
                 </a>
               </li>

               <li class="nav-item">
                 <a href="<?= base_url(); ?>dashboard/intervention-1" class="nav-lin">
                   <span class="label label-rouded label-menu label-danger">SOFT</span>
                   <span class="title">Dashboard Intervention</span>
                 </a>
               </li>

               <!-- <li class="nav-item">
                 <a href="<?php echo base_url() ?>violation/table" class="nav-link">
                   <i class="material-icons">room</i>
                   <span class="label label-rouded label-menu label-danger">new</span>
                   <span class="title">Violation Table</span>
                 </a>
               </li> -->

               <!-- <li class="nav-item">
                 <a href="<?php echo base_url() ?>view/tia/violation" class="nav-link">
                   <i class="material-icons">room</i>
                   <span class="title">Violation TIA</span>
                 </a>
               </li> -->
             </ul>
           </li>

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "monitorexca") {
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

        <!-- <li class="nav-item <?php echo $openparentmenu ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">room</i>
            <span class="title">Monitoring Excavator</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
            <li class="nav-item">
              <a href="<?php echo base_url() ?>dashboard/view/rom" class="nav-link">
                <i class="material-icons">room</i>
                <span class="title">ROM</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>dashboard/view/mapsstandard" class="nav-link">
                <i class="material-icons">room</i>
                <span class="title">Maps</span>
              </a>
            </li>
          </ul>
        </li> -->

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "monitoring_lv") {
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

        <!-- <li class="nav-item <?php echo $openparentmenu ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">room</i>
            <span class="title">Monitoring LV</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/bib/vehicle" class="nav-link">
                <span class="title">Maps</span>
              </a>
            </li>
          </ul>
        </li> -->

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "monitoring_hrm") {
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

        <!-- <li class="nav-item <?php echo $openparentmenu ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">room</i>
            <span class="title">Monitoring HRM</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
            <li class="nav-item">
              <a href="<?php echo base_url() ?>view/hrm/maps" class="nav-link">
                <span class="title">Maps</span>
              </a>
            </li>
          </ul>
        </li> -->

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "dashboard") {
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
            <i class="material-icons">dashboard</i>
            <span class="title">Dashboard</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Truck on Duty</span>
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
              <a href="<?= base_url(); ?>truck/pool" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Truck On Pool</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>ritasehour/board" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Dashboard Ritase</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Dashboard Trend Alert</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">

                <li class="nav-item">
                  <a href="<?= base_url(); ?>hseboard/violation" class="nav-lin">
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
              <a href="<?= base_url(); ?>esdm/board" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Dashboard ESDM</span>
              </a>
            </li> -->
          </ul>
        </li>

        <!-- <li class="nav-item">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">dashboard</i>
            <span class="title">U-Board</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu">

            <li class="nav-item">
              <a href="https://uboard.ugems.id/superset/dashboard/p/ODPojXaPpk8/" target="_blank" class="nav-lin">
                <span class="title">Speed Distribution</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="https://uboard.ugems.id/superset/dashboard/p/X3dNZMZBpDw/" target="_blank" class="nav-lin">
                <span class="title">Daily Violation</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="https://uboard.ugems.id/superset/dashboard/p/VZm9ea6J9GD/" target="_blank" class="nav-lin">
                <span class="title">Location Report</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="https://uboard.ugems.id/superset/dashboard/p/PKLp1ELqprx/" target="_blank" class="nav-lin">
                <span class="title">Driver Detected</span>
              </a>
            </li>

          </ul>
        </li> -->

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "wimmenu") {
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

        <!-- <li class="nav-item <?php echo $openparentmenu ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">theaters</i>
            <span class="title">WIM</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
            <li class="nav-item">
              <a href="<?php echo base_url() ?>wim" class="nav-link">
                <i class="material-icons">theaters</i>
                <span class="title">Master Menu</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>wim/operatoritws" class="nav-link">
                <i class="material-icons">theaters</i>
                <span class="title">Operator Menu</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>wim/otherport" class="nav-link">
                <i class="material-icons">theaters</i>
                <span class="title">Other Port</span>
              </a>
            </li>
          </ul>
        </li> -->

        <?php if (isset($code_view_menu)) {
          if ($code_view_menu == "masterdata") {
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
            <i class="material-icons">book</i>
            <span class="title">Master Data</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">

            <!-- <li class="nav-item">
              <a href="<?= base_url(); ?>masterfromportal" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Master Unit Portal</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>masterunit" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Master Unit ITWS</span>
              </a>
            </li> -->

            <!-- <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Driver</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">

                <li class="nav-item">
                  <a href="<?= base_url(); ?>masterportalsimper" class="nav-lin">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Simper (isafe)</span>
                  </a>
                </li>

                <li class="nav-item start">
                  <a href="<?= base_url(); ?>masterdata/driveritws" class="nav-link ">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Driver ITWS</span>
                  </a>
                </li>

                <li class="nav-item start">
                  <a href="<?= base_url(); ?>account/driver" class="nav-link ">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Driver Account</span>
                  </a>
                </li>

              </ul>
            </li> -->



            <li class="nav-item">
              <a href="<?= base_url(); ?>vehicles" class="nav-link">
                <span class="title">Master Device</span>
              </a>
            </li>

            <li class="nav-item <?php echo $submenuactive ?>">
              <a href="<?= base_url(); ?>account/branch" class="nav-link ">
                <span class="title">Contractor</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/employee" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Employee</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/site" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Site</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/location" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Location</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/object" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Object</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/objectdetail" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Object Detail</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/quickaction" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Quick Action</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/categorytype" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Category Type</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>beats/pja" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">PJA</span>
              </a>
            </li>

            <!-- <li class="nav-item">
              <a href="<?= base_url(); ?>masterdata/client" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Data Client</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url(); ?>masterdata/material" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Data Material</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url(); ?>masterdata/dumping" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Data Dumping</span>
              </a>
            </li> -->

            <li class="nav-item start">
              <a href="<?= base_url(); ?>account" class="nav-link">
                <span class="title">User</span>
              </a>
            </li>

            <li class="nav-item start">
              <a href="<?= base_url(); ?>masterdata/speedlevel" class="nav-link">
                <span class="title">Overspeed Category</span>
              </a>
            </li>

            <li class="nav-item start">
              <a href="<?= base_url(); ?>masterdata/mdvrlevel" class="nav-link">
                <span class="title">DSM Alert Category</span>
              </a>
            </li>


          </ul>
        </li>

        <li class="nav-item <?php echo $menuactive ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">settings</i>
            <span class="title">Configuration</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu">
            <li class="nav-item">
              <a href="<?= base_url(); ?>mapssetting" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Density Limit</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>productionplan" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Production Plan </span>
              </a>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Maintenance</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="<?= base_url(); ?>maintenance" class="nav-link">
                    <span class="title">Set Maintenance</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>maintenance/onprocess" class="nav-link">
                    <span class="title">On Process Status</span>
                  </a>
                </li>
              </ul>
            </li>

            <li class="nav-item start">
              <a href="<?= base_url(); ?>ritase" class="nav-link ">
                <span class="title">Ritase</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>streetdata" class="nav-link ">
                <span class="title">Street</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>geofencedatalive" class="nav-link ">
                <span class="title">Geofence Setup (Live)</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url(); ?>geofencedatalistlive" class="nav-link ">
                <span class="title">Geofence List (Live)</span>
              </a>
            </li>

            <li class="nav-item start">
              <a href="<?= base_url(); ?>poidata" class="nav-link ">
                <span class="title">POI (Point of Interest)</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>account/add/<?= $this->sess->user_id; ?>" class="nav-link">
                <span class="title">Private Information</span>
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
        <li class="nav-item <?php echo $openparentmenu; ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">report</i>
            <span class="title">Report</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu; ?>">
            <li class="nav-item">
              <a href="<?= base_url(); ?>swreport" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Smartwatch</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>violation/historikal" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Violation Historical</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>locationreport" class="nav-link">
                <span class="title">Location Report</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>locationhour" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Location Hour</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>tripreport/playbackhistory" class="nav-link">
                <span class="title">History Map</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>overspeedreport" class="nav-link ">
                <span class="title">Overspeed</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>fuelreport" class="nav-lin">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Fuel Report</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>operational" class="nav-link ">
                <span class="title">Operational Report</span>
              </a>
            </li>

            <!-- <li class="nav-item">
              <a href="<?= base_url(); ?>securityevidence" class="nav-link">
                <span class="label label-rouded label-menu label-danger">d</span>
                <span class="title">Security Evidence</span>
              </a>
            </li> -->

            <li class="nav-item">
              <a href="<?php echo base_url() ?>driverdetected" class="nav-link">
                <span class="title">Driver Detected</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>driverabsensi" class="nav-link ">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Driver Absensi</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>driverbreakdown" class="nav-link ">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Breakdown Report</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Ritase Report</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="<?= base_url(); ?>ritasereport/full" class="nav-link">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Raw Data</span>
                  </a>
                </li>

                <!-- <li class="nav-item">
                  <a href="<?= base_url(); ?>ritasereport" class="nav-link">
                    <span class="label label-rouded label-menu label-danger">new</span>
                    <span class="title">Raw Data</span>
                  </a>
                </li> -->

                <li class="nav-item">
                  <a href="<?= base_url(); ?>ritasedetail" class="nav-link">
                    <span class="title">Detail</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>ritase/summary" class="nav-link ">
                    <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                    <span class="title">Summary</span>
                  </a>
                </li>
              </ul>
            </li>

            <!-- <li class="nav-item">
              <a href="<?= base_url(); ?>devicealert" class="nav-lin">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Device Alert</span>
              </a>
            </li> -->

            <!-- <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">Wim Report</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">
                <li class="nav-item">
                  <a href="<?= base_url(); ?>wimreport" class="nav-link">
                    <span class="title">Raw Data</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>tonasereport/jam" class="nav-link">
                    <span class="title">Tonase Per jam</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>tonasereport/wb" class="nav-link">
                    <span class="title">WB Report</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>tonasereport/stockpile" class="nav-link">
                    <span class="title">Stockpile</span>
                  </a>
                </li>
              </ul>
            </li> -->

            <li class="nav-item">
              <a href="javascript:;" class="nav-link nav-toggle">
                <span class="title">NON Activity</span>
                <span class="arrow"></span>
              </a>
              <ul class="sub-menu">

                <li class="nav-item">
                  <a href="<?= base_url(); ?>nonbib/dumping" class="nav-lin">
                    <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                    <span class="title">Other Port</span>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="<?= base_url(); ?>nonbib" class="nav-lin">
                    <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                    <span class="title">Alert</span>
                  </a>
                </li>

              </ul>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>devicereport/mdvrreportstatus" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">History MDVR</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>installedfms" class="nav-link">
                <span class="label label-rouded label-menu label-danger">new</span>
                <span class="title">Installed FMS</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?= base_url(); ?>audittrail" class="nav-link ">
                <!-- <span class="label label-rouded label-menu label-danger">new</span> -->
                <span class="title">Audit Trail</span>
              </a>
            </li>

          </ul>
        </li>

      </ul>
    </div>
  </div>
</div>
