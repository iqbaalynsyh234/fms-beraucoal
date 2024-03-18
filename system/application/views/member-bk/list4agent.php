<!--
<link rel="stylesheet" href="http://jqueryui.com/css/base.css" type="text/css" media="all" /> 
-->

<table width="100%" cellpadding="3" class="tablelist">
	<thead>
		<tr>			
			<th width="50%"><?=$this->lang->line("lusername"); ?></th>
			<th><?=$this->lang->line("lvehicle"); ?></th>
			<th width="18px;">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if (count($vehicles)) 
		{
			foreach($vehicles as $key=>$vehicle)
			{
				for($i=0; $i < count($vehicle); $i++)
				{
		?>	
				<tr>	
					<?php if ($i == 0) { ?>
					<td><?=$vehicle[$i]->user_name;?>&nbsp;</td>		
					<?php } else { ?>
						<td>&nbsp;</td>		
					<?php } ?>
					<td><?=$vehicle[$i]->vehicle_name;?> - <?=$vehicle[$i]->vehicle_no;?>&nbsp;</td>		
					<td><a href="<?=base_url(); ?>map/realtime/<?=$vehicle[$i]->vehicle_device_name;?>/<?=$vehicle[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></a></a></td>
				</tr>
		<?php
				}
			}
		}
		?>
	</tbody>	
	
</table>