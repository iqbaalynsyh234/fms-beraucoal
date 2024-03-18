<!--<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

-->

<!--<script src="<?php echo base_url()?>assets/dashboard/assets/js/hc/highcharts.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/hc/data.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/hc/drilldown.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/hc/exporting.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/hc/export-data.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/hc/accessibility.js"></script>-->

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>


<script type="text/javascript">


function getViolationToday(){
	
	// Create the chart
Highcharts.chart('container', {
  chart: {
    type: 'pie'
  },
  title: {
    text: 'VIOLATION BOARD'
  },
  subtitle: {
    text: ''
  },

  accessibility: {
    announceNewData: {
      enabled: true
    },
    point: {
      valueSuffix: ''
    }
  },

  plotOptions: {
    series: {
      dataLabels: {
        enabled: true,
        format: '{point.name}: {point.y:.0f}'
      }
    }
  },

  tooltip: {
    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b>'
  },

  series: [{
    name: "",
    colorByPoint: true,
   data: [{
        name: "RBT",
        y: 220,
        drilldown: "RBT"
      },
      {
        name: "BKA",
        y: 82,
        drilldown: "BKA"
      },
      {
        name: "GECL",
        y: 130,
        drilldown: "GECL"
      },
      {
        name: "MKS",
        y: 88,
        drilldown: "MKS"
      },
      {
        name: "MMS",
        y: 84,
        drilldown: "MMS"
      },
      {
        name: "BBS",
        y: 56,
        drilldown: "BBS"
      },
      {
        name: "EST",
        y: 51,
        drilldown: "EST"
      },
	  {
        name: "KMB",
        y: 67,
        drilldown: "KMB"
      },
	  {
        name: "RAM",
        y: 106,
        drilldown: "RAM"
      },
	  {
        name: "STLI",
        y: 93,
        drilldown: "STLI"
      }

    ]
  }],
  drilldown: {
    series: [{
        name: "RBT",
        id: "RBT",
        data: [
          [
            "Overspeed",
            78
          ],
          [
            "Fatigue",
            23
          ],
          [
            "Smoking",
            38
          ],
          [
            "Distracted",
            30
          ],
          [
            "Car Distance",
            19
          ],
          [
            "Call",
            32
          ]
        ]
      },
      {
        name: "BKA",
        id: "BKA",
        data: [
          [
            "Overspeed",
            33
          ],
          [
            "Fatigue",
            15
          ],
          [
            "Smoking",
            14
          ],
          [
            "Distracted",
            8
          ],
          [
            "Car Distance",
            5
          ],
          [
            "Call",
            7
          ]
        ]
      },
      {
        name: "GECL",
        id: "GECL",
        data: [
         [
            "Overspeed",
            40
          ],
          [
            "Fatigue",
            27
          ],
          [
            "Smoking",
            25
          ],
          [
            "Distracted",
            10
          ],
          [
            "Car Distance",
            8
          ],
          [
            "Call",
            20
          ]
        ]
      },
      {
        name: "MKS",
        id: "MKS",
        data: [
          [
            "Overspeed",
            44
          ],
          [
            "Fatigue",
            17
          ],
          [
            "Smoking",
            7
          ],
          [
            "Distracted",
            4
          ],
          [
            "Car Distance",
            7
          ],
          [
            "Call",
            9
          ]
        ]
      },
      {
        name: "MMS",
        id: "MMS",
        data: [
          [
            "Overspeed",
            33
          ],
          [
            "Fatigue",
            6
          ],
          [
            "Smoking",
            18
          ],
          [
            "Distracted",
            14
          ],
          [
            "Car Distance",
            5
          ],
          [
            "Call",
            8
          ]
        ]
      },
      {
        name: "BBS",
        id: "BBS",
        data: [
          [
            "Overspeed",
            14
          ],
          [
            "Fatigue",
            8
          ],
          [
            "Smoking",
            13
          ],
          [
            "Distracted",
            7
          ],
          [
            "Car Distance",
            5
          ],
          [
            "Call",
            9
          ]
        ]
      },
	  
	  {
        name: "EST",
        id: "EST",
        data: [
          [
            "Overspeed",
            18
          ],
          [
            "Fatigue",
            8
          ],
          [
            "Smoking",
            5
          ],
          [
            "Distracted",
            8
          ],
          [
            "Car Distance",
            7
          ],
          [
            "Call",
            5
          ]
        ]
      },
	  
	  {
        name: "KMB",
        id: "KMB",
        data: [
          [
            "Overspeed",
            20
          ],
          [
            "Fatigue",
            12
          ],
          [
            "Smoking",
            8
          ],
          [
            "Distracted",
            8
          ],
          [
            "Car Distance",
            4
          ],
          [
            "Call",
            15
          ]
        ]
      },
	  
	  {
        name: "RAM",
        id: "RAM",
        data: [
          [
            "Overspeed",
            25
          ],
          [
            "Fatigue",
            12
          ],
          [
            "Smoking",
            9
          ],
          [
            "Distracted",
            32
          ],
          [
            "Car Distance",
            14
          ],
          [
            "Call",
            14
          ]
        ]
      },
	  
	  {
        name: "STLI",
        id: "STLI",
        data: [
          [
            "Overspeed",
            30
          ],
          [
            "Fatigue",
            18
          ],
          [
            "Smoking",
            14
          ],
          [
            "Distracted",
            12
          ],
          [
            "Car Distance",
            11
          ],
          [
            "Call",
            8
          ]
        ]
      }
	  
	  
	  
    ]
  }
});

}

