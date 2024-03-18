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

							<div class="col-lg-6 col-sm-6">
								<input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
								<input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none"/>
							</div>
							<div class="col-lg-2 col-sm-2">
							</div>
							<br />

<div class="panel" id="panel_form">
  <header class="panel-heading panel-heading-blue">
    Report Result
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
            <th id="detaildata">Detail</th>
            <th>Date</th>
            <th>Time</th>

            <th>Vehicle No</th>
            <th>Vehicle Name</th>
            <th>Alarm Type</th>
			<!--<th>Speed(kph)</th>-->
            <th>Position</th>
            <th>Coordinate</th>

          </tr>
        </thead>
        <tbody>
          <?php $no = 1; for ($i=0; $i < sizeof($content); $i++) {
			  $data_wita = date("Y-m-d H:i:s", strtotime($content[$i]['alarm_report_start_time'])+60*60);


			  ?>
            <tr>
              <td><?php echo $no ?></td>
              <td id="detaildatatd<?php echo $i?>">
                <button type="button" class="btn btn-primary" onclick="getdetailinfo('<?php echo $content[$i]['alarm_report_vehicle_id'].','.$content[$i]['alarm_report_start_time'] ?>');">
                  <span class="fa fa-list"></span>
                </button>
              </td>

              <td><?php echo date("d-m-Y", strtotime($data_wita)) ?></td>
              <td><?php echo date("H:i:s", strtotime($data_wita)) ?></td>
              <td><?php echo $content[$i]['alarm_report_vehicle_no']?></td>
              <td><?php echo $content[$i]['alarm_report_vehicle_name'] ?></td>
              <td style="color:red;"><?php echo $content[$i]['alarm_report_name'] ?></td>
			  <!--<td><?php echo $content[$i]['alarm_report_speed'] ?>
				  <!--<?php echo date("d-m-Y H:i:s", strtotime($content[$i]['alarm_report_speed_time'])) ?><br />
				  <?php echo $content[$i]['alarm_report_speed_status'] ?><br />
				  <?php echo $content[$i]['alarm_report_jalur'] ?>
			  </td>-->
			  <td><?php echo $content[$i]['alarm_report_location_start'] ?></td>

              <td>
                <?php
                $coordstart = $content[$i]['alarm_report_coordinate_start'];
                  if (strpos($coordstart, '-') !== false) {
                    $coordstart = $coordstart;
                  }else {
                    $coordstart = "-".$coordstart;
                  }
                $coordend = $content[$i]['alarm_report_coordinate_end'];
                  if (strpos($coordend, '-') !== false) {
                    $coordend = $coordend;
                  }else {
                    $coordend = "-".$coordend;
                  }
                 ?>
                <!--<a href='http://maps.google.com/maps?z=12&t=m&q=loc:<?php echo $coordstart ?>' target='_blank'><?php echo $coordstart ?></a>-->
				<?php echo $coordstart ?>
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
