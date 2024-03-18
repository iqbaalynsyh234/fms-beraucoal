		<script>
			function changeport(vid)
			{
				if (! confirm("<?php echo $this->lang->line('lchangeport_confirm');?>")) return;
				
				jQuery.post("<?=base_url();?>user/changeport/"+vid, {},
					function(r)
					{
						alert(r.message);
						if (r.error) return;
						
						jQuery("#changeport"+vid).hide();
					}
					, "json"
				);				
			}
			</script>
		<table width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th width="13%"><a href="#" onclick="javascript:order('announcement_created')"><?if ($sortby == 'announcement_created') { echo '<u>'; }?><?=$this->lang->line("lcreated"); ?><?if ($sortby == 'announcement_created') { echo '</u>'; }?></a></th>
					<?php if ($this->sess->user_type == 1) { ?>
					<th width="15%"><a href="#" onclick="javascript:order('announcement_owner')"><?if ($sortby == 'announcement_created') { echo '<u>'; }?><?=$this->lang->line("lagent"); ?><?if ($sortby == 'announcement_owner') { echo '</u>'; }?></a></th>
					<?php } ?>
					<th><?=$this->lang->line("lmessage"); ?></th>
					<?php if ($canedit) { ?>
					<th width="8%"><?=$this->lang->line("lstatus"); ?></th>
					<th width="7%">&nbsp;</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top"><?=date("d/m/Y H:i:s", $data[$i]->announcement_created_fmt);?></td>
					<?php if ($this->sess->user_type == 1) { ?>
					<td valign="top"><?=$data[$i]->announcement_owner;?></td>
					<?php } ?>
					<td valign="top"><?=$data[$i]->announcement_message;?></td>
					<?php if ($canedit) { ?>
					<td valign="top"><?=($data[$i]->announcement_status == 1) ? $this->lang->line("lactive") : $this->lang->line("linactive");?></td>					
					<td valign="top">
						<a href="<?=base_url();?>announcement/status/<?=$data[$i]->announcement_id;?>"><img src="<?=base_url();?>assets/images/data_preferences.png" border="0" alt="<?=$this->lang->line("lstatus"); ?>" title="<?=$this->lang->line("lstatus"); ?>"></a>
						<a href="<?=base_url();?>announcement/add/<?=$data[$i]->announcement_id;?>"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
					</td>
					<?php } ?>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
					<tr>
							<td colspan="9"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>