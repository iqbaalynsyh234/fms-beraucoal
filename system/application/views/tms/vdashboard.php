<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script>
  $(document).ready(function() {
    /*var randomScalingFactor = function() {
        return Math.round(Math.random() * 100);
    };*/
    //looping chart
    <?php if (count($company)>0){
		for ($i=0;$i<count($company);$i++){
			$totaldata = $this->dashboardmodel->gettotalengine($company[$i]->company_id);
			$totalengine = explode("|", $totaldata);
			$total_nodata = $totalengine[3];
			
	?>

    var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            <?=$totalengine[0]?>, //off
            <?=$totalengine[1]?>, //on
            <?php if($total_nodata > 0){ ?>
            <?=$totalengine[3]?>, //go to history
            <?php } ?>

          ],
          backgroundColor: [
            window.chartColors.red,
            window.chartColors.blue,
            <?php if($total_nodata > 0){ ?>
            window.chartColors.yellow,
            <?php } ?>


          ],
          label: 'Dataset 1'
        }],
        labels: [

          <?php if($total_nodata > 0){ ?>
          "Engine OFF" + " (" + <?=$totalengine[0]?> + ")",
          "Engine ON" + " (" + <?=$totalengine[1]?> + ")",
          "No Data" + " (" + <?=$totalengine[3]?> + ")"
          <?php }else { ?>
          "Engine OFF" + " (" + <?=$totalengine[0]?> + ")",
          "Engine ON" + " (" + <?=$totalengine[1]?> + ")"
          <?php } ?>

        ]
      },
      options: {
        responsive: true
      }
    };

    var ctx = document.getElementById("chartjs_pie" + <?=$i?>).getContext("2d");
    window.myPie = new Chart(ctx, config);

    <?php }
		$lastcheck = $this->dashboardmodel->getlastcheck();
		$datastatus = explode("|", $rstatus);
		$dataspeed = explode("|", $rspeed);
		if($dataspeed[2] > 0){
			$overspeed = $this->dashboardmodel->getoverspeed($this->sess->user_id);
		}

	}
?>

    //pie status
    var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            <?=$datastatus[0]?>, //P
            <?=$datastatus[1]?>, //K
            <?=$datastatus[2]?>, //M

          ],
          backgroundColor: [
            window.chartColors.blue,
            window.chartColors.yellow,
            window.chartColors.red,

          ],
          label: 'Dataset 1'
        }],
        labels: [
          "Online" + " (" + <?=$datastatus[0]?> + ")",
          "Online (Delay)" + " (" + <?=$datastatus[1]?> + ")",
          "Offline" + " (" + <?=$datastatus[2]?> + ")"
        ]
      },
      options: {
        responsive: true
      }
    };

    //pie all vehicle
    var ctx = document.getElementById("chartjs_pie_status").getContext("2d");
    window.myPie = new Chart(ctx, config);

    var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            <?=$datastatus[4]?>, //OFF
            <?=$datastatus[5]?>, //ON
            <?php if($datastatus[6] > 0){ ?>
            <?=$datastatus[6]?>, //NO DATA
            <?php } ?>
          ],
          backgroundColor: [
            window.chartColors.red,
            window.chartColors.blue,
            <?php if($datastatus[6] > 0){ ?>
            window.chartColors.yellow,
            <?php } ?>

          ],
          label: 'Dataset 1'
        }],
        labels: [
          <?php if($datastatus[6] > 0){ ?>
          "Engine OFF" + " (" + <?=$datastatus[4]?> + ")",
          "Engine ON" + " (" + <?=$datastatus[5]?> + ")",
          "No Data" + " (" + <?=$datastatus[6]?> + ")"
          <?php }else{ ?>
          "Engine OFF" + " (" + <?=$datastatus[4]?> + ")",
          "Engine ON" + " (" + <?=$datastatus[5]?> + ")"
          <?php } ?>

        ]
      },
      options: {
        responsive: true
      }
    };

    var ctx = document.getElementById("chartjs_pie_all").getContext("2d");
    window.myPie = new Chart(ctx, config);



    //speed status
    var config = {
      type: 'pie',
      data: {
        datasets: [{
          data: [
            <?=$dataspeed[0]?>, //0
            <?=$dataspeed[1]?>, //40
            <?=$dataspeed[2]?>, //80

          ],
          backgroundColor: [
            window.chartColors.green,
            window.chartColors.purple,
            window.chartColors.yellow,

          ],
          label: 'Dataset 1'
        }],
        labels: [
          "< 40 kph" + " (" + <?=$dataspeed[0]?> + ")",
          "> 40 kph" + " (" + <?=$dataspeed[1]?> + ")",
          "> 80 kph" + " (" + <?=$dataspeed[2]?> + ")"
        ]
      },
      options: {
        responsive: true
      }
    };

    //pie all speed
    var ctx = document.getElementById("chartjs_pie_speed_status").getContext("2d");
    window.myPie = new Chart(ctx, config);

  });
