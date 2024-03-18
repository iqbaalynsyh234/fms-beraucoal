<style media="screen">
  div#modalinformationdetail {
    max-height: 500px;
    width: 65%;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }

  #operational-report{
    background-color: #221f1f;
    color: white;
  }
</style>
<div class="sidebar-container">
  <?= $sidebar; ?>
</div>

<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12" id="reportfilter">

        <!-- MODAL LIST VEHICLE -->
        <div id="modalinformationdetail" style="display: none;">
          <div id="mydivheader"></div>
          <div id="contentinformationdetail">

          </div>
        </div>

        <div class="panel" id="panel_form">
          <header class="panel-heading" id="operational-report">Operational Report</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript: return frmsearch_onsubmit();">

              <div class="form-group row">
                <label class="col-lg-3 col-md-3 control-label">Contractor
                </label>
                <div class="col-lg-4 col-md-3">
                  <select class="form-control select2" id="company" name="company" onchange="javascript:company_onchange()">
                    <option value="0" selected>--All Contractor--</option>
                    <?php
                    $ccompany = count($rcompany);
                    for ($i = 0; $i < $ccompany; $i++) {

                      echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label">Vehicle
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="vehicle" name="vehicle" class="form-control select2">
                    <option value="0">--All Vehicle--</option>
                    <?php
                    $i = 1;
                    foreach ($vehicles as $rowvehicle) {
                    ?>
                      <option value="<?php echo $rowvehicle['vehicle_device'] ?>">
                        <?php echo $i . '. ' . $rowvehicle['vehicle_no'] . ' ' . $rowvehicle['vehicle_name'] ?>
                      </option>
                    <?php $i++;
                    } ?>
                  </select>
                </div>
              </div>

              <div class="form-group row" id="mn_vehicle">
                <label class="col-lg-3 col-md-3 control-label">Periode
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="periode" name="periode" id="periode" class="form-control select2" onchange="javascript:periode_onchange()">
                    <option value="yesterday">Yesterday</option>
                    <option value="last7">Last 7 Days</option>
                    <option value="last30">Last 30 Days</option>
                    <option value="custom">Custom Date</option>
                  </select>
                </div>

              </div>

              <div class="form-group row" id="mn_sdate" style="display:none">
                <label class="col-lg-3 col-md-4 control-label">Start Date
                </label>
                <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                  <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?= date('d-m-Y', strtotime(" yesterday ")) ?>">
                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                <input type="hidden" id="dtp_input2" value="" />

                <!-- <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                  <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?= date(" H:i ", strtotime("00:00:00 ")) ?>">
                  <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                </div>
                <input type="hidden" id="dtp_input3" value="" /> -->
              </div>

              <div class="form-group row" id="mn_edate" style="display:none">
                <label class="col-lg-3 col-md-4 control-label">End Date
                </label>
                <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                  <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?= date('d-m-Y', strtotime(" yesterday ")) ?>">
                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                <input type="hidden" id="dtp_input2" value="" />
                <!-- <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                  <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?= date(" H:i ", strtotime("23:59:59 ")) ?>">
                  <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                </div>
                <input type="hidden" id="dtp_input3" value="" /> -->
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label"></label>
                <div class="col-lg-3 col-md-3">
                  <button class="btn btn-circle btn-success" id="btnsearchreport" type="submit">Search</button>
                  <img src="<?php echo base_url(); ?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>

      <div class="col-md-12" id="resultreport" style="display:none;">

      </div>

    </div>
    <!-- <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div> -->
    <div id="result" style="width:100%"></div>
  </div>
</div>

<div id="modalState" class="modal">
  <div class="modal-content-state">
    <div class="row">
      <div class="col-md-10">
        <p class="modalTitleforAll" id="modalStateTitle">
        </p>
        <!-- <div id="contractorinlocation" style="font-size:14px; color:black"></div> -->
        <!-- <div id="lastcheckpoolws" style="font-size:12px; color:black"></div> -->
      </div>
      <div class="col-md-2">
        <div class="closethismodalall btn btn-danger btn-sm">X</div>
      </div>
    </div>
    <div id="modalStateContent"></div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url() ?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  $("#loadernya").hide();

  function frmsearch_onsubmit() {
    console.log(jQuery("#frmsearch").serialize());
    $("#resultreport").hide();
    $("#loadernya").show();
    $.post("<?php echo base_url() ?>operational/operational_search", jQuery("#frmsearch").serialize(), function(response) {
      if (response.error) {
        alert(response.message)
        $("#loader2").hide();
        $("#result").hide();
        $("#loadernya").hide();
      } else {
        $("#loader2").hide();
        $("#result").show();
        $("#loadernya").hide();
        $("#resultreport").html(response.html);
        $("#resultreport").show();
        console.log("response : ", response);
      }
    }, "json");
    return false;
  }

  function closemodallistofvehicle() {
    $("#modalinformationdetail").hide();
  }

  function houronclick() {
    console.log("ok");
    $(".switch").html("<?php echo date("Y F d ") ?>");
  }

  function periode_onchange() {
    var data_periode = jQuery("#periode").val();
    if (data_periode == 'custom') {
      jQuery("#mn_sdate").show();
      jQuery("#mn_edate").show();
    } else {
      jQuery("#mn_sdate").hide();
      jQuery("#mn_edate").hide();
    }
  }

  function option_form(v) {
    switch (v) {
      case "hide":
        jQuery("#btn_hide_form").hide();
        jQuery("#btn_show_form").show();
        jQuery("#panel_form").hide();

        break;
      case "show":
        jQuery("#btn_hide_form").show();
        jQuery("#btn_show_form").hide();
        jQuery("#panel_form").show();
        break;
    }
  }

  function company_onchange() {
    var data_company = jQuery("#company").val();
    if (data_company == 0) {
      // alert('Silahkan Pilih Cabang!!');
      // jQuery("#mn_vehicle").hide();

      jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--All Vehicle--</option>");
    } else {
      jQuery("#mn_vehicle").show();

      var site = "<?= base_url() ?>operational/get_vehicle_by_company_with_numberorder/" + data_company;
      jQuery.ajax({
        url: site,
        success: function(response) {
          jQuery("#vehicle").html("");
          jQuery("#vehicle").html(response);
        },
        dataType: "html"
      });

    }

  }
</script>