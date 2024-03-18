<script>
	jQuery(document).ready(
		function()
		{
			showclock();
		}
	);		
	
	function page(n)
	{
		if (! n) n = 0;
		
		document.frmsearch.action = document.frmsearch.action + "/" + n;
		document.frmsearch.submit();
	}			
	
</script>
<div style="position: absolute; margin: 0px; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
    <div class="block-border">
        <form class="block-content form" name="frmsearch" id="frmsearch" action="<?php echo base_url(); ?>poi/category" method="get">
		<h1><?=$this->lang->line("lpoi_category_list"); ?> (<?=$total;?>)</h1>
        <table width="100%" cellpadding="3" class="tablelist">
        <tr>
        <td width="10%">
            <fieldset>
            <legend>
                <?=$this->lang->line("lsearchby");?>
            </legend>
            <select id="field" name="field">
                <option value="poi_cat_name"><?=$this->lang->line("lpoi_category_name");?></option>
            </select>
            <input type="text" name="keyword" id="keyword" value="<?php if (isset($_GET['keyword'])) echo htmlspecialchars(trim($_GET['keyword']), ENT_QUOTES); ?>" />
            <input type="submit" value="<?=$this->lang->line("lsearch");?>" />
            </fieldset>
            <a class="button" href="<?=base_url();?>poi/add_category"><font color="#0000ff">Add</font></a>
        </td>
        </table>
		</form>		
		<table class="table sortable no-margin" width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th><?=$this->lang->line("lpoi_category_name"); ?></th>
					<th width="100px;" style="text-align: center"><?=$this->lang->line("licon"); ?></th>
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
					<td><?=$data[$i]->poi_cat_name;?></td>
					<?php if ($data[$i]->poi_cat_icon) { ?>
					<td style="text-align: center"><img src="<?=base_url()?>assets/images/poi/<?=$data[$i]->poi_cat_icon;?>" border="0" /></td>
					<?php } else { ?>
					<td style="text-align: center">&nbsp;</td>
					<?php } ?>

					<td>
							<?php if (($this->sess->user_type == 1) || ($this->sess->user_id == $data[$i]->poi_cat_creator)) { ?>
							<a href="<?=base_url();?>poi/add_category/<?=$data[$i]->poi_cat_id;?>"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>								
							<a href="<?=base_url();?>poi/remove_category/<?=$data[$i]->poi_cat_id;?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"></a>
							<?php } else { ?>
							&nbsp;
							<?php } ?>
					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
                <tr>
                    <td colspan="4"><?=$paging?></td>
                </tr>
			</tfoot>
		</table>
	</div>
    </div>
</div>
