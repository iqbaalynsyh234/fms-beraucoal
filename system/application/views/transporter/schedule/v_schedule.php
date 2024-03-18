<script>
jQuery(document).ready(
  function()
  {
    showclock();


    // field_onchange();
    page(0);
  }
);

function page(p)
{
  if(p==undefined){
    p=0;
  }
  jQuery("#offset").val(p);
  jQuery("#loader").show();

  jQuery.post("<?=base_url();?>projectschedule/searchschedule/"+p, jQuery("#frmsearch").serialize(),
    function(r)
    {
      console.log("r : ", r);
      jQuery("#loader").hide();
      jQuery("#result").html(r.html);
      jQuery("#total").html(r.total);
    }
    , "json"
  );
}

function frmsearch_onsubmit()
{
  page(0);
  return false;
}

function delete_data(id)
		{
      console.log("id : ", id);
			if (confirm("Are you sure delete this data?")) {
				jQuery.post('<?=base_url()?>projectschedule/delete_project/' + id, {}, function(r){
					if (r.error) {
						if (confirm(alert(r.message))) {
              window.location = '<?php echo base_url()?>projectschedule';
            };
						return;
					}else{
            if (confirm(alert(r.message))) {
              window.location = '<?php echo base_url()?>projectschedule';
            };
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
      <form class="block-content form" name="frmsearch" id="frmsearch">
  		<h1>Project Schedule</h1>
        [ <a href="<?=base_url();?>projectschedule/add_project"><font color="#0000ff">Add Project Schedule</font></a> ]
        [ <a href="<?=base_url();?>projectschedule/pool_list"><font color="#0000ff">Pool List</font></a> ]
  		</form>

      <table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
        <thead>
          <tr>
            <th>No</th>
            <th>Project Name</th>
            <th>Vehicle</th>
            <th width="8%;">Company</th>
            <th>Driver / Operator</th>
            <th>Customer</th>
            <th width="18%;">Address</th>
            <th>Project Date</th>
            <th>Project Price</th>
            <th>Status</th>
            <th>Option</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($dataproject as $rowproject) {?>
          <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $rowproject['project_name'] ?></td>
            <td><?php echo $rowproject['project_vehicle_no'].'-'.$rowproject['project_vehicle_name'] ?></td>
            <td>

                <?php foreach ($company as $rowcompany) {?>
                    <?php if ($rowcompany['company_id'] == $rowproject['project_vehicle_company']) {?>
                      <?php echo $rowcompany['company_name'] ?>
                    <?php } ?>
                <?php } ?>


            </td>
            <td><?php echo $rowproject['project_driver_operator_name']?></td>
            <td><?php echo $rowproject['project_customer_name'] ?></td>
            <td>
              <?php echo $rowproject['project_address'] ?><br>
              Coord : <a href="https://www.google.com/maps?z=12&t=m&q=loc:<?php echo $rowproject['project_latitude'].','.$rowproject['project_longitude']?>" target="_blank">
                <?php echo $rowproject['project_latitude'].','.$rowproject['project_longitude']?>
              </a>
            </td>
            <td>
              <?php echo date("d-m-Y H:i", strtotime($rowproject['project_startdate'])).' s/d '.date("d-m-Y H:i", strtotime($rowproject['project_enddate'])); ?><br>
              <?php echo $rowproject['project_durationofwork']; ?>
            </td>
            <td>
              <?php echo "Rp. ". number_format($rowproject['project_price'], 0, ",", "."); ?>
            </td>
            <td>
              <?php $status = $rowproject['project_status']; ?>
              <?php if ($status == 0) {?>
                <?php echo "On Schedule" ?>
              <?php }elseif ($status == 1) {?>
                <?php echo "On Duty" ?>
              <?php }elseif ($status == 2) {?>
                <?php echo "On Site" ?>
              <?php }elseif ($status == 3) {?>
                <?php echo "Completed" ?>
              <?php } ?>
            </td>
            <td>
            <?php if ($status != 3) {?>
              <img width="20px;" src="<?=base_url();?>assets/images/update.png" border="0" title="Complete Project" onclick="completeproject('<?php echo $rowproject['project_no']?>')">
              <a href="<?=base_url();?>projectschedule/edit/<?php echo $rowproject['project_no'];?>">
    						<img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>">
    					</a>

              <a href="javascript:delete_data(<?php echo $rowproject['project_no'];?>)">
    						<img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>">
    					</a>
            <?php } ?>


            </td>
          </tr>
        <?php $no++; } ?>
        <!-- <tfoot>
            <tr>
                <td colspan="11"><?=$paging?></td>
            </tr>
        </tfoot> -->
      </table>

		</div>
	</div>
</div>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<div id="result" style="width:97%;"></div>
</div>

<script type="text/javascript">
  function completeproject(id){
    console.log("id : ", id);
    if (confirm("Are you sure want to complete this project ?") == true) {
      jQuery.post('<?php echo base_url(); ?>projectschedule/completingproject', {id:id},
       function(response)
       {
         console.log("response : ", response);
         if (response.error == "false") {
           if (confirm(alert("Project Failed to Completed"))) {
             window.location = '<?php echo base_url()?>projectschedule';
           }
         }else {
           if (confirm(alert("Project Completed"))) {
             window.location = '<?php echo base_url()?>projectschedule';
           }
         }
       }
       , "json"
     );
     } else {
       return false;
     }

  }
</script>
