<!--
<link rel="stylesheet" href="http://jqueryui.com/css/base.css" type="text/css" media="all" /> 
-->

<table width="100%" cellpadding="3" class="tablelist">
	<thead>
		<tr>			
			<th><?=$this->lang->line("lvehicle"); ?></th>
			<th width="18px;">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php 
				for($i=0; $i < count($vehicles); $i++)
				{
		?>	
				<tr>	
					<td><?=$vehicles[$i]->vehicle_name;?> - <?=$vehicles[$i]->vehicle_no;?>&nbsp;</td>		
					<td><a href="<?=base_url(); ?>map/realtime/<?=$vehicles[$i]->vehicle_device_name;?>/<?=$vehicles[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></a></a></td>
				</tr>
		<?php
				}
		?>
	</tbody>	
	
</table>