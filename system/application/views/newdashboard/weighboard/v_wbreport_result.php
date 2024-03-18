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

<div class="row">
	<div class="col-md-12">
  <!-- <div class="panel" id="panel_form">
    <div class="panel-body" id="bar-parent10"> -->
		<div class="panel" id="panel_form">
			<header class="panel-heading panel-heading-blue">WB Report Result</header>
			<div class="panel-body" id="bar-parent10">
        <button type="button" name="button" id="export_xcel" class="btn btn-warning btn-sm">Export Excel</button>

        <div id="isexport_xcel"  style="overflow-y:auto;">
          <table class="table table-bordered table-striped table-hover" style="font-size:12px;">
            <thead>
              <tr>
                <th>No</th>
                <th>Trans</th>
                <th>Mode</th>
                <th>Truck</th>
                <th>Driver</th>
                <th>Client</th>
                <th>Material</th>
                <th>Hauling</th>
                <th>Date Trans</th>
                <th>Time Trans</th>
                <th>Date Gross</th>
                <th>Time Gross</th>
                <th>Date Netto</th>
                <th>Time Netto</th>
                <th>Code</th>
                <th>Gross</th>
                <th>Tare</th>
                <th>Netto</th>
                <th>Coal</th>
              </tr>
            </thead>
            <tbody>
              <?php if (sizeof($content) < 1) {?>
                <?php echo "Data Is Empty"; ?>
              <?php }else {
              for ($i=0; $i < sizeof($content); $i++) {?>
                <tr>
                  <td><?php echo $i+1; ?></td>
                  <td><?php echo $content[$i]['Trans'] ?></td>
                  <td><?php echo $content[$i]['Mode'] ?></td>
                  <td><?php echo $content[$i]['Truck'] ?></td>
                  <td><?php echo $content[$i]['Driver'] ?></td>
                  <td><?php echo $content[$i]['Client'] ?></td>
                  <td><?php echo $content[$i]['Material'] ?></td>
                  <td><?php echo $content[$i]['Hauling'] ?></td>
                  <td><?php echo date("d-m-Y", strtotime($content[$i]['DateTimeTrans'])); ?></td>
                  <td><?php echo date("H:i:s", strtotime($content[$i]['DateTimeTrans'])); ?></td>
                  <td><?php echo date("d-m-Y", strtotime($content[$i]['DateTimeGross'])); ?></td>
                  <td><?php echo date("H:i:s", strtotime($content[$i]['DateTimeGross'])); ?></td>
                  <td><?php echo date("d-m-Y", strtotime($content[$i]['DateTimeNetto'])); ?></td>
                  <td><?php echo date("H:i:s", strtotime($content[$i]['DateTimeNetto'])); ?></td>
                  <td><?php echo $content[$i]['Code'] ?></td>
                  <td><?php echo $content[$i]['Gross'] ?></td>
                  <td><?php echo $content[$i]['Tare'] ?></td>
                  <td><?php echo $content[$i]['Netto'] ?></td>
                  <td><?php echo $content[$i]['Coal'] ?></td>
                </tr>
              <?php } ?>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
     </div>
    </div>
  </div>
