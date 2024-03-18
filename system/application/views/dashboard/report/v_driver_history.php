<script>
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
		jQuery("#loader").show();

		var vehicle   = jQuery("#vehicle").val();
		var driver    = jQuery("#driver").val();
		var startdate = jQuery("#startdate").val();
		var shour     = jQuery("#shour").val();
		var enddate   = jQuery("#enddate").val();
		var ehour     = jQuery("#ehour").val();

		var data = {
			vehicle				: vehicle,
			driver				: driver,
			startdate			: startdate,
			shour					: shour,
			enddate				: enddate,
			ehour					: ehour
		};

		console.log("Ini Data yg dikiim : ", data);

		jQuery.post("<?=base_url();?>driverhistory/driver_hist_report/", data, function(r){
				console.log("respon : ", r);
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);
			}, "json");
	}

	function frmsearch_onsubmit()
	{
		jQuery("#loader").show();
		page(0);
		return false;
	}

	function excel_onsubmit(){
		jQuery("#loader2").show();

		jQuery.post("<?=base_url();?>report/driver_hist_report_excel/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
				if(r.success == true){
					jQuery("#frmreq").attr("src", r.filename);
				}else{
					alert(r.errMsg);
				}
			}
			, "json"
		);

		return false;
	}

</script>

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
          <header class="panel-heading panel-heading-blue">Driver History</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        			<input type="hidden" name="offset" id="offset" value="" />
        			<input type="hidden" id="sortby" name="sortby" value="" />
        			<input type="hidden" id="orderby" name="orderby" value="" />

							<table class="table">
								<tr>
									<td>Vehicle</td>
									<td>
										<select id="vehicle" name="vehicle" class="form-control select2">
        							<option value="0">All</option>
        							<?php for($i=0; $i < count($vehicles); $i++) { ?>
        							<option value="<?php echo $vehicles[$i]->vehicle_id; ?>"><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." - "; } ?><?php echo $vehicles[$i]->vehicle_name; ?> - <?php echo $vehicles[$i]->vehicle_no; ?></option>
        							<?php } ?>
        						</select>
									</td>
								</tr>

								<tr>
									<td>Driver</td>
									<td>
										<select id="driver" name="driver" class="form-control select2">
											<option value="0">All</option>
											<?php for($i=0; $i < count($drivers); $i++) { ?>
											<option value="<?php echo $drivers[$i]->driver_id; ?>"><?php echo $drivers[$i]->driver_name;?></option>
											<?php } ?>
										</select>
									</td>
								</tr>

								<tr>
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
								</tr>

								<tr>
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
								</tr>
							</table>
								<div class="text-right">
                  <button class="btn btn-success btn-circle" id="btnsearchreport" type="submit"/>Search</button>
                  <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
								</div>
        		</form>
          </div>

        </div>

      </div>

    </div>
    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
    <div id="result" style="width:100%"></div>
  </div>
  <!-- end page content -->

</div>
<!-- end page container -->

<script type="text/javascript">
	function houronclick(){
		console.log("ok");
		$(".switch").html("<?php echo date("Y F d")?>");
	}
</script>
