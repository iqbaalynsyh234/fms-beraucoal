<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/popper/popper.min.js" ></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>

    <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/Chart.bundle.js" ></script>
    <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/utils.js" ></script>
    <!--<script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/chart/chartjs/chartjs-data.js" ></script>-->
	<!--<script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/chart/chartjs/home-data2.js" ></script>-->

	<script>
	$(document).ready(function() {

	var datavalue =  '<?php echo json_encode($content_data); ?>';
	var datavalue_fix = JSON.parse(datavalue);
	console.log(datavalue_fix);
	var datatime =  '<?php echo json_encode($content_time); ?>';
	var datatime_fix = JSON.parse(datatime);
	console.log(datatime_fix);
	var datalabel =  '<?php echo json_encode($content_label); ?>';
	var datalabel_fix = JSON.parse(datalabel);

	var datalabel2 =  '<?php echo json_encode($content_label2); ?>';
	var datalabel_fix2 = JSON.parse(datalabel2);

	var datavehicle = '<?php echo $vehicle_no;?>';
	var dataperiode = '<?php echo $vehicle_no.' Periode '.$start_date." s/d ".$end_date;?>';

	var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var config = {
        type: 'line',
        data: {
            //labels: ["KM 1", "Km 2", "Km 3", "Km 4", "Km 5", "Km 6", "Km9"],
			labels: datalabel_fix,
            datasets: [{
                label: "Fuel (Liter)",
                backgroundColor: window.chartColors.blue,
                borderColor: window.chartColors.blue,
                /* data: [
                    99,
                    89,
                    79,
                    69,
                    59,
                    49,
                    39
                ], */

				data: datavalue_fix,
				fill: false,
            } /* , {
                label: "Time",
                fill: false,
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: datatime_fix,
            }  */

			]
        },
        options: {
            responsive: true,
            title:{
                display:true,
                text:dataperiode
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Location'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Liter'
                    }
                }]
            }
        }
    };
    var ctx = document.getElementById("chartjs_line").getContext("2d");
    window.myLine = new Chart(ctx, config);
	});
	</script>

  <script src="<?php echo base_url();?>assets/js/jsblong/jquery.table2excel.js"></script>
  <script>
  jQuery(document).ready(
  		function()
  		{
  			jQuery("#export_xcel").click(function()
  			{
  				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
  			});
  		}
  	);
  </script>


<!-- start page container -->
        <div class="page-container">
 			<!-- start sidebar menu -->

			<!-- start sidebar menu -->
			<div class="sidebar-container">
			  <?=$sidebar;?>
			</div>
			<!-- end sidebar menu -->

			 <!-- end sidebar menu -->
			<!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                   <!-- <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">ChartJs</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="index.html">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li><a class="parent-item" href="">Charts</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">ChartJs</li>
                            </ol>
                        </div>
                    </div>-->

                    <!-- start chart -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-topline-red">
                                <div class="card-head">
                                    <header>Fuel Consumption Chart</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
	                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
	                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body " id="chartjs_line_parent">
                                    <div class="row">
                                        <canvas id="chartjs_line"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-topline-red">
                                <div class="card-head">
                                    <header>TABLE DETAIL</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
	                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
	                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body " id="chartjs_line_parent">
                                  <div class="col-lg-4 col-sm-4">
                                    <a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
                                  </div>

                                  <div id="isexport_xcel">
                                  <table class="table table-striped custom-table table-hover">
                                    <thead>
                                      <tr>
                                        <th style="text-align:center;" width="3%">No</th>
                                        <th style="text-align:center;" width="10%">Vehicle</th>
                                        <th style="text-align:center;" width="10%">GPS Date</th>
                                        <th style="text-align:center;" width="7%">GPS Time</th>
                                        <th style="text-align:center;" width="7%">GPS Speed (kph)</th>
                                        <th style="text-align:center;" width="7%">Status</th>
                                        <th style="text-align:center;" width="10%">Location</th>
                                        <th style="text-align:center;" width="4%">Jalur</th>
                                        <th style="text-align:center;" width="5%">Coordinate</th>
                                        <th style="text-align:center;" width="5%">Odometer (KM)</th>
                                        <th style="text-align:center;" width="7%">Fuel (L)</th>
                                        <th style="text-align:center;" width="7%">Fuel (%)</th>

                                      </tr>
                                    </thead>
                                    <tbody>


                       <?php
                        if(isset($dataforexcel) && (count($dataforexcel) > 0)){
                          $j = 0;
                            $jkm = 0;
                            for ($i=0;$i<count($dataforexcel);$i++)
                            {
                              $ad1_volt = $dataforexcel[$i]['location_report_fuel_data'];

                              //ultrasonic fuel
                              $fullcap     = 200; // liter
                              $fullpercent = 100; // percentage
                              $fullvolt		 = 3.54;
                              $currentvolt = $ad1_volt;

                              $percenvoltase   = $currentvolt * ($fullpercent / $fullvolt); // persentase yg didapat dari perubahan voltase;
                              $sisaliterbensin = ($percenvoltase * $fullcap) / $fullpercent;

                              $percenvoltase = $percenvoltase;
                              $sisaliterbensin = $sisaliterbensin;
                            ?>
                            <tr>
                              <td style="text-align:center;font-size:12px;"><?php echo $i+1;?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo $dataforexcel[$i]['location_report_vehicle_no'];?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo date("d-m-Y",strtotime($dataforexcel[$i]['location_report_gps_time']));?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo date("H:i:s",strtotime($dataforexcel[$i]['location_report_gps_time']));?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo $dataforexcel[$i]['location_report_speed'];?></td>
                              <td style="text-align:center;font-size:12px;">
                                <?php
                                  if($dataforexcel[$i]['location_report_name'] == "location_idle"){
                                    $status_name = "IDLE";
                                  }else if($dataforexcel[$i]['location_report_name'] == "location_off"){
                                    $status_name = "OFF";
                                  }else{
                                    $status_name = "MOVE";
                                  }
                                ?>
                                <?php echo $status_name; ?>
                              </td>
                              <td style="text-align:center;font-size:12px;"><?php echo $dataforexcel[$i]['location_report_location'];?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo $dataforexcel[$i]['location_report_jalur'];?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo $dataforexcel[$i]['location_report_coordinate'];?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo round(($dataforexcel[$i]['location_report_odometer']/1000),0, PHP_ROUND_HALF_DOWN);?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo str_replace(".", ",", $sisaliterbensin);?></td>
                              <td style="text-align:center;font-size:12px;"><?php echo str_replace(".", ",", $percenvoltase);?></td>

                            </tr>
                        <?php
                            }

                        }else{
                      ?>
                            <tr>
                              <td colspan="10">No Available Data</td>
                        </tr>
                      <?php
                        }
                      ?>
                                    </tbody>

                                  </table>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
            <!-- end page content -->

        </div>
        <!-- end page container -->
