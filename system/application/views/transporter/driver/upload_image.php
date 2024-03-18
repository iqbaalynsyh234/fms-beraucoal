<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<div id="main"><br />
    <div class="block-border">
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url();?>transporter/driver/save_image" method="post">
	<table class="table sortable no-margin">
		<tr>
			<td>
				<?php if (!$row_image) { ?>
					<img src="<?php echo base_url().$this->config->item("dir_photo").$this->config->item("default_photo_driver");?>" width="256px" height="256px" />
				<?php } else { ?>
					<img src="<?php echo base_url().$this->config->item("dir_photo").$row_image->driver_image_raw_name.$row_image->driver_image_file_ext;?>" width="256px" height="256px" />
				<?php } ?>
			</td>
			<td>
				<h2>Driver Information</h2>
				<hr>
				<small>Name : <?php if($row->driver_name) { echo $row->driver_name; } ?></small>
				<br />
				<small>ID Card : <?php if($row->driver_idcard) { echo $row->driver_idcard; } ?></small>
				<br />
				<small>Mobile : <?php if($row->driver_mobile) { echo $row->driver_mobile; }
									  if($row->driver_mobile2) { echo " " . " - " . " " . $row->driver_mobile2; }?>
				</small>
				<br />
				<small>Sex : <?php if($row->driver_sex) { echo $row->driver_sex; } ?></small>
				<hr>
				<br />
				<br />
				<?php if ($this->sess->user_group == 0) { ?>
				<small><span>Change Picture Driver</span></small>
				<?php echo $error_upload;?>
				<input type="hidden" name="driver_id" value="<?php echo $row->driver_id?>" />
				<input type="file" name="userfile" size="20" />
				<br />
				<input type="submit" value="Upload" />
				<input type="button" value="Cancel" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
				<br /><br />
				<small>Note : File images will be resize to 256 x 256 px</small>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">
				<small><?php if($row->driver_name) { echo $row->driver_name; } ?></small>
			</td>
		</tr>		
	</table>
	</form>
    </div>
	</div>
</div>