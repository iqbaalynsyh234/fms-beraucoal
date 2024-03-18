<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
  jQuery(document).ready(
    function() {
      jQuery("#export_xcel").click(function() {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      });
    }
  );
</script>
<style media="screen">
div#modaladdserviceworks {
  margin-top: 1.5%;
  margin-left: 17.5%;
  max-height: 70%;
  position: absolute;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  z-index: 1;
  overflow-y: auto;
  width: 60%;
}

  div#modalforconfigservice {
    margin-top: 1%;
    margin-left: 17.5%;
    max-height: 70%;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 1;
    overflow-y: auto;
    width: 50%;
  }

  div#modalforsetservicess {
    margin-top: 1.5%;
    margin-left: 17.5%;
    max-height: 70%;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 1;
    overflow-y: auto;
    width: 60%;
  }

  div#modalvehiclesetting {
    margin-top: 3%;
    margin-left: 25%;
    width: 60%;
    /* max-height: 300px;
    max-width: 754px; */
    /* position: absolute; */
    max-height: 500px;
    max-width: 950px;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }

  div#modalvfuelcalibration {
    margin-top: 3%;
    margin-left: 25%;
    width: 60%;
    /* max-height: 300px;
    max-width: 754px; */
    /* position: absolute; */
    max-height: 500px;
    max-width: 950px;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }
</style>
<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->



<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!-- <a class="btn btn-success" href="javascript:addserviceworks()"><font>Service Works</font></a> -->
    <!-- <a class="btn btn-info" target="_blank" href="<?=base_url();?>vehicles/maintenanceshistory"><font>Maintenance History</font></a> -->
    <!-- <a class="btn btn-primary" target="_blank" href="<?=base_url();?>vehicles/workshop"><font>Manage Workshop / Agencies / Location</font></a> -->
    <!-- <br><br> -->
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
      <div class="row">
        <div class="col-md-12" id="tablevehicles">
          <div class="panel" id="panel_form">
            <header class="panel-heading" style="background-color:#221f1f;color:white;">Master Device
				<button type="button" name="button" id="export_xcel" class="btn btn-warning btn-sm">Export Excel</button>
			</header>
            <div class="panel-body" id="bar-parent10">
			<div id="isexport_xcel" style="overflow-x: auto;">
              <!--<table id="example1" class="table table-striped" style="font-size:14px;">-->
			  <table id="example" class="table table-striped" style="font-size:14px;">
                <thead>
                  <tr>
                    <th>
                      <!-- <button type="button" class="btn btn-success btn-xs">
                        <span class="fa fa-plus"></span>
                      </button> -->
                      No
                    </th>
                    <th>No. Lambung</th>
                    <th>No. Lambung BCKP</th>
                    <th>Type</th>
                    <th>Contractor</th>
                    <th>Device IMEI</th>
					<th>Device SIM</th>
                    <th>CAM IMEI</th>
					<th>Fuel Sensor</th>
					<th>No. Rangka</th>
					<th>No. Mesin</th>
					<th>RFID SPI</th>
					<th>RFID WIM</th>
					<th>Tare</th>
                    <th>Tgl. Pasang</th>
                    <th>Status</th>
                    <?php if ($privilegecode == 3) {?>

                    <?php }else {?>
                      <th>Control</th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php for($i=0;$i<count($datavehicle);$i++) { ?>
                    <tr>
                      <td width="2%"><?=$i+1?></td>
                      <td>
                        <?=$datavehicle[$i]['vehicle_no'];?><br>
                      </td>
                      <td>
                        <?=$datavehicle[$i]['vehicle_no_bk'];?><br>
                      </td>
                      <td>
                        <?php echo $datavehicle[$i]['vehicle_name'] ?>
                      </td>
                      <td>
                        <?php
                        if (isset($company)) {
                          for ($j=0; $j < sizeof($company); $j++) {
                            if ($datavehicle[$i]['vehicle_company'] == $company[$j]['company_id']) {
                              echo $company[$j]['company_name'];
                            }
                          }
                        }
                        // echo "<br>";
                        // if (isset($subcompany)) {
                        //   for ($k=0; $k < sizeof($subcompany); $k++) {
                        //     if ($datavehicle[$i]['vehicle_subcompany'] == $subcompany[$k]['subcompany_id']) {
                        //       echo "Sub Branch Office : " .  $subcompany[$k]['subcompany_name'];
                        //     }
                        //   }
                        // }

                        // echo "<br>";
                        // if (isset($group)) {
                        //   for ($l=0; $l < sizeof($group); $l++) {
                        //     if ($datavehicle[$i]['vehicle_group'] == $group[$l]['group_id']) {
                        //       echo "Customer : " .  $group[$l]['group_name'];
                        //     }
                        //   }
                        // }

                        // echo "<br>";
                        // if (isset($subgroup)) {
                        //   for ($l=0; $l < sizeof($subgroup); $l++) {
                        //     if ($datavehicle[$i]['vehicle_subgroup'] == $subgroup[$l]['subgroup_id']) {
                        //       echo "Sub Customer : " .  $subgroup[$l]['subgroup_name'];
                        //     }
                        //   }
                        // }
                         ?>
                      </td>
                      <td>
                          <?php
                              $deviceimei = explode("@", $datavehicle[$i]['vehicle_device']);
                              echo $deviceimei[0];
                          ?>
                      </td>
					  <td><?=$datavehicle[$i]['vehicle_card_no'];?></td>
                      <td><?=$datavehicle[$i]['vehicle_mv03'];?></td>
					  <td><?=$datavehicle[$i]['vehicle_sensor'];?></td>
					  <td><?=$datavehicle[$i]['vehicle_portal_rangka'];?></td>
					  <td><?=$datavehicle[$i]['vehicle_portal_mesin'];?></td>
					  <td><?=$datavehicle[$i]['vehicle_portal_rfid_spi'];?></td>
					  <td><?=$datavehicle[$i]['vehicle_portal_rfid_wim'];?></td>
					  <td><?=$datavehicle[$i]['vehicle_portal_tare'];?></td>
                      <td>
                          <?php
                              echo date("d-m-Y", strtotime($datavehicle[$i]['vehicle_tanggal_pasang']));
                          ?>
                      </td>
                      <td>
                        <?php
                            $vstatsufix = $datavehicle[$i]['vehicle_status'];
                              if ($vstatsufix == 1 || $vstatsufix == 2) {
                                echo "Active";
                              }else {
                                echo "Historical";
                              }
                        ?>
                      </td>
                      <?php if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <td>
                          <a  href="<?php echo base_url() ?>vehicles/detail/<?php echo $datavehicle[$i]['vehicle_id'];?>">
                            <button class="btn btn-success btn-sm" title="Detail">
                              <span class="fa fa-search"></span>
                            </button>
                          </a>

                          <a  href="javascript:vform(<?php echo $datavehicle[$i]['vehicle_id'];?>)">
                            <button class="btn btn-primary btn-sm" alt="<?php echo "Vehicle Setting"; ?>" title="Vehicle Setting">
                              <span class="fa fa-cog"></span>
                            </button>
                          </a>
                        </td>
                      <?php } ?>
                    </tr>
                  <? } ?>
                </tbody>
              </table>
            </div>
			</div>
      		</div>
        </div>
      </div>
