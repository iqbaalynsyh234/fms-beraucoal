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
	var dataname =  '<?php echo json_encode($content_name); ?>';
	var dataname_fix = JSON.parse(dataname);
	
	var data =  '<?php echo json_encode($content); ?>';
	var data_fix = JSON.parse(data);
	
	var dataname_muatan =  '<?php echo json_encode($content_name_muatan); ?>';
	var dataname_fix_muatan = JSON.parse(dataname_muatan);
	
	var data_muatan =  '<?php echo json_encode($content_muatan); ?>';
	var data_fix_muatan = JSON.parse(data_muatan);
	
    'use strict';
    require.config({
        paths: {
            //echarts: "../light/assets/plugins/echarts"
  			echarts: "<?php echo base_url();?>assets/dashboard/assets/plugins/echarts"
        }
    }), require(["echarts", "echarts/chart/bar", "echarts/chart/chord", "echarts/chart/eventRiver", "echarts/chart/force", "echarts/chart/funnel", "echarts/chart/gauge", "echarts/chart/heatmap", "echarts/chart/k", "echarts/chart/line", "echarts/chart/map", "echarts/chart/pie", "echarts/chart/radar", "echarts/chart/scatter", "echarts/chart/tree", "echarts/chart/treemap", "echarts/chart/venn", "echarts/chart/wordCloud"], function(a) {
       

        var f = a.init(document.getElementById("echarts_pie"));
        f.setOption({
            tooltip: {
                trigger: "item",
                //formatter: "{a} <br/>{b} : {c} ({d}%)"
				formatter: "{b} : ({d}%)"
            },
            legend: {
                orient: "vertical",
                x: "left",
                //data: ["B1111ABC", "B7777CBA", "B9999RTA", "B8888THA", "B4444UGH"]
				data: dataname_fix
				
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
                                max: 2000
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
				data: data_fix
			 
				/*data: [{
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
				*/
				
				
				
            }]
        })
		
		var g = a.init(document.getElementById("echarts_pie2"));
        g.setOption({
            tooltip: {
                trigger: "item",
                //formatter: "{a} <br/>{b} : {c} ({d}%)"
				formatter: "{b} : ({d}%)"
            },
            legend: {
                orient: "vertical",
                x: "left",
                //data: ["B1111ABC", "B7777CBA", "B9999RTA", "B8888THA", "B4444UGH"]
				data: dataname_fix_muatan
				
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
                                max: 2000
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
				data: data_fix_muatan
			 
				/*data: [{
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
				*/
				
				
				
            }]
        })
		
    })
});

</script>





							<div class="col-lg-6 col-sm-6">	
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">	
							</div>
							<br />
							
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">

			<tr>
				<td style="text-align:center;"><small>Periode: <?php echo $startdate." - ".$enddate;?></small></td>
				
			</tr> 

					<div class="row">	
					 <div class="col-md-12">
                            <div class="card card-topline-red">
                                <div class="card-head">
                                    <header>Top <?=$limitdata;?> Geofence Overspeed (Kosongan)</header>
                                    <div class="tools">
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
						
						<div class="col-md-12">
                            <div class="card card-topline-red">
                                <div class="card-head">
                                    <header>Top <?=$limitdata;?> Geofence Overspeed (Muatan)</header>
                                    <div class="tools">
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body no-padding ">
                                    <div class="row">
                                        <div id="echarts_pie2" class="chart-window"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
					
					</div>
				
		</div>
	</div>
</div>
