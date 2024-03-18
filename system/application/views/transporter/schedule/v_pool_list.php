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
				jQuery.post('<?=base_url()?>projectschedule/delete_pool/' + id, {}, function(r){
					if (r.error) {
						if (confirm(alert(r.message))) {
              window.location = '<?php echo base_url()?>projectschedule/pool_list';
            };
						return;
					}else{
            if (confirm(alert(r.message))) {
              window.location = '<?php echo base_url()?>projectschedule/pool_list';
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
        [ <a href="<?=base_url();?>projectschedule"><font color="#0000ff">Project List</font></a> ]
        [ <a href="<?=base_url();?>projectschedule/add_pool"><font color="#0000ff">Add Pool</font></a> ]
  		</form>

      <table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
        <thead>
          <tr>
            <th>No</th>
            <th>Pool Name</th>
            <th width="18%;">Address</th>
            <th>Opsi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($datapool as $rowpool) {?>
          <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $rowpool['pool_name'] ?></td>
            <td>
              <?php echo $rowpool['pool_address'] ?><br>
              Coord : <a href="https://www.google.com/maps?z=12&t=m&q=loc:<?php echo $rowpool['pool_latitude'].','.$rowpool['pool_longitude']?>" target="_blank">
                <?php echo $rowpool['pool_latitude'].','.$rowpool['pool_longitude']?>
              </a>
            </td>
            <td>
              <a href="<?=base_url();?>projectschedule/edit_pool/<?php echo $rowpool['pool_no'];?>">
    						<img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>">
    					</a>

              <a href="javascript:delete_data(<?php echo $rowpool['pool_no'];?>)">
    						<img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>">
    					</a>
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
