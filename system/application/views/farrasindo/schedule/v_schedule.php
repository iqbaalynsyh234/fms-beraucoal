<script>
jQuery(document).ready(
  function()
  {
    showclock();

    jQuery("#sortby").val('<?=$sortby?>');
    jQuery("#orderby").val('<?=$orderby?>')

    field_onchange();
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

function order(by)
{
  if (by == jQuery("#sortby").val())
  {
    if (jQuery("#orderby").val() == "asc")
    {
      jQuery("#orderby").val("desc");
    }
    else
    {
      jQuery("#orderby").val("asc");
    }
  }
  else
  {
    jQuery("#orderby").val('asc')
  }

  jQuery("#sortby").val(by);
  page(0);
}

</script>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<div class="block-border">
      <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
  		<fieldset class="grey-bg required">
  		<legend>Search By</legend>
  			<input type="hidden" name="offset" id="offset" value="" />
  			<input type="hidden" id="sortby" name="sortby" value="" />
  			<input type="hidden" id="orderby" name="orderby" value="" />
  		</fieldset>
  		</form>
  		<br />
  		[ <a href="<?=base_url();?>projectschedule/add_project"><font color="#0000ff">Add Project Schedule</font></a> ]
		</div>
	</div>
</div>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<div id="result" style="width:97%;"></div>
</div>
