<script>
function frmedit_onsubmit()
{
	jQuery("#loaderupdate").show();
	jQuery.post("<?=base_url()?>comment/save_comment", jQuery("#frmedit").serialize(),
	function(r)
	{
		jQuery("#loaderupdate").hide();
		alert(r.message);

								if (r.error)
								{
									return;
								}
								jQuery("#modaladdcomment").hide();
							}
							, "json"
						);

						return false;

}


</script>
<div id="wrapper">
    <form id="frmedit"onsubmit="javascript: return frmedit_onsubmit()">
	<div id="main"><br />
		<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
		<input type="hidden" name="vid" id="vid" value="<?php echo $rowv->vehicle_id; ?>" />
		<input type="hidden" name="vname" id="vname" value="<?php echo $rowv->vehicle_name; ?>" />
		<input type="hidden" name="vno" id="vno" value="<?php echo $rowv->vehicle_no; ?>" />
		<input type="hidden" name="vdevice" id="vdevice" value="<?php echo $rowv->vehicle_device; ?>" />
		<input type="hidden" name="comment_table" id="comment_table" value="<?php echo $comment_table; ?>" />
		<?php if(isset($row) && count($row) > 0){ ?>
			<tr>
				<td>Last Comment</td>
				<td>:</td>
				<td><?php echo $row->comment_title; ?></td>
			</tr>
			<tr>
				<td>Last Created</td>
				<td>:</td>
				<td><?php echo $row->comment_creator_name; ?></td>
			</tr>
			<tr>
				<td>Last Modified</td>
				<td>:</td>
				<td><?php echo date("d-m-Y H:i:s", strtotime($row->comment_datetime)); ?></td>
			</tr>
		<?php } ?>

			<tr>
				<td>Comment</td>
				<td>:</td>
				<td><input type="text" size="45" name="title" id="title" value="" maxlength="160" class="formdefault" />
					<input type="button" class="btn btn-warning" value="Cancel" onclick="closemodaladdcomment();" />
          <input type="submit" class="btn btn-primary" name="btnsave" id="btnsave" value=" Save " />
					<img id="loaderupdate" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;"></td>
			</tr>

		</table>
		</div>
	</form>
</div>
