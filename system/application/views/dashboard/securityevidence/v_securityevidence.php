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

  /* #mydivheader {
    padding: 10px;
    cursor: move;
    z-index: 10;
    background-color: #2196F3;
    color: #fff;
  } */
</style>
<div class="sidebar-container">
  <?=$sidebar;?>
</div>

<div class="page-content-wrapper">
  <div class="page-content" id="page-content-new">
    <div class="row">
      <div class="col-md-12 col-sm-12" id="reportfilter">

        <!-- MODAL LIST VEHICLE -->
        <div id="modalinformationdetail" style="display: none;">
          <div id="mydivheader"></div>
          <div id="contentinformationdetail">

          </div>
        </div>

        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-blue">Security Evidence</header>
          <div class="panel-body" id="bar-parent10">
            <form class="form-horizontal" name="frmsearch" id="frmsearch" onsubmit="frmsearch_onsubmit();">
              <input type="text" name="alarmfix" id="alarmfix" hidden>

              <div class="form-group row">
                <label class="col-lg-3 col-md-4 control-label">Vehicle
                </label>
                <div class="col-lg-9 col-md-8">
                    <select id="vehicle" name="vehicle" class="form-control select2" tabindex="-1" aria-hidden="true">
                      <option value="all">--All Vehicle</option>
                    <?php foreach ($data as $rowvehicle) {?>
                      <option value="<?php echo $rowvehicle['vehicle_mv03'] ?>"><?php echo $rowvehicle['vehicle_no'].' '.$rowvehicle['vehicle_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
            </div>

              
										<div class="form-group row" id="mn_vehicle" >
											<label class="col-lg-3 col-md-3 control-label">Type
                                            </label>
                                            <div class="col-lg-4 col-md-3">
                                                <select id="reporttype" name="reporttype" class="form-control select2" >
													<option value="0">Reguler</option>

												</select>
                                            </div>

                                        </div>
										<div class="form-group row" id="mn_vehicle" >
											<label class="col-lg-3 col-md-3 control-label">KM
                                            </label>
                                            <div class="col-lg-4 col-md-3">
                                                 <input class="form-control" size="5" type="text" name="km" id="km" value="" placeholder="ex: 5 / 6.5">
                                            </div>
										 </div>

										<div class="form-group row" id="mn_vehicle" >
											<label class="col-lg-3 col-md-3 control-label">Periode
                                            </label>
                                            <div class="col-lg-4 col-md-3">
                                                <select id="periode" name="periode" id="periode" class="form-control select2" onchange="javascript:periode_onchange()" >
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
                                                <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('d-m-Y',strtotime("yesterday") )?>">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            <input type="hidden" id="dtp_input2" value="" />

											<div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                                <input class="form-control" size="5" type="text" readonly id="shour" name="shour" value="<?=date("H:i",strtotime("00:00:00"))?>">
                                                <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                                            </div>
											<input type="hidden" id="dtp_input3" value="" />
										</div>

										<div class="form-group row" id="mn_edate" style="display:none">
											<label class="col-lg-3 col-md-4 control-label">End Date
                                            </label>
											<div class="input-group date form_date col-md-4" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                                <input class="form-control" size="5" type="text" readonly name="enddate" id="enddate" value="<?=date('d-m-Y',strtotime("yesterday") )?>">
                                                <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                            </div>
                                            <input type="hidden" id="dtp_input2" value="" />
                                            <div class="input-group date form_time col-md-2" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                                <input class="form-control" size="5" type="text" readonly id="ehour" name="ehour" value="<?=date("H:i",strtotime("23:59:59"))?>">
                                                <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                                            </div>
                                            <input type="hidden" id="dtp_input3" value="" />
                                        </div>

              <!--<div class="form-group row">
                  <label class="col-lg-3 col-md-4 control-label">Alarm Category</label>
                  <div class="col-lg-4 col-md-4">
                      <select id="alarmcategory" name="alarmcategory" class="form-control select2" onchange="getalarmsubcategory();">
                        <option value="All">All</option>
                        <?php foreach ($alarmcategory as $rowalarmcat) {?>
                            <option value="<?php echo $rowalarmcat['webtracking_alarmcategory_id'] ?>"><?php echo $rowalarmcat['webtracking_alarmcategory_name'] ?></option>
                        <?php } ?>
                      </select>
                  </div>
              </div>

              <div id="thisissubcategoryview"></div>-->

				<div class="form-group row">
                  <label class="col-lg-3 col-md-4 control-label">Alarm Type</label>
                  <div class="col-lg-4 col-md-4">
                      <select id="alarmtype" name="alarmtype" class="form-control select2-multiple" multiple required>
                        <option value="All">All</option>
                        <?php foreach ($alarmtype as $rowalarmtype) {?>
                            <option value="<?php echo $rowalarmtype['alarm_type'] ?>"><?php echo $rowalarmtype['alarm_name'] ?></option>
                        <?php } ?>
                      </select>
                  </div>
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

      <div class="col-md-12" id="resultreport" style="display:none;">

      </div>

    </div>
    <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
    <div id="result" style="width:100%"></div>
  </div>
</div>

<script type="text/javascript">
  function frmsearch_onsubmit(){
    $("#resultreport").hide();
    var alarmtype = $("#alarmtype").val();
    $("#alarmfix").val(alarmtype);
    // console.log("alarmtype : ", alarmtype);
    $("#loadernya").show();
    $.post("<?php echo base_url() ?>securityevidence/searchreport", jQuery("#frmsearch").serialize(), function(response){
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

  function getalarmsubcategory(){
    var categoryid = $("#alarmcategory").val();
    var data       = {id: categoryid};
    console.log("categoryid : ", categoryid);
    $.post("<?php echo base_url() ?>securityevidence/getalarmsubcat", data, function(response){
      console.log("response : ", response);
      // $("#thisissubcategoryview").html(response.html);
      var html = "";
        html += '<div class="form-group row">';
            html += '<label class="col-lg-3 col-md-4 control-label">Alarm Sub Category</label>';
            html += '<div class="col-lg-4 col-md-4">';
                html += '<select id="alarmsubcategory" name="alarmsubcategory" class="form-control select2" onchange="getalarmchild();">';
                  html += '<option value="All">All</option>';
                    for (var i = 0; i < response.alarmsubcategory.length; i++) {
                      html += '<option value="'+response.alarmsubcategory[i].webtracking_alarmsubcategory_id+'">'+response.alarmsubcategory[i].webtracking_alarmsubcategory_name+'</option>';
                    }
                html += '</select>';
            html += '</div>';
        html += '</div>';
        $("#thisissubcategoryview").html(html);
    }, "json");
  }

  function getdetailinfo(id){
	 $("#loadernya").show();
    var data = id.split(",");
    console.log("id detail : ", data[0]);
    console.log("sdate detail : ", data[1]);

    $.post("<?php echo base_url() ?>securityevidence/getinfodetail", {alert_id : data[0], sdate : data[1]}, function(response){
	  $("#loadernya").hide();
      console.log("response getdetailinfo: ", response);
      $("#contentinformationdetail").html(response.html);
      $("#modalinformationdetail").show();
    }, "json");
  }

  function closemodallistofvehicle(){
    $("#modalinformationdetail").hide();
  }

  // dragElement(document.getElementById("modalinformationdetail"));

  // function dragElement(elmnt) {
  //   var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  //   if (document.getElementById(elmnt.id + "header")) {
  //     // if present, the header is where you move the DIV from:
  //     document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  //   } else {
  //     // otherwise, move the DIV from anywhere inside the DIV:
  //     elmnt.onmousedown = dragMouseDown;
  //   }
  //
  //   function dragMouseDown(e) {
  //     e = e || window.event;
  //     e.preventDefault();
  //     // get the mouse cursor position at startup:
  //     pos3 = e.clientX;
  //     pos4 = e.clientY;
  //     document.onmouseup = closeDragElement;
  //     // call a function whenever the cursor moves:
  //     document.onmousemove = elementDrag;
  //   }
  //
  //   function elementDrag(e) {
  //     e = e || window.event;
  //     e.preventDefault();
  //     // calculate the new cursor position:
  //     pos1 = pos3 - e.clientX;
  //     pos2 = pos4 - e.clientY;
  //     pos3 = e.clientX;
  //     pos4 = e.clientY;
  //     // set the element's new position:
  //     elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
  //     elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  //   }
  //
  //   function closeDragElement() {
  //     // stop moving when mouse button is released:
  //     document.onmouseup = null;
  //     document.onmousemove = null;
  //   }
  // }

	function houronclick(){
		console.log("ok");
		$(".switch").html("<?php echo date("Y F d")?>");
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

	function option_form(v)
		{
			switch(v)
			{
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
