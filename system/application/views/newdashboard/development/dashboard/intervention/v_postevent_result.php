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
      jQuery("#export_xcel").click(function() {

        var $table = $('#isexport_xcel');
        $rows = $table.find('tr');
        var csvData = "";
        for(var i=0;i<$rows.length;i++){
                        var $cells = $($rows[i]).children('th,td'); //header or content cells

                        for(var y=0;y<$cells.length;y++){
                            if(y>0){
                              csvData += ",";
                            }
                            var txt = ($($cells[y]).text()).toString().trim();
                            if(txt.indexOf(',')>=0 || txt.indexOf('\"')>=0 || txt.indexOf('\n')>=0){
                                txt = "\"" + txt.replace(/\"/g, "\"\"") + "\"";
                            }
                            csvData += txt;
                        }
                        csvData += '\n';
        }

        var e = document.getElementById("company");
        var textCompany = e.options[e.selectedIndex].text;
        e = document.getElementById("vehicle");
        var textVehicle = e.options[e.selectedIndex].text;



        var link = document.createElement("a");
        link.href = 'data:text/csv,' + encodeURIComponent("sep=,\n"+csvData);
        link.download = "Dashboard_ntervensi.csv";
        link.click();
        //window.open('data:application/csv;charset=utf-8,' + encodeURIComponent(csvData));
      });

			// jQuery("#export_xcel").click(function()
			// {
			// 	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
			// });
		}
	);
</script>

							<div class="col-lg-6 col-sm-6">
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
                <input id="btn_stop_interval" class="btn btn-circle btn-warning" title="" type="button" value="Stop Refresh" onclick="javascript:return start_stop_refresh('stop')" />
                <input id="btn_start_interval" class="btn btn-circle btn-success" title="" type="button" value="Start Refresh" onclick="javascript:return start_stop_refresh('start')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">
							</div>
							<br />

<div class="panel" id="panel_form">
  <header class="panel-heading" id="report-result">
     Result - DEVELOPMENT EXISTING
    <button type="button" name="button" id="showexportview" class="btn btn-danger btn-sm" onclick="showexportview();">Export View</button>
    <button type="button" name="button" id="hideexportview" class="btn btn-danger btn-sm" onclick="hideexportview();" style="display:none;">Show Detail</button>
    <button type="button" name="button" id="export_xcel" class="btn btn-warning btn-sm" style="display:none;">Export Excel</button>
  </header>
  <div class="panel-body" id="bar-parent10">
    <?php if (sizeof($content) < 1) {?>
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
			<!--<th>Speed(kph)</th>-->
            <th>Position</th>
            <th>Geofence</th>
            <th>Speed Limit</th>
            <th>Speed Alert</th>
            <th>Jalur</th>
            <th>Coordinate</th>
      			<th>Interv Status</th>
      			<th>Interv By</th>
            <th>Interv Cat</th>
            <th>Fatigue Category</th>
            <th>Note CR</th>
      			<th>Note Pengawas</th>
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

              <!-- SPEED LIMIT -->
              <td style="color:red;"><?php echo $content[$i]['alarm_report_name'] ?></td>

              <!-- <?php
              if ($content[$i]['alarm_report_name'] == "Overspeed") {?>
                <td style="color:red;"><?php echo $content[$i]['alarm_report_'] ?></td>
              <?php }else {?>
                <td style="color:red;"><?php echo $content[$i]['alarm_report_name'] ?></td>
              <?php } ?> -->

			  <!--<td><?php echo $content[$i]['alarm_report_speed'] ?>
				  <!--<?php echo date("d-m-Y H:i:s", strtotime($content[$i]['alarm_report_speed_time'])) ?><br />
				  <?php echo $content[$i]['alarm_report_speed_status'] ?><br />
				  <?php echo $content[$i]['alarm_report_jalur'] ?>
			  </td>-->
			         <td><?php echo $content[$i]['alarm_report_location_start'] ?></td>
               <td><?php echo $content[$i]['alarm_report_geofence'] ?></td>

               <!-- SPEED LIMIT -->
               <?php
               if ($content[$i]['alarm_report_type'] == "Overspeed") {?>
                 <td><?php echo $content[$i]['alarm_report_speed_limit'] ?></td>
               <?php }else {?>
                 <td></td>
               <?php } ?>

               <!-- SPEED ALERT -->
               <?php
               if ($content[$i]['alarm_report_type'] == "Overspeed") {?>
                 <td><?php echo $content[$i]['alarm_report_speed'] ?></td>
               <?php }else {?>
                 <td></td>
               <?php } ?>

               <!-- JALUR -->
               <?php
               if ($content[$i]['alarm_report_type'] == "Overspeed") {?>
                 <td><?php echo $content[$i]['alarm_report_jalur'] ?></td>
               <?php }else {?>
                 <td></td>
               <?php } ?>

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

  					<?php
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
                </td>

                <?php
                // FILTER ALERT KHUSUS FATIGUE
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
                        <?php } ?>


      				<td id="detaildatatd<?php echo $i?>">
                <?php if ($content[$i]['alarm_report_type'] == "Overspeed") {?>
                    <button type="button" class="btn btn-warning" onclick="modal_post_event_controlroom('<?php echo $content[$i]['alarm_report_vehicle_id'].','.$content[$i]['alarm_report_start_time'].','.$content[$i]['alarm_report_id'].','.$content[$i]['alarm_report_type']; ?>');" title="Intervention">
    									<span class="fa fa-tasks"></span>
    								</button>

                <?php }else {?>
                  <button type="button" class="btn btn-primary" onclick="getdetailinfo('<?php echo $content[$i]['alarm_report_vehicle_id'].','.$content[$i]['alarm_report_start_time'] ?>');" title="Evidence">
  									<span class="fa fa-camera"></span>
  								</button>

  								<button type="button" class="btn btn-warning" onclick="modal_post_event_controlroom('<?php echo $content[$i]['alarm_report_vehicle_id'].','.$content[$i]['alarm_report_start_time'].','.$content[$i]['alarm_report_id'].','.$alarmtype; ?>');" title="Intervention">
  									<span class="fa fa-tasks"></span>
  								</button>
                <?php } ?>
							</td>
            </tr>
          <?php $no++; } ?>
        </tbody>
      </table>
    <?php } ?>
    </div>
  </div>
</div>



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
