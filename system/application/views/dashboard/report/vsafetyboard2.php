<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/popper/popper.min.js" ></script>
<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo base_url();?>assets/dashboard/assets/plugins/echarts/echarts.js" ></script>


  <!--Chart JS-->
    <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/Chart.bundle.js" ></script>
    <script src="<?php echo base_url();?>assets/dashboard/assets/plugins/chart-js/utils.js" ></script>
    <script src="<?php echo base_url();?>assets/dashboard/assets/js/pages/chart/chartjs/chartjs-data.js" ></script>


<script>
jQuery(document).ready(function() {
  var dataadas           = '<?php echo json_encode($adas); ?>';
  var datadsm            = '<?php echo json_encode($dsm); ?>';

  var objadas            = JSON.parse(dataadas);
  var objdsm             = JSON.parse(datadsm);

  var periode            = "Yesterday";
  var dataarrayadas      = [];
  var dataarraydsm       = [];
  var dataarrayalarmname = [];

  for (var i = 0; i < objadas.length; i++) {
    dataarrayadas.push(objadas[i].jumlah);
    dataarrayalarmname.push(objadas[i].report_name);
  }

  for (var i = 0; i < objdsm.length; i++) {
    dataarraydsm.push(objdsm[i].jumlah);
    dataarrayalarmname.push(objdsm[i].report_name);
  }

  console.log("objadas : ", objadas);
  console.log("objdsm : ", objdsm);
  console.log("dataarrayadas : ", dataarrayadas);
  console.log("dataarraydsm : ", dataarraydsm);
  console.log("dataarrayalarmname : ", dataarrayalarmname);


    'use strict';
    require.config({
        paths: {
            //echarts: "../light/assets/plugins/echarts"
  			echarts: "<?php echo base_url();?>assets/dashboard/assets/plugins/echarts"
        }
    }), require(["echarts", "echarts/chart/bar", "echarts/chart/chord", "echarts/chart/eventRiver", "echarts/chart/force", "echarts/chart/funnel", "echarts/chart/gauge", "echarts/chart/heatmap", "echarts/chart/k", "echarts/chart/line", "echarts/chart/map", "echarts/chart/pie", "echarts/chart/radar", "echarts/chart/scatter", "echarts/chart/tree", "echarts/chart/treemap", "echarts/chart/venn", "echarts/chart/wordCloud"], function(a) {
        var b = a.init(document.getElementById("echarts_bar"));
        b.setOption({
            tooltip: {
                trigger: "axis"
            },
            legend: {
                data: ["ADAS", "DSM"]
            },
            toolbox: {
                show: !0,
                orient: "vertical",
                feature: {
                    mark: {
                        show: !0
                    },
                    dataView: {
                        show: !0,
                        readOnly: !1
                    },
                    magicType: {
                        show: !0,
                        type: ["line", "bar"]
                    },
                    restore: {
                        show: !0
                    },
                    saveAsImage: {
                        show: !0
                    }
                }
            },
            calculable: !0,
            xAxis: [{
                type: "category",
                data: dataarrayalarmname,
                show: false
            }],
            yAxis: [{
                type: "value",
                splitArea: {
                    show: !0
                }
            }],
            series: [{
                name: "ADAS",
                type: "bar",
                data: dataarrayadas
            }, {
                name: "DSM",
                type: "bar",
                data: dataarraydsm
      			}]
        });

        var f = a.init(document.getElementById("echarts_pie"));
        f.setOption({
            tooltip: {
                trigger: "item",
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: "vertical",
                x: "left",
                data: ["B1111ABC", "B7777CBA", "B9999RTA", "B8888THA", "B4444UGH"]
            },
            toolbox: {
                show: !0,
                orient: "vertical",
                feature: {
                    mark: {
                        show: !0
                    },
                    dataView: {
                        show: !0,
                        readOnly: !1
                    },
                    magicType: {
                        show: !0,
                        type: ["pie", "funnel"],
                        option: {
                            funnel: {
                                x: "25%",
                                width: "50%",
                                funnelAlign: "left",
                                max: 1548
                            }
                        }
                    },
                    restore: {
                        show: !0
                    },
                    saveAsImage: {
                        show: !0
                    }
                }
            },
            calculable: !0,
            series: [{
                name: "pie_chart",
                type: "pie",
                radius: "55%",
                center: ["50%", "60%"],
                data: [{
                    value: 119,
                    name: "B1111ABC"
                }, {
                    value: 178,
                    name: "B7777CBA"
                }, {
                    value: 134,
                    name: "B9999RTA"
                }, {
                    value: 150,
                    name: "B8888THA"
                }, {
                    value: 133,
                    name: "B4444UGH"
				}]
            }]
        })
    })
});

