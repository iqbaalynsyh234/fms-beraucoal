
<div class="block-border">
<form class="block-content form" id="frm" onsubmit="javascript: return frm_onsubmit()">		
<input type="hidden" name="job_id" id="job_id" value="<?=isset($row) ? $row->transporter_job_id : 0;?>"/>

<table width="100%">
	<tr>
		<td colspan="7"><h2>Job Detail Info [ Status: 
			<?php if($row->transporter_job_status == 1)
			{
				echo "On Going";
			}
			if ($row->transporter_job_status == 2)
			{
				echo "Delivered";
			}
			
			?> ]</h2>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Job Number</td>
		<td>:</td>
		<td><?=$row->transporter_job_number;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td width="14%">Start Date</td>
		<td width="1%">:</td>
		<td><?=date("d-m-Y H:i",strtotime($row->transporter_job_deliv_date." ".$row->transporter_job_deliv_time)) ?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Vehicle</td>
		<td>:</td>
		<td>[<?=$row->transporter_job_vehicle_no;?>] <?=$row->transporter_job_vehicle_name;?></td>
	</tr>	
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Driver</td>
		<td>:</td>
		<td><?=$row->driver_name;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Datetime</td>
		<td>:</td>
		<td><?=date("d-m-Y H:i",strtotime($row->transporter_job_date. " ".$row->transporter_job_time)) ?></td>	
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Start From</td>
		<td>:</td>
		<td><?=$row->transporter_job_from;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Destination</td>
		<td>:</td>
		<td><?=$row->transporter_job_to;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Client</td>
		<td>:</td>
		<td><?=$row->transporter_job_client;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Dimension</td>
		<td>:</td>
		<td><?=$row->transporter_job_dimensi_p; ?>cm x <?=$row->transporter_job_dimensi_l;?>cm x <?=$row->transporter_job_dimensi_t; ?>cm</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Weight</td>
		<td>:</td>
		<td><?=$row->transporter_job_weight;?>kg</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	
	<tr>
		<td colspan="3">	
			<input type="button" value="Close" name="close" id="close" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
			<img id="loader2" src="<?php echo base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
		</td>
	</tr>
</table>
</form>
</div>