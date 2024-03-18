<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<div id="main"><br />
    <div class="block-border">
	<form method="post" enctype="multipart/form-data" action="<?php echo base_url();?>carmanagement/save_image" method="post">
	<table class="table sortable no-margin">
		<tr>
			<td>
				<?php if (!$row_image) { ?>
					<img src="<?php echo base_url().$this->config->item("dir_photo").$this->config->item("default_photo_tenant");?>" width="256px" height="256px" />
				<?php } else { ?>
					<img src="<?php echo base_url().$this->config->item("dir_photo").$row_image->customer_image_client_name;?>" width="256px" height="256px" />
				<?php } ?>
			</td>
			<td>
				<h2>Tenant Information</h2>
				<hr>
				<small>Name : <?php if($row->customer_name) { echo $row->customer_name; } ?></small>
				<br />
				<small>Mobile : <?php if($row->customer_mobile) { echo $row->customer_mobile; } ?></small>
				<br />
				<small>Phone : <?php if($row->customer_phone) { echo $row->customer_phone; } ?></small>
				<br />
				<small>Address : <?php if($row->customer_address) { echo $row->customer_address; } ?></small>
				<hr>
				<br />
				
				<?php if ($this->sess->user_group == 0) { ?>
				<small><span>Change Picture Tenant</span></small>
				<?php echo $error_upload;?>
				<input type="hidden" name="customer_id" value="<?php echo $row->customer_id?>" />
				<input type="file" name="userfile" size="20" />
				<br />
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
				<small><?php if($row->customer_name) { echo $row->customer_name; } ?></small>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>		
	</table>
	</form>
    </div>
	</div>
</div>