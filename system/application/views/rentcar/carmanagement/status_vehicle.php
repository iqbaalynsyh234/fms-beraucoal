<script>
jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
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
			jQuery("#alldate").attr("checked", true);
			jQuery("#displayperiode").hide();
			jQuery("#startdate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?php echo base_url(); ?>assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);	

			jQuery("#enddate").datepicker(
				{
							dateFormat: 'yy/mm/dd'
						, 	startDate: '1900/01/01'
						, 	showOn: 'button'
						//, changeYear: true
						//,	changeMonth: true
						, 	buttonImage: '<?php echo base_url(); ?>/assets/images/calendar.gif'
						, 	buttonImageOnly: true
						,	beforeShow: 
								function() 
								{	
									jQuery('#ui-datepicker-div').maxZIndex();
								}
				}
			);	
			jQuery("#periodedate").click(function(){
				jQuery("#startdate").attr("value", "");
				jQuery("#enddate").attr("value", "");
				jQuery("#displayperiode").show();
			});	
			jQuery("#alldate").click(function(){
				jQuery("#displayperiode").hide();
			});
			
			jQuery("#sortby").val('expense_date');
			jQuery("#orderby").val('desc')
			
			//field_onchange();
			page(0);			
		}
	);
	
function frmsearch_onsubmit()
	{
		var field = jQuery("#field").val();
		location = '<?php echo base_url();?>carmanagement/status_vehicle/'+jQuery("#field").val()+jQuery("#keyword").val();
		return false;
	}

function field_onchange()
	{
		var v = jQuery("#field").val();

		jQuery("#keyword").hide();
		jQuery("#vehicle_status").hide();
		jQuery("#start_date").hide();
		jQuery("#start_date").datepicker( "destroy" );
		
		switch(v)
		{
			case "vehicle_status":
				jQuery("#vehicle_status").show();
				break;
			case "data_will_expired":
				jQuery("#keyword").hide();
				break;
			case "data_is_expired":
				jQuery("#keyword").hide();
				break;	
			case "start_date":
				jQuery("#start_date").datepicker(
					{
								dateFormat: 'yy/mm/dd'
							, 	start_date: '1900/01/01'
							, 	showOn: 'button'
							, 	changeYear: true
							,	changeMonth: true
							, 	buttonImage: '<?=base_url()?>assets/images/calendar.gif'
							, 	buttonImageOnly: true
							,	beforeShow: 
									function() 
									{	
										jQuery('#ui-datepicker-div').maxZIndex();
									}
					}
				);
				jQuery("#start_date").show();
				break;
			default:
				jQuery("#keyword").show();			
		}
	}
	
function changephoto(v)
			{
				showdialog();
				jQuery.post('<?php echo base_url(); ?>carmanagement/changephoto/', {id: v},
					function(r)
					{
						showdialog(r.html, "Tenant Profile");
					}
					, "json"
				);
			}

	
