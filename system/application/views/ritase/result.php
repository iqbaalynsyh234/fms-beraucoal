<script>
function info_delete(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>transporter/ritase/info_delete/', {id: v},
					function(r)
					{
						showdialog(r.html, "Delete Data Ritase!");
					}
					, "json"
				);
			}
			
function frmsearch_onsubmit()
	{
		var field = jQuery("#field").val();
		location = '<?php echo base_url();?>transporter/ritase/index/'+jQuery("#field").val()+"/"+jQuery("#keyword").val();
		return false;
	}
			
jQuery(document).ready(
	function()
	{
		showclock();
	}
	);
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
        <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
		<h1><?php echo "Ritase List"; ?> (<?php echo $total;?>)</h1>
		<fieldset class="grey-bg required">
        <legend><?=$this->lang->line("lsearchby");?></legend>
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />	
			
        <table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>
						<select id="field" name="field">
							<option value="All">All</option>
							<option value="ritase_geofence_name">Ritase Name</option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
					</td>
				</tr>
			</table>
        </fieldset>
        [ <a href="<?php echo base_url();?>transporter/ritase/add"><font color="#0000ff"><?php echo $this->lang->line("ladd")." "."Ritase"; ?></font></a> ]
		</form>
		<table width="100%" cellpadding="3" class="table sortable no-margin">
			<thead>
				<tr>
					<th width="1%">&nbsp;</td>
					<th width="2%"><?=$this->lang->line("lno"); ?></td>
					<th width="10%"><?php echo "Name" ?></td>
					<th width="10%"><?php echo "Status" ?></td>
					<th width="10%"><?php echo "Control" ?></td>
				</tr>
			</thead>
			<tbody>
				<?php
					if ($data) {
						for ($i=0; $i<count($data); $i++)
					{
				?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<th width="1%">&nbsp;</td>
					<td width="2%"><?=$i+1+$offset;?></td>
					<td width="10%"><?=$data[$i]->ritase_geofence_name;?></td>
					<td width="10%">
						<?php
							if ($data[$i]->ritase_status ==  1)
							{
								echo "Active";
							}
							else
							{
								echo "InActive";
							}
						?>
					</td>
					<td width="10%">
					<a href="javascript:info_delete(<?php echo $data[$i]->ritase_id;?>)">
						<img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="Delete Ritase" title="Delete Ritase">
					</a>
				</td>
				</tr>
				<?php } 
				} else { 
					echo "Data Not Available";
				}?>
			</tbody>
			
			<tfoot>
					<tr>
						<td colspan="12"><?php echo $paging;?></td>
					</tr>
			</tfoot>
			
		</table>
	</div>
    </div>
</div>