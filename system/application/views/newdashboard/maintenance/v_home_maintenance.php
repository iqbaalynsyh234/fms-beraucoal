<style media="screen">
div#modaladdserviceworks {
  margin-top: 1.5%;
  margin-left: 17.5%;
  height: 96%;
  position: absolute;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  z-index: 1;
  overflow-y: auto;
  width: 60%;
}

#scheduled{
  background-color: #221f1f;
  color: white;
}

  div#modalforconfigservice {
    margin-top: 1.5%;
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
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 1;
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
    <button type="button" class="btn btn-primary" id="btnunscheduledservice" onclick="btnunscheduledservice();">Show Unscheduled Service</button>
    <button type="button" class="btn btn-primary" id="hidebtnunscheduledservice" onclick="btnunscheduledservice();" style="display:none;">Hide Unscheduled Service</button>
    <!-- <a class="btn btn-info" target="_blank" href="<?=base_url();?>vehicles/maintenanceshistory"><font>Maintenance History</font></a> -->
    <!-- <a class="btn btn-primary" target="_blank" href="<?=base_url();?>vehicles/workshop"><font>Manage Workshop / Agencies / Location</font></a> -->
    <br><br>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablevehicles">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red" id="scheduled">Scheduled Maintenance Data</header>
          <div class="panel-body" id="bar-parent10">
              <table id="example1" class="table table-striped" style="font-size:14px;">
                <thead>
          				<tr>
          					<th>
                      No
                    </th>
          					<th>Vehicle</th>
                    <th width="20%">Area</th>
                    <th>Type</th>
                    <th>Device</th>
                    <th>Odometer</th>
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
                        <?=$datavehicle[$i]['vehicle_name'].' - '.$datavehicle[$i]['vehicle_no'];?><br>
                      </td>
                      <td>
                        <?php
                        if (isset($company)) {
                          for ($j=0; $j < sizeof($company); $j++) {
                            if ($datavehicle[$i]['vehicle_company'] == $company[$j]['company_id']) {
                              echo "Branch Office : " . $company[$j]['company_name'];
                            }
                          }
                        }
                        echo "<br>";
                        if (isset($subcompany)) {
                          for ($k=0; $k < sizeof($subcompany); $k++) {
                            if ($datavehicle[$i]['vehicle_subcompany'] == $subcompany[$k]['subcompany_id']) {
                              echo "Sub Branch Office : " .  $subcompany[$k]['subcompany_name'];
                            }
                          }
                        }

                        echo "<br>";
                        if (isset($group)) {
                          for ($l=0; $l < sizeof($group); $l++) {
                            if ($datavehicle[$i]['vehicle_group'] == $group[$l]['group_id']) {
                              echo "Customer : " .  $group[$l]['group_name'];
                            }
                          }
                        }

                        echo "<br>";
                        if (isset($subgroup)) {
                          for ($l=0; $l < sizeof($subgroup); $l++) {
                            if ($datavehicle[$i]['vehicle_subgroup'] == $subgroup[$l]['subgroup_id']) {
                              echo "Sub Customer : " .  $subgroup[$l]['subgroup_name'];
                            }
                          }
                        }
                         ?>
                      </td>
            					<td><?=$datavehicle[$i]['vehicle_type'];?></td>
                      <td><?=$datavehicle[$i]['vehicle_device'];?></td>
                      <td><?=$datavehicle[$i]['vehicle_odometer'];?></td>
                      <?php if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <td>
                          <a href="javascript:configthisvehicle(<?php echo $datavehicle[$i]['vehicle_id'];?>)">
                            <img src="<?=base_url();?>assets/images/addvehicle.png" width="30px" height="30px" border="0" alt="<?php echo "Assign Vehicle"; ?>" title="Configuration Setup">
                          </a>

                          <a href="javascript:setservicess(<?php echo $datavehicle[$i]['vehicle_id'];?>)">
                            <img src="<?=base_url();?>assets/images/learning_profile.png" width="30px" height="30px" border="0" alt="<?php echo "Assign Vehicle"; ?>" title="Set Servicess">
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

    <div class="col-md-12" id="tablescheduledservice" style="display:none;">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red">Unscheduled Service List</header>
        <div class="panel-body" id="bar-parent10">
          <?php if ($privilegecode == 3) {?>

          <?php }else {?>
            <a class="btn btn-success" href="javascript:addserviceworks()">
              <span class="fa fa-plus"></span>
              Unscheduled Service
            </a>
          <?php } ?>
          <table id="example1" class="table table-striped" style="font-size:14px;">
            <thead>
              <tr>
                <th>
                  No
                </th>
                <th>Vehicle</th>
                <th>Service Type</th>
                <th>Odometer</th>
                <th>Note</th>
                <th>Status</th>
                <th>Service Date</th>
                <?php if ($privilegecode == 3) {?>

                <?php }else {?>
                  <th>Control</th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; for ($j=0; $j < sizeof($unscheduledservice); $j++) {?>
                <tr>
                  <td><?php echo $no; ?></td>
                  <td><?php echo $unscheduledservice[$j]['servicess_vehicle_no'].' '.$unscheduledservice[$j]['servicess_vehicle_name']; ?></td>
                  <td><?php echo $unscheduledservice[$j]['servicess_name']; ?></td>
                  <td><?php echo $unscheduledservice[$j]['servicess_nol']; ?></td>
                  <td><?php echo $unscheduledservice[$j]['servicess_note']; ?></td>
                  <td>
                    <?php
                    $status = $unscheduledservice[$j]['servicess_status'];
                     if ($status == 0) {?>
                      <button type="button" class="btn btn-warning btn-sm">Process</button>
                    <?php }else {?>
                      <button type="button" class="btn btn-success btn-sm">Completed</button>
                    <?php }  ?>
                  </td>
                  <td>
                    <?php
                    $singlestatus = $unscheduledservice[$j]['servicess_singleorestimated'];
                    if ($singlestatus == 0) {
                      echo date("d-m-Y", strtotime($unscheduledservice[$j]['servicess_date']));
                     }else {
                       $date1 = date("d-m-Y", strtotime($unscheduledservice[$j]['servicess_estimateddate_from']));
                       $date2 = date("d-m-Y", strtotime($unscheduledservice[$j]['servicess_estimateddate_end']));
                       echo $date1 . ' ~ ' . $date2;
                     }
                     ?>
                  </td>
                  <?php if ($privilegecode == 3) {?>

                  <?php }else {?>
                    <td>
                      <?php
                      $status = $unscheduledservice[$j]['servicess_status'];
                       if ($status == 0) {?>
                        <button type="button" class="btn btn-warning btn-sm" title="Change Status" onclick="changethisstatus('<?php echo $unscheduledservice[$j]['servicess_id'] ?>')">
                          <span class="fa fa-check"></span>
                        </button>
                      <?php }?>
                      <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deletethisdata('<?php echo $unscheduledservice[$j]['servicess_id'] ?>')">
                        <span class="fa fa-trash"></span>
                      </button>
                    </td>
                  <?php } ?>
                </tr>
              <?php $no++; } ?>
            </tbody>
          </table>
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


<div id="modaladdserviceworks" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-topline-yellow">
        <div class="card-head">
          <header>Unscheduled Service Form</header>
          <div class="tools">
            <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
            <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
            <button type="button" class="btn btn-danger" name="button" onclick="closemodaladdserviceworks();">X</button>
          </div>
        </div>
        <div class="card-body">
          <form class="frmserviceworks" id="frmserviceworks" action="javascript:saveserviceworks()">
            <input type="hidden" name="estimatedornot" id="estimatedornot">
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
                  <select class="select2" name="serviceworks_work_agenc_setservicess" id="serviceworks_work_agenc_setservicess">
                    <?php foreach ($workshop as $work) {?>
                      <option value="<?php echo $work['workshop_id'] ?>">
                        <?php echo $work['workshop_name'] ?>
                      </option>
                      <?php } ?>
                  </select>
                </td>
                <td></td>
              </tr>

              <tr>
                <td>Service Date</td>
                <td>
                  <input type="checkbox" name="showdate" id="showdate" onclick="showthisdate()" ><span style="color:blue;"> Estimated date</span>

                  <div id="singledate">
                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <input class="form-control" type="text" name="serviceworks_service_date" id="serviceworks_service_date" class="date-pick"/>
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                  </div>

                  <div id="estimateddateservice" style="display:none;">
                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <input class="form-control" type="text" name="serviceworks_estimateddate_from" id="serviceworks_estimateddate_from" class="date-pick"/>
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    ~
                    <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd-mm-yyyy" data-link-format="yyyy-mm-dd">
                        <input class="form-control" type="text" name="serviceworks_estimateddate_end" id="serviceworks_estimateddate_end" class="date-pick"/>
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
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
              </tr>

              <tr>
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
            <button type="submit" class="btn btn-success" id="btnsaveunscheduledservice">Save</button>
            <img id="loaderunscheduledservice" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
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
          $("#modalforconfigservice").fadeIn(1000);
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
          $("#modalforsetservicess").fadeIn(1000);
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
              window.location = '<?php echo base_url()?>vehicles/maintenance';
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
      		window.location = '<?php echo base_url()?>vehicles/maintenance';
      	}
      }else {
      	alert("Process Failed");
      }
    }, 'json');
  }

  function closemodalforconfigservice(){
    $("#modalforconfigservice").fadeOut(1000);
  }

  function closemodalforsetservice(){
    $("#modalforsetservicess").fadeOut(1000);
  }

  // GET DATA FOR VEHICLE SETTING
  // function vform(v){
  //   console.log("v : ", v);
  //   jQuery.post('<?php echo base_url(); ?>vehicles/formvehicle/', {id: v}, function(r){
  //     console.log("r : ", r);
  //       $("#resultcontent").html(r.html);
  //       $("#modalvehiclesetting").show();
  //     }, "json");
  // }

  function closemodalvehiclesetting(){
    $("#modalvehiclesetting").fadeOut(1000);
  }

  function addserviceworks(){
    $("#modaladdserviceworks").fadeIn(1000);
  }

  function closemodaladdserviceworks(){
    $("#modaladdserviceworks").fadeOut(1000);
  }

  function saveserviceworks(){
    $("#loaderunscheduledservice").show();
    // $("#btnsaveunscheduledservice").hide();
    $.post("<?php echo base_url() ?>vehicles/saveserviceworks", $("#frmserviceworks").serialize(), function(response){
      $("#btnsaveunscheduledservice").hide();
      console.log("response : ", response);
      if (response.status == "success") {
        if (confirm(response.msg)) {
      		window.location = '<?php echo base_url()?>vehicles/maintenance';
      	}
      }else {
        if (confirm(alert("Failed Insert Service Works"))) {
      		window.location = '<?php echo base_url()?>vehicles/maintenance';
      	}
      }
      $("#loaderunscheduledservice").show();
    }, "json");
  }

  function btnunscheduledservice(){
    $("#tablescheduledservice").toggle();
    $("#tablevehicles").toggle();
    $("#hidebtnunscheduledservice").toggle();
    $("#btnunscheduledservice").toggle();
  }

  function showthisdate() {
    console.log("show nih");
    var checkBox             = document.getElementById("showdate");
    // Get the output text
    var estimateddateservice = document.getElementById("estimateddateservice");
    var singledate           = document.getElementById("singledate");

    // If the checkbox is checked, display the output text
    if (checkBox.checked == true){
      $("#estimatedornot").val(1);
      estimateddateservice.style.display = "block";
      singledate.style.display = "none";
    } else {
      $("#estimatedornot").val(0);
      estimateddateservice.style.display = "none";
      singledate.style.display = "block";
    }
  }

  function changethisstatus(id){
    // console.log("id : ", id);
    if (confirm("Change this progress?")) {
      $.post("<?php echo base_url() ?>vehicles/changestatusunscheduledservice", {idscheduledservice:id}, function(response){
        console.log("response : ", response);
        if (response.status == "success") {
          if (confirm(response.msg)) {
            window.location = '<?php echo base_url()?>vehicles/maintenance';
          }
        }else {
          alert("Change Status is failed");
        }
      }, "json");
    }
  }

  function deletethisdata(id){
    // console.log("id : ", id);
    if (confirm("Are you sure want delete this data?")) {
      $.post("<?php echo base_url() ?>vehicles/deleteunscheduledservice", {idscheduledservice:id}, function(response){
        console.log("response : ", response);
        if (response.status == "success") {
        	if (confirm(response.msg)) {
        		window.location = '<?php echo base_url()?>vehicles/maintenance';
        	}
        }else {
        	alert("Change Status is failed");
        }
      }, "json");
    }
  }
</script>
