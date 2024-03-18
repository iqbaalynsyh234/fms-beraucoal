<style media="screen">
  .modalClient{
    z-index: 9999;
    width: 100%;
    margin: auto;
  }

  .modalMaterial{
    z-index: 9999;
    width: 100%;
    margin: auto;
  }

  /* AUTOCOMPLETE STYLE */
  * {
    box-sizing: border-box;
  }

  body {
    font: 16px Arial;
  }

  .autocomplete_client {
    position: relative;
    display: inline-block;
  }

  .autocomplete_material {
    position: relative;
    display: inline-block;
  }

  input {
    border: 1px solid transparent;
    background-color: #f1f1f1;
    padding: 10px;
    font-size: 16px;
  }

  input[type=text] {
    /* background-color: #f1f1f1; */
    width: 100%;
  }

  input[type=submit] {
    background-color: DodgerBlue;
    color: #fff;
    cursor: pointer;
  }

  /* AUTOCOMPLETE CLIENT */
  .autocomplete_client-items {
    position: absolute;
    border: 1px solid #d4d4d4;
    border-bottom: none;
    border-top: none;
    z-index: 99;
    /*position the autocomplete items to be the same width as the container:*/
    top: 100%;
    left: 0;
    right: 0;
  }

  .autocomplete_client-items div {
    padding: 10px;
    cursor: pointer;
    background-color: #fff;
    border-bottom: 1px solid #d4d4d4;
  }

  /*when hovering an item:*/
  .autocomplete_client-items div:hover {
    background-color: #e9e9e9;
  }

  /*when navigating through the items using the arrow keys:*/
  .autocomplete_client-active {
    background-color: DodgerBlue !important;
    color: #ffffff;
  }

  /* AUTOCOMPLETE MATERIAL */
  .autocomplete_material-items {
    position: absolute;
    border: 1px solid #d4d4d4;
    border-bottom: none;
    border-top: none;
    z-index: 99;
    /*position the autocomplete items to be the same width as the container:*/
    top: 100%;
    left: 0;
    right: 0;
  }

  .autocomplete_material-items div {
    padding: 10px;
    cursor: pointer;
    background-color: #fff;
    border-bottom: 1px solid #d4d4d4;
  }

  /*when hovering an item:*/
  .autocomplete_material-items div:hover {
    background-color: #e9e9e9;
  }

  /*when navigating through the items using the arrow keys:*/
  .autocomplete_material-active {
    background-color: DodgerBlue !important;
    color: #ffffff;
  }
</style>
<!-- start sidebar menu -->
<!-- <div class="sidebar-container">
  <?=$sidebar;?>
</div> -->
<!-- end sidebar menu -->

