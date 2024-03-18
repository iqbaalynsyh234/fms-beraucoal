<script>
	function page(p){
		if(p==undefined){
			p=0;
		}
		jQuery("#offset").val(p);
		jQuery("#result").html('<img src="<?php echo base_url();?>assets/transporter/images/loader2.gif">');
		jQuery("#loader").show();
		jQuery.post("<?=base_url();?>operationalreport/result_summary/", jQuery("#frmsearch").serialize(),
			function(r){
				if (r.error) {
					alert(r.message);
					jQuery("#loader").hide();
					jQuery("#result").hide();
					return;
				}else{
					jQuery("#loader").hide();
					jQuery("#result").show();
					jQuery("#result").html(r.html);
					jQuery("#total").html(r.total);

				}
			}, "json");
	}


	function frmsearch_onsubmit(){
		jQuery("#loader").show();
		page(0);
		return false;
	}

function company_onchange(){
		var data_company = jQuery("#company").val();
		if(data_company == 0){
			alert('Silahkan Pilih Cabang!!');
			jQuery("#mn_vehicle").hide();

			jQuery("#vehicle").html("<option value='0' selected='selected'>--Select Vehicle--</option>");
		}else{
			jQuery("#mn_vehicle").show();

			var site = "<?=base_url()?>operationalreport/get_vehicle_by_company/" + data_company;
		    jQuery.ajax({
		        url: site,
		        success: function(response){
  					jQuery("#vehicle").html("");
            jQuery("#vehicle").html(response);
		        },
		    	dataType:"html"
		    });
		}
	}
</script>
<div class="sidebar-container">
	<?=$sidebar;?>
    </div>
    <div class="page-content-wrapper">
        <div class="page-content" style="width:100%;">
            <div class="row">
  						<div class="col-md-12 col-sm-12">
                <div class="panel" id="panel_form">
                    <header class="panel-heading panel-heading-blue">OPERATIONAL REPORT (SUMMARY)</header>
                    <div class="panel-body" id="bar-parent10">
                      <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
                  			<input type="hidden" name="offset" id="offset" value="" />
                  			<input type="hidden" id="sortby" name="sortby" value="" />
                  			<input type="hidden" id="orderby" name="orderby" value="" />
                  			<table width="100%" cellpadding="3" class="tablelist" style="font-size: 12px;">

                  				<tr id="mn_company">
                  					<td>Cabang</td>
                  					<td>
                              <div class="input-group col-md-12">
                    						<select id="company" name="company" class="select2">
                    							<option value="" selected='selected'>--Cabang--</option>
                    							<?php
                    								$ccompany = count($rcompany);
                    									for($i=0;$i<$ccompany;$i++){
                    										if (isset($rcompany)&&($row->user_company == $rcompany[$i]->company_id)){
                    												$selected = "selected";
                    											}else{
                    												$selected = "";
                    											}
                    										echo "<option value='" . $rcompany[$i]->company_id ."' " . $selected . ">" . $rcompany[$i]->company_name . "</option>";
                    										}
                    							?>
                    						</select>
                              </div>
                  					</td>
                  				</tr>
                  				<tr id="mn_vehicle" style="display:none">
                  					<td>Vehicle</td>
                  					<td>
                  						<select id="vehicle" name="vehicle" class="select2">
                  							<!--<option value="" selected='selected'>--Select Vehicle--</option>-->
                  							<?php
                  								$cvehicle = count($vehicles);
                  									for($i=0;$i<$cvehicle;$i++){
                  										if (isset($vehicles)&&($row->vehicle_company == $vehicles[$i]->company_id)){
                  												$selected = "selected";
                  											}else{
                  												$selected = "";
                  											}
                  										echo "<option value='" . $vehicles[$i]->vehicle_device ."' " . $selected . ">" . $vehicles[$i]->vehicle_no ." - ".$vehicles[$i]->vehicle_name. "</option>";
                  										}
                  							?>
                  						</select>
                  					</td>
                  				</tr>

                  				<tr id="filterdatestartend">
                  					<td width="10%">Date</td>
                  					<td>
                              <div class="input-group date form_date col-md-4" data-date="" data-date-format="yyyy-mm-dd"  data-link-format="yyyy-mm-dd">
                                  <input class="form-control" size="5" type="text" readonly name="startdate" id="startdate" value="<?=date('Y-m-d',strtotime("yesterday") )?>">
                                  <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                              </div>
                  					</td>
                  				</tr>
                  			</table>
                        <div class="text-right">
                          <td style="border: 0px;"><input class="btn btn-primary" id="btnsearchreport" type="submit" value="Search" />
                          <img id="loader2" style="display: none;" src="<?php echo base_url();?>assets/images/ajax-loader.gif" />
                        </div>
                  		</form>
                    </div>
  							</div>
              </div>
            </div>
            <div id="loader2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
  					<div id="result"></div>
          </div>
    </div>
