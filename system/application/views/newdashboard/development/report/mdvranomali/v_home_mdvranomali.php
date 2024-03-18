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

/*the container must be positioned relative:*/
.autocompleteno_lambung {
  position: relative;
  display: inline-block;
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

/* AUTOCOMPLETE NO LAMBUNG */
.autocompleteno_lambung-items {
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

.autocompleteno_lambung-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff;
  border-bottom: 1px solid #d4d4d4;
}

/*when hovering an item:*/
.autocompleteno_lambung-items div:hover {
  background-color: #e9e9e9;
}

/*when navigating through the items using the arrow keys:*/
.autocompleteno_lambung-active {
  background-color: DodgerBlue !important;
  color: #ffffff;
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

#modalinfodetail {
  margin-top: 1.5%;
  margin-left: 20%;
  height: 300px;
  max-height: 100%;
  position: fixed;
  background-color: #f1f1f1;
  text-align: left;
  border: 1px solid #d3d3d3;
  z-index: 99999999;
  overflow-y: auto;
  /* width: 50%; */
}

#security-evidence{
  background-color: #221f1f;
  color: white;
}
</style>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading" id="security-evidence">Historikal Anomali MDVR - DEVELOPMENT</header>
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

											echo "<option value='" . $rcompany[$i]->company_id . "'>" . $rcompany[$i]->company_name . "</option>";
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
											echo "<option value='" . $vehicles[$i]['vehicle_device'] . "'>" . $vehicles[$i]['vehicle_no'] . " - " . $vehicles[$i]['vehicle_name'] . "</option>";
										}
										?>

									</select>
								</div>
							</div>

              <div class="form-group row" id="mn_vehicle">
                <label class="col-lg-3 col-md-3 control-label">Type
                </label>
                <div class="col-lg-4 col-md-3">
                  <select id="anomali_type" name="anomali_type" class="form-control select2">
                    <option value="all">--Anomali List--</option>
                    <?php
                    $privilegecode = $this->sess->user_id_role;
                      if ($privilegecode != 0) {
                        $cmdvranomali = count($mdvr_anomali_list);
                        $cnon_admin   = count($non_admin);
                        for ($i = 0; $i < $cmdvranomali; $i++) {
                          $alarm_name = explode("Start", $mdvr_anomali_list[$i]['alarm_name']);
                          $alarm_type = $mdvr_anomali_list[$i]['alarm_type'];
                            for ($j=0; $j < $cnon_admin; $j++) {
                              if ($alarm_type == $non_admin[$j]) {
                                echo "<option value='" . $mdvr_anomali_list[$i]['alarm_type'] . "'>" . $alarm_name[0] . "</option>";
                              }
                            }

                        }
                      }else {
                        $cmdvranomali = count($mdvr_anomali_list);
                        for ($i = 0; $i < $cmdvranomali; $i++) {
                          $alarm_name = explode("Start", $mdvr_anomali_list[$i]['alarm_name']);
                          echo "<option value='" . $mdvr_anomali_list[$i]['alarm_type'] . "'>" . $alarm_name[0] . "</option>";
                        }
                      }
                    ?>

                  </select>
                </div>
              </div>

              <!-- <div class="form-group row" id="mn_vehicle">
								<label class="col-lg-3 col-md-3 control-label">Port</label>
								<div class="col-lg-4 col-md-3">
									<input type="number" name="port" id="port" class="form-control">
								</div>
							</div> -->

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

                    <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii" onclick="houronclick()">
                        <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?=date("H:i",strtotime("00:00:00"))?>">
                        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                    </div>
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
                      <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii" onclick="houronclick()">
                          <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?=date("H:i",strtotime("23:59:59"))?>">
                          <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                      </div>
                      <input type="hidden" id="dtp_input3" value="" />
                  </div>

                  <div class="form-group row" id="tooltip">
                    <label class="col-lg-3 col-md-3 control-label">
                    </label>
                    <div class="col-lg-4 col-md-3">
                      <div class="btn btn-info btn-sm btn-circle" title="Informasi" onclick="modalinfodetail()">
                        Info ?
                      </div>
                    </div>
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
  <!-- end page content -->

</div>

<div id="modalinfodetail" style="display: none; width:70%;">
  <div class="row">
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header id="titleheader">Informasi</header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalinfodetail();">X</button>
                </div>
            </div>
            <div class="card-body">
              <div id="contentpostevent">
                <table class="table table-bordered table-striped" style="font-size:12px;">
                  <tr>
                    <td>No</td>
                    <td>Nama Anomali</td>
                    <td>Deskripsi</td>
                  </tr>
                  <tr>
                    <td>1</td>
                    <td>Main Power Failure</td>
                    <td>Terputus Arus</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Video Loss Alarm</td>
                    <td>Socket Kamera Terputus</td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>GNSS Module Fault</td>
                    <td>Module GPS Pada MDVR Bermasalah</td>
                  </tr>

                  <tr>
                    <td>4</td>
                    <td>GNSS Antenna Missed or Cut</td>
                    <td>Antena GPS pada MDVR terlepas/rusak</td>
                  </tr>
                </table>
              </div>
            </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>

  function modalinfodetail(){
    $("#modalinfodetail").show();
  }

  function closemodalinfodetail(){
    $("#modalinfodetail").hide();
  }

	 function page(p) {
		if (p == undefined) {
			p = 0;
		}

		jQuery("#offset").val(p);
		jQuery("#loader2").show();
    jQuery("#result").hide();
		jQuery.post("<?= base_url(); ?>development/search_mdvr_anomali", jQuery("#frmsearch").serialize(),
			function(r) {
        console.log("response search_mdvr_anomali : ", r);
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

	function frmsearch_onsubmit()
	{
    $("#first_load").val(0);
		jQuery("#loader2").show();
		page();
		return false;
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

  function company_onchange() {
		var data_company = jQuery("#company").val();
		if (data_company == 0) {
			// alert('Silahkan Pilih Cabang!!');
			// jQuery("#mn_vehicle").hide();

			jQuery("#vehicle").html("<option value='0' selected='selected' class='form-control'>--All Vehicle--</option>");
		} else {
			jQuery("#mn_vehicle").show();

			var site = "<?= base_url() ?>development/get_vehicle_by_company_with_vdevice/" + data_company;
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

	function houronclick(){
		console.log("ok");
		$(".switch").html("<?php echo date("Y F d")?>");
	}

</script>