function excel_onsubmit(){
		jQuery("#loader2").show();
		
		jQuery.post("<?=base_url();?>carmanagement/report_to_excel/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader2").hide();
				if(r.success == true){
					jQuery("#frmreq").attr("src", r.filename);			
				}else{
					alert(r.errMsg);
				}	
			}
			, "json"
		);
		
		return false;
	}	
	
	function remove_status(id)
		{
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>carmanagement/removestatus/' + id, {}, function(r){
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
    <form class="block-content form" name="frmsearch" id="frmsearch" method="post" action="<?=base_url()?>carmanagement/status_vehicle">
      <h1><?php echo "Status Vehicle"; ?> (<?php echo $total;?>)</h1>
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
                <!--<option value="settenant_name">Tenant Name</option>-->
				<option value="vehicle_no">Vehicle No</option>
                <option value="vehicle_name">Vehicle Name</option>
				<option value="vehicle_status">Vehicle Status</option>
				<option value="longtime">Longtime</option>
                <option value="data_will_expired">Data Will Expired</option>
                <option value="data_is_expired">Data Expired</option>
              </select>
			  <select id="vehicle_status" name="vehicle_status" style="display: none;">
					<option value="0">Rent</option>
					<option value="1">Complete</option>
			  </select>
				
				<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
				<b>Date:</b>
                    <input type="radio" name="searchdate" id="alldate" value="all" checked="checked"/> All 
    				<input type="radio" name="searchdate" id="periodedate" value="periode"/>by Rent Start Date
    				<span id="displayperiode" style="display:none;">
				       From <input type='text' name="startdate" id="startdate" class="date-pick" value=""  maxlength='10'>
                        To <input type='text' name="enddate" id="enddate"  class="date-pick" value=""  maxlength='10'>
    				</span> 
				
				<input type="text" name="cp" id="cp" value="" class="formdefault" style="display: none;" />	
				<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
				<input class="btn_export" type="button" name="excel" id="btnexcelreport" value="Export To Excel" onclick="javascript:return excel_onsubmit()" />
                <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
				
            </td>
        </tr>
      </table>
	  </fieldset>
	</form>
	<iframe id="frmreq" style="display:none;"></iframe>
    <table width="100%" cellpadding="3" class="table sortable no-margin">
      <thead>
      <tr style="text-align: left;">
       <th width="2%" align="left"><?=$this->lang->line("lno"); ?></th>
        <th width="15%" valign="top" style="text-align:center;">Nama Penyewa</th>
        <th width="13%" valign="top" style="text-align:center;">Vehicle</th>
        <th width="10%" valign="top" style="text-align:center;">Longtime</th>
        <th width="12%" valign="top" style="text-align:center;">Start Date</th>
        <th width="12%" valign="top" style="text-align:center;">End Date</th>
		<th width="12%" valign="top" style="text-align:center;">Expired Date</th>
		<th width="6%" valign="top" style="text-align:center;">Status</th>
		<th width="6%"  valign="top" style="text-align:center;">Control</th>
      </tr>
      </head>
      <tbody>
          <?php $i = 1 ?>
          <?php foreach ($data as $bt): ?>
          <tr>
            <td valign="top" style="text-align:center;"><?php echo $i++ ?></td>
            <!--<td><?php echo $bt->settenant_name ?></td>-->
			<td valign="top" style="text-align:center;">
                    
                    <?php
                        if (isset($settenant_name) && count($settenant_name)>0)
                        {
                            foreach($settenant_name as $settenant_names)
                            {
                                if($settenant_names->customer_id == $bt->settenant_name)
                                {
                                    echo $settenant_names->customer_name;
                                }
                            }
                        }
                    ?>
            </td>
			
            <td valign="top" style="text-align:center;"><?php echo $bt->vehicle_name ?><br/><?php echo $bt->vehicle_no ?></td>
            <td valign="top" style="text-align:center;"><?php echo $bt->longtime ?> Days</td>
			
				
            <td valign="top" style="text-align:center;"><?php echo date("d-m-Y",strtotime($bt->start_date)) ?></td>
            <td valign="top" style="text-align:center;"><?php echo date("d-m-Y",strtotime($bt->end_date)) ?></td>
			<td valign="top" style="text-align:center;"><?php echo date("d-m-Y",strtotime($bt->expired_date)) ?></td>
			
            <?php if ($bt->vehicle_status == 1) { ?>
				<td valign="top" style="text-align:center;"><img src="<?=base_url();?>assets/rentcar/images/complete.png" height="20" width="20" border="0" alt="Complete" title="Complete"></td>
            <?php } ?>
			<?php if ($bt->vehicle_status == 0) { ?>
				<td valign="top" style="text-align:center;"><img src="<?=base_url();?>assets/rentcar/images/rent.png" height="20" width="20" border="0" alt="Rent" title="Rent"></td>
            <?php } ?>
			<td valign="top" style="text-align:center;">
                <a href="<?=base_url();?>carmanagement/edit_status/<?=$bt->settenant_id;?>"> <img src="<?=base_url();?>assets/rentcar/images/edit.png" height="20" width="20" border="0" alt="Update Status" title="Update Status">
                </a>
				<a href="<?=base_url();?>carmanagement/removestatus" onclick="javascript:remove_status(<?=$bt->settenant_id;?>)"><img src="<?=base_url();?>assets/rentcar/images/delete.png" height="20" width="20" alt="Delete Data" title="Delete Data"></a>
				
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
