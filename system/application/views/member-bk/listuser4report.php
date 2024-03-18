<!--
<link rel="stylesheet" href="http://jqueryui.com/css/base.css" type="text/css" media="all" /> 
-->
<script>
	function pagereport(offset)
	{
		jQuery("#dvlistuser").html("loading...");
		if (! offset)  offset = 0;
		jQuery.post("<?php echo base_url(); ?>member/showvehicle/<?php echo $id; ?>/"+offset, jQuery("#frmsearchreport").serialize(),
			function(r)
			{
				jQuery("#dvlistuser").html(r.html);
			}
			, "json"
		);		
	}
	
	function frmsearchreport_onsubmit()
	{
		pagereport(0);
		return false;
	}
</script>
<div class="block-border">
<?php if (! isset($_POST['keyword'])) { ?>
<form id="frmsearchreport" name="form" onsubmit="javascript:return frmsearchreport_onsubmit();">
	<?php echo $this->lang->line("lsearch"); ?>&nbsp;&nbsp;
	<select name="search" id="search">
		<?php if ($this->sess->user_type != 2) { ?>
		<option value="login"><?php echo $this->lang->line("llogin"); ?></option>
		<option value="user"><?php echo $this->lang->line("luser"); ?></option>
		<?php } ?>
		<option value="vehicle"><?php echo $this->lang->line("lvehicle"); ?></option>
	</select>
	<input type='text' name="keyword" id="keyword" class='default' value="">
	<input type="submit" value="<?=$this->lang->line("lsearch"); ?>" />
</form>
<?php } ?>
</div>

<div class="block-border">
<div id="dvlistuser">
	<table class="table sortable no-margin" cellspacing="0" width="100%">
		<thead style="text-align: center;">
			<tr style="text-align: center;">			
				<?php if ($this->sess->user_type != 2) { ?>
				<th width="35%"><?=$this->lang->line("lusername"); ?></th>
				<?php } ?>
				<th style="text-align: center;"><?=$this->lang->line("lvehicle"); ?></th>
				<th width="18px;">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php 
					$lastusername = "";				
					for($i=0; $i < count($vehicles); $i++)
					{
			?>	
					<tr>	
						<?php if ($this->sess->user_type != 2) { ?>
							<?php if ($lastusername != $vehicles[$i]->user_name) { ?>
								<?php $lastusername = $vehicles[$i]->user_name; ?>
								<td>
								
								<?php if ($id=="mangeofence") 
								{
								?>
								<a href="<?=base_url();?>geofence/listallgeofence/<?=$vehicles[$i]->user_id;?>"><?php echo $vehicles[$i]->user_name; ?>&nbsp;
								<?php
								}
								else 
								{
										echo $vehicles[$i]->user_name; ?>&nbsp;
								<?php } ?>
								</td>
							<?php } else { ?>
								<td>&nbsp;</td>
							<?php } ?>
						<?php } ?>
						<td><?=$vehicles[$i]->vehicle_name;?> - <?=$vehicles[$i]->vehicle_no;?>&nbsp;</td>		
						<td>
						<a href="<?=base_url(); ?>trackers/<?=$id?>/<?=$vehicles[$i]->vehicle_device_name;?>/<?=$vehicles[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0"></a></a>
						<?php if($this->sess->user_id == "4043"){ ?>
							<a href="<?=base_url(); ?>geofencelive/manage/<?=$vehicles[$i]->vehicle_device_name;?>/<?=$vehicles[$i]->vehicle_device_host;?>"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0" title="Goefence Live (trial)"></a></a>
						<?php } ?>
						</td>
					</tr>
			<?php
					}
			?>
		</tbody>	
		<tfoot>
			<?php if (isset($paging)) { ?>
			<tr>
				<td colspan="<?php echo ($this->sess->user_type != 2) ? 3 : 2; ?>"><?php echo $paging; ?></td>
			</tr>
			<?php } ?>		
		</tfoot>	
	</table>
</div>
</div>
