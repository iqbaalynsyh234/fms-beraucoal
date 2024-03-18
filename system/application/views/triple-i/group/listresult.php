		<table width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th style="text-align:left;"><a href="#" onclick="javascript:order('group_name')"><?php echo "Customer";?></a></th>
					<th width="20%" style="display:none;"><?php echo "Group"; ?></th>
					<th width="30%" style="display:none;"><?=$this->lang->line("lparent"); ?></th>
					<th width="40px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td style="text-align:left;"><?=$data[$i]->group_name;?></td>
					<td style="display:none;"><?php echo $data[$i]->company_name; ?></td>
					<td style="display:none;"><?=$data[$i]->parent_name;?></td>
					<td>
							<a href="<?=base_url();?>transporter/customer/add/<?=$data[$i]->group_id;?>"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
							<?php if ($data[$i]->used) { ?>
							&nbsp;
							<?php } else { ?>
							<a href="<?=base_url();?>transporter/customer/remove/<?=$data[$i]->group_id;?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"></a>
							<?php } ?>
					</td>					
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="5"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
