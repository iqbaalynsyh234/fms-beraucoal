			<script>
					function frmchangepass_onsubmit()
					{
						jQuery.post("<?=base_url()?>user/savepass/<?=$row->user_id?>", jQuery("#frmchangepass").serialize(),
							function(r)
							{
								alert(r.message);
								
								if (r.error)
								{								
									return;									
								}								
								
								jQuery("#dialog").dialog("close");
							}
							, "json"
						);
						
						return false;
					}
				</script>
            <div class="block-border">
			<form class="block-content form" id="frmchangepass" onsubmit="javascript: return frmchangepass_onsubmit()">				
				<table width="100%" cellpadding="3" class="tablelist">
    			<tr>
						<td colspan="2">
                        <fieldset>
                        <legend><?=$this->lang->line("llogin");?></legend>
                        <?=$row->user_login?>
                        </fieldset>
                        </td>
                        <td>
                        <fieldset>
                        <legend>
                        <?=$this->lang->line("lname");?>
                        </legend>
                        <?=$row->user_name?>
                        </fieldset>
                        </td>
					</tr>
                    
    			<tr>    
    				
                    <?php if ($this->sess->user_type == 2) { ?>
    			<tr>
						<td>
                        <fieldset>
                        <legend>
                        <?=$this->lang->line("loldpassword");?>
                        </legend>
                        <input type="password" name="oldpass" id="oldpass" value="" class="formdefault" size="30" />
                        </fieldset>
                        </td>
 				
    			<?php } ?>				

						<td>
                        <fieldset>
                        <legend>
                        <?=$this->lang->line("lnewpassword");?>
                        </legend>
                        <input type="password" name="pass" id="pass" value="" class="formdefault" size="30" />
                        </fieldset>
                        </td>
						<td>
                        <fieldset>
                        <legend>
                        <?=$this->lang->line("lconfirm_password");?>
                        </legend>
                        <input type="password" name="cpass" id="cpass" value="" class="formdefault" size="30" />
                        </fieldset>
                        </td>
					</tr>					
    			<tr>
						<td colspan="3">
                        <fieldset>
                        <legend>Control</legend>
                        <input class="button" type="submit" name="btnsave" id="btnsave" value=" Save " />
                        <input class="button" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="javascript:jQuery('#dialog').dialog('close');" />
                        </fieldset>
						</td>
					</tr>					
				</table>
			</form>		
            </div>