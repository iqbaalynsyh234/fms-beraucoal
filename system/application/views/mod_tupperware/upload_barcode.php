<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<h2>Upload Data Barcode (*.csv)</h2>
			<hr />
			<?php echo form_open_multipart('transporter/tupperware/upload_barcode');?>
			<input type="file" id="file_upload" name="userfile" size="30" />
			<br /><br />
			<input type="submit" value="Upload" />
			<input type="button" name="btnlist" id="btnlist" value="List Barcode" onclick="location='<?=base_url()?>transporter/tupperware/barcode_list';" />
			<?php echo form_close();?>
		</div>
	</div>
</div>