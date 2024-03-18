<style>

/* White sidebar color */
.white-sidebar-color .sidemenu-container {
    background-color: #ffffff;
}
.white-sidebar-color .sidemenu-container .sidemenu>li.active.open>a,
.white-sidebar-color .sidemenu-container .sidemenu>li.active>a,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.active.open>a,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li.active>a {
    background-color: #1f50a2;
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
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li:hover>a{
	background-color: #1f50a2;
	opacity : 0.8;
    border-top-color: transparent;
    color: white;
}
.white-sidebar-color .user-panel,
.white-sidebar-color .txtOnline,
.white-sidebar-color .sidemenu-container .sidemenu>li>a,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li>a{
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
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu>li:hover>a>i{
	color: white;
}
.white-sidebar-color .sidemenu-container .sidemenu .sub-menu,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu{
	    background-color: #F4F6F9;
}
.white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li>a,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li>a{
	color : #444;
}
.white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li.active>a,
.white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li.open>a,
.white-sidebar-color .sidemenu-container .sidemenu .sub-menu>li:hover>a,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li.active>a,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li.open>a,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu .sub-menu>li:hover>a{
	background-color: #1f50a2;
	color: white;
}
.white-sidebar-color .page-container{
	background-color : #ffffff;
}
.selector-title{
	margin-top: 0px !important;
}
.white-sidebar-color .sidemenu-hover-submenu li:hover a>.arrow{
	    border-right: 8px solid #1f50a2;
}
.white-sidebar-color .sidemenu-hover-submenu li:hover>.sub-menu{
	background-color: #F5F5F5;
}
.white-sidebar-color .sidemenu-container .sidemenu>li.active>a>i,
.white-sidebar-color .sidemenu-container .sidemenu li.active>a>.arrow.open:before,
.white-sidebar-color .sidemenu-container .sidemenu li.active>a>.arrow:before,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu li.active>a>.arrow.open:before,
.white-sidebar-color .sidemenu-closed.sidemenu-container-fixed .sidemenu-container:hover .sidemenu li.active>a>.arrow:before{
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
        <li class="nav-item <?php echo $openparentmenu ?>">
          <a href="#" class="nav-link nav-toggle active">
            <i class="material-icons">room</i>
            <span class="title">Monitoring</span>
            <span class="arrow"></span>
          </a>
          <ul class="sub-menu" style="<?php echo $opensubmenu ?>">
            <li class="nav-item">
              <a href="<?php echo base_url() ?>exca" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">Maps</span>
              </a>
            </li>

            <li class="nav-item">
              <a href="<?php echo base_url() ?>dashboard/view/rom" class="nav-link">
                <!-- <i class="material-icons">room</i> -->
                <span class="title">ROM</span>
              </a>
            </li>
          </ul>
        </li>

      </ul>
    </div>
  </div>
</div>
