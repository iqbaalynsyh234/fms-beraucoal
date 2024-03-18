<div id="istracking">
<table width="100%" id="tblresult" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
<thead>
	<tr>
		<th width="3%" style="text-align:center">No</td>
		<th style="text-align:center">Vehicle No</th>
		<th style="text-align:center">Date</th>
		<th style="text-align:center">Condition</th>
		<th style="text-align:center">Last Update</th>
	</tr>
</thead>
<tbody>
	<?php 
		if (isset($data))
		{
			$merah = 0; $kuning = 0;
			for ($i=0;$i<count($data);$i++)
			{
		?>
			<tr>
				<td><?php echo $i+1;?></td>
				<td><?php echo $data[$i]->device_problem_vehicle_no;?></td>
				<td><?php echo date("d-m-Y",strtotime($data[$i]->device_problem_date));?></td>
				<td>
					<?php 
						if($data[$i]->device_problem_condition == 1)
						{
							$kuning = $kuning + 1;
							echo "KUNING";
						}
						else
						{
							$merah = $merah + 1;
							echo "MERAH";
						}
					?>
				</td>
				<td><?php echo date("d-m-Y",strtotime($data[$i]->device_problem_lastupdate));?></td>
			</tr>
	<?php
			}
		}
		else
		{
	?>
		<tr>
			<td colspan="5">Data Not Available</td>
		</tr>
	<?php
		}
	?>
		<tr>
			<td colspan="4" style="text-align:right;">Total Kuning</td>
			<td colspan="4"><?php echo $kuning; ?></td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:right;">Total Merah</td>
			<td colspan="4"><?php echo $merah; ?></td>
		</tr>
		<tr>
			<td colspan="4" style="text-align:right;">Total</td>
			<td colspan="4"><?php echo $merah+$kuning; ?></td>
		</tr>
</tbody>
</table>
</div>
<a class="button" href="javascript:void(0);" id="export_xcel">Export to Excel</a>
<!--<input class="btn_search2" onclick="javascript:toexcell();" type="button" value="Export To Excel" />-->
<script>
	jQuery("#export_xcel").click(function() 
			{ 
				window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#istracking').html()));
			});
</script>
