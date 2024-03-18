<script>
function detailstatus(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>carmanagement/detail_status_tenant/', {id: v},
					function(r)
					{
						showdialog(r.html, "Tenant Blacklist Detail Info");
					}
					, "json"
				);
			}

function frmsearch_onsubmit()
	{
		var field = jQuery("#field").val();
		location = '<?php echo base_url();?>carmanagement/tenant_blacklist/'+jQuery("#field").val()+"/"+jQuery("#keyword").val();
		return false;
	}
			
jQuery(document).ready(
	function()
	{
		showclock();
			/* /* 
			jQuery("#sortby").val('<?=$sortby?>');
			jQuery("#orderby").val('<?=$orderby?>') */
			
			/* field_onchange();
			page(0);	 */	
	}
	);
	
	function delete_data(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>carmanagement/remove/' + id, {}, function(r){
					if (r.error) {
						alert(r.message);
						return;
					}else{
						alert(r.message);
						page();
						return;
					}
				}, "json");
			}		
		}
</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
  <?=$navigation;?>
  <div id="main" style="margin: 20px;">
  <div class="block-border">
  
     <form class="block-content form" name="frmsearch" id="frmsearch" method="post" action="<?=base_url()?>carmanagement/tenant_blacklist">
      <h1><?php echo "Tenant Blacklist"; ?> (<?php echo $total;?>)</h1>
	  <fieldset class="grey-bg required">
	  <legend><?=$this->lang->line("lsearchby"); ?></legend>
	  
	  <input type="hidden" name="offset" id="offset" value="" />
      <input type="hidden" id="sortby" name="sortby" value="" />
      <input type="hidden" id="orderby" name="orderby" value="" />
	  
      <table width="100%" cellpadding="3" class="tablelist">
        <tr>
          <td>
              <select id="field" name="field">
                <option value="All">All</option>
                <option value="customer_name">Tenant Name</option>
				<option value="customer_phone">Tenant Phone</option>
                <option value="customer_mobile">Tenant Mobile</option>
                <option value="customer_email">Tenant Email</option>
                <option value="customer_address">Tenant Address</option>
				
              </select>
              <input type="text" name="keyword" id="keyword" value="" class="formdefault" />
              <input type="submit" value="<?=$this->lang->line("lsearch");?>" />
            </td>
        </tr>
      </table>
	  </fieldset>
	
    <!--[ <a href="<?php echo base_url();?>carmanagement/add_tenant"><font color="#0000ff"><?php echo $this->lang->line("ladd")." "."Tenant"; ?></font></a> ]
	 <br />-->
	</form>
	
    <table width="100%" border="0" cellpadding="3" class="table sortable no-margin">
      <thead>
      <tr>
      <th width="2%" align="left"><?=$this->lang->line("lno"); ?></th>
		<th width="15%" >Name</th>
        <th width="19%" >Address</th>
        <th width="11%" >Phone</th>
        <th width="11%" >Mobile</th>
        <th width="15%" >Email</th>
        <th width="12%" >IDCard/Passport</th>
        <th width="10%" >Status</th>
        <!--<th width="6%"  >&nbsp;</th>-->
      </tr>
      </thead>
      <tbody> 
      <?php $i = 1 ?>
      <?php foreach ($data as $bt): ?>
	  
      <tr align="center"<?=($i%2) ? "class='odd'" : "";?>>
        <td align="center"><?php echo $i++ ?></td>
        <td><a href="javascript:detailstatus(<?php echo $bt->customer_id; ?>)"><font color='#0000ff'><?=$bt->customer_name;?></font></a></td>
        <td><?php echo $bt->customer_address ?></td>
        <td><?php echo $bt->customer_phone ?></td>
        <td><?php echo $bt->customer_mobile ?></td>
        <td><?php echo $bt->customer_email ?></td>
        <td><?php echo $bt->customer_idcard ?></td>
		
        <?php if ($bt->customer_status == 1) { ?>
				<td><a href="javascript:detailstatus(<?php echo $bt->customer_id; ?>)"><font color='blue'><?php echo "Recommended" ?></font></td></a></td>
			<?php } ?>
			<?php if ($bt->customer_status == 0) { ?>
				<td><a href="javascript:detailstatus(<?php echo $bt->customer_id; ?>)"><font color='red'><?php echo "Blacklist" ?></font></td></a></td>
			<?php } ?>
        
		<!--<td>
			<a href="<?=base_url();?>carmanagement/edit_blacklist/<?=$bt->customer_id;?>"> 
                <img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a> 
            <a href="#"onclick="javascript:changephoto(<?php echo $bt->customer_id;?>)"> <img src="<?=base_url();?>assets/images/driver_photo.png" 
                width="16px" height="16px" border="0" alt="<?php echo "Upload Photo"; ?>" title="<?php echo "Upload Photo"; ?>" /></a>
                
                <?php if ($this->sess->user_type == 1) { ?>
            <a href="remove/<?php echo $bt->customer_id;?>"> <img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="
					<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"> </a>
                <?php } ?>
		</td>-->
      </tr>
	  <?php endforeach ?>
      </tbody>
      
	  <tfoot>
		<tr>
			<td colspan="13"><?=$paging?></td>
		</tr>
      </tfoot>
    </table>
  </div>
</div>
