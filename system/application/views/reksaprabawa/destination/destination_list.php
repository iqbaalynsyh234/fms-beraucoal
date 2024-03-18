<script type="text/javascript" src="<?= base_url() ?>/assets/kim/js/ajaxfileupload.js"></script>

<script>

function destination_take_vehicle(v)
{
	showdialog();
	jQuery.post('<?php echo base_url(); ?>destination/take_vehicle/', {id: v},
					function(r)
					{
						showdialog(r.html, "Destination Vehicle");
					}
					, "json"
				);
}
			
function frmsearch_onsubmit()
	{
		var field = jQuery("#field").val();
		location = '<?php echo base_url();?>destination/index/'+jQuery("#field").val()+"/"+jQuery("#keyword").val();
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
		<h1><?php echo "Destination"; ?></h1>
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
							<option value="destination_name">Destination Name</option>
							<option value="destination_vehicle">Vehicle No</option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
					</td>
				</tr>
			</table>
		</form>
		[ <a href="<?php echo base_url();?>destination/add"><font color="#0000ff"><?php echo "Add Destination"; ?></font></a> ]
		<p>
		
		<table width="100%"  cellpadding="3"  class="tablelist">
		
			<thead>
				<tr style="text-align:center">
					<th width="1%">&nbsp;</td>
					<th width="2%" align="center"><?=$this->lang->line("lno"); ?></td>
					<th align="center"><?php echo "Destination Name" ?></td>
					<th align="center"><?php echo "Vehicle Name" ?></td>
					<th align="center"><?php echo "Button" ?></td>
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
					<td width="2%"><?=$i+1+$offset?></td>
					<td><?=$data[$i]->destination_name1;?><br/></td>
					
					<td>
						<?php 
							if (isset($rows_vehicle))
							{
								foreach($rows_vehicle as $rowvehicle)
								{
									if ($data[$i]->destination_vehicle == $rowvehicle->vehicle_id)
									{
										echo $rowvehicle->vehicle_name." ".$rowvehicle->vehicle_no;
									}
									else
									{}
								}
							}
						?>
					</td>
					
					
					<td width="10%"  style="text-align:center;">
				
						<a href="<?=base_url();?>destination/edit/<?=$data[$i]->destination_id;?>">
							<img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>">
						</a>
						
						<a href="javascript:destination_take_vehicle(<?php echo $data[$i]->destination_id;?>)">
							<img src="<?=base_url();?>assets/images/truckicon.png" border="0" alt="Take Vehicle" title="Take Vehicle" width="20px" height="20px"/>
						</a>
						
						<a href="<?=base_url();?>destination/delete/<?=$data[$i]->destination_id;?>">
							<img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>">
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
						<td colspan="12"><?=$paging?></td>
					</tr>
			</tfoot>
			
		</table>
	</div>
</div>