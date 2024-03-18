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
</style>
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
		var startdate = jQuery("#startdate").val();
		var shour     = jQuery("#shour").val();
		var enddate   = jQuery("#enddate").val();
		var ehour     = jQuery("#ehour").val();

		var data = {
			vehicle				: vehicle,
			startdate			: startdate,
			shour					: shour,
			enddate				: enddate,
			ehour					: ehour
		};

		console.log("Ini Data yg dikiim : ", data);

		jQuery.post("<?=base_url();?>wimreport/search_report/", data, function(r){
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
          <header class="panel-heading panel-heading-blue">WIM Report</header>
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
                      <option value="all">All</option>
        							<option value="BCL-101">BCL-101</option>
                      <option value="BKA 101">BKA 101</option>
                      <option value="BSL 201">BSL 201</option>
                      <option value="DT 01">DT 01</option>
                      <option value="EST 002">EST 002</option>
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
