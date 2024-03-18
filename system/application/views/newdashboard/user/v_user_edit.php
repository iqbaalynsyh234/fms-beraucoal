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
    <div class="row">
      <div class="col-md-12">
        <div class="panel" id="panel_form">
          <header class="panel-heading" style="background-color:#221f1f;color:white;"> Add User</header>
          <div class="panel-body" id="bar-parent10">
            <form class="block-content form" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
    					<!-- <?php if (isset($row)) { ?>
      					<h4><?=$this->lang->line("luser_edit"); ?></h4>
    					<?php } else { ?>
      					<h4><?=$this->lang->line("luser_add"); ?></h4>
    					<?php } ?> -->

            <h4>Required Information</h4>

    				<table width="100%" cellpadding="3" class="table sortable no-margin">
    					<?php if (isset($row)) { ?>
    					<input class="form-control" type="hidden" id="id" name="id" value="<?=$row->user_id;?>" />
    					<tr>
    						<td>ID</td>
    						<td>:</td>
    						<td><?=$row->user_id;?></td>
    					</tr>
    					<?php } ?>
    				<tr>
    					<td colspan="3">
                <h4>
                  <b style="color: blue;"><?=$this->lang->line("llogin_info");?></b>
                </h4>
              </td>
    				</tr>
    				<?php if (isset($row)) { ?>
        			<tr>
    						<td width="130"><?=$this->lang->line("llogin");?></td>
    						<td width="1">:</td>
    						<!--<td><?=isset($row) ? htmlspecialchars($row->user_login, ENT_QUOTES) : "";?></td>-->
    						<td><input class="form-control" type="text" name="username" id="username" value="<?=isset($row) ? htmlspecialchars($row->user_login, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    				<?php } else { ?>
        			<tr>
    						<td width="130"><?=$this->lang->line("llogin");?></td>
    						<td width="1">:</td>
    						<td><input class="form-control" type="text" name="username" id="username" value="<?=isset($row) ? htmlspecialchars($row->user_login, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lpassword");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="password" name="pass" id="pass" value="" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lconfirm_password");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="password" name="cpass" id="cpass" value="" class="formdefault" /></td>
    					</tr>
      			<?php } ?>
        			<tr>
    						<td style="display:none;"><?=$this->lang->line("ltype");?></td>
    						<td style="display:none;">:</td>
    						<td style="display:none;">
      						<select class="form-control" id="type" name="type" onchange="javascript:type_onchange()">
      							<option value="2"><?php echo $this->config->item("transporter_user_type_name");?></option>
      						</select>
  							</td>
    					</tr>

        			<tr id="tragent">
    						<td style="display:none;"><?=$this->lang->line("lagent");?></td>
    						<td style="display:none;">:</td>
    						<td style="display:none;">
    							<select class="form-control" id="agent" name="agent">
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
    					<td colspan="3">
                <h4>
                  <b style="color: blue;"><?=$this->lang->line("lprivate_info");?></b>
                </h4>
              </td>
    				</tr>
        			<tr>
    						<td><?=$this->lang->line("lname");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="text" name="name" id="name" value="<?=isset($row) ? htmlspecialchars($row->user_name, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lemail");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="text" name="email" id="email" value="<?=isset($row) ? htmlspecialchars($row->user_mail, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("llicense");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="text" name="license" id="license" value="<?=isset($row) ? htmlspecialchars($row->user_license_id, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lsex");?></td>
    						<td>:</td>
    						<td>
    						<select class="form-control" id="sex" name="sex" onchange="javascript:type_onchange()">
    							<option value="1" <? if ((! isset($user)) || ($user->user_sex == 'M')) { ?>selected<?php } ?>><?=$this->lang->line("lmale");?></option>
    							<option value="1" <? if (isset($user) && ($user->user_sex == 'F')) { ?>selected<?php } ?>><?=$this->lang->line("lfemale");?></option>
    						</select>

    							</td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lbirthdate");?></td>
    						<td>:</td>
    						<td>
                  <div class="input-group date form_date col-md-4" data-date="" data-date-format="dd/mm/yyyy" data-link-format="dd-mm-yy">
                    <input class="form-control" class="form-control" type="text" name="birthdate" id="birthdate" value="<?php if (isset($row)) echo $row->user_date_fmt; ?>"  maxlength='10'>
                    <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                  </div>
                </td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lprovince");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="text" name="province" id="province" value="<?=isset($row) ? htmlspecialchars($row->user_province, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lcity");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="text" name="city" id="city" value="<?=isset($row) ? htmlspecialchars($row->user_city, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("laddress");?></td>
    						<td>:</td>
    						<td><textarea class="form-control" name="address" id="address" class="formdefault"><?=isset($row) ? htmlspecialchars($row->user_address, ENT_QUOTES) : "";?></textarea></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lmobile");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="text" name="mobile" id="mobile" value="<?=isset($row) ? htmlspecialchars($row->user_mobile, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>
        			<tr>
    						<td><?=$this->lang->line("lphone");?></td>
    						<td>:</td>
    						<td><input class="form-control" type="text" name="phone" id="phone" value="<?=isset($row) ? htmlspecialchars($row->user_phone, ENT_QUOTES) : "";?>" class="formdefault" /></td>
    					</tr>


              <?php if (isset($row)) {
                $useridsession = $this->sess->user_id;
                $useridrow     = $row->user_id;
                // echo $useridsession.'-'.$useridrow;
                  if ($useridsession == $useridrow) {?>
                    <!-- EDIT DARI TOMBOL LOGOUT -->
                  <?php }else{?>
                    <!-- EDIT DARI TABLE -->
                    <tr id="trpermissiongroup">
              				<td colspan="3">
                        <h4>

                          <b style="color: blue;"><?php echo "User Level Information";?></b>
                        </h4>
                      </td>
              			</tr>
                    <tr id="trmanprofile">
              				<td style="display:none";><?=$this->lang->line("lcan_manage_profile");?></td>
              				<td style="display:none";>:</td>
              				<td style="display:none";>
              					<input class="form-control" type="radio" name="manprofile" id="manprofile" value="1"<?php if ((! isset($row)) || ($row->user_change_profile == 1)) { echo " checked"; } ?> /> <?=$this->lang->line("lyes");?>
              					<input class="form-control" type="radio" name="manprofile" id="manprofile" value="2"<?php if (isset($row) && ($row->user_change_profile == 2)) { echo " checked"; } ?> /> <?=$this->lang->line("lno");?>
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
              							<select class="form-control" name="usersite" id="usersite" onchange="javascript:loadgroup()">
              							<?php foreach($companies as $company) {
              								if ( $company->company_id == $this->sess->user_company) {?>
              								<option value="<?php echo $company->company_id; ?>" <?php if (isset($row) && ($row->user_company == $company->company_id)) { echo "selected"; } ?>><?php echo $company->company_name; ?></option>
              							<?php } } ?>
              							</select>
              						</td>
              			</tr>
                      <?php if (isset($row)) { ?>
                        <tr>
                          <td>Current User Level</td>
                          <td>:</td>
                          <td>
                            <?php
                              $userlevel = $row->user_level;
                                if ($userlevel == 2) {?>
                                  <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                                  <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Branch Office User" readonly>
                                <?}elseif ($userlevel == 3) {?>
                                  <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                                  <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Sub Branch Office User" readonly>
                                <?php }elseif ($userlevel == 4) {?>
                                  <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                                  <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Customer User" readonly>
                                <?php }else {?>
                                  <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                                  <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Sub Customer User" readonly>
                                <?php } ?>
                          </td>
                        </tr>

                        <tr>
                          <td>Current Branch Office</td>
                          <td>:</td>
                          <td>
                            <input type="text" class="form-control" name="cur_branchoffice_old" id="cur_branchoffice_old" value="<?php echo $cur_branchoffice[0]['company_id']; ?>" hidden>
                            <input type="text" class="form-control" name="cur_branchoffice_foredit" id="cur_branchoffice_foredit" value="<?php echo $cur_branchoffice[0]['company_name'] ?>" readonly>
                          </td>
                        </tr>

                        <tr>
                          <td>Current Sub Branch Office</td>
                          <td>:</td>
                          <td>
                            <input type="text" class="form-control" name="cur_subbranchoffice_old" id="cur_subbranchoffice_old" value="<?php echo $cur_subbranchoffice[0]['subcompany_id']; ?>" hidden>
                            <input type="text" class="form-control" name="cur_subbranchoffice_foredit" id="cur_subbranchoffice_foredit" value="<?php echo $cur_subbranchoffice[0]['subcompany_name'] ?>" readonly>
                          </td>
                        </tr>

                        <tr>
                          <td>Current Customer</td>
                          <td>:</td>
                          <td>
                            <input type="text" class="form-control" name="cur_customer_old" id="cur_customer_old" value="<?php echo $cur_customer[0]['group_id']; ?>" hidden>
                            <input type="text" class="form-control" name="cur_customer" id="cur_customer" value="<?php echo $cur_customer[0]['group_name'] ?>" readonly>
                          </td>
                        </tr>

                        <tr>
                          <td>Current Sub Customer</td>
                          <td>:</td>
                          <td>
                            <input type="text" class="form-control" name="cur_subcustomer_old" id="cur_subcustomer_old" value="<?php echo $cur_subcustomer[0]['subgroup_id']; ?>" hidden>
                            <input type="text" class="form-control" name="cur_subcustomer" id="cur_subcustomer" value="<?php echo $cur_subcustomer[0]['subgroup_name'] ?>" readonly>
                          </td>
                        </tr>
                      <?php }?>
                      <!-- USER LEVEL SETTING -->
                      <tr>
                        <td>
                          User Level
                          <br>
                          <p>
                            <small>Level 2 : Tracking, Report</small><br>
                            <small>Level 3 : Tracking, Report</small><br>
                            <small>Level 4 : Tracking</small><br>
                            <small>Level 5 : Tracking</small>
                          </p>
                        </td>
                        <td>:</td>
                        <td>
                          <select class="form-control" name="user_level" id="user_level">
                            <option value="">--Select User Level--</option>
                            <option value="2">Level 2 (Branch Office User)</option>
                            <option value="3">Level 3 (Sub Branch Office User)</option>
                            <option value="4">Level 4 (Customer User)</option>
                            <option value="5">Level 5 (Sub Customer User)</option>
                          </select>
                        </td>
                      </tr>
                      <tr id="trgroup" style="display: none;">
                        <td><?php echo "Branch Office"; ?></td>
                        <td>:</td>
                        <td><div id="usergroup"></div></td>
                      </tr>
                      <tr id="showthissubcompany" style="display: none;">

                      </tr>
                      <tr id="showthiscustomer" style="display: none;">

                      </tr>
                      <tr id="showthissubcustomer" style="display: none;">

                      </tr>
                <?php } ?>
              <?php } }else {?>
                <tr id="trpermissiongroup">
                  <td colspan="3">
                    <h4>

                      <b style="color: blue;"><?php echo "User Level Information";?></b>
                    </h4>
                  </td>
                </tr>
                <tr id="trmanprofile">
                  <td style="display:none";><?=$this->lang->line("lcan_manage_profile");?></td>
                  <td style="display:none";>:</td>
                  <td style="display:none";>
                    <input class="form-control" type="radio" name="manprofile" id="manprofile" value="1"<?php if ((! isset($row)) || ($row->user_change_profile == 1)) { echo " checked"; } ?> /> <?=$this->lang->line("lyes");?>
                    <input class="form-control" type="radio" name="manprofile" id="manprofile" value="2"<?php if (isset($row) && ($row->user_change_profile == 2)) { echo " checked"; } ?> /> <?=$this->lang->line("lno");?>
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
                        <select class="form-control" name="usersite" id="usersite" onchange="javascript:loadgroup()">
                        <?php foreach($companies as $company) {
                          if ( $company->company_id == $this->sess->user_company) {?>
                          <option value="<?php echo $company->company_id; ?>" <?php if (isset($row) && ($row->user_company == $company->company_id)) { echo "selected"; } ?>><?php echo $company->company_name; ?></option>
                        <?php } } ?>
                        </select>
                      </td>
                </tr>
                  <?php if (isset($row)) { ?>
                    <tr>
                      <td>Current User Level</td>
                      <td>:</td>
                      <td>
                        <?php
                          $userlevel = $row->user_level;
                            if ($userlevel == 2) {?>
                              <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                              <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Branch Office User" readonly>
                            <?}elseif ($userlevel == 3) {?>
                              <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                              <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Sub Branch Office User" readonly>
                            <?php }elseif ($userlevel == 4) {?>
                              <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                              <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Customer User" readonly>
                            <?php }else {?>
                              <input type="text" class="form-control" name="cur_user_level_old" id="cur_user_level_old" value="<?php echo $userlevel; ?>" hidden>
                              <input type="text" class="form-control" name="cur_user_level_foredit" id="cur_user_level_foredit" value="Sub Customer User" readonly>
                            <?php } ?>
                      </td>
                    </tr>

                    <tr>
                      <td>Current Branch Office</td>
                      <td>:</td>
                      <td>
                        <input type="text" class="form-control" name="cur_branchoffice_old" id="cur_branchoffice_old" value="<?php echo $cur_branchoffice[0]['company_id']; ?>" hidden>
                        <input type="text" class="form-control" name="cur_branchoffice_foredit" id="cur_branchoffice_foredit" value="<?php echo $cur_branchoffice[0]['company_name'] ?>" readonly>
                      </td>
                    </tr>

                    <tr>
                      <td>Current Sub Branch Office</td>
                      <td>:</td>
                      <td>
                        <input type="text" class="form-control" name="cur_subbranchoffice_old" id="cur_subbranchoffice_old" value="<?php echo $cur_subbranchoffice[0]['subcompany_id']; ?>" hidden>
                        <input type="text" class="form-control" name="cur_subbranchoffice_foredit" id="cur_subbranchoffice_foredit" value="<?php echo $cur_subbranchoffice[0]['subcompany_name'] ?>" readonly>
                      </td>
                    </tr>

                    <tr>
                      <td>Current Customer</td>
                      <td>:</td>
                      <td>
                        <input type="text" class="form-control" name="cur_customer_old" id="cur_customer_old" value="<?php echo $cur_customer[0]['group_id']; ?>" hidden>
                        <input type="text" class="form-control" name="cur_customer" id="cur_customer" value="<?php echo $cur_customer[0]['group_name'] ?>" readonly>
                      </td>
                    </tr>

                    <tr>
                      <td>Current Sub Customer</td>
                      <td>:</td>
                      <td>
                        <input type="text" class="form-control" name="cur_subcustomer_old" id="cur_subcustomer_old" value="<?php echo $cur_subcustomer[0]['subgroup_id']; ?>" hidden>
                        <input type="text" class="form-control" name="cur_subcustomer" id="cur_subcustomer" value="<?php echo $cur_subcustomer[0]['subgroup_name'] ?>" readonly>
                      </td>
                    </tr>
                  <?php }?>
                  <!-- USER LEVEL SETTING -->
                  <tr>
                    <td>
                      User Level
                      <br>
                      <p>
                        <small>Level 2 : Tracking, Report</small><br>
                        <small>Level 3 : Tracking, Report</small><br>
                        <small>Level 4 : Tracking</small><br>
                        <small>Level 5 : Tracking</small>
                      </p>
                    </td>
                    <td>:</td>
                    <td>
                      <select class="form-control" name="user_level" id="user_level">
                        <option value="">--Select User Level--</option>
                        <option value="2">Level 2 (Branch Office User)</option>
                        <option value="3">Level 3 (Sub Branch Office User)</option>
                        <option value="4">Level 4 (Customer User)</option>
                        <option value="5">Level 5 (Sub Customer User)</option>
                      </select>
                    </td>
                  </tr>
                  <tr id="trgroup" style="display: none;">
                    <td><?php echo "Branch Office"; ?></td>
                    <td>:</td>
                    <td><div id="usergroup"></div></td>
                  </tr>
                  <tr id="showthissubcompany" style="display: none;">

                  </tr>
                  <tr id="showthiscustomer" style="display: none;">

                  </tr>
                  <tr id="showthissubcustomer" style="display: none;">

                  </tr>
              <?php } ?>
            <?php } ?>

    			<tr id="dvpaymentgroup">
    				<td colspan="3" style="display:none";><h2><?=$this->lang->line("lpayment_info");?></h2></td>
    			</tr>
    			<tr id="dvpaymenttype" style="display:none";>
    					<td style="display:none";><?=$this->lang->line("lpayment_type");?></td>
    					<td style="display:none";>:</td>
    					<td style="display:none";>
    						<input  type="radio" name="user_payment_type" id="user_payment_type" value="1" onclick="javascript:payment_type_onclick()" <?php if (isset($row) && ($row->user_payment_type == 1)) { echo "checked"; } ?>/>&nbsp;<?=$this->lang->line("labodement");?>
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
                  <input class="btn btn-warning" type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="location='<?=base_url()?>account'" />
  								<input class="btn btn-success" type="submit" name="btnsave" id="btnsave" value=" Save " />
                  <img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" style="display:none;"/>
    						</td>
    					</tr>
    				</table>
    			</form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="js/script.js"></script>
<script src="<?php echo base_url()?>assets/dashboard/assets/js/jquery-1.7.1.min.js" type="text/javascript"></script>

<script type="text/javascript">
  $("#notifnya").fadeIn(1000);
  $("#notifnya").fadeOut(5000);

  jQuery(document).ready(
		function()
		{
			type_onchange();
			payment_type_onclick();
			loadgroup();
		}
	);

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

  function loadgroup()
	{
		jQuery.post("<?php echo base_url(); ?>account/options<?php if (isset($row)) { echo "/".$row->user_id; } ?>", jQuery("#frmadd").serialize(),
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

  function frmadd_onsubmit()
	{
    $("#loader").show();
			jQuery.post("<?=base_url()?>account/save", jQuery("#frmadd").serialize(),
			function(r)
			{
        $("#loader").hide();
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

  function btncancel(){
    $("#formaddcustomermaster").hide();
    $("#formtablecustomermaster").show();
  }

  // FOR DISABLE SUBMIT FORM
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  // FOR OPTION GROUP
  function getsubcompanybyid(){
    // GET SUBCOMPANY BY COMPANY ID
    var branchoffice = $("#branchoffice").val();
    console.log("data : ", branchoffice);
    jQuery.post("<?=base_url()?>account/getsubcompanybyid", {id : branchoffice}, function(r){
				jQuery("#loader").hide();
        $("#showthissubcompany").show();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Sub Branch Office</td>';
              html += '<td>:</td>';
                html += '<td>';
                  html += '<select class="form-control" name="subbranchoffice" id="subbranchoffice" onchange="getcustomerbysubbranchofficeid();">';
                      html += '<option value="">--Select Subcompany--</option>';
                      html += '<option value="empty">Empty</option>';
                      for (var i = 0; i < size; i++) {
                        html += '<option value="'+r.data[i].subcompany_id+'">'+r.data[i].subcompany_name+'</option>';
                      }
                html += '</select>';
              html += '</td>';
        $("#showthissubcompany").html(html);
			}, "json");
  }

  function getcustomerbysubbranchofficeid(){
    // GET CUSTOMER BY SUBCOMPANY ID
    var subcompany = $("#subbranchoffice").val();
    console.log("data : ", subcompany);
    jQuery.post("<?=base_url()?>account/getcustomerbysubcompanyid", {id : subcompany}, function(r){
				jQuery("#loader").hide();
        $("#showthiscustomer").show();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Customer</td>';
              html += '<td>:</td>';
                html += '<td>';
                  html += '<select class="form-control" name="customer" id="customer" onchange="getsubcustomerbyid();">';
                    html += '<option value="">--Select Customer--</option>';
                    html += '<option value="empty">Empty</option>';
                    for (var i = 0; i < size; i++) {
                      html += '<option value="'+r.data[i].group_id+'">'+r.data[i].group_name+'</option>';
                    }
              html += '</select>';
            html += '</td>';
        $("#showthiscustomer").html(html);
			}, "json");
  }

  function getsubcustomerbyid(){
    // GET CUSTOMER BY SUBCOMPANY ID
    var customerid = $("#customer").val();
    console.log("data : ", customerid);
    jQuery.post("<?=base_url()?>account/getsubcustomerbysubcompanyid", {id : customerid}, function(r){
				jQuery("#loader").hide();
        $("#showthissubcustomer").show();
				console.log("r : ", r);
        var size = r.data.length;
        var html = "";
              html += '<td>Sub Customer</td>';
              html += '<td>:</td>';
                html += '<td>';
                  html += '<select class="form-control" name="subcustomer" id="subcustomer">';
                    html += '<option value="">--Select Sub Customer--</option>';
                    html += '<option value="empty">Empty</option>';
                    for (var i = 0; i < size; i++) {
                      html += '<option value="'+r.data[i].subgroup_id+'">'+r.data[i].subgroup_name+'</option>';
                    }
              html += '</select>';
            html += '</td>';
        $("#showthissubcustomer").html(html);
			}, "json");
  }
</script>
