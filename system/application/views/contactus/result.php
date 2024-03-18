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
					<th width="12%"><a href="#" onclick="javascript:order('created')"><?if ($sortby == 'created') { echo '<u>'; }?><?=$this->lang->line("lsent"); ?><?if ($sortby == 'created') { echo '</u>'; }?></th>
					<th width="10%"><a href="#" onclick="javascript:order('name')"><?if ($sortby == 'name') { echo '<u>'; }?><?=$this->lang->line("lsender_name"); ?><?if ($sortby == 'name') { echo '</u>'; }?></a></th>
					<th width="10%"><a href="#" onclick="javascript:order('email')"><?if ($sortby == 'email') { echo '<u>'; }?><?=$this->lang->line("lsender_mail"); ?><?if ($sortby == 'email') { echo '</u>'; }?></a></th>
					<th width="15%"><?=$this->lang->line("lreceipt"); ?></th>
					<th width="15%"><?=$this->lang->line("lsubject"); ?></th>
					<th><?=$this->lang->line("lmessage"); ?></th>
					<th width="15%"><?=$this->lang->line("lstatus"); ?></th>
					<th width="7%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td valign="top"><?=$i+1+$offset?></td>
					<td valign="top"><?=date("d/m/Y H:i:s", $data[$i]->created_fmt);?></td>
					<td valign="top"><?=$data[$i]->name;?></td>
					<td valign="top"><?=$data[$i]->email;?></td>
					<td valign="top"><?=$data[$i]->dest;?></td>
					<td valign="top"><?=$data[$i]->subject;?></td>
					<td valign="top"><?=$data[$i]->message;?></td>
					<td valign="top"><?=($data[$i]->status == 1) ? $this->lang->line("lnew") : $this->lang->line("lreplied");?></td>
					<td valign="top">
						<a href="<?=base_url();?>contactus/status/<?=$data[$i]->id;?>"><img src="<?=base_url();?>assets/images/data_preferences.png" border="0" alt="<?=$this->lang->line("lstatus"); ?>" title="<?=$this->lang->line("lstatus"); ?>"></a>
						<a href="<?=base_url();?>contactus/remove/<?=$data[$i]->id;?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"></a>
					</td>
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