</div>
</div>

<div id="modalforconfigservice" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header id="titleheader"></header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalforconfigservice();">X</button>
                </div>
            </div>
            <div class="card-body">
              <h4>
                <b style="color:blue;">Vehicle Detail Info</b>
              </h4>
              <table width="100%" cellpadding="8" class="table">
                <input class="form-control" type="hidden" name="vehicle_device" id="vehicle_device">
                <input class="form-control" type="hidden" name="vehicle_type_gps" id="vehicle_type_gps">
                <input class="form-control" type="hidden" id="adaisinya" value="1">
                <tr>
                  <td>Vehicle No </td>
                  <td>
                    <input class="form-control" type="text" name="vehicle_no" id="vehicle_no" readonly style="font-size : large;">
                  </td>
                  <td>Vehicle Name </td>
                  <td>
                    <input class="form-control" type="text" name="vehicle_name" id="vehicle_name" readonly style="font-size : large;">
                  </td>
                </tr>

                <tr>
                  <td>Vehicle Type</td>
                  <td>
                    <input class="form-control" type="text" name="vehicle_type" id="vehicle_type" class="formdefault">
                  </td>
                  <td>Year</td>
                  <td>
                    <input class="form-control" type="number" name="vehicle_year" id="vehicle_year" class="formdefault" size="4">
                  </td>
                </tr>

                <tr>
                  <td>No. Rangka</td>
                  <td>
                    <input class="form-control" type="text" name="no_rangka" id="no_rangka" class="formdefault">
                  </td>
                  <td>No. Mesin</td>
                  <td>
                    <input class="form-control" type="text" name="no_mesin" id="no_mesin" class="formdefault">
                  </td>
                </tr>

                <tr>
                  <td>STNK No.</td>
                  <td>
                    <input class="form-control" type="text" name="stnk_no" id="stnk_no" class="formdefault">
                  </td>
                  <td>Exp. Date</td>
                  <td>
                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" name="stnkexpdate" id="stnkexpdatenotempty" class="date-pick" value="<?=date('d-m-Y')?>"/><br />
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>
                    <h4>
                      <b style="color:blue;">KIR Info</b>
                    </h4>
                  </td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>

                <tr>
                  <td>KIR No</td>
                  <td>
                    <input class="form-control" type="text" name="kir_no" id="kir_no" class="formdefault">
                  </td>
                  <td>Exp. Date</td>
                  <td>
                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" name="kirexpdate" id="kirexpdatenotempty" class="date-pick" value="<?=date('d-m-Y')?>"/>
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>
                    <h4>
                      <b style="color:blue;">Service Info</b>
                    </h4>
                  </td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>

                <tr>
                  <td>Serviced By </td>
                  <td>
                    <select class="form-control" name="servicedby" id="servicedby" onchange="servicedbyonchange();">
                      <option value="">--Choose Serviced By--</option>
                      <option value="perkm">Per Km</option>
                      <option value="permonth">Per Month</option>
                    </select><br>
                    <input class="form-control" type="number" name="valueservicedby" id="valueservicedby" class="formdefault" style="display: none;" placeholder="Target KM"><br>
                    <input class="form-control" type="number" name="alertlimit" id="alertlimit" class="formdefault" style="display: none;" placeholder="Remind Before (KM/Bln)"><br>
                    <small style="color: red;">
                      <i>* Isi dengan periode bulan <br> atau dengan periode Kilometer.</i>
                    </small>
                  </td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>

                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <div>
                      <button class="btn btn-warning" type="button" onclick="closemodalforconfigservice();" /> Cancel</button>
                      <button class="btn btn-success" type="submit" name="submit" onclick="saveconfiguration()"> Save</button>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
        </div>
    </div>
  </div>