</script>
<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content" style="margin-left:-30px;">
    <!--<div class="page-bar">
      <div class="page-title-breadcrumb">
        <div class=" pull-left">
          <div class="page-title">Dashboard</div>
        </div>
        <ol class="breadcrumb page-breadcrumb pull-right">
          <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="<?=base_url();?>dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
          </li>
          <li class="active">Dashboard</li>
        </ol>
      </div>
    </div>-->
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <div class="mg-bottom-xxl">
          <?php if(isset($lastcheck)){?>
            <small>Last Checked at <font color="green" style="align-right;"><strong><?=date("d-m-Y H:i:s", strtotime($lastcheck->auto_last_check));?></strong></font></small>
            <?php } else{
									echo "No Data Sync";
								}?>

        </div>
      </div>
    </div>

    <div class="row">
      <?php if(isset($datastatus)){?>
        <div class="col-md-6">
          <div class="card card-topline-lightblue">
            <div class="card-head">
              <header>ALL Vehicles (
                <?=$datastatus[3];?>)</header>
              <div class="tools">
                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
              </div>
            </div>
            <div class="card-body" id="chartjs_pie_parent">
              <div class="row">
                <canvas id="chartjs_pie_all" height="120"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card card-topline-lightblue">
            <div class="card-head">
              <header>GPS Status (
                <?=$datastatus[3];?>)</header>
              <div class="tools">
                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
              </div>
            </div>
            <div class="card-body" id="chartjs_pie_parent">
              <div class="row">
                <canvas id="chartjs_pie_status" height="120"></canvas>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
          <?php if(isset($dataspeed)){?>
            <div class="col-md-6">
              <div class="card card-topline-lightblue">
                <div class="card-head">
                  <header>
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
											All Speed Status (<?=$dataspeed[3];?>)
										</a>
                    <ul class="dropdown-menu animated swing">
                      <li>
                        <ul class="dropdown-menu-list medium-slimscroll-style" data-handle-color="#637283">
                          <h5><span class="bold">Vehicle > 80 kph</span></h5>
                          <?php if (isset($overspeed) && (count($overspeed)>0)){
														for ($i=0;$i<count($overspeed);$i++){

														?>
                            <li>
                              <span class="time bold"><?=$overspeed[$i]->auto_vehicle_no;?></span>
                              <span class="time"><?=$overspeed[$i]->auto_last_speed;?> kph</span>
                              <span class="time"><?=date("d-m-Y H:i",strtotime($overspeed[$i]->auto_last_update));?></span>
                            </li>
                            <?php } } else { ?>
                              <li>
                                <span class="time">No Data Overspeed</span>
                              </li>
                              <?php } ?>
                        </ul>
                      </li>
                    </ul>
                  </header>
                  <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                    <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                    <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                  </div>
                </div>
                <div class="card-body" id="chartjs_pie_parent">
                  <div class="row">
                    <canvas id="chartjs_pie_speed_status" height="120"></canvas>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>

              <?php if (count($company)>0){
						for ($i=0;$i<count($company);$i++){
							$totaldata = $this->dashboardmodel->gettotalengine($company[$i]->company_id);
							$totalengine = explode("|", $totaldata);

						?>
                <div class="col-md-6">
                  <div class="card card-topline-lightblue">
                    <div class="card-head">
                      <header><a href="<?=base_url();?>maps/area/<?=$company[$i]->company_id;?>"><?=$company[$i]->company_name;?> (<?=$totalengine[2];?>)</a></header>
                      <div class="tools">
                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                        <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                      </div>
                    </div>
                    <div class="card-body " id="chartjs_pie_parent">
                      <div class="row">
                        <canvas id="chartjs_pie<?=$i;?>" height="120"></canvas>
                      </div>
                    </div>
                  </div>
                </div>
                <?php }} ?>

    </div>

  </div>
</div>
<!-- end page content -->

<!-- start chat sidebar -->
<div class="chat-sidebar-container" data-close-on-body-click="false">
  <?=$chatsidebar;?>
</div>
<!-- end chat sidebar -->
