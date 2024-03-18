<style media="screen">
  div#modaldeleteritase {
    margin-top: 5%;
    margin-left: 50%;
    max-height: 300px;
    max-width: 754px;
    position: absolute;
    background-color: #f1f1f1;
    text-align: left;
    border: 1px solid #d3d3d3;
    z-index: 1;
  }

  #ritase {
    background-color: #221f1f;
    color: white;
  }
</style>
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
        <div class="col-md-12" id="tableritase">
          <?php if ($privilegecode == 3) {?>

          <?php }else {?>
            <button class="btn btn-success" onclick="showAddRitase();"><font>Add Ritase</font></button><br><br>
          <?php } ?>

          <div class="panel" id="panel_form">
            <header class="panel-heading panel-heading-red" id="ritase">Ritase</header>
            <div class="panel-body" id="bar-parent10">
                <table id="example1" class="table table-striped">
                  <thead>
            				<tr>
            					<th>No</th>
            					<th>Name</th>
                      <th>Status</th>
                      <?php if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <th>Control</th>
                      <?php } ?>
            				</tr>
            			</thead>
                  <tbody>
                    <?php
            					if ($rows_ritase) {
            						for ($i=0; $i<count($rows_ritase); $i++)
            					{
            				?>
            				<tr <?=($i%2) ? "class='odd'" : "";?>>
            					<td width="2%"><?=$i+1;?></td>
            					<td width="10%"><?=$rows_ritase[$i]->ritase_geofence_name;?></td>
            					<td width="10%">
            						<?php
            							if ($rows_ritase[$i]->ritase_status ==  1)
            							{
            								echo "Active";
            							}
            							else
            							{
            								echo "InActive";
            							}
            						?>
            					</td>
                      <?php if ($privilegecode == 3) {?>

                      <?php }else {?>
                        <td width="10%">
                					<div onclick="showmodaldeleteritase(<?php echo $rows_ritase[$i]->ritase_id;?>)">
                						<img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="Delete Ritase" title="Delete Ritase">
                					</div>
                				</td>
                      <?php } ?>
            				</tr>
            				<?php }
            				} else {
            					echo "Data Not Available";
            				}?>
            			</tbody>
    						</table>
              </div>
        </div>
      </div>

      <div class="col-md-12" id="formaddritase" style="width: 900px; display: none;">
        <div class="panel" id="panel_form">
          <header class="panel-heading panel-heading-red">Add Ritase</header>
          <div class="panel-body" id="bar-parent10">
            <form  class="form-horizontal" name="frmadd" id="frmadd" onsubmit="javascript: return frmadd_onsubmit()">
        		<h6 style="color: red; ">Make sure you made Geofence first</h6>
        			<table width="100%" cellpadding="3" class="table sortable no-margin">
        				<tr>
        					<input type="hidden" name="ritase_company" id="ritase_company"  value="<?php echo $this->sess->user_company;?>" /></td>
        				</tr>
        				<tr>
        					<td width="15%">Set Ritase System For :</td>
        					<td>
        						<select id="ritase_name" name="ritase_name" class="form-control">
        						<?php
        							if (isset($dataforritase) && (count($dataforritase) > 0))
        							{
        								for ($i=0;$i<count($dataforritase);$i++)
        								{
        									if ($dataforritase[$i]->geofence_name != $dataforritase[$i+1]->geofence_name)
        									{
        						?>
        									<option value="<?php echo $dataforritase[$i]->geofence_name;?>" ><?php echo $dataforritase[$i]->geofence_name;?></option>

        						<?php 		}
        								}
        							}
        						?>
        						</select>
        					</td>
        				</tr>

        				<tr>
        					<td>&nbsp;</td>
        						<td>
        								<button type="button" name="btncancel" id="btncancel" class="btn btn-flat" onclick="location='<?=base_url()?>ritase';" />Cancel</button>
                        <button type="submit" name="btnsave" id="btnsave" class="btn btn-success" />Save</button>
        								<img id="loader" src="<?=base_url();?>assets/images/ajax-loader.gif" border="0" alt="" title="" style="display:none;">
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

<div id="modaldeleteritase" style="display: none;">
  <div id="mydivheader"></div>
  <div class="row" >
    <div class="col-md-12">
        <div class="card card-topline-yellow">
            <div class="card-head">
                <header id="titleheader"></header>
                <div class="tools">
                    <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                  <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                  <button type="button" class="btn btn-danger" name="button" onclick="closemodaldeleteritase();">X</button>
                </div>
            </div>
            <div class="card-body">
              <input type="hidden" id="idritasefordelete">
              Are you sure want to delete this ritase?
              <table width="100%" cellpadding="8" class="table">
                <tr>
                  <td></td>
                  <td>
                    <div class="text-right">
                      <button class="btn btn-flat" type="button" onclick="closemodaldeleteritase();" /> Cancel</button>
                      <button class="btn btn-danger" type="submit" name="submit" onclick="remove()"> Delete</button>
                    </div>
                  </td>
                </tr>
              </table>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
  function showAddRitase(){
    $("#formaddritase").show();
    $("#tableritase").hide();
  }

  function showmodaldeleteritase(id){
    $("#idritasefordelete").val(id);
    $("#modaldeleteritase").show();
  }

  function closemodaldeleteritase(){
    $("#modaldeleteritase").hide();
  }

  function remove(){
    var idritasefordelete = $("#idritasefordelete").val();
		jQuery.post('<?php echo base_url(); ?>ritase/remove/', {id_ritase: idritasefordelete},
			function(r)
			{
        if (r.msg == "success") {
          if (confirm(alert("Ritase has been deleted"))) {
            window.location = '<?php echo base_url()?>ritase';
          }
        }
			}
			, "json"
		);
	}

  function frmadd_onsubmit(){
		jQuery("#loader").show();
		jQuery.post("<?=base_url()?>ritase/save", jQuery("#frmadd").serialize(),
			function(r)
			{
				jQuery("#loader").hide();
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
</script>