<!-- start page content -->
<!-- <div class="page-content-wrapper">
  <div class="page-content"> -->
    <br>
    <div class="row">
      <div class="col-md-12">
        <div class="panel-body" id="bar-parent10">
          <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmittes()">
            <table id="table_form" width="100%" class="table table-striped" style="font-size:14px;">
            <input class="form-control" type="hidden" id="integrationwim_id" name="integrationwim_id" value="<?php echo $transdata[0]['integrationwim_id']; ?>" />

            <input type="hidden" name="integrationwim_transactionID_old" id="integrationwim_transactionID_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_transactionID']; ?>">
            <input type="text" name="integrationwim_penimbanganStartUTC_old" id="integrationwim_penimbanganStartUTC_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_penimbanganStartUTC']; ?>" hidden>
            <input type="text" name="integrationwim_penimbanganStartLocal_old" id="integrationwim_penimbanganStartLocal_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_penimbanganStartLocal']; ?>" hidden>
            <input type="text" name="integrationwim_penimbanganFinishUTC_old" id="integrationwim_penimbanganFinishUTC_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_penimbanganFinishUTC']; ?>" hidden>
            <input type="text" name="integrationwim_penimbanganFinishLocal_old" id="integrationwim_penimbanganFinishLocal_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_penimbanganFinishLocal']; ?>" hidden>
            <input type="text" name="integrationwim_beratTiapGandar_old" id="integrationwim_beratTiapGandar_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_beratTiapGandar']; ?>" hidden>
            <input type="text" name="integrationwim_totalGandar_old" id="integrationwim_totalGandar_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_totalGandar']; ?>" hidden>
            <input type="text" name="integrationwim_gross_old" id="integrationwim_gross_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_gross']; ?>" hidden>
            <input type="text" name="integrationwim_gross_manual_old" id="integrationwim_gross_manual_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_gross_manual']; ?>" hidden>
            <input type="text" name="integrationwim_tare_old" id="integrationwim_tare_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_tare']; ?>" hidden>
            <input type="text" name="integrationwim_tare_manual_old"  id="integrationwim_tare_manual_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_tare_manual']; ?>" hidden>
            <input type="text" name="integrationwim_netto_old" id="integrationwim_netto_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_netto']; ?>" hidden>
            <input type="text" name="integrationwim_netto_manual_old" id="integrationwim_netto_manual_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_netto_manual']; ?>" hidden>
            <input type="text" name="integrationwim_averageSpeed_old" id="integrationwim_averageSpeed_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_averageSpeed']; ?>" hidden>
            <input type="text" name="integrationwim_weightBalance_old" id="integrationwim_weightBalance_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_weightBalance']; ?>" hidden>
            <input type="text" name="integrationwim_rfid_old" id="integrationwim_rfid_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_rfid']; ?>" hidden>
            <input type="text" name="integrationwim_rfid_master_old" id="integrationwim_rfid_master_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_rfid_master']; ?>" hidden>
            <input type="text" name="integrationwim_gps_device_old" id="integrationwim_gps_device_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_gps_device']; ?>" hidden>
            <input type="text" name="integrationwim_gps_mv03_old" id="integrationwim_gps_mv03_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_gps_mv03']; ?>" hidden>
            <input type="text" name="integrationwim_noRangka_old" id="integrationwim_noRangka_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_noRangka']; ?>" hidden>
            <input type="text" name="integrationwim_noMesin_old" id="integrationwim_noMesin_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_noMesin']; ?>" hidden>
            <input type="text" name="integrationwim_truckType_old" id="integrationwim_truckType_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_truckType']; ?>" hidden>
            <input type="text" name="integrationwim_providerId_old" id="integrationwim_providerId_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_providerId']; ?>" hidden>
            <input type="text" name="integrationwim_truckID_old" id="integrationwim_truckID_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_truckID']; ?>" hidden>
            <input type="text" name="integrationwim_haulingContractor_old" id="integrationwim_haulingContractor_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_haulingContractor']; ?>" hidden>
            <input type="text" name="integrationwim_driver_name_old" id="integrationwim_driver_name_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_driver_name']; ?>" hidden>
            <input type="text" name="integrationwim_driver_id_old" id="integrationwim_driver_id_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_driver_id']; ?>" hidden>
            <input type="text" name="integrationwim_status_old" id="integrationwim_status_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_status']; ?>" hidden>
            <input type="text" name="integrationwim_distanceWB_old" id="integrationwim_distanceWB_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_distanceWB']; ?>" hidden>
            <input type="text" name="integrationwim_distanceWB_status_old" id="integrationwim_distanceWB_status_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_distanceWB_status']; ?>" hidden>
            <input type="text" name="integrationwim_truckImage_old" id="integrationwim_truckImage_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_truckImage']; ?>" hidden>
            <input type="text" name="integrationwim_created_date_old" id="integrationwim_created_date_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_created_date']; ?>" hidden>
            <input type="text" name="integrationwim_last_rom_old" id="integrationwim_last_rom_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_last_rom']; ?>" hidden>
            <input type="text" name="integrationwim_material_id_old" id="integrationwim_material_id_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_material_id']; ?>" hidden>
            <input type="text" name="integrationwim_hauling_id_old" id="integrationwim_hauling_id_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_hauling_id']; ?>" hidden>
            <input type="text" name="integrationwim_itws_coal_old" id="integrationwim_itws_coal_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_itws_coal']; ?>" hidden>
            <input type="text" name="integrationwim_client_id_old" id="integrationwim_client_id_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_client_id']; ?>" hidden>
            <input type="text" name="integrationwim_dumping_id_old" id="integrationwim_dumping_id_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_id']; ?>" hidden>
            <input type="text" name="integrationwim_dumping_name_old" id="integrationwim_dumping_name_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_name']; ?>" hidden>

            <input type="text" name="integrationwim_dumping_fms_port_old" id="integrationwim_dumping_fms_port_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_fms_port']; ?>" hidden>
            <input type="text" name="integrationwim_dumping_fms_cp_old" id="integrationwim_dumping_fms_cp_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_fms_cp']; ?>" hidden>
            <input type="text" name="integrationwim_dumping_fms_time_old" id="integrationwim_dumping_fms_time_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_fms_time']; ?>" hidden>
            <input type="text" name="integrationwim_dumping_fms_status_old" id="integrationwim_dumping_fms_status_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_fms_status']; ?>" hidden>
            <input type="text" name="integrationwim_dumping_fms_status_datetime_old" id="integrationwim_dumping_fms_status_datetime_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_fms_status_datetime']; ?>" hidden>


            <input type="text" name="integrationwim_other_text1_old" id="integrationwim_other_text1_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_other_text1']; ?>" hidden>
            <input type="text" name="integrationwim_other_text2_old" id="integrationwim_other_text2_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_other_text2']; ?>" hidden>
            <input type="text" name="integrationwim_approval_status_old" id="integrationwim_approval_status_old" class="form-control" value="<?php echo $transdata[0]['integrationwim_approval_status']; ?>" hidden>
            <input type="text" name="integrationwim_flag_old" id="integrationwim_flag_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_flag']; ?>" hidden>
            <input type="text" name="integrationwim_itws_trans_old" id="integrationwim_itws_trans_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_itws_trans']; ?>" hidden>
            <input type="text" name="integrationwim_itws_datetimetrans_old" id="integrationwim_itws_datetimetrans_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_itws_datetimetrans']; ?>" hidden>
            <input type="text" name="integrationwim_itws_slip_old" id="integrationwim_itws_slip_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_itws_slip']; ?>" hidden>
            <input type="text" name="integrationwim_itws_mode_old" id="integrationwim_itws_mode_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_itws_mode']; ?>" hidden>
            <input type="text" name="integrationwim_operator_status_old" id="integrationwim_operator_status_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_operator_status']; ?>" hidden>
            <input type="text" name="integrationwim_operator_user_id_old" id="integrationwim_operator_user_id_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_operator_user_id']; ?>" hidden>
            <input type="text" name="integrationwim_operator_user_name_old" id="integrationwim_operator_user_name_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_operator_user_name']; ?>" hidden>
            <input type="text" name="integrationwim_operator_datetime_old" id="integrationwim_operator_datetime_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_operator_datetime']; ?>" hidden>

            <input type="text" name="integrationwim_last_rom_stime_old" id="integrationwim_last_rom_stime_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_last_rom_stime']; ?>" hidden>
            <input type="text" name="integrationwim_last_rom_etime_old" id="integrationwim_last_rom_etime_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_last_rom_etime']; ?>" hidden>
            <input type="text" name="integrationwim_fms_status_old" id="integrationwim_fms_status_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_fms_status']; ?>" hidden>
            <input type="text" name="integrationwim_fms_status_datetime_old" id="integrationwim_fms_status_datetime_old" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_fms_status_datetime']; ?>" hidden>


            <tr>
              <td colspan="2">Image 1</td>
              <td>
                <!--<img src="<?php echo $transdata[0]['integrationwim_truckImage']; ?>" width="50%" height="50%">-->
                <a href="<?php echo base_url() ?>wim/show/<?php echo $transdata[0]['integrationwim_id'];?>" target="_blank"><img src="<?php echo $gdrive_image; ?>" width="100%" height="100%" ></a>
              </td>

              <td colspan="2">Image 2</td>
              <td>
                <!--<img src="<?php echo $transdata[0]['integrationwim_truckImage']; ?>" width="50%" height="50%">-->
                <a href="<?php echo base_url() ?>wim/show_2/<?php echo $transdata[0]['integrationwim_id'];?>" target="_blank"><img src="<?php echo $gdrive_image2; ?>" width="100%" height="100%" ></a>
              </td>
            </tr>

            <tr>
              <td colspan="2">TransID</td>
              <td>
                <input type="text" name="detail_vehicle_no" id="detail_vehicle_no" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_transactionID']; ?>" readonly>
              </td>
              <td colspan="2">TruckID</td>
              <td>
                <input type="text" name="detail_integrationwim_truckID" id="detail_integrationwim_truckID" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_truckID']; ?>" readonly>
              </td>
            </tr>

        <tr>
      <td colspan="2">RFID WIM</td>
      <td>
        <input type="text" name="detail_integrationwim_rfid" id="detail_integrationwim_rfid" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_rfid']; ?>" readonly>
      </td>

      <td colspan="2">RFID SPIP</td>
      <td>
        <input type="text" name="detail_integrationwim_rfidmaster" id="detail_integrationwim_rfidmaster" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_rfid_master']; ?>" readonly>
      </td>

    </tr>
    <tr>
        <td colspan="2">Driver</td>
        <td>
          <input type="text" name="detail_integrationwim_driverid" id="detail_integrationwim_driverid" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_driver_id']; ?>" readonly>
        </td>
        <td colspan="2">Status</td>
        <td>
          <input type="text" name="detail_integrationwim_status" id="detail_integrationwim_status" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_status']; ?>" readonly>
        </td>
    </tr>
        <tr>
            <td colspan="2">Penimbangan Start</td>
          <td>
            <input type="text" name="detail_integrationwim_penimbanganstartlocal" id="detail_integrationwim_penimbanganstartlocal" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_penimbanganStartLocal']; ?>" readonly>
          </td>
          <td colspan="2">Penimbangan Finish</td>
          <td>
            <input type="text" name="detail_integrationwim_penimbanganfinishlocal" id="detail_integrationwim_penimbanganfinishlocal" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_penimbanganFinishLocal']; ?>" readonly>
          </td>
        </tr>

            <tr>
                <td colspan="2">Gross</td>
                <td>
                  <input type="text" name="detail_integrationwim_gross" id="detail_integrationwim_gross" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_gross']; ?>" readonly>
                </td>
                <td colspan="2">Gross <small>(isi jika dari 3rd Party Port)</small></td>
                <td>
                  <input type="text" name="detail_integrationwim_grossmanual" id="detail_integrationwim_grossmanual" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_gross_manual']; ?>" >
                </td>
            </tr>

            <tr>
              <td colspan="2">Tare</td>
              <td>
                <input type="text" name="detail_integrationwim_tare" id="detail_integrationwim_tare" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_tare']; ?>" readonly>
              </td>
              <td colspan="2">Tare <small>(isi jika dari 3rd Party Port)</small></td>
              <td>
                <input type="text" name="detail_integrationwim_taremanual" id="detail_integrationwim_taremanual" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_tare_manual']; ?>" >
              </td>
            </tr>

            <tr>
              <td colspan="2">Netto</td>
              <td>
                <input type="text" name="detail_integrationwim_netto" id="detail_integrationwim_netto" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_netto']; ?>" readonly>
              </td>
              <td colspan="2">Netto <small>(isi jika dari 3rd Party Port)</small></td>
              <td>
                <input type="text" name="detail_integrationwim_nettomanual" id="detail_integrationwim_nettomanual" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_netto_manual']; ?>" >
              </td>
            </tr>

            <tr>
              <td colspan="2">Total Gandar</td>
              <td>
                <input type="text" name="detail_integrationwim_totalgandar" id="detail_integrationwim_totalgandar" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_totalGandar']; ?>" readonly>
              </td>
              <td colspan="2">Berat Tiap Gandar</td>
              <td>
                <input type="text" name="detail_integrationwim_berattiapgandar" id="detail_integrationwim_berattiapgandar" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_beratTiapGandar']; ?>" readonly>
              </td>
            </tr>

            <tr>
              <td colspan="2">Average Speed</td>
              <td>
                <input type="text" name="detail_integrationwim_average" id="detail_integrationwim_average" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_averageSpeed']; ?>" readonly>
              </td>
              <td colspan="2">Weight Balance (%)</td>
              <td>
                <input type="text" name="detail_integrationwim_wightbalance" id="detail_integrationwim_wightbalance" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_weightBalance']; ?>" readonly>
              </td>
            </tr>

           <tr>
              <td colspan="2">Remark <small>(Distance from WB in meter)</small></td>
              <td>
                <input type="text" name="detail_integrationwim_distanceWB" id="detail_integrationwim_distanceWB" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_distanceWB']; ?>" readonly>
              </td>
              <td colspan="2">Dumping <small>(isi jika dari 3rd Party Port)</small></td>
              <td>
                <select class="form-control select2" name="detail_dumping" id="detail_dumping">
                  <option value="0|0">--Select Port</option>
                  <?php for ($i=0; $i < sizeof($data_dumping); $i++) {?>
                    <?php
                      $curdatadumpingid = $transdata[0]['integrationwim_dumping_id'];
                      $curdatadumpingname = $transdata[0]['integrationwim_dumping_name'];

                      if ($data_dumping[$i]['dumping_id'] == $curdatadumpingid) {
                        $selected = "selected";
                      }else {
                        $selected = "";
                      }
                     ?>
                    <option value="<?php echo $data_dumping[$i]['dumping_id'].'|'.$data_dumping[$i]['dumping_name'] ?>" <?php echo $selected; ?>><?php echo $data_dumping[$i]['dumping_name'] ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>

            <tr>
              <td colspan="2">last Rom</td>
              <td>
                <input type="text" name="detail_lastrom" id="detail_lastrom" class="form-control" value="<?php echo $transdata[0]['integrationwim_last_rom']; ?>" readonly>
              </td>

              <td colspan="2">Dumping Port</td>
              <td>
                <input type="text" name="detail_dumpingport" id="detail_dumpingport" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_fms_port']; ?>" readonly>
              </td>
            </tr>

            <tr>
              <td colspan="2">Dumping CP</td>
              <td>
                <input type="text" name="detail_dumpingcp" id="detail_dumpingcp" class="form-control" value="<?php echo $transdata[0]['integrationwim_dumping_fms_cp']; ?>" readonly>
              </td>

            </tr>

             <tr>
               <td colspan="2">Client</td>
               <td>
                 <div class="autocompleteclient" style="width:200px;">
                 <div class="input-group mb-3">
                     <input type="text" name="detail_client" id="detail_client" class="form-control" value="<?php echo $transdata[0]['integrationwim_client_id']; ?>">
                   <div class="input-group-append">
                     <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#clientModal">
                       <span class="fa fa-info"></span>
                     </button>
                   </div>
                 </div>
                 </div>
               </td>

              <td colspan="2">Material</td>
              <td>
                <div class="autocompletematerial" style="width:200px;">
                <div class="input-group mb-3">
                    <input type="text" name="detail_material" id="detail_material" class="form-control" value="<?php echo $transdata[0]['integrationwim_material_id']; ?>">
                  <div class="input-group-append">
                    <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#materialModal">
                      <span class="fa fa-info"></span>
                    </button>
                  </div>
                </div>
                </div>
              </td>
            </tr>

            <tr>
              <td colspan="2">Hauling</td>
              <td>
                <input type="text" name="detail_hauling" id="detail_hauling" class="form-control" value="<?php echo $transdata[0]['integrationwim_hauling_id']; ?>" readonly>
              </td>

             <td colspan="2">Coal</td>
             <td>
               <input type="text" name="detail_coal" id="detail_coal" class="form-control" value="<?php echo $transdata[0]['integrationwim_itws_coal']; ?>" readonly>
             </td>
           </tr>

       <tr>
              <td colspan="2">Doc (Other Text 1)</td>
                <td>
                  <input type="text" name="integrationwim_other_text1" id="integrationwim_other_text1" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_other_text1']; ?>" >
                </td>
          <td colspan="2">Slip (Other Text 2)</td>
                <td>
                  <input type="text" name="integrationwim_other_text2" id="integrationwim_other_text2" class="form-control col-md-12" value="<?php echo $transdata[0]['integrationwim_other_text2']; ?>" >
                </td>
            </tr>



           <tr>
              <td colspan="2">Process</td>
              <td>
                <select class="form-control col-md-12" name="detail_status" id="detail_status">
                  <option value="approve">Approve</option>
                  <option value="reject">Reject</option>
                </select>
              </td>
              <td colspan="2"></td>
              <td>
                <div class="text-right">
                  <a onclick="btnCancel();" class="btn btn-warning">Cancel</a>
                  <!-- <button type="submit" name="button" class="btn btn-success">Update</button> -->
                  <button name="button" class="btn btn-success">Update</button>
                  <img id="loader3" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
                 <!-- <button type="button" name="button" class="btn btn-success" onclick="#">Update</button> -->
                </div>
              </td>
            </tr>

          </table>
        </form>
        </div>

      </div>
    </div>

    <div class="modal fade modalMaterial" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="materialModal" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="width:150%; margin-left: -20%;">
          <div class="modal-header">
            <h5 class="modal-title" id="materialModal">
              <b>
                Data Material
              </b>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div style="width: 100%; overflow-y:auto; max-height:450px;">
              <table id="table_2" class="table table-striped" style="width: 100%; font-size:11px;">
                <thead>
                  <tr>
                    <th>
                      No
                    </th>
                    <th>Shortcut</th>
                    <th>Material ID</th>
                    <th>Hauling</th>
                    <th>Coal</th>
                    <th>Desc</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (sizeof($data_material) > 0) {
                    for ($i=0; $i < sizeof($data_material); $i++) {?>
                      <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $data_material[$i]['material_shortcut'] ?></td>
                        <td><?php echo $data_material[$i]['material_id'] ?></td>
                        <td><?php echo $data_material[$i]['material_hauling'] ?></td>
                        <td><?php echo $data_material[$i]['material_coal'] ?></td>
                        <td><?php echo $data_material[$i]['material_description'] ?></td>
                      </tr>
                    <?php } } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade modalClient" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="clientModal" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="width:150%; margin-left: -20%;">
          <div class="modal-header">
            <h5 class="modal-title" id="clientModal">
              <b>
                Data Client
              </b>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div style="width: 100%; overflow-y:auto; max-height:450px;">
              <table id="table_1" class="table table-striped" style="width: 100%; font-size:11px;">
                <thead>
                  <tr>
                    <th>
                      No
                    </th>
                    <th>Shortcut</th>
                    <th>Client ID</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (sizeof($data_client) > 0) {
                    for ($i=0; $i < sizeof($data_client); $i++) {?>
                      <tr>
                        <td><?php echo $i+1; ?></td>
                        <td><?php echo $data_client[$i]['client_shortcut'] ?></td>
                        <td><?php echo $data_client[$i]['client_id'] ?></td>
                        <td><?php echo $data_client[$i]['client_description'] ?></td>
                      </tr>
                    <?php } } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>



<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
      $("#table_1").DataTable( {
          // "scrollY": "200px",
          "scrollCollapse": true,
          "searching": true,
          "paging": false
      });

      $("#table_2").DataTable( {
          // "scrollY": "100px",
          "scrollCollapse": true,
          "searching": true,
          "paging": false
      });

    });

  document.addEventListener('keypress', function (e) {
          if (e.keyCode === 13 || e.which === 13) {
              e.preventDefault();
              return false;
          }
      });

  function frmadd_onsubmittes(){
    $("#loader3").show();
    $.post('<?php echo base_url() ?>wim/updateDetailTruck', $("#frmadd").serialize(), function(response){
      $("#loader3").hide();
      console.log("response : ", response);
        if (response.msg == "success") {
          if (confirm("Data WIM successfully updated")) {
            page(0);
            $("#example1").show();
            $("#datadetail").hide();
            intervalpage = setInterval(page, 15000);
          }else {
            $("#example1").show();
            $("#datadetail").hide();
          }
        }else {
          if (confirm("Data WIM failed updated")) {
            $("#example1").show();
            $("#datadetail").hide();
          }else {
            $("#example1").show();
            $("#datadetail").hide();
          }
        }
    }, 'json');
    return false;
  }

  // AUTOCOMPLETE CLIENT
  function autocompleteclient(inp) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val    = this.value;
      var keyword_vehicle = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists_client();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete_client-list");
      a.setAttribute("class", "autocomplete_client-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);

      // GET FROM DATABASE
      var data = {
        keyword : keyword_vehicle
      };
      $("#loader2").show();
      $.post("<?= base_url(); ?>development/getClientByInput", data,
  			function(r) {
          $("#loader2").hide();
          console.log("response getClientByInput : ", r);
          var arr = r.data;
          console.log("arr : ", arr);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            // if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              console.log("masuk bang");
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              /*insert a input field that will hold the current array item's value:*/
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {

                  /*insert the value for the autocomplete text field:*/
                  inp.value = this.getElementsByTagName("input")[0].value;

                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists_client();
              });
              a.appendChild(b);
            // }
          }
  			}, "json"
  		);
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete_client-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive_client(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive_client(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive_client(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive_client(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete_client-active");
  }
  function removeActive_client(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete_client-active");
    }
  }
  function closeAllLists_client(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete_client-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists_client(e.target);
    });
  }

  // AUTOCOMPLETE MATERIAL
  function autocompletematerial(inp) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val    = this.value;
      var keyword_vehicle = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists_material();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete_material-list");
      a.setAttribute("class", "autocomplete_material-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);

      // GET FROM DATABASE
      var data = {
        keyword : keyword_vehicle
      };
      $("#loader2").show();
      $.post("<?= base_url(); ?>development/getMaterialByInput", data,
  			function(r) {
          $("#loader2").hide();
          console.log("response getMaterialByInput : ", r);
          var arr = r.data;
          console.log("arr : ", arr);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            // if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              console.log("masuk bang");
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
              b.innerHTML += arr[i].substr(val.length);
              /*insert a input field that will hold the current array item's value:*/
              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {

                  /*insert the value for the autocomplete text field:*/
                  inp.value = this.getElementsByTagName("input")[0].value;

                  var data3 = {
                    materialid :inp.value
                  };
                  $("#loader2").show();
                    $.post("<?= base_url(); ?>development/getMaterialValue", data3, function(response) {
                      $("#loader2").hide();
                      console.log("response getMaterialValue : ", response);
                      $("#detail_hauling").val(response.data[0].material_hauling);
                      $("#detail_coal").val(response.data[0].material_coal);
                    }, "json");

                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists_material();
              });
              a.appendChild(b);
            // }
          }
  			}, "json"
  		);
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete_material-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive_material(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive_material(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive_material(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive_material(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete_material-active");
  }
  function removeActive_material(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete_material-active");
    }
  }
  function closeAllLists_material(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete_material-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists_material(e.target);
    });
  }

  /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
  // autocompletenolambung(document.getElementById("itws_nolambung"), countries);
  autocompleteclient(document.getElementById("detail_client"));
  autocompletematerial(document.getElementById("detail_material"));


  /* var url = "https://www.googleapis.com/drive/v2/files/18LnBe3byOs7TUFiAXAgxt0vd4T8qGOB8?alt=media";

	var xhr = new XMLHttpRequest();
	xhr.open("GET", url);

	xhr.setRequestHeader("Accept", "application/json");
	xhr.setRequestHeader("Authorization", "Bearer ya29.a0ARrdaM8zNBX_eoCULFxvUOc-A1vh8aQXRrlnOzqCHxrqCEprCf2qOD4sM_0NOttUBAyF_VuzOlvj65-RUs5fvQQotTf_ojAgOlO6q4TFDr1124F2GzLJKrbxpeIZyYMhRWxf5x_8aRmPMXokbb5RZtrHnJY_0Q");

	xhr.onreadystatechange = function () {
	   if (xhr.readyState === 4) {
		  //console.log(xhr.status);
		  console.log(xhr.responseText);


	   }};

	xhr.send(); */
</script>
