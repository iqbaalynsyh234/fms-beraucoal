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
</style>

<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->
<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading" style="background-color: #221f1f; color:white;">DRIVER DETECTED</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        			<input type="hidden" name="offset" id="offset" value=""/>
        			<input type="hidden" id="sortby" name="sortby" value=""/>
        			<input type="hidden" id="orderby" name="orderby" value=""/>

						<div class="form-group row">
								<label class="col-lg-3 col-md-3 control-label">Contractor
								</label>
								<div class="col-lg-4 col-md-3">
									<select class="form-control select2" id="company" name="company" onchange="javascript:company_onchange()">
										<option value="all" selected>--All Contractor--</option>
										<?php
										$ccompany = count($rcompany);
										for ($i = 0; $i < $ccompany; $i++) {
                      if ($this->sess->user_privilege == 5 || $this->sess->user_privilege == 6) {
                        if ($rcompany[$i]->company_id == $this->sess->user_company) {
                          echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                        }
                      }else {
                        echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
                      }
										}
										?>
									</select>
								</div>
							</div>

							<div class="form-group row" id="mn_vehicle">
								<label class="col-lg-3 col-md-3 control-label">Vehicle
								</label>
								<div class="col-lg-4 col-md-3">
									<select id="vehicle" name="vehicle" class="form-control select2 multi">
										<option value="all">--All Vehicle--</option>
										<?php
										$cvehicle = count($vehicles);
										for ($i = 0; $i < $cvehicle; $i++) {
											echo "<option value='" . $vehicles[$i]->vehicle_no . "'>" . $vehicles[$i]->vehicle_no . " - " . $vehicles[$i]->vehicle_name . "</option>";
										}
										?>

									</select>
								</div>
							</div>

              <div class="form-group row">
								<label class="col-lg-3 col-md-3 control-label">Driver
								</label>
								<div class="col-lg-4 col-md-3">
									<select id="driver" name="driver" class="form-control select2 multi">
										<option value="all">--All Driver--</option>
										<?php
										$cvehicle = count($driverportal);
										for ($i = 0; $i < $cvehicle; $i++) {
                      $portalnikexpl = explode("-", $driverportal[$i]['portal_nik']);
											echo "<option value='" . $portalnikexpl[1] . "'>" . $driverportal[$i]['portal_nik'] . " - " . $driverportal[$i]['portal_name'] . "</option>";
										}
										?>

									</select>
								</div>
							</div>

                  <div class="form-group row" id="mn_vehicle">
                    <label class="col-lg-3 col-md-3 control-label">Periode
                    </label>
                    <div class="col-lg-4 col-md-3">
                      <select id="periode" name="periode" class="form-control select2" onchange="javascript:periode_onchange()">
                        <option value="today">Today</option>
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
                      <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y')?>">
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <input type="hidden" id="dtp_input2" value="" />

                    <!--<div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                      <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?=date(" H:i ",strtotime("00:00:00 "))?>">
                      <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                    </div>-->
                    <input type="hidden" id="dtp_input3" value="" />
                  </div>

                  <div class="form-group row" id="mn_edate" style="display:none">
                    <label class="col-lg-3 col-md-4 control-label">End Date
                    </label>
                    <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                      <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y')?>">
                      <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <input type="hidden" id="dtp_input2" value="" />
                    <!--<div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                      <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?=date(" H:i ",strtotime("23:59:59 "))?>">
                      <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                    </div>-->
                    <input type="hidden" id="dtp_input3" value="" />
                  </div>

								<div class="text-right">
                  <button class="btn btn-success btn-circle" id="btnsearchreport" type="submit"/>Search</button>
								</div>
        		</form>
          </div>

        </div>

      </div>

    </div>
    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" style="display:none;"></div>
    <div id="result" style="width:100%"></div>
  </div>

</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>
function frmsearch_onsubmit()
{
  $("#first_load").val(0);
  jQuery("#loader2").show();
  page(0);
  return false;
}

	 function page(p) {
		if (p == undefined) {
			p = 0;
		}

		jQuery("#offset").val(p);
		//jQuery("#result").html('<img src="<?php echo base_url(); ?>assets/transporter/images/loader2.gif">');
		jQuery("#loader2").show();
		jQuery.post("<?= base_url(); ?>driverchange/search_report", jQuery("#frmsearch").serialize(),
			function(r) {
        console.log("response search_report : ", r);
				if (r.error) {
					alert(r.message);
					jQuery("#loader2").hide();
					jQuery("#result").hide();
				} else {
					jQuery("#loader2").hide();
					jQuery("#result").show();
					jQuery("#result").html(r.html);
					jQuery("#total").html(r.total);
				}
			}, "json"
		);
    return false;
	}



	function company_onchange() {
		var data_company = jQuery("#company").val();
		if (data_company == 0) {
			// alert('Silahkan Pilih Cabang!!');
			// jQuery("#mn_vehicle").hide();

			jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--All Vehicle--</option>");
		} else {
			jQuery("#mn_vehicle").show();

			var site = "<?= base_url() ?>driverchange/get_vehicle_by_company_with_numberorder/" + data_company;
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

	function houronclick(){
		console.log("ok");
		$(".switch").html("<?php echo date("Y F d")?>");
	}
</script>
