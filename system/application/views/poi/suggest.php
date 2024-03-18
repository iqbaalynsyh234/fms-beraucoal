		<script>
			function showmapex()
			{
				showmap();
			}
			
			function poicat_onchange(i, elmt)
			{
				var html = jQuery("#img"+elmt.value).html();
				jQuery('#img1'+i).html(html);
			}
			
			function frmadd_onsubmit()
			{
				jQuery.post("<?=base_url()?>poi/dosuggest", jQuery("#frmadd").serialize(),
					function()
					{
						alert(r.message);
						
						if (r.error)
						{
							return;
						}
												
						location = r.redirect;
					}
					, "json"
				);
				return false;	
			}
			
			</script>
        <div class="block-border">
        <form class="block-content form" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
		<h1><?=$this->lang->line("lpoi_suggest_list"); ?> (<?=$total;?>)</h1>		
		<div id="map" style="width: 100%; height: 400px;"></div>
		<table class="table sortable no-margin" width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
			<thead>
				<tr>
					<th width="2%">No.</td>
					<th><?=$this->lang->line("lpoi_name"); ?></th>
					<th width="12%""><?=$this->lang->line("lsuggest_poi_category"); ?></th>
					<th width="20px">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php for($i=0; $i < count($categories); $i++) { ?>
				<?php if ($categories[$i]->poi_cat_icon) { ?>
					<span id="img<?=$categories[$i]->poi_cat_id;?>" style="display: none;"><img src="<?=base_url()?>assets/images/poi/<?=$categories[$i]->poi_cat_icon;?>" border="0" /></span>
				<?php } else { ?>
					<span id="img<?=$categories[$i]->poi_cat_id;?>" style="display: none;">&nbsp;</span>
				<?php } ?>											
			<?php } ?>				
			<?php
			for($i=0; $i < count($data); $i++)
			{
			?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<td><?=$i+1+$offset?></td>
					<td><?=$data[$i]->poi_name;?></td>
					<input type="hidden" name="poiname[]" value="<?=htmlspecialchars($data[$i]->poi_name, ENT_QUOTES);?>" />
					<td align="center">
						<select id="poicat" name="poicat[]" onchange="javascript:poicat_onchange(<?=$i?>, this)">
							<option value="0">--- <?=$this->lang->line('lpoi_category');?> ---</option>
						<?php for($j=0; $j < count($categories); $j++) { ?>
						<option value="<?=$categories[$j]->poi_cat_id?>"<?php if ($categories[$j]->poi_cat_id == $data[$i]->poi_category) { echo " selected"; }?>><?=$categories[$j]->poi_cat_name?></option>
						<?php } ?>
					</select>
					</td>
					<td>
					<?php if ($data[$i]->poi_icon) { ?>
					<span id='img1<?=$i?>'><img src="<?=base_url()?>assets/images/poi/<?=$data[$i]->poi_icon;?>" border="0" /></span>					
					<?php } else { ?>
					<span id='img1<?=$i?>'></span>
					<?php } ?>					
				</td>
				</tr>
			<?php
			}
			?>
			</tbody>
			<?php if ($total > 0) { ?>
			<tfoot>
            <tr>
                <td colspan="6"><input class="button" type="submit" value=" Save " /></td>
            </tr>
			</tfoot>
			<?php } ?>
		</table>
	</form>
    <footer>
    <div class="float-right">
    <a href="#top" class="button"><img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/navigation-090.png" width="16" height="16" /> Page top</a>
    </div>		
    </footer>
    </div>