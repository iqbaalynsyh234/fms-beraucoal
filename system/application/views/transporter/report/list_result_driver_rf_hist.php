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
	  <th width="7%" style="text-align:center">Datetime</th>
	  <th width="7%" style="text-align:center">Driver</th>
      <th width="7%" style="text-align:center">Vehicle</th>
      <th width="15%" style="text-align:center">Location</th>
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
            <?php echo date("d-m-Y H:i:s", strtotime($datanya->datetime));?>
          </td>
		   <td valign="top" style="text-align:center;">
		    <?php
				if (isset($drivers)) {
					foreach ($drivers as $drv) {
						if ($drv->driver_idcard == $datanya->item) {
							echo $drv->driver_name." - ".$drv->driver_idcard; 
						}
					}
				}
			?>
          </td>
          <td valign="top" style="text-align:center;">
            <?php
				if (isset($vehicles)) {
					foreach ($vehicles as $vhcl) {
						if ($vhcl->vehicle_device == $datanya->device) {
							echo $vhcl->vehicle_no; 
						}
					}
				}
			?>
          </td>
		  <td valign="top" style="text-align:center;">
			<?php 
				$position = $this->gpsmodel->GeoReverse($datanya->latitude,$datanya->longitude);
				$position_name = $position->display_name; 
			?>
			<a href="https://www.google.com/maps?q=<?=$datanya->latitude.",".$datanya->longitude;?>" target="_blank"><?=$position_name;?></a>
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
