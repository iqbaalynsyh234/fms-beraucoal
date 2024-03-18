			<script>
				function frmconfig_onsubmit()
				{
					jQuery.post("<?php echo base_url(); ?>home/saveconfig", jQuery("#frmconfig").serialize(),
						function (r)
						{
							jQuery("#dialog").dialog("close");
						}
						, "json"
					);				
					
					return false;
				}				
			</script>
			<form name="frmconfig" id="frmconfig" onsubmit="javascript: return frmconfig_onsubmit()">				
				<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td colspan="3"><h2><?=$this->lang->line("lapplication");?></h2></td>
				</tr>
    			<tr>
						<td width="130"><?=$this->lang->line("lmeta_keywords");?></td>
						<td width="1">:</td>
						<td>
							<textarea name="metakeywords" id="metakeywords" cols="100" rows="5"><?php if (isset($settings['metakeywords'])) { echo htmlspecialchars($settings['metakeywords'], ENT_QUOTES); } else { echo htmlspecialchars($this->config->item('APPKEYWORDS'), ENT_QUOTES); } ?></textarea>
						</td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lmeta_description");?></td>
						<td>:</td>
						<td>
								<textarea name="metadescription" id="metadescription" cols="100" rows="5"><?php if (isset($settings['metadescription'])) { echo htmlspecialchars($settings['metadescription'], ENT_QUOTES); } else { echo htmlspecialchars($this->config->item('APPDESCRIPTION'), ENT_QUOTES); } ?></textarea>
						</td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lcutoffengine_desclimer");?></td>
						<td>:</td>
						<td>
								<textarea name="cutoffengine" id="cutoffengine" cols="100" rows="5"><?php if (isset($settings['cutoffengine'])) { echo htmlspecialchars($settings['cutoffengine'], ENT_QUOTES); } else { echo htmlspecialchars($this->config->item('CUTOFFENGINEDESCLIMER'), ENT_QUOTES); } ?></textarea>
						</td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lmaxhistory");?></td>
						<td>:</td>
						<td>
								<input name="maxhist" id="maxhist" value="<?php if (isset($settings['maxhist'])) { echo htmlspecialchars($settings['maxhist'], ENT_QUOTES); } else { echo "3"; } ?>" maxlength="2" size="3" /> <?=$this->lang->line("lmonthlabel");?>
						</td>
					</tr>
    			<tr>
						<td>By Pass Password</td>
						<td>:</td>
						<td>
								<input type="password" name="bypasspassword" id="bypasspassword" value="<?php if (isset($settings['bypasspassword'])) { echo htmlspecialchars($settings['bypasspassword'], ENT_QUOTES); } else { echo "gpsjayatrackervilani666630"; } ?>" />
						</td>
					</tr>

    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
							<input type="submit" name="btnsave" id="btnsave" value=" Save " />
							<input type="button" name="btncancel" id="btncancel" value=" Reset " onclick="document.frmconfig.reset()" />
						</td>
					</tr>					
				</table>
			</form>		
