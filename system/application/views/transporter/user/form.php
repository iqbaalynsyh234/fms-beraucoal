<script>
	
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    /// <summary>
	    /// Returns the max zOrder in the document (no parameter)
	    /// Sets max zOrder by passing a non-zero number
	    /// which gets added to the highest zOrder.
	    /// </summary>    
	    /// <param name="opt" type="object">
	    /// inc: increment value, 
	    /// group: selector for zIndex elements to find max for
	    /// </param>
	    /// <returns type="jQuery" />
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
		
	jQuery(document).ready(
		function()
		{
			showclock();
			type_onchange();
			
			jQuery("#birthdate").datepicker(
				{
							dateFormat: 'dd/mm/yy'
						, 	startDate: '01/01/1900'
						, 	showOn: 'button'
						, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);
			payment_type_onclick();
			loadgroup();	
		}
	);
	
	function payment_type_onclick()
	{
		var abodement = jQuery("input[id=user_payment_type][value=1]").attr("checked");
		if (abodement)
		{
			//jQuery("#dvpaymentpulse").show();
		}
		else
		{
			//jQuery("#dvpaymentpulse").hide();
		}
	}
	
	function frmadd_onsubmit()
	{
			jQuery.post("<?=base_url()?>transporter/user/save", jQuery("#frmadd").serialize(),
			function(r)
			{
				if (r.error)
				{
					alert(r.message);
					return false;
				}
				
				alert(r.message);
				location = r.redirect;
			}
			, "json"
		);
		return false;
	}
	
	function type_onchange()
	{
		var  v = jQuery("#type").val();
		if ((v == 1) || (v == 4))
		{
			jQuery("#tragent").hide();
			jQuery("#tragentadmin").hide();	
			jQuery("#trcompanies").hide();		
			jQuery("#trmanpass").hide();
			jQuery("#trmanengine").hide();
			jQuery("#trmanprofile").hide();
			jQuery("#trpermissiongroup").hide();			
		}
		else
		{
			if (v == 2)
			{
				jQuery("#tragentadmin").hide();
			}
			else
			{
				jQuery("#tragentadmin").show();
			}
			jQuery("#tragent").show();
		}		
		
		if (v == 2)
		{
			jQuery("#dvpaymentgroup").show();
			jQuery("#dvpaymenttype").show();
			jQuery("#dvpaymentperiod").show();
			jQuery("#dvpaymentamount").show();
			jQuery("#dvpaymentpulse").show();
		}
		else
		{
			jQuery("#dvpaymentgroup").hide();
			jQuery("#dvpaymenttype").hide();
			jQuery("#dvpaymentperiod").hide();
			jQuery("#dvpaymentamount").hide();
			jQuery("#dvpaymentpulse").hide();
		}
	}

	function loadgroup()
	{
		jQuery.post("<?php echo base_url(); ?>transporter/customer/options<?php if (isset($row)) { echo "/".$row->user_group; } ?>", jQuery("#frmadd").serialize(),
			function(r)
			{
				if (r.empty)
				{
					jQuery("#trgroup").hide();
					return;
				}

				jQuery("#trgroup").show();
				jQuery("#usergroup").html(r.html);
			}
			, "json"
		);
	}
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<article class="container_12">
        <section class="grid_12">
        <div class="block-border">
        <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
		<?php if (isset($row)) { ?>			
		<h1><?=$this->lang->line("luser_edit"); ?></h1>
		<?php } else { ?>
		<h1><?=$this->lang->line("luser_add"); ?></h1>
		<?php } ?>
        
        <fieldset class="grey-bg required">
        <legend>
        Required Information
        </legend>
						
				<table width="100%" cellpadding="3" class="table sortable no-margin">
					<?php if (isset($row)) { ?>					
					<input type="hidden" id="id" name="id" value="<?=$row->user_id;?>" />
					<tr>
						<td>ID</td>
						<td>:</td>
						<td><?=$row->user_id;?></td>
					</tr>
					<?php } ?>
				<tr>
					<td colspan="3"><h2><?=$this->lang->line("llogin_info");?></h2></td>
				</tr>
				<?php if (isset($row)) { ?>
    			<tr>
						<td width="130"><?=$this->lang->line("llogin");?></td>
						<td width="1">:</td>
						<!--<td><?=isset($row) ? htmlspecialchars($row->user_login, ENT_QUOTES) : "";?></td>-->
						<td><input type="text" name="username" id="username" value="<?=isset($row) ? htmlspecialchars($row->user_login, ENT_QUOTES) : "";?>" class="formdefault" /></td>
						
					</tr>
    			<tr>

				<?php } else { ?>
    			<tr>
						<td width="130"><?=$this->lang->line("llogin");?></td>
						<td width="1">:</td>
						<td><input type="text" name="username" id="username" value="<?=isset($row) ? htmlspecialchars($row->user_login, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lpassword");?></td>
						<td>:</td>
						<td><input type="password" name="pass" id="pass" value="" class="formdefault" /></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lconfirm_password");?></td>
						<td>:</td>
						<td><input type="password" name="cpass" id="cpass" value="" class="formdefault" /></td>
					</tr>					

    			<?php } ?>
    			
    			<tr>
						<td style="display:none;"><?=$this->lang->line("ltype");?></td>
						<td style="display:none;">:</td>
						<td style="display:none;">
						
						<select id="type" name="type" onchange="javascript:type_onchange()">
							<option value="2"><?php echo $this->config->item("transporter_user_type_name");?></option>
						</select>
						
							</td>
					</tr>	
				
    			<tr id="tragent">
						<td style="display:none;"><?=$this->lang->line("lagent");?></td>
						<td style="display:none;">:</td>
						<td style="display:none;">
							<select id="agent" name="agent">
								<?php for($i=0; $i < count($agents); $i++) { 
								if ($agents[$i]->agent_id == $this->sess->user_agent){ ?>
								<option value="<?=$agents[$i]->agent_id?>" <? if (isset($row) && ($row->user_agent == $agents[$i]->agent_id)) { ?>selected<?php } ?>><?=$agents[$i]->agent_name?></option>
								<?php }} ?>
							</select>
							</td>
					</tr>		
					<tr id="tragentadmin">							
						<td colspan="2" style="display:none;">&nbsp;</td>
						<td style="display:none;"><input type="checkbox" name="agent_admin" id="agent_admin" value="1"<? if (isset($row) && ($row->user_agent_admin == 1)) { ?>checked<?php } ?> /><?=$this->lang->line("lasadmin4agent");?></td>
					</tr>
				<tr>
					<td colspan="3"><h2><?=$this->lang->line("lprivate_info");?></h2></td>
				</tr>
    			<tr>
						<td><?=$this->lang->line("lname");?></td>
						<td>:</td>
						<td><input type="text" name="name" id="name" value="<?=isset($row) ? htmlspecialchars($row->user_name, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>	
    			<tr>
						<td><?=$this->lang->line("lemail");?></td>
						<td>:</td>
						<td><input type="text" name="email" id="email" value="<?=isset($row) ? htmlspecialchars($row->user_mail, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>								
    			<tr>
						<td><?=$this->lang->line("llicense");?></td>
						<td>:</td>
						<td><input type="text" name="license" id="license" value="<?=isset($row) ? htmlspecialchars($row->user_license_id, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lsex");?></td>
						<td>:</td>
						<td>
						<select id="sex" name="sex" onchange="javascript:type_onchange()">
							<option value="1" <? if ((! isset($user)) || ($user->user_sex == 'M')) { ?>selected<?php } ?>><?=$this->lang->line("lmale");?></option>
							<option value="1" <? if (isset($user) && ($user->user_sex == 'F')) { ?>selected<?php } ?>><?=$this->lang->line("lfemale");?></option>
						</select>
							
							</td>
					</tr>	
    			<tr>
						<td><?=$this->lang->line("lbirthdate");?></td>
						<td>:</td>
						<td><input type='text' name="birthdate" id="birthdate"  class="date-pick" value="<?php if (isset($row)) echo $row->user_date_fmt; ?>"  maxlength='10'></td>
					</tr>	
    			<tr>
						<td><?=$this->lang->line("lprovince");?></td>
						<td>:</td>
						<td><input type="text" name="province" id="province" value="<?=isset($row) ? htmlspecialchars($row->user_province, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lcity");?></td>
						<td>:</td>
						<td><input type="text" name="city" id="city" value="<?=isset($row) ? htmlspecialchars($row->user_city, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("laddress");?></td>
						<td>:</td>
						<td><textarea name="address" id="address" class="formdefault"><?=isset($row) ? htmlspecialchars($row->user_address, ENT_QUOTES) : "";?></textarea></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lmobile");?></td>
						<td>:</td>
						<td><input type="text" name="mobile" id="mobile" value="<?=isset($row) ? htmlspecialchars($row->user_mobile, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
    			<tr>
						<td><?=$this->lang->line("lphone");?></td>
						<td>:</td>
						<td><input type="text" name="phone" id="phone" value="<?=isset($row) ? htmlspecialchars($row->user_phone, ENT_QUOTES) : "";?>" class="formdefault" /></td>
					</tr>
			
			<tr id="trpermissiongroup">
				<td colspan="3"><h2><?php echo "Customer Information";?></h2></td>
			</tr>			
			<tr id="trmanprofile">
				<td style="display:none";><?=$this->lang->line("lcan_manage_profile");?></td>
				<td style="display:none";>:</td>
				<td style="display:none";>
					<input type="radio" name="manprofile" id="manprofile" value="1"<?php if ((! isset($row)) || ($row->user_change_profile == 1)) { echo " checked"; } ?> /> <?=$this->lang->line("lyes");?>
					<input type="radio" name="manprofile" id="manprofile" value="2"<?php if (isset($row) && ($row->user_change_profile == 2)) { echo " checked"; } ?> /> <?=$this->lang->line("lno");?>
				</td>
			</tr>
			<tr id="trmanengine">
						<td style="display:none";><?=$this->lang->line("lcan_manage_engine");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";><input type="checkbox" name="manengine" id="manengine" value="1" <?php if (! isset($row)) { echo "checked"; } else if ($row->user_engine == 1) { echo "checked"; } ?> /></td>
			</tr>
			<tr id="trmanpass" style="display:none";>
						<td style="display:none";><?=$this->lang->line("lmanage_password");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";><input type="checkbox" name="manpasswd" id="manpasswd" value="1" <?php if (! isset($row)) { echo "checked"; } else if ($row->user_manage_password == 1) { echo "checked"; } ?> /></td>
			</tr>			
				<?php if (count($companies)) { ?>
			<tr id="trcompanies">
						<td style="display:none";><?=$this->lang->line("lcompany");?></td>
						<td style="display:none";>:</td>
						<td style="display:none";>
							<select name="usersite" id="usersite" onchange="javascript:loadgroup()">
							<?php foreach($companies as $company) { 
								if ( $company->company_id == $this->sess->user_company) {?>
								<option value="<?php echo $company->company_id; ?>" <?php if (isset($row) && ($row->user_company == $company->company_id)) { echo "selected"; } ?>><?php echo $company->company_name; ?></option>
							<?php } } ?>
							</select>		
						</td>
			</tr>
			<tr id="trgroup" style="display: none;">
				<td><?php echo "Customer Group"; ?></td>
				<td>:</td>
				<td><div id="usergroup"></div></td>
			</tr>
				<?php } ?>
			<tr id="dvpaymentgroup">
				<td colspan="3" style="display:none";><h2><?=$this->lang->line("lpayment_info");?></h2></td>
			</tr>				
			<tr id="dvpaymenttype" style="display:none";>
					<td style="display:none";><?=$this->lang->line("lpayment_type");?></td>
					<td style="display:none";>:</td>
					<td style="display:none";>
						<input type="radio" name="user_payment_type" id="user_payment_type" value="1" onclick="javascript:payment_type_onclick()" <?php if (isset($row) && ($row->user_payment_type == 1)) { echo "checked"; } ?>/>&nbsp;<?=$this->lang->line("labodement");?>
						&nbsp;&nbsp;<input type="radio" name="user_payment_type" id="user_payment_type" value="2" onclick="javascript:payment_type_onclick()" <?php if (isset($row) && ($row->user_payment_type == 2)) { echo "checked"; } ?>/>&nbsp;<?=$this->lang->line("lflat");?>
					</td>
			</tr>
			<tr id="dvpaymentperiod">
					<td style="display:none";><?=$this->lang->line("lpayment_period");?></td>
					<td style="display:none";>:</td>
					<td style="display:none";><input type="text" name="user_payment_period" id="user_payment_period" value="<?php if (isset($row) && $row->user_payment_period) { echo $row->user_payment_period; } ?>" class="formshort" style="text-align: right;" /> <?= strtolower($this->lang->line("lmonthlabel"));?></td>
			</tr>
			<tr id="dvpaymentamount">
					<td style="display:none";><?=$this->lang->line("lpayment_total");?></td>
					<td style="display:none";>:</td>
					<td style="display:none";>Rp. <input type="text" name="user_payment_amount" id="user_payment_amount" value="<?php if (isset($row) && $row->user_payment_amount) { echo number_format($row->user_payment_amount, 0, "", ","); } ?>" class="formshort" style="text-align: right;" /></td>
			</tr>						
			<tr id="dvpaymentpulse">
					<td colspan="2" style="display:none";>&nbsp;</td>
					<td style="display:none";><input type="checkbox" name="user_payment_pulsa" id="user_payment_pulsa" value="1" <?php if (isset($row) && ($row->user_payment_pulsa == 1)) { echo "checked"; } ?>/> <?=$this->lang->line("lnot_include_pulse");?></td>
			</tr>	
			
    			<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>
								<input type="submit" name="btnsave" id="btnsave" value=" Save " />
								<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>transporter/user';" />
						</td>
					</tr>					
				</table>
			</form>
            </div>
    </section>
    </article>		
	</div>
</div>
			
