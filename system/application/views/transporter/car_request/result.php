<style>
a:link {color:green;}
a:visited {color: #660066;}
a:hover {text-decoration: none; color: #ff9900;
font-weight:bold;}
a:active {color: red;text-decoration: none}
</style>
<script>
jQuery(document).ready(
		function()
		{
		  showclock();	
		}
	);
 
 function request_detail(v)
 {
    showdialog();
    jQuery.post('<?php echo base_url(); ?>transporter/car_request/getdetail/', {id: v},
    function(r)
    {
        showdialog(r.html, "Request Detail");
        }
        , "json"
        );
        }

 function confirm_request(v)
 {
    showdialog();
    jQuery.post('<?php echo base_url(); ?>transporter/car_request/confirm_request/', {id: v},
    function(r)
    {
        showdialog(r.html, "Confirm Request");
        }
        , "json"
        );
        }
    
 function field_onchange()
 {
    var v = jQuery("#field").val();

    if (v == "status")
    {
        jQuery("#status").show();
        jQuery("#keyword").hide();
    }
    else
    {
        jQuery("#status").hide();
        jQuery("#keyword").show();
    }
 }
 
 function cancel_by_customer(v)
 {
    jQuery.post('<?php echo base_url(); ?>transporter/car_request/cancel_by_customer/', {id: v},
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
 }
 
 function order_complete(v)
 {
    showdialog();
    jQuery.post('<?php echo base_url(); ?>transporter/car_request/order_complete_dialog/', {id: v},
    function(r)
    {
        showdialog(r.html, "Order Complete");
        }
        , "json"
        );
 }
 

</script>

<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
<div id="main" style="margin: 20px;">
    <br />&nbsp;
    <h1><?php echo "Request List"; ?> (<?php echo $total;?>)</h1>
    <h2><?=$this->lang->line("lsearch"); ?></h2>
    <form name="frmsearch" id="frmsearch" method="post" action="<?php base_url();?>trasnporter/car_request/getlist">
        <input type="hidden" name="offset" id="offset" value="" />
        <table width="100%" cellpadding="3" class="tablelist">
            <tr>
                <td><?=$this->lang->line("lsearchby");?></td>
                <td>
                    <select id="field" name="field" onchange="javascript:field_onchange()">
                        <option value="All">All</option>
                        <?php if ($this->sess->user_group == 0)
                        { 
                        ?>
                        <option value="group">Company</option>
                        <?php    
                        }
                        ?>
                        <option value="vehicle_no">Vehicle No</option>
                        <option value="status">Status</option>
                    </select>
                    
                    <select id="status" name="status" style="display: none;" >
                        <?php
                        if (isset($request_status) && $request_status!="")
                        {
                            foreach($request_status as $req_status)
                            {
                        ?>
                        <option value="<?php echo $req_status->request_status_id; ?>"><?php echo $req_status->request_status_name;?></option>
                        <?php 
                            }
                        }
                         ?>
                    </select>
						<input type="text" name="keyword" id="keyword" value="" class="formdefault" />
						<input type="submit" value="<?=$this->lang->line("lsearch");?>" />
					</td>
				</tr>
			</table>
		</form>
        [<a href="<?php echo base_url();?>transporter/car_request/add_request">Request Car</a>]
        <br /><br />
        <table class="tablelist">
            <thead>
            <tr>
                <th width="1%">&nbsp;</th>
                <th width="2%"><?=$this->lang->line("lno"); ?></th>
                
                <?php if ($this->sess->user_group == 0)
                { 
                ?>
                <th>Company</th>
                <?php    
                } else {
                ?>
                <th>Name</th>
                <?php } ?>
                
                <th>Vehicle</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Purpose</th>
                <th>Create Date</th>
                <th>Status</th>
                <th>Confirm</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($data) && count($data)>0)
            {
                for ($i=0;$i<count($data);$i++)
                {
            ?>
            <tr>
                <td width="1%">&nbsp;</td>
                <td width="2%"><?=$i+1+$offset?></td>
                
                <?php if ($this->sess->user_group == 0)
                { 
                ?>
                <td><?php echo $data[$i]->request_group_name;?></td>
                <?php    
                } else {
                ?>
                <td><?php echo $data[$i]->request_pic_name;?></td>
                <?php } ?>
                
                <td><?php echo $data[$i]->request_vehicle_no;?></td>
                <td><?php echo $data[$i]->request_start_date;?></td>
                <td><?php echo $data[$i]->request_end_date;?></td>
                <td>
                <?php
                if (isset($data[$i]->request_purpose))
                    {
                        foreach($trip_purpose as $purpose)
                        {
                            if ($purpose->trip_purpose_id == $data[$i]->request_purpose)
                            {
                                echo $purpose->trip_purpose_name;       
                            }
                        }    
                    }
                ?>
                </td>
                <td><?php echo $data[$i]->request_create_date;?></td>
                
                <td>
                <?php 
                    if (isset($data[$i]->request_status))
                    {
                        foreach($request_status as $req_status)
                        {
                            if ($req_status->request_status_id == $data[$i]->request_status)
                            {
                                if (!$this->sess->user_group)
                                {
                                    echo $req_status->request_status_name;   
                                }
                                else
                                {
                                    if ($data[$i]->request_status == $this->config->item("new_order"))
                                    {
                                        echo "Wait For Approval";
                                    }
                                    else
                                    {
                                        echo $req_status->request_status_name;
                                    }
                                }       
                            }
                        }    
                    }
                ?>
                </td>
                
                <td><?php echo $data[$i]->request_confirm_date;?></td>
                <td>
                <?php 
                //confirm
                if ($this->sess->user_group == 0) {
                if ($data[$i]->request_status == $this->config->item("new_order"))
                { ?>
                <a href="javascript:confirm_request(<?php echo $data[$i]->request_id; ?>)" title="Need confirm !">
                <img src="<?php echo base_url();?>assets/transporter/images/help.png" border="0" width="20px" height="20px" />
                </a>
                <?php } ?>
                <a href="javascript:request_detail(<?php echo $data[$i]->request_id;?>)" title="Info Detail">
                <img src="<?php echo base_url();?>assets/transporter/images/info.png" border="0" width="20px" height="20px" />
                </a>
                <?php
                if ($data[$i]->request_status == $this->config->item("booked")) {
                 ?>
                <a href="javascript:order_complete(<?php echo $data[$i]->request_id;?>)" title="Order Complete">
                <img src="<?php echo base_url();?>assets/transporter/images/success.png" border="0" />
                </a>
                <?php }  ?>
                <?php
                 }
                 else {
                    if ($data[$i]->request_status == $this->config->item("new_order")){
                 ?>
                 <a href="javascript:cancel_by_customer(<?php echo $data[$i]->request_id; ?>)" title="cancel">
                 <img src="<?php echo base_url();?>assets/images/logout2.gif" />
                 </a>
                 <?php } } ?>
                </td>
            </tr>
            <?php   
                }
            } else {
             ?>
             <tr><td colspan="10">Data Not Available !</td></tr>
             <?php } ?>
            </tbody>
            <tfoot>
            <tr><td colspan="12"><?=$paging?></td></tr>
			</tfoot>
        </table>
</div>
</div>