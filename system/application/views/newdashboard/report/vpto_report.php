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
    jQuery("#loader2").show();
    jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
    jQuery("#result").show();
    jQuery("#btnsearchreport").hide();
    jQuery.post("<?=base_url();?>tripreport/search_pto/", jQuery("#frmsearch").serialize(),
      function(r) {
        if (r.error) {
          alert(r.message);
          jQuery("#loader2").hide();
          jQuery("#result").hide();
          jQuery("#btnsearchreport").show();
          return;
        } else {
          jQuery("#loader2").hide();
          jQuery("#btnsearchreport").show();
          jQuery("#result").show();
          jQuery("#result").html(r.html);
          jQuery("#total").html(r.total);

        }
      }, "json"
    );
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
<style>

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
                                <div class="page-title">History Map</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a class="parent-item" href="">Report</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">History Map</li>
                            </ol>
                        </div>
                    </div>-->
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">PTO DAILY REPORT</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
              <div class="form-group row" id="mn_vehicle">
                <label class="col-lg-3 col-md-3 control-label">Vehicle
                </label>
                <div class="col-lg-4 col-md-4">
                  <select id="vehicle" name="vehicle" class="form-control select2">
                    <!--<option value="">Select a Vehicle</option>-->

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
                  <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y')?>">
                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                <input type="hidden" id="dtp_input2" value="" />

                <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                  <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?=date("H:i",strtotime("00:00:00"))?>" onclick="houronclick();">
                  <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                </div>
                <input type="hidden" id="dtp_input3" value="" />
              </div>

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label">End Date
                </label>
                <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                  <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y')?>">
                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                </div>
                <input type="hidden" id="dtp_input2" value="" />
                <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                  <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?=date("H:i",strtotime("23:59:59"))?>" onclick="houronclick();">
                  <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                </div>
                <input type="hidden" id="dtp_input3" value="" />
              </div>


              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label">
                </label>
                <div class="col-lg-3 col-md-3">
                  <!--<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-1">
													<input name="tableview" id="option-1" type="radio" value="" class="mdl-radio__button" checked>
													<span class="mdl-radio__label">No</span>
												</label>
												<label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="option-2">
													<input name="tableview" id="option-2" type="radio" value="1" class="mdl-radio__button">
													<span class="mdl-radio__label">Yes</span>
												</label>-->
                  <button class="btn btn-circle btn-success" id="btnsearchreport" type="submit" />Search</button>
                </div>
                <div class="col-lg-3 col-md-3">
                  <td>
                    <small>Note: Report ini khusus untuk penarikan data harian. Untuk report lebih dari 1 (satu) hari klik <b> <a href="<?=base_url();?>operationalreport/pto" />Operational Report (PTO)</a></b></small>
                  </td>
                </div>
              </div>

            </form>
          </div>

        </div>

      </div>

    </div>

    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
    <div id="result"></div>
  </div>
  <!-- end page content -->

</div>
<!-- end page container -->
<script type="text/javascript">
	function houronclick(){
		$(".switch").html("<?php echo date("Y F d")?>");
	}
</script>
