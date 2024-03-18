<!--<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

-->
<script type="text/javascript">
function getspark(){
	$("#sparkline99").sparkline([5,6,7,9,9,5,3,2,2,4,6,7], {
	    type: 'line',
	    width: '100%',
	    fillColor: '#5fc29d54',
	    lineColor: '#ffffff',
	    lineWidth: 1,
	    spotRadius: 2,
	    spotColor: '#ffffff',
	    minSpotColor: '#ffffff',
	    maxSpotColor: '#ffffff',
	    highlightSpotColor: '#ffffff',
	    highlightLineColor: '#ffffff',
	    height: '45',
   });
}

function getbarchart(){
	
	new Chart(document.getElementById("bar-chart"), {
		type: 'bar',
		data: {
			//labels: ["2013", "2014", "2015", "2016"],
			labels: ["06-08", "09-11", "12-14", "15-17","18-20","21-23"],
			datasets: [
			           {
			        	   label: "tonnage",
			        	   backgroundColor: "#d32e36",
			        	   data: [
			                      8327.1,
			                      4946.4,
								  7246.4,
			                      8346.4,
			                      3546.4,
			                      5746.4
			                  ]
			           }
					   
					  /*  , {
			        	   label: "Earing",
			        	   backgroundColor: "#5F6B6D",
			        	   data: [
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor()
			                  ]
			           } */
					   
			           ]
		},
		options: {
			title: {
				display: true,
				text: ''
			}
		}
	});
}

function getbarchart_bib(){
	
	new Chart(document.getElementById("bar-chart-bib"), {
		type: 'bar',
		data: {
			//labels: ["2013", "2014", "2015", "2016"],
			labels: ["06-08", "09-11", "12-14", "15-17","18-20","21-23"],
			datasets: [
			           {
			        	   label: "tonnage",
			        	   backgroundColor: "#275daa",
			        	   data: [
			                      8327.1,
			                      4946.4,
								  7246.4,
			                      0,
			                      0,
			                      0
			                  ]
			           }
					   
					  /*  , {
			        	   label: "Earing",
			        	   backgroundColor: "#5F6B6D",
			        	   data: [
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor()
			                  ]
			           } */
					   
			           ]
		},
		options: {
			title: {
				display: true,
				text: ''
			}
		}
	});
}

function getbarchart_bir(){
	
	new Chart(document.getElementById("bar-chart-bir"), {
		type: 'bar',
		data: {
			//labels: ["2013", "2014", "2015", "2016"],
			labels: ["06-08", "09-11", "12-14", "15-17","18-20","21-23"],
			datasets: [
			           {
			        	   label: "tonnage",
			        	   backgroundColor: "green",
			        	   data: [
			                      0,
			                      0,
								  0,
			                      8346.4,
			                      0,
			                      5746.4
			                  ]
			           }
					   
					  /*  , {
			        	   label: "Earing",
			        	   backgroundColor: "#5F6B6D",
			        	   data: [
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor()
			                  ]
			           } */
					   
			           ]
		},
		options: {
			title: {
				display: true,
				text: ''
			}
		}
	});
}

function getbarchart_tia(){
	
	new Chart(document.getElementById("bar-chart-tia"), {
		type: 'bar',
		data: {
			//labels: ["2013", "2014", "2015", "2016"],
			labels: ["06-08", "09-11", "12-14", "15-17","18-20","21-23"],
			datasets: [
			           {
			        	   label: "tonnage",
			        	   backgroundColor: "orange",
			        	   data: [
			                      0,
			                      0,
								  0,
			                      0,
			                      3546.4,
			                      0
			                  ]
			           }
					   
					  /*  , {
			        	   label: "Earing",
			        	   backgroundColor: "#5F6B6D",
			        	   data: [
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor()
			                  ]
			           } */
					   
			           ]
		},
		options: {
			title: {
				display: true,
				text: ''
			}
		}
	});
}

function getbarchart_truck(){
	
	new Chart(document.getElementById("bar-chart-truck"), {
		type: 'bar',
		data: {
			//labels: ["2013", "2014", "2015", "2016"],
			labels: ["BKAE", "MMS", "RBT", "STLI","RAMB","KMB","GECL"],
			datasets: [
			           {
			        	   label: "DT in Hauling",
			        	   backgroundColor: "#217547",
			        	   data: [
			                      23,
			                      22,
								  298,
			                      10,
			                      41,
			                      33,
								  55
			                  ]
			           }
					   
					  /*  , {
			        	   label: "Earing",
			        	   backgroundColor: "#5F6B6D",
			        	   data: [
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor(),
			                      randomScalingFactor()
			                  ]
			           } */
					   
			           ]
		},
		options: {
			title: {
				display: true,
				text: ''
			}
		}
	});
}

