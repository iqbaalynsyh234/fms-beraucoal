
<div class="block-border">
<form class="block-content form" id="frm" onsubmit="javascript: return frm_onsubmit()">		
<input type="hidden" name="job_id" id="job_id" value="<?=isset($row) ? $row->job_id : 0;?>"/>

<table width="100%">
	<tr>
		<td colspan="7"><h2>Job Detail Info [ Status: 
			<?php if($row->job_status == 1)
			{
				echo "Scheduled";
			}
			if ($row->job_status == 2)
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
		<td><?=$row->job_number;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td width="14%">Schedule Date</td>
		<td width="1%">:</td>
		<td><?=date("d-m-Y H:i",strtotime($row->job_date." ".$row->job_time)) ?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Vehicle</td>
		<td>:</td>
		<td>[<?=$row->job_mobil_no;?>] <?=$row->job_mobil_name;?></td>
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
		<td><?=date("d-m-Y H:i",strtotime($row->job_date. " ".$row->job_time)) ?></td>	
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Start From</td>
		<td>:</td>
		<td><?=$row->job_from;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Destination</td>
		<td>:</td>
		<td><?=$row->job_to;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Customer</td>
		<td>:</td>
		<td><?=$row->customer_company_name;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Dimension</td>
		<td>:</td>
		<td><?=$row->job_dimensi_p; ?>cm x <?=$row->job_dimensi_l;?>cm x <?=$row->job_dimensi_t; ?>cm</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Weight</td>
		<td>:</td>
		<td><?=$row->job_weight;?>kg</td>
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