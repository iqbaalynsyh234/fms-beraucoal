<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/popper/popper.min.js" ></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/echarts/echarts.js" ></script>


  <!--Chart JS-->
    <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/Chart.bundle.js" ></script>
    <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/utils.js" ></script>
    <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/chart/chartjs/chartjs-data.js" ></script>

<script language="javascript">
setInterval(function(){
   window.location.reload(1);
}, 30000);

function closemodalcommandcenter(){
  $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
  // $("#modallistvehicle").hide();
}

function showmodalcommandcenter(){
  $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
}

function submitcommandcenter(){
  $("#loadernya").show();
  $.post("<?php echo base_url() ?>trackingboard/submitcommand", jQuery("#frmsubmitcommand").serialize(), function(response){
    console.log("response : ", response);
      $("#loadernya").hide();
      var code   = response.code;
      var msg    = response.msg;
      $("#commandtext").val("");
        if (code == 400) {
          $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
          alert(""+msg);
        }else {
          $("#modalcommandcenter").animate({width: 'toggle'}, "slow");
          alert(""+msg);
        }
  }, "json");
}
</script>

<style media="screen">
.material-icons{
  font-size: 50px;
  padding: 10px;
}

.info-box-icon.push-bottom {
    margin-top: 5px;
}

div#modalcommandcenter {
  /* margin-top: -69%;
  margin-left: -11%; */
  height: auto;
  width: 50%;
  position: fixed;
  z-index: 9;
  /* background-color: #f1f1f1; */
  text-align: left;
  /* border: 1px solid #d3d3d3; */
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

    <div id="modalcommandcenter" style="display: none;">
      <div class="col-md-12">
        <div class="card">
        <div class="card-topline-green">
            <div class="card-head">
                <h4 id="titlemodal">Command Center</h4>
                <div class="tools" style="margin-top: -40px;">
                  <!-- <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a> -->
                  <!-- <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a> -->
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodalcommandcenter();">X</button>
                </div>
            </div>
            <div class="card-body" id="bodymodal">
              <form class="form-horizontal" name="frmsubmitcommand" id="frmsubmitcommand" onsubmit="submitcommandcenter();">
                <div class="form-group row" id="mn_vehicle">
                  <label class="col-lg-3 col-md-3 control-label">Command Text
                  </label>
                  <div class="col-lg-4 col-md-3">
                    <textarea name="commandtext" id="commandtext" rows="8" cols="60"></textarea>
                  </div>
                </div>
              </form>
              <div class="form-group row  text-right">
                <label class="col-lg-3 col-md-4 control-label"></label>
                <div class="col-md-12">
                  <img src="<?php echo base_url();?>assets/transporter/images/loader2.gif" style="display: none;" id="loadernya">
                  <button class="btn btn-circle btn-primary" id="btnsubmitcommandcenter" type="button" onclick="submitcommandcenter();"/>Submit Command</button>
                </div>
              </div>
            </div>
        </div>
      </div>
      </div>
    </div>

		<div class="page-bar">
            <div class="page-title-breadcrumb">
				<div class=" pull-left">
                   <div class="page-title">Tracking Board (Today)</div>
				</div>
                <!--<ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;</li>
					<li class="active"><a href="#">Yesterday</a> | </li>
					<li><a href="#">Last 7</a> | </li>
                     <li><a href="#">Last 30</a> </li>

                </ol>-->
             </div>
		</div>

    <button type="button" name="btncommandcenter" id="btncommandcenter" class="btn btn-circle btn-success" onclick="showmodalcommandcenter();">
      <span class="fa fa-microphone"></span>
      Command Center
    </button> <br><br>

			<div class="row">
					<div class="col-xl-4 col-md-6 col-12" style="cursor:pointer;">
			          <div class="info-box bg-blue">
			            <span class="info-box-icon push-bottom"><i class="material-icons">my_location</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">ROM/PIT</span>
			              <!--<span class="info-box-number" id="totalarmingdevice" style="font-size:12px;">&nbsp</span>-->
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
							<span class="progress-description" style="font-size:15px;">
							 <?=$total['rom_location'];?>
							</span>
			            </div>
			          </div>
			        </div>
					<div class="col-xl-4 col-md-6 col-12" style="cursor:pointer;">
			          <div class="info-box bg-success">
			            <span class="info-box-icon push-bottom"><i class="material-icons">directions_boat</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">PORT</span>
			              <!--<span class="info-box-number" id="totalarmingdevice" style="font-size:12px;">&nbsp</span>-->
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
							<span class="progress-description" style="font-size:15px;">
							 <?=$total['port_location'];?>
							</span>
			            </div>
			          </div>
			        </div>

			        <div class="col-xl-4 col-md-6 col-12" style="cursor:pointer;">
			          <div class="info-box bg-warning">
			            <span class="info-box-icon push-bottom"><i class="material-icons">local_movies</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Jembatan Timbang</span>
			              <!--<span class="info-box-number" style="font-size:12px;">&nbsp</span>-->
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:15px;">
						   <?=$total['timbangan_location'];?>
						  </span>
			            </div>
			          </div>
			        </div>

			        <div class="col-xl-4 col-md-6 col-12" style="cursor:pointer;">
			          <div class="info-box bg-warning">
			            <span class="info-box-icon push-bottom"><i class="material-icons">layers</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Jalur Hauling</span>
			              <!--<span class="info-box-number" id="totaldisarmingdevice" style="font-size:12px;">&nbsp</span>-->
			              <!-- <div class="progress">
			                <div class="progress-bar width-40"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:15px;">
							  <?=$total['hauling_location'];?>
						  </span>
			            </div>
			          </div>
			        </div>
					<div class="col-xl-4 col-md-6 col-12" style="cursor:pointer;">
			          <div class="info-box bg-danger">
			            <span class="info-box-icon push-bottom"><i class="material-icons">store_mall_directory</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Pool/Workshop</span>
			              <!--<span class="info-box-number" id="totalrfidregdevice" style="font-size:12px;">&nbsp</span>-->
			              <!-- <div class="progress">
			                <div class="progress-bar width-80"></div>
			              </div> -->
							<span class="progress-description" style="font-size:15px;">
							  <?=$total['pool_location'];?>
						  </span>
			            </div>
			          </div>
			        </div>

			        <div class="col-xl-4 col-md-6 col-12" style="cursor:pointer;">
			          <div class="info-box bg-blue">
			            <span class="info-box-icon push-bottom"><i class="material-icons">layers_clear</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Luar Hauling</span>
			              <!--<span class="info-box-number" id="totalrfidregdevice" style="font-size:12px;">&nbsp</span>-->
			              <!-- <div class="progress">
			                <div class="progress-bar width-80"></div>
			              </div> -->
							<span class="progress-description" style="font-size:15px;">
							  <?=$total['out_location'];?>
						  </span>
			            </div>
			          </div>
			        </div>

            </div>

			<div class="row">
				<div class="col-md-12" id="tablecustomer">
					<div class="panel" id="panel_form">
					<header class="panel-heading panel-heading-red">List Vehicle</header>
						<div class="panel-body" id="bar-parent10">
							<table id="example1" class="table table-striped">
							<thead>
								<tr>
									<th style="text-align:center;" width="2%">No</th>
									<th style="text-align:center;" width="10%">Vehicle</th>
									<th style="text-align:center;" width="5%">Engine</th>
									<th style="text-align:center;" width="10%">GPS Time</th>
									<th style="text-align:center;" width="5%">Speed (Kph)</th>
									<th style="text-align:center;" width="10%">Location</th>
									<th style="text-align:center;" width="7%">Coordinate</th>
								</tr>
							</thead>
							<tbody>
							  <?php for($i=0;$i<count($data);$i++) { ?>
										<tr>
											<td style="text-align:center;font-size:12px;"><?=$i+1?></td>
											<td style="text-align:center;font-size:12px;"><?=$data[$i]['vehicleno'];?></td>
											<td style="text-align:center;font-size:12px;"><?=$data[$i]['engine'];?></td>
											<td style="text-align:center;font-size:12px;"><?=date("d-m-Y H:i:s", strtotime($data[$i]['gpstime']));?></td>
											<td style="text-align:center;font-size:12px;"><?=round($data[$i]['speed'],0);?></td>
											<td style="text-align:center;font-size:12px;"><?=$data[$i]['location'];?></td>
											<td style="text-align:center;font-size:12px;"><?=$data[$i]['coord'];?></td>
										</tr>
							  <?php } ?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>


</div>
