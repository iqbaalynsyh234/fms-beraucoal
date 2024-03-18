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
      <th width="1%" style="text-align:center">No</td>
        <th width="10%" style="text-align:center">Vehicle</th>
        <th width="3%" style="text-align:center">Driver</th>
        <th width="3%" style="text-align:center">Creator</th>
        <th width="10%" style="text-align:center">Tanggal Submit</th>
    </tr>
  </thead>
    <tbody>
      <?php
           if (isset($data)) {
             $no = 1;
             foreach ($data as $datanya) {?>
        <tr>
          <td valign="top" style="text-align:center;">
            <?php echo $no; ?>
          </td>
          <td valign="top" style="text-align:center;">
            <?php echo $datanya->driver_history_vehicle_no . " - " . $datanya->driver_history_vehicle_name; ?>
          </td>
          <td valign="top" style="text-align:center;">
            <?php echo $datanya->driver_history_driver_name; ?>
          </td>
          <td valign="top" style="text-align:center;">
            <?php echo $datanya->driver_history_username; ?>
          </td>
          <td valign="top" style="text-align:center;">
            <?php echo date("d-m-Y H:i:s", strtotime($datanya->driver_history_tanggal_submit));?>
          </td>
        </tr>
        <?php $no++;    }?>

          <?php }else{ ?>
            <tr>
              <td colspan="4">No Available Data</td>
            </tr>
            <?php
  			}
  			?>
    </tbody>
  <tfoot>
    <tr>
      <td colspan="11">&nbsp;</td>
    </tr>
  </tfoot>
</table>
</div>
