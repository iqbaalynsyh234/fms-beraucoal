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
			field_onchange();
			page(0);		
			
			<?php if ($act == "loadconfirmation") { ?>
			confirmation('<?php echo $id; ?>');
			<?php } ?>	
			
			jQuery("#period1").datepicker(
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
			
			jQuery("#period2").datepicker(
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
			
		}
	);
	
	function page(p)
	{
		if (ginvoiceclock) clearTimeout(ginvoiceclock);
		
		jQuery("#offset").val(p);
		jQuery("#result").html("<?=$this->lang->line("lwait_loading_data");?>");
		
		jQuery.post("<?=base_url();?>invoice/search/"+p, jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#result").html(r.html);		
				jQuery("#totalinvnotpaid").html(r.totalnotpaid);
				jQuery("#totalivprocessed").html(r.totalprocessed);
				jQuery("#totalinvpaid").html(r.totalpaid);
				
				<?php if ($this->sess->user_type != 2) { ?>
				ginvoiceclock = setTimeout("page("+p+")", 30000);
				<?php } ?>
				//jQuery("#total").html(r.total);
			}
			, "json"
		);
	}
		
	function frmsearch_onsubmit()
	{
		page(0);
		return false;
	}
	
	function field_onchange()
	{
		var vfield = jQuery("#field").val();
		if (vfield == "")
		{
			jQuery("#keyword").hide();
			jQuery("#dvliststatus").hide();
			jQuery("#dvlistagent").hide();
			jQuery("#dvlistdate").hide();
		}
		else
		if (vfield == "invoice_status")
		{
			jQuery("#keyword").hide();
			jQuery("#dvliststatus").show();			
			jQuery("#dvlistagent").hide();
			jQuery("#dvlistdate").hide();
		}
		else
		if (vfield == "agent")
		{
			jQuery("#keyword").hide();
			jQuery("#dvliststatus").hide();
			jQuery("#dvlistagent").show();
			jQuery("#dvlistdate").hide();
		}
		else
		if (vfield == "date")
		{
			jQuery("#keyword").hide();
			jQuery("#dvliststatus").hide();
			jQuery("#dvlistagent").hide();
			jQuery("#dvlistdate").show();
		}
		else
		{
			jQuery("#keyword").show();
			jQuery("#dvliststatus").hide();
			jQuery("#dvlistagent").hide();
			jQuery("#dvlistdate").hide();
		}
	}
	
		function confirmation(invoiceid)
		{
			showdialog();
			jQuery.post('<?=base_url()?>invoice/confirmation/', {id: invoiceid},
				function(r)
				{
					if (r.error)
					{
						alert("Retry");
						return;
					}
					
					showdialog(r.html, '<?=$this->lang->line("lpayment_confirmation"); ?>');					
				}
				, "json"
			);			
			
		}
		
		function approved(invoiceid)
		{
			if (! confirm("<?php echo $this->lang->line("lconfirm_approved_invoice"); ?>")) return;
			jQuery.post('<?=base_url()?>invoice/changestatus/', {id: invoiceid, status: 2},
				function(r)
				{
					if (r.error)
					{
						alert("Access denied");
						return;
					}
					
					alert('<?=$this->lang->line("lapproved_success"); ?>');
					frmsearch_onsubmit();					
				}
				, "json"
			);			
			
		}
	
		function rejected(invoiceid)
		{
			if (! confirm("<?php echo $this->lang->line("lconfirm_rejected_invoice"); ?>")) return;
			jQuery.post('<?=base_url()?>invoice/changestatus/', {id: invoiceid, status: 3},
				function(r)
				{
					if (r.error)
					{
						alert("Access denied");
						return;
					}
					
					alert('<?=$this->lang->line("lrejected_success"); ?>');					
					frmsearch_onsubmit();
				}
				, "json"
			);			
			
		}
		
		function payment(id)
		{
			var status = jQuery("#trpayment"+id).css("display");
			if (status == "none")
			{
				jQuery("#trpayment"+id).show();
				jQuery("#plusminus"+id).html("(-)");
			}
			else
			{
				jQuery("#trpayment"+id).hide();
				jQuery("#plusminus"+id).html("(+)");
			}
		}
		
		var ginvoiceclock = null;

</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
		<h1><?php echo $this->lang->line("linvoice_list"); ?></h1>
		<h3><?php printf("%s: <span id='totalinvnotpaid'></span>, %s: <span id='totalivprocessed'></span>, %s: <span id='totalinvpaid'></span>", $this->lang->line("lnot_payment"), $this->lang->line("lproessed"), $this->lang->line("lpaid")); ?></h3>
		<br />
		<?php if ($this->sess->user_type != 2) { ?>
		<h2><?=$this->lang->line("lsearch"); ?></h2>		
		<form name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
			<table width="100%" cellpadding="3" class="tablelist">
				<tr>
					<td width="10%"><?=$this->lang->line("lsearchby");?></td>
					<td>
						<select id="field" name="field" onchange="javascript:field_onchange()">	
								<option value=""><?php echo $this->lang->line("lall"); ?></option>
								<option value="agent"><?=$this->lang->line("lagent");?></option>								
								<option value="invoice_no"><?=$this->lang->line("linvoice_no");?></option>
								<option value="user_login"><?=$this->lang->line("llogin");?></option>		
								<option value="date"><?=$this->lang->line("lprinted");?></option>						
								<option value="invoice_status"><?=$this->lang->line("lstatus");?></option>
						</select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<span id="dvliststatus">
							<select name="status" id="status">
								<option value="1"><?php echo $this->lang->line("lnot_payment"); ?></option>
								<option value="2"><?php echo $this->lang->line("lproessed"); ?></option>
								<option value="3"><?php echo $this->lang->line("lpaid"); ?></option>
								<option value="4"><?php echo $this->lang->line("lrejected"); ?></option>
							</select>
						</span>
						<span id="dvlistagent">
							<select name="agent" id="agent">
								<?php foreach($agents as $agent) { ?>
								<option value="<?php echo $agent->agent_id; ?>"><?php echo $agent->agent_name; ?></option>
								<?php } ?>
							</select>
						</span>
						<span id="dvlistdate">
							<input type='text' name="period1" id="period1"  class="date-pick" value=""  maxlength='10'> 
							<?php echo $this->lang->line("luntil"); ?>
							<input type='text' name="period2" id="period2"  class="date-pick" value=""  maxlength='10'>
						</span>
					</td>
				</tr>
				<tr>
					<td style="border: 0px;">&nbsp;</td>
					<td style="border: 0px;"><input type="submit" value="<?=$this->lang->line("lsearch");?>" /> <input type="button" value="<?=$this->lang->line("lrefresh");?>" onclick="javascript: page(0);" /></td>
				</tr>
			</table>
		</form>
		<?php } ?>
		<div id="result"></div>		
	</div>
</div>

