<script>
jQuery(document).ready(
		function()
		{
			field_onchange();			
		}
	);
	
function checkall()
	{
		var found = false;
		
		<?php for($i=0; $i < count($data_geofence); $i++) { ?>
		jQuery("#geofence<?php echo $data_geofence[$i]->geofence_id; ?>").attr("checked", jQuery("#vehicleall").attr("checked"));
		found = true;
		
		<?php } ?>
		
		if (! found)
		{
			alert("No Value Checked");
			return;
		}
		
	}
	
function deleteall()
{
	var found = false;
	var serialize = "src=<?php echo $sourceid; ?>";
	
	<?php for($i=0; $i < count($data_geofence); $i++) { ?>
		if (jQuery("#geofence<?php echo $data_geofence[$i]->geofence_id; ?>").attr("checked"))
		{
		found = true;
		serialize += "&geoid[]="+<?php echo $data_geofence[$i]->geofence_id; ?>;
		}
	<?php } ?>
		
		if (! found)
		{
			alert("No Value Checked");
			return;
		}
		
		jQuery.post('<?php echo base_url(); ?>geofence/deleteallbyid', serialize,
			function(r)
			{
				if (r.error)
				{			
					alert(r.message);
					return;
				}
				
				alert(r.message);
				location.reload();
				
			}
			, "json"
		);
	
}

function frmsearch_onsubmit()
 {
		var field = jQuery("#field").val();
		var id = jQuery("#id").val();
		var vid = jQuery("#vid").val();
		var vids = vid.split('@');
		
		location = '<?php echo base_url();?>geofence/listallgeofence/'+id+"/"+vids[0]+"/"+jQuery("#field").val()+"/"+jQuery("#keyword").val();
		return false;
 }
 
 function field_onchange()
 {
	var v = jQuery("#field").val();
	switch(v)
	{
		case "vehicle":
		jQuery("#vid").show();
		jQuery("#keyword").hide();
		break;
		default:
		jQuery("#keyword").show();
		jQuery("#vid").hide();
	}
 }
 
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<br /><br />
	<h2>List Geofence = <?php echo $total_list?></h2><br />
	<small>Note: Menghindari Overload Bandwidth Pada Server System, Geofence hanya di tampilkan Max 20 Geofence</small>
	<br/>
	<br />
	<form id="search" name="search" onsubmit="javascript:return frmsearch_onsubmit()">
	<input type="hidden" value="<?php echo $id;?>" name="id" id="id" />
		<table>
			<tr>
				<td>
					<label style="font-size:12px">Search By : </label>
				</td>
				<td>
					<select name="field" id="field" onchange="javascript: return field_onchange();">
						<option id="all" name="all">All</option>
						<option id="vehicle" name="vehicle" value="vehicle">Vehicle</option>
						<option id="geofence_name" name="geofence_name" value="geofence_name">Geofence Name</option>
					</select>
				</td>
				<td>
					<select name="vid" id="vid">
					<?php for($i=0;$i<count($vehicle);$i++) { ?>
					<option value="<?php echo $vehicle[$i]->vehicle_device;?>"><?php echo $vehicle[$i]->vehicle_name . " " .$vehicle[$i]->vehicle_no;?></option>
					<?php } ?>
					</select>
					<input type="text" name="keyword" id="keyword" />
				</td>
				<td>
					<input type="submit" name="submit" id="submit" value="Search" />
					<input type="button" name="btndelete" id="btndelete" value="Delete" onclick="javascript:deleteall()" />
				</td>
			</tr>
		</table>
	</form>
	<table width="100%" cellpadding="3" class="tablelist">
	<thead>
		<tr>
			<td width="2%"><input type="checkbox" id="vehicleall" name="vehicleall" value="-1" onclick="javascript:checkall()" /></td>
			<td width="2%">No</td>
			<td>Vehicle</td>
			<td>Geofence</td>
		</tr>
	</thead>
	<tbody>
	<?php for ($i=0;$i<count($data_geofence);$i++)
	{ ?>
	<tr>
		<td><input type="checkbox" id="geofence<?=$data_geofence[$i]->geofence_id;?>" name="geo_id[]" value="<?=$data_geofence[$i]->geofence_id;?>" /></td>
		<td><?php echo $i+1;?></td>
		<td>
			<?php 
			foreach ($vehicle as $v)
			{
				if ($data_geofence[$i]->geofence_vehicle == $v->vehicle_device)
				{
					echo $v->vehicle_name ." ". $v->vehicle_no;
				}
			}
			?>
		</td>
		<td>
			<?php if (isset($data_geofence[$i]->geofence_name) && $data_geofence[$i]->geofence_name != "") {
				echo $data_geofence[$i]->geofence_name;
				}
			?>
		</td>
	</tr>
	<?php } ?>
	<tfoot>
		<tr>
			<td colspan="4"><?php echo $paging;?></td>
		</tr>
	</tfoot>
	</tbody>
	</table>
	</div>	
</div>