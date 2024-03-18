<script>

function detailstatus(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>carmanagement/detail_tenant/', {id: v},
					function(r)
					{
						showdialog(r.html, "Tenant Detail Info");
					}
					, "json"
				);
			}
			
function frmsearch_onsubmit()
	{
		var field = jQuery("#field").val();
		location = '<?php echo base_url();?>carmanagement/data_tenant/'+jQuery("#field").val()+"/"+jQuery("#keyword").val();
		return false;
	}			



/*function remove_data(id)
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
		}*/
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
		
function tenant_image(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>carmanagement/upload_image/', {id: v},
					function(r)
					{
						showdialog(r.html, "Tenant Profile");
					}
					, "json"
				);
			}

function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#customer_status").hide();
    	switch(v)
		{
            case "customer_status":
				jQuery("#customer_status").show();
				break;	
			default:
				jQuery("#keyword").show();			
		}
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
	
</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
  <?=$navigation;?>
  <div id="main" style="margin: 20px;"> 
  <div class="block-border">
    
	<form class="block-content form" name="frmsearch" id="frmsearch" method="post" action="<?=base_url()?>carmanagement/data_tenant">
    <h1><?php echo "List Penyewa"; ?>(<?php echo $total;?>)</h1>
	<fieldset class="grey-bg required">
    <legend><?=$this->lang->line("lsearchby");?></legend>
    
	  <input type="hidden" name="offset" id="offset" value="" />
      <input type="hidden" id="sortby" name="sortby" value="" />
      <input type="hidden" id="orderby" name="orderby" value="" />
    <table width="100%" cellpadding="3" class="tablelist">
        <tr>
          
          <td>
              <select id="field" name="field" onchange="javascript:field_onchange()">
                <option value="All">All</option>
                <option value="customer_name">Nama Penyewa</option>
                <option value="customer_mobile">Mobile Phone</option>
                <option value="customer_phone">Phone</option>
                <option value="customer_email">Email</option>
                <option value="customer_address">Alamat</option>
                <option value="customer_status">Status Penyewa</option>
              </select>
			  <select id="customer_status" name="customer_status" style="display: none;">
					<option value="1">Recommended</option>
					<option value="0">Blacklist</option>
			  </select> 
              <input type="text" name="keyword" id="keyword" value="" class="formdefault" />
              <input type="submit" value="<?=$this->lang->line("lsearch");?>" />
            
            </td>
        </tr>
      </table>
	  </fieldset>
    [ <a href="<?php echo base_url();?>carmanagement/add_tenant"><font color="#0000ff"><?php echo " Tambah Penyewa"; ?></font></a> ]
    </form>
    <table width="100%" border="0" cellspacing="3" class="table sortable no-margin">
      <thead>
      <tr style="text-align: left;">
        <th width="2%" align="left"><?=$this->lang->line("lno"); ?></th>
        <th width="15%" >Name</th>
        <th width="20%" >Address</th>
        <th width="8%" >Phone</th>
        <th width="8%" >Mobile</th>
        <th width="10%" >Email</th>
        <th width="10%" >IDCard/Passport</th>
        <th width="10%" >Status</th>
        <th width="8%"  >&nbsp;</th>
      </tr>
      </thead> 
      <tbody>

          <?php $i = 1 ?>
          <?php foreach ($data as $bt): ?>
         
        <tr align="center"<?=($i%2) ? "class='odd'" : "";?> >
        
    	    <td><?php echo $i++ ?></td>
        
            <td><a href="javascript:tenant_image(<?php echo $bt->customer_id; ?>)"><font color='#0000ff'><?=$bt->customer_name;?></font></td></a></td>
            <td><?php echo $bt->customer_address?></td>
            <td><?php echo $bt->customer_phone?></td>
            <td><?php echo $bt->customer_mobile?></td>
            <td><?php echo $bt->customer_email?></td>
            <td><?php echo $bt->customer_idcard?></td>
			
			<?php if ($bt->customer_status == 1) { ?>
				<td><a href="javascript:detailstatus(<?php echo $bt->customer_id; ?>)"><font color='blue'><?php echo "Recommended" ?></font></td></a></td>
			<?php } ?>
			<?php if ($bt->customer_status == 0) { ?>
				<td><a href="javascript:detailstatus(<?php echo $bt->customer_id; ?>)"><font color='red'><?php echo "Blacklist" ?></font></td></a></td>
			<?php } ?>
					
            <td>
                <a href="<?=base_url();?>carmanagement/edit/<?=$bt->customer_id;?>"> <img src="<?=base_url();?>assets/rentcar/images/edit.png" border="0" height="20" width="20"
                    alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a> 
                <a href="javascript:tenant_image(<?php echo $bt->customer_id;?>)"> <img src="<?=base_url();?>assets/rentcar/images/uploadphoto.png" height="20" width="20" border="0" alt="<?php echo "Upload Photo"; ?>" title="<?php echo "Upload Photo"; ?>"></a>
				<!--<a href="<?=base_url();?>carmanagement/remove" onclick="javascript:remove_data(<?=$bt->customer_id;?>)"><img src="<?=base_url();?>assets/rentcar/images/delete.png" height="20" width="20" alt="Delete Data" title="Delete Data"></a>-->
				<a href="<?=base_url();?>carmanagement/remove" onclick="javascript:delete_data(<?=$bt->customer_id;?>)"><img src="<?=base_url();?>assets/rentcar/images/delete.png" border="0" height="20" width="20" alt="Delete Data" title="Delete Data"></a>				
            </td>
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
