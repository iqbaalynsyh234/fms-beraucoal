<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<div id="main"><br />
    <div class="block-border">
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url();?>transporter/ritase/remove" method="post" >
	<h2>Are you sure, you want to delete this data ??</h2>
	<table class="table sortable no-margin">
		<tr>
			<input type="text" name="id_ritase" id="id_ritase" style="display:none" value="<?php echo $row->ritase_id;?>" >
			<td>Ritase Name :</td>
			<td><?php echo $row->ritase_geofence_name;?></td>
			<td><input type="submit" value="Delete"></td>
			<td><input type="button" value="Cancel" onclick="location='<?php echo base_url();?>transporter/ritase'" ></td>
		</tr>
	</table>
	</form>
    </div>
	</div>
</div>