function getpiechart(){
	var randomScalingFactor = function() {
        return Math.round(Math.random() * 1000);
    };

    var config = {
        type: 'pie',
    data: {
        datasets: [{
            data: [
                282,
                102,
                89,
                58,
                45,
				32,
            ],
            backgroundColor: [
                window.chartColors.red,
                window.chartColors.orange,
                window.chartColors.yellow,
                window.chartColors.green,
                window.chartColors.blue,
				window.chartColors.purple,
            ],
            label: 'Dataset 1'
        }],
        labels: [
            "Overspeed (282)",
            "Fatigue (102)",
            "Smoking (89)",
            "Distracted (58)",
            "Car Distance (45)",
			"Call (32)"
        ]
    },
    options: {
        responsive: true
    }
};

    var ctx = document.getElementById("chartjs_pie").getContext("2d");
    window.myPie = new Chart(ctx, config);
	
}


</script>

<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->
<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
 
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-8 col-8">
							<div class="card card-box" >
	                              <div class="card-head">
	                                  <header>Today Total Hauling</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
	                              </div>
	                              <div class="card-body no-padding height-9">
									<div class="row">
									    <canvas id="bar-chart"></canvas>
									</div>
								</div>
	                          </div>
				        </div>
						
						<div class="col-lg-6 col-md-6 col-sm-8 col-8">
							<div class="card card-box">
	                              <div class="card-head">
	                                  <header>Today BIB Hauling</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
	                              </div>
	                              <div class="card-body no-padding height-9">
									<div class="row">
									    <canvas id="bar-chart-bib"></canvas>
									</div>
								</div>
	                          </div>
				        </div>
						
						<div class="col-lg-6 col-md-6 col-sm-8 col-8">
							<div class="card card-box">
	                              <div class="card-head">
	                                  <header>Today BIR Hauling</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
	                              </div>
	                              <div class="card-body no-padding height-9">
									<div class="row">
									    <canvas id="bar-chart-bir"></canvas>
									</div>
								</div>
	                          </div>
				        </div>
						
						<div class="col-lg-6 col-md-6 col-sm-8 col-8">
							<div class="card card-box">
	                              <div class="card-head">
	                                  <header>Today TIA Hauling</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
	                              </div>
	                              <div class="card-body no-padding height-9">
									<div class="row">
									    <canvas id="bar-chart-tia"></canvas>
									</div>
								</div>
	                          </div>
				        </div>
						
						<div class="col-lg-6 col-md-6 col-sm-8 col-8">
							<div class="card card-box">
	                              <div class="card-head">
	                                  <header>DT Active</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
	                              </div>
	                              <div class="card-body no-padding height-9">
									<div class="row">
									    <canvas id="bar-chart-truck"></canvas>
									</div>
								</div>
	                          </div>
				        </div>
						
						<div class="col-md-6">
                            <div class="card card-topline-lightblue">
                                <div class="card-head">
                                    <header>Violations Today</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
	                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
	                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body " id="chartjs_pie_parent">
                                    <div class="row">
                                         <canvas id="chartjs_pie" height="120"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
						
					</div>

					<!-- start widget -->
					<!--<div class="row">
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="card bg-info">
			                    <div class="text-white py-3 px-4">
			                      <h6 class="card-title text-white mb-0">Page View</h6>
			                      <p>7582</p>
			                      <div id="sparkline26"></div>
			                      <small class="text-white">View Details</small>
			                    </div>
			                 </div>
						</div>
						
						 <div class="col-md-4 col-sm-4 col-4">
			                 <div class="card bg-success">
			                    <div class="text-white py-3 px-4">
			                      <h6 class="card-title text-white mb-0">Earning</h6>
			                      <p>3669.25</p>
			                      <div id="sparkline27"></div>
			                      <small class="text-white">View Details</small>
			                    </div>
			                 </div>
                        </div>
						 <div class="col-md-4 col-sm-4 col-4">
			                 <div class="card bg-success">
			                    <div class="text-white py-3 px-4">
			                      <h6 class="card-title text-white mb-0">Earning</h6>
			                      <p>3669.25</p>
			                      <div id="sparkline99"></div>
			                      <small class="text-white">View Details</small>
			                    </div>
			                 </div>
                        </div>
					</div>-->
					<!-- end widget -->
					
					<div class="row">
                        
						
						
                    </div>
					
	</div>				
  <!-- end page content -->

</div>
<!-- end page container -->

