<script type="text/javascript" src="<?= base_url() ?>/assets/kim/js/ajaxfileupload.js"></script>
<script>

	
function frmsearch_onsubmit()
	{
		var field = jQuery("#field").val();
		location = '<?php echo base_url();?>fibconfigslide/index/'+jQuery("#field").val()+"/"+jQuery("#keyword").val();
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
		<br />&nbsp;
		<h1><?php echo "Config FIB Slide List"; ?> (<?php echo $total;?>)</h1>
		<h2><?=$this->lang->line("lsearch"); ?></h2>
		<form name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
			<input type="hidden" name="offset" id="offset" value="" />
			<input type="hidden" id="sortby" name="sortby" value="" />
			<input type="hidden" id="orderby" name="orderby" value="" />	
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td>
						<?=$this->lang->line("lsearchby");?>
					</td>
					<td>
						<select id="field" name="field">
							<option value="All">All</option>
							<option value="fib_config_name">Name</option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
					</td>
				</tr>
			</table>
		</form>
		<!--[ <a href="<?php echo base_url();?>fibconfigslide/add"><font color="#0000ff"><?php echo $this->lang->line("ladd")." "."Config FIB"; ?></font></a> ]-->
		<p>
		<table width="100%" cellpadding="3" class="tablelist">
			<thead>
				<tr style="text-align:center">
					<th width="1%" style="text-align:center";>&nbsp;</td>
					<th width="2%"style="text-align:center"><?=$this->lang->line("lno"); ?></td>
					<th style="text-align:center"><?php echo "Name" ?></td>
					<th style="text-align:center"><?php echo "Vehicle (per Slide)" ?></td>
					<th style="text-align:center"><?php echo "App ID" ?></td>
					<th style="text-align:center"><?php echo "Status Active" ?></td>
					<th style="text-align:center"><?php echo "Control" ?></td>
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
					<td valign="top" style="text-align:center;" width="2%"><?=$i+1+$offset?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->fib_config_name;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->fib_config_vehicle;?></td>
					<td valign="top" style="text-align:center;"><?=$data[$i]->fib_config_app_id;?></td>
					<td valign="top" style="text-align:center;" >
						<?php if ($data[$i]->fib_config_status == 1){ ?>
							YES
						<?php }else{ ?>
							NO
						<?php } ?>
					</td>
					<td valign="top" style="text-align:center;">
						<a href="<?=base_url();?>fibconfigslide/edit/<?=$data[$i]->fib_config_id;?>">
							<img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>">
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
						<td colspan="12"><!--<?=$paging?>--></td>
					</tr>
			</tfoot>
			
		</table>
	</div>
</div>