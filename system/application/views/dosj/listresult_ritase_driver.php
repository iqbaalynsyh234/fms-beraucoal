<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
	<thead>
		<tr>
			<th width="2%">NO</td>
			<th style="text-align:center;">Driver</th>
			<th style="text-align:center;">Total Ritase</th>
		</tr>
	</thead>
	
	<tbody>
	<?php  
		for($i=0; $i < count($data); $i++)
		{
            unset($data_rit);
            $data_rit = explode("|",$data[$i]);
            if ($data_rit[1]!=0)
            {
	?>
		<tr <?=($i%2) ? "class='odd'" : "";?>>
			<td style="text-align:center;"><?=$i+1?></td>
			<td style="text-align:left;"><?=$data_rit[0];?></td>
			<td style="text-align:center;"><?=$data_rit[1];?></td>
		</tr>
	<?php
            }
		}
	?>
	</tbody>
    
	<tfoot>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
	</tfoot>
</table>