<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
			<h2>Upload Data SO From Excel (*.xls)</h2>
			<hr />
			<br />
			<?php echo form_open_multipart('transporter/dosj_all/do_upload_dosj');?>
			<input type="file" id="file_upload" name="userfile" size="30" />
			<br /><br />

			<input type="submit" value="Upload" />
	
			<?php echo form_close();?>
		</div>
	</div>
</div>