</div>

<div id="modalforsetservicess" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header id="titleheadersetservicess"></header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalforsetservice();">X</button>
                </div>
            </div>
            <div class="card-body">
              <div id="viewsizeconfig">
                <table width="100%" cellpadding="8" class="table">
                  <tr>
                    <td>Servicess</td>
                    <td>
                      <select class="form-control" name="selectservicess" id="selectservicess" onchange="selectservicess();">
                        <option value="">--Choose Servicess--</option>
                        <?php for ($i=0; $i < sizeof($dataservicetype); $i++) {?>
                          <option value="<?php echo $dataservicetype[$i]['service_type_id'];?>">
                            <?php echo $dataservicetype[$i]['service_type'];?>
                          </option>
                          <?php } ?>
                      </select>
                    </td>
                  </tr>
                </table>
                  <input class="form-control" type="hidden" name="vehicle_device_setservicess" id="vehicle_device_setservicess">

                  <div id="kirview" style="display: none;">
                    <table width="100%" cellpadding="8" class="table sortable no-margin">
                      <!-- FOR KIR START -->
                      <input class="form-control" type="text" name="service_type" id="service_type" hidden>
                      <tr>
                        <td>Vehicle No</td>
                        <td>
                          <input class="form-control" type="text" name="v_kirvehicle_no" id="v_kirvehicle_no" class="formdefault" readonly>
                        </td>
                        <td>Vehicle Name</td>
                        <td>
                          <input class="form-control" type="text" name="v_kirvehicle_name" id="v_kirvehicle_name" class="formdefault" readonly>
                        </td>
                      </tr>
                      <tr>
                        <td>Workshop / Agencies / Location</td>
                        <td>
                          <select class="form-control" class="formdefault" name="work_agenc_kir_setservicess" id="work_agenc_kir_setservicess">
                            <?php foreach ($workshop as $work) {?>
                              <option value="<?php echo $work['workshop_id'] ?>">
                                <?php echo $work['workshop_name'] ?>
                              </option>
                              <?php } ?>
                          </select>
                        </td>
                        <td>KIR. No</td>
                        <td>
                          <input class="form-control" type="text" name="v_kirno_setservicess" id="v_kirno_setservicess" class="formdefault" readonly>
                        </td>
                      </tr>
                      <tr>
                        <td>KIR Date</td>
                        <td>
                          <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                              <!-- <input class="form-control" size="5" type="text" name="kirexpdate" id="kirexpdatenotempty" class="date-pick" value="<?=date('d-m-Y')?>"/> -->
                              <input class="form-control" type="text" name="v_kirdate_setservicess" id="v_kirdate_setservicess" class="date-pick" value="<?=date('d-m-Y')?>"/>
                              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                          </div>
                        </td>
                        <td>KIR Exp Date</td>
                        <td>
                          <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                              <!-- <input class="form-control" size="5" type="text" name="kirexpdate" id="kirexpdatenotempty" class="date-pick" value="<?=date('d-m-Y')?>"/> -->
                              <input class="form-control" type="text" name="v_kir_exp_date_setservicess" id="v_kir_exp_date_setservicess" class="date-pick" value="<?=date('d-m-Y')?>"/>
                              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>Pelaksana</td>
                        <td>
                          <input class="form-control" type="text" name="v_kir_pelaksana" id="v_kir_pelaksana" class="formdefault">
                        </td>
                        <td>Biaya</td>
                        <td>
                          <input class="form-control" type="number" name="v_kir_biaya" id="v_kir_biaya" class="formdefault rupiah">
                        </td>
                      </tr>

                      <tr>
                        <td>Note</td>
                        <td>
                          <textarea class="form-control" name="v_kirnote_setservicess" name="v_kirnote_setservicess" id="v_kirnote_setservicess" rows="5" cols="50"></textarea>
                        </td>
                        <td></td>
                        <td></td>
                      </tr>
                    </table>
                    <div class="text-right">
                      <button type="button" class="btn btn-warning" onclick="closemodalforsetservice();" />Cancel</button>
                      <button type="submit" class="btn btn-success" onclick="saveservicess()">Save</button>
                    </div>
                  </div>
                  <!-- FOR KIR END -->

                  <!-- FOR PERPANJANG STNK START -->
                  <div id="perpanjangstnkview" style="display: none;">
                    <table width="100%" cellpadding="8" class="table sortable no-margin">
                      <!-- FOR KIR START -->
                      <input class="form-control" type="text" name="service_type_stnk" id="service_type_stnk" hidden>
                      <tr>
                        <td>Vehicle No</td>
                        <td>
                          <input class="form-control" type="text" name="v_perpstnk_vehicle_no" id="v_perpstnk_vehicle_no" class="formdefault" readonly>
                        </td>
                        <td>Vehicle Name</td>
                        <td>
                          <input class="form-control" type="text" name="v_perpstnk_vehicle_name" id="v_perpstnk_vehicle_name" class="formdefault" readonly>
                        </td>
                      </tr>
                      <tr>
                        <td>Workshop / Agencies / Location</td>
                        <td>
                          <select class="form-control" class="formdefault" name="work_agenc_stnk_setservicess" id="work_agenc_stnk_setservicess">
                            <?php foreach ($workshop as $work) {?>
                              <option value="<?php echo $work['workshop_id'] ?>">
                                <?php echo $work['workshop_name'] ?>
                              </option>
                              <?php } ?>
                          </select>
                        </td>
                        <td>STNK. No</td>
                        <td>
                          <input class="form-control" type="text" name="v_perpstnk_no_setservicess" id="v_perpstnk_no_setservicess" class="formdefault" readonly>
                        </td>
                      </tr>
                      <tr>
                        <td>Extend Date</td>
                        <td>
                          <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                              <input class="form-control" type="text" name="v_perpstnk_date_setservicess" id="v_perpstnk_date_setservicess" class="date-pick" value="<?=date('d-m-Y')?>"/>
                              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                          </div>
                        </td>
                        <td>Exp Date</td>
                        <td>
                          <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                              <input class="form-control" type="text" name="v_perpstnk_expdate_setservicess" id="v_perpstnk_expdate_setservicess" class="date-pick" value="<?=date('d-m-Y')?>"/>
                              <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td>Pelaksana</td>
                        <td>
                          <input class="form-control" type="text" name="v_perpstnk_pelaksana" id="v_perpstnk_pelaksana" class="formdefault">
                        </td>
                        <td>Biaya</td>
                        <td>
                          <input class="form-control" type="number" name="v_perpstnk_biaya" id="v_perpstnk_biaya" class="formdefault rupiah">
                        </td>
                      </tr>

                      <tr>
                        <td>Note</td>
                        <td>
                          <textarea class="form-control" name="v_perpstnk_note_setservicess" id="v_perpstnk_note_setservicess" rows="5" cols="50"></textarea>
                        </td>
                        <td></td>
                        <td></td>
                      </tr>
                    </table>
                    <div class="text-right">
                      <button type="button" class="btn btn-warning" onclick="closemodalforsetservice();" />Cancel</button>
                      <button type="submit" class="btn btn-success" onclick="saveservicess()">Save</button>
                    </div>
                  </div>
                  <!-- FOR PERPANJANG STNK END -->

                  <!-- FOR SERVICE START -->
                  <div id="serviceview" style="display: none;">
                  <!-- <td>
                    <h4>
                      Serviced By :
                      <div id="configservicedby"></div>
                    </h4>
                  </td> -->
                  <table width="100%" cellpadding="8" class="table sortable no-margin">
                    <input class="form-control" type="text" name="service_type_stnk" id="service_type_stnk" hidden>
                    <tr>
                      <td>Vehicle No</td>
                      <td>
                        <input class="form-control" type="text" name="v_service_vehicle_no" id="v_service_vehicle_no" class="formdefault" readonly>
                      </td>
                      <td>Vehicle Name</td>
                      <td>
                        <input class="form-control" type="text" name="v_service_vehicle_name" id="v_service_vehicle_name" class="formdefault" readonly>
                      </td>
                    </tr>
                    <tr>
                      <td>Workshop / Agencies / Location</td>
                      <td>
                        <select class="form-control" class="formdefault" name="work_agenc_setservicess" id="work_agenc_setservicess">
                          <?php foreach ($workshop as $work) {?>
                            <option value="<?php echo $work['workshop_id'] ?>">
                              <?php echo $work['workshop_name'] ?>
                            </option>
                            <?php } ?>
                        </select>
                      </td>
                      <td>Service Date</td>
                      <td>
                        <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                            <input class="form-control" type="text" name="v_service_date_setservicess" id="v_service_date_setservicess" class="date-pick" value="<?=date('d-m-Y')?>"/>
                            <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                      <td>Last Odometer</td>
                      <td>
                        <input class="form-control" type="number" name="v_service_lastodometer" id="v_service_lastodometer" class="formdefault">
                      </td>
                    </tr>

                    <tr>
                      <td>Pelaksana</td>
                      <td>
                        <input class="form-control" type="text" name="v_service_pelaksana" id="v_service_pelaksana" class="formdefault">
                      </td>
                      <td>Biaya</td>
                      <td>
                        <input class="form-control" type="number" name="v_service_biaya" id="v_service_biaya" class="formdefault rupiah">
                      </td>
                    </tr>


                    <tr>
                      <td>Note</td>
                      <td>
                        <textarea class="form-control" name="v_service_note_setservicess" id="v_service_note_setservicess" rows="5" cols="50"></textarea>
                      </td>
                      <td></td>
                      <td></td>
                    </tr>
                </table>
                <div class="text-right">
                  <button type="button" class="btn btn-warning" onclick="closemodalforsetservice();" />Cancel</button>
                  <button type="submit" class="btn btn-success" onclick="saveservicess()">Save</button>
                </div>
              </div>
            </div>
        </div>
    </div>
  </div>
