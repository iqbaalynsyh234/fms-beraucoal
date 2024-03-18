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
<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a> 
<div id="isexport_xcel">

	<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			<thead>
				<tr>
					<th width="3%" valign="top" style="text-align:center;">No</td>
					<th width="15%" valign="top" style="text-align:center;">Nomor Polisi</th>
					<th width="15%" valign="top" style="text-align:center;">Tanggal</th>
					<?php if(count($configtime) > 0){
						for($t=0;$t<count($configtime);$t++){ ?>
						<th width="10%" valign="top" style="text-align:center;"><?=date("H:i", strtotime($configtime[$t]->time_start));?></th>
					<?php }} ?>
				</tr>
			</thead>
			<tbody>
           <?php
			if(count($data) > 0){
			for($i=0;$i<count($data);$i++)
			{
			?>
            <tr>
                <td valign="top" style="text-align:center;"><?=$i+1;?></td>
				<td valign="top" style="text-align:center;"><?=$data[$i]->history_hour_no;?></td>
				<td valign="top" style="text-align:center;"><?=date("d-m-Y", strtotime($data[$i]->history_hour_date));?></td>	
				
				<?php if(count($configtime) > 0){
					for($t=0;$t<count($configtime);$t++){ 
					$xconfigtime = explode(":",$configtime[$t]->time_start);
					$configtime_head = $xconfigtime[0];
					$fieldtime_gps = "history_hour_".$configtime_head."_gps";
					$fieldtime_address = "history_hour_".$configtime_head."_address";
					$fieldtime_geofence = "history_hour_".$configtime_head."_geofence";
					$fieldtime_coor = "history_hour_".$configtime_head."_coor";
					?>
					<td valign="top" style="text-align:center;">
						<?=$data[$i]->$fieldtime_address;?>
						<?php 
							if($data[$i]->$fieldtime_geofence != 0){
								echo "<br /><small>".$data[$i]->$fieldtime_geofence."</small>";
							}
							if($checkdetail == 1){
								echo "<br /><small>(".$data[$i]->$fieldtime_gps.")</small>";
								echo "<br /><small>".$data[$i]->$fieldtime_coor."</small>";
							}
						?>
					</td>
				<?php }} ?>
			</tr>
            <?php }} else{ ?>
            <tr><td colspan="26">No Available Data</td></tr>
			<?php } ?>
            </tbody>
			<tfoot>
				
						
			</tfoot>
		</table>
</div>