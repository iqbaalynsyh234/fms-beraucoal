			
			<script>
					function frmchangephoto_onsubmit()
					{
						jQuery("#loader").show();
						
						jQuery.ajaxFileUpload
						(
							{
								url:'<?=base_url()?>driver/savephoto/<?=$row->driver_id?>',
								secureuri:false,
								fileElementId:'fileToUpload',
								dataType: 'json',
								success: function (data, status)
								{
									if(typeof(data.error) != 'undefined')
									{
										if(data.error != '')
										{
											jQuery("#loader").hide();
											alert(data.error);
										}else
										{
											jQuery("#loader").hide();
											alert(data.msg);
											jQuery("#dialog").dialog("close");
											location.reload();
										}
									}
								},
								error: function (data, status, e)
								{
									jQuery("#loader").hide();
									alert(e);
								}
							}
						)
												
						return false;
					}
				</script>
			<form id="frmchangephoto" onsubmit="javascript: return frmchangephoto_onsubmit()">	
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td>Driver Name</td>
						<td>:</td>
						<td><?=$row->driver_name?></td>
					</tr>
    			<tr>
    			<tr>
						<td>Old Photo</td>
						<td>:</td>
						<td>
							<?php
							if($row->driver_pict != ""){
							$filename = $this->config->item("driver_photo_path") . $row->driver_pict;
							$fileurl = base_url(). "assets/kim/media/foto_driver/" . $row->driver_pict;
							if(file_exists($filename)){
							?>
							<img src="<?=$fileurl;?>" border="1" width="85" height="100" alt="<?=$row->driver_pict?>" title="<?=$row->driver_pict?>" />
							<?php }else{ ?>
							<img src="<?=base_url();?>assets/kim/images/no_photo.jpg" border="1" width="85" height="100" alt="<?=$row->driver_pict?>" title="<?=$row->driver_pict?>" />
							<?php } ?>
						<?php
						}else{
						?>
							<img src="<?=base_url();?>assets/kim/images/no_photo.jpg" border="1" width="85" height="100" alt="No Photo Available" title="No Photo Available" />
						<?php }	?>
							
						</td>
					</tr>
    			<tr>    
    							
    			<tr>
						<td>New Photo</td>
						<td>:</td>
						<td><input id="fileToUpload" type="file" size="45" name="fileToUpload" class="formdefault"></td>
					</tr>
    								
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="javascript:jQuery('#dialog').dialog('close');" />
								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
						</td>
					</tr>					
				</table>
			</form>		

