<div style="position: absolute; margin: 0; padding: 0; z-index: 100%; width: 100%;"> 
	<div id="main"><br />
    <div class="block-border">
	<form method="post" enctype="multipart/form-data" action="#" method="post">
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
				<h2>Informasi Penyewa</h2>
				<hr>
				<big><b>Name : <?php if($row->customer_name) { echo $row->customer_name; } ?></b></big>
				<br />
				<big>Mobile  : <?php if($row->customer_mobile) { echo $row->customer_mobile; } ?></big>
				<br />
				<big>Phone   : <?php if($row->customer_phone) { echo $row->customer_phone; } ?></big>
				<br />
				<big>Address : <?php if($row->customer_address) { echo $row->customer_address; } ?></big>
				<br />
				<big>ID Card : <?php if($row->customer_idcard) { echo $row->customer_idcard; } ?></big>
				<br />
				<big>Email   : <?php if($row->customer_email) { echo $row->customer_email; } ?></big>
				<br />
				<hr>
				<?php if ($row->customer_status == 1) { ?>
					<big><b>Status : <?php echo "Recommended" ?></b></big>
				<?php } ?>
				<?php if ($row->customer_status == 0) { ?>
					<big><b>Status : <font color='red'><?php echo "Blacklist" ?></font></b></big>
				<?php } ?>
				
				<br />
				<big><b>Keterangan : <?php if($row->customer_keterangan) { echo $row->customer_keterangan; } ?></b></big>
				<hr>
				
				<input type="hidden" name="customer_id" value="<?php echo $row->customer_id?>" />
				<br />
				<input type="button" value="Close" onclick="javascript:jQuery('#dialog').dialog('close');" /> 
				<hr>
				
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">
				<big><?php if($row->customer_name) { echo $row->customer_name; } ?></big>
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