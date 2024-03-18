<select name="kabkota" id="kabkota">
	<?php for($i=0; $i < count($kotas); $i++) { ?>
	<option value="<?php echo $kotas[$i]->KAB_KOTA; ?>"><?php echo $kotas[$i]->KAB_KOTA; ?></option>
	<?php } ?>	
</select>