</div>
</div>

<div id="modalvehiclesetting" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Vehicle Setting</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodalvehiclesetting();">X</button>
          </div>
        </div>
        <div class="card-body">
          <div id="resultcontent">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="modalvfuelcalibration" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Vehicle Fuel Calibration</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodalvfuelcalibration();">X</button>
          </div>
        </div>
        <div class="card-body">
          <div id="resultfuelcalibration">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="modaladdserviceworks" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Add Service Works</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodaladdserviceworks();">X</button>
          </div>
        </div>
        <div class="card-body">
          <form class="frmserviceworks" id="frmserviceworks" action="javascript:saveserviceworks()">
            <table width="100%" cellpadding="8" class="table sortable no-margin">
              <tr>
                <td>Vehicle No</td>
                <td>
                  <select class="select2" name="serviceworks_vehicle_no" id="serviceworks_vehicle_no">
                      <?php for ($i=0; $i < sizeof($datavehicle); $i++) {?>
                        <option value="<?php echo $datavehicle[$i]['vehicle_id'].'.'.$datavehicle[$i]['vehicle_device'].'.'.$datavehicle[$i]['vehicle_no'].'.'.$datavehicle[$i]['vehicle_name'] ?>">
                          <?php echo $datavehicle[$i]['vehicle_no'].' '.$datavehicle[$i]['vehicle_name']; ?>
                        </option>
                      <?php } ?>
                  </select>
                </td>
                <!-- <td>Vehicle Name</td>
                <td>
                  <input class="form-control" type="text" name="serviceworks_vehicle_name" id="serviceworks_vehicle_name" class="formdefault" readonly>
                </td> -->
              </tr>
              <tr>
                <td>Workshop / Agencies / Location</td>
                <td>
                  <select class="form-control" name="serviceworks_work_agenc_setservicess" id="serviceworks_work_agenc_setservicess">
                    <?php foreach ($workshop as $work) {?>
                      <option value="<?php echo $work['workshop_id'] ?>">
                        <?php echo $work['workshop_name'] ?>
                      </option>
                      <?php } ?>
                  </select>
                </td>
                <td>Service Date</td>
                <td>
                  <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                      <input class="form-control" type="text" name="serviceworks_service_date" id="serviceworks_service_date" class="date-pick" value="<?=date('d-m-Y')?>"/>
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                  </div>
                </td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td>Last Odometer</td>
                <td>
                  <input class="form-control" type="number" name="serviceworks_lastodometer" id="serviceworks_lastodometer" class="formdefault">
                </td>
              </tr>

              <tr>
                <td>Pelaksana</td>
                <td>
                  <input class="form-control" type="text" name="serviceworks_pelaksana" id="serviceworks_pelaksana" class="formdefault">
                </td>
                <td>Biaya</td>
                <td>
                  <input class="form-control" type="number" name="serviceworks_biaya" id="serviceworks_biaya" class="formdefault rupiah">
                </td>
              </tr>


              <tr>
                <td>Note</td>
                <td>
                  <textarea class="form-control" name="serviceworks_note" id="serviceworks_note" rows="5" cols="50"></textarea>
                </td>
                <td></td>
                <td></td>
              </tr>
          </table>
          <div class="text-right">
            <button type="button" class="btn btn-warning" onclick="closemodaladdserviceworks();" />Cancel</button>
            <button type="submit" class="btn btn-success" onclick="saveserviceworks()">Save</button>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>



