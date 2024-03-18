<script>
	jQuery(document).ready(
		function()
		{
			showclock();
		}
	);
	
	function showAlert(msg)
	{
		alert(msg);
	}
	
	function showAlertSuccess(msg, url)
	{
		alert(msg);
		
		location = url;
	}
	
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
    <div class="block-border">
        <form class="block-content form" id="frmadd" enctype="multipart/form-data" method="post" action="<?=base_url()?>poi/savecategory" target="ifrmsave">
		<?php if (isset($row)) { ?>	
		<h1><?=$this->lang->line("lpoi_category_update"); ?></h1>
		<?php } else { ?>
		<h1><?=$this->lang->line("lpoi_category_add"); ?></h1>
		<?php } ?>				
				<table width="100%" cellpadding="3" class="tablelist">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->poi_cat_id;?>" />
					<tr style="border: 0px;">
						<td style="border: 0px;">ID</td>
						<td style="border: 0px;"><?=$row->poi_cat_id;?></td>
					</tr>
					<?php } ?>
    			<tr style="border: 0px;">
						<td width="100" style="border: 0px;"><?=$this->lang->line('lpoi_category_name'); ?></td>
						<td style="border: 0px;"><input type="text" name="catname" id="catname" value="<?=isset($row) ? htmlspecialchars($row->poi_cat_name, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
    			<tr style="border: 0px;">
						<td width="100" style="border: 0px;"><?=$this->lang->line('licon'); ?></td>
						<td style="border: 0px;">
							<input class="button" type="file" name="userfile" id="userfile" value="" class="formdefault" />
							<?php if (isset($row)) { ?>	
							<br />
							<img src="<?=base_url()?>assets/images/poi/<?=$row->poi_cat_icon;?>" border="0" />
							<?php } ?>
						</td>
					</tr>					
    			<tr style="border: 0px;">
						<td style="border: 0px;">&nbsp;</td>
						<td style="border: 0px;">
                            <input class="button" type="submit" name="btnsave" id="btnsave" value=" Save " />
                            <input class="button" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>poi/category';" />
						</td>
					</tr>					
				</table>
			</form>		
	</div>
    </div>
	<iframe id="ifrmsave" name="ifrmsave" src="" style="width: 0px; height: 0px; border: 0px;" />
</div>
			
