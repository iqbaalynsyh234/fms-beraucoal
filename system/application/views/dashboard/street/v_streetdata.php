<style media="screen">
	div#modaleditstreet {
		margin-top: 0.5%;
    margin-left: 17%;
		max-height: 500px;
		max-width: 900px;
		overflow-x: auto;
		position: fixed;
		z-index: 9;
		background-color: #f1f1f1;
		text-align: left;
		border: 1px solid #d3d3d3;
	}
</style>
<script>
	function showmapex()
	{
		showmap();
	}
	function edit_street(id)
	{
		// showdialog();
		// console.log("id : ", id);
		jQuery.post('<?=base_url()?>streetdata/edit/' + id, {},
			function(r)
			{
				// console.log("r : ", r);
				$("#id").val(r.street_id);
				$("#street_name").val(r.street_name);
				$("#streetidforupdate").html(r.street_id);
				$("#modaleditstreet").show();
			}
			, "json"
		);
	}

	function edit_street_onsubmit()
	{
		jQuery.post("<?=base_url()?>streetdata/update", jQuery("#frmeditstreet").serialize(),
		function(r)
		{
			alert(r.message);

									if (r.error)
									{
										return;
									}

									jQuery("#modaleditstreet").hide();
									window.location.reload();
								}
								, "json"
							);

							return false;
	}

	function frmsearch_onsubmit()
	{
		var field = jQuery("#field").val();
		location = '<?php echo base_url(); ?>streetdata/index/'+jQuery("#field").val()+"/"+jQuery("#keyword").val();

		return false;
	}

	function field_onchange()
	{
	}

	function gostreet(id)
	{
		jQuery.post("<?=base_url()?>streetdata/docenter/"+id, {},
			function(r)
			{
				if (! map)
				{
					setcenter(r.lat, r.lng);
				}
				else
				{
					dosetcenter(r.lat, r.lng);
				}
			},
			"json"
		);
	}
	</script>

<div class="sidebar-container">
  <?=$sidebar;?>
</div>
<!-- end sidebar menu -->

<!-- start page content -->
<div class="page-content-wrapper">
  <div class="page-content" style="width:89%;">
    <br>
    <?php if ($this->session->flashdata('notif')) {?>
      <div class="alert alert-success" id="notifnya" style="display: none;"><?php echo $this->session->flashdata('notif');?></div>
    <?php }?>
    <!--<div class="alert alert-success" id="notifnya2" style="display: none;"></div>-->
    <div class="row">
      <div class="col-md-12" id="tablestreet">
				<div class="panel" id="panel_form">
					<header class="panel-heading panel-heading-blue">Street Data</header>
					<div class="panel-body" id="bar-parent10">
							<div id="map" style="width: 100%; height: 400px; display:none;"></div>
              <table id="example1" class="table table-striped">
                <thead>
          				<tr>
          					<th>
                      <a type="button" class="btn btn-success btn-xs" href="<?php echo base_url()?>streetdata/add">
                        <span class="fa fa-plus"></span>
                      </a>
                      No
                    </th>
          					<th>Street</th>
                    <th>Coordinate</th>
          					<th>Control</th>
          				</tr>
          			</thead>
                <tbody>
                  <?php
            			   for($i=0; $i < count($data); $i++){
            			?>
            				<tr <?=($i%2) ? "class='odd'" : "";?>>
            					<td><?=$i+1?></td>
            					<td><?=$data[$i]->street_name;?></td>
            					<td><?=$data[$i]->street_polygon;?></td>
            					<td>
            							<a href="javascript:gostreet('<?=$data[$i]->street_id;?>')"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>
            							<?php if ($data[$i]->updated) { ?>
            							<a href="<?=base_url();?>streetdata/remove/<?=$data[$i]->street_id;?>" onclick="javascript: return confirm('<?=$this->lang->line("lconfirm_delete"); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>">
                          <a href="#" onclick="javascript: edit_street('<?=$data[$i]->street_id;?>')"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
            							<?php } else { ?>
            							&nbsp;
            							&nbsp;
            							<?php } ?>
            					</td>
            				</tr>
            			<?php } ?>
  							</tbody>
  						</table>
            </div>
      </div>
    </div>


</div>
</div>
</div>

<div id="modaleditstreet" style="display: none;">
	<div id="modaleditstreetheader"></div>
	<div class="row">
		<div class="col-md-12">
			<div class="card card-topline-yellow">
				<div class="card-head">
					<header>Edit Street</header>
					<div class="tools">
						<a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
						<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
						<button type="button" class="btn btn-danger" name="button" onclick="closemodaleditstreet();">X</button>
					</div>
				</div>
				<div class="card-body">
					<form id="frmeditstreet" onsubmit="javascript: return edit_street_onsubmit()">
					<input type="hidden" name="street_id" id="id"/>
						<table width="100%" cellpadding="3" class="tablelist">
		    				<tr>
								<td>ID</td>
								<td>:</td>
								<td>
									<label id="streetidforupdate"></label>
								</td>
							</tr>

						<tr>
								<td>Street Name</td>
								<td>:</td>
								<td><input type="text" name="street_name" id="street_name" class="form-control" /></td>
							</tr>

		    			<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>
										<input type="submit" name="btnsave" id="btnsave" value=" Update " class="btn btn-success"/>
										<input type="button" name="btncancel" id="btncancel" value=" Cancel " onclick="javascript:jQuery('#modaleditstreet').hide();" class="btn btn-warning"/>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
