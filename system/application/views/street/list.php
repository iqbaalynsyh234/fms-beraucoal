		<script>
			function showmapex()
			{
				showmap();
			}
			function edit_street(id)
			{
				showdialog();
				jQuery.post('<?=base_url()?>street/edit/' + id, {},
					function(r)
					{
						showdialog(r.html, 'Edit Street');
					}
					, "json"
				);			
			}	
			function frmsearch_onsubmit()
			{
				var field = jQuery("#field").val();
				location = '<?php echo base_url(); ?>street/index/'+jQuery("#field").val()+"/"+jQuery("#keyword").val();
				
				return false;
			}
			
			function field_onchange()
			{
			}
			
			function gostreet(id)
			{
				jQuery.post("<?=base_url()?>street/docenter/"+id, {},
					function(r)
					{
						if (! map)
						{
							setcenter(r.lat, r.lng);
						}
						else
						{
							dosetcenter(r.lat, r.lng);
						}
					},
					"json"
				);
			}
			
			jQuery(document).ready(
				function()
				{
				}
			);
			</script>
        <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1><?=$this->lang->line("lstreet_list"); ?> (<?=$total;?>)</h1>		
		<div id="map" style="width: 100%; height: 400px;"></div>
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td width="10%">
                    <fieldset>
                    <legend>
                    <?=$this->lang->line("lsearchby");?>
                    </legend>
                    <select id="field" name="field" onchange="javascript:field_onchange()">
                        <option value="street_name"><?=$this->lang->line("lstreet");?></option>
                    </select>
                    <span id='dvkeyword' ><input type="text" name="keyword" id="keyword" value="" class="formdefault" /></span>
                    <input type="submit" value="<?=$this->lang->line("lsearch");?>" />
                    </fieldset>
                    </td>											
				</tr>								
			</table>
            <a class="button" href="<?=base_url();?>street/add"><font color="#0000ff"><?=$this->lang->line('ladd_street')?></font></a>
		</form>		
        
		
		<table class="table sortable no-margin" width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th><?=$this->lang->line("lstreet"); ?></th>
					<th width="30%"><?=$this->lang->line("lcoordinate"); ?></th>										
					<th width="60px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td><?=$data[$i]->street_name;?></td>
					<td><?=$data[$i]->street_polygon;?></td>
					<td>
							<a href="javascript:gostreet('<?=$data[$i]->street_id;?>')"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>
							<?php if ($data[$i]->updated) { ?>
							<a href="<?=base_url();?>street/remove/<?=$data[$i]->street_id;?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>">
<a href="#" onclick="javascript: edit_street('<?=$data[$i]->street_id;?>')"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
</a>
							<?php } else { ?>
							&nbsp;
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
							<td colspan="6"><?=$paging?></td>
					</tr>
			</tfoot>
		</table>
        <footer>
        <div class="float-right">
        <a href="#top" class="button"><img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/navigation-090.png" width="16" height="16" /> Page top</a>
        </div>		
        </footer>
