<!--<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

-->
<script type="text/javascript">

/*function getpiechart_rbt(){
	var randomScalingFactor = function() {
        return Math.round(Math.random() * 100);
    };

    var config = {
        type: 'pie',
    data: {
        datasets: [{
            data: [
                149,
                18,
                10,
                58,
				13,
               
            ],
            backgroundColor: [
                window.chartColors.green,
                window.chartColors.yellow,
                window.chartColors.blue,
				window.chartColors.red,
				window.chartColors.orange,
            ],
            label: 'Dataset 1'
        }],
        labels: [
			"HAULING(149)",
            "ROM (18)",
            "PORT (10)"
			"POOL/WS (58)",
			"OUT (13)"
        ]
    },
    options: {
        responsive: true
    }
};

    var ctx = document.getElementById("chartjs_pie_rbt").getContext("2d");
    window.myPie = new Chart(ctx, config);
	
} */

function getpiechart_violation(){
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

    var ctx = document.getElementById("chartjs_pie_violation").getContext("2d");
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
						<div class="col-md-6">
                            <div class="card card-topline-lightblue">
                                <div class="card-head">
                                    <header>RBT Today</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
	                                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
	                                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body " id="chartjs_pie_rbt_parent">
                                    <div class="row">
                                         <canvas id="chartjs_pie_rbt" height="120"></canvas>
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
                                <div class="card-body " id="chartjs_pie_violation_parent">
                                    <div class="row">
                                         <canvas id="chartjs_pie_violation" height="120"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
						
						
						
					</div>

					
					
	</div>				
  <!-- end page content -->

</div>
<!-- end page container -->