<script type="text/javascript">
  // FOR CHANGE SERVICED BY
  function servicedbyonchange(){
    var servicedby = $("#servicedby").val();
    console.log("klik");
    console.log("servicedby : ", servicedby);
      if (servicedby == "") {
        $("#valueservicedby").hide();
        $("#alertlimit").hide();
      }else {
        $("#valueservicedby").show();
        $("#alertlimit").show();
      }
  }

  function configthisvehicle(idnya){
    console.log(idnya);
    $.post('<?php echo base_url(); ?>vehicles/forconfigservicess/', {id: idnya},
      function(response)
      {
        console.log("response : ", response);
        if (response.isirow > 0) {
          $("#titleheader").html("Update Configuration for this Vehicle");
          $("#vehicle_no").val(response.vehicle[0].vehicle_no);
          $("#vehicle_name").val(response.vehicle[0].vehicle_name);
          $("#vehicle_device").val(response.vehicle[0].vehicle_device);
          $("#vehicle_type_gps").val(response.vehicle[0].vehicle_type);
          $("#stnkexpdatenotempty").val(response.data[0].maintenance_conf_stnkexpdate);
          $("#kirexpdatenotempty").val(response.data[0].maintenance_conf_kirexpdate);
          $("#valueservicedby").val(response.data[0].maintenance_conf_valueservicedby);
          $("#alertlimit").val(response.data[0].maintenance_conf_alertlimit);
          $("#servicedby").val(response.data[0].maintenance_conf_servicedby);
          $("#vehicle_type").val(response.data[0].maintenance_conf_vehicle_type);
          $("#vehicle_year").val(response.data[0].maintenance_conf_vehicle_year);
          $("#no_rangka").val(response.data[0].maintenance_conf_no_rangka);
          $("#no_mesin").val(response.data[0].maintenance_conf_no_mesin);
          $("#stnk_no").val(response.data[0].maintenance_conf_stnk_no);
          $("#kir_no").val(response.data[0].maintenance_conf_kir_no);
          $("#valueservicedby").show();
          $("#alertlimit").show();
          $("#modalforconfigservice").show();
        }else {
          $("#titleheader").html("Set Configuration for this Vehicle");
          $("#vehicle_no").val(response.vehicle[0].vehicle_no);
          $("#vehicle_name").val(response.vehicle[0].vehicle_name);
          $("#vehicle_device").val(response.vehicle[0].vehicle_device);
          $("#vehicle_type_gps").val(response.vehicle[0].vehicle_type);
          $("#stnkexpdatenotempty").val("");
          $("#kirexpdatenotempty").val("");
          $("#valueservicedby").val("");
          $("#alertlimit").val("");
          $("#servicedby").val("");
          $("#vehicle_type").val("");
          $("#vehicle_year").val("");
          $("#no_rangka").val("");
          $("#no_mesin").val("");
          $("#stnk_no").val("");
          $("#kir_no").val("");
          $("#valueservicedby").hide();
          $("#alertlimit").hide();
          $("#modalforconfigservice").show();
        }
      }
      , "json"
    );
  }

  function setservicess(idnya){
		// console.log(idnya);
    $("#perpanjangstnkview").hide();
    $("#serviceview").hide();
    $("#kirview").hide();
    $("#selectservicess").val("");

		$.post('<?php echo base_url(); ?>vehicles/forsetservicess/', {id: idnya},
			function(response)
			{
        console.log("response : ", response);
        if (response.sizeconfig == 0) {
          alert("Please input Maintenance Configuration First.");
        }else {
          $("#titleheadersetservicess").html("Set Servicess for this Vehicle");
          $("#vehicle_device_setservicess").val(response.dataconfigmaintenance[0].maintenance_conf_vehicle_device);
          $("#v_kirvehicle_no").val(response.dataconfigmaintenance[0].maintenance_conf_vehicle_no);
          $("#v_kirvehicle_name").val(response.dataconfigmaintenance[0].maintenance_conf_vehicle_name);
          $("#v_kirno_setservicess").val(response.dataconfigmaintenance[0].maintenance_conf_kir_no);
          $("#v_perpstnk_vehicle_no").val(response.dataconfigmaintenance[0].maintenance_conf_vehicle_no);
          $("#v_perpstnk_vehicle_name").val(response.dataconfigmaintenance[0].maintenance_conf_vehicle_name);
          $("#v_perpstnk_no_setservicess").val(response.dataconfigmaintenance[0].maintenance_conf_stnk_no);
          $("#v_service_vehicle_no").val(response.dataconfigmaintenance[0].maintenance_conf_vehicle_no);
          $("#v_service_vehicle_name").val(response.dataconfigmaintenance[0].maintenance_conf_vehicle_name);
          $("#modalforsetservicess").show();
  				// console.log(response);
        }
			}
			, "json"
		);
	}

  // FOR SAVE CONFIGURATION
  function saveconfiguration(){
    var vehicle_no          = $("#vehicle_no").val();
    var vehicle_name        = $("#vehicle_name").val();
    var vehicle_type        = $("#vehicle_type").val();
    var vehicle_year        = $("#vehicle_year").val();
    var no_rangka           = $("#no_rangka").val();
    var no_mesin            = $("#no_mesin").val();
    var stnk_no             = $("#stnk_no").val();
    var stnkexpdatenotempty = $("#stnkexpdatenotempty").val();
    var stnkexpdateifempty  = $("#stnkexpdateifempty").val();
    var kir_no              = $("#kir_no").val();
    var kirexpdatenotempty  = $("#kirexpdatenotempty").val();
    var kirexpdateifempty   = $("#kirexpdateifempty").val();
    var servicedby          = $("#servicedby").val();
    var valueservicedby     = $("#valueservicedby").val();
    var vehicle_device      = $("#vehicle_device").val();
    var vehicle_type_gps    = $("#vehicle_type_gps").val();
    var adaisinya           = $("#adaisinya").val();
    var alertlimit           = $("#alertlimit").val();
    var stnkexpdatefix;
    var kirexpdatefix;

    if (adaisinya == 1) {
      stnkexpdatefix = stnkexpdatenotempty;
      kirexpdatefix = kirexpdatenotempty;
    }else {
      stnkexpdatefix = stnkexpdateifempty;
      kirexpdatefix = kirexpdateifempty;
    }

    var data = {
      vehicle_no          : vehicle_no,
      vehicle_name        : vehicle_name,
      vehicle_type        : vehicle_type,
      vehicle_year        : vehicle_year,
      no_rangka           : no_rangka,
      no_mesin            : no_mesin,
      stnk_no             : stnk_no,
      stnkexpdatefix      : stnkexpdatefix,
      kir_no              : kir_no,
      kirexpdatefix       : kirexpdatefix,
      servicedby          : servicedby,
      valueservicedby     : valueservicedby,
      vehicle_device      : vehicle_device,
      vehicle_type_gps    : vehicle_type_gps,
      alertlimit          : alertlimit,
    };

    $.post("<?php echo base_url()?>vehicles/savethisconfiguration", data,
    function(response)
      {
  				if (response.status == "success") {
            if (confirm(response.msg)) {
              window.location = '<?php echo base_url()?>vehicles';
            }
          }else {
            alert("Process Failed");
          }
			}
			, "json"
		);
  }

  // FOR SELECT SERVICESS ON CHANGE
  function selectservicess(){
    var servicess = $("#selectservicess").val();
    $("#service_type").val(servicess);
    if (servicess == 2) {
      // KIR
      $("#perpanjangstnkview").hide();
      $("#serviceview").hide();
      $("#kirview").show();
    } else if (servicess == 3) {
      // PERPNANG STNK
      $("#kirview").hide();
      $("#serviceview").hide();
      $("#perpanjangstnkview").show();
    } else {
      // SERVICE
      $("#kirview").hide();
      $("#perpanjangstnkview").hide();
      $("#serviceview").show();
    }
    console.log("servicess : ", servicess);
  }

  // SAVE SERVICE TO SERVICE HISTORY
  function saveservicess() {
    var tipeservice = $("#service_type").val();
    var vehicle_device = $("#vehicle_device_setservicess").val();

    var data;
    var url;
    if (tipeservice == 2) {
      // KIR
      var v_kirvehicle_no             = $("#v_kirvehicle_no").val();
      var v_kirvehicle_name           = $("#v_kirvehicle_name").val();
      var work_agenc_kir_setservicess = $("#work_agenc_kir_setservicess").val();
      var v_kirno_setservicess        = $("#v_kirno_setservicess").val();
      var v_kirdate_setservicess      = $("#v_kirdate_setservicess").val();
      var v_kir_exp_date_setservicess = $("#v_kir_exp_date_setservicess").val();
      var v_kir_pelaksana             = $("#v_kir_pelaksana").val();
      var v_kir_biaya                 = $("#v_kir_biaya").val();
      var v_kirnote_setservicess      = $("#v_kirnote_setservicess").val();

      url                             = "<?php echo base_url();?>vehicles/savetomaintenancehistory";
      data = {
        v_kirno_setservicess: v_kirno_setservicess,
        v_kirdate_setservicess: v_kirdate_setservicess,
        v_kir_exp_date_setservicess: v_kir_exp_date_setservicess,
        v_kirnote_setservicess: v_kirnote_setservicess,
        v_kirvehicle_no: v_kirvehicle_no,
        v_kirvehicle_name: v_kirvehicle_name,
        v_kir_biaya: v_kir_biaya,
        v_kir_pelaksana: v_kir_pelaksana,
        work_agenc_kir_setservicess: work_agenc_kir_setservicess,
        tipeservice: tipeservice,
        vehicle_device: vehicle_device
      };
    } else if (tipeservice == 3) {
      // PERPANJANG STNK
      var v_perpstnk_vehicle_no           = $("#v_perpstnk_vehicle_no").val();
      var v_perpstnk_vehicle_name         = $("#v_perpstnk_vehicle_name").val();
      var work_agenc_stnk_setservicess    = $("#work_agenc_stnk_setservicess").val();
      var v_perpstnk_no_setservicess      = $("#v_perpstnk_no_setservicess").val();
      var v_perpstnk_date_setservicess    = $("#v_perpstnk_date_setservicess").val();
      var v_perpstnk_expdate_setservicess = $("#v_perpstnk_expdate_setservicess").val();
      var v_perpstnk_pelaksana            = $("#v_perpstnk_pelaksana").val();
      var v_perpstnk_biaya                = $("#v_perpstnk_biaya").val();
      var v_perpstnk_note_setservicess    = $("#v_perpstnk_note_setservicess").val();


      url = "<?php echo base_url();?>vehicles/savetomaintenancehistory";
      data = {
        v_perpstnk_vehicle_no: v_perpstnk_vehicle_no,
        v_perpstnk_vehicle_name: v_perpstnk_vehicle_name,
        work_agenc_stnk_setservicess: work_agenc_stnk_setservicess,
        v_perpstnk_no_setservicess: v_perpstnk_no_setservicess,
        v_perpstnk_date_setservicess: v_perpstnk_date_setservicess,
        v_perpstnk_expdate_setservicess: v_perpstnk_expdate_setservicess,
        v_perpstnk_pelaksana: v_perpstnk_pelaksana,
        v_perpstnk_biaya: v_perpstnk_biaya,
        v_perpstnk_note_setservicess: v_perpstnk_note_setservicess,
        tipeservice: tipeservice,
        vehicle_device: vehicle_device
      };
    } else {
      // SERVICE
      var v_service_vehicle_no        = $("#v_service_vehicle_no").val();
      var v_service_vehicle_name      = $("#v_service_vehicle_name").val();
      var work_agenc_setservicess     = $("#work_agenc_setservicess").val();
      var v_service_date_setservicess = $("#v_service_date_setservicess").val();
      var v_service_pelaksana         = $("#v_service_pelaksana").val();
      var v_service_biaya             = $("#v_service_biaya").val();
      var v_service_lastodometer      = $("#v_service_lastodometer").val();
      var v_service_note_setservicess = $("#v_service_note_setservicess").val();

      url = "<?php echo base_url();?>vehicles/savetomaintenancehistory";
      data = {
        v_service_vehicle_no: v_service_vehicle_no,
        v_service_vehicle_name: v_service_vehicle_name,
        work_agenc_setservicess: work_agenc_setservicess,
        v_service_date_setservicess: v_service_date_setservicess,
        v_service_pelaksana: v_service_pelaksana,
        v_service_biaya: v_service_biaya,
        v_service_lastodometer: v_service_lastodometer,
        v_service_note_setservicess: v_service_note_setservicess,
        tipeservice: tipeservice,
        vehicle_device: vehicle_device
      };
    }
    console.log("url : ", url);
    console.log("data : ", data);
    console.log("tipeservice : ", tipeservice);
    $.post(url, data, function(response) {
      console.log("response", response);
      if (response.status == "success") {
      	if (confirm(response.msg)) {
      		window.location = '<?php echo base_url()?>vehicles';
      	}
      }else {
      	alert("Process Failed");
      }
    }, 'json');
  }

  function closemodalforconfigservice(){
    $("#modalforconfigservice").hide();
  }

  function closemodalforsetservice(){
    $("#modalforsetservicess").hide();
  }

  // GET DATA FOR VEHICLE SETTING
  function vform(v){
    console.log("v : ", v);
    jQuery.post('<?php echo base_url(); ?>vehicles/formvehicle/', {id: v}, function(r){
      console.log("r : ", r);
        $("#resultcontent").html(r.html);
        $("#modalvehiclesetting").show();
      }, "json");
  }

  // GET DATA FOR VEHICLE CALIBRATION
  function vformfuel(id){
    console.log("id : ", id);
    jQuery.post('<?php echo base_url(); ?>vehicles/vfuelcalibration/', {id: id}, function(r){
      console.log("r : ", r);
        $("#resultfuelcalibration").html(r.html);
        $("#modalvfuelcalibration").show();
      }, "json");
  }

  function vehiclemdt(id){
    console.log("id : ", id);
    jQuery.post('<?php echo base_url(); ?>vehicles/mdtimei/', {id: id}, function(r){
      console.log("r : ", r);
        $("#resultcontent").html(r.html);
        $("#modalvehiclesetting").show();
      }, "json");
  }

  function closemodalvehiclesetting(){
    $("#modalvehiclesetting").hide();
  }

  function closemodalvfuelcalibration(){
    $("#modalvfuelcalibration").hide();
  }

  function addserviceworks(){
    $("#modaladdserviceworks").show();
  }

  function closemodaladdserviceworks(){
    $("#modaladdserviceworks").hide();
  }

  function saveserviceworks(){
    $.post("<?php echo base_url() ?>vehicles/saveserviceworks", $("#frmserviceworks").serialize(), function(response){
      console.log("response : ", response);
      if (response.status == "success") {
      	if (confirm(response.msg)) {
      		window.location = '<?php echo base_url()?>vehicles';
      	}
      }else {
      	alert("Failed Insert Service Works");
      }
    }, "json");
  }
</script>
