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
        <form class="block-content form" id="frmadd" enctype="multipart/form-data" method="post" action="<?=base_url()?>poi/doimport" target="ifrmsave">
		<h1><?=$this->lang->line("lpoi_import"); ?></h1>
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr style="border: 0px;">
                    <td width="100" style="border: 0px;">
                        <fieldset>
                        <legend><?=$this->lang->line("lfile"); ?></legend>
                        <input class="button" type="file" name="userfile" id="userfile" value="" />
                        <input class="button" type="submit" name="btnsave" id="btnsave" value=" Import " />
                        <input class="button" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>poi';" />
                        </fieldset>
                    </td>
                </tr>				
				</table>
        </form>	
	</div>
    </div>	
</div>
<iframe id="ifrmsave" name="ifrmsave" src="" style="width: 700px; height: 300px; border: 1px; solid #cccccc; display: none;" />
			
