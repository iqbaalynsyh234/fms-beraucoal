<style media="screen">
#report-result{
  background-color: #221f1f;
  color: white;
}
</style>

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

							<!-- <div class="col-lg-6 col-sm-6">
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div> -->
							<div class="col-lg-2 col-sm-2">
							</div>
							<br />

<!-- <div class="panel" id="panel_form">
  <header class="panel-heading" id="report-result">
     Data Alert
    <button type="button" name="button" id="showexportview" class="btn btn-danger btn-sm" onclick="showexportview();" style="display:none;">Export View</button>
    <button type="button" name="button" id="hideexportview" class="btn btn-danger btn-sm" onclick="hideexportview();" style="display:none;">Show Detail</button>
    <button type="button" name="button" id="export_xcel" class="btn btn-warning btn-sm" style="display:none;">Export Excel</button>
  </header> -->
  <div class="panel-body" id="bar-parent10">
    <?php if ($error == true) {?>
      <table class="table table-striped table-bordered" style="font-size: 16px; overflow-y:auto;">
        <tr>
          <td><?php echo $message; ?></td>
        </tr>
      </table>
    <?php }elseif (sizeof($content) < 1) {?>
          <?php echo "Data is Empty" ?>
    <?php }else {?>
    <div id="isexport_xcel" style="overflow-y:auto;">
      <table class="table table-striped table-bordered" style="font-size: 11px; overflow-y:auto;">
        <thead>
          <tr>
            <th>No</th>
            <th>Alert ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Vehicle No</th>
            <th>Vehicle Name</th>
            <th>Alarm Type</th>
            <!-- <th>Status</th>
            <th>Photo</th>
            <th>Video</th>
            <th>Intervention DateTime</th> -->
			<!--<th>Speed(kph)</th>-->
            <th>Provider</th>
            <th>Position</th>
            <th>Coordinate</th>
      			<!-- <th>Interv Status</th>
      			<th>Interv By</th>
            <th>Interv Cat</th>
            <th>Fatigue Category</th>
            <th>Note CR</th>
      			<th>Note Pengawas</th> -->
      			<th id="detaildata">Detail</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; for ($i=0; $i < sizeof($content); $i++) {
			  // $data_wita = date("Y-m-d H:i:s", strtotime($content[$i]['alarm_report_start_time'])+60*60);
				$date_normal = date("Y-m-d H:i:s", strtotime($content[$i]['alarm_report_start_time']));
			  ?>
            <tr>
              <td><?php echo $no ?></td>
              <!-- <td><?php echo date("d-m-Y", strtotime($data_wita)) ?></td>
              <td><?php echo date("H:i:s", strtotime($data_wita)) ?></td> -->
              <td><?php echo $content[$i]['alarm_report_id']?></td>
							<td><?php echo date("d-m-Y", strtotime($date_normal)) ?></td>
              <td><?php echo date("H:i:s", strtotime($date_normal)) ?></td>
              <td><?php echo $content[$i]['alarm_report_vehicle_no']?></td>
              <td><?php echo $content[$i]['alarm_report_vehicle_name'] ?></td>
              <td style="color:red;"><?php echo $content[$i]['alarm_report_name'] ?></td>

              <!-- <td style="color:red;">
                <?php
                  $status_alert = $content[$i]['alarm_report_statusintervention_cr'];
                    if ($status_alert == 1) {
                      echo "True";
                    }elseif ($status_alert == 2) {
                      echo "False";
                    }else {
                      echo "Belum Diintervensi";
                    }
                ?>
              </td> -->

              <!-- <td>
                <a href="<?php echo $content[$i]['alarm_report_image_link'] ?>" target="_blank">
                  <?php echo $content[$i]['alarm_report_image_link'] ?>
                </a>
              </td>
              <td>
                <a href="<?php echo $content[$i]['alarm_report_video_link'] ?>" target="_blank">
                  <?php echo $content[$i]['alarm_report_video_link'] ?>
                </a>
              </td>
              <td><?php echo $content[$i]['alarm_report_datetime_cr'] ?></td> -->

			  <!--<td><?php echo $content[$i]['alarm_report_speed'] ?>
				  <!--<?php echo date("d-m-Y H:i:s", strtotime($content[$i]['alarm_report_speed_time'])) ?><br />
				  <?php echo $content[$i]['alarm_report_speed_status'] ?><br />
				  <?php echo $content[$i]['alarm_report_jalur'] ?>
			  </td>-->
                <td><?php echo $content[$i]['alarm_report_provider_name'] ?></td>
			         <td><?php echo $content[$i]['alarm_report_location_start'] ?></td>

              <td>
                <?php
                $coordstart = $content[$i]['alarm_report_coordinate_start'];
                  if (strpos($coordstart, '-') !== false) {
                    $coordstart = $coordstart;
                  }else {
                    $coordstart = "".$coordstart;
                  }
                $coordend = $content[$i]['alarm_report_coordinate_end'];
                  if (strpos($coordend, '-') !== false) {
                    $coordend = $coordend;
                  }else {
                    $coordend = "".$coordend;
                  }
                 ?>
                <!--<a href='http://maps.google.com/maps?z=12&t=m&q=loc:<?php echo $coordstart ?>' target='_blank'><?php echo $coordstart ?></a>-->
	              <?php echo $coordstart ?>
              </td>

  					<!-- <?php
              $alarm_report_status_intervensi = $content[$i]['alarm_report_statusintervention_cr'];
                if ($alarm_report_status_intervensi == 0) {?>
                    <td style="background-color:red;">
                    <?php echo "<p style='color:white;font-size:11px;'>Belum Diintervensi</p>";?>
                <?php }else { ?>
                    <td style="background-color:green;">
                    <?php echo "<p style='color:white;font-size:11px;'>Sudah Diintervensi</p>";?>
                  <?php }?>
                </td>

            <?php
              $alarm_report_name_cr = $content[$i]['alarm_report_name_cr'];
                if ($alarm_report_name_cr == "") {?>
                    <td style="background-color:red;">
                    <?php echo "<p style='color:white;font-size:11px;'>Belum Diintervensi</p>";?>
                <?php }else { ?>
                    <td style="background-color:green;">
                    <?php echo "<p style='color:white;font-size:11px;'>".$alarm_report_name_cr."</p>";?>
                  <?php }?>
                </td>

            <?php
              $alarm_report_intervention_category_cr = $content[$i]['alarm_report_intervention_category_cr'];
                if ($alarm_report_intervention_category_cr == "") {
                  ?>
                    <td style="background-color:red;">
                    <?php echo "<p style='color:white;font-size:11px;'>Belum Diintervensi</p>";?>
                <?php }else {
                  $alarm_report_intervention_category_crfix = explode("|", $content[$i]['alarm_report_intervention_category_cr'])
                  ?>
                    <td style="background-color:green;">
                    <?php echo "<p style='color:white;font-size:11px;'>".$alarm_report_intervention_category_crfix[1]."</p>";?>
                  <?php }?>
                </td> -->

                <!-- <?php
                    $fatigue_category = $content[$i]['alarm_report_fatiguecategory_cr'];
                    $note_cr          = $content[$i]['alarm_report_note_cr'];
                    $note_layer2up    = $content[$i]['alarm_report_note_up'];

                    if ($fatigue_category == "0") {?>
                      <td>
                        <p style='font-size:11px;text-align: center;'>-</p>
                      </td>
                    <?php }elseif ($fatigue_category == "") {?>
                      <td style="background-color:red;">
                        <p style='color:white;font-size:11px;'>Belum Diintervensi</p>
                      </td>
                    <?php } else {?>
                      <td><?php echo $fatigue_category; ?></td>
                    <?php } ?>

                    <?php
                      if ($note_cr != "") {?>
                        <td><?php echo $note_cr; ?></td>
                      <?php }else {?>
                        <td style="background-color:red;">
                          <p style='color:white;font-size:11px;'>Belum Diintervensi</p>
                        </td>
                      <?php }?>

                      <?php
                        if ($note_layer2up != "") {?>
                          <td><?php echo $note_layer2up; ?></td>
                        <?php }else {?>
                          <td>
                            <p style='font-size:11px;'>Null</p>
                          </td>
                        <?php } ?> -->


      				<td id="detaildatatd<?php echo $i?>">
                <!-- <?php if ($content[$i]['alarm_report_type'] != "Overspeed") {?>
                  <button type="button" class="btn btn-primary" onclick="getdetailinfo('<?php echo $content[$i]['alarm_report_vehicle_id'].','.$content[$i]['alarm_report_start_time'] ?>');" title="Evidence">
                    <span class="fa fa-camera"></span>
                  </button>
                <?php } ?> -->

                <button type="button" class="btn btn-warning" id="btn_intervention" onclick="modal_intervention_controlroom_p1('<?php echo $content[$i]['alarm_report_vehicle_id'].','.$content[$i]['alarm_report_start_time'].','.$content[$i]['alarm_report_id'].','.$content[$i]['alarm_report_type'].','.$content[$i]['alarm_report_imei']; ?>');" title="Mockup Intervensi">
                  <span class="fa fa-exclamation-triangle"></span>
                </button>

								<!-- <button type="button" class="btn btn-info" id="btn_intervention" onclick="modal_intervention_controlroom('<?php echo $content[$i]['alarm_report_vehicle_id'].','.$content[$i]['alarm_report_start_time'].','.$content[$i]['alarm_report_id'].','.$content[$i]['alarm_report_type'].','.$content[$i]['alarm_report_imei']; ?>');" title="Intervensi">
									<span class="fa fa-tasks"></span>
								</button> -->
							</td>
            </tr>
          <?php $no++; } ?>
        </tbody>
      </table>
    <?php } ?>
    </div>
  </div>
<!-- </div> -->



<script type="text/javascript">
  function showexportview(){
    $("#detaildata").hide();
    $("#showexportview").hide();
    $("#hideexportview").show();
    $("#export_xcel").show();
    var datareport = '<?php echo json_encode($content)?>';
    var obj        = JSON.parse(datareport);
    console.log("datareport report : ", datareport);
    console.log("obj report : ", obj);
    for (var i = 0; i < obj.length; i++) {
      $("#detaildatatd"+i).hide();
    }
  }

  function hideexportview(){
    $("#detaildata").show();
    $("#showexportview").show();
    $("#hideexportview").hide();
    $("#export_xcel").hide();
    var datareport = '<?php echo json_encode($content)?>';
    var obj        = JSON.parse(datareport);
    console.log("datareport report : ", datareport);
    console.log("obj report : ", obj);
    for (var i = 0; i < obj.length; i++) {
      $("#detaildatatd"+i).show();
    }
  }
</script>
