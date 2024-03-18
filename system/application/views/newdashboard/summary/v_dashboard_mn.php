<script src="<?php echo base_url(); 					?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>

<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/stock/highstock.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/stock/modules/data.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/stock/modules/exporting.js"></script>

<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/highcharts.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/modules/data.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/modules/drilldown.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/modules/exporting.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/modules/export-data.js"></script>
<script src="<?php echo base_url(); ?>assets/dashboard/assets/plugins/highcharts/modules/accessibility.js"></script>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
<!-- <script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet" /> -->

<script>
	$(document).ready(function() {
		// $('select').selectpicker();

		page(0);

	});

	function frmsearch_onsubmit() {
		jQuery("#loader2").show();
		page(0);
		return false;
	}

	function page(p) {
		if (p == undefined) {
			p = 0;
		}
		jQuery("#offset").val(p);
		jQuery("#result").hide();
		jQuery("#loader2").show();

		jQuery.post("<?= base_url(); ?>esdm/search_dev", jQuery("#frmsearch").serialize(),
			function(r) {
				if (r.error) {
					alert(r.message);
					jQuery("#loader2").hide();
					jQuery("#result").hide();
					return;
				} else {

					console.log(r.periode_show);
					jQuery("#loader2").hide();
					jQuery("#result").show();
					jQuery("#result").html(r.html);
					jQuery("#periode_show").html(r.periode_show);

					jQuery("#btn_hide_form").hide();
					jQuery("#btn_show_form").show();


				}
			}, "json"
		);
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
</script>
<!-- start sidebar menu -->
<div class="sidebar-container">
	<?= $sidebar; ?>
</div>
<!-- end sidebar menu -->
<!-- start page content -->
<div class="page-content-wrapper">
	<div class="page-content">
		<form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
			<div class="row">
				<div class="col-md-12 col-sm-12">
					<div class="panel" id="panel_form">
						<div class="card-header">
							<center>
								<font size="5px"><b>KEPMEN ESDM DASHBOARD</font> </b><br /><small><span id="periode_show"></span></small>
							</center>
						</div>
						<div class="panel-body" id="bar-parent10">
							<div class="form-group row">
								<div class="col-lg-3 col-md-3">
									<select id="contractor" name="contractor" class="form-control select2">

										<?php
										$pc = $this->sess->user_id_role;
										if (($pc == 0) || ($pc == 1) || ($pc == 2) || ($pc == 3) || ($pc == 4)) {
											echo '<option value="all">--All Contractor</option>';
										}
										$ccompany = count($rcompany);
										for ($i = 0; $i < $ccompany; $i++) {
											echo "<option value='" . $rcompany[$i]->company_id . "' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
										}
										?>
									</select>
								</div>
								<!-- <div class="col-lg-3 col-md-3">
									<select id="type" name="type" class="form-control select2">
										<option value="all">All Data</option>
										<option value="pa">Physical Availability (PA)</option>
										<option value="ua">Utilization of Availability (UA)</option>
										<option value="eu">Effective Utilization (EU)</option>
										<option value="ma">Mechanical Availability (MA)</option>

									</select>
								</div> -->
								<!-- <div class="col-lg-3 col-md-3">

									<select class="form-control" multiple name="test">
										<option value="pa">Physical Availability (PA)</option>
										<option value="ma">Mechanical Availability (MA)</option>
										<option value="ua">Utilization of Availability (UA)</option>
										<option value="eu">Effective Utilization (EU)</option>
									</select>
								</div> -->
								<div class="col-lg-3 col-md-3">
									<!--<select id="periode" name="periode" id="periode" class="form-control select2"  >-->
									<select name="periode" id="periode" class="form-control select2" onchange="javascript:periode_onchange()">
										<option value="yesterday">Yesterday</option>
										<option value="last7">Last 7 Days</option>
										<option value="last30">Last 30 Days</option>
										<option value="custom">Custom Date</option>
									</select>
								</div>
								<div class="col-lg-3 col-md-3">
									<button class="btn btn-circle btn-success" id="btnsearchreport" type="submit" />Search</button>
									<img id="loader2" style="display:none;" src="<?php echo base_url(); ?>assets/images/ajax-loader.gif" />
								</div>
							</div>
							<!-- <div class="form-group row">
								<div class="col-1">
									<input type="checkbox" id="alldata" name="type_all" value="all" checked>
									<label for="alldata">All</label>
								</div>
								<div class="col-1">
									<input type="checkbox" id="pa" name="type_pa" value="pa">
									<label for="pa">PA</label>
								</div>
								<div class="col-1">
									<input type="checkbox" id="ma" name="type_ma" value="ma">
									<label for="ma">MA</label>
								</div>
								<div class="col-1">
									<input type="checkbox" id="ua" name="type_ua" value="ua">
									<label for="ua">UA</label>
								</div>
								<div class="col-1">
									<input type="checkbox" id="eu" name="type_eu" value="eu">
									<label for="eu">EU</label>
								</div>
							</div> -->
							<div class="form-group row" id="mn_sdate" style="display:none;">

								<div class="col-lg-3 col-md-3">
									<div class="input-group date form_date" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
										<input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?= date('d-m-Y', strtotime("yesterday")) ?>">
										<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
									</div>
									<input type="hidden" id="dtp_input2" value="" />

								</div>
								<div class="col-lg-3 col-md-3">
									<div class="input-group date form_date" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
										<input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?= date('d-m-Y', strtotime("yesterday")) ?>">
										<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
									</div>
									<input type="hidden" id="dtp_input2" value="" />
								</div>



							</div>

						</div>

					</div>

				</div>


			</div>
		</form>

		<div id="result"></div>
	</div>
	<!-- end page content -->
	<!--<div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate" ></div>-->

</div>
<!-- end page container -->