function getViolationAllToday(){
	
	// Create the chart
Highcharts.chart('container', {
  chart: {
    type: 'pie'
  },
  title: {
    text: 'VIOLATION BOARD'
  },
  subtitle: {
    text: ''
  },

  accessibility: {
    announceNewData: {
      enabled: true
    },
    point: {
      valueSuffix: ''
    }
  },

  plotOptions: {
    series: {
      dataLabels: {
        enabled: true,
        format: '{point.name}: {point.y:.0f}'
      }
    }
  },

  tooltip: {
    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b>'
  },

  series: [{
    name: "",
    colorByPoint: true,
   data: [{
        name: "RBT",
        y: 220,
        drilldown: "RBT"
      },
      {
        name: "BKA",
        y: 82,
        drilldown: "BKA"
      },
      {
        name: "GECL",
        y: 130,
        drilldown: "GECL"
      },
      {
        name: "MKS",
        y: 88,
        drilldown: "MKS"
      },
      {
        name: "MMS",
        y: 84,
        drilldown: "MMS"
      },
      {
        name: "BBS",
        y: 56,
        drilldown: "BBS"
      },
      {
        name: "EST",
        y: 51,
        drilldown: "EST"
      },
	  {
        name: "KMB",
        y: 67,
        drilldown: "KMB"
      },
	  {
        name: "RAM",
        y: 106,
        drilldown: "RAM"
      },
	  {
        name: "STLI",
        y: 93,
        drilldown: "STLI"
      }

    ]
  }],
  drilldown: {
    series: [{
        name: "RBT",
        id: "RBT",
        data: [
          [
            "Overspeed",
            78
          ],
          [
            "Fatigue",
            23
          ],
          [
            "Smoking",
            38
          ],
          [
            "Distracted",
            30
          ],
          [
            "Car Distance",
            19
          ],
          [
            "Call",
            32
          ]
        ]
      },
      {
        name: "BKA",
        id: "BKA",
        data: [
          [
            "Overspeed",
            33
          ],
          [
            "Fatigue",
            15
          ],
          [
            "Smoking",
            14
          ],
          [
            "Distracted",
            8
          ],
          [
            "Car Distance",
            5
          ],
          [
            "Call",
            7
          ]
        ]
      },
      {
        name: "GECL",
        id: "GECL",
        data: [
         [
            "Overspeed",
            40
          ],
          [
            "Fatigue",
            27
          ],
          [
            "Smoking",
            25
          ],
          [
            "Distracted",
            10
          ],
          [
            "Car Distance",
            8
          ],
          [
            "Call",
            20
          ]
        ]
      },
      {
        name: "MKS",
        id: "MKS",
        data: [
          [
            "Overspeed",
            44
          ],
          [
            "Fatigue",
            17
          ],
          [
            "Smoking",
            7
          ],
          [
            "Distracted",
            4
          ],
          [
            "Car Distance",
            7
          ],
          [
            "Call",
            9
          ]
        ]
      },
      {
        name: "MMS",
        id: "MMS",
        data: [
          [
            "Overspeed",
            33
          ],
          [
            "Fatigue",
            6
          ],
          [
            "Smoking",
            18
          ],
          [
            "Distracted",
            14
          ],
          [
            "Car Distance",
            5
          ],
          [
            "Call",
            8
          ]
        ]
      },
      {
        name: "BBS",
        id: "BBS",
        data: [
          [
            "Overspeed",
            14
          ],
          [
            "Fatigue",
            8
          ],
          [
            "Smoking",
            13
          ],
          [
            "Distracted",
            7
          ],
          [
            "Car Distance",
            5
          ],
          [
            "Call",
            9
          ]
        ]
      },
	  
	  {
        name: "EST",
        id: "EST",
        data: [
          [
            "Overspeed",
            18
          ],
          [
            "Fatigue",
            8
          ],
          [
            "Smoking",
            5
          ],
          [
            "Distracted",
            8
          ],
          [
            "Car Distance",
            7
          ],
          [
            "Call",
            5
          ]
        ]
      },
	  
	  {
        name: "KMB",
        id: "KMB",
        data: [
          [
            "Overspeed",
            20
          ],
          [
            "Fatigue",
            12
          ],
          [
            "Smoking",
            8
          ],
          [
            "Distracted",
            8
          ],
          [
            "Car Distance",
            4
          ],
          [
            "Call",
            15
          ]
        ]
      },
	  
	  {
        name: "RAM",
        id: "RAM",
        data: [
          [
            "Overspeed",
            25
          ],
          [
            "Fatigue",
            12
          ],
          [
            "Smoking",
            9
          ],
          [
            "Distracted",
            32
          ],
          [
            "Car Distance",
            14
          ],
          [
            "Call",
            14
          ]
        ]
      },
	  
	  {
        name: "STLI",
        id: "STLI",
        data: [
          [
            "Overspeed",
            30
          ],
          [
            "Fatigue",
            18
          ],
          [
            "Smoking",
            14
          ],
          [
            "Distracted",
            12
          ],
          [
            "Car Distance",
            11
          ],
          [
            "Call",
            8
          ]
        ]
      }
	  
	  
	  
    ]
  }
});

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
	
						<div class="col-lg-12 col-md-12 col-sm-12 col-12">
							<div class="card card-box" >
									<div class="card-head">
	                                  <header>VIOLATION <?php echo date("d-m-Y");?> SHIFT PAGI</header>
	                                  <div class="tools">
	                                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
										<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
										<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
	                                 </div>
									</div>
									
									<div class="card-body no-padding height-9">
										<figure class="highcharts-figure">
											<div id="container"></div>
											
										</figure>
										
			

									</div>
								
	                          </div>
				        </div>
						
						
						
					
	</div>				
  <!-- end page content -->

</div>
<!-- end page container -->



