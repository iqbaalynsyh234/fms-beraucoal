<style media="screen">
  div#modalinformationdetail {
    /* margin-top: 5%; */
    /* margin-left: 20%; */
    max-height: 500px;
    width: 65%;
    overflow-x: auto;
    position: fixed;
    z-index: 9;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
  }

  #data-wim{
    background-color: #1f50a2;
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
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red" id="data-wim">DATA WIM - DEVELOPMENT</header>
          <div class="panel-body" id="bar-parent10">
            <input type="text" id="showhidetable" value="0" hidden>
            <form class="form-horizontal form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        			<input type="hidden" name="offset" id="offset" value=""/>
        			<input type="hidden" id="sortby" name="sortby" value=""/>
        			<input type="hidden" id="orderby" name="orderby" value=""/>
              <input type="hidden" id="first_load" name="first_load" value="1"/>

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
											echo "<option value='" . $vehicles[$i]->vehicle_no . "' " . $selected . ">" . $vehicles[$i]->vehicle_no . " - " . $vehicles[$i]->vehicle_name . "</option>";
										}
										?>

									</select>
								</div>
							</div>

              <div class="form-group row" id="mn_vehicle">
								<label class="col-lg-3 col-md-3 control-label">Transaction ID
								</label>
								<div class="col-lg-4 col-md-3">
									<input type="number" name="transactionid" id="transactionid" class="form-control">
                  <div id="showhideselect" style="display:none;">
                    <select class="form-control select2" multiple="multiple" name="transactionid_select[]" id="transactionid_select"></select>
                  </div>
                  <!-- <p style="color:red; font-size: 11px;">
                    <i>*Gunakan koma sebagai pembatas untuk mencari lebih dari satu Transaction ID</i>
                  </p> -->
								</div>
							</div>

                <div class="form-group row" id="mn_vehicle">
                  <label class="col-lg-3 col-md-3 control-label">Status
                  </label>
                  <div class="col-lg-4 col-md-3">
                    <select id="statuswim" name="statuswim" class="form-control select2">
                      <option value="all">All</option>
                      <option value="0">Unprocess</option>
                      <option value="1">Updated By Operator</option>
                      <option value="2">Updated By Admin</option>
                      <option value="3">Rejected</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row" id="mn_vehicle">
                  <label class="col-lg-3 col-md-3 control-label">Mode
                  </label>
                  <div class="col-lg-4 col-md-3">
                    <select id="modewim" name="modewim" class="form-control select2" onchange="javascript:periode_onchange()">
                      <option value="all">All</option>
                      <option value="ACTUAL">ACTUAL</option>
                      <option value="AVERAGE WIM">AVERAGE WIM</option>
                      <option value="AVERAGE FMS">AVERAGE FMS</option>
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

								<!-- <tr>
									<td>Start Date</td>
									<td>
										<div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="10" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="hidden" id="dtp_input2" value="" class="form-control"/>
											</div>
										<div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
	                   <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?=date("H:i",strtotime("00:00:00"))?>" onclick="houronclick();">
	                   <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
	                  <input type="hidden" id="dtp_input3" value="" class="form-control"/>
									</div>
									</td>
								</tr> -->

								<!-- <tr>
									<td>End Date</td>
									<td>
										<div class="input-group date form_date col-md-6" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                    <input type="hidden" id="dtp_input2" value="" />
										<div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
		                 <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?=date("H:i",strtotime("23:59:00"))?>" onclick="houronclick();">
		                 <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
		                </div>
		                <input type="hidden" id="dtp_input3" value="" />
									</td>
								</tr> -->
								<div class="text-right">
                  <button class="btn btn-success btn-circle" id="btnsearchreport" type="submit"/>Search</button>
                  <!--<img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />-->
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

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>
	$(document).ready(function() {
		page(0);
		//frmsearch_onsubmit();
    // setInterval(function () {
    //   page(0);
    // }, 60000);
  });

  var intervalpage = setInterval(page, 15000);

	 function page(p) {
		if (p == undefined) {
			p = 0;
		}

		jQuery("#offset").val(p);
		//jQuery("#result").html('<img src="<?php echo base_url(); ?>assets/transporter/images/loader2.gif">');
		jQuery("#loader2").show();
		jQuery.post("<?= base_url(); ?>wim/search_report", jQuery("#frmsearch").serialize(),
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

  // SUGESTION FOR MULTIPLE SEARCH TRANSACTION ID
  $("#transactionid").on("keydown", function(){
    var transactionid = $("#transactionid").val();
    console.log("transactionid : ", transactionid);

      if (transactionid.length >= 2) {
        $("#showhideselect").show();
        $.post("<?php echo base_url() ?>wim/getTransactionID", {transactionid : transactionid}, function(response){
          console.log("response getTransactionID : ", response.data[0].integrationwim_transactionID);

          var sizedata = response.data.length;
          var data     = response.data;

          var html = "";
            for (var i = 0; i < sizedata; i++) {
              html += '<option value="'+data[i].integrationwim_transactionID+'">'+data[i].integrationwim_transactionID+'</option>';
            }

          $("#transactionid_select").html(html);
        }, "json");
      }else {
        $("#showhideselect").hide();
      }
  });

	/* function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#loader2").show();
		jQuery("#offset").val(p);
		<!--jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');-->


		var vehicle   = jQuery("#vehicle").val();
		var startdate = jQuery("#startdate").val();
		var shour     = jQuery("#shour").val();
		var enddate   = jQuery("#enddate").val();
		var ehour     = jQuery("#ehour").val();
		var periode   = jQuery("#periode").val();
		var statuswim   = jQuery("#statuswim").val();

		var data = {
			vehicle				  : vehicle,
			startdate			  : startdate,
			shour					  : shour,
			enddate				  : enddate,
			ehour					  : ehour,
		periode					: periode,
		statuswim					: statuswim
		};

		console.log("Ini Data yg dikiim : ", data);

		jQuery.post("<?=base_url();?>wim/search_report/", data, function(r){
				console.log("respon : ", r);
				jQuery("#loader2").hide();
				jQuery("#result").html(r.html);
			}, "json");
	} */

	function frmsearch_onsubmit()
	{
    $("#first_load").val(0);
		jQuery("#loader2").show();
		page(0);
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

			var site = "<?= base_url() ?>wim/get_vehicle_by_company_with_numberorder/" + data_company;
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
