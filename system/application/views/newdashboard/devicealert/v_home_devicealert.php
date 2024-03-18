<style media="screen">
  #device-alert{
    background-color: #1f50a2;
    color: white;
  }
</style>


<script>
  function frmsearch_onsubmit() {
    jQuery("#loader").show();
    page(0);
    return false;
  }

  function page(p) {
    if (p == undefined) {
      p = 0;
    }
    jQuery("#offset").val(p);
    // jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
    jQuery("#loader2").show();
    jQuery.post("<?=base_url();?>devicealert/searchthisalert", jQuery("#frmsearch").serialize(),
      function(r) {
        if (r.error) {
          alert(r.message);
          jQuery("#loader2").hide();
          jQuery("#result").hide();
          return;
        } else {
          jQuery("#loader2").hide();
          jQuery("#result").show();
          jQuery("#result").html(r.html);
          jQuery("#total").html(r.total);

        }
      }, "json"
    );
  }

  function company_onchange() {
    var data_company = jQuery("#company").val();
    if (data_company == 0) {
      alert('Silahkan Pilih Cabang!!');
      jQuery("#mn_vehicle").hide();

      jQuery("#vehicle").html("<option value='0' selected='selected'>--Select Vehicle--</option>");
    } else {
      jQuery("#mn_vehicle").show();

      var site = "<?=base_url()?>dashboard/get_vehicle_by_company/" + data_company;
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

  function option_type_location(v) {
    switch (v) {
      case "location_no":
        jQuery('#location').val("");
        jQuery("#location_view").hide();
        break;
      case "location_yes":
        jQuery("#location_view").show();
        break;
    }
  }

  function option_type_duration(v) {
    switch (v) {
      case "duration_no":
        jQuery('#s_minute').val("");
        jQuery('#e_minute').val("");
        jQuery("#duration_view").hide();
        break;
      case "duration_yes":
        jQuery("#duration_view").show();
        break;
    }
  }

  function option_type_km(v) {
    switch (v) {
      case "km_no":
        jQuery('#km_start').val("");
        jQuery('#km_end').val("");
        jQuery("#km_view").hide();
        break;
      case "km_yes":
        jQuery("#km_view").show();
        break;
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
</script>
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<div class="page-content-wrapper">
  <div class="page-content">

    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading" id="device-alert">DEVICE ALERT REPORT</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
              <div class="form-group row" id="mn_vehicle">
                <label class="col-lg-3 col-md-3 control-label">Vehicle
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="vehicle" name="vehicle" class="form-control select2">
                    <option value="0">--Select Vehicle</option>
                    <?php for ($i=0; $i < sizeof($vehicles); $i++) {?>
                      <option value="<?php echo $vehicles[$i]->vehicle_device ?>"><?php echo $vehicles[$i]->vehicle_no ." - ".$vehicles[$i]->vehicle_name ?></option>
                    <?php } ?>
                  </select>
                </div>

              </div>


              <div class="form-group row" id="mn_vehicle">
                <label class="col-lg-3 col-md-3 control-label">Alert Type
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="alertype" name="alertype" class="form-control select2">
                    <option value="all">--All Alarm</option>
                    <?php for ($i=0; $i < sizeof($devicealertlist); $i++) {?>
                      <option value="<?php echo $devicealertlist[$i]['alerttype_name'] ?>"><?php echo $devicealertlist[$i]['alerttype_alias'] ?></option>
                    <?php } ?>
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

              <div class="form-group row">
                <div class="col-lg-3 col-md-3">

                </div>
                <div class="col-lg-3 col-md-3">
                  <button class="btn btn-circle btn-success" id="btnsearchreport" type="submit" />Search</button>
                  <!--<img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />-->
                </div>
                <!--<div class="col-lg-6 col-md-6">
												<input class="btn btn-circle btn-danger" type="button" value="History Maps" onclick="javascript:return historymap()"/>
											</div>-->
              </div>
            </form>
          </div>

        </div>

      </div>

    </div>
    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" style="display: none;"></div>
    <div id="result"></div>
  </div>
  <!-- end page content -->

</div>
<!-- end page container -->
