
<div class="block-border">
<form class="block-content form" id="frm" onsubmit="javascript: return frm_onsubmit()">		
<input type="hidden" name="muatan_id" id="muatan_id" value="<?=isset($row) ? $row->muatan_id : 0;?>"/>

<table width="100%">
	
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Vehicle</td>
		<td>:</td>
		<td>[<?=$row->muatan_vehicle_name;?>] <?=$row->muatan_vehicle_no;?></td>
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
		<td><?=date("d-m-Y H:i",strtotime($row->muatan_startdate. " ".$row->muatan_starttime)) ?></td>	
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Destination</td>
		<td>:</td>
		<td><?=$row->muatan_dest;?></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Weight</td>
		<td>:</td>
		<td><?=$row->muatan_weight;?>kg</td>
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