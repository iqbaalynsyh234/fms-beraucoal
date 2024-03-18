<script type="text/javascript">
  jQuery(document).ready(function(){
    // EXPORT TO EXCEL
    jQuery("#btnexcelreport").click(function()
    {
      window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#exportthisecel').html()));
    });
  });
</script>

<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:void(0);" />
<div id="exportthisecel">
<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
  <thead>
    <tr>
      <th>No</th>
      <th>Vehicle</th>
      <th>Servicess Name</th>
      <th>Number Of Letter</th>
      <th>Executor</th>
      <th>Cost</th>
      <th>Agencies</th>
      <th>Note</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($data) > 0) {?>
      <?php $no = 1; for ($i=0; $i < count($data); $i++) {?>
        <tr>
          <td><?php echo $no; ?></td>
          <td>
            <?php echo $data[$i]['servicess_vehicle_no']?> - <?php echo $data[$i]['servicess_vehicle_name']?>
          </td>
          <td>
            <?php echo $data[$i]['servicess_name']?>
          </td>

          <td>
            <?php echo $data[$i]['servicess_nol']?>
          </td>
          <td>
            <?php echo $data[$i]['servicess_pelaksana']?>
          </td>
          <td>
            <?php echo $data[$i]['servicess_biaya']?>
          </td>
          <td>
            <?php echo $data[$i]['workshop_name']?>
          </td>
          <td>
            <?php echo $data[$i]['servicess_note']?>
          </td>
          <td>
            <?php echo date("d-m-Y", strtotime($data[$i]['servicess_date']))?>
          </td>
        </tr>
      <?php $no++; } ?>
    <?}else {?>
      <tr>
        <td>Data Not Available</td>
      </tr>
    <?} ?>
  </tbody>
</table>
</div>