</script>


<style media="screen">
.material-icons{
  font-size: 50px;
  padding: 10px;
}

.info-box-icon.push-bottom {
    margin-top: 8px;
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
		<div class="page-bar">
            <div class="page-title-breadcrumb">
				<div class=" pull-left">
                   <div class="page-title">Safety Board (Yesterday)</div>
				</div>
                <ol class="breadcrumb page-breadcrumb pull-right">
                    <li><i class="fa fa-home"></i>&nbsp;</li>
					<li class="active"><a href="#">Yesterday</a> | </li>
					<li><a href="#">Last 7</a> | </li>
                     <li><a href="#">Last 30</a> </li>

                </ol>
             </div>
		</div>

			<div class="row">
					<div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="armingfeature();">
			          <div class="info-box bg-success">
			            <span class="info-box-icon push-bottom"><i class="material-icons">directions_car</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Total Vehicle</span>
			              <span class="info-box-number" id="totalarmingdevice" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
							<span class="progress-description" style="font-size:12px;">
							 999
							</span>
			            </div>
			          </div>
			        </div>

			        <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="disarmingfeature();">
			          <div class="info-box bg-warning">
			            <span class="info-box-icon push-bottom"><i class="material-icons">warning</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">Overspeed Alarm</span>
			              <span class="info-box-number" id="totaldisarmingdevice" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-40"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
							999
						  </span>
			            </div>
			          </div>
			        </div>

			        <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="rfidregfeature();">
			          <div class="info-box bg-blue">
			            <span class="info-box-icon push-bottom"><i class="material-icons">notifications_active</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text" >DSM Alarm</span>
			              <span class="info-box-number" id="totalrfidregdevice" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-80"></div>
			              </div> -->
							<span class="progress-description" style="font-size:12px;">
							 999
						  </span>
			            </div>
			          </div>
			        </div>

			        <div class="col-xl-3 col-md-6 col-12" style="cursor:pointer;" onclick="rfidcheckfeature();">
			          <div class="info-box bg-danger">
			            <span class="info-box-icon push-bottom"><i class="material-icons">track_changes</i></span>
			            <div class="info-box-content">
			              <span class="info-box-text">ADAS Alarm</span>
			              <span class="info-box-number" style="font-size:12px;">&nbsp</span>
			              <!-- <div class="progress">
			                <div class="progress-bar width-60"></div>
			              </div> -->
			              <span class="progress-description" style="font-size:12px;">
						   999
						  </span>
			            </div>
			          </div>
			        </div>
            </div>

			<div class="row">
				<div class="col-md-12">

			<!-- bar chart start -->
			<div class="row">
				<div class="col-md-6">
					<div class="card card-topline-red">
						<div class="card-head">
						<header>DSM/ADAS CHART</header>
							<div class="tools">
								<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
								<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
								<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
							</div>
						</div>
						<div class="card-body no-padding height-9">
							<div class="row">
								<div id="echarts_bar" class="chart-window"></div>
							</div>
						</div>
					</div>

				</div>
				  <div class="col-md-6">
                            <div class="card card-topline-red">
                                <div class="card-head">
                                    <header>Top 5 Overspeed</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body no-padding ">
                                    <div class="row">
                                        <div id="echarts_pie" class="chart-window"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

			</div>


			</div>

     </div>


</div>
