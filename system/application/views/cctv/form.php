<h2>Source: lewatmana.com</h2>
<form id="frmcctv" action="<?php echo base_url(); ?>cctv/save" method="post">
	<table width="100%" cellpadding="2" cellspacing="2" border="0">
		<tr>
			<td width="8%">ID</td>
			<td><input type="text" name="cctv_tag" value="" size="5" /></td>
		</tr>
		<tr>
			<td>Name</td>
			<td><input type="text" name="cctv_alias" value="" /></td>
		</tr>
		<tr>
			<td>Latitude</td>
			<td><input type="text" name="cctv_lat" value="" size="8" /></td>
		</tr>
		<tr>
			<td>Longitude</td>
			<td><input type="text" name="cctv_lon" value="" size="8" /></td>
		</tr>
		<tr>
			<td>Deskripsi</td>
			<td><textarea name="cctv_desc"></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Save" /></td>
		</tr>		
	</table>
</form>
