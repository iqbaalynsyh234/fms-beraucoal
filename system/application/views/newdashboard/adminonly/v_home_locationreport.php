<style media="screen">
  #location-report{
    background-color: #221f1f;
    color: white;
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
    <!--<div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">Operational Report</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a class="parent-item" href="">Report</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Operational Report</li>
                            </ol>
                        </div>
                    </div>-->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading" id="location-report">LOCATION REPORT - DEVELOPMENT</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
              <div class="form-group row">
                <label class="col-lg-3 col-md-3 control-label">Contractor
                </label>
                <div class="col-lg-4 col-md-3">
                  <select class="form-control select2" id="company" name="company" onchange="javascript:company_onchange()">
                    <option value="all">Select Contractor</option>
                    <?php
														$ccompany = count($rcompany);
															for($i=0;$i<$ccompany;$i++){
																if (isset($rcompany)&&($row->parent_company == $rcompany[$i]->company_id)){
																		$selected = "selected";
																	}else{
																		$selected = "";
																	}
																echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
																}
													?>
                  </select>
                </div>
              </div>

              <div class="form-group row" id="mn_vehicle">
                <label class="col-lg-3 col-md-3 control-label">Vehicle
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="vehicle" name="vehicle" class="form-control select2">
                    <option value="all">Select Vehicle</option>

                    <?php
														$cvehicle = count($vehicles);
															for($i=0;$i<$cvehicle;$i++){
																echo "<option value='" . $vehicles[$i]->vehicle_device ."' " . $selected . ">" . $vehicles[$i]->vehicle_no ." - ".$vehicles[$i]->vehicle_name. "</option>";
																}
													?>

                  </select>
                </div>

              </div>

              <div class="form-group row">
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

              <div class="form-group row">
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
                <label class="col-lg-3 col-md-4 control-label">Status
                </label>
                <div class="col-lg-3 col-md-4">
                  <select class="form-control" id="statusname" name="statusname">
                    <option value="all">All</option>
                    <option value="location">MOVE</option>
                    <option value="location_idle">IDLE</option>
                    <option value="location_off">OFF</option>
                  </select>
                </div>

              </div>

              <!-- <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label">Max Voltage</label>
                <div class="col-lg-3 col-md-4">
                  <input type="text" name="maxvoltage" id="maxvoltage" class="form-control">
                </div>
              </div> -->

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label">by Speed
                </label>
                <div class="col-lg-3 col-md-4">
                  <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-1">
                    <input name="type_speed" id="option-1" type="radio" value="" onClick="option_type_speed('speed_no')" class="mdl-radio__button" checked>
                    <span class="mdl-radio__label">No</span>
                  </label>
                  <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-2">
                    <input name="type_speed" id="option-2" type="radio" value="1" onClick="option_type_speed('speed_yes')" class="mdl-radio__button">
                    <span class="mdl-radio__label">Yes</span>
                  </label>

                  <div id="speed_view" style="display:none">
                    Speed :
                    <input class="form-control" type="text" name="statusspeed" id="statusspeed" value="" size="50" placeholder="Ex: 10" />
                  </div>
                </div>
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label"> by Location
                </label>
                <div class="col-lg-3 col-md-4">
                  <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-1">
                    <input name="type_location" id="option-1" type="radio" value="" onClick="option_type_location('location_no')" class="mdl-radio__button" checked>
                    <span class="mdl-radio__label">No</span>
                  </label>
                  <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-2">
                    <input name="type_location" id="option-2" type="radio" value="1" onClick="option_type_location('location_yes')" class="mdl-radio__button">
                    <span class="mdl-radio__label">Yes</span>
                  </label>

                  <div id="location_view" style="display:none">
                    Location Start:
                    <input class="form-control" type="text" name="location_start" id="location_start" value="" size="50" placeholder="Ex: KM 5.5" />
                    <!--Location End: <input class="form-control" type="text" name="location_end" id="location_end" value="" size="50" placeholder="Ex: batulicin"/>-->
                  </div>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-lg-3 col-md-3">

                </div>
                <div class="col-lg-3 col-md-3">
                  <button class="btn btn-circle btn-success" id="btnsearchreport" type="submit" />Search</button>
                  <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
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
    <!--jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');-->
    jQuery("#loader2").show();
    jQuery("#result").hide();
    jQuery.post("<?=base_url();?>adminonly/search_locationreport", jQuery("#frmsearch").serialize(),
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

  function option_type_speed(v) {
    switch (v) {
      case "speed_no":
        jQuery('#speed_status').val("");
        jQuery("#speed_view").hide();
        break;
      case "speed_yes":
        jQuery("#speed_view").show();
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

	function option_map(v) {
		switch (v) {
			case "hide":
				jQuery("#btn_hide_map").hide();
				jQuery("#btn_show_map").show();
				jQuery("#panel_map").hide();

				break;
			case "show":
				jQuery("#btn_hide_map").show();
				jQuery("#btn_show_map").hide();
				jQuery("#panel_map").show();
				break;
		}
	}
</script>
