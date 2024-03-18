<script src="<?php echo base_url(); ?>assets/js/jsblong/jquery.table2excel.js"></script>
<script>
  jQuery(document).ready(
    function() {
      jQuery("#export_xcel").click(function() {
        window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
      });
    }
  );
</script>



<div class="col-lg-6 col-sm-6">
  <input id="btn_hide_form" class="btn btn-circle btn-danger" title="" type="button" value="Hide Form" onclick="javascript:return option_form('hide')" />
  <input id="btn_show_form" class="btn btn-circle btn-success" title="" type="button" value="Show Form" onClick="javascript:return option_form('show')" style="display:none" />
</div>
<div class="col-lg-2 col-sm-2"></div>
<br />

<div class="panel" id="panel_form">
  <header class="panel-heading panel-heading-red">
    Report Result <?php if (isset($title)) echo $title; ?>
    <button type="button" name="button" id="export_xcel" class="btn btn-primary btn-sm">Export Excel</button>
  </header>
  <div class="panel-body" id="bar-parent10">
    <?php //if (sizeof($data) < 1) { 
    ?>
    <?php //echo "Data is Empty" 
    ?>
    <?php //} else { 
    ?>
    <div id="isexport_xcel" style="overflow-y:auto;">
      <table class="table table-striped table-bordered" style="font-size: 12px; overflow-y:auto;">
        <thead>
          <tr>
            <th style="text-align:center;">No</th>
            <th style="text-align:center;" colspan="2">Date &nbsp; Time</th>
            <th style="text-align:center;">Position</th>
            <th style="text-align:center;">Coordinate</th>
            <th style="text-align:center;">Status</th>
            <th style="text-align:center;">Speed</th>
            <th style="text-align:center;">Engine</th>
            <th style="text-align:center;">Odometer (km)</th>
          </tr>
        </thead>
        <tbody>
          <?php if (sizeof($data) > 0) {
            $n = 1;
            for ($i = 0; $i < sizeof($data); $i++) {
          ?>
              <tr>
                <td style="text-align:center;"><?php echo $i + 1 ?></td>
                <td style="text-align:center;"><?php echo $data[$i]->gps_date_fmt  ?></td>
                <td style="text-align:center;"><?php echo  $data[$i]->gps_time_fmt ?></td>
                <td style="text-align:center;"><?php echo $data[$i]->georeverse ?></td>
                <td style="text-align:center;"><?php echo $data[$i]->gps_latitude_real_fmt . ',' . $data[$i]->gps_longitude_real_fmt ?></td>
                <td style="text-align:center;"><?php echo $data[$i]->gpstatus ?></td>
                <td style="text-align:center;"><?php echo $data[$i]->gps_speed_fmt ?></td>
                <td style="text-align:center;"><?php echo $data[$i]->status1 ?></td>
                <td style="text-align:center;"><?php echo $data[$i]->odometer ?></td>
              </tr>
          <?php }
          } else {
            echo "<tr><td colspan='9'><b>Data is empty</b></td></tr>";
          }
          ?>

        </tbody>
      </table>
    </div>
  </div>
</div>