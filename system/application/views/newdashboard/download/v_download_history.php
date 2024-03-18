<!-- start sidebar menu -->
<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12">
        <div class="card-box">
          <div class="card-body">
            <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
        			<h3>Download History Report (CSV)</h3>
        				<input type="hidden" name="offset" id="offset" value="" />
        				<input type="hidden" id="sortby" name="sortby" value="" />
        				<input type="hidden" id="orderby" name="orderby" value="" />
        				<input type="hidden" id="userid" name="userid" value="<?=$this->sess->user_id;?>" />
        				<input type="hidden" id="usercompany" name="usercompany" value="<?=$this->sess->user_company;?>" />
        				<table width="100%" cellpadding="3" class="tablelist" style="font-size: 12px;">
        					<tr>
        						<td>Vehicle</td>
        						<td>
        							<select id="vehicle" name="vehicle" class="select2">
        								<option value="all">All</option>
        								<?php for($i=0; $i < count($vehicles); $i++) { ?>
        								<option value="<?php echo $vehicles[$i]->vehicle_device; ?>"><?php if ($this->sess->user_type != 2) { echo $vehicles[$i]->user_name." - "; } ?><?php echo $vehicles[$i]->vehicle_no; ?> - <?php echo $vehicles[$i]->vehicle_name; ?></option>
        								<?php } ?>
        							</select>
        						</td>
        					</tr>
        					<tr id="filterdatestartend">
        						<td width="10%">Date</td>
        						<td>
                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="yyyy/m/d" data-link-field="dtp_input2" data-link-format="yyyy/m/d">
                        <input class="form-control" size="10" type="text" readonly name="startdate" id="startdate" value="<?=date('Y/m/d')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="hidden" id="dtp_input2" value="" class="form-control"/>
											</div>

                      ~

                      <div class="input-group date form_date col-md-6" data-date="" data-date-format="yyyy/m/d" data-link-field="dtp_input2" data-link-format="yyyy/m/d">
                        <input class="form-control" size="10" type="text" readonly name="enddate" id="enddate" value="<?=date('Y/m/d')?>">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                        <input type="hidden" id="dtp_input2" value="" class="form-control"/>
											</div>
        						</td>
        					</tr>
        				</table>
                <div class="text-right">
                  <input class="btn btn-primary" id="btnsearchreport" type="submit" value="Search"/>
                  <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif"/>
                </div>
        		</form>
            <br />
        		<div id="result"></div>
        		<iframe id="frmreq" style="display:none;"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery.min.js"></script>
<!-- <link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.css"> -->
<script src="<?php echo base_url()?>assets/js/assetsbaru/jquery-ui.min.js"></script>
<script src="<?php echo base_url()?>assets/js/assetsbaru/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url()?>assets/js/assetsbaru/chosen.min.css">
<script>
	function page(p)
	{
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>download/download_historycsv/", jQuery("#frmsearch").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
				jQuery("#result").html(r.html);
			}
			, "json"
		);
	}

	function frmsearch_onsubmit()
	{
		jQuery("#loader").show();
		page(0);
		return false;
	}
</script>
