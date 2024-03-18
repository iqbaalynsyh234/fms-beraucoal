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
  <table width="100%" cellpadding="3" class="table sortable no-margin">
  			<thead>
  				<tr>
  					<th width="2%">No</td>
  					<th width="20%">In</th>
  					<th>Out</th>
  					<th>Status Engine</th>
  					<th>Duration</th>
  					<th>Jarak</th>
  				</tr>
  			</thead>
  			<tbody>
        <?php if ($data != "empty") {?>
          <?php for ($i=0; $i< sizeof($data); $i++){?>
          <tr <?=($i%2) ? "class='odd'" : "";?>>
            <td width="2%"><?=$i+1;?></td>
            <td width="10%">
              <?=$data[$i]['trip_mileage_geofence_start'];?> <br>
              <?=$data[$i]['trip_mileage_start_time'];?>
            </td>
            <td width="10%">
              <?=$data[$i]['trip_mileage_geofence_end'];?><br>
              <?=$data[$i]['trip_mileage_end_time'];?>
            </td>
            <td width="10%">
              <?php
              // $status_engine = ;
              if ($data[$i]['trip_mileage_engine'] == 0) {
                echo "Off";
              }else {
                echo "On";
              }
              ?><br>
            </td>
            <td width="10%">
              <?=$data[$i]['trip_mileage_duration'];?>
            </td>
            <td width="10%">
              <?php if ($data[$i]['kondisi1'] == "1") {?>
                <?php echo $data[$i]['trip_mileage_trip_mileage2']." Km";?>
              <?php }else {?>
                <?php echo ($data[$i]['trip_mileage_trip_mileage']+$data[$i]['trip_mileage_trip_mileage2'])." Km";?>
              <?php } ?>
            </td>
          </tr>
          <?php }	?>
      <?php }else { ?>
        <table>
          <?php echo "Data Not Available"; ?>
        </table>
      <?php } ?>
  			</tbody>
  		</table>
</div>
