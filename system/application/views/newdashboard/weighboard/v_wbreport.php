<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
  <div class="row">
    <div class="col-md-12">
      <div class="panel" id="panel_form">
        <header class="panel-heading panel-heading-red">WB Report</header>
        <div class="panel-body" id="bar-parent10">
          <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="frmsearch_onsubmit();">
            <div class="form-group row" id="mn_vehicle">
              <label class="col-lg-3 col-md-3 control-label">Vehicle</label>
              <div class="col-lg-4 col-md-4">
                <select id="vehicle" name="vehicle" class="form-control select2">
                    <option value="all">--All Vehicle</option>
                  <?php foreach ($vehicle as $rowvehicle) {?>
                    <option value="<?php echo $rowvehicle['vehicle_no'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="form-group row" id="mn_vehicle">
              <label class="col-lg-3 col-md-3 control-label">Periode
              </label>
              <div class="col-lg-4 col-md-3">
                <select id="periode" name="periode" id="periode" class="form-control select2" onchange="periode_onchange()">
                  <option value="yesterday">Yesterday</option>
                  <option value="last7">Last 7</option>
                  <option value="last30">Last 30</option>
                  <option value="custom">Custom Date</option>
                </select>
              </div>
            </div>

            <div class="form-group row" id="mn_sdate" style="display:none">
              <label class="col-lg-3 col-md-4 control-label">Start Date
              </label>
              <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y',strtotime(" yesterday ") )?>">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
              </div>
              <input type="hidden" id="dtp_input2" value="" />

              <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?=date(" H:i ",strtotime("00:00:00 "))?>">
                <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
              </div>
              <input type="hidden" id="dtp_input3" value="" />
            </div>

            <div class="form-group row" id="mn_edate" style="display:none">
              <label class="col-lg-3 col-md-4 control-label">End Date
              </label>
              <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y',strtotime(" yesterday ") )?>">
                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
              </div>
              <input type="hidden" id="dtp_input2" value="" />
              <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?=date(" H:i ",strtotime("23:59:59 "))?>">
                <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
              </div>
              <input type="hidden" id="dtp_input3" value="" />
            </div>
          </form>
          <div class="form-group row">
            <label class="col-lg-3 col-md-4 control-label">
            </label>
            <div class="col-lg-3 col-md-3">
              <button class="btn btn-circle btn-success" id="btnsearchreport" type="button" onclick="frmsearch_onsubmit();"/>Search</button>
              <img src="<?php echo base_url();?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    <div id="resultreport" style="display:none;"></div>
</div>

<script type="text/javascript">
function frmsearch_onsubmit(){
  $("#resultreport").hide();
  var alarmtype = $("#alarmtype").val();
  $("#alarmfix").val(alarmtype);
  // console.log("alarmtype : ", alarmtype);
  $("#loadernya").show();
  $.post("<?php echo base_url() ?>wbreport/searchreport", jQuery("#frmsearch").serialize(), function(response){
    if (response.error) {
      alert(response.message)
        $("#loader2").hide();
        $("#result").hide();
        $("#loadernya").hide();
          }else {
      $("#loader2").hide();
      $("#result").show();
      $("#loadernya").hide();
      $("#resultreport").html(response.html);
      $("#resultreport").show();
      console.log("response : ", response);
    }
  }, "json");
}

function periode_onchange(){
  var data_periode = jQuery("#periode").val();
  if(data_periode == 'custom'){
    jQuery("#mn_sdate").show();
    jQuery("#mn_edate").show();
  }else{
    jQuery("#mn_sdate").hide();
    jQuery("#mn_edate").hide();

  }
}
